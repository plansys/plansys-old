package main

import (
	"os"
	"sync"
)

type FileWriter struct {
	File  *os.File
	Mutex sync.Mutex
}

func NewFileWriter(name string, perm os.FileMode) (*FileWriter, error) {
	f, err := os.OpenFile(name, os.O_CREATE|os.O_WRONLY, perm)
	if err != nil {
		return nil, err
	}
	return &FileWriter{
		File: f,
	}, err
}

func (f *FileWriter) Write(b string) error {
	f.Mutex.Lock()
	defer f.Mutex.Unlock()
	_, err := f.File.Write([]byte(b))
	if err != nil {
		return err
	}
	return f.File.Sync() // ensure that the write is done.
}

func (f *FileWriter) Close() error {
	f.Mutex.Lock()
	defer f.Mutex.Unlock()
	return f.File.Close()
}

type FileManagerHandler struct {
	Writer map[string]*FileWriter
}

func NewFileManagerHandler() *FileManagerHandler {
	return &FileManagerHandler{
		Writer: make(map[string]*FileWriter),
	}
}

func (p *FileManagerHandler) Write(path, content string) (err error) {
	if _, ok := p.Writer[path]; !ok {
		if writer, err := NewFileWriter(path, 0755); err == nil {
			p.Writer[path] = writer
		} else {
			return err
		}
	}

	if err = p.Writer[path].Write(content); err != nil {
		return err
	}
	
	p.Writer[path].Close()
	return nil
}
