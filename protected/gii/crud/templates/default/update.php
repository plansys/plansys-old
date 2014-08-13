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
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->class2name($this->modelClass);
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	'Update #' . \$model->{$this->tableSchema->primaryKey},
);\n";
?>

$this->menu=array(
    array('label' => 'Manage <?php echo $this->modelClass; ?>', 'url' => array('index')),
    array('label' => 'Buat <?php echo $this->modelClass; ?> Baru', 'url' => array('create')),
    array('template' => '<hr/>'),
    array('label' => 'Audit Trail', 'url' => array('audit')),
    array('template' => '<hr/>'),
    array('label' => 'Audit Trail <?php echo $this->modelClass; ?> #' . $model->id, 'url' => array('audit', 'id' => $model->id)),
);
?>

<h1>Update <?php echo $this->modelClass." #<?php echo \$model->{$this->tableSchema->primaryKey}; ?>"; ?></h1>
<div class="clearfix"></div>

<?php echo "<?php \$this->renderPartial('_form', array('model'=>\$model)); ?>"; ?>