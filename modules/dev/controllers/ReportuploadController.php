<?php
require_once(Yii::app()->basePath . '/vendor/jasper/Jasper/Jasper.php');

class ReportuploadController extends Controller {
    
    public function actionIndex() {
        $menus = Array (
            '0' => Array
            (
                'module' => 'Dev',
                'items' => Array
                    (
                        '0' => Array (
                            'name' => 'List Report',
                            'module' => 'Dev',
                            'class' => 'list',
                            'class_path' => ''
                        ),
                        '1' => Array(
                            'name' => 'Upload Report',
                            'module' => 'Dev',
                            'class' => 'upload',
                            'class_path' => ''
                        )
                    )

            )
        );
        
        $this->render('index', array(
            'reportupload' => $menus
        ));
    }
    
    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }
    
    public function actionUpdate($path) {
        $this->layout = "//layouts/blank";
        
        if ($path == 'list') {
            $this->render('list', array(
                'server' => 'http://localhost:8080/jasperserver/flow.html?_flowId=searchFlow&decorate=no&j_username=jasperadmin&j_password=jasperadmin'
            ));
        } elseif ($path == 'upload') {
            try {
                $directory = '/reports/';
                $jasper        = new \Jasper\Jasper('localhost:8080','jasperadmin','jasperadmin');
            } catch (Exception $e) {
                echo $e;
                exit();
            }

            try {
                $report = new \Jasper\JasperJrxml($directory . '/masterlubang.jrxml');
                $report->setIsNew('false')
                       ->setPropVersion((string)time());
                $jasper->createContent($report, file_get_contents(Yii::app()->basePath . '/jrxml/masterlubang.jrxml'));
            } catch (Exception $e) {
                echo $e;
            }

            try {
                // Instance of the Report
                $report = new \Jasper\JasperReportUnit($directory . '/RMLubang');
                $report->setLabel('Master Lubang');
                $report->setIsNew('false')
                       ->setPropVersion((string)time());
                // jrxml Template
                $jrxml = $jasper->getResourceDescriptor($directory . '/masterlubang.jrxml');
                $jrxml->setPropRuIsMainReport('true')
                      ->setIsNew('false')
                      ->setPropVersion((string)time());

                // Datasource
                $mysql = new \Jasper\JasperDatasource();
                $mysql->setPropIsReference('true');
                $mysql->setPropReferenceUri('/datasources/JServerMySQL');

                // Put everything together and deploy the report
                $report->addChildResource($mysql);
                $report->addChildResource($jrxml);

                print_r($report->getXml(true));
                $jasper->createResource($report);
            } catch (Exception $e) {
                //echo $e;
                $this->render('errorcon', array());
            }
            
        
            $this->render('upload', array());
        }
        
    }
    
    public function actionError() {
        $this->layout = "//layouts/blank";
        $this->render('error', array());
    }
}