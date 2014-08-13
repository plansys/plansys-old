<?php

use Doctrine\ORM\EntityRepository;

abstract class YDBaseRepository extends EntityRepository implements IDataProvider {

    public $modelClass;
    public $model;
    public $keyAttribute;
    public $data;
    protected $_id;
    private $_data;
    private $_keys;
    private $_totalItemCount;
    private $_sort;
    private $_pagination;
    private $_criteria;
    private $_countCriteria;

    public function getId() {
        
    }

    public function getPagination($className = 'CPagination') {
        
    }

    public function setPagination($value) {
        
    }

    public function getData($refresh = false) {
        
    }

    public function setData($value) {
        
    }

    public function getKeys($refresh = false) {
        
    }

    public function setKeys($value) {
        
    }

    public function getItemCount($refresh = false) {
        
    }

    public function getTotalItemCount($refresh = false) {
        
    }

    public function setTotalItemCount($value) {
        
    }

    public function getCriteria() {
        
    }

    public function setCriteria($value) {
        
    }

    public function getCountCriteria() {
        
    }

    public function setCountCriteria($value) {
        
    }

    public function getSort($className = 'CSort') {
        
    }

    public function setSort($value) {
        
    }

    abstract protected function fetchData();

    abstract protected function calculateTotalItemCount();

    protected function fetchKeys() {
        
    }

    private function _getSort($className) {
        
    }

}
