<?php
/**
 * Description of JasperDataSource
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperDatasource extends JasperResourceDescriptor {
    public function __construct($uriString = null) {
        parent::__construct($uriString);

        $this->setWsType('custom')
             ->setPropResourceType('com.jaspersoft.jasperserver.api.metadata.jasperreports.domain.CustomReportDataSource');
    }
}


