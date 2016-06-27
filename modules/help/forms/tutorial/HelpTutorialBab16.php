<?php

class HelpTutorialBab16 extends Form {
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
					<h3 id="bab16">Bab XVI. SETTING MANAGEMENT</h3>
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#16.1">16.1. Setting Management</a><br>
							<a href="#16.2">16.2. Application Setting</a><br>
							<a href="#16.3">16.3. Database Setting</a><br>
							<a href="#16.4">16.4. Email Setting</a><br>
						</td>					
					</table>
				</div>				
				<div class=\"isi\">
					<hr>
					<h4 id="16.1">16.1. Setting Management</h4><a href="#bab16"><i> back to top >></i></a>
					<p>Pada Plansys, terdapat 3 setting yang perlu diatur agar sistem berjalan dengan baik. Ketiga setting tersebut mencakup Application Setting, Database Setting, dan Email Setting.

					<h4 id="16.2">16.2. Application Setting</h4><a href="#bab16"><i> back to top >></i></a>
					<p>Application Setting digunakan untuk mengatur application dan locale setting. Langkah-langkah settingnya sebagai berikut :
					<ol><li>Login sebagai Dev
						<li>Buka menu Settings >> pilih submenu Application Setting.
					 		<p><img src="plansys/modules/help/img/16-2-2.png"> 
						<li>Pada halaman Application Setting, isikan nama aplikasi sistem informasi yang sedang dikembangkan. Untuk field ini dapat juga dikosongi. Lalu pada Mode, pilih mode yang sesuai dengan kebutuhan. Mode tersebut terdiri dari 3 mode :
							<ul><li>Development		: mode untuk pengembangan aplikasi sistem informasi
								<li>Production		: saat ini mode Production sama dengan mode Development
								<li>Plansys Development	: mode untuk pengembangan tool Plansys
							</ul>
							<p><img src="plansys/modules/help/img/16-2-3.png"> 
						<li>Lalu atur beberapa format, yaitu Date Format, Time Format, Date Time Format. 
							<p><img src="plansys/modules/help/img/16-2-4.png"> 
						<li>Fitur Audit Trail dapat di aktifkan (Enbled) ataupun dinonaktifkan.
					 		<p><img src="plansys/modules/help/img/16-2-5.png"> 
						<li>Tekan tombol Save Setting untuk menyimpan perubahan-perubahan tersebut.
							<p><img src="plansys/modules/help/img/16-2-6.png"> 
					 </ol>

					<h4 id="16.3">16.3. Database Setting</h4><a href="#bab16"><i> back to top >></i></a>
					<p>Database setting digunakan untuk mengatur penggunaan database baik primary database maupun optional database. Pada saat instalasi plansys, informasi mengenai pengaksesan database sudah disimpan maka informasi yang ada di Database Setting ini pengaksesan database yang sedang aktif. Langkah-langkah untuk mengubah/mengatur penggunaan database adalah sebagai berikut :
					<ol><li>Login sebagai Dev
						<li>Buka menu Settings >> pilih Database Setting.
							<p><img src="plansys/modules/help/img/16-3-2.png"> 
						<li>Pada bagian Primary Database, pilih driver, host, dan port yang digunakan aplikasi untuk mengakses database. Untuk saat ini, Plansys mendukung driver database MySQL dan Oracle.
					 		<p><img src="plansys/modules/help/img/16-3-3.png"> 
						<li>Lalu isikan username, password, dan database yang digunakan.
					 		<p><img src="plansys/modules/help/img/16-3-4.png"> 
						<li>Jika ingin menambahkan database, pada Optional Database klik tombol +Add.
					 		<p><img src="plansys/modules/help/img/16-3-5.png"> 
						<li>Maka pada Optional Database, maka akan ditambahkan satu pengaturan database. Lalu, isikan Connection Name, Driver, Host, Port, Username, Password, Database.
					 		<p><img src="plansys/modules/help/img/16-3-6.png"> 
						<li>Jika ingin mengapus database dari Optional Database, pada bagian Optional Database klik tanda silang (X) di sebelah pojok kanan.
					 		<p><img src="plansys/modules/help/img/16-3-7.png"> 
						<li>Tekan tombol Save Setting untuk menyimpan perubahan-perubahan tersebut.
					 		<p><img src="plansys/modules/help/img/16-3-8.png"> 
					 </ol>

					<h4 id="16.4">16.4. Email Setting</h4><a href="#bab16"><i> back to top >></i></a>
					<p>Email setting digunakan untuk mengatur email master yang digunakan dalam aplikasi sistem informasi ini. Langkah-langkah pengaturan email adalah sebagai berikut :
					<ol><li>Login sebagai Dev
						<li>Buka Menu Settings >> pilih Email Setting
					 		<p><img src="plansys/modules/help/img/16-4-2.png"> 
						<li>Lalu pada halaman Email Setting, isikan pilih Transport >> isikan host dan port yang digunakan.
					 		<p><img src="plansys/modules/help/img/16-4-3.png"> 
						<li>Lalu isikan username dan password untuk mengakses Transport email tersebut >> From Address dapat diisi dengan email address yang juga valid. Email pada From Address merupakan email yang digunakan email sebagai pengirim dari aplikasi sistem informasi ini.
					 		<p><img src="plansys/modules/help/img/16-4-4.png"> 
						<li>Tekan tombol Save Setting untuk menyimpan perubahan-perubahan tersebut.
					 		<p><img src="plansys/modules/help/img/16-4-5.png"> 
				<div>				
				',
            ),
        );
    }
}