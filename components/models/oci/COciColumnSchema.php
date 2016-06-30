<?php
/**
 * COciColumnSchema class file.
 *
 * @author Ricardo Grana <rickgrana@yahoo.com.br>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * COciColumnSchema class describes the column meta data of an Oracle table.
 *
 * @author Ricardo Grana <rickgrana@yahoo.com.br>
 * @package system.db.schema.oci
 */
class COciColumnSchema extends CDbColumnSchema
{
	/**
	 * Extracts the PHP type from DB type.
	 * @param string $dbType DB type
	 * @return string
	 */
	protected function extractOraType($dbType){
		if(strpos($dbType,'FLOAT')!==false) return 'double';

		if (strpos($dbType,'NUMBER')!==false || strpos($dbType,'INTEGER')!==false)
		{
			if(strpos($dbType,'(') && preg_match('/\((.*)\)/',$dbType,$matches))
			{
				$values=explode(',',$matches[1]);
				if(isset($values[1]) and (((int)$values[1]) > 0))
					return 'double';
				else
					return 'integer';
			}
			else
				return 'double';
		}
		else
			return 'string';
	}

	public function typecast($value)
	{ 

		if (is_string($value)) {
		    if (($this->dbType == "DATE" || $this->dbType == "TIMESTAMP")) {
		    	$stamp = strtotime($value);
		    	return new CDbExpression("TO_DATE('" . date("Y-m-d", $stamp) . "', 'YYYY-MM-DD')");
		    } else if (substr($this->dbType,0,6) == "NUMBER") {
		    	return ((int)$value) * 1;
		    } else {
		        $value = str_replace("'","", $value);
		        $value = trim($value);
		    }
		}

	    if(gettype($value)===$this->type || $value===null || $value instanceof CDbExpression)
	        return $value;
	    
	    if($value==='' && $this->allowNull)
	        return $this->type==='string' ? '' : null;

	    switch($this->type)
	    {
	        case 'string': return (string)$value;
	        case 'integer': return (integer)$value;
	        case 'boolean': return (boolean)$value;
	        case 'double':
	        default: return $value;
	    }
	}


	/**
	 * Extracts the PHP type from DB type.
	 * @param string $dbType DB type
	 */
	protected function extractType($dbType)
	{
		$this->type=$this->extractOraType($dbType);
	}

	/**
	 * Extracts the default value for the column.
	 * The value is typecasted to correct PHP type.
	 * @param mixed $defaultValue the default value obtained from metadata
	 */
	protected function extractDefault($defaultValue)
	{
		if(stripos($defaultValue,'timestamp')!==false)
			$this->defaultValue=null;
		else
			parent::extractDefault($defaultValue);
	}
}
