<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::app()->name . ' - Login';
?>


<div ui-layout>
    <div>
        <div class="login form" >
            <center>
                <h3>System Login</h3>
            </center>
            <hr/>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'login-form',
                'enableClientValidation' => true,
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                ),
            ));
            ?>
            <table>
                <tr>
                    <td>
                        <?php echo $form->labelEx($model, 'username'); ?></td>
                    <td>
                        &nbsp;&nbsp;
                        <?php echo $form->textField($model, 'username'); ?>
                        <?php echo $form->error($model, 'username'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $form->labelEx($model, 'password'); ?></td>
                    <td>
                        &nbsp;&nbsp;
                        <?php echo $form->passwordField($model, 'password'); ?>
                        <?php echo $form->error($model, 'password'); ?>
                    </td>
                </tr>
            </table>

            <div>
                <hr/>
                <?php
                echo CHtml::submitButton('Login', array(
                    'class' => 'btn btn-primary'
                ));
                ?>
            </div>

            <?php $this->endWidget(); ?>
        </div><!-- form --></div>
</div>