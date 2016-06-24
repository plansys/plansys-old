<?php

class HelpTutorialBab15 extends Form {
	 public function getForm() {
        return array (
            'title' => 'Plansys - Tutorial',
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
				<h2>Tutorial Plansys</h2><hr>
				</center>				
				<div class=\"daftar_isi\">					
					<p><a ng-url="/help/tutorial/bab1">INTRODUCTION</a> 					
					<p><a ng-url="/help/tutorial/bab2">TABEL DATABASE</a> 	
					<p><a ng-url="/help/tutorial/bab3">MODULE MANAGEMENT</a> 
					<p><a ng-url="/help/tutorial/bab4">MODEL MANAGEMENT</a> 
					<p><a ng-url="/help/tutorial/bab5">CRUD GENERATOR</a> 	
					<p><a ng-url="/help/tutorial/bab6">VIEW MANAGEMENT</a> 
					<p><a ng-url="/help/tutorial/bab7">CONTROLLER MANAGEMENT</a> 
					<p><a ng-url="/help/tutorial/bab8">ROLE MANAGEMENT</a> 	
					<p><a ng-url="/help/tutorial/bab9">USER MANAGEMENT</a> 
					<p><a ng-url="/help/tutorial/bab10">MENU TREE EDITOR</a> 
					<p><a ng-url="/help/tutorial/bab11">ACCESS PERMISSIONS</a> 	
					<p><a ng-url="/help/tutorial/bab12">REPORT MANAGEMENT</a> 
					<p><a ng-url="/help/tutorial/bab13">EMAIL BUILDER</a> 
					<p><a ng-url="/help/tutorial/bab14">SERVICE MANAGEMENT</a> 	
					<p><a ng-url="/help/tutorial/bab15">DATABASE MANAGEMENT</a> 
					<p><a ng-url="/help/tutorial/bab16">SETTING MANAGEMENT</a> 	
					<p><a ng-url="/help/tutorial/bab17">REPOSITORY</a>											
				</div>
				<div class=\"judul_bab\">					
					<h3 id="bab15">Bab XV. DATABASE MANAGEMENT</h3>
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#15.1">15.1. Backup</a><br>
							<a href="#15.2">15.2. Import</a><br>							
						</td>					
					</table>
				</div>				
				<div class=\"isi\">
					<hr>					
					<h4 id="15.1">15.1. Backup</h4><a href="#bab15"><i> back to top >></i></a>
					<p>Langkah-langkah untuk melakukan proses backup database adalah sebagai berikut :
					<ol><li>Buka server/phpmyadmin/adminer.php kemudian login dengan mengisi username, password, dan database  tekan tombol Login.
					 		<p><img src="plansys/modules/help/img/15-1-1.png"> 
						<li>Lalu pilih Export.
					 		<p><img src="plansys/modules/help/img/15-1-2.png"> 
						<li>Lalu pilih opsi-opsi yang mencakup : Output, Format, Database, Tables, Data. Dan juga beri centang pada tabel yang akan dibackup pada Tables dan isi datanya pada Data.
					  		<p><img src="plansys/modules/help/img/15-1-3-i.png"> 
					  		<p><img src="plansys/modules/help/img/15-1-3-ii.png"> 
						<li>Klik tombol Export.
					</ol>			

					<h4 id="15.2">15.2. Import</h4><a href="#bab15"><i> back to top >></i></a>
					<p>Langkah-langkah untuk melakukan proses import database adalah sebagai berikut :
					<ol><li>Buka server/phpmyadmin/adminer.php kemudian login dengan mengisi username, dan password  tekan Login
							<p><img src="plansys/modules/help/img/15-2-1.png"> 
						<li>Lalu pilih Import.
					 		<p><img src="plansys/modules/help/img/15-2-2.png"> 
						<li>Tekan tombol Browse untuk memilih file database yang akan diimport. Beri centang pada Stop on error dan Show olny errors agar saat proses import gagal maka proses import akan terhenti da nada informasi error yang terjadi.
					 		<p><img src="plansys/modules/help/img/15-2-3.png"> 
						<li>Tekan Execute untuk menjalankan proses import database.
					</ol>
				</div>				
				',
            ),
        );
    }
}