<?php
/**
 * Description of JasperSimpleXml
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperSimpleXml extends \SimpleXMLElement {
    public function addCData($string) {
        $dom   = dom_import_simplexml($this);
        $cdata = $dom->ownerDocument->createCDATASection($string);
        $dom->appendChild($cdata);
        return $this;
    }

    public function saveXml($pretty = false) {
        $dom = dom_import_simplexml($this);
        if ($pretty === true) {
            $dom->ownerDocument->formatOutput = true;
        } else {
            $dom->ownerDocument->formatOutput = false;
        }
        return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
    }

    public function addXmlObject($simpleXmlObject) {
        if (strlen(trim((string)$simpleXmlObject)) == 0) {
            $xml = $this->addChild($simpleXmlObject->getName());
            foreach($simpleXmlObject->children() as $child) {
                $xml->addXmlObject($child);
            }
        } else {
            $xml = $this->addChild($simpleXmlObject->getName())->addCData((string)$simpleXmlObject);
        }
        foreach ($simpleXmlObject->attributes() as $key => $val) {
            $xml->addAttribute($key, $val);
        }
    }
 }