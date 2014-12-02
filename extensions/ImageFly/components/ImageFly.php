<?php
Yii::import("ext.ImageFly.vendors.canvas");

/**
 * Classe que implementa funcionalidade de 
 * Redimensionamento de thumbnails on the fly
 * utilizando como componente a biblioteca 
 * canvas e GD lib do PHP
 * 
 * <pre>
 *  echo Chtml::image(Image::Instance()->get($model, 'image_attribute', Image::Medium, Image::Medium), $alt, $htmlOptions);
 * </pre>
 * 
 * @see CHtml @link http://www.yiiframework.com/doc/api/1.1/CHtml Classe static que implementa a criação de html.
 * @see canvas @link http://www.daviferreira.com/posts/canvas-nova-classe-para-manipulacao-e-redimensionamento-de-imagens-com-php Classe de Manipulação de imagens Utilizando GD
 * 
 * @author Marcus Vinicius O. Freire
 */
class ImageFly
{
    /**
     * Constante que define tamanho pequeno
     */
    const SMALL = 50;
    
    /**
     * Constante que define tamanho medio
     */
    const MEDIUM = 100;
    
    /**
     * Constante que define tamanho Grande
     */
    const LARGE = 200;
    
    /**
     * Constante que define tamanho Grande++
     */
    const BIG = 500;
    
    /**
     * Attributo que arnazena o caminho da Url da imagem
     * @var string 
     */
    private $webRoot;
    
    /**
     * Attributo que arnazena o caminho da pasta do servidor
     * @var string 
     */
    private $pathRoot;
    
    /**
     * Attributo que arnazena o nome do arquivo default
     * @var string 
     */
    private $file;
    
    /**
     * Variavel statica que guarda apenas uma instancia do  objecto em memoria do servidor.
     * @static
     * @var type 
     */
    private static $instance;
    
    /**
     * Metodo statico de Singleton - Instanciamento de apenas um objeto no sistema inteiro
     * 
     * @static
     * @return ImageFly
     */
    public static function Instance(){
        if(empty(self::$instance))
        {
            self::$instance = new ImageFly();
        }
        
        return self::$instance;
    }
    
    /**
     * Construtor da classe.
     * 
     * @param string $webRoot
     * @param string $pathRoot
     * @param string $file
     * @return Image
     */
    private function __construct($webRoot = null, $pathRoot = null, $file = "images")
    {
        $this->webRoot = $webRoot;
        $this->pathRoot = $pathRoot;
        $this->file = $file;
        
        if(empty($webRoot) || empty($pathRoot))
        {
            $this->webRoot = Yii::app()->getBaseUrl(true) . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR;
            $this->pathRoot = Yii::getPathOfAlias("webroot") . DIRECTORY_SEPARATOR . $file. DIRECTORY_SEPARATOR;
        }
        
        return $this;
    }
    
    /**
     * Retorna caminho da url onde esta buscando as imagens
     * @return string
     */
    public function getWebRoot() {
        return $this->webRoot;
    }
    
    /**
     * Altera o caminho da url onde busca as imagens
     * @param string $webRoot
     */
    public function setWebRoot($webRoot) {
        $this->webRoot = $webRoot;
    }

    /**
     * Retorna o caminha da pasta onde busca as imagens
     * @return string
     */
    public function getPathRoot() {
        return $this->pathRoot;
    }

    /**
     * Altera o caminho da pasta onde busca as imagens
     * @param string $pathRoot
     */
    public function setPathRoot($pathRoot) {
        $this->pathRoot = $pathRoot;
    }
    
    /**
     * Metodo que valida se o thumbnail do tamanho solicitado ja existe.
     * Se não existe ele cria o novo thumbnail e retorna o 
     * caminha da url para ser acessado.
     * 
     * Se ja existe valida o arquivo original pelo seu filemtime()
     * data de modificação, caso o fila time dele seja diferente do thumbnail 
     * ele gera um novo thumbnail com a nova imagem
     * 
     * <pre>
     *  echo Chtml::image(Image::Instance()->get($model, 'image_attribute', Image::Medium, Image::Medium), $alt, $htmlOptions);
     * </pre>
     * 
     * @param mixed $model
     * @param string $attribute
     * @param integer $size
     * @return string
     * 
     * @throws Exception
     */
    public function get($model, $attribute, $width = self::MEDIUM, $height = self::MEDIUM) {

        $path = $this->getThumbName($model, $width, $height);
        
        if(empty($model->$attribute))
            return '';
        else
            $imageName = $model->$attribute;
        
        $filePath = $this->pathRoot . $model->imagePath;
        $savePath = $this->pathRoot . $model->imagePathThumb . $width . "_" . $height . DIRECTORY_SEPARATOR;
        $c = filemtime($filePath . $imageName)."_".$imageName;
        
        if (!file_exists($this->pathRoot . $path . $c)) 
        {
            if(!is_dir($this->pathRoot . $model->imagePathThumb . $width . "_" . $height))
            {
                $listfolderName = explode("/", $model->imagePathThumb);
                $listfolderName[] = $width . "_" . $height;
                self::validCreateFoldersFiles($listfolderName, true, $this->file);
            }
            
            $image = $this->resizeAndSave($filePath.$imageName, $savePath, $c, $model, $width, $height);
        }
        else
        {
            if(!$this->validImage($filePath.$imageName, $c)){
                unlink($savePath.$c);
                $image = $this->resizeAndSave($filePath, $savePath, $c, $model, $width, $height);
            }else{
                $image = $this->webRoot . $path . $c;
            }
        }

        return $image;
    }
    
    /**
     * Metodo que valida se o arquivo solicitado foi alterado no servidor
     * 
     * @access private
     * @param string $filename
     * @param string $imageName
     * @return boolean
     */
    private function validImage($filename, $imageName)
    {
        $imageInfo = filemtime($filename);
        
        $check = explode("_", $imageName);
        if(count($check)==2){
            if($imageInfo == $check[0]){
                return true;
            }
        }
        
        return false;
    }

    /**
     * 
     * @param string $imagePath
     * @param string $savePath
     * @param string $imageName
     * @param mixed $model
     * @param integer $width
     * @param integer $height
     * @return string
     */
    private function resizeAndSave($imagePath, $savePath, $imageName, $model, $width, $height) {
        
        $canvas = canvas::Instance();
        $canvas->carrega($imagePath)
               ->redimensiona($width, $height, 'proporcional')
               ->grava($savePath.$imageName);
        
        return $this->webRoot.$this->getThumbName($model, $width, $height).$imageName;
    }
    
    /**
     * Metodo que retorna o nome do thumbnail de acordo com o modelo passado
     * 
     * @param mixed $model
     * @param integer $width
     * @param integer $height
     * @return string
     */
    private function getThumbName($model, $width, $height) {
        return $model->imagePathThumb . $width . '_' . $height . DIRECTORY_SEPARATOR;
    }

    /**
     * Metodo que retorna o path a ser salva a imagem de acordo com o modelo.
     * 
     * @param mixed $model
     * @param string $attribute
     * @return string
     * @throws Exception
     */
    public function getPath($model, $attribute) {
        
        if ($model->$attribute instanceof CUploadedFile) {
            return "images" . DIRECTORY_SEPARATOR . $model->imagePath . md5($model->$attribute->getName()) . "." . $model->$attribute->getExtensionName();
        }

        throw new Exception("$attribute not found.");
    }
    
    /**
     * Metodo estatico que cria arquivos de acordo com a necesidade.
     * 
     * <pre>
     * $r = Image::validCreateFoldersFiles(array('teste', 'image'), true, 'files');
     * </pre>
     * 
     * @param array $listfolderName array de string com o nome de arquivos que deseja criar
     * @param boolean se deseja criar ou apenas validar se existe esse caminho true = criar.
     * @param string $file = nome do arquivo
     * @return string|object
     */
    public static function validCreateFoldersFiles($listfolderName, $create = false, $file = 'files') {
        $root = Yii::getPathOfAlias('webroot') . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR;
        $errors = array();

        foreach ($listfolderName as $folderName) {
            if (empty($folderName))
                continue;

            if (!is_dir($root . $folderName) && $create) {
                $folderName = strtolower($folderName);
                if (!file_exists($root . $folderName)) {
                    if (!mkdir($root . $folderName, 0777)) {
                        array_push($errors, array(
                            'error' => Yii::t('app', 'Unable to create {filename}', array(
                                '{filename}' => $folderName
                            )),
                        ));
                    }
                    else
                        $root = $root . $folderName . DIRECTORY_SEPARATOR;
                }
                else
                    $root = $root . $folderName . DIRECTORY_SEPARATOR;
            }
            else
                $root = $root . $folderName . DIRECTORY_SEPARATOR;
        }

        if (empty($errors)) {
            return $root;
        } else {
            return (Object) $errors;
        }
    }
}

?>
