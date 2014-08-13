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
	'Entri Data',
);\n";
?>

$this->menu=array(
	array('label'=>'Manage <?php echo $this->modelClass; ?>', 'url'=>array('index')),
);
?>

<h1>Entri Data <?php echo $this->modelClass; ?></h1>
<div class="clearfix"></div>

<?php echo "<?php \$this->renderPartial('_form', array('model'=>\$model)); ?>"; ?>
