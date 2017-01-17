<?php

namespace NodejsPhpFallback;

use Stylus\Stylus as PhpStylusEngine;

class Stylus
{
    protected $path;
    protected $contents;
    protected $compress;
    protected $node;
    protected $stylus;
    protected $renderFile;

    public function __construct($file, $compress = false)
    {
        $key = file_exists($file) ? 'path' : 'contents';
        $this->$key = $file;
        $this->node = new NodejsPhpFallback();
        $this->stylus = new PhpStylusEngine();
        $this->compress = $compress;
    }

    protected function stylusExec($arguments, $fallback)
    {
        return $this->node->execModuleScript(
            'stylus',
            'bin/stylus',
            ($this->compress ? '-c ' : '') . $arguments,
            $fallback
        );
    }

    protected function getTempPath()
    {
        return sys_get_temp_dir() . '/stylus.compilation';
    }

    protected function getPath()
    {
        if ($this->path) {
            return $this->path;
        }
        $path = $this->getTempPath();
        file_put_contents($path, $this->getStylus());

        return $path;
    }

    protected function cleanTempFiles()
    {
        if (!isset($this->path) && file_exists($path = $this->getTempPath())) {
            unlink($path);
        }
    }

    protected function getStylusCompiler()
    {
        return $this->stylus->fromString($this->getStylus());
    }

    public function toString()
    {
        return $this->getStylusCompiler()->toString();
    }

    public function toFile()
    {
        file_put_contents($this->renderFile, $this);
    }

    public function getStylus()
    {
        return isset($this->contents)
            ? $this->contents
            : file_get_contents($this->path);
    }

    public function getCss()
    {
        $css = $this->stylusExec('--print ' . escapeshellarg($this->getPath()), array($this, 'toString'));
        $this->cleanTempFiles();

        return $css ?: '';
    }

    public function write($file)
    {
        $this->renderFile = $file;
        $this->stylusExec(' < ' . escapeshellarg($this->getPath()) . ' > ' . escapeshellarg($file), array($this, 'toFile'));
        $this->cleanTempFiles();
    }

    public function __toString()
    {
        return $this->getCss();
    }
}
