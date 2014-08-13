<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */

<?php
$label=$this->class2name($this->modelClass);
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	'Manage',
);\n";
?>

$this->menu=array(
	array('label'=>'Manage <?php echo $this->modelClass; ?>', 'url'=>array('index')),
	array('label'=>'Buat <?php echo $this->modelClass; ?> Baru', 'url'=>array('create')),
	array('template'=>'<hr/>'),
	array('label'=>'Audit Trail', 'url'=>array('audit')),
);

?>

<h1>Manage <?php echo $this->class2name($this->modelClass); ?></h1>
<div class="top-right">
    <?php echo "<?php\n"; ?>
    echo CHtml::link('+ Buat <?php echo $this->modelClass; ?> Baru ', array('create'), array(
        'class' => 'btn'
    ));
    ?>
</div>
<div class="clearfix"></div>

<?php echo "<?php"; ?> $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
<?php
$count=0;
foreach($this->tableSchema->columns as $column)
{
    if ($column->name == "id")
        continue;
    
	if(++$count==7)
		echo "\t\t/*\n";
	echo "\t\t'".$column->name."',\n";
}
if($count>=7)
	echo "\t\t*/\n";
?>
		array(
			'class'=>'CButtonColumn',
            'template' => '{update} {delete}'
		),
	),
)); ?>
