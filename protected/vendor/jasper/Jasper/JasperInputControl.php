<?php
/**
 * Description of JasperInputControl
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperInputControl extends JasperResourceDescriptor {
    public function __construct($uriString = null) {
        parent::__construct($uriString);

        $this->setWsType('inputControl')
             ->setPropResourceType('com.jaspersoft.jasperserver.api.metadata.common.domain.InputControl')
             ->setPropIsReference('false');
    }
}


