<!DOCTYPE html>
<html lang="en" ng-app="main">
	<?php include("_head.php"); ?>

	<body ng-controller="MainController">
		<?php 
			include("topbar.php");
		?>
		<main class="main" id="content" ng-cloak>
        		<?php echo $content; ?>
    		</main>	
	</body>
</html>