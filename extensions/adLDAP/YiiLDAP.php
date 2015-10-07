<?php
/**
 * adLDAP support for Yii
 *
 * @author Konrad Fedorczyk
 * @version 1.0
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2013 Konrad Fedorczyk
 *
 * Copyright (C) 2013 Konrad Fedorczyk.
 *
 * 	This program is free software: you can redistribute it and/or modify
 * 	it under the terms of the GNU Lesser General Public License as published by
 * 	the Free Software Foundation, either version 2.1 of the License, or
 * 	(at your option) any later version.
 *
 * 	This program is distributed in the hope that it will be useful,
 * 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 	GNU Lesser General Public License for more details.
 *
 * 	You should have received a copy of the GNU Lesser General Public License
 * 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For third party licenses and copyrights, please see phpmailer/LICENSE
 *
 */

/**
 * Inlude adLDAP class
 */
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'adLDAP.php');


/**
 * YiiLDAP is a simple wrapper for an adLDAP class.
 * @see http://adldap.sourceforge.net/
 *
 * @author Konrad Fedorczyk
 * @package application.extensions.adLDAP
 * @since 1.1,14
 */
class YiiLDAP extends  CApplicationComponent {
   /**
    * The internal adLDAP object.
    *
    * @var object adLDAP
    */
	private $_adLDAP=null;
	
   /**
    * Options for a ldap connection
    *
    * @var array options
    */	
	public $options=null;

   /**
    * Init method for the application component mode.
    */	
	public function init() {
		// try to connect to domain controller
	    try {
			$this->_adLDAP = new adLDAP($this->options);
		}
	    catch (adLDAPException $e) {
			throw new CException($e);   
		}		
	}

   /**
    * Call an adLDAP function
    *
    * @param string $method the method to call
    * @param array $params the parameters
    * @return mixed
    */	
	public function __call($method, $params) {
		if(is_object($this->_adLDAP) && get_class($this->_adLDAP)==='adLDAP') return call_user_func_array(array($this->_adLDAP, $method), $params);
	}	
}

?>