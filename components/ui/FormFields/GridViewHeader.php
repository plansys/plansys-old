<?php

class GridViewHeader extends Form {

    public function getFields() {
        return array (
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<div style=\'padding:10px 15px;margin:0px -15px;border-bottom:1px solid #ddd\'>
    <div class=\"btn-group\" style=\"float:right;\" dropdown>
      <button id=\"split-button\" type=\"button\" dropdown-toggle
              class=\"btn btn-xs btn-default\">
          Row Header: {{rowHeaders}} Row
        <span class=\"caret\"></span>
      </button>
      <ul class=\"dropdown-menu\" role=\"menu\" aria-labelledby=\"split-button\">
        <li role=\"menuitem\"><a href=\"#\" ng-click=\"setRowHeaders(1)\">1 Row</a></li>
        <li role=\"menuitem\"><a href=\"#\" ng-click=\"setRowHeaders(2)\">2 Row</a></li>
        <li role=\"menuitem\"><a href=\"#\" ng-click=\"setRowHeaders(3)\">3 Row</a></li>
        <li role=\"menuitem\"><a href=\"#\" ng-click=\"setRowHeaders(4)\">4 Row</a></li>
      </ul>
    </div>
    <h3 style=\"margin:0px\">GridView Header</h3>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<table 
    style=\"width:auto;margin-top:20px;\"
    class=\"table table-condensed\">
    <tr ng-repeat=\"r in rowHeadersArray\">
        <td class=\"th-head\">
            Row Header {{r}}:
        </td>
        <td class=\'th\' tooltip=\"{{c.label}}\"
            ng-class=\"{active: isActive(r, $index)}\"
            ng-click=\"select(r, $index, $event)\"
            ng-repeat=\"c in active.columns\">
            {{ $index }}
        </td>
    </tr>
    
    <tr onclick=\"return false;\">
        <td style=\"border:0px;\"></td>
        <td 
            ng-repeat=\"c in active.columns\"
            style=\"width:30px;color:#999;font-size:11px;text-align:center;
            user-select:none;-webkit-user-select:none;-moz-user-select:none;\">
            {{ getWidth(c) }}
        </td>
    </tr>
</table>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\"
    position:fixed;
    bottom:0px;
    right:0px;
    left:0px;
    height:150px;
    border-top:1px solid #ccc;
    padding:10px;
\">
   ',
            ),
            array (
                'totalColumns' => '3',
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Col Span',
                        'name' => 'colspan',
                        'fieldType' => 'number',
                        'labelWidth' => '5',
                        'fieldWidth' => '4',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Width',
                        'name' => 'width',
                        'fieldType' => 'number',
                        'labelWidth' => '5',
                        'fieldWidth' => '4',
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Header',
                        'name' => 'header',
                        'fieldOptions' => array (
                            'auto-grow' => 'true',
                        ),
                        'type' => 'TextArea',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '33%',
                'w2' => '33%',
                'w3' => '33%',
                'options' => array (
                    'ng-if' => '!!activeCol && !!activeRow',
                ),
                'perColumnOptions' => array (
                    'style' => 'padding:0px',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"text-center\">
    &nbsp; Please choose column header above &nbsp;
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

    ### columnType

    public function getForm() {
        return array (
            'formTitle' => 'Grid View Header',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'title' => 'GridView Header Settings',
            'inlineJS' => 'GridView/headerPopUp.js',
        );
    }

}