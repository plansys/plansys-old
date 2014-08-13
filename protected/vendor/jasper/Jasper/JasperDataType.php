<?php
/**
 * Description of JasperDataType
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperDataType extends JasperResourceDescriptor {
    public function __construct($uriString = null) {
        parent::__construct($uriString);

        $this->setWsType('dataType')
             ->setPropResourceType('com.jaspersoft.jasperserver.api.metadata.common.domain.DataType')
             ->setPropIsReference('false');
    }
}


