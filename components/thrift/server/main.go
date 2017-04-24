package main

import (
	"crypto/md5"
	"encoding/hex"
	"io/ioutil"
	"log"
	"net"
	"os"
	"path/filepath"
	"strconv"
	"strings"

	"git.apache.org/thrift.git/lib/go/thrift"
	"github.com/VividCortex/godaemon"
	"github.com/cleversoap/go-cp"
	"github.com/plansys/psthrift/state"
	"github.com/plansys/psthrift/svc"
	"github.com/tidwall/buntdb"
)

type program struct{}

func (p *program) Run() {
	runServer(thrift.NewTTransportFactory(), thrift.NewTCompactProtocolFactory())
}
func (p *program) Quit() {
	dir, _ := filepath.Abs(filepath.Dir(os.Args[0]))
	dirs := strings.Split(filepath.ToSlash(dir), "/")
	rootdirs := dirs[0 : len(dirs)-4]
	portfile := filepath.FromSlash(strings.Join(append(rootdirs, "assets", "ports.txt"), "/"))

	if portcontent, err := ioutil.ReadFile(portfile); err == nil {
		ports := strings.Split(string(portcontent[:]), ":")
		addr := "127.0.0.1:" + ports[0]
		transport, _ := thrift.NewTSocket(addr)
		var protocol thrift.TProtocol = thrift.NewTCompactProtocol(transport)
		protocol = thrift.NewTMultiplexedProtocol(protocol, "ServiceManager")
		service := svc.NewServiceManagerClientProtocol(transport, protocol, protocol)
		err := transport.Open()
		defer transport.Close()
		if err != nil {
			log.Println(err)
		}
		service.Quit()
	}
}

func GetMD5Hash(text string) string {
	hasher := md5.New()
	hasher.Write([]byte(text))
	return hex.EncodeToString(hasher.Sum(nil))
}

func main() {
	p := &program{}
	if len(os.Args) > 1 {
		switch os.Args[1] {
		case "start":
			p.Run()
		case "restart":
			p.Quit()
			p.Run()
		case "stop":
			p.Quit()
		}
	} else {
		p.Run()
	}
}

func runServer(transportFactory thrift.TTransportFactory, protocolFactory thrift.TProtocolFactory) error {
	svport, wsport, rootdirs := InitPort()
	if svport == "" || wsport == "" {
		return nil
	}

	// log all error to file
	if len(os.Args) > 1 {
		logfile := LogToFile()
		defer logfile.Close()
	}

	log.Println("Running Thrift Server at: 127.0.0.1:" + svport)

	svaddr := "127.0.0.1:" + svport
	wsaddr := "0.0.0.0:" + wsport
	svcPath := filepath.FromSlash(strings.Join(append(rootdirs, "app", "config", "service.buntdb"), "/"))
	svcPathTemp := filepath.FromSlash(strings.Join(append(rootdirs, "assets", "service.buntdb"), "/"))

	_, err := os.OpenFile(svcPath, os.O_RDWR|os.O_CREATE, 0666)
	if err != nil {
		if os.IsPermission(err) {
			if _, err = os.OpenFile(svcPath, os.O_RDONLY, 0666); !os.IsPermission(err) {
				if _, err := os.OpenFile(svcPathTemp, os.O_RDWR, 0666); err == nil {
					if os.IsNotExist(err) {
						// if assets/service.buntdb is not exist,
						// then copy app/config/service.buntdb to assets
						if err = cp.Copy(svcPath, svcPathTemp); err != nil {
							log.Println(err)
						}
					} else {
						log.Println(err)
					}
				} else {
					log.Println(err)
				}
			}
			svcPath = svcPathTemp
		} else {
			log.Println(err)
		}
	} else {
		if sptInfo, err := os.Stat(svcPathTemp); err == nil {
			if spInfo, err := os.Stat(svcPath); err == nil {
				if spInfo.ModTime().Sub(sptInfo.ModTime()) < 0 {
					// if assets/service.buntdb is newer
					// then move it to app/config
					if err = os.Rename(svcPathTemp, svcPath); err != nil {
						log.Println(err)
					}
				}
			}
		}
	}

	var transport thrift.TServerTransport
	transport, err = thrift.NewTServerSocket(svaddr)
	if err != nil {
		return err
	}

	// create multiplexed service
	var processor = thrift.NewTMultiplexedProcessor()

	// register svc processor
	svcDB, err := buntdb.Open(svcPath)
	if err != nil {
		return err
	}
	defer svcDB.Close()

	restartChan := make(chan bool)
	cwd := filepath.FromSlash(strings.Join(rootdirs, "/"))
	svcProcessor := svc.NewServiceManagerProcessor(NewServiceManagerHandler(svcDB, cwd, svport, restartChan))
	processor.RegisterProcessor("ServiceManager", svcProcessor)

	// register state processor (and start ws server)
	sm := NewStateManagerHandler(wsaddr, rootdirs)
	defer func() { // close all state db connection when exiting
		for _, v := range sm.States {
			v.DB.Close()
		}
	}()
	stateProcessor := state.NewStateManagerProcessor(sm)
	processor.RegisterProcessor("StateManager", stateProcessor)

	// run thrift server
	server := thrift.NewTSimpleServer4(processor, transport, transportFactory, protocolFactory)
	go func() {
		if err = server.Serve(); err != nil {
			log.Println(err)
		}
	}()

	// daemonize after running server
	godaemon.MakeDaemon(&godaemon.DaemonAttr{})

	isRestarted := <-restartChan
	if isRestarted {
		panic("Restarting...")
	} else {
		log.Println("Exiting...")
	}
	return nil
}

func LogToFile() (file *os.File) {
	dir, _ := filepath.Abs(filepath.Dir(os.Args[0]))
	dirs := strings.Split(filepath.ToSlash(dir), "/")
	rootdirs := dirs[0 : len(dirs)-4]
	logfile := filepath.FromSlash(strings.Join(append(rootdirs, "assets", "service.log"), "/"))
	f, err := os.OpenFile(logfile, os.O_RDWR|os.O_CREATE|os.O_TRUNC, 0666)

	if err != nil {
		log.Println("error opening file: %v", err)
	} else {
		log.SetOutput(f)
	}
	return f
}

func InitPort() (svport string, wsport string, rootdirs []string) {
	dir, _ := filepath.Abs(filepath.Dir(os.Args[0]))
	dirs := strings.Split(filepath.ToSlash(dir), "/")
	rootdirs = dirs[0 : len(dirs)-4]
	rootdir := filepath.FromSlash(strings.Join(rootdirs, "/"))
	portfile := filepath.FromSlash(strings.Join(append(rootdirs, "assets", "ports.txt"), "/"))

	if portcontent, err := ioutil.ReadFile(portfile); err == nil {
		ports := strings.Split(string(portcontent[:]), ":")
		if ThriftPortAvailable(ports[0]) {
			svport = ports[0]
			wsport = ports[1]
		} else {
			if ThriftAlreadyRun(ports[0], rootdir) {
				if len(os.Args) > 1 && os.Args[1] == "restart" {
					return ports[0], ports[1], rootdirs
				} else {
					return "", "", rootdirs
				}
			} else {
				svport = strconv.Itoa(GeneratePort())
				wsport = ports[1]
				err := ioutil.WriteFile(portfile, []byte(svport+":"+wsport), 0644)
				if err != nil {
					log.Println(err)
				}
			}
		}
	} else {
		svport = strconv.Itoa(GeneratePort())
		wsport = strconv.Itoa(GeneratePort())
		ioutil.WriteFile(portfile, []byte(svport+":"+wsport), 0644)
		if err != nil {
			log.Println(err)
		}
	}

	return svport, wsport, rootdirs
}

func GeneratePort() int {
	addr, err := net.ResolveTCPAddr("tcp", "127.0.0.1:0")
	if err != nil {
		panic(err)
	}

	l, err := net.ListenTCP("tcp", addr)
	if err != nil {
		panic(err)
	}
	defer l.Close()
	return l.Addr().(*net.TCPAddr).Port
}

func ThriftPortAvailable(port string) bool {
	addr := "127.0.0.1:" + port
	transport, _ := thrift.NewTSocket(addr)
	var protocol thrift.TProtocol = thrift.NewTCompactProtocol(transport)
	protocol = thrift.NewTMultiplexedProtocol(protocol, "ServiceManager")
	err := transport.Open()

	defer transport.Close()
	return (err != nil)
}

func ThriftAlreadyRun(port string, dir string) bool {
	addr := "127.0.0.1:" + port
	transport, _ := thrift.NewTSocket(addr)
	var protocol thrift.TProtocol = thrift.NewTCompactProtocol(transport)
	protocol = thrift.NewTMultiplexedProtocol(protocol, "ServiceManager")
	service := svc.NewServiceManagerClientProtocol(transport, protocol, protocol)

	err := transport.Open()
	if err != nil {
		log.Println(err)
		return true
	}

	defer transport.Close()
	tdir, _ := service.Cwd()
	return (tdir == dir)
}
