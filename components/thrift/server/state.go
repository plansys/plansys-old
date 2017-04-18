package main

import (
	"bytes"
	"fmt"
	"github.com/gorilla/websocket"
	"github.com/plansys/psthrift/state"
	"github.com/ryanuber/go-glob"
	"github.com/tidwall/buntdb"
	"github.com/tidwall/gjson"
	"io/ioutil"
	"log"
	"net"
	"net/http"
	"os"
	"os/exec"
	"path"
	"path/filepath"
	"strconv"
	"strings"
)

type StateManagerHandler struct {
	Clients     map[*websocket.Conn]*state.Client
	Connections map[string]*websocket.Conn
	States      map[*state.Client]interface{}
	DB          *buntdb.DB
	WsUrl       string
}

type Message struct {
	Client *state.Client          `json:"c,omitempty"`
	Data   map[string]interface{} `json:"d,omitempty"`
}

func getPhpPath() string {
	ex, err := os.Executable()
	if err != nil {
		log.Println(err)
		return ""
	}
	
	php := "php"
	sep := fmt.Sprintf("%c", os.PathSeparator)
	dirs := strings.Split(path.Dir(ex), sep)
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

func NewStateManagerHandler(addr string, rootdirs []string, db *buntdb.DB) *StateManagerHandler {
	sm := &StateManagerHandler{
		Clients:     make(map[*websocket.Conn]*state.Client),
		Connections: make(map[string]*websocket.Conn),
		States:      make(map[*state.Client]interface{}),
		DB:          db,
	}

	urljson := sm.Yiic(true, "ws", "path")
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
				return false
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

					sm.SilentYiic(yiic...)
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

					sm.SilentYiic(yiic...)

					if err != nil {
						log.Println(err)
						return
					}
				} else {
					err = conn.WriteMessage(msgType, msg)
					if err != nil {
						log.Println(err)
						return
					}
				}
			}
		})

		log.Println("Running Websocket Server at:", addr)
		err := http.ListenAndServe(addr, nil)
		if err != nil {
			// listen to another port
			log.Println(err)

			wsport := strconv.Itoa(GeneratePort())
			addr = "0.0.0.0:" + wsport
			portfile := filepath.FromSlash(strings.Join(append(rootdirs, "assets", "ports.txt"), "/"))
			if portcontent, err := ioutil.ReadFile(portfile); err == nil {
				ports := strings.Split(string(portcontent[:]), ":")
				ioutil.WriteFile(portfile, []byte(ports[0]+":"+wsport), 0644)
				if err != nil {
					log.Println(err)
				}
				log.Println("Running Websocket Server at:", addr)
			}

		}
	}()
	return sm
}

func (p *StateManagerHandler) SilentYiic(params ...string) {
	p.Yiic(false, params...)
}

func (p *StateManagerHandler) Yiic(returnOutput bool, params ...string) (ret string) {
	ex, err := os.Executable()
	if err != nil {
		log.Println(err)
		return
	}

	sep := fmt.Sprintf("%c", os.PathSeparator)
	dirs := strings.Split(path.Dir(ex), sep)
	base := strings.Join(dirs[:len(dirs)-3], sep)
	yiic := base + sep + "yiic.php"
	php := getPhpPath()

	params = append([]string{yiic}, params...)
	cmd := exec.Command(php, params...)
	var out bytes.Buffer
	cmd.Stdout = &out

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

func (p *StateManagerHandler) GetClients(filter *state.Client) (clients []*state.Client, err error) {
	return nil, err
}

func (p *StateManagerHandler) Send(to *state.Client, message string) (err error) {
	for conn, val := range p.Clients {
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
								log.Println(val, to)
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

func (p *StateManagerHandler) SetState(key string, val string) (err error) {

	p.DB.Update(func(tx *buntdb.Tx) error {
		_, _, err := tx.Set(key, val, nil)
		return err
	})
	return err
}

func (p *StateManagerHandler) GetState(key string) (val string, err error) {
	var value string
	p.DB.View(func(tx *buntdb.Tx) error {
		value, err = tx.Get(key)
		return err
	})

	return value, err
}
