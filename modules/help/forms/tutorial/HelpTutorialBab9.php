<?php

class HelpTutorialBab9 extends Form {
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
					<h3>Bab IX. USER MANAGEMENT</h3>
					<h4>9.1. Konsep User</h4>
					<p>Setelah dibuatkan role-role dalam sistem informasi, selanjutnya perlu dibuatkan user dan diberikan role yang sesuai dengan hak aksesnya dalam sistem informasi tersebut.

					<h4>9.2. Create User</h4>
					<p>Langkah-langkah untuk membuat user baru adalah sebagai berikut :
					<ol><li>Pilh menu User >> pilih submenu User List
							<p><img src="plansys/modules/help/img/9-2-1.png">
						<li>Pada halaman New User, isikan username >> pilih role yang sesuai (defaultnya memiliki role Developer IT maka harus dihapus setelah menambahkan role yang seharusnya). Role yang diberikan ke user dapat lebih dari satu role, letakkan role defaultnya pada posisi teratas dengan cara drag and drop.
							<p><img src="plansys/modules/help/img/9-2-2.png">
						<li>Lalu isikan email >> isikan password sebanyak 2 kali >> klik Simpan.
						<li>Maka akan ditambahkan user baru tersebut dalam User List.
							<p><img src="plansys/modules/help/img/9-2-4.png">
						<li>Cek login user dengan akun user yang telah dibuat. Silahkan Log out dahulu dari user Dev, caranya klik menu Dev >> klik Log Out
							<p><img src="plansys/modules/help/img/9-2-5.png">
						<li>Kembali ke halaman awal, masukkan username dan password >> klik Submit.
							<p><img src="plansys/modules/help/img/9-2-6.png">
						<li>Jika berhasil maka akan menuju halaman default page dari role yang ditempelkan pada user tersebut.
					</ol>

					<h4>9.3. Update User</h4>
					<p>Langkah-langkah untuk memperbarui (update) user adalah sebagai berikut :
					<ol><li>Pada halaman User List, tekan tombol Edit di sebelah kanan user yang akan perbarui
							<p><img src="plansys/modules/help/img/9-3-1.png">
						<li>Menuju ke halaman Update User, isikan data perubahan yang diinginkan >> klik Simpan.
							<p><img src="plansys/modules/help/img/9-3-2.png">
					</ol>
					 
					<h4>9.4. Delete User</h4>
					<p>Langkah-langkah untuk menghapus user adalah sebagai berikut :
					<ol><li>Pada halaman yang sama dengan halaman untuk Update User.
						<li>Lalu klik tombol Hapus ( ) >> maka akan muncul pop up konfirmasi menghapus role, ketik DELETE >> klik OK	
							<p><img src="plansys/modules/help/img/9-4-2.png">
					</ol>
					 

					<h4>9.5. Audit Trail</h4>
					<p>Audit Trail digunakan untuk menyimpan history aktifitas user dalam aplikasi sistem informasi ini. Sehingga setiap aktifitas user dalam membuat/menambahkan data baru, mengedit, menghapus data akan terekam dalam sistem termasuk kapan waktu aktifitas tersebut dilakukan. Audit Trail ini hanya digunakan untuk menyimpan history aktifitas user selain user yang memiliki role Developer. Jadi, aktifitas user dengan role Developer tidak akan ada history aktifitasnya dalam aplikasi sistem informasi ini.
					<p>Untuk melihat history aktifitas user adalah sebagai berikut :
					<ol><li>Setelah user berhasil login, pilih Nama User di sebelah kiri pojok atas >> pilih Edit Profile.
							<p><img src="plansys/modules/help/img/9-5-1.png">
						<li>Maka akan terbuka halaman Edit Profile, di bagian bawah ada section Audit Trail. Di bagian ini, history aktifitas user akan ditampilkan.
							<p><img src="plansys/modules/help/img/9-5-2.png">
						<li>Daftar history aktifitas tersebut dapat difilter berdasarkan Date, Type, Description, atau Pathinfo.
					</ol>

				<div>
				
				',
            ),
        );
    }

}