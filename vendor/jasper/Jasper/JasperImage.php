<?php
/**
 * Description of JasperImage
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperImage extends JasperResourceDescriptor {
    public function __construct($uriString = null) {
        parent::__construct($uriString);

        $this->setWsType('img')
             ->setPropResourceType('com.jaspersoft.jasperserver.api.metadata.common.domain.FileResource');
    }
}


