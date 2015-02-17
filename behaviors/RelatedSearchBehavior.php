<?php
/**
 * RelatedSearchBehavior Class File
 *
 * Behavior making it easier to provide search functionality for relations
 * in a grid view.
 * Also uses the {@link KeenActiveDataProvider} extension to limit the number of requests
 * to the database.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *  The MIT License
 * @author Mario De Weerd
 *
 * @example
 *
 * 1. Add the RelatedSearchBehavior to the Active record class.
 * <pre>
 *    public function behaviors() {
 *        return array(
 *            'relatedsearch'=>array(
 *                'class'=>'RelatedSearchBehavior',
 *                'relations'=>array(
 *                    'serial'=>'device.device_identifier',
 *                    'location'=>'device.location.description',
 *                    /* Next line describes a field where the value to search for is $this->deviceid
 *                      (from dropdown for instance) and the value to show is 'nametoshowtouser' which
 *                      has to be available as a value of the model ('value' is optional, 'field' is used by default \*\/
 *                    'fieldwithoptions'=>array(
 *                        'field'=>'device.displayname',
 *                        'searchvalue'=>'deviceid',   // Optional, when missing searchvalue is 'fieldwithoption', if not value of this option
 *                        'value'=>'nametoshowtouser', // Optional, value to show
 *                        'partialMatch'=>false,       // Optional, default is 'true'.
 *                        ),
 *                    /* Next line describes a field we do not search, but we define it here for convienience \*\/
 *                    'mylocalreference'=>'field.very.far.away.in.the.relation.tree',
 *                ),
 *		);
 *      $sort=array(
 *          'defaultOrder'=>'device_identifier DESC',
 *      );
 *		return $this->relatedSearch(
 *					$criteria,
 *					array('sort'=>$sort) // Optional default sort
 *      );
 * </pre>
 *
 * Add the new fields as safe attributes for the search scenario in rules:
 * <pre>
 * 	public function rules()
 *	{
 *	    return array(
 *	        [...]
 *			array('serial,location,deviceid','safe','on'=>'search'),
 *		);
 *	}
 * </pre>
 *
 * For the CGridView column specification, you can then just put 'serial' for the column
 *  (no need to do 'name'=>..., 'filter'=>..., 'value'=>... .
 *
 * Example:
 * <pre>
 * $this->widget('zii.widgets.grid.CGridView', array(
 *  [...]
 *	'columns'=>array(
 *      [...]
 *		'serial',
 *   )
 * ));
 * </pre>
 *
 *
 * @property $owner CActiveRecord
 */
class RelatedSearchBehavior extends CActiveRecordBehavior {
    /**
     * TODO: Idea to support CDBExpressions similar to this:
     array(
     'criteria'=>array(
     'select'=>array(
     'DATEDIFF(t.date_expires, CURDATE()) AS datediff',
     ),
     ),
     )
 );
 Requires adding select to critéria and indication of 'with' expression...
 */

    /**
     * Extends the search criteria with related search criteria.
     *
     * @param CDbCriteria $criteria  Existing search criteria
     * @param array $relations List of properties to find through relations
     *   'key' is the local variable name, the value is the relation.
     *   <code>
     *         array(
     *              'entity_displayname'=>'entity.displayname',
     *              'owner_displayname'=>'entity.ownerUser.displayname'
     *          );
     *   </code>
     * @return KeenActiveDataProvider
     */
    public function relatedSearch($criteria,$options=array()) {

        $relations=$this->relations;
        
        $provider=new KeenActiveDataProvider($this->getOwner());

        $sort=$provider->getSort();
        if(isset($options['sort'])) {
            foreach($options['sort'] as $name=>$value) {
                $sort->$name=$value;
            }
        }
        $sort_attributes=array();
        $with=array();
        $sort_key="";
        if(isset($_GET[$sort->sortVar])) {
            $sort_key=$_GET[$sort->sortVar];
            if(($pos=strpos($sort_key, '.'))!==false) {
                $sort_key=substr($sort_key, 0, $pos);
            }
        }
        /*@var $dbSchema CDbSchema */
        $dbSchema=$this->getOwner()->getDbConnection()->getSchema();

        $resolved_relations=array();
        /* Convert relation properties to search and sort conditions */
        foreach($relations as $var=>$relationvar) {
            if(in_array(strtolower($var),array('owner','enabled'))) {
                throw new CException("Related name '$var' in '".get_class($this->getOwner())."' clashes with Behavior property.  Choose another name.");
            }
            $partialMatch=true;
            $ovar=$var;
            if(is_array($relationvar)) {
                $relationfield=$relationvar['field'];
                if(isset($relationvar['searchvalue'])) {
                     $ovar=$relationvar['searchvalue'];
                }
                if(isset($relationvar['partialMatch'])) {
                     $partialMatch=$relationvar['partialMatch'];
                }
            } else {
                $relationfield=$relationvar;
            }
            $search_value=$this->getOwner()->{$ovar};
            
            // Get relation part, table alias, and column reference in query.
            $relation=$relationfield;
            $column=$relationfield;
            // The column name itself is everything after the last dot in the relationfield.
            $pos=strrpos($relationfield, '.');
            $column=substr($relationfield, $pos+1);

            // The full relation path is everything before the last dot.
            $pos=strrpos($relation, '.');
            $relation=substr($relation, 0, $pos);

            // The join table alias is the last part of the relation.
            $shortrelation=$relation;
            if(($pos=strrpos($shortrelation, '.'))!==false) {
                $shortrelation=substr($shortrelation, $pos+1);
            }

            // The column reference in the query is the table alias + the column name.
            $column="$shortrelation.$column";
            $column=$dbSchema->quoteColumnName($column);

            $resolved_relations[$var]=$relation;

            /* Actual search functionality */

            // If a search is done on this relation, add compare condition and require relation in query.
            // Excluding object to avoid special cases.
            if("$search_value"!==""||(is_array($search_value)&&!empty($search_value))) {
                if(!is_object($search_value)) {
                    $with[$relation]=$relation;
                    $criteria->compare($column,$search_value,$partialMatch);
                } else {
                    throw new CException("Provided search value for '$ovar' ($column) is an object, should be string or array.");
                }
            }
            // If a sort is done on this relation, require the relation in the query.
            if($sort_key==="$var") {
                $with[$relation]=$relation;
            }
            // Add sort attributes (always).
            $sort_attributes["$var"] = array(
                    "asc" => $column,
                    "desc" => "$column DESC",
                    "label" => $this->getOwner()->getAttributeLabel($var),
            );
        }
        /* Always allow sorting on default attributes */
        $sort_attributes[]="*";

        if(isset($options['sort'])){
            $sort->attributes= CMap::mergeArray($sort->attributes, $sort_attributes);
        }
        else
        {
            $sort->attributes=$sort_attributes;
        }

        /* Check defaultOrder for use of alias. */
        if(isset($sort->defaultOrder)) {
            if(is_string($sort->defaultOrder)) {
                // Currently support alias for one related field.
                if(preg_match_all('/\s*(?<var>[^,\s]*)\s+(?<sort>DESC|ASC)?\s*,?/i', $sort->defaultOrder,$matches,PREG_SET_ORDER)) {
                	$sort_fields=array();
                	foreach($matches as $m) {
	                    $var=$m['var'];
	                    $order=$m['sort'];
	                    if(isset($sort_attributes[$var])) {
	                    	if("$order"==="") {
	                    		$order="asc";
	                    	}
	                    	/* Find the appropriate sorting rule from sorting directives in $sort_attributes */
	                        $sort_fields[]=$sort_attributes[$var][strtolower($order)];
	                        /* Require the relation to make the sort possible */ 
	                        $with[$resolved_relations[$var]]=$resolved_relations[$var];
	                    } else {
	                    	$sort_fields[] = "$var $order";
	                    }
                	}
                	$sort->defaultOrder=implode(',',$sort_fields);
                }
            } /* else, is an array, do nothing */
        }
        //   print "Default order ".$sort->defaultOrder;exit;

        $criteria->mergeWith(array('with'=>array_values($with)));

        // Construct options for the data provider.
        $providerConfig=array();
        // Copy the options provides to empty array (to prevent overwriting the original array.
        $providerConfig=CMap::mergeArray($providerConfig, $options);
        // Merge our constructed options with the array.
        $providerConfig=CMap::mergeArray(
                $providerConfig,
                array(
                        'criteria'=>$criteria,
                        'sort'=>$sort,
                )
        );
        foreach($providerConfig as $key=>$value) {
            $provider->$key=$value;
        }
        return $provider;
    }


    /****************************************************
     * Implementation of getter/setters for search fields
    */
    public $relations=array();

    private $_data = array();
    /**
     * Provides set search values in the 'search' scenario and database values in any other case.
     *
     * (non-PHPdoc)
     * @see CComponent::__get()
    */
    public function __get($key) {
        if($this->getOwner()->getScenario()==='search') {
            // When in the search scenario get the value for the search stored locally.
            return (array_key_exists($key,$this->_data) ? $this->_data[$key] : null);
        } else {
            // Not in search scenario - return the normal value.
            if(isset($this->relations[$key])) {
                // This field is known in our relations
                $relationvar = $this->relations[$key];
                if(is_array($relationvar)) {
                    // Complex field: has different value for search and display value.
                    if(isset($relationvar['value'])) {
                        $valueField=$relationvar['value'];
                    } else {
                        $valueField=$relationvar['field'];
                    }
                    $search_value=CHtml::value($this->getOwner(),$valueField);
                } else {
                    // Standard field: same value for searh and for display value.
                    $relationfield=$relationvar;
                    $search_value=CHtml::value($this->getOwner(),$relationvar);
                }
                return $search_value;
            }
        }
    }

    /**
     * Sets the value for the search key.
     * (non-PHPdoc)
     * @see CComponent::__set()
     */
    public function __set($key, $value) {
        if($this->getOwner()->getScenario()==='search') {
            if($this->getOwner()->isAttributeSafe($key)) {
                $this->_data[$key] = $value;
            }
        } else {
            throw new Exception("Can only set safe search attributes");
        }
    }

    /**
     * Check if a property is available.
     *
     * Relies on __isset() because any attribute here is a property.
     *
     * (non-PHPdoc)
     * @see CComponent::canGetProperty()
     */
    public function canGetProperty($name) {
        return parent::canGetProperty($name)||$this->__isset($name);
    }

    /**
     * Validate properties that are save in the 'search scenario'.
     * (non-PHPdoc)
     * @see CComponent::canSetProperty()
     */
    public function canSetProperty($key) {
        if(parent::canSetProperty($key)) return true;
        if($this->getOwner()->getScenario()==='search') {
            return($this->getOwner()->isAttributeSafe($key));
        }
        return false;
    }

    /**
     * Checks if a value is available and set through this behavior.
     *
     * 1. Checks if the value was set in the search scenario (no need to test if this
     *    is the search scenario, because that is tested in the setter.
     * 2. Checks if the value is available through a defined relation (alias).
     *
     * (non-PHPdoc)
     * @see CComponent::__isset()
     */
    public function __isset($name) {
        if(array_key_exists($name,$this->_data)) {
            return true;
        } else {
            foreach($this->relations as $key=>$relationvar) {
                if($key===$name) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Unsets a value - only unsets search values.
     *
     * (non-PHPdoc)
     * @see CComponent::__unset()
     */
    public function __unset($key) {
        if(isset($this->_data[$key])) {
            unset($this->_data[$key]);
        }
    }

    /**
     * Implement automatic scopes for fields.
     *
     * Should be called from owner_class like this:
     * public function __call($name,$parameters) {
     *     try {
     *         return parent::__call($name,$parameters);
     *     } catch (CException $e) {
     *        if(preg_match(
     *	                '/'.Yii::t(
     *	                        'yii',
     *	                        quotemeta(
     *	                                Yii::t(
     *	                                        'yii',
     *	                                        '{class} and its behaviors do not have a method or closure named "{name}".'
     *	                                        )
     *	                                ),
     *	                                array('{class}'=>'.*','{name}'=>'.*')
     *	                        )
     *	                .'/',$e->getMessage())) {
     *             return $this->autoScope($name, $parameters);
     *         } else {
     *             throw $e;
     *         }
     *     }
     * }
     *
     * (non-PHPdoc)
     * @see CComponent::__call()
     */
    public function autoScope($name, $parameters) {
        /* @var $owner CActiveRecord */
        $owner = $this->getOwner();
        if(count($parameters) && $owner instanceof CACtiveRecord) {
            if($owner->hasAttribute($name)) {
                $column=$name;
                $value=$parameters[0];
                $partialMatch=false;
                $operator="AND";
                $escape=false;
                switch(count($parameters)) {
                    case 4:
                        $escape=$parameters[3];
                        /* fall through */
                    case 3:
                        $operator=$parameters[2];
                        /* fall through */
                    case 2:
                        $partialMatch=$parameters[1];
                        /* fall through */
                }
                $db_col = $owner->getDbConnection()->getSchema()->quoteColumnName($owner->getTableAlias().'.'.$column);
                if($value!==null||$partialMatch) {
                    $owner->getDbCriteria()->compare($db_col, $value,$partialMatch,$operator,$escape);
                } else {
                    // Creates is null condition for exact match
                    $owner->getDbCriteria()->addInCondition($column, $value, $operator);
                }
                return $owner;
            }
        }
        if(!count($parameters)) {
            throw new CException("Parameters required for autoscope '$name'");
        } else {
            throw new CException("Invalid owner of type ".get_class($owner)." for autoscope '$name'");
        }
    }
    // Suggestions:
    //  Add 'quoteRelationField' method to quote a relation according to the defined fields.

    /** History
     * 1.03  Quoting relations in database.
     * 1.04  Added autoScope.
     *       Added option 'partialMatch' for relation.
     * 1.05  Enable multiple attributes in default sort.
     * 1.06  Fix to autoScope - return owner (chaining) + correct example in comment.
     * 1.07  Rely on DataProvider to create sort object in order to get the usual key for the $_GET sort var.
     * 1.08  Fix in KeenDataProvider to quote column in GROUP BY.
     * 1.09  Allow array for search value.
     */
}