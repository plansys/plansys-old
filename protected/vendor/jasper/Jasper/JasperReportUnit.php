<?php
/**
 * Description of JasperReportUnit
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperReportUnit extends JasperResourceDescriptor {
    public function __construct($uriString = null) {
        parent::__construct($uriString);

        $this->setWsType('reportUnit')
             ->setPropResourceType('com.jaspersoft.jasperserver.api.metadata.jasperreports.domain.ReportUnit')
             ->setPropRuAlwaysPropmtControls('false')
             ->setPropRuControlsLayout('1')
             ->setPropHasData('false');
    }
}

