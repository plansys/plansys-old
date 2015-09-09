<?php

class Report extends CComponent {

    public $filePath;

    public function getModule() {
        return Yii::app()->controller->module;
    }

    public function load($file, $_data = null) {
        $this->filePath = $file;
        if (is_array($_data))
            extract($_data, EXTR_PREFIX_SAME, 'data');
        else
            $data           = $_data;

        ob_start();
        ob_implicit_flush(false);
        require($file);
        return ob_get_clean();
    }

    public function getLocation($alias) {
        return Helper::explodeFirst('.', $alias);
    }

    public static function createPdf($html) {
        $html2pdf = Yii::app()->ePdf->HTML2PDF();
        $html2pdf->WriteHTML($html);
        $html2pdf->Output();
    }

    public function staticUrl($path = '') {
        $plansysPath = Yii::getPathOfAlias('application');
        $appPath     = Yii::getPathOfAlias('app');
        $baseUrl     = Yii::app()->baseUrl;

        if (strpos($this->filePath, $appPath) === 0) {
            return Yii::app()->request->hostInfo . $baseUrl . '/app/static' . $path;
        } else if (strpos($this->filePath, $plansysPath) === 0) {
            return Yii::app()->request->hostInfo . $baseUrl . '/plansys/static' . $path;
        } else {
            return "";
        }
    }

}
