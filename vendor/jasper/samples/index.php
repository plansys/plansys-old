<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING ^ E_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', true);
header('Content-Type: text/plain');



// Init
try {
    require_once('../Jasper/Jasper.php');
    $customerSpace = '/reports/Customers/';
    $jasper        = new \Jasper\Jasper();
    $jasper->login();
} catch (Exception $e) {
    echo $e;
    exit();
}



// Fetch a report export
if (!empty($_GET['getExport'])) {
    try {
        $export = $jasper->getReport(new \Jasper\JasperReportUnit($customerSpace . '/' . $_GET['getExport']), 'html');
    } catch (Exception $e) {
        echo $e;
    }

    header('Content-disposition: attachment; filename=report-' . date('Y-m-d') . '.html');
    header('Content-type: text/html');
    echo $export;
    exit();
}


/*
// Upload an image file
echo "\n\n" . "// Upload an image file" . "\n" . str_pad('', 128, '_') . "\n\n";
try {
    $file = new \Jasper\JasperImage($customerSpace . '/image02.gif');
    $file->setPropHasData('true');
    $file->setLabel('test123');
    $file->setDescription('Test');
    $jasper->createContent($file, file_get_contents('serve/test.gif'));
} catch (Exception $e) {
    echo $e;
}
*/



// Upload a jrxml file
echo "\n\n" . "// Upload a jrxml file" . "\n" . str_pad('', 128, '_') . "\n\n";
try {
    $report = new \Jasper\JasperJrxml($customerSpace . '/test01.jrxml');
    $report->setIsNew('false')
           ->setPropVersion((string)time());
    $jasper->createContent($report, file_get_contents('jrxml/test01.jrxml'));
} catch (Exception $e) {
    echo $e;
}




// Create a reportUnit
echo "\n\n" . "// Create a reportUnit" . "\n" . str_pad('', 128, '_') . "\n\n";
try {
//    $jasper->deleteResource($customerSpace . '/corp');
//    $jasper->deleteResource($customerSpace . '/corp.jrxml');
    
    // Instance of the Report
    $report = new \Jasper\JasperReportUnit($customerSpace . '/REPORT');
    $report->setLabel('REPORT');
    $report->setIsNew('false')
           ->setPropVersion((string)time());
    // jrxml Template
    $jrxml = $jasper->getResourceDescriptor($customerSpace . '/test01.jrxml');
    $jrxml->setPropRuIsMainReport('true')
          ->setIsNew('false')
          ->setPropVersion((string)time());
    
//    // Abfragesteuerelement bestehend aus InputControl und DataType
//    $inputControl = new \Jasper\JasperInputControl($customerSpace . '/cid');
//    $inputControl->setLabel('cid');
//    $dataType = new \Jasper\JasperDataType($customerSpace . '/cid');
//    $dataType->setLabel('cid');
//    $inputControl->addChildResource($dataType);

//    // Image
//    $img = new \Jasper\JasperImage('/images/adn_logo01');
//    $img->setIsNew('false')
//        ->setPropIsReference('true')
//        ->setPropReferenceUri('/images/adn_logo01');

    // Datasource
    $mongo = new \Jasper\JasperDatasource();
    $mongo->setPropIsReference('true');
    $mongo->setPropReferenceUri('/datasources/mongo_local_test');

    // Put everything together and deploy the report
    $report->addChildResource($mongo);
    $report->addChildResource($jrxml);
//    $report->addChildResource($img);
//    $report->addChildResource($inputControl);

    print_r($report->getXml(true));
    $jasper->createResource($report);
} catch (Exception $e) {
    echo $e;
}


/*
// Create a folder
echo "\n\n" . "// Create a folder" . "\n" . str_pad('', 128, '_') . "\n\n";
try {
    $folder = new \Jasper\JasperFolder($customerSpace . '/newFolder');
    $folder->setLabel('My new Folder');
    $folder->setDescription('contains reports about magic');
    $folder->setIsNew(true);
    $jasper->createFolder($folder);
} catch (Exception $e) {
    echo $e;
}
*/


// Directory listing
echo "\n\n" . "// Directory listing" . "\n" . str_pad('', 128, '_') . "\n\n";
try {
    $resources = $jasper->getFolder($customerSpace);

    echo str_pad('Label', 32, ' ') . str_pad('URI', 64, ' ') . str_pad('Type', 32, ' ') . str_pad('Description', 64, ' ') . "\n\n";
    foreach ($resources as $res) {
        echo str_pad($res->getLabel(), 32, ' ') . str_pad($res->getUriString(), 64, ' ') . str_pad($res->getWsType(), 32, ' ') . str_pad($res->getDescription(), 64, ' ') . "\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo $e;
}


/*
// Delete previously created folder
echo "\n\n" . "// Delete previously created folder" . "\n" . str_pad('', 128, '_') . "\n\n";
try {
    $jasper->deleteResource($folder);
} catch (Exception $e) {
    echo $e;
}
*/


/*
// Let's have a look at a reportUnit (first is plain object, second is resourceDescriptor xml)
echo "\n\n" . "// Let's have a look at a reportUnit (first is plain object, second is resourceDescriptor xml)" . "\n" . str_pad('', 128, '_') . "\n\n";
try {
    $reportUnit = $jasper->getResourceDescriptor(new \Jasper\JasperResourceDescriptor('/reports/Customers/testXX'));
    print_r($reportUnit);
    print_r($reportUnit->getXml(true));
} catch (Exception $e) {
    echo $e;
}



// And now open test02 (its a jrxml file) (first is resourceDescriptor xml, second is contents)
echo "\n\n" . "// And now open test02 (its a jrxml file) (first is resourceDescriptor xml, second is contents)" . "\n" . str_pad('', 128, '_') . "\n\n";
try {
    $jrxml = $jasper->getResourceDescriptor(new \Jasper\JasperResourceDescriptor('/reports/Customers/test02'));
    print_r($jrxml->getXml(true));
    print_r($jasper->getResourceContents($jrxml));
} catch (Exception $e) {
    echo $e;
}



// Show the descriptor of a datasource
echo "\n\n" . "// Show the descriptor of a datasource" . "\n" . str_pad('', 128, '_') . "\n\n";
try {
    $datasource = $jasper->getResourceDescriptor('/datasources/mongo_local_test');
    print_r($datasource->getXml(true));
} catch (Exception $e) {
    echo $e;
}
*/

