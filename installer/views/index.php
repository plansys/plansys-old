<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="content-language" content="en"/>
<link rel="stylesheet" type="text/css" href="css/main.css" />
<link rel="stylesheet" type="text/css" href="../static/css/bootstrap.css" />
<title>Plansys Requirement Checker</title>
</head>

<body>
<nav class="navbar navbar-default navbar-fixed-top">
  <span class="navbar-brand">Plansys</span>
</nav>
<div id="page" style="margin-top:60px;">

<div id="page-header">
<h1>Plansys Requirement Checker</h1>
</div><!-- header-->

<div id="content">
<p>
This script checks if your server configuration meets the requirements
for running Plansys Web applications.
It checks if the server is running the right version of PHP,
if appropriate PHP extensions have been loaded, and if php.ini file settings are correct.
</p>
<?php
function setup(){
    $basedir = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
    $basedir = array_slice($basedir, 0, count($basedir) -2);
    $plansys = array_pop($basedir);
    $basedir = implode(DIRECTORY_SEPARATOR, $basedir) . DIRECTORY_SEPARATOR;

    if (!file_exists($basedir .'assets')) {
        mkdir($basedir . 'assets', 0777, true);
    }
    if (!file_exists($basedir . $plansys . DIRECTORY_SEPARATOR. 'runtime')) {
        mkdir($basedir .  $plansys . DIRECTORY_SEPARATOR. 'runtime', 0777, true);
    }
    if (!file_exists($basedir . 'repo')) {
        mkdir($basedir . 'repo', 0777, true);
    }

    if (!file_exists($basedir . 'index.php')) {
        $template = file_get_contents($basedir . $plansys . DIRECTORY_SEPARATOR . 'index.template.php');
        $template = str_replace("{root}", $plansys, $template);
        file_put_contents($basedir . "index.php" , $template);
    }
    
    if (!file_exists($basedir . '.gitignore')) {
        file_put_contents($basedir . ".gitignore", $plansys);
    }

    touch('setup_db.lock');
}
?>
<?php if($result>0): ?>
    <?php setup();?>
    <div class="alert alert-success">
    <table>
    <tr>
        <td><span class="glyphicon glyphicon-ok" style="font-size:30px;"></span></td>
        <td style="padding-left:15px;">Congratulations! Your server configuration satisfies all requirements by Plansys.</td>
    </tr>
    </table>
    <br/><a href="../../" class="btn btn-primary">Plansys Setup</a>
    </div>
<?php elseif($result<0): ?>
    <?php setup();?>
    <div class="alert alert-warning">
    <table>
    <tr>
        <td><span class="glyphicon glyphicon-ok" style="font-size:30px;"></span></td>
        <td style="padding-left:15px;">Your server configuration satisfies the minimum requirements by Plansys. Please pay attention to the warnings listed below if your application will use the corresponding features.</td>
    </tr>
    </table>
    <br/><a href="../../" class="btn btn-primary">Plansys Setup</a>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
    <table>
    <tr>
        <td><span class="glyphicon glyphicon-remove" style="font-size:30px;"></span></td>
        <td style="padding-left:15px;">Unfortunately your server configuration does not satisfy the requirements by Plansys.</td>
    </tr>
    </table>
    </div>
<?php endif; ?>
</p>

<h2>Details</h2>

<table class="result table">
<tr><th>Name</th><th style="width:12%;">Result</th><th>Required By</th><th>Memo</th></tr>
<?php foreach($requirements as $requirement): ?>
<tr>
	<td>
	<?php echo $requirement[0]; ?>
	</td>
	<td class="<?php echo $requirement[2] ? 'passed' : ($requirement[1] ? 'failed' : 'warning'); ?>">
	<?php echo $requirement[2] ? '<span class="glyphicon glyphicon-ok-sign"></span> <b>Passed</b>' : ($requirement[1] ? '<span class="glyphicon glyphicon-remove-sign"></span> <b>Failed</b>' : '<span class="glyphicon glyphicon-exclamation-sign"></span> <b>Warning</b>'); ?>
	</td>
	<td>
	<?php echo $requirement[3]; ?>
	</td>
	<td>
	<?php echo $requirement[4]; ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<table>
<tr>
<td class="passed">&nbsp;</td><td>passed</td>
<td class="failed">&nbsp;</td><td>failed</td>
<td class="warning">&nbsp;</td><td>warning</td>
</tr>
</table>

</div><!-- content -->
<div id="footer">
<?php echo $serverInfo; ?>
</div><!-- footer -->

</div><!-- page -->
</body>
</html>