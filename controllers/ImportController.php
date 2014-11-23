<?php

class ImportController extends Controller
{
    public function actionExcel($file, $sheets = 0)
    {
        $this->read($file);
        $se = $this->sheets[$sheets]['cells'];
        $arr = array_shift($se);
        $temp = array();
        $result = array();

        foreach ($se as $row) {
            foreach ($row as $k => $val) {
                $temp[$arr[$k]] = $val;
            }
            $result[] = $temp;
        }
        return $result;
    }
}