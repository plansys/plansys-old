<?php
/**
 * JasperReport class 
 *
 * @author César Quintero <cquinteroj@gmail.com>
 * @version 1.0
 *
 * Ejemplo de uso:
 *
 *      $re = new JasperReport('/reports/samples/AllAccounts');
 *      //Ejecutar el reporte
 *      $re->exec();
 *      //Mostrar en HTML
 *      $re->toHTML($pagina)
 *      //Mostrar en PDF
 *      $re->toPDF($pagina)
 *      //Mostrar en Excel
 *      $re->toXLS($pagina)
 *      //Ej. $pagina = 10; $pagina = '3-7'; 
 *      //Obtener y establecer el id del reporte para no generarlo de nuevo
 *      $id = $re->id
 *      $re->id = $id
 *      
 *      //Reporte no exportable a otros formatos
 *      $re = new JasperReport('/reports/samples/AllAccounts', JasperReport::FORMAT_PDF);
 *      $re->getReport(array $opciones);
 *      //Ej. $opciones = array('page' => 2);      
 *
 */

class JasperReport extends CComponent {

    const FORMAT_HTML = 'html';
    const FORMAT_PDF  = 'pdf';
    const FORMAT_XLS  = 'xls';


    const REPORT_EXEC = 'reportExecutionRequest';
    const REPORT_EXP  = 'export';

    /**
    * @var string La url base del servidor jasper
    */
    protected $baseUrl = 'http://localhost:8080/jasperserver-pro/';

    /**
    * @var string Uri para logearse en el servidor jasper
    */
    protected $loginUri = '/rest/login/';

    /**
    * @var string nombre de usuario de Jasper Server
    */
    protected $jusername = 'jasperadmin';

    /**
    * @var string contraseña de Jasper Server
    */
    protected $jpassword = 'jasperadmin';

    /**
    * @var array headers para agregar a la peticion
    */
    protected $headers = array();
    
    /**
    * @var JRequest utilizado para hacer las peticiones a Jasper Server
    */
    private $req;
    
    /**
    * @var array Opciones para el reporte, estas opciones son convinadas con las opciones comunes
    */
    private $options;

    /**
    * @var string almacena el formato de la solicitud original
    */
    private $format;

    /**
    * @var string almacena el valor del id del reporte devuelto por jasper server
    */
    private $requestID;

    /**
    * @var int almacena el numero total de paginas generadas devuelto por jasper server
    */
    private $totalPages;

    /**
    * @var string almacena el valor del devuelto por jasper server similiar a formato solicitado pero puede contener otra información
    */
    //private $exportID;

    /**
    * @var almacena el valor del devuelto por jasper server sobre el tipo del contenido
    */
    //private $contentType;

    /**
    * @param string $reportUri, Uri donde se localiza el Reporte
    * @param string $format formato inicial
    * @param array $parameters, se envia en un array asociativo nombre => valor
    * @param array $options, opciones adicionales para el jasper server
    * @param string $baseUrl, url de jasper server
    */
    public function __construct($reportUri, $format = self::FORMAT_HTML, $parameters = array(), $options = array(), $baseUrl = null){

        if($baseUrl != null)
            $this->baseUrl  = $baseUrl;
        
        $this->format  = $format;

        //Junta las opciones comunes con el array de opciones.
        $this->options = CMap::mergeArray(array(
            'reportUnitUri' => $reportUri,
            'outputFormat' => $format,
            'parameters'=> $parameters,
        ), $options);

        $this->req = new JRequest($this->baseUrl, array(
            array($this, '401,403', 'login', array(true), true),
        ));

        try{ 
            $this->login();
        }catch(Exception $e){
            $e->getMessage();
        }

    }

    /**
    * Utilizado para llamar las funciones que exportan el reporte
    * @param string $method corresponde al nombre del método al que se está llamando. 
    * @param array $parameters array enumerado que contiene los parámetros que se han pasado al método.
    * 
    * @return el resultado del metodo llamado puede ser el contendio html, pdf o xls
    */
    public function __call($method, $parameters){

        $name = 'report' . $method;
           
        if(method_exists($this,$name)){

            switch ($method) {
                case 'toHTML': $format = self::FORMAT_HTML; break;               
                case 'toPDF':  $format = self::FORMAT_PDF;  break;                
                case 'toXLS':  $format = self::FORMAT_XLS;  break;
            }

            //Primero debe exportar si hay parametros nuevos o el formato cambio al original
            if(stripos($this->format, $format)!==0)
                call_user_func_array(array($this,'export'), array($format));
            
            return call_user_func_array(array($this,$name), $parameters);
        
        } /*else
            throw new Exception("Error Processing Request", 1);            */
    }

    /**
    * Regresa el ID del Reporte
    *
    * @return el valor del Id
    */
    public function getId(){
        if($this->isReady())
            return $this->requestID;
        else
            return false;
    }

    /**
    * Establece el ID del Reporte, en caso de existir de una consulta previa en la misma sesion
    *
    * @param string $value, sesion id.
    */
    public function setId($value){
        $this->requestID = $value;
    }

    public function getPages(){
        return $this->totalPages;
    }

    public function setPages($value){
        $this->totalPages = $value;
    }    

    /**
    * Genera el XML para solicitar el Reporte
    * @param array $options Opciones para el reporte, estas opciones son convinadas con las opciones por default
    * @param string $tipo el tipo de request puede ser: REPORT_EXEC (Ejecutar) o REPORT_EXP (Exportar)
    *
    * @return string xml enviado a jasperserver
    */
    public function xmlRequest($options, $tipo){

        //Junta las opciones por default con las enviadas.
        $options = CMap::mergeArray(array(
            'reportUnitUri' => '',
            'outputFormat' => self::FORMAT_HTML,
            'freshData' => true,
            'saveDataSnapshot' => false,
            'interactive' => false,
            'ignorePagination' => false,
           // 'pages' => '1',
            'async' => false,
            //'transformerKey' => '',
            //'attachmentsPrefix' => '',
            //'parameters'=> array('name' => 'value',)
        ), $options);

        //Remplazar los valores booleanos por la literal correspondiente true o false
        //http://docs.oracle.com/javase/specs/jls/se7/html/jls-4.html#jls-4.2.5
        $fnBool2Txt = function($var){
            if(is_bool($var))
                return $var ? 'true' : 'false';
            else
                return $var;
        };

        //Crea un nuevo XMLWriter usando memoria para el string de salida. 
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        //Inicia el elemento principal
        $xml->startElement($tipo);   

        foreach ($options as $tag => $cnt){
            //Cuando es un array se refiere a que son los parametros, es el unico dato que acepta un array
            if(is_array($cnt)){
                $xml->startElement($tag);
                //Agrega todos los parametros enviados en un array asociativo
                foreach ($cnt as $param => $val){
                    $xml->startElement('reportParameter');
                    $xml->writeAttribute('name', $param);
                    $xml->writeElement('value', $fnBool2Txt($val));
                    $xml->endElement();
                }
                $xml->endElement();
            }else
                $xml->writeElement($tag, $fnBool2Txt($cnt));
        }
        //Fin elemento principal
        $xml->endElement();
        //Fin documento
        $xml->endDocument();  
        
        return $xml->flush();
    }

    /**
    * Regresa el contentType segun el formato
    * @param string $format indica el formato del que se quiere saber el contentType
    *
    * @return el contentType solicitado
    */
    private function cntTyp($format){
        
        $cntTyp = array(
            self::FORMAT_HTML => 'text/html',
            self::FORMAT_PDF  => 'application/pdf',
            self::FORMAT_XLS  => 'application/vnd.ms-excel',
        );

        return $cntTyp[$format];
    }

    /**
    * Crea una excepción si ocurre algun error con las solicitudes
    * @param string $e titulo del error
    **/
    public function e($e){
        return new Exception("{e} Error, HTTP Code: {$this->req->getStatusCode()}");
    }

    /**
    * Regresa el identificador de la sesion en caso de no existir inicia una nueva sesion
    * @param  boolean $force verdadero indica si debe logearse aun cuando exista la sesion declarada
    *
    * @return mixed true si fue logeado, regresa una excepción en caso de error
    **/
    public function login($force = false){
        $req = new JRequest($this->baseUrl);
        //Revisa si no existe una session previa
        if($force or $req->session === null){
            $req->post($this->loginUri);
            $req->httpAuth($this->jusername, $this->jpassword);
            //Borra la session anterior en caso de existir
            unset($req->session);
            $req->send();
            if($req->getStatusCode() == 200){
                //Activa la sesion
                $req->session = true;
                return true;
            }else
                return $this->e("Login");
        }
    }

    /**
    * Ejecuta la solicitud inicial, creada con el contrstructor
    *
    * @return el estatus de la solicitud
    */
    public function exec(){

        $this->req->post("rest_v2/reportExecutions", null, $this->xmlRequest($this->options, self::REPORT_EXEC));
        $this->req->send(); 

        $xmlReq =  simplexml_load_string($this->req->getResponse());  

        if($xmlReq === false)
            return $this->e("Execution");

        $this->requestID   = (string)$xmlReq->requestId;
        $this->totalPages   = (string)$xmlReq->totalPages;

        return $xmlReq->status;
    }

    /**
    * Cambia el formato de salida
    *
    * @return el estatus de la solicitud
    */
    public function export($format){
        $this->req->post("/rest_v2/reportExecutions/{$this->requestID}/exports/", null, $this->xmlRequest(array('outputFormat' => $format), self::REPORT_EXP));
        $this->req->send();
        $xmlReq =  simplexml_load_string($this->req->getResponse());
        
        return $xmlReq->status;
    }    

    /**
    * Revisa si una solicitud tiene el estatus de 'ready', en caso de no estar listo, lo revisa cada segundo diez veces. 
    *
    * @return true si la solicitud esa lista, false si pasado el tiempo el estatus nunca cambio.
    */
    public function isReady($exportID = null){
        
        if($exportID !== null)
            $exportID = "/exports/{$exportID}";
        
        for($i=0; $i<10; $i++){
        
            $this->req->get("/rest_v2/reportExecutions/{$this->requestID}{$exportID}/status");
            $this->req->send();
            if(strpos($this->req->getResponse(),'ready') !== false)
                return true;
            elseif(strpos($this->req->getResponse(),'not found') !== false)
                $this->exec();
            else
                sleep(1); 
        }

        return false;

    }

    /**
    * Obtiene un reporte al formado dado por $exportID, regresa el repodre o la(s) pagina(s) enviada(s) en la variable $pages
    * @param string $extportID formato de salida
    * @param mixed $pages admite valor string o numeric, indica la pagina o segmento de paginas ej. 1-10
    *
    * @return array regresa un array asociativo donde: 
    *   body: Respuesta de Jasper Server
    *   ContentLength: Tamaño de la respuesta
    *   Pragma: Indica que la respuesta puede ser almacenado en caché por cualquier caché, aunque normalmente sería no almacenables en caché o almacenar en caché sólo dentro de una caché no compartida.
    *   Expires: Contiene la fecha en la cual expira el archivo 
    */
    public function reportOutput($exportID,$pages = false){
        
        if($pages)
            $pages = ";pages={$pages}";


        if($this->isReady($exportID)){
            $this->req->get("/rest_v2/reportExecutions/{$this->requestID}/exports/{$exportID}{$pages}/outputResource");
            $this->req->send(); 

            return $this->req->getArrayResponse();

        } else
            throw new Exception('Limite de tiempo excedido, por favor intente de nuevo');
    }

    /**
    * Agrega los headers y regresa la salida lista para el navegador
    * @param array $cnt respuesta obtenida de jasper server devuelta por el metodo reportOutput
    * @param string $contentType tipo de documento 
    * @param boolean $download si se debe forzar la descarga o no
    *
    * @return reporte listo para mostrarse en el formato solicitado
    */
    public function output($cnt, $contentType, $download = false){

        header("Content-Type: {$contentType}");       
        if($download) header("Content-Disposition: attachment; filename={$download}");
        
        header("Content-Length: {$cnt['ContentLength']}");
        header("Pragma: {$cnt['Pragma']}");
        header("Expires: {$cnt['Expires']}");

        return $cnt['body'];

        Yii::app()->end();      
    }

    /**
    * Extrae las imagenes de jasper server y las serializa ademas quita los archivos js que deben ser incluidos en el main para evitar conflictos
    * @param string $html contenido del reporte en formato html
    *
    * @return html completo con imagenes serializadas
    */
    public function parseHtml($html){

        $html = preg_replace('~<script[\d\D]*?>[\d\D]*?</script>~i', '', $html);
        //Obtener imagenes y serializarlas
        
        $html = str_replace('<td width="50%">&nbsp;</td>',"",$html);
        
        /*
//        $req = $this->req;
//        $html = preg_replace_callback('/(<img.+src\s*=\s*")\/jasperserver([^"]*)(".*?>)/', function($im) use ($req) {
//            $req->get($im[2]);
//            $req->send();           
//            $imagen  = base64_encode($req->getResponse());
//            $mimeType= $req->getHeader('Content-Type');
//            return "{$im[1]}data:{$mimeType};base64,{$imagen}$im[3]";
//        } , $html); 
*/


        return $html; 
    }
    
    /**
    * Obtiene el reporte en formato html
    * @param mixed $pages valor numerico o string que indica la pagina o el segmento de paginas a mostrar
    * @param boolean $download si el formato debe mostrarse en el navegador o forzar la descarga
    *
    * @return regresa el contenido en el formato solicitado listo para mostrarse
    */
    public function reportToHTML($pages = null, $download = false){
        $cnt = $this->reportOutput(self::FORMAT_HTML, $pages);
        $cnt['body'] = $this->parseHtml($cnt['body']);     
        return $this->output($cnt, $this->cntTyp(self::FORMAT_HTML));
    }
    
    /**
    * Obtiene el reporte en formato pdf
    * @param mixed $pages valor numerico o string que indica la pagina o el segmento de paginas a mostrar
    * @param boolean $download si el formato debe mostrarse en el navegador o forzar la descarga
    *
    * @return regresa el contenido en el formato solicitado listo para mostrarse
    */
    public function reportToPDF($pages = null, $download = false){
        $cnt = $this->reportOutput(self::FORMAT_PDF, $pages);
        return $this->output($cnt, $this->cntTyp(self::FORMAT_PDF));
    }
    
    /**
    * Obtiene el reporte en formato xls
    * @param mixed $pages valor numerico o string que indica la pagina o el segmento de paginas a mostrar
    * @param boolean $download si el formato debe mostrarse en el navegador o forzar la descarga
    *
    * @return regresa el contenido en el formato solicitado listo para mostrarse
    */
    public function reportToXLS ($pages = null, $download = false){
        $cnt = $this->reportOutput(self::FORMAT_XLS, $pages);
        return $this->output($cnt, $this->cntTyp(self::FORMAT_XLS));
    }

    /**
    * Genera un reporte de forma sincrona y no genera el id del reporte, formatos soportados: html, pdf, xls, rtf, csv, xml, jrprint
    *  
    * @param array $arguments, se envia en un array asociativo nombre => valor 
    * @param boolean $download  si se debe forzar la descarga o no
    *
    * @return el contenido a mostarse
    */
    public function getReport($parameters, $download = false, $baseUrl = null){

        $parameters = http_build_query($parameters);
        $this->req->get("/rest_v2/reports/{$this->options['reportUnitUri']}.{$this->format}?{$parameters}");
        $this->req->send();  
        $cnt = $this->req->getArrayResponse();
        $cnt['body'] = $this->parseHtml($cnt['body']); 
        return $this->output($cnt, $this->cntTyp($this->format), $download);

    }
}