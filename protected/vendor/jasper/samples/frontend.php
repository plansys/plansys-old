<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING ^ E_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', true);

// Init
try {
    require_once('../Jasper/Jasper.php');
    $customerSpace = '/reports/';
    //$customerSpace = '/test/';
    $jasper        = new \Jasper\Jasper('localhost:8080', 'jasperadmin', 'jasperadmin');
} catch (Exception $e) {
    echo $e, PHP_EOL;
    exit();
}

if (!empty($_GET['fetch'])) {
    $report = $jasper->getReport($customerSpace . $_GET['fetch'], 'pdf', array('CUSTOMERID' => 2));
    header('Content-Type: application/pdf');
    echo $report;
    exit();
}

if (!empty($_GET['delete'])) {
    try {
        $report = $jasper->getResourceDescriptor($customerSpace . $_GET['delete']);
        // Delete the reportUnit
        $jasper->deleteResource($customerSpace . $_GET['delete']);
        // Delete attached templates and images
        foreach ($report->getChildResources() as $resource) {
            if ($resource->getWsType() == 'jrxml' ||
                $resource->getWsType() == 'img') {
                    $jasper->deleteResource($resource);
            }
        }
    } catch (\Exception $e) {
        if ($e->getCode() == 403) {
            // The file to delete is in use by other reports
        } else {
            // Other error
            echo $e, PHP_EOL;
        }
    }
    header("Location: frontend.php");
    exit();
}

if (!empty($_GET['edit'])) {
}

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <title>Reporting</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <link rel="stylesheet" href="serve/normalize.min.css">
        <link rel="stylesheet" href="serve/bootstrap.min.css">
        <link rel="stylesheet" href="serve/custom.css">
        <script src="serve/jquery-1.9.1.min.js"></script>
        <script src="serve/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <h1>Reporting</h1>
            <hr>
            <p class="lead well well-small">Repository</p>

            <table class="table table-hover table-condensed">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Label</th>
                  <th>Description</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $reports = $jasper->getFolder($customerSpace);
                foreach ($reports as $key => $report):
                ?>
                <tr>
                    <td><?php echo $report->getName(); ?></td>
                    <td><?php echo $report->getLabel(); ?></td>
                    <td><?php echo $report->getDescription(); ?></td>
                    <td><a href="?fetch=<?php echo str_replace($customerSpace, '', $report->getUriString()); ?>"><i class="icon-download"></i> </a></td>
                    <td><a href="?edit=<?php echo str_replace($customerSpace, '', $report->getUriString()); ?>"><i class="icon-edit"> </i> </a></td>
                    <td><a href="?delete=<?php echo str_replace($customerSpace, '', $report->getUriString()); ?>"><i class="icon-trash"> </i> </a></td>
                <?php
                endforeach;
                ?>

                </tbody>
            </table>

            <p class="lead well well-small">New Report</p>
            
            <?php
            echo '<pre>';
            echo htmlspecialchars($jasper->getResourceContents('/reports/samples/StandardChartsAegeanReport_files/standardCharts.properties'));
            echo "\n\n--------------------------\n\n";
            echo htmlspecialchars($jasper->getResourceContents('/reports/samples/StandardChartsAegeanReport_files/StandardChartsAegeanReport'));
            echo '</pre>';
//            $report = $jasper->getReport('/reports/samples/StandardChartsReport', 'html');
//            echo str_replace('/jasperserver/rest_v2/reportExecutions/', './attachement.php?get=', $report);

            ?>

        </div>
    </body>
</html>