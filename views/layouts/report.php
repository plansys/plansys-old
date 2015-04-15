<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
        <style>
            .table{
                border: 2px solid red;
            }
        </style>
    </head>
    <body>
        <?php echo $content; ?>
    </body>
</html>
