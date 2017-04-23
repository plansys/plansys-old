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
	"time"

	"git.apache.org/thrift.git/lib/go/thrift"
	"github.com/cleversoap/go-cp"
	"github.com/plansys/psthrift/state"
	"github.com/plansys/psthrift/svc"
	"github.com/plansys/service"
	"github.com/tidwall/buntdb"
)

type program struct{}

func (p *program) Start(s service.Service) error {
	// Start should not block. Do the actual work async.
	p.Run()
	return nil
}
func (p *program) Run() {
	go runServer(thrift.NewTTransportFactory(), thrift.NewTCompactProtocolFactory())
}
func (p *program) Stop(s service.Service) error {
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

	return nil
}

func GetMD5Hash(text string) string {
	hasher := md5.New()
	hasher.Write([]byte(text))
	return hex.EncodeToString(hasher.Sum(nil))
}

func IsArgValid() bool {
	if len(os.Args) > 1 && (os.Args[1] == "setup" ||
		os.Args[1] == "start" ||
		os.Args[1] == "restart" ||
		os.Args[1] == "stop" ||
		os.Args[1] == "install" ||
		os.Args[1] == "remove") {
		return true
	}
	return false
}

func main() {
	ex, _ := os.Executable()
	hash := GetMD5Hash(ex)

	svcConfig := &service.Config{
		Name:        "PlansysDaemon_" + hash,
		DisplayName: "Plansys Daemon [" + ex + "]",
		Description: "Plansys Daemon Service (Running at " + ex + ")",
	}

	log.Println("Service: " + "PlansysDaemon_" + hash)

	prg := &program{}
	if s, err := service.New(prg, svcConfig); err == nil {
		if IsArgValid() {
			log.Println(os.Args[1] + " service...")
			switch os.Args[1] {
			case "setup":
				s.Install()
				time.Sleep(time.Second)
				err = s.Start()
			case "start":
				err = s.Start()
			case "stop":
				err = s.Stop()
			case "restart":
				err = s.Restart()
			case "install":
				err = s.Install()
			case "remove":
				s.Stop()
				time.Sleep(time.Second)
				err = s.Uninstall()
			}
			time.Sleep(time.Second)

			if err != nil {
				log.Println(err)
			} else {
				log.Println(os.Args[1] + ": success")
			}
		} else {
			s.Run()
		}
	} else {
		log.Println(err)
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
