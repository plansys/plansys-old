<?php

class ArrayGroup {
    public  $grouped;
    private $data;
    private $groups;
    private $cols;
    private $groupCount = 0;
    private $flatted;
    private $rIdx = 1;

    public function __construct($data, $groups, $cols) {
        $this->data   = $data;
        $this->groups = $groups;
        $this->cols   = $cols;
    }

    public function flatten() {
        $cursor           = &$this->grouped;
        $this->groupCount = count($this->groups);
        $this->flatted    = [];
        $this->recurseGroup($cursor);
        return $this->flatted;
    }


    private function recurseGroup($cursor, $lvl = 0) {
        if (isset($cursor['$items'])) {
            foreach ($cursor['$items'] as $k => $r) {
                if (isset($cursor['$items'][$k]['$items'])) {
                    $this->flatted[] = [
                        $this->groups[$lvl + 1]['col'] => $k,
                        '$level' => $lvl,
                        '$type' => 'g',
                        '$group' => $this->groups[$lvl + 1]['col']
                    ];
                    $this->recurseGroup($cursor['$items'][$k], $lvl + 1);
                } else {
                    $this->flatted[] = array_merge($r, [
                        '$type' => 'r',
                        '$index' => $this->rIdx++,
                        '$level' => $lvl,
                    ]);
                }
            }

            if (isset($cursor['$aggregate'])) {
                $lvl             = max($lvl - 1, 0);
                $this->flatted[] = array_merge($cursor['$aggregate'], [
                    '$type' => 'a',
                    '$level' => $lvl,
                    '$aggr' => count($this->cols) > 0
                ]);
            }
        }
    }

    public function group() {
        $this->groupCount = count($this->grouped);
        $this->grouped    = [];
        foreach ($this->data as $r => $d) {
            $this->executeGroup($r, $d);
        }

        return $this->grouped;
    }

    private function executeGroup($rowIndex, $data) {
        $cursor = &$this->grouped; //drill deep for each group
        foreach ($this->groups as $lvl => $group) {
            if (!isset($cursor['$items'])) {
                $cursor = [
                    '$items' => [],
                    '$aggregate' => []
                ];
            }
            if ($group['col'] == '-all-' || $group['col'] == '' || !isset($data[$group['col']])) continue;

            if (!isset($cursor['$items'][$data[$group['col']]]['$items'])) {
                $cursor['$items'][$data[$group['col']]] = [
                    '$items' => [],
                    '$lvl' => $lvl,
                    '$gcol' => $group['col'],
                    '$aggregate' => [],
                    '$parent' => &$cursor
                ];
            }
            $cursor = &$cursor['$items'][$data[$group['col']]];
        }

        $cursor['$items'][] = $data;
        $this->aggregateRow($cursor, $data);
    }

    private function aggregateRow(&$cursor, $data) {
        $loop = true;
        while ($loop) {
            foreach ($this->cols as $colName => $agg) {
                $this->aggregateCell($colName, $agg, $cursor, $data);
            }

            if (!isset($cursor['$parent'])) {
                $loop = false;
            } else {
                $cursor = &$cursor['$parent'];
            }
        }
    }


    public function aggregateCell($colName, $agg, &$cursor, &$data) {
        if (!isset($cursor['$aggregate'][$colName])) {
            $cursor['$aggregate'][$colName] = 0;
        }

        switch ($agg['type']) {
            case 'custom':
                $params = [
                    'text' => function ($col = '') use ($colName, $agg, &$cursor, &$data) {
                        $agg['type'] = 'text';
                        if ($col != '') $agg['col'] = $col;
                        return $this->aggregateCell('$custom_' . $colName, $agg, $cursor, $data);
                    },
                    'sum' => function ($col = '') use ($colName, $agg, &$cursor, &$data) {
                        $agg['type'] = 'sum';
                        if ($col != '') $agg['col'] = $col;
                        return $this->aggregateCell('$custom_' . $colName, $agg, $cursor, $data);
                    },
                    'avg' => function ($col = '') use ($colName, $agg, &$cursor, &$data) {
                        $agg['type'] = 'avg';
                        if ($col != '') $agg['col'] = $col;
                        return $this->aggregateCell('$custom_' . $colName, $agg, $cursor, $data);
                    },
                    'count' => function ($col = '') use ($colName, $agg, &$cursor, &$data) {
                        $agg['type'] = 'count';
                        if ($col != '') $agg['col'] = $col;
                        return $this->aggregateCell('$custom_' . $colName, $agg, $cursor, $data);
                    },
                    'max' => function ($col = '') use ($colName, $agg, &$cursor, &$data) {
                        $agg['type'] = 'max';
                        if ($col != '') $agg['col'] = $col;
                        return $this->aggregateCell('$custom_' . $colName, $agg, $cursor, $data);
                    },
                    'min' => function ($col = '') use ($colName, $agg, &$cursor, &$data) {
                        $agg['type'] = 'min';
                        if ($col != '') $agg['col'] = $col;
                        return $this->aggregateCell('$custom_' . $colName, $agg, $cursor, $data);
                    },
                ];
                extract($params);
                if (trim(@$agg['custom']) != '') {
                    $code = '$cursor[\'$aggregate\'][$colName] = ' . $agg['custom'] . ';';
                    eval($code);
                } else {
                    $cursor['$aggregate'][$colName] = '';
                }
                break;
            case 'text': 
                if (isset($cursor['$gcol'])) {
                    $cursor['$aggregate'][$colName] = $data[$cursor['$gcol']];
                } else {
                    $cursor['$aggregate'][$colName] = "All";
                }
                break;
            case 'count':
                $cursor['$aggregate'][$colName] += 1;
                break;
            case 'max':
                $cursor['$aggregate'][$colName] = max($data[$agg['col']], $cursor['$aggregate'][$colName]);
                break;
            case 'min':
                $cursor['$aggregate'][$colName] = min($data[$agg['col']], $cursor['$aggregate'][$colName]);
                break;
            case 'sum':
                if (isset($data[$agg['col']])) {
                    $cursor['$aggregate'][$colName] += $data[$agg['col']];
                }
                break;
            case 'avg':
                if (isset($data[$agg['col']])) {
                    if (!isset($cursor['$aggregate']['$sum_' . $colName])) {
                        $cursor['$aggregate']['$sum_' . $colName] = 0;
                    }

                    if (!isset($cursor['$aggregate']['$count_' . $colName])) {
                        $cursor['$aggregate']['$count_' . $colName] = 0;
                    }

                    $cursor['$aggregate']['$sum_' . $colName] += $data[$agg['col']];
                    $cursor['$aggregate']['$count_' . $colName] += 1;

                    $cursor['$aggregate'][$colName] = $cursor['$aggregate']['$sum_' . $colName] / $cursor['$aggregate']['$count_' . $colName];
                }
                break;
        }

        return $cursor['$aggregate'][$colName];
    }


}