<?php
/**
 * JRequest class 
 *
 * @author César Quintero <cquinteroj@gmail.com>
 * @version 1.0
 *
 * Utilizado para consumir la API RESTful de JasperServer
 *
 * Ejemplo de uso:
 *
 * $this->req = new JRequest(string Servidor URL, array(
 *      array($this, '401,403', 'fn', array(true), true),
 * ));
 *
 */

class JRequest extends CComponent {

    /**
     * @var resource CURL
     */
    public $ch;

    /**
     * @var string header de la respuesta
     */
    public $header;

    /**
     * @var string body de la respuesta
     */
    public $body;

    /**
     * @var array indexado de funciones llamadas segun el codigo http recibido de respuesta
     *
     * [0] string Código o codigos http separados por coma, regresados por el servidor
     * [1] object Objeto donde se localiza el metodo
     * [2] string nombre del metodo a ejecutar
     * [3] array paramentros para el metodo
     * [4] boolean si debe ejecutar la peticion despues del callback
     */
    public $callBack = array();    

    /**
    * @var string almacena el id de la sesion.que regresa el servidor jasper
    * Ej. Respuesta Set-Cookie: JSESSIONID=52E79BCEE51381DF32637EC69AD698AE; Path=/jasperserver
    */
    //protected $jsession = false;

    /**
    * @var string La url base del servidor jasper
    */
    protected $baseUrl;

    public function __construct($baseUrl, $callBack = array()){
        /**/

        $this->baseUrl  = $baseUrl;
        $this->callBack = $callBack;

        /* Inicia sesión cURL */
        $this->ch  = curl_init();

        /* Contenido del header "User-Agent: " a ser usado en la petición HTTP. */
        curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201");

        /* Indica a php retornar el valor en lugar de mostrarlo */
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);

        /* Incluir el header respuesta. */
        curl_setopt($this->ch, CURLOPT_HEADER, TRUE);        

        /* Incluir el header de la petición. */
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, TRUE);

        /* Número de segundos a esperar cuando se está intentado conectar */
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);

        /* TRUE para seguir cualquier encabezado "Location: "*/
        #curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        /***/
    }

    public function __destruct(){
        curl_close($this->ch);
    }

    /**
    * Crea un nuevo objecto de JRequest
    *
    * @return JRequest
    */
    public static function req($baseUrl){
        return new self($baseUrl);
    }

    /**
    * Establece el valor para id de la sesion 
    *
    * @param mixed $value si el valor es true, establece el valor Set-Cookie de la respuesta
    */
    public function setSession($value){
        if($value === true)
            $value = $this->getHeader('Set-Cookie', 'JSESSIONID');
        Yii::app()->user->setState('jsession', $value);
    }

    /**
    * Obtiene el id de la sesion
    */
    public function getSession(){
        return Yii::app()->user->getState('jsession');
    }

    /**
    * Establece las opciones para la autentificacion por HTTP
    *
    * @param string $username
    * @param string $password
    * @param CURLOPT_HTTPAUTH Método de autenticación HTTP. Las opciones son: CURLAUTH_BASIC, CURLAUTH_DIGEST, CURLAUTH_GSSNEGOTIATE, CURLAUTH_NTLM, CURLAUTH_ANY, y CURLAUTH_ANYSAFE.
    */
    public function httpAuth($username, $password, $auth=CURLAUTH_BASIC){
        curl_setopt($this->ch, CURLOPT_HTTPAUTH,$auth);
        curl_setopt($this->ch, CURLOPT_USERPWD, "{$username}:{$password}");
    }

    /**
    * Request metodos (operaciones utilizadas por REST):
    *   - get                 
    *   - post
    *   - put
    *   - patch
    *   - delete
    *
    * @param string $uri el Identificador del recurso
    * @param array $headers cabeceras adicionales a las globales para la peticion
    * @param string $body contenido de la peticion
    * @param array $options opciones adicionales
    *
    * @return el request generado
    *
    */
    public function get($uri = null, $headers = null, $body = null, $options = array()){
        curl_setopt($this->ch, CURLOPT_HTTPGET, TRUE);          

        return $this->request($uri, $headers, $body, $options);
    }

    public function post($uri = null, $headers = null, $body = null, $options = array()){
        curl_setopt ($this->ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/xml"));

        curl_setopt($this->ch, CURLOPT_POST, TRUE);            
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $body);            

        return $this->request($uri, $headers, $body, $options);
    }

    public function put($uri = null, $headers = null, $body = null, $options = array()){
        curl_setopt($this->ch, CURLOPT_PUT, TRUE);              

        return $this->request($uri, $headers, $body, $options);
    }

    public function patch($uri = null, $headers = null, $body = null, $options = array()){
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PATCH"); 

        return $this->request($uri, $headers, $body, $options);
    }

    public function delete($uri = null, $headers = null, $body = null, $options = array()){
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        return $this->request($uri, $headers, $body, $options);
    }


    public function request($uri = null, $headers = null, $body = null, $options = array()){
        
        /* Define la URL de la petición HTTP */
        curl_setopt($this->ch, CURLOPT_URL, $this->getUrl($uri));

    }

    public function exec(){

        if($this->getSession() !== null)
            curl_setopt($this->ch, CURLOPT_COOKIE, $this->getSession());
        
        $res = curl_exec($this->ch);
        return $res;

    }

    /**
    * Ejecuta la peticion HTTP
    * @return mixed
    */
    public function send(){     
        
        $res = $this->exec();

        if(!empty($this->callBack)){
            foreach($this->callBack as $callBack){
                $codes = explode(',', preg_replace('/\s+/', '', $callBack[1]));
                if(in_array($this->getStatusCode(), $codes)){
                    if(call_user_func_array(array($callBack[0], $callBack[2]), $callBack[3]) and $callBack[4]){
                        $res = $this->exec();
                        // Manejar error 
                    }
                }
            }
        }

        $headerSize   = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
        $this->header = substr($res, 0, $headerSize);
        $this->body   = substr($res, $headerSize);
    }    

    /**
    * Obtiene el valor del header solicitado
    * @return mixed
    */
    public function getHeader($header, $content = false){
        $list = explode("\n", $this->header);
        foreach($list as $item){
            if(preg_match('/^('. $header .'):(.*)$/i', $item, $res)){
                if($content !== false){
                    if(strpos($res[2], $content) === false)
                        continue;
                }
                return preg_replace('/\s+/', '', $res[2]);
            }
        }
    }

    /**
    * Regresa el header HTTP enviado
    * @return int
    */
    public function getHeaderOut(){
        return curl_getinfo($this->ch, CURLINFO_HEADER_OUT);
    }

    /**
    * Regresa el codigo HTTP recivido
    * @return int
    */
    public function getStatusCode(){
        return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }

    /**
    * Regresa el codigo HTTP recivido
    * @return int
    */
    public function getResponse($body = true, $header = false){
        $r = '';
        if($header)
            $r .= $this->header;
        if($body)
            $r .= $this->body;        
        return $r;
    } 

    /**
    * Regresa la respuesta en un array
    * @return array
    */
    public function getArrayResponse(){

        return array(
            'body' => $this->body,
            'ContentLength' => $this->getHeader('Content-Length'),
            'Pragma'        => $this->getHeader('Pragma'),
            'Expires'       => $this->getHeader('Expires'),
        );
    } 

    /**
    * Genera la url para la peticion
    * @param string $uri el Identificador del recurso
    *
    * @return string Url Base mas la Uri
    */
    public function getUrl($uri){
        
        $url = "{$this->baseUrl}/{$uri}";
        return preg_replace('/([^:])\/+/', '\\1/', $url);

    }

}