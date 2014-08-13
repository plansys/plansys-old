<?php
/**
 * Description of JasperJrxml
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperJrxml extends JasperResourceDescriptor {
    public function __construct($uriString = null) {
        parent::__construct($uriString);

        $this->setWsType('jrxml')
             ->setPropResourceType('com.jaspersoft.jasperserver.api.metadata.common.domain.FileResource')
             ->setPropRuIsMainReport('true');
    }
}


