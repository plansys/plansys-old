<?php

/**
 * YDComponent
 *
 * @author    Giang Nguyen (tn.hoanggiang@gmail.com)
 * @link      http://smartexts.com
 * @copyright Copyright &copy; 2014, smartexts.com.
 * @license   https://github.com/bluecloudy/yiidoctrine2
 *
 * Usage:
 *
 * 'doctrine'=>array(
 *        'class' => 'YiiDoctrine.components.YDComponent',
 *        'basePath'      => $appPath,
 *        'proxyPath'     => $appPath.'/proxies',
 *        'entityPath'    => array($appPath.'/models'),
 *        'cachePath'     => dirname($appPath).'/cache',
 *        'db' => array(
 *            'driver' => 'pdo_sqlite',
 *            'path' => $appPath.'/data/blog.db'
 *        )
 *    ),
 *
 *  Note: $appPath = Yii BasePath
 */
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

Yii::import('YiiDoctrine.repositories.*');
Yii::import('YiiDoctrine.controllers.*');

class YDComponent extends CApplicationComponent {

    private $em = null;
    private $basePath;
    private $proxyPath;
    private $entityPath;
    private $cachePath;
    private $db;

    public function init() {
        // Add alias path
        Yii::setPathOfAlias('Doctrine', $this->getBasePath() . '/vendor/doctrine');
        Yii::setPathOfAlias('Symfony', $this->getBasePath() . '/vendor/Symfony');

        // Init DoctrineORM
        $this->initDoctrine();

        parent::init();
    }

    public function getBasePath() {
        return $this->basePath;
    }

    public function setBasePath($basePath) {
        $this->basePath = $basePath;
    }

    public function initDoctrine() {
        $cache = new Doctrine\Common\Cache\FilesystemCache($this->getCachePath());
        $driver = new Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver($this->entityPath);
        $config = Setup::createConfiguration($this->entityPath);
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir($this->getProxyPath());
        $config->setProxyNamespace('Proxies');
        $config->setAutoGenerateProxyClasses(true);
        $config->setMetadataDriverImpl($driver);
        
        $this->em = EntityManager::create($this->db, $config);
    }

    public function getCachePath() {
        return $this->cachePath;
    }

    public function setCachePath($cachePath) {
        $this->cachePath = $cachePath;
    }

    public function getProxyPath() {
        return $this->proxyPath;
    }

    public function setProxyPath($proxyPath) {
        $this->proxyPath = $proxyPath;
    }

    public function getEntityPath() {
        return $this->entityPath;
    }

    public function setEntityPath($entityPath) {
        $this->entityPath = $entityPath;
    }

    public function getDb() {
        return $this->db;
    }

    public function setDb($info) {
        $this->db = $info;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager() {
        return $this->em;
    }

}
