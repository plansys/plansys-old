package main

import (
	"bytes"
	"fmt"
	"io/ioutil"
	"log"
	"net"
	"net/http"
	"os"
	"os/exec"
	"path/filepath"
	"strconv"
	"strings"

	"github.com/VividCortex/godaemon"
	"github.com/gorilla/websocket"
	"github.com/plansys/psthrift/state"
	"github.com/ryanuber/go-glob"
	"github.com/tidwall/buntdb"
	"github.com/tidwall/gjson"
)

type StateManagerHandler struct {
	Clients     map[*websocket.Conn]*state.Client
	Connections map[string]*websocket.Conn
	States      map[string]*StateDB
	WsUrl       string
	Rootdirs    []string
}

type Message struct {
	Client *state.Client          `json:"c,omitempty"`
	Data   map[string]interface{} `json:"d,omitempty"`
}

type StateDB struct {
	DB      *buntdb.DB
	Indexes map[string]string
}

func getPhpPath() string {
	ex, err := godaemon.GetExecutablePath()
	if err != nil {
		log.Println(err)
		return ""
	}

	php := "php"
	sep := fmt.Sprintf("%c", os.PathSeparator)
	dirs := strings.Split(filepath.Dir(ex), sep)
	config := strings.Join(dirs[:len(dirs)-4], sep) + sep + "app" + sep + "config" + sep + "settings.json"

	if _, err := os.Stat(config); err == nil {
		if json, err := ioutil.ReadFile(config); err == nil {
			value := gjson.GetBytes(json, "app.phpPath").String()
			if value != "null" && value != "" {
				php = value
			}
		}
	}

	return php
}

func NewStateManagerHandler(addr string, rootdirs []string) *StateManagerHandler {
	sm := &StateManagerHandler{
		Clients:     make(map[*websocket.Conn]*state.Client),
		Connections: make(map[string]*websocket.Conn),
		States:      make(map[string]*StateDB),
		Rootdirs:    rootdirs,
	}

	urljson := sm.Yiic(true, nil, "ws", "path")
	sm.WsUrl = gjson.Get(urljson, "wsurl").String()
	origin := gjson.Get(urljson, "base").String()

	var upgrader = websocket.Upgrader{
		ReadBufferSize:  1024,
		WriteBufferSize: 1024,
		CheckOrigin: func(r *http.Request) bool {
			host, _, _ := net.SplitHostPort(r.Host)
			if host == origin {
				return true
			} else {
				// log.Println(host, origin, urljson)
				return true
			}
		},
	}

	go func() {
		http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
			conn, err := upgrader.Upgrade(w, r, nil)
			if err != nil {
				log.Println(err)
				return
			}

			iCid := 1
			Cid := "1"
			findingCid := true
			for findingCid {
				Cid = strconv.Itoa(iCid)
				if _, ok := sm.Connections[Cid]; ok {
					iCid++
				} else {
					findingCid = false
					sm.Connections[Cid] = conn
				}
			}

			Tag := ""
			sm.Clients[conn] = &state.Client{
				Cid: &Cid,
				Tag: &Tag,
			}

			for {
				msgType, msg, err := conn.ReadMessage()
				if err != nil {

					yiic := make([]string, 7)
					yiic[0] = "ws"
					yiic[1] = "disconnected"
					yiic[2] = "--tid=" + *sm.Clients[conn].Tid
					yiic[3] = "--uid=" + *sm.Clients[conn].Uid
					yiic[4] = "--sid=" + *sm.Clients[conn].Sid
					yiic[5] = "--cid=" + *sm.Clients[conn].Cid

					delete(sm.Connections, *sm.Clients[conn].Cid)
					delete(sm.Clients, conn)

					if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway) {
						yiic[6] = "--reason='connection closed'"
						log.Printf("error: %v, user-agent: %v \n", err, r.Header.Get("User-Agent"))
					} else {
						yiic[6] = "--reason='" + err.Error() + "'"
						log.Println(err)
					}

					sm.SilentYiic(nil, yiic...)
					return
				}

				if string(msg[0:7]) == "connect" {
					s := strings.Split(string(msg), ":")

					sm.Clients[conn].Tid = &s[1]
					sm.Clients[conn].Uid = &s[2]
					sm.Clients[conn].Sid = &s[3]

					err = conn.WriteMessage(msgType, []byte("connect:"+Cid))

					yiic := make([]string, 6)
					yiic[0] = "ws"
					yiic[1] = "connected"
					yiic[2] = "--tid=" + *sm.Clients[conn].Tid
					yiic[3] = "--uid=" + *sm.Clients[conn].Uid
					yiic[4] = "--sid=" + *sm.Clients[conn].Sid
					yiic[5] = "--cid=" + *sm.Clients[conn].Cid

					sm.SilentYiic(msg, yiic...)

					if err != nil {
						log.Println(err)
						return
					}
				} else {
					go func() {
						yiic := make([]string, 6)
						yiic[0] = "ws"
						yiic[1] = "received"
						yiic[2] = "--tid=" + *sm.Clients[conn].Tid
						yiic[3] = "--uid=" + *sm.Clients[conn].Uid
						yiic[4] = "--sid=" + *sm.Clients[conn].Sid
						yiic[5] = "--cid=" + *sm.Clients[conn].Cid

						// log.Println("Received:", fmt.Sprintf("%s", msg))
						sm.SilentYiic(msg, yiic...)

						if err != nil {
							log.Println(err)
							return
						}
					}()
				}
			}
		})

		log.Println("Running Websocket Server at:", addr)
		err := http.ListenAndServe(addr, nil)
		if err != nil {
			// listen to another port
			log.Println("Failed to listen ", addr)
			log.Println(err)

			wsport := strconv.Itoa(GeneratePort())
			addr = "0.0.0.0:" + wsport
			portfile := filepath.FromSlash(strings.Join(append(rootdirs, "assets", "ports.txt"), "/"))
			if portcontent, err := ioutil.ReadFile(portfile); err == nil {
				if err != nil {
					log.Println("Failed to read ports.txt")
					log.Println(err)
				} else {
					ports := strings.Split(string(portcontent[:]), ":")
					ferr := ioutil.WriteFile(portfile, []byte(ports[0]+":"+wsport), 0644)
					if ferr == nil {
						log.Println("Running Websocket Server at:", addr)

						werr := http.ListenAndServe(addr, nil)
						if werr != nil {
							log.Println("Failed to listen ", addr)
							log.Println(werr)
						}
					} else {
						log.Println("Failed to write ports.txt")
						log.Println(ferr)
					}
				}
			}
		}
	}()
	return sm
}

func (p *StateManagerHandler) SilentYiic(stdin []byte, params ...string) {
	p.Yiic(false, stdin, params...)
}

func (p *StateManagerHandler) Yiic(returnOutput bool, stdin []byte, params ...string) (ret string) {
	ex, err := godaemon.GetExecutablePath()
	if err != nil {
		log.Println(err)
		return
	}

	sep := fmt.Sprintf("%c", os.PathSeparator)
	dirs := strings.Split(filepath.Dir(ex), sep)
	base := strings.Join(dirs[:len(dirs)-3], sep)
	yiic := base + sep + "yiic.php"
	php := getPhpPath()

	params = append([]string{yiic}, params...)
	cmd := exec.Command(php, params...)
	var out bytes.Buffer
	cmd.Stdout = &out

	if stdin != nil {
		in := bytes.NewReader(stdin)
		cmd.Stdin = in
	}

	if !returnOutput {
		go func() {
			cmd.Run()
			if out.Len() > 0 {
				log.Printf(out.String())
			}
		}()
		return ""
	} else {
		cmd.Run()
		return out.String()
	}
}

func (p *StateManagerHandler) Disconnect(client *state.Client, reason string) (err error) {
	url := p.WsUrl + "disconnect"
	url = url + "&tid=" + *client.Tid
	url = url + "&uid=" + *client.Uid
	url = url + "&sid=" + *client.Sid
	url = url + "&cid=" + *client.Cid
	url = url + "&reason=" + reason
	http.Get(url)

	return err
}

func (p *StateManagerHandler) SetTag(client *state.Client, tag string) (err error) {
	if p.Clients == nil {
		return nil
	}

	for conn, val := range p.Clients {
		if val.Tid == nil {
			delete(p.Clients, conn)
			continue
		}

		if client.Tid != nil && client.Uid != nil && client.Sid != nil && client.Cid != nil {
			if val.Tid != nil && val.Uid != nil && val.Sid != nil && val.Cid != nil {
				if *client.Tid == *val.Tid && *client.Uid == *val.Uid && *client.Sid == *val.Sid && *client.Cid == *val.Cid {
					p.Clients[conn].Tag = &tag
				}
			}
		}
	}
	return err
}

func (p *StateManagerHandler) GetClients(to *state.Client) (clients []*state.Client, err error) {

	for conn, val := range p.Clients {

		if val.Tid == nil {
			delete(p.Clients, conn)
			continue
		}

		if *to.Tag != "" {
			if glob.Glob(*to.Tag, *val.Tag) {
				clients = append(clients, val)
			}
		} else {
			if *to.Tid != "" {
				if *to.Uid != "" {
					if *to.Sid != "" {
						if *to.Cid != "" {
							if *to.Tid == *val.Tid && *to.Uid == *val.Uid && *to.Sid == *val.Sid && *to.Cid == *val.Cid {
								clients = append(clients, val)
							}
						} else {
							if *to.Tid == *val.Tid && *to.Uid == *val.Uid && *to.Sid == *val.Sid {
								clients = append(clients, val)
							}
						}
					} else {
						if *to.Tid == *val.Tid && *to.Uid == *val.Uid {
							clients = append(clients, val)
						}
					}
				} else {
					if *to.Tid == *val.Tid {
						clients = append(clients, val)
					}
				}
			} else {
				clients = append(clients, val)
			}
		}
	}

	return clients, err
}

func (p *StateManagerHandler) Send(to *state.Client, message string) (err error) {

	for conn, val := range p.Clients {
		if val.Tid == nil {
			delete(p.Clients, conn)
			continue
		}

		if *to.Tag != "" {
			if glob.Glob(*to.Tag, *val.Tag) {
				conn.WriteMessage(websocket.TextMessage, []byte(message))
			}
		} else {
			if *to.Tid != "" {
				if *to.Uid != "" {
					if *to.Sid != "" {
						if *to.Cid != "" {
							if *to.Tid == *val.Tid && *to.Uid == *val.Uid && *to.Sid == *val.Sid && *to.Cid == *val.Cid {
								conn.WriteMessage(websocket.TextMessage, []byte(message))
							}
						} else {
							if *to.Tid == *val.Tid && *to.Uid == *val.Uid && *to.Sid == *val.Sid {
								conn.WriteMessage(websocket.TextMessage, []byte(message))
							}
						}
					} else {
						if *to.Tid == *val.Tid && *to.Uid == *val.Uid {
							conn.WriteMessage(websocket.TextMessage, []byte(message))
						}
					}
				} else {
					if *to.Tid == *val.Tid {
						conn.WriteMessage(websocket.TextMessage, []byte(message))
					}
				}
			} else {
				conn.WriteMessage(websocket.TextMessage, []byte(message))
			}
		}
	}

	return err
}

func (p *StateManagerHandler) Receive(from *state.Client) (val string, err error) {
	return "", err
}

func (p *StateManagerHandler) use(dbname string) (db *buntdb.DB, success bool) {
	if state, ok := p.States[dbname]; ok {
		return state.DB, true
	} else {
		if !strings.Contains(dbname, ":memory") {
			statePath := filepath.FromSlash(strings.Join(append(p.Rootdirs, "assets", dbname+".buntdb"), "/"))
			log.Printf("Opening: " + statePath)
			stateDB, err := buntdb.Open(statePath)
			log.Printf(" Opened: " + statePath)

			if err == nil {
				p.States[dbname] = &StateDB{
					DB:      stateDB,
					Indexes: make(map[string]string),
				}
				return p.States[dbname].DB, true
			} else {
				log.Println(err)
				return nil, false
			}
		} else {
			stateDB, err := buntdb.Open(":memory:")

			if err == nil {
				p.States[dbname] = &StateDB{
					DB:      stateDB,
					Indexes: make(map[string]string),
				}
				return p.States[dbname].DB, true
			} else {
				log.Println(err)
				return nil, false
			}
		}

	}
}

func (p *StateManagerHandler) StateSet(db string, key string, val string) (err error) {
	if pdb, ok := p.use(db); ok {
		pdb.Update(func(tx *buntdb.Tx) error {
			_, _, err := tx.Set(key, val, nil)
			return err
		})
	}
	return err
}

func (p *StateManagerHandler) StateDel(db string, key string) (err error) {
	if pdb, ok := p.use(db); ok {
		pdb.Update(func(tx *buntdb.Tx) error {
			_, err := tx.Delete(key)
			return err
		})
	}
	return err
}

func (p *StateManagerHandler) StateGet(db string, key string) (val string, err error) {
	if pdb, ok := p.use(db); ok {
		var value string
		pdb.View(func(tx *buntdb.Tx) error {
			value, err = tx.Get(key)
			return err
		})
		return value, err
	}

	return "", err
}

func (p *StateManagerHandler) StateCount(db string) (count int32, err error) {
	if pdb, ok := p.use(db); ok {
		var value int
		pdb.View(func(tx *buntdb.Tx) error {
			value, err = tx.Len()
			return err
		})
		return int32(value), err
	}

	return 0, err
}

func (p *StateManagerHandler) StateCreateIndex(db, name, pattern, indextype string) (err error) {
	if pdb, ok := p.use(db); ok {
		indexsplit := strings.Split(indextype, ":")

		switch indexsplit[0] {
		case "int":
			pdb.ReplaceIndex(name, pattern, buntdb.IndexInt)
		case "float":
			pdb.ReplaceIndex(name, pattern, buntdb.IndexFloat)
		case "json":
			pdb.ReplaceIndex(name, pattern, buntdb.IndexJSON(indexsplit[1]))
		case "string":
			pdb.ReplaceIndex(name, pattern, buntdb.IndexString)
		default:
			pdb.ReplaceIndex(name, pattern, buntdb.IndexString)
		}

		p.States[db].Indexes[name] = indextype
	}
	return err
}

func (p *StateManagerHandler) StateGetByKey(db string, key string) (val []map[string]string, err error) {
	if pdb, ok := p.use(db); ok {
		var result []map[string]string

		pdb.View(func(tx *buntdb.Tx) error {
			err = tx.AscendKeys(key, func(key, value string) bool {
				var keyval = make(map[string]string)
				keyval["key"] = key
				keyval["val"] = value
				result = append(result, keyval)
				return true
			})

			if err != nil {
				log.Println(err)
			}
			return err
		})

		return result, err
	} else {
		log.Println(err)
		return nil, err
	}
}

func (p *StateManagerHandler) StateGetByIndex(db, name string, params map[string]string) (val []map[string]string, err error) {
	if pdb, ok := p.use(db); ok {
		var result []map[string]string

		pdb.View(func(tx *buntdb.Tx) error {
			i := 0   // index
			cp := 0  // current page
			ipp := 0 // item per page

			if params["itemperpage"] != "" && params["page"] != "" {
				if _ipp, err := strconv.Atoi(params["itemperpage"]); err == nil {
					if _cp, err := strconv.Atoi(params["page"]); err == nil {
						cp = _cp
						ipp = _ipp
					}
				}
			}
			loopfunc := func(key, value string) bool {
				if buntdb.Match(key, params["pattern"]) {
					var keyval = make(map[string]string)
					keyval["key"] = key
					keyval["val"] = value
					i++

					if cp > 0 && ipp > 0 {
						if (i > (cp-1)*ipp) && (i <= cp*ipp) {
							result = append(result, keyval)
						}
					} else {
						result = append(result, keyval)
					}
				}
				return true
			}

			if params["startfrom"] == "first" {
				if pivot, ok := params["pivot"]; ok && pivot != "" {
					err = tx.AscendLessThan(name, pivot, loopfunc)
				} else {
					err = tx.Ascend(name, loopfunc)
				}
			} else {
				if pivot, ok := params["pivot"]; ok && pivot != "" {
					err = tx.DescendGreaterThan(name, pivot, loopfunc)
				} else {
					err = tx.Descend(name, loopfunc)
				}
			}

			if err != nil {
				log.Println(err)
			}
			return err
		})

		return result, err
	} else {
		log.Println(err)
		return nil, err
	}
}
