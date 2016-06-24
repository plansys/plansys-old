<?php

class HelpTutorialBab5 extends Form {
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
					<h3 id="bab5">Bab V. CRUD GENERATOR</h3>
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#5.1">5.1. Konsep CRUD Generator</a><br>
							<a href="#5.2">5.2. Create CRUD</a><br>
							<a href="#5.3">5.3. Re-Create CRUD</a><br>
							<a href="#5.4">5.4. Create RelForm</a><br>
							<a href="#5.5">5.5. Pengelolaan CRUD</a><br>
						</td>
					</table>
				</div>
				<div class=\"isi\">
					<hr>
					<h4 id="5.1">5.1. Konsep CRUD Generator</h4><a href="#bab5"><i> back to top >></i></a>
					<p>Fungsi manipulasi data mencakup Create (Membuat data baru), Read (membaca data yang sudah ada), Update (memperbarui data), Delete (menghapus data). Agar fungsi CRUD dapat berjalan dengan baik, pada sistem informasi yang menggunakan framework MVC, perlu dibuatkan View dan Controllernya. Dengan menggunakan Plansys, View dan Controller untuk menjalankan fungsi CRUD tersebut akan dibuatkan secara otomatis oleh CRUD Generator.
					
					<h4 id="5.2">5.2. Create CRUD</h4><a href="#bab5"><i> back to top >></i></a>
					<h5>(a). Menggunakan Module</h5>
					<p>Langkah-langkahnya untuk membuat CRUD menggunakan CRUD Generator adalah sebagai berikut:
					<ol><li>Sebelum menjalankan CRUD Generator, pastikan bahwa Model yang akan dibuatkan CRUD nya sudah dibuat melalui menu Model Builder.
						<li>Pilih menu Builder, pilih Forms
						<li>Klik kanan pada module (sdm) >> pilih New CRUD.
						<p><img src="plansys/modules/help/img/5-2-a-3.png">
						<li>Maka akan menuju halaman Generate CRUD. Pada Base Model, pilih Model yang digunakan.
						<p><img src="plansys/modules/help/img/5-2-a-4.png">
						<li>Lalu pada halaman yang sama akan muncul beberapa isian field, pilihan, advanced settings, relations. Biarkan saja jika ingin menggunakan pengaturan default >> Klik Next Step.
						<p><img src="plansys/modules/help/img/5-2-a-5.png">
						<li>Pada halaman selanjutnya, akan diinformasikan type item yang akan dibuat agar tercipta fungsi CRUD dengan benar. Type item tersebut mencakup folder, index, form, dan controller. Item tersebut siap di generate ditandai dengan status READY. Lalu klik Generate CRUD.
						<p><img src="plansys/modules/help/img/5-2-a-6.png">
						<li>Proses generate CRUD selesai ditandai dengan status masing-masing item menjadi OK >> Klik Done untuk menutup halaman Generate CRUD.
						<p><img src="plansys/modules/help/img/5-2-a-7.png">
						<li>Item-item yang telah dibuat Generate CRUD dapat dilihat pada masing-masing submenu.
							<ul><li>Folder (app.modules.sdm.forms.anggota), index (SdmAnggotaIndex.php), form (SdmAnggotaForm.php)
								<p><img src="plansys/modules/help/img/5-2-a-8-i.png">
								<li>AnggotaController.php
								<p><img src="plansys/modules/help/img/5-2-a-8-ii.png">
							</ul>
					</ol>
					
					<h5>(b). Tanpa Menggunakan Module</h5>
					<p>Langkah-langkahnya untuk membuat CRUD menggunakan CRUD Generator adalah sebagai berikut:
					<ol><li>Sebelum menjalankan CRUD Generator, pastikan bahwa Model yang akan dibuatkan CRUD nya sudah dibuat melalui menu Model Builder.
						<li>Pilih menu Builder, pilih Forms
						<p><img src="plansys/modules/help/img/5-2-b-2.png">
						<li>Klik kanan pada app >> pilih New CRUD.
						<li>Maka akan menuju halaman Generate CRUD. Pada Base Model, pilih Model yang digunakan.
						<li>Lalu pada halaman yang sama akan muncul beberapa isian field, pilihan, advanced settings, relations. Biarkan saja jika ingin menggunakan pengaturan default >> Klik Next Step.
						<p><img src="plansys/modules/help/img/5-2-b-5.png">
						<li>Pada halaman selanjutnya, akan diinformasikan type item yang akan dibuat agar tercipta fungsi CRUD dengan benar. Type item tersebut mencakup folder, index, form, dan controller. Item tersebut siap di generate ditandai dengan status READY. Lalu klik Generate CRUD.
						<p><img src="plansys/modules/help/img/5-2-b-6.png">
						<li>Proses generate CRUD selesai ditandai dengan status masing-masing item menjadi OK >> Klik Done untuk menutup halaman Generate CRUD.
						<p><img src="plansys/modules/help/img/5-2-b-7.png">
						<li>Item-item yang telah dibuat Generate CRUD dapat dilihat pada masing-masing submenu.
							<ul><li>Folder (app.forms.divisi), index (AppDivisiIndex.php), form (AppDivisiForm.php)
								<p><img src="plansys/modules/help/img/5-2-b-8-i.png">
								<li>DivisiController.php
								<p><img src="plansys/modules/help/img/5-2-b-8-ii.png">
							</ul>
					</ol>
					
					<<h4 id="5.3">5.3. Re-Create CRUD</h4><a href="#bab5"><i> back to top >></i></a>
					<h5>(a). Menggunakan Module</h5>
					<p>Langkah-langkah untuk Re-Create CRUD adalah sebagai berikut :
					<ol><li>Sebelum menjalankan CRUD Generator ulang, pastikan bahwa Model yang baru sudah dibuat  melalui menu Model Builder.
						<li>Pilih menu Builder, pilih Forms >> klik kanan pada Module (sdm) >> pilih New CRUD.
						<p><img src="plansys/modules/help/img/5-3-a-2.png">
						<li>Maka akan menuju halaman Generate CRUD. Pada Base Model, pilih Model yang digunakan.
						<li>Lalu pada halaman yang sama akan muncul beberapa isian field, pilihan, advanced settings, relations. Biarkan saja jika ingin menggunakan pengaturan default >> Klik Next Step.
						<p><img src="plansys/modules/help/img/5-3-a-4.png">
						<li>Pada halaman selanjutnya, akan diinformasikan type item yang akan dibuat agar tercipta fungsi CRUD dengan benar. Type item tersebut mencakup folder, index, form, dan controller. Item tersebut siap di generate ditandai dengan status READY sedangkan item yang sudah ada ditandai dengan status EXIST. Beri centang pada kolom Check all untuk memberi centang Overwrite pada setiap EXIST item >> Lalu klik Generate CRUD.
						<p><img src="plansys/modules/help/img/5-3-a-5.png">
						<li>Proses generate CRUD selesai ditandai dengan status masing-masing item menjadi OK >> Klik Done untuk menutup halaman Generate CRUD.
						<li>Item-item yang telah dibuat Generate CRUD dapat dilihat pada masing-masing submenu.
							<ul><li>Folder (app.modules.sdm.forms.anggota), index (SdmAnggotaIndex.php), form (SdmAnggotaForm.php)
								<p><img src="plansys/modules/help/img/5-3-a-6-i.png">
								<li>AnggotaController.php
								<p><img src="plansys/modules/help/img/5-3-a-6-ii.png">
							</ul>
					</ol>
					<h5>(b). Tanpa Menggunakan Module</h5>
					<p>Langkah-langkah untuk Re-Create CRUD adalah sebagai berikut :
					<ol><li>Sebelum menjalankan CRUD Generator ulang, pastikan bahwa Model yang baru sudah dibuat  melalui menu Model Builder.
						<li>Pilih menu Builder, pilih Forms >> klik kanan pada app (jika tidak menggunakan Modul) atau Modul (jika menggunakan Modul) >> pilih New CRUD.
						<p><img src="plansys/modules/help/img/5-3-b-2.png">
						<li>Maka akan menuju halaman Generate CRUD. Pada Base Model, pilih Model yang digunakan.
						<li>Lalu pada halaman yang sama akan muncul beberapa isian field, pilihan, advanced settings, relations. Biarkan saja jika ingin menggunakan pengaturan default >> Klik Next Step.
						<li>Pada halaman selanjutnya, akan diinformasikan type item yang akan dibuat agar tercipta fungsi CRUD dengan benar. Type item tersebut mencakup folder, index, form, dan controller. Item tersebut siap di generate ditandai dengan status READY sedangkan item yang sudah ada ditandai dengan status EXIST. Beri centang pada kolom Check all untuk memberi centang Overwrite pada setiap EXIST item >> Lalu klik Generate CRUD.
						<li>Proses generate CRUD selesai ditandai dengan status masing-masing item menjadi OK >> Klik Done untuk menutup halaman Generate CRUD.
						<li>Item-item yang telah dibuat Generate CRUD dapat dilihat pada masing-masing submenu.
							<ul><li>Folder (app.modules.sdm.forms.divisi), index (SdmDivisiIndex.php), form (SdmDivisiForm.php)
								<p><img src="plansys/modules/help/img/5-3-a-7-i.png">
								<li>DivisiController.php
								<p><img src="plansys/modules/help/img/5-3-a-7-ii.png">
							</ul>
					</ol>
					
					<h4 id="5.4">5.4. Create RelForm</h4><a href="#bab5"><i> back to top >></i></a>
					<p>RelForm merupakan form sekunder yang dipanggil melalui button dalam suatu form dan tidak dapat dibuka tanpa form primer tersebut. Sebagai contoh, ada 2 tabel yaitu tabel blog dan kategori. Dalam tabel blog  terdapat kolom kategori_id sebagai penghubung dalam relasi tabel blog dengan tabel kategori. Berikut adalah langkah-langkah untuk membuat RelForm tabel blog dengan tabel kategori : 
					<ol><li>Jalankan CRUD generator melalui menu Form Builder. Pilih model yang menjadi model utama (misal: Blog).
						<p><img src="plansys/modules/help/img/5-4-1.png">
						<li>Selanjutnya di halaman kedua, pada bagian Relations kli tombol +Add untuk menambahkan relasi tabel >> Pilih relasi pada Relasi Name >> pilih model form sekunder pada Form Type, misal PopUp maka ketika melalui form utama memamggil form sekunder akan menampilkannya form sekunder dalam bentuk pop up.
						<p><img src="plansys/modules/help/img/5-4-2.png">
						<li>Klik Next Step untuk melanjutkan proses generate CRUD.
						<li>Proses generate CRUD akan membuat file relform selain file yang degenerate default CRUD generator (index, form, controller).
						<p><img src="plansys/modules/help/img/5-4-4.png">
						<li>Maka pada Form Builder, akan ditampilkan folder dan semua file yang telah degenerate.
						<p><img src="plansys/modules/help/img/5-4-5.png">
						<li>Pada halaman form Tambah Blog tampak seperti berikut, di bawah field Kategori terdapat button +New Kategori :
						<p><img src="plansys/modules/help/img/5-4-6.png">
						<li>Pilihan pada field Kategori akan menampilkan isi kolom nama dari tabel kategori. Jika dalam tabel tidak ada kolom nama, maka kolom kedua setelah kolom ID yang akan digunakan.
						<p><img src="plansys/modules/help/img/5-4-7.png">
						<li>Jika tombol +Add New Kategori diklik maka akan membuka halaman RelKategoriForm. 
						<p><img src="plansys/modules/help/img/5-4-8.png">
					</ol> 

					
					<h4 id="5.5">5.5. Pengelolaan CRUD</h4><a href="#bab5"><i> back to top >></i></a>
					<h5>5.5.1. Create</h5>
					<p>Langkah-langkah untuk membuat data baru (Create) sebagai berikut :
					<ol><li>Pada halaman halaman Divisi Index, klik tombol Tambah Divisi 
						<p><img src="plansys/modules/help/img/5-5-1-1.png">
						<li>Pada halaman Form Divisi, isikan data pada field-field yang tersedia >> Klik Save
						<p><img src="plansys/modules/help/img/5-5-1-2.png">
					</ol>

					<h5>5.5.2. Read</h5>
					<p>Langkah-langkah untuk membaca/melihat data (Read) sebagai berikut :
					<p>Pada halaman halaman Divisi Index, data dari tabel Divisi ditampilkan.
					<p><img src="plansys/modules/help/img/5-5-2-1.png">
												
					<h5>5.5.3. Update</h5>
					<p>Langkah-langkah untuk memperbarui data (Update) sebagai berikut :
					<ol><li>Pada halaman halaman Divisi Index, di sebelah kanan data dalam tabel terdapat 2 icon yaitu icon Change ( ) dan icon Delete (  ).
						<p><img src="plansys/modules/help/img/5-5-3-1.png">
						<li>Pilih icon Change untuk mengubah data tersebut, pada halaman Update ganti data yang diperlukan >> Klik Save.
						<p><img src="plansys/modules/help/img/5-5-3-2.png">
					</ol>

					<h5>5.5.4. Delete</h5>
					<p>Langkah-langkah untuk menghapus data (Delete) sebagai berikut :
					<ol><li>Pada halaman halaman Divisi Index, di sebelah kanan data dalam tabel pilih icon Delete untuk menghapus data. Akan muncul pop up konfirmasi penghapusan data, klik OK.
						<p><img src="plansys/modules/help/img/5-5-4-1.png">
						<li>Sistem akan kembali ke halaman index, dan terdapat pesan bahwa data berhasil dihapus.
						<p><img src="plansys/modules/help/img/5-5-4-2.png">
					</ol>



				</div>
				
				',
            ),
        );
    }

}