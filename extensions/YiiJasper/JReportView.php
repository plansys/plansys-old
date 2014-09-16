<?php
/**
 * JReportView class 
 *
 * @author César Quintero <cquinteroj@gmail.com>
 * @version 1.0
 *
 * El codigo minimo usado por JReportView es el siguiente:
 *
 * $this->widget('JReportView', array(
 * 		'pathReport'=>'/reports/samples/AllAccounts',
 * 		'params' => array(
 *    		'name' => 'value',
 *    	),
 * ));
 *
 */

Yii::import('zii.widgets.CBaseListView');

/**
 * JReportView muestra un reporte de JaserServer en HTML como una lista
 *
 */
class JReportView extends CBaseListView {

	/**
	 * @var string Path del reporte que se va utilizar.
	 */
	public $pathReport;

	/**
	 * @var JasperReport instancia
	 */
	public $jasperReport;

	/**
	 * @var string Html que divide cada pagina del reporte en caso de haber dos o mas en la vista
	 */
	public $separator;
	
	/**
	 * @var array parametros adicionales se envia en un array asociativo nombre => valor
	 */
	public $params = array();	

	/**
	 * @var numeric el numero de paginas del reporte que se deben mostrar por vista
	 */
	public $pageSize = 1;


	/**
	 * Inicializa JReportView
	 * Este metodo inicializa los valores requeridos por las propiedades y crea una instancia JasperReport
	 */
	public function init(){
		//No se puede iniciar el widget si no fue declarado el path del reporte
		if($this->pathReport===null)
			throw new CException('La ruta del reporte(pathReport) no puede estar vacia');
		
		//Crea una nueva instancia de JasperReport
		$this->jasperReport = new JasperReport($this->pathReport, JasperReport::FORMAT_HTML, $this->params);

		//Estas variables las recoje de la URL, son utilidas para no generar el mismo reporte cuando se trata de paginacion
		$report = Yii::app()->request->getQuery('report', false);
		$pages  = Yii::app()->request->getQuery('pages', null);
		
		//si fue declarado el id del reporte lo asigna al objecto jasperReport
		if($report){
			$this->jasperReport->id = $report;
			$this->jasperReport->pages = $pages;
		}else
			$this->jasperReport->exec(); //de lo contrario ejecuta el reporte
		
		//El dataprovider se utiliza para generar el numero de paginas del reporte
		//Y para el paso de las variables del reporte id y numero de paginas
		$this->dataProvider = new CArrayDataProvider(range(1, $this->jasperReport->pages), array(
            'pagination'=>array(
                'pageSize'=> $this->pageSize,
                'params'=> array(
                	'report' => $this->jasperReport->id,
                	'pages' => $this->jasperReport->pages,
                ),
            ),
        ));

		parent::init();

	}

	/**
	 * Obtiene las paginas de Jasper en HTML
	 */
	public function renderItems(){
		
		$data=$this->dataProvider->getData();

	    if(($n=count($data))>0){
	    	$j=0;
		    foreach($data as $i=>$item){
		    	echo $this->jasperReport->toHTML($item);
		    	if($j++ < $n-1)
	                echo $this->separator;
	        }
  	    }else
    	    $this->renderEmptyText();

	}

}