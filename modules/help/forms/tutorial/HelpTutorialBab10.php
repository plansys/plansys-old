<?php

class HelpTutorialBab10 extends Form {
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
					<h3 id="bab10">Bab X. MENU TREE EDITOR</h3>
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#10.1">10.1. Konsep Menu Tree</a><br>
							<a href="#10.2">10.2. Create Menu</a><br>
							<a href="#10.3">10.3. Update Menu</a><br>
							<a href="#10.4">10.4. Delete Menu</a><br>
							<a href="#10.5">10.5. Memasang Menu pada Role</a><br>							
						</td>					
					</table>
				</div>				
				<div class=\"isi\">
					<hr>					
					<h4 id="10.1">10.1. Konsep Menu Tree Editor</h4><a href="#bab10"><i> back to top >></i></a>
					<p>Sebelum dibuatkan menu, setiap user yang akan dibuatkan nantinya hanya dapat log ini tapi tidak memiliki menu dalam aplikasi sistem informasi ini. Jika belum dibuatkan menu, setelah user berhasil login, halaman yang akan menuju halaman dengna url Home Page. Sebagai contoh log in user Manjaer SDM dengan role manajersdm dimana role tersebut memiliki Home Page /sdm/anggota, maka setelah user manajersdm log in akan menuju halaman seperti di bawah ini :
					<p><img src="plansys/modules/help/img/10-1.png"> 

					<h4 id="10.2">10.2. Create Menu</h4><a href="#bab10"><i> back to top >></i></a>
					<h5>(a). Menggunakan Module</h5>
					<p>Langkah-langkah untuk membuat menu adalah sebagai berikut :
					<ol><li>Login sebagai Dev.
						<li>Plih menu Builder >> pilih submenu Menu Tree Editor
							<p><img src="plansys/modules/help/img/10-2-a-2.png"> 
						<li>Lalu klik kanan pada module >> klik New Menu.
							<p><img src="plansys/modules/help/img/10-2-a-3.png"> 
						<li>Muncul pop up, lalu isikan nama menu >> klik OK.
							<p><img src="plansys/modules/help/img/10-2-a-4.png"> 
						<li>Maka menu baru (SDM) akan tampil pada daftar menu. Pada halaman ini terdapat 3 bagian:						
							<ul><li>Bagian sebelah kiri 	: daftar seluruh induk menu tree 
								<li>Bagian sebelah tengah	: baris menu dari masing-masing induk menu tree
								<li>Bagian sebelah kanan	: properties dari masing-masing menu dari barisan menu
							</ul>
							<p><img src="plansys/modules/help/img/10-2-a-5.png"> 
						<li>Untuk membuat menu dan barisan menu, klik tombol hijau New pada bagian tengah halaman >> klik menu, maka bagian properties di sebelah kanan akan terbuka lalu isikan :
							<ul><li>Label
								<li>URL, isikan tanda pagar (#) jika tidak diberikan link URL.
								<li>Menu icon
							</ul>
							<p>Perubahan pada field Label dan Menu Icon dapat langsung tampil pada halaman bagian tengah.
							<p><img src="plansys/modules/help/img/10-2-a-6.png"> 
						<li>Lakukan langkah (6) untuk membuat menu lagi. Jika ingin mengubah menu menjadi submenu caranya adalah drag and drop menu tersebut ke menu induknya. Sebagai contoh, klik lalu drag menu Sub Menu 1 --> arahkan ke bawah menu SDM >> geser ke kanan sedikit hingga border Sub Menu 1 sedikit menjorok ke kanan dan lepaskan. Lakukan langkah yang sama untuk sub menu yang lainnya. Sebaliknya jika ingin mengubah submenu menjadi menu, klik dan geser ke arah kiri (keluar dari menu induk di atasnya).
							<p><img src="plansys/modules/help/img/10-2-a-7.png"> 
						<li>Jika sudah, maka tampilan menu SDM yang memiliki 2 submenu (Sub menu 1, Sub Menu 2) tersebut seperti di bawah ini :
							<p><img src="plansys/modules/help/img/10-2-a-8.png"> 
						<li>Langkah selanjutnya, menempelkan menu ini ke role agar user yang memiliki role tersebut dapat membuka menu ini.  <silahkan lihat Bagian 10.5. Memasang Menu pada Role>
					</ol>		
					
					<h5>(b). Tanpa Menggunakan Module</h5>
					<p>Langkah-langkah untuk membuat menu adalah sebagai berikut :
					<ol><li>Login sebagai Dev.
						<li>Plih menu Builder >> pilih submenu Menu Tree Editor
							<p><img src="plansys/modules/help/img/10-2-b-2.png"> 
						<li>Lalu klik kanan pada app >> klik New Menu.
							<p><img src="plansys/modules/help/img/10-2-b-3.png"> 
						<li>Muncul pop up, lalu isikan nama menu >> klik OK.
							<p><img src="plansys/modules/help/img/10-2-b-4.png"> 
						<li>Maka menu baru (Kadiv) akan tampil pada daftar menu. Pada halaman ini terdapat 3 bagian:
							<ul><li>Bagian sebelah kiri 	: daftar seluruh induk menu tree 
								<li>Bagian sebelah tengah	: baris menu dari masing-masing induk menu tree
								<li>Bagian sebelah kanan	: properties dari masing-masing menu dari barisan menu
							</ul>
							<p><img src="plansys/modules/help/img/10-2-b-5.png"> 
						<li>Untuk membuat menu dan barisan menu, klik tombol hijau New pada bagian tengah halaman >> klik menu, maka bagian properties di sebelah kanan akan terbuka lalu isikan :
							<ul><li>Label
								<li>URL, isikan tanda pagar (#) jika tidak diberikan link URL.
								<li>Menu icon
							</ul>
							<p>Perubahan pada field Label dan Menu Icon dapat langsung tampil pada halaman bagian tengah.
							<p><img src="plansys/modules/help/img/10-2-b-6.png"> 
						<li>Lakukan langkah (6) untuk membuat menu lagi. Jika ingin mengubah menu menjadi submenu caranya adalah drag and drop menu tersebut ke menu induknya. Sebagai contoh, klik lalu drag menu Sub Menu 1 --> arahkan ke bawah menu Divisi >> geser ke kanan sedikit hingga border Sub Menu 1 sedikit menjorok ke kanan dan lepaskan. Lakukan langkah yang sama untuk sub menu yang lainnya. Sebaliknya jika ingin mengubah submenu menjadi menu, klik dan geser ke arah kiri (keluar dari menu induk di atasnya).
							<p><img src="plansys/modules/help/img/10-2-b-7.png"> 
						<li>Jika sudah, maka tampilan menu Divisi yang memiliki 2 submenu (Sub menu 1, Sub Menu 2) tersebut seperti di bawah ini :
							<p><img src="plansys/modules/help/img/10-2-b-8.png"> 
						<li>Langkah selanjutnya, menempelkan menu ini ke role agar user yang memiliki role tersebut dapat membuka menu ini.  <silahkan lihat Bagian 10.5. Memasang Menu pada Role>
					</ol>
					
					<h4 id="10.3">10.3. Update Menu</h4><a href="#bab10"><i> back to top >></i></a>
					<h5>(a). Menggunakan Module</h5>
					<p>Untuk mengupdate menu, buka halaman yang sama dengan membuat menu. Langkah-langkahnya adalah sebagai berikut :					
					<ol><li>Login sebagai Dev >> buka menu Builder >> pilih Menu Tree Editor >> buka induk menu yang akan diEdit (misal menu SDM).
							<p><img src="plansys/modules/help/img/10-3-a-1.png"> 
						<li>Pilih menu ataupun sub menu yang akan diEdit, misalnya menambah icon pada menu.
							<p><img src="plansys/modules/help/img/10-3-a-2.png"> 					 
						<li>Jika diperlukan untuk mengedit script menu/submenu tersebut, pilih menu/submenu >> klik Normal Menu pada bagian pojok kanan atas hingga berubah menjadi Custom Script.
							<p><img src="plansys/modules/help/img/10-3-a-3.png"> 
						<li>Maka halaman bagian tengah (baris menu) dan bagian kanan (properties) akan digabung menjadi satu halaman editor. Ubah script melalui editor tersebut >> klik Custom Script agar menjadi Normal Menu.
							<p><img src="plansys/modules/help/img/10-3-a-4.png"> 
						<li>Hasil perubahan dapat langsung ditampilkan pada halaman tersebut :
							<p><img src="plansys/modules/help/img/10-3-a-5.png"> 
					</ol>

					<h6>(b). Tanpa Menggunakan Module</h6>
					<p>Untuk mengupdate menu, buka halaman yang sama dengan membuat menu. Langkah-langkahnya adalah sebagai berikut :
					<ol><li>Login sebagai Dev >> buka menu Builder >> pilih Menu Tree Editor >> buka induk menu yang akan diEdit (misal menu Kadiv).
							<p><img src="plansys/modules/help/img/10-3-b-1.png"> 
						<li>Pilih menu ataupun sub menu yang akan diEdit >> ubah Label, URL, menu icon.
							<p><img src="plansys/modules/help/img/10-3-b-2.png"> 
						<li>Jika diperlukan untuk mengedit script menu/submenu tersebut, pilih menu/submenu >> klik Normal Menu pada bagian pojok kanan atas hingga berubah menjadi Custom Script.
							<p><img src="plansys/modules/help/img/10-3-b-3.png"> 
						<li>Maka halaman bagian tengah (baris menu) dan bagian kanan (properties) akan digabung menjadi satu halaman editor. Ubah script melalui editor tersebut >> klik Custom Script agar menjadi Normal Menu.
							<p><img src="plansys/modules/help/img/10-3-b-4.png"> 
						<li>Hasil perubahan dapat langsung ditampilkan pada halaman tersebut :
							<p><img src="plansys/modules/help/img/10-3-b-5.png"> 
					</ol> 

					<h4 id="10.4">10.4. Delete Menu</h4><a href="#bab10"><i> back to top >></i></a>
					<h5>(a). Menggunakan Module</h5>
					<p>Langkah untuk menghapus menu adalah sebagai berikut :
					<ol><li>Login sebagai Dev >> buka menu Builder >> pilih Menu Tree Editor.
						<li>Pilih module >> klik pada menu yang akan dihapus (misal SDM)
							<p><img src="plansys/modules/help/img/10-4-a-2.png"> 
						<li>Untuk menghapus menu ataupun sub menu, caranya adalah pilih menu yang akan dihapus.
							<p><img src="plansys/modules/help/img/10-4-a-3.png"> 
						<li>Lalu klik Delete.
						<li>Untuk menghapus seluruh menu, dapat langsung klik kanan pada menu di sebelah kiri.
							<p><img src="plansys/modules/help/img/10-4-a-5.png"> 
						<li>Pilih Delete >> Klik OK pada dialog konfirmasi penghapusan menu.
					</ol>

					<h6>(b). Tanpa Menggunakan Module</h6>
					<p>Langkah untuk menghapus menu adalah sebagai berikut :
					<ol><li>Login sebagai Dev >> buka menu Builder >> pilih Menu Tree Editor.
						<li>Pilih app >> klik pada menu yang akan dihapus (misal Kadiv)
							<p><img src="plansys/modules/help/img/10-4-b-2.png"> 
						<li>Untuk menghapus menu ataupun sub menu, caranya adalah pilih menu yang akan dihapus.
							<p><img src="plansys/modules/help/img/10-4-b-3.png"> 
						<li>Lalu klik Delete ( ).
						<li>Untuk menghapus seluruh menu, dapat langsung klik kanan pada menu di sebelah kiri (misal Kadiv) >> pilih Delete.
							<p><img src="plansys/modules/help/img/10-4-b-5.png"> 
						<li>Klik OK pada dialog konfirmasi penghapusan menu.
					</ol>

					<h4 id="10.5">10.5. Memasang Menu pada Role</h4><a href="#bab10"><i> back to top >></i></a>
					<p>Menu yang telah dibuat harus dipasangkan/ditempelkan pada role agar dapat dibuka di halaman web. 
					<ol><li>Buka halaman daftar role, klik pada tombol Edit (tombol biru   di sebelah kanan) pada salah satu role yang akan diupdate.
							<p><img src="plansys/modules/help/img/10-5-1.png"> 
						<li>Pada field Role Name, ganti nama role menjadi nama_modul.nama_role seperti berikut :
							<p><img src="plansys/modules/help/img/10-5-2.png"> 
						<li>Klik Simpan maka akan kembali ke halaman daftar role >> klik tombol Edit lagi.
							<p><img src="plansys/modules/help/img/10-5-3.png"> 
						<li>Maka akan kembali ke halaman Role Detail: sdm.manajersdm >> Pada field Menu Tree, pilih menu (misal SDM).
							<p><img src="plansys/modules/help/img/10-5-4.png"> 
						<li>Setelah melakukan Edit, klik Save untuk menyimpan perubahan tersebut.
						<li>Log in dengan user yang memiliki role manajersdm maka akan memiliki menu SDM yang telah dipasangkan/ditempelkan tadi seperti di bawah ini :
							<p><img src="plansys/modules/help/img/10-5-6.png"> 
					</ol> 
				</div>				
				',
            ),
        );
    }

}