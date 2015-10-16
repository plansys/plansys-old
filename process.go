package main

import "fmt"
import "os"
import "os/exec"
import "strings"
import "strconv"
import "log"

func main() {	
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
	} else if os.Args[1] == "run"{
		cmd := exec.Command(os.Args[2], strings.Join(os.Args[3:], " "))
		err := cmd.Start()	
		if err == nil {
			if cmd.Process != nil {
				fmt.Print(cmd.Process.Pid)
			}
		}else{
			log.Fatal(err)		
		}
	}

}