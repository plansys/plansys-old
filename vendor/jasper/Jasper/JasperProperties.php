<?php
/**
 * Description of JasperProperties
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperProperties extends JasperResourceDescriptor {
    public function __construct($uriString = null) {
        parent::__construct($uriString);

        $this->setWsType('prop')
             ->setPropResourceType('com.jaspersoft.jasperserver.api.metadata.common.domain.FileResource')
             ->setPropHasData('true');
    }
}


