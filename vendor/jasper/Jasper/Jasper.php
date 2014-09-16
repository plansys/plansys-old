<?php
/**
 * Description of Jasper
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class Jasper {
    private $host;
    private $user;
    private $pass;
    
    private $rest;
    
    private $staticFolder = './serve/';


    public function __construct($host = null, $user = null, $pass = null) {
        spl_autoload_register(function ($class) {
            @include_once(__DIR__ . '/' . str_replace(__NAMESPACE__ . '\\', '', $class) . '.php');
            if (!class_exists($class)) {
                throw new \Exception($class . ' class not available');
            }
        });

        $this->user = $user == null ? $this->user : $user;
        $this->pass = $pass == null ? $this->pass : $pass;
        $this->host = $host == null ? $this->host : $host;
        
        try {
            $this->rest = new JasperRest($this->host);

            // Do login if user and password are passed to the constructor
            if ($user !== null && $pass !== null) {
                $this->login();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function login($user = null, $pass = null) {
        $this->user = $user == null ? $this->user : $user;
        $this->pass = $pass == null ? $this->pass : $pass;
        try {
            $resp = $this->rest->post(JasperHelper::url("/jasperserver/rest/login") . "?j_username={$this->user}&j_password={$this->pass}");
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }


    public function getServerInfo() {
        try {
            $resp = $this->rest->get(JasperHelper::url("/jasperserver/rest_v2/serverInfo"));
        } catch (\Exception $e) {
            throw $e;
        }
        return new \SimpleXMLElement($resp['body']);
    }


    public function getFolder($resource) {
        // You can pass JasperResourceDescriptor objects or a plain uriString to this method
        if ($resource instanceof JasperResourceDescriptor) {
            if ($resource->getWsType() != 'folder') {
                throw new JasperException("resource is not typeof 'folder', ('{$resource->getWsType()}' given) in Jasper::getFolder();");
            }
        } else {
            $resource = new JasperResourceDescriptor($resource);
        }
        try {
            $resp = $this->rest->get(JasperHelper::url("/jasperserver/rest/resources/{$resource->getUriString()}"));
            $list = new \SimpleXMLElement($resp['body']);
        } catch (\Exception $e) {
            throw $e;
        }

        $collection = array();
        foreach ($list->resourceDescriptor as $resource) {
            $descriptor   = new JasperResourceDescriptor();
            $collection[] = $descriptor->fromXml($resource);
            $descriptor   = null;
        }
        return $collection;
    }


    public function getResourceDescriptor($resource) {
        // You can pass JasperResourceDescritpor objects or a plain uriString to this method
        if (!($resource instanceof JasperResourceDescriptor)) {
            $resource = new JasperResourceDescriptor($resource);
        }
        try {
            $resp     = $this->rest->get(JasperHelper::url("/jasperserver/rest/resource/{$resource->getUriString()}"));

            $resource = $resource->fromXml(new \SimpleXMLElement($resp['body']));
        } catch (\Exception $e) {
            throw $e;
        }
        return $resource;
    }


    public function getResourceContents($resource) {
        // You can pass JasperResourceDescritpor objects or a plain uriString to this method
        if (!($resource instanceof JasperResourceDescriptor)) {
            $resource = new JasperResourceDescriptor($resource);
        }
        try {
            $resp     = $this->rest->get(JasperHelper::url("/jasperserver/rest/resource/{$resource->getUriString()}?fileData=true"));
            $content  = $resp['body'];
        } catch (\Exception $e) {
            throw $e;
        }
        return $content;
    }


    public function getReport($resource, $format, Array $params = null) {
        // You can pass JasperResourceDescriptor objects or a plain uriString to this method
        if ($resource instanceof JasperResourceDescriptor) {
            if ($resource->getWsType() != 'reportUnit') {
                throw new JasperException("resource is not typeof 'reportUnit' ('{$resource->getWsType()}' given) in Jasper::getReport();");
            }
        } else {
            $resource = new JasperResourceDescriptor($resource);
        }
        try {
            $paramStr = '';
            if (is_array($params) && sizeof($params) > 0) {
                $paramStr .= '?';
                foreach ($params as $param => $val) {
                    // Might need to be urlencoded
                    $paramStr .= $param . '=' . $val . '&';
                }
            }
            $resp = $this->rest->get(JasperHelper::url("/jasperserver/rest_v2/reports/{$resource->getUriString()}.{$format}{$paramStr}"));
        } catch (\Exception $e) {
            throw $e;
        }

        // Replace static content URL
        if ($format == 'html') {
            return str_replace('/jasperserver/scripts/jquery/js/', $this->staticFolder, $resp['body']);
        }
        return $resp['body'];
    }


    public function createFolder($resource) {
        // You can pass JasperResourceDescriptor objects or a plain uriString to this method
        if (!($resource instanceof JasperResourceDescriptor)) {
            $resource = new JasperFolder($resource);
        } else {
            if ($resource->getWsType() != 'folder') {
                throw new JasperException("resource is not typeof 'folder' ('{$resource->getWsType()}' given) in Jasper::createFolder();");
            }
        }
        $resource->setPropHasData('false');
        try {
            $resp = $this->rest->put(JasperHelper::url("/jasperserver/rest/resource/{$resource->getUriString()}"), $resource->getXml());
        } catch (\Exception $e) {
            throw $e;
        }
        return $resp;
    }


    public function createResource(JasperResourceDescriptor $resource) {
        if ($resource->getWsType() == 'folder') {
            throw new JasperException("please use Jasper::createFolder(); instead of Jasper::createRessource for wsType 'folder'");
        }
        $resource->setPropHasData('false');
        try {
            $resp = $this->rest->put(JasperHelper::url("/jasperserver/rest/resource/{$resource->getUriString()}"), $resource->getXml());
        } catch (\Exception $e) {
            throw $e;
        }
        return $resp;
    }


    public function createContent(JasperResourceDescriptor $resource, $content) {
        $resource->setPropHasData('true');
        
        try {
            $resp = $this->rest->multiput(JasperHelper::url("/jasperserver/rest/resource/{$resource->getUriString()}"), $resource->getXml(), $resource->getUriString(), $content);
        } catch (\Exception $e) {
            throw $e;
        }
        return $resp;
    }


    public function deleteResource($resource) {
        // You can pass JasperResourceDescriptor objects or a plain uriString to this method
        if (!($resource instanceof JasperResourceDescriptor)) {
            $resource = new JasperResourceDescriptor($resource);
        }
        if (($resource->getUriString() == '/'
                || $resource->getUriString() == '/reports'
                || $resource->getUriString() == '/reports/')
                && $this->customerMode === true) {
            throw new JasperException("cannot delete root folder {JasperHelper::url($resource->getUriString())}");
        }
        try {
            $resp = $this->rest->delete(JasperHelper::url("/jasperserver/rest/resource/{$resource->getUriString()}"));
        } catch (\ Exception $e) {
            throw $e;
        }
        return true;
    }
}

