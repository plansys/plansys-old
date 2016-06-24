<?php

class HelpTutorialBab12 extends Form {
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
					<h3 id="bab12">Bab XII. REPORT MANAGEMENT</h3>
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#12.1">12.1. Create Report Word File (docx)</a><br>							
						</td>					
					</table>
				</div>				
				<div class=\"isi\">
					<hr>					
					<h4 id="12.1">12.1. Create Report Word File (docx)</h4><a href="#bab12"><i> back to top >></i></a>
					<p>Langkah-langkah untuk membuat report (laporan ) berupa word file (.docx) adalah sebagai berikut :
					<ol><li>Buat template laporan
 							<p><img src="plansys/modules/help/img/12-1-1.png"> 
							<p>Isikan script [model.nama_kolom_tabel_database;block=tbs:row] di masing-masing kolom.
						<li>Buat folder reports untuk menyimpan file template laporan pada direktori :
					webserver >> nama_aplikasi >> app >> modules >> nama_module. Sehinggan file template laporan akan berada di folder reports tersebut :
					 		<p><img src="plansys/modules/help/img/12-1-2.png"> 
						<li>Buat Controller melalui Controller Builder (misal:  LaporanController.php)
						<li>Edit script Controller tersebut menjadi seperti di bawah ini :
					 		<p><img src="plansys/modules/help/img/12-1-4.png"> 
						<li>Tambahkan link untuk memanggil controller tersebut. Sebagai contoh, link tersebut akan dipanggil melalui menu maka perlu mengedit menu melalui Menu Tree Editor. 
					 		<p><img src="plansys/modules/help/img/12-1-5.png"> 
						<li>Maka ketika link tersebut dibuka melalui menu tersebut akan mendownload file tersebut :
					 		<p><img src="plansys/modules/help/img/12-1-6.png"> 
						<li>Pada tabel barang_masuk dalam database berisi data sebagai berikut :
					 		<p><img src="plansys/modules/help/img/12-1-7.png"> 
						<li>Ketika sudah didownload, isi file dokumen word (docx) akan seperti berikut :
					 		<p><img src="plansys/modules/help/img/12-1-8.png"> 
					</ol>
				</div>
				',
            ),
        );
    }

}