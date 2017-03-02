package main

import "fmt"
import "os"
import "os/exec"
import "strconv"
import "io"
import "bufio"
import "log"
import "net"
import "strings"

func main() {	
	if (len(os.Args) < 3) {
		fmt.Println("Usage: \n");
		fmt.Println("   process find  [PID]");
		fmt.Println("   process kill  [PID]");
		fmt.Println("   process run [logPath] [command]");
		fmt.Println("   process service [port] [command]");
		fmt.Println("   process exec [command]\n\n");
		fmt.Println("   process debug [command]\n\n");
	} else {
		if os.Args[1] == "find"{
			pid, err1 := strconv.Atoi(os.Args[2])		 
			if err1 == nil {
				_ , err2 := os.FindProcess(pid)
				if err2 == nil{
					fmt.Print(true)
					//fmt.Printf("Process %d is found", process.Pid)
				}else{
					fmt.Print(false)	
				}
			}else{
				log.Fatal(false)		
			}
		}else if os.Args[1] == "kill"{
			pid, err1 := strconv.Atoi(os.Args[2])		 
			if err1 == nil {
				process, err2 := os.FindProcess(pid)
				if err2 == nil{
					err3 := process.Kill()
					if err3 == nil{
						fmt.Print(true)		
					}else{
						fmt.Print(false)		
					}            	
				}else{
					log.Fatal(false)			
				}
			}else{
				log.Fatal(false)		
			}
		} else if os.Args[1] == "exec" {
			cmd := exec.Command(os.Args[2], os.Args[3:]...)
			err := cmd.Start()	
			if err == nil {
				if cmd.Process != nil {
					fmt.Println(cmd.Process.Pid)
				}
			}else{
				fmt.Println(err);	
			}
		}else if os.Args[1] == "service" {
			port := 8123
			listener, err := net.Listen("tcp", ":" + strconv.FormatInt(int64(port),10))
			args := strings.TrimSpace(strings.Join(os.Args[2:], " "))
			println("Port:", port)

			// if failed to start at current port
			for (err != nil) {
				// try to connect to current port
				conn, _ := net.Dial("tcp", "127.0.0.1:" + strconv.FormatInt(int64(port),10))
				
				// Send hello message
				fmt.Fprintf(conn, "Helo" + "\n")

				// Wait for answer
				message, _ := bufio.NewReader(conn).ReadString('\n')

				// If the answer is same as current args then bail
				if (strings.TrimSpace(message) == args) {
					fmt.Println(args)
					println("Process already exists, exiting")
					return;
				}

				// Close Connection
				conn.Close()

				// Try to connect to next port
				port++
				println("Port:", port)
				listener, err = net.Listen("tcp", ":" + strconv.FormatInt(int64(port),10))
			}


			cmd := exec.Command(os.Args[2], os.Args[3:]...)
			err = cmd.Start()	
			if err == nil {
				if cmd.Process != nil {
					fmt.Println(cmd.Process.Pid)
				}
			}else{
				fmt.Println(err);	
			}
			
			for {
				// echo message
				conn, err := listener.Accept()
		        if err != nil {
		            println("Error accept:", err.Error())
		            return
		        } else { 
				    conn.Write([]byte(args + "\n"))
		        }
			}
			
		} else if os.Args[1] == "run" {
			cmd := exec.Command(os.Args[3], os.Args[4:]...)
		    stdout, err := cmd.StdoutPipe()
		    stderr, err := cmd.StderrPipe()

			cmd.Start()	
    		
			if err == nil {
				f, err := os.OpenFile(os.Args[2], os.O_APPEND | os.O_CREATE, 0666) 
				writer := bufio.NewWriter(f)
			    defer writer.Flush()

				if (err == nil) {
				    go io.Copy(writer, stdout)
				    go io.Copy(writer, stderr)

					if cmd.Process != nil {
						fmt.Println(cmd.Process.Pid)
					}
				} else{
					fmt.Println(err);	
				}
			}else{
				fmt.Println(err);	
			}
			
		} else if os.Args[1] == "debug"{
			//cmd := exec.Command(os.Args[2], strings.Join(os.Args[3:], " "))
			cmd := exec.Command(os.Args[2], os.Args[3:]...)
			cmd.Stdout = os.Stdout
		    cmd.Stderr = os.Stderr
		    cmd.Run()
		}
	}

}
