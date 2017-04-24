package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"git.apache.org/thrift.git/lib/go/thrift"
	"github.com/plansys/psthrift/state"
	"github.com/plansys/psthrift/svc"
	"github.com/tidwall/buntdb"
	"io"
	"log"
	"os"
	"os/exec"
	"strconv"
	"strings"
	"time"
	"unicode"
)

type RunningInstance struct {
	Instance     *svc.Instance
	Cmd          *exec.Cmd
	Out          *bytes.Buffer
	ProcessState *os.ProcessState
}

type ServiceConfig struct {
	PhpPath            string
	KeepClosedInstance int // number of closed instance, delate closed instance more than this number
}

type ServiceManagerHandler struct {
	Services         map[string]*svc.Service
	RunningInstances map[string]*RunningInstance
	DB               *buntdb.DB
	Config           ServiceConfig
	Dir              string
	Port             string
	RestartChan      chan bool
}

func min(a, b int) int {
	if a < b {
		return a
	}
	return b
}

func lcfirst(str string) string {
	for i, v := range str {
		return string(unicode.ToLower(v)) + str[i+1:]
	}
	return ""
}

func NewServiceManagerHandler(db *buntdb.DB, dir string, port string, restartChan chan bool) *ServiceManagerHandler {
	service := make(map[string]*svc.Service)
	runningInstances := make(map[string]*RunningInstance)

	db.CreateIndex("services", "svc:*", buntdb.IndexString)
	db.Update(func(tx *buntdb.Tx) error {
		tx.Ascend("services", func(key, val string) bool {
			var svc *svc.Service = &svc.Service{}
			json.Unmarshal([]byte(val), svc)
			service[svc.Name] = svc
			return true
		})

		if len(service) == 0 {
			service["SendEmail"] = &svc.Service{
				Name:                 "SendEmail",
				CommandPath:          "application.commands",
				Command:              "EmailCommand",
				Action:               "actionSend",
				Schedule:             "manual",
				Period:               "",
				InstanceMode:         "single",
				SingleInstanceAction: "wait",
				Status:               "ok",
			}

			service["ImportData"] = &svc.Service{
				Name:                 "ImportData",
				CommandPath:          "application.commands",
				Command:              "ImportCommand",
				Action:               "actionIndex",
				Schedule:             "manual",
				Period:               "",
				InstanceMode:         "parallel",
				SingleInstanceAction: "wait",
				Status:               "ok",
			}
			for k, v := range service {
				str, _ := json.Marshal(v)
				tx.Set("svc:"+k, string(str), nil)
			}
		}
		return nil
	})

	for _, val := range service {
		val.RunningInstances = make(map[string]*svc.Instance)
	}

	p := &ServiceManagerHandler{
		Services:         service,
		DB:               db,
		RunningInstances: runningInstances,
		Config: ServiceConfig{
			PhpPath:            getPhpPath(),
			KeepClosedInstance: 10,
		},
		Dir:         dir,
		Port:        port,
		RestartChan: restartChan,
	}

	ticker := time.NewTicker(time.Second)
	go func() {
		for range ticker.C {
			for sname, svc := range service {
				if svc.Schedule != "manual" {
					var lastRun int64
					if len(svc.StoppedInstances) > 0 {
						lastRun, _ = strconv.ParseInt(svc.StoppedInstances[0].StopTime, 10, 32)
					}

					var period int64
					svcPeriod, _ := strconv.ParseInt(svc.Period, 10, 32)
					switch svc.Schedule {
					case "day":
						period = svcPeriod * 86400
					case "hour":
						period = svcPeriod * 3600
					case "minute":
						period = svcPeriod * 60
					case "second":
						period = svcPeriod
					}

					curTime := int64(time.Now().Unix())

					if lastRun == 0 || (curTime-lastRun)%period == 0 {
						if svc.InstanceMode == "single" {
							if len(svc.RunningInstances) > 0 {
								if svc.SingleInstanceAction == "wait" {
									continue
								} else {
									for _, v := range svc.RunningInstances {
										if val, ok := runningInstances[v.Pid]; ok {
											if val.Cmd.ProcessState == nil {
												val.Cmd.Process.Kill()
											}
										}
									}
								}
							}
						}
						p.Start(sname, "")
					}
				}
			}
		}
	}()

	return p
}

func (p *ServiceManagerHandler) Add(svc *svc.Service) (err error) {
	p.Services[svc.Name] = svc

	err = p.DB.Update(func(tx *buntdb.Tx) error {
		svc.Status = "ok"
		str, _ := json.Marshal(svc)
		tx.Set("svc:"+svc.Name, string(str), nil)
		return nil
	})
	return err
}

func (p *ServiceManagerHandler) Remove(name string) (err error) {
	p.Stop(name)
	delete(p.Services, name)

	err = p.DB.Update(func(tx *buntdb.Tx) error {
		tx.Delete("svc:" + name)
		return nil
	})
	return err
}

func (p *ServiceManagerHandler) Start(name string, params string) (pid string, err error) {
	if _, ok := p.Services[name]; !ok {
		return "", nil
	}
	if p.Services[name].RunningInstances == nil {
		p.Services[name].RunningInstances = make(map[string]*svc.Instance)
	}
	p.Services[name].View = make(map[string]string)

	running := &RunningInstance{}

	cpath := strings.Split(p.Services[name].CommandPath, ".")
	cname := strings.ToLower(string(p.Services[name].Command[0])) + p.Services[name].Command[1:len(p.Services[name].Command)-7]
	sname := ""
	if len(cpath) == 2 {
		if cpath[0] == "app" {
			sname = "app." + cname
		} else {
			sname = cname
		}
	} else if len(cpath) > 2 {
		sname = cpath[2] + "." + cname
	}

	saction := lcfirst(p.Services[name].Action[6:])

	sparams := ""
	sfparams := ""
	erparams := ""
	if params != "" && params != "null" {
		sparams = "--params=" + params
		var fparams map[string]interface{}
		json.Unmarshal([]byte(params), &fparams)
		sbparams, err := json.MarshalIndent(fparams, "", "  ")
		if err != nil {
			erparams = err.Error()
		}
		sfparams = string(sbparams)
	}

	cmd := exec.Command(p.Config.PhpPath, p.Dir+"/plansys/yiic.php", sname, saction, "--_sname="+name)
	if sfparams != "" {
		cmd = exec.Command(p.Config.PhpPath, p.Dir+"/plansys/yiic.php", sname, saction, "--_sname="+name, sparams)
	}

	stdout, _ := cmd.StdoutPipe()
	stderr, _ := cmd.StderrPipe()
	if err = cmd.Start(); err != nil {
		log.Fatal(err)
	}

	running.Cmd = cmd
	running.Out = new(bytes.Buffer)
	running.Instance = &svc.Instance{
		Pid:         strconv.Itoa(cmd.Process.Pid),
		StartTime:   strconv.FormatInt(time.Now().Unix(), 10),
		ServiceName: name,
	}
	p.Services[name].LastRun = running.Instance.StartTime
	p.Services[name].Status = "ok"

	log.Println("Service " + name + ": Started")
	p.WsSend(name+":*", "started:"+running.Instance.Pid)
	startTime := time.Now().Format("2006/01/02 15:04:05")
	running.Instance.Output = fmt.Sprintf("%s %v started\n", startTime, name)

	if erparams != "" {
		running.Instance.Output += fmt.Sprintf("%s %v Failed to parse params: \n%v\n%v\n", startTime, name, params, erparams)
		log.Printf("%v failed to parse params: \n%v \n%v\n", name, params, erparams)
	}
	if sfparams != "" {
		running.Instance.Output += fmt.Sprintf("%s %v params: \n%v\n\n", startTime, name, sfparams)
		log.Printf("%v params: \n%v \n\n", name, sfparams)
	}

	processOutput := func(out io.ReadCloser, timeout time.Duration) {
		buffer := ""
		lastSend := time.Now()
		for {
			if _, err := io.CopyN(running.Out, out, 1); err == nil {
				r, _, _ := running.Out.ReadRune()

				if len(buffer) >= 1000 || time.Since(lastSend) > 100*time.Millisecond {
					running.Instance.Output += buffer
					p.WsSend(name+":"+running.Instance.Pid, buffer)
					buffer = ""
					lastSend = time.Now()
				}
				buffer += string(r)
			} else {
				time.Sleep(timeout * time.Millisecond)
				if len(buffer) > 0 {
					running.Instance.Output += buffer
					p.WsSend(name+":"+running.Instance.Pid, buffer)
				}
				break
			}
		}
	}

	go processOutput(stdout, 5)
	go processOutput(stderr, 1000)

	// save it to file
	p.DB.Update(func(tx *buntdb.Tx) error {
		jsonsvc, _ := json.Marshal(p.Services[name])
		tx.Set("svc:"+name, string(jsonsvc), nil)
		return nil
	})

	go func() {
		cmd.Wait()

		// update instance data
		running.Instance.StopTime = strconv.FormatInt(time.Now().Unix(), 10)
		p.Services[name].LastRun = running.Instance.StopTime

		go func() {
			time.Sleep(500 * time.Millisecond)
			stoppedMsg := fmt.Sprintf("\n%s %v stopped\n", time.Now().Format("2006/01/02 15:04:05"), name)
			running.Instance.Output += stoppedMsg
			p.WsSend(name+":"+running.Instance.Pid, stoppedMsg)
		}()

		// add it to stopped instance
		p.Services[name].StoppedInstances = append([]*svc.Instance{running.Instance}, p.Services[name].StoppedInstances...)
		p.Services[name].StoppedInstances = p.Services[name].StoppedInstances[0:min(p.Config.KeepClosedInstance, len(p.Services[name].StoppedInstances))]

		// remove from running instance list
		delete(p.Services[name].RunningInstances, running.Instance.Pid)

		// remove it from runnning instance
		delete(p.RunningInstances, running.Instance.Pid)

		// save it to file
		p.DB.Update(func(tx *buntdb.Tx) error {
			jsonsvc, _ := json.Marshal(p.Services[name])
			tx.Set("svc:"+name, string(jsonsvc), nil)
			return nil
		})

		time.Sleep(10 * time.Millisecond)
		p.WsSend(name+":*", "stopped:"+running.Instance.Pid)
		log.Println("Service " + name + ": Stopped")
	}()

	p.RunningInstances[running.Instance.Pid] = running
	p.Services[name].RunningInstances[running.Instance.Pid] = running.Instance

	return strconv.Itoa(cmd.Process.Pid), err
}

func (p *ServiceManagerHandler) Stop(name string) (err error) {
	for _, val := range p.RunningInstances {
		if val.Instance.ServiceName == name {
			p.StopInstance(val.Instance.Pid)
		}
	}
	return nil
}

func (p *ServiceManagerHandler) StopInstance(pid string) (err error) {
	if val, ok := p.RunningInstances[pid]; ok {
		if val.Cmd.ProcessState == nil {
			val.Cmd.Process.Kill()
		}
	}
	return nil
}

func (p *ServiceManagerHandler) GetInstance(pid string) (instance *svc.Instance, err error) {
	if val, ok := p.RunningInstances[pid]; ok {
		return val.Instance, nil
	}
	return nil, nil
}

func (p *ServiceManagerHandler) WsSend(tag string, msg string) (err error) {

	addr := "127.0.0.1:" + p.Port
	transport, _ := thrift.NewTSocket(addr)
	var protocol thrift.TProtocol = thrift.NewTCompactProtocol(transport)
	protocol = thrift.NewTMultiplexedProtocol(protocol, "StateManager")
	service := state.NewStateManagerClientProtocol(transport, protocol, protocol)

	err = transport.Open()
	if err != nil {
		log.Println(err)
		return err
	}

	defer transport.Close()

	tid := "dev/service"
	service.Send(&state.Client{
		Tid: &tid,
		Tag: &tag,
	}, msg)

	return err
}

func (p *ServiceManagerHandler) GetService(name string) (service *svc.Service, err error) {
	return p.Services[name], nil
}

func (p *ServiceManagerHandler) GetAllServices() (services map[string]*svc.Service, err error) {
	return p.Services, nil
}

func (p *ServiceManagerHandler) Cwd() (str string, err error) {
	return p.Dir, err
}

func (p *ServiceManagerHandler) Quit() (err error) {
	p.RestartChan <- true
	return err
}

func (p *ServiceManagerHandler) SetView(name string, key string, value string) (err error) {
	if _, ok := p.Services[name]; ok {
		if p.Services[name].View == nil {
			p.Services[name].View = make(map[string]string)
		}

		p.Services[name].View[key] = value
	}

	return err
}
