<?php

/**
 * This is the model class for table "report".
 *
 * The followings are the available columns in table 'report':
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property string $module
 */
class Report extends ActiveRecord {

    protected $name;
    protected $path;
    protected $module;
    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'report';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, path', 'required'),
            array('name, module', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, path, module', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array of used behaviors
     */
    public function behaviors() {
        $behaviors = array(
        );
        return $behaviors + parent::behaviors();
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'path' => 'Path',
            'module' => 'Module',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('path', $this->path, true);
        $criteria->compare('module', $this->module, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Report the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function reportItems($reportRaw) {
        $reports = array();
        foreach ($reportRaw as $k => $v) {
            $reports[$v['module']][] = $v->attributes;
        }

        $reports2 = array();
        foreach ($reports as $k => $v) {
            $reports2[] = array(
                'module' => ucfirst($k),
                'items' => $v
            );
        }

        return $reports2;
    }

}
