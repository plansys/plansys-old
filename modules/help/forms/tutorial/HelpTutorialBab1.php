<?php

class HelpTutorialBab1 extends Form {
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
					<h3 id="bab1">Bab I. INTRODUCTION</h3>				
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#1.1">1.1. Framework MVC</a><br>
							<a href="#1.2">1.2. Plansys Beta</a><br>
							<a href="#1.3">1.3. Plansys Installation</a><br>
						</td>					
					</table>
				</div>				
				<div class=\"isi\">
					<hr>
					<h4 id="1.1">1.1. Framework MVC</h4><a href="#bab1"><i> back to top >></i></a>
					<p>Framework MVC (Model, View, Controller) merupakan kerangka kerja dalam mengembangkan sebuah sistem informasi yang menggunakan bahasa pemrograman PHP. MVC adalah singkatan dari Model, View, Controller. Tanpa menggunakan framework MVC, proses pengembangan sistem informasi akan membutuhkan waktu lebih lama. Dengan menggunakan kerangka kerja, proses pengembangan sistem informasi akan lebih mudah, efektif, dan efisien. Kerangka kerja ini sangat membantu apalagi jika sistem informasi yang akan dikembangkan bersifat kompleks yang akan membutuhkan resource waktu yang lama. Ada beberapa framework PHP yang menerapkan pola MVC antara lain Yii, Laravel, Code Igniter, Symphony, dan lainnya.
	
					<h4 id="1.2">1.2. Plansys Beta</h4><a href="#bab1"><i> back to top >></i></a>
					<p>Plansys merupakan tool untuk mengembangkan sistem informasi. Plansys ini dibangun menggunakan framework Yii versi 1. Kunci utama Plansys terletak pada Relational Database artinya modal awal dalam mengembangkan sistem informasi dengan menggunakan Plansys adalah relasi antar table dalam database sistem informasi tersebut. Sampai saat ini pengembangan tool ini masih dalam tahap Plansys Beta.

					<h4 id="1.3">1.3. Plansys Installation</h4><a href="#bab1"><i> back to top >></i></a>
					<p>Langkah untuk menginstall plansys cukup mudah. Sebagai contoh dalam Plansys Guide ini diinstall pada Personal Computer dan menggunakan localhost sebagai server. 
					Sistem yang digunakan dalam panduan ini adalah sebagai berikut :
						<ul> 
							<li>Operating Sistem		: Windows 7 Ultimate 32 bit
							<li>Paket Webserver & Database	: XAMPP - 7.0.5-0-VC14 -  32 bit
						</ul>
					Berikut langkah-langkah instalasi Plansys :
					<ol>
						<li>Download tool Plansys di site plansys.co atau langsung menuju halaman di github (https://github.com/plansys/plansys)
						<li>Ekstrak dan pindahkan isi folder plansys ke dalam folder plansy dalam site pada server. Sebagai contoh site yang digunakan adalah coba. Maka folder plansys akan berada di direktori C:/xampp/htdocs/coba.
						<p><img src="plansys/modules/help/img/1-3-2.png" max-width="500px" >
						<li>Siapkan database yang akan digunakan.
						<li>Buka plansys pada web browser dengan alamat localhost/nama_si/plansys maka akan halaman pertama. Jika muncul pesan Composer failed to load! Buka CMD (Command Prompt).
						<div class=\"gambar\"><img src="plansys/modules/help/img/1-3-4.png"></div>
						<li>Pastikan Composer PHP sudah terinstall di sistem. Jika composer PHP belum terinstall maka akan muncul pesan error. 
						<p><img src="plansys/modules/help/img/1-3-5.png">
							<p>Solusinya adalah :
							<ul><li>Download composer PHP pada link https://getcomposer.org/download/
								<li>Install Composer PHP tersebut
								<li>Jika sudah terinstall, tutup dan buka lagi Command Prompt. Lalu langkah instalasi Plansys selanjutnya dapat dilakukan.
							</ul>
						<li>Jika Composer PHP sudah terinstall maka langkah selanjutnya dapat dilakukan, pada command prompt masuk ke direktori plansys >> ketikkan composer update >> tekan Enter >> tunggu proses sampai selesai.
						<li>Jika proses Generating autoload files sudah selesai maka proses update composer sudah selesai.
						<div class=\"gambar\"><img src="plansys/modules/help/img/1-3-7.png"></div>
						<li>Kembali ke browser dan buka plansys dengan alamat localhost/nama_si maka akan halaman pertama instalasi akan tampil. Plansys akan melakukan pengecekan sistem yang dibutuhkan. Hasil pengecekan menunjukkan tidak ada masalah ditandai dengan tanda centang (v) semua, jika ada tanda silang (x) berarti ada masalah dan harus diselesaikan agar dapat melanjutkan proses selanjutnya. Jika sudah tidak ada masalah, klik Next Step.
						<p><img src="plansys/modules/help/img/1-3-8.png">
						<li>Pada halaman berikutnya, halaman untuk mengatur informasi database server yang digunakan.  Isian mencakup Driver (MySQL atau Oracle), Host, Username, Password, Database name yang akan digunakan (misal coba) >> Beri tanda centang pada Re-Create Plansys table, pilihan ini akan membuat tabel secara otomatis untuk keperluan log in setelah proses instalasi selesai. Lalu klik Next Step.
						<p><img src="plansys/modules/help/img/1-3-9.png">
						<li>Setelah itu dalam database akan dibuatkan oleh Plansys beberapa tabel sebagai berikut :
						<p><img src="plansys/modules/help/img/1-3-10.png">
						<li>Pada halaman selanjutnya, isikan username dan password untuk akun Developer (misal dev dengan password 12345) >> klik Finish Installation.
						<p><img src="plansys/modules/help/img/1-3-11.png">
						<li>Proses Instalasi Plansys telah selesai, maka untuk log in menggunakan user developer yang telah didaftarkan saat instalasi plansys :
								<ul><li>Username 	: dev
									<li>Password	: 12345
								</ul>
							<p><img src="plansys/modules/help/img/1-3-12.png">
					</ol>
				</div>
				
				',
            ),
        );
    }

}