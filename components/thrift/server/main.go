package main

import (
	"git.apache.org/thrift.git/lib/go/thrift"
	"github.com/VividCortex/godaemon"
	"github.com/cleversoap/go-cp"
	"github.com/plansys/psthrift/state"
	"github.com/plansys/psthrift/svc"
	"github.com/tidwall/buntdb"
	"io/ioutil"
	"log"
	"net"
	"os"
	"path/filepath"
	"strconv"
	"strings"
)

func runServer(transportFactory thrift.TTransportFactory, protocolFactory thrift.TProtocolFactory) error {
	svport, wsport, rootdirs := InitPort()
	if svport == "" || wsport == "" {
		return nil
	} else {
		log.Printf("started")
	}
	
	// log all error to file
	logfile := LogToFile()
	defer logfile.Close()
	
	if len(os.Args) > 1 && os.Args[1] == "restart" {
		log.Println("Restarting server...")
	}
	log.Println("Running Thrift Server at: 127.0.0.1:" + svport)
	
	svaddr := "127.0.0.1:" + svport
	wsaddr := "0.0.0.0:" + wsport
	svcPath := filepath.FromSlash(strings.Join(append(rootdirs, "app", "config", "service.buntdb"), "/"))
	svcPathTemp := filepath.FromSlash(strings.Join(append(rootdirs, "assets", "service.buntdb"), "/"))
	statePath := filepath.FromSlash(strings.Join(append(rootdirs, "assets", "state.buntdb"), "/"))

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
	
	// get cwd before daemonized (we cant get cwd after daemonized!)
	cwd := filepath.FromSlash(strings.Join(rootdirs, "/"))
	svcProcessor := svc.NewServiceManagerProcessor(NewServiceManagerHandler(svcDB, cwd, svport, restartChan))
	processor.RegisterProcessor("ServiceManager", svcProcessor)

	// register state processor
	stateDB, err := buntdb.Open(statePath)
	if err != nil {
		return err
	}
	defer stateDB.Close()

	// run thrift server
	server := thrift.NewTSimpleServer4(processor, transport, transportFactory, protocolFactory)

	

	// run ws server
	stateProcessor := state.NewStateManagerProcessor(NewStateManagerHandler(wsaddr, rootdirs, stateDB))
	processor.RegisterProcessor("StateManager", stateProcessor)

	go func() {
		if err = server.Serve(); err != nil {
			log.Println(err)
		}
	}()
	
	// daemonize after running server
	godaemon.MakeDaemon(&godaemon.DaemonAttr{})
	
	isRestarted := <- restartChan
	if (isRestarted) {
		panic("Restarting...")
	} else {
		log.Println("Exiting...")
	}
	return nil
}

func main() {
	// run the server, this will be blocked until exit
	runServer(thrift.NewTTransportFactory(), thrift.NewTCompactProtocolFactory())
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
				}  else {
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

	if (len(os.Args) > 1 && os.Args[1] == "restart") {
		service.Quit()
		return true
	} else {
		tdir, _ := service.Cwd()
		return (tdir == dir)
	}
}
