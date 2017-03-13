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
				<style>
				.home_screen{
					padding: 10px;	
					max-width:100%;	
					max-height:100%;
					background-color: white;
					box-shadow: 3px 3px 3px #888888;						
					margin-top:100px;	
					margin-bottom:100px;						
					margin-right:300px;	
					margin-left:300px;	
					font-size:12px;		
				}
				</style>
				<div class=\"home_screen\">
				<center>							
				<hr>
				<br><br><br>
				<p><img src=\"plansys/static/img/logo.png\" style=\"width:250px\">
				<p><strong>Fastest PHP Web App Builder</strong>
				<hr>
				<br>
				<h4>Selamat Datang di Plansys</h4>			
				<p><a ng-url=\"/help/tutorial/bab1\">Saya baru menggunakan Plansys, Pelajari Tutorial Plansys</a>.				
				<br><br><br>				
				<hr>
				Plansys Beta - Under Licensed GPL v.3.
				<p><a href=\"http://www.plansys.co\" target=\"_blank\">plansys.co</a>
				<hr>
				</center>
				</div>
				',
            ),
        );
    }

}