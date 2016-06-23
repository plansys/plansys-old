<?php

class HelpTutorialBab7 extends Form {
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
				<div class=\"isi\">
					<h3>Bab VII. CONTROLLER MANAGEMENT</h3>
					<h4>7.1. Konsep Controller</h4>
					<p>Controller bertugas untuk merender Form dari alamat URL yang didefinisikan. Controller berisi beberapa Action yang dimana masing-masing Action berisi perintah untuk merender sebuah Form. Sehingga pada sebuah Controller dapat berisi beberapa Action. Susunan pada url yang dipanggil melalui web browser adalah :
server/nama_si/index.php?r=/nama_module/nama_controller/nama_action
					<p><img src="plansys/modules/help/img/7-1.png">

					<h4>7.2. Create Controller</h4>
					<p>Sama seperti View, Plansys dapat membuatkan Controller secara otomatis melalui CRUD generator. Akan tetapi, tidak menutup kemungkinan perlu ada perubahan ataupun penambahan. 
					<h5>(a). Menggunakan Module</h5>
					<p>Langkah untuk membuat controller adalah sebagai berikut :
					<ol><li>Pilih menu Form Builder  pilih Controller Builder
						<p><img src="plansys/modules/help/img/7-2-a-1.png">
						<li>Buka App  lalu klik kanan pada module (misal sdm)  klik New Controller 
						<p><img src="plansys/modules/help/img/7-2-a-2.png">
						<li>Muncul kotak Generate New Controller, isikan nama controller pada Controller Name  klik Save.
						<p><img src="plansys/modules/help/img/7-2-a-3.png">
						<li>Jika berhasil, akan tampil Controller yang telah dibuat pada daftar controller (sebelah kiri) dan isi script controller tersebut dapat diEdit pada editor (sebelah kanan).
						<li>Tekan CTRL + S untuk menyimpan perubahan script.
					</ol>

					<h5>(b). Tanpa Menggunakan Module</h5>
					<p>Langkah untuk membuat controller adalah sebagai berikut :
					<ol><li>Pilih menu Form Builder  pilih Controller Builder
							<p><img src="plansys/modules/help/img/7-2-b-1.png">
						<li>Lalu klik kanan pada App  klik New Controller 
							<p><img src="plansys/modules/help/img/7-2-b-2.png">
						<li>Muncul kotak Generate New Controller, isikan nama controller pada Controller Name  klik Save.
							<p><img src="plansys/modules/help/img/7-2-b-3.png">
						<li>Jika berhasil, akan tampil Controller yang telah dibuat pada daftar controller (sebelah kiri) dan isi script controller tersebut dapat diEdit pada editor (sebelah kanan).
							<p><img src="plansys/modules/help/img/7-2-b-4.png">
						<li>Tekan CTRL + S untuk menyimpan perubahan script.
					</ol>
					
					<h4>7.3. Delete Controller</h4>
					<h5>(a). Menggunakan Module</h5>
					<p>Langkah untuk menghapus (Delete) Controller yang tidak digunakan, caranya adalah sebagai berikut :
					<ol><li>Buka Controller List  buka App  pilih module.
						<li>Klik kanan pada controller yang akan dihapus.
							<p><img src="plansys/modules/help/img/7-3-a-2.png">
						<li>Pilih Delete.
						<li>Akan muncul kotak konfirmasi penghapusan, ketikkan DELETE.
							<p><img src="plansys/modules/help/img/7-3-a-4.png">
						<li>Lalu klik OK untuk menghapus Controller tersebut.

					<h5>(b). Tanpa Menggunakan Module</h5>
					<p>Langkah untuk menghapus (Delete) Controller yang tidak digunakan, caranya adalah sebagai berikut :
					<ol><li>Buka Controller List  buka App.
						<li>Klik kanan pada controller yang akan dihapus.
							<p><img src="plansys/modules/help/img/7-3-b-2.png">
						<li>Pilih Delete.
						<li>Akan muncul kotak konfirmasi penghapusan, ketikkan DELETE.
							<p><img src="plansys/modules/help/img/7-3-b-4.png">
						<li>Lalu klik OK untuk menghapus Controller tersebut.
					</ol>
					
				</div>
				
				',
            ),
        );
    }

}