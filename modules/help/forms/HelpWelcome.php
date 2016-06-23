<?php

class HelpWelcome extends Form {

    public function getForm() {
        return array (
            'title' => 'Plansys - Welcome',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '
				<center>
				<h1>Selamat Datang di Plansys</h1>
				
				<hr>
				<br><br><br>
				Saya ingin <a ng-url="/help/tutorial/bab1">Tutorial Plansys</a>.
				</center>
				',
            ),
        );
    }

}