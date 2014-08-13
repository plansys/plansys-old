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
	'Audit Trail',
);\n";
?>

$this->menu=array(
	array('label'=>'Manage <?php echo $this->modelClass; ?>', 'url'=>array('index')),
	array('label'=>'Buat <?php echo $this->modelClass; ?> Baru', 'url'=>array('create')),
);

?>

<h1>Audit Trail <?php echo $this->class2name($this->modelClass); ?></h1>
<div class="clearfix"></div>
<?php echo "<?php"; ?> $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
            'header' => 'Tanggal / Jam',
            'name' => 'stamp'
        ),
        array(
            'header' => 'User',
            'name' => 'user_search'
        ),
        array(
            'header' => 'Role',
            'name' => 'role_search'
        ),
        array(
            'type' => 'raw',
            'header' => 'Action',
            'name' => 'action',
            'value' => '$data->action_label'
        ),
        array(
            'header' => '<?php echo $this->modelClass; ?> ID#',
            'name' => 'model_id'
        ),
		array(
			'class'=>'CButtonColumn',
            'template' => '{view}',
            'buttons' => array(
                'view' => array(
                    'url' => 'array("audit","id"=>$data->model_id)'
                )
            )
		),
	),
)); ?>
