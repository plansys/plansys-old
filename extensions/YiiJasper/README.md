YiiJasper
=========

Yii libraries to use Jasper Server Reports 

This extension provides access to show Jasper Reports in Yii from Jasper Server.

** Note: This IS NOT a .jrxml parser version, you must install Jasper Server **

Requirements:

Yii Version 1.1.14
Installed and working Jasper Server (Tested in Community v5.2.0)

Installation:

Copy YiiJasper folder to yii extensions folder.

You can import the extension where you use this or in the config main adding

	'import'=>array(
		//...
		'ext.YiiJasper.*',
	),


Then you need configure some parameters in JasperReport.php to communicate with your Jasper Server: 

//Jasper server URL (Default Value)
protected $baseUrl = 'http://localhost:8080/jasperserver/';

//Jasper server username (Default Value)
protected $jusername = 'jasperadmin';

//Jasper server password (Default Value)
protected $jpassword = 'jasperadmin';

Examples for basic usage:

	/* Html Report */
	$pages = '1-4';
	$re = new JasperReport('/reports/samples/AllAccounts');
  	$re->exec();
  	echo $re->toHTML($pages); //Page 1 to 4

	/* Pdf Report */
	$re = new JasperReport('/reports/samples/AllAccounts');
  	$re->exec();
  	echo $re->toPDF(); //All pages

	/* Xls Report */
	$re = new JasperReport('/reports/samples/AllAccounts');
   	$re->exec();
   	echo $re->toXLS(8); //Page 8

   	/* Widget Report, Html report with pagination */
   	$this->widget('JReportView', array(
		'pathReport'=>'/reports/samples/AllAccounts',
	));