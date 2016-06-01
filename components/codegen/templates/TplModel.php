<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */
?>
<?php echo "<?php\n"; ?>

class <?php echo $modelClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
<?php if (!!@$options['softDelete']): ?>
	public $_softDelete = array(
		'column' => '<?php echo $options['softDelete']['column']; ?>',
		'value' => '<?php echo $options['softDelete']['value']; ?>'
	);
<?php endif; ?>

	public function tableName()
	{
		return '<?php echo $tableName; ?>';
	}

	public function rules()
	{
		return array(
<?php foreach($rules as $rule): ?>
			<?php echo $rule.",\n"; ?>
<?php endforeach; ?>
		);
	}

	public function relations()
	{
		return array(
<?php

foreach ($relations as $n => $r) {
    if (in_array($n, ['user', 'role', 'email'])) {
        $r = str_replace('PUser', 'User', $r);
        $r = str_replace('PRole', 'Role', $r);
        $r = str_replace('PEmail', 'Email', $r);

        $relations[$n] = $r;
    }
}

foreach($relations as $name=>$relation): ?>
			<?php
                        $name = strtolower($name);
                        echo "'$name' => $relation,\n"; ?>
<?php endforeach; ?>
		);
	}

	public function attributeLabels()
	{
		return array(
<?php foreach($labels as $name=>$label): ?>
			<?php echo "'".$name."' => '".str_replace("'","\'",$label)."',\n"; ?>
<?php endforeach; ?>
		);
	}

<?php if($connectionId!='db'):?>
	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()-><?php echo $connectionId ?>;
	}

<?php endif?>
}
