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
    'Audit Trail' => array('audit'),
    \$model_id,
);\n";
?>

$this->menu = array(
    array('label' => 'Manage <?php echo $this->modelClass; ?>', 'url' => array('index')),
    array('label' => 'Buat <?php echo $this->modelClass; ?> Baru', 'url' => array('create')),
	array('template'=>'<hr/>', 'visible' => !$model->isNewRecord),
    array('label' => 'Update <?php echo $this->modelClass; ?> #' . $model_id, 'url' => array('update', 'id' => $model_id), 'visible' => !$model->isNewRecord),
	array('template'=>'<hr/>'),
	array('label'=>'Audit Trail', 'url'=>array('audit')),
);
?>

<h1>Audit Trail <?php echo $this->modelClass; ?> #<?php echo "<?php\n"; ?> echo $model_id; ?></h1>

<?php echo "<?php\n"; ?>
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model
));
?>
<br/><br/>
Histori Perubahan Data:
<?php echo "<?php"; ?> $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'audit-grid',
	'dataProvider'=>$audit->search_detail(),
    'summaryText' => '',
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
        'field',
        'old_value',
        'new_value'
	),
)); ?>
<br/><br/><br/>