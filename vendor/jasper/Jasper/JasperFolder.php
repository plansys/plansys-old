<?php
/**
 * Description of JasperFolder
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperFolder extends JasperResourceDescriptor {
    public function __construct($uriString = null) {
        parent::__construct($uriString);
        
        $this->setWsType('folder')
             ->setPropResourceType('com.jaspersoft.jasperserver.api.metadata.common.domain.Folder')
             ->setPropHasData('false');
    }
}

