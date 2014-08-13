Jasper.php
======
http://blog.flowl.info/2013/jasper-php-library-on-github

PHP library providing smooth access to JasperReports server via REST / REST v2 API



I did not like the original JasperSoft PHP library,
it was not well written, not fully object orientated and not well enough documentated.

My classes are fluent, namespaced, well documentated (PHPDoc comments coming next release)
and make use of Exceptions.

The first release will be this month.

Functionality:
- fluent classes, proper OOP, Zend code style
- get server info
- create folders, reportUnits and other resources
- delete resources
- fetch resourceDescriptor or contents of resources
- export reports
- next release: scheduled report jobs
- next release: asynchronous report polling


Example:

start by an instance of the Jasper object and list the resource inside of a directory:

    <?php
    
    require_once('Jasper/Jasper.php');
    
    $jasper = new Jasper\Jasper('localhost:8080', 'jasperadmin', 'jasperadmin');
    
    $resources = $jasper->getFolder('/reports/');
    
    foreach ($resources as $res) {
        echo "<p>" . $res->getLabel() . " " . $res->getUriString() . " " . $res->getWsType() . " " . $res->getDescription() . "</p>";
    }
    
    ?>



Please keep in mind this project just got started and is still kinda beta.

Meanwhile, you might visit my projects websites:

http://blog.flowl.info

http://www.flowl.info

http://www.woisteinebank.de

