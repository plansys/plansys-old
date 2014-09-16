<?php
/**
 * Description of JasperRest
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperRest {
    private $host;
    private $ssl = false;
    
    private $cookieFile = '/tmp/jasper_rest_cookies';
    private $userAgent  = 'Jasper.php/0.1 (+http://blog.flowl.info/p/projekte/jasper-php/)';


    public function __construct($host, $ssl = false) {
        $this->host = $host;
        $this->ssl  = $ssl;
        
        $this->cookieFile = \Yii::app()->runtimePath . DIRECTORY_SEPARATOR . 'jasper_rest_cookies';
    }


    private function getErrorMsg($code) {
        switch ((int)$code) {
            case 100: return 'Continue';
            case 101: return 'Switching Protocols';
            case 200: return 'OK';
            case 201: return 'Created';
            case 202: return 'Accepted';
            case 203: return 'Non-Authoritative Information';
            case 204: return 'No Content';
            case 205: return 'Reset Content';
            case 206: return 'Partial Content';
            case 300: return 'Multiple Choices';
            case 301: return 'Moved Permanently';
            case 302: return 'Found';
            case 303: return 'See Other';
            case 304: return 'Not Modified';
            case 305: return 'Use Proxy';
            case 307: return 'Temporary Redirect';
            case 400: return 'Bad Request';
            case 401: return 'Unauthorized';
            case 402: return 'Payment Required';
            case 403: return 'Forbidden';
            case 404: return 'Not Found';
            case 405: return 'Method Not Allowed';
            case 406: return 'Not Acceptable';
            case 407: return 'Proxy Authentication Required';
            case 408: return 'Request Time-out';
            case 409: return 'Conflict';
            case 410: return 'Gone';
            case 411: return 'Length Required';
            case 412: return 'Precondition Failed';
            case 413: return 'Request Entity Too Large';
            case 414: return 'Request-URI Too Large';
            case 415: return 'Unsupported Media Type';
            case 416: return 'Request Range Not Satisfiable';
            case 417: return 'Expecation Failed';
            case 500: return 'Internal Server Error';
            case 501: return 'Not Implemented';
            case 502: return 'Bad Gateway';
            case 503: return 'Service Unavailable';
            case 504: return 'Gateway Time-out';
            case 505: return 'HTTP Version Not Suppported';
            defaul:   return 'Unknown';
        }
    }


    private function getRequestHandle($url) {
        // URL must start with a slash
        if ($url[0] != '/') {
            $url = '/' . $url;
        }

        try {
            $curl = curl_init('http://' . $this->host . $url);
            //var_dump('http://' . $this->host . $url); die;
            
            //curl_setopt($curl, CURLOPT_FAILONERROR,         true);
            
            curl_setopt($curl, CURLOPT_FORBID_REUSE,        false);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT,       false);
            curl_setopt($curl, CURLOPT_HEADER,              false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,      true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,      0);
            curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT,   1024);
            curl_setopt($curl, CURLOPT_COOKIEFILE,          $this->cookieFile);
            curl_setopt($curl, CURLOPT_COOKIEJAR,           $this->cookieFile);
            curl_setopt($curl, CURLOPT_COOKIESESSION,       false);
            
            //curl_setopt($curl, CURLOPT_ENCODING,            'identity');
            curl_setopt($curl, CURLOPT_USERAGENT,           $this->userAgent);
        } catch(\Exception $e) {
            throw $e;
        }
        
        return $curl;
    }


    public function get($url) {
        try {
            $curl = $this->getRequestHandle($url);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
            $body = curl_exec($curl);
            $head = curl_getinfo($curl);
            curl_close($curl);
        } catch(\Exception $e) {
            throw $e;
        }

        if ($head['http_code'] >= 400) {
            throw new JasperException("{$this->getErrorMsg($head['http_code'])} on GET request ({$url})", $head['http_code'], $body);
        }

        return array('header' => $head, 'body' => $body);
    }


    public function post($url, Array $arg = null) {
        try {
            $curl = $this->getRequestHandle($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $arg);
            $body = curl_exec($curl);
            $head = curl_getinfo($curl);
            curl_close($curl);
        } catch(\Exception $e) {
            throw $e;
        }

        if ($head['http_code'] >= 400) {
            throw new JasperException("{$this->getErrorMsg($head['http_code'])} on POST request ({$url})", $head['http_code'], $body);
        }

        return array('header' => $head, 'body' => $body);
    }


    public function put($url, $fileString, $isBinary = false) {
        try {
            // Open PHP stream, read/write/binary, 1GB memory cap before caching on disc
            $streamBuffer = @fopen('php://temp/maxmemory:1024000', 'wb+');
            if ($streamBuffer === false) {
                throw new JasperException('failed to open PHP stream buffer');
            }
            fwrite($streamBuffer, $fileString);
            fseek($streamBuffer, 0);
            
            $curl = $this->getRequestHandle($url);
            curl_setopt($curl, CURLOPT_UPLOAD, true);
            // CURLOPT_PUT does not work, use CUSTOMREQUEST instead
            //curl_setopt($curl, CURLOPT_PUT, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_INFILE, $streamBuffer);
            curl_setopt($curl, CURLOPT_INFILESIZE, strlen($fileString));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Expect:',
                'X-HTTP-Method-Override: PUT',
            ));
            if ($isBinary === true) {
                curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            }

            $body = curl_exec($curl);
            $head = curl_getinfo($curl);
            curl_close($curl);
            fclose($streamBuffer);
        } catch(\Exception $e) {
            throw $e;
        }

        if ($head['http_code'] >= 400) {
            throw new JasperException("{$this->getErrorMsg($head['http_code'])} on multipart PUT request ({$url})", $head['http_code'], $body);
        }

        return array('header' => $head, 'body' => $body);
    }


    public function multiput($url, $resourceDescriptorXml, $fileUri, $fileContent, $isBinary = true) {
        try {
            $curl     = $this->getRequestHandle($url);
            $boundary = md5(microtime());
            
            $requestBody = "--{$boundary}\r\n"
                         . "Content-Disposition: form-data; name=\"ResourceDescriptor\"\r\n"
                         . "Content-Length: " . strlen($resourceDescriptorXml) . "\r\n"
                         . "Content-Type: text/plain; charset=UTF-8\r\n"
                         . "Content-Transfer-Encoding: 8bit\r\n"
                         . "\r\n"
                         . $resourceDescriptorXml . "\r\n"
                         . "--" . $boundary . "\r\n"
                         . "Content-Disposition: form-data; name=\"" . $fileUri . "\"; filename=\"" . $fileUri . "\"\r\n"
                         . "Content-Length: " . strlen($fileContent) . "\r\n"
                         . "Content-Type: application/octet-stream\r\n"
                         . "Content-Transfer-Encoding: binary\r\n"
                         . "\r\n"
                         . $fileContent . "\r\n"
                         . "--" . $boundary . "--\r\n";
            
            //var_dump($requestBody); die;
            
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // CURLOPT_PUT does not work, use CUSTOMREQUEST instead
            //curl_setopt($curl, CURLOPT_PUT, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: multipart/form-data; boundary="'.$boundary.'"',
                'X-HTTP-Method-Override: PUT',
            ));

            if ($isBinary === true) {
                curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            }

            $body = curl_exec($curl);
            $head = curl_getinfo($curl);
            curl_close($curl);
        } catch(\Exception $e) {
            throw $e;
        }
        
        
        if ($head['http_code'] >= 400) {
            throw new JasperException("{$this->getErrorMsg($head['http_code'])} on PUT request ({$url})", $head['http_code'], $body);
        }

        return array('header' => $head, 'body' => $body);
    }


    public function delete($url) {
        try {
            $curl = $this->getRequestHandle($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            $body = curl_exec($curl);
            $head = curl_getinfo($curl);
            curl_close($curl);
        } catch(\Exception $e) {
            throw $e;
        }

        if ($head['http_code'] >= 400) {
            throw new JasperException("{$this->getErrorMsg($head['http_code'])} on DELETE request ({$url})", $head['http_code'], $body);
        }

        return array('header' => $head, 'body' => $body);
    }
}
