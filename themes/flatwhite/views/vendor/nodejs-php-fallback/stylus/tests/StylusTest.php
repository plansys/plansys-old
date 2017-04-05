<?php

use NodejsPhpFallback\NodejsPhpFallback;
use NodejsPhpFallback\Stylus;

class StylusWithoutNode extends Stylus
{
    public function __construct($file, $compress = false)
    {
        parent::__construct($file, $compress);
        $this->node = new NodejsPhpFallback('nowhere');
    }
}

class StylusTest extends PHPUnit_Framework_TestCase
{
    public function testGetStylusFromRaw()
    {
        $expected = "body\n" .
            "  color red\n" .
            "  font 14px Arial, sans-serif\n" .
            "  a\n" .
            "    text-decoration: none";
        $stylus = new Stylus($expected);
        $stylus = trim($stylus->getStylus());

        $this->assertSame($expected, $stylus, 'Stylus can be get as it with a raw input.');
    }

    public function testGetCssFromRaw()
    {
        $code = "body\n" .
            "  color red\n" .
            "  font 14px Arial, sans-serif\n" .
            "  a\n" .
            "    text-decoration: none";
        $stylus = new Stylus($code);
        $css = trim($stylus->getCss());
        $expected = "body {\n" .
            "  color: #f00;\n" .
            "  font: 14px Arial, sans-serif;\n" .
            "}\n" .
            "body a {\n" .
            "  text-decoration: none;\n" .
            "}";

        $this->assertSame($expected, $css, 'Stylus should be rendered anyway.');
    }

    public function testGetStylusFromPath()
    {
        $stylus = new Stylus(__DIR__ . '/test.styl');
        $stylus = trim(str_replace("\r", '', $stylus->getStylus()));
        $expected = "body\n" .
            "  color red\n" .
            "  font 14px Arial, sans-serif\n" .
            "  a\n" .
            "    text-decoration: none";

        $this->assertSame($expected, $stylus, 'Stylus can be get with a file path input too.');
    }

    public function testGetCss()
    {
        $stylus = new Stylus(__DIR__ . '/test.styl');
        $css = trim($stylus);
        $expected = "body {\n" .
            "  color: #f00;\n" .
            "  font: 14px Arial, sans-serif;\n" .
            "}\n" .
            "body a {\n" .
            "  text-decoration: none;\n" .
            "}";

        $this->assertSame($expected, $css, 'Stylus should be rendered anyway.');
    }

    public function testGetMinifiedCss()
    {
        $stylus = new Stylus(__DIR__ . '/test.styl', true);
        $css = trim($stylus);
        $expected = "body{" .
            "color:#f00;" .
            "font:14px Arial,sans-serif;" .
            "}" .
            "body a{" .
            "text-decoration:none" .
            "}";

        $this->assertSame($expected, $css, 'Stylus should be rendered compressed if set to true.');
    }

    public function testWrite()
    {
        $file = sys_get_temp_dir() . '/test.css';
        $stylus = new Stylus(__DIR__ . '/test.styl');
        $stylus->write($file);
        $css = trim(file_get_contents($file));
        unlink($file);
        $expected = "body {\n" .
            "  color: #f00;\n" .
            "  font: 14px Arial, sans-serif;\n" .
            "}\n" .
            "body a {\n" .
            "  text-decoration: none;\n" .
            "}";

        $this->assertSame($expected, $css, 'Stylus should be rendered anyway.');
    }

    public function testGetCssWithoutNode()
    {
        $code = "body\n" .
            "  color red\n" .
            "  font 14px Arial, sans-serif\n" .
            "  a\n" .
            "    text-decoration: none";
        $expected = "body {\n" .
            "  color: red;\n" .
            "  font: 14px Arial, sans-serif;\n" .
            "}\n" .
            "body a {\n" .
            "  text-decoration: none;\n" .
            "}";
        $stylus = new StylusWithoutNode($code);
        $css = trim(str_replace(array("\r", "\t"), array('', '  '), $stylus->getCss()));

        $this->assertSame($expected, $css, 'Stylus should be rendered anyway.');
    }

    public function testWriteWithoutNode()
    {
        $expected = "body {\n" .
            "  color: red;\n" .
            "  font: 14px Arial, sans-serif;\n" .
            "}\n" .
            "body a {\n" .
            "  text-decoration: none;\n" .
            "}";
        $file = sys_get_temp_dir() . '/test.css';
        $stylus = new StylusWithoutNode(__DIR__ . '/test.styl');
        $stylus->write($file);
        $css = trim(file_get_contents($file));
        unlink($file);
        $css = str_replace(array("\r", "\t"), array('', '  '), $css);

        $this->assertSame($expected, $css, 'Stylus should be rendered anyway.');
    }
}
