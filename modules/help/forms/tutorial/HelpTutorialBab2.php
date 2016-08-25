<?php

class HelpTutorialBab2 extends Form {
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
					<h3 id="bab2">Bab II. TABEL DATABASE</h3>
				</div>
				<div class=\"sub_daftar_isi\">					
					<table>
						<td style="width:20%">
							<a href="#2.1">2.1. Konsep Tabel Database</a><br>
							<a href="#2.2">2.2. Relasi Tabel Database</a><br>
							<a href="#2.3">2.3. Create Tabel</a><br>
						</td>
					</table>
				</div>
				<div class=\"isi\">
					<hr>
					<h4 id="2.1">2.1 Konsep Tabel Database</h4><a href="#bab2"><i> back to top >></i></a>
					<p>Tabel dapat memiliki satu kolom atau lebih. Dalam Framewok MVC, dari tabel akan dijadikan acuan dalam pembuatan Model. Dasar dalam pengembangan sistem informasi adalah relasi antar tabel dalam database. Apabila dasar yang dijadikan pondasi belum kuat atau masih kurang efektif maka proses pengembangan sistem informasi tidak akan berjalan efektif dan sistem informasi tidak dapat dioperasikan dengan baik. Maka, detail dari arsitektur tabel dalam database harus dipertimbangkan dengan baik apalagi sistem informasi yang dikembangkan akan berisi data-data yang sangat banyak.
					
					<h4 id="2.2">2.2. Relasi Tabel Database</h4><a href="#bab2"><i> back to top >></i></a>
					<p>Untuk tabel dalam database agar dapat berelasi dengan tabel lain harus memiliki minimal dua kolom, dimana kolom pertama berisi ID dan kolom kedua berisi data. Relasi tabel merupakan hubungan antar tabel. Tanpa relasi, tabel-tabel dalam database hanya akan menjadi kumpulan tabel tanpa hubungan. Relasi ini dibentuk sesuai hubungan antar tabel. Hubungan antar tabel yang akan dibentuk dapat berupa relasi sebagia berikut : 
					<ul><li>One to One
						<li>Many to many
						<li>One to Many
						<li>Many to One
						<li>Many to Many
					</ul>
					<h4 id="2.3">2.3. Create Tabel</h4><a href="#bab2"><i> back to top >></i></a>
					<p>Sebelum mengembangkan sistem informasi, struktur dan relasi tabel harus didesain dengan baik agar pengelolaan data dalam database dapat berjalan dengan baik.
				</div>				
				',
            ),
        );
    }
}