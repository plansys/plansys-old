<?php

class HelpTutorialBab3 extends Form {
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
					<hr>
					<p align="right"><a href="https://drive.google.com/folderview?id=0B1jwrrSeSILiNDh3eWc5RkYtcHM&usp=sharing" target="_blank"> <strong><i> >>> Download PDF</strong> </i></a>
				</div>
				<div class=\"judul_bab\">					
					<h3 id="bab3">Bab III. MODULE MANAGEMENT</h3>
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td><a href="#3.1">3.1. Konsep Module</a><br>
							<a href="#3.2">3.2. Create Module</a><br>
							<a href="#3.3">3.3. Update Module</a><br>
							<a href="#3.4">3.4. Delete Module</a><br>
						</td>					
					</table>
				</div>
				<div class=\"isi\">
					<hr>
					<h4 id="3.1">3.1. Konsep Module Management</h4><a href="#bab3"><i> back to top >></i></a>   
					<p>Modul merupakan pengelompokkan satu paket model, view, dan controller (MVC). Dalam pengembangan aplikasi sistem informasi menggunakan tool Plansys, dapat menerapkan penggunaan module maupun tidak. Dengan menggunakan Module, pengembangan aplikasi sistem informasi selanjutnya akan lebih mudah karena struktur sistem sudah terstruktur. Dalam sistem informasi, module dapat mewakili sebuah bagian dalam organisasi perusahaan. Misalnya dalam perusahaan terdapat bagian SDM, Keuangan, Pemasaran, dan lain sebagainya. Penggunaan module dalam aplikasi sistem informasi berdasarkan masing-masing bagian tersebut dapat membangun sistem aplikasi yang terstruktur, yaitu terdapat module SDM, module Keuangan, dan modul lainnya. Maka dari itu, kami sangat merekomendasikan untuk menggunakan Module dalam pengembangan aplikasi system informasi.
	
					<h4 id="3.2">3.2. Create Module</h4><a href="#bab3"><i> back to top >></i></a>   
					<p>Langkah-langkah untuk membuat module adalah sebagai berikut :
					<ol>
						<li>Buka menu Builder >> pilih Module Builder
						<li>Klik kana pada App >> pilih New App Module
						<li>Muncul pop up, masukkan nama module (misal: sdm) >> klik OK.
						<li>Maka pada daftar app di Module akan ditambahkan sdm
						<li>Buka modul sdm, terdapat 2 tab pengaturan (Module Info dan Access Control). Pada tab Module Info, pada bagian kiri (Import Initialization) terdapat tombol Generate Import untuk mereplace isi script module tersebut (hal ini perlu dilakukan jika telah dilakukan editing dan ingin dikembalikan ke script defaultnya).
						<p><img src="plansys/modules/help/img/3-2-5.png">
						<li>Pada tab yang sama di bagian kanan terdapat informasi letak Class Path dan  Module Directory.
						<li>Untuk pembahasan access permissions terhadap suatu module, silahkan lihat di BAB XI. Access Permissions.
					</ol>

					<h4 id="3.3">3.3. Update Module</h4><a href="#bab3"><i> back to top >></i></a>   
					<p>Langkah-langkah untuk memperbarui (update) module adalah sebagai berikut :
					<ol>
						<li>Buka menu Builder >> pilih Module Builder >> klik App >> klik kanan pada module yang akan diEdit >> pilih Open New Tab.
						<li>Maka akan menujua halaman Module Info dan Access Control seperti berikut :
						<p><img src="plansys/modules/help/img/3-3-2.png">
						<li>Edit sesuai kebutuhan.
						<li>Pada tab Module Info, jika diklik Generate Import maka akan mereplace script module dengan script default.
						<li>Pada tab Access Control berisi pengaturan hak akses, baik pengaturan Role Access maupun User Access.
					</ol>
					
					<h4 id="3.4">3.4. Delete Module</h4><a href="#bab3"><i> back to top >></i></a>   
					<p>Langkah-langkah untuk menghapus module adalah sebagai berikut :
					<ol>
						<li>Buka menu Builder >> pilih Module Builder >> klik App.
						<li>Lalu klik kanan pada module yang akan dihapus >> pilih Delete Module.
						<li>Akan muncul peringatan delete, klik OK untuk melanjutkan.
						<li>Pada kotak pop up selanjutnya ketikkan DELETE >> Klik OK untuk menghapus modul tersebut.
						<p><img src="plansys/modules/help/img/3-4-4.png">
					</ol>
				</div>				
				',
            ),
        );
    }
}