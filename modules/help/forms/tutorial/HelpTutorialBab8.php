<?php

class HelpTutorialBab8 extends Form {
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
					<h3 id="bab8">Bab VIII. ROLE MANAGEMENT</h3>
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#8.1">8.1. Konsep Role</a><br>
							<a href="#8.2">8.2. Create Role</a><br>
							<a href="#8.3">8.3. Update Role</a><br>
							<a href="#8.4">8.4. Delete Role</a><br>
						</td>					
					</table>
				</div>		
				<div class=\"isi\">
					<hr>
					<h4 id="8.1">8.1. Konsep Role</h4><a href="#bab8"><i> back to top >></i></a>
					<p>Role merupakan peran seorang user. Sistem informasi yang akan diakses oleh beberapa user akan diatur hak akses masing-masing user berdasarkan peran (role) yang menjadi haknya. Sebelum mengatur role untuk user, role yang akan digunakan dalam sistem informasi tersebut harus didefinisikan dan dibuatkan dengan jelas dalam sistem.

					<h4 id="8.2">8.2. Create Role</h4><a href="#bab8"><i> back to top >></i></a>
					<p>Untuk membuat role baru, langkah-langkahnya adalah sebagai berikut : 
					<ol><li>Pilih menu Users >> pilih submenu Role Manager.
						<p><img src="plansys/modules/help/img/8-2-1.png">
						<li>Pada halaman New Role, isikan nama role pada Role Name (tanpa spasi dan huruf kecil semua) dan isikan deskripsi singkat pada Role Description >> klik Save.
						<p><img src="plansys/modules/help/img/8-2-2.png">
						<li>Pada halaman selanjutnya, isikan url pada Home Page >> Pilih menu Tree tapi karena kita belum membuat menu tree maka biarkan default saja nanti akan di-edit lagi >> klik Simpan.
						<p><img src="plansys/modules/help/img/8-2-3.png">
						<li>Jika berhasil, maka akan ada informasi bahwa Role berhasil disimpan. Role baru tersebut akan tampil pada daftar Role Manager.
						<p><img src="plansys/modules/help/img/8-2-4.png">
					</ol>

					<h4 id="8.3">8.3. Update Role</h4><a href="#bab8"><i> back to top >></i></a>
					<p>User dapat melakukan update Role, langkahnya sebagai berikut :					
					<ol><li>Buka halaman daftar role, klik pada tombol Edit (tombol biru di sebelah kanan) pada salah satu role yang akan diupdate.
						<p><img src="plansys/modules/help/img/8-3-1.png">
						<li>Setelah melakukan Edit, klik Save untuk menyimpan perubahan tersebut.
					</ol>
						 
					<h4 id="8.4">8.4. Delete Role</h4><a href="#bab8"><i> back to top >></i></a>
					<p>User menghapus role, langkahnya adalah sebagai berikut :
					<ol><li>Tekan tombol Edit (tombol biru di sebelah kanan) pada salah satu role yang akan dihapus.
						<li>Akan menuju halaman yang sama dengan update Role, lalu klik tombol Hapus >> maka akan muncul pop up konfirmasi menghapus role, ketik DELETE >> klik OK	
						<p><img src="plansys/modules/help/img/8-4-2.png">
					</ol>
				</div>				
				',
            ),
        );
    }
}