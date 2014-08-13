<?php

/**
 * YiiDoctrine2Controller
 *
 * @author    Giang Nguyen (tn.hoanggiang@gmail.com)
 * @link      http://smartexts.com
 * @copyright Copyright &copy; 2014, smartexts.com.
 * @license   https://github.com/bluecloudy/yiidoctrine2
 */
class YDController extends CController {

    /**
     * @var Doctrine\ORM\EntityManager $entityManager
     */
    private $entityManager = null;

    /**
     * @return CHttpRequest
     */
    public function getRequest() {
        return Yii::app()->request;
    }

    /**
     * @return YDBaseRepository
     */
    public function getRepository() {
        $class = get_class($this);
        return $this->getEntityManager()->getRepository(substr($class, 0, (strlen($class) - 10)));
    }

    /**
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        if (is_null($this->entityManager)) {
            $this->entityManager = Yii::app()->doctrine->getEntityManager();
        }
        return $this->entityManager;
    }

}
