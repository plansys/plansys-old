<?php

class HelpTutorialBab11 extends Form {
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
					<h3 id="bab11">Bab XI. ACCESS PERMISSIONS </h3>
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#11.1">11.1. Membatasi Akses Module</a><br>
							<a href="#11.2">11.2. Membatasi Akses View (Form/Index)</a><br>							
						</td>					
					</table>
				</div>				
				<div class=\"isi\">
					<hr>					
					<h4 id="11.1">11.1. Membatasi Akses Module</h4><a href="#bab11"><i> back to top >></i></a>
					<p>Selain dengan menutup akses module secara default, perlu mengatur/memilih role dan user mana saja yang  diperbolehkan mengakses suatu module. Langkah-langkah untuk mengatur akses suatu module adalah sebagai berikut :
					<ol><li>Buka Module Builder  buka App  klik kanan pada module (misal: gudang)  klik Open New Tab maka akan menuju halaman module gudang.
					 		<p><img src="plansys/modules/help/img/11-1-1.png"> 
						<li>Lalu pada tab Access Control, terdapat pilihan Default Access Rule yaitu Deny atau Allow. Pilihan tersebut yang akan digunakan ketika tidak ada role yang sesuai. Jika dipilih Deny maka module tersebut tidak akan dapat dibukan/dipanggil pada halaman web.
							<p><img src="plansys/modules/help/img/11-1-2.png"> 
						<li>Tetap pada tab Access Control, di sebelah kiri untuk terdapat section Role Access untuk menambahkan Role dan di sebelah kanan terdapat section User Access untuk menambahkan user. Pengaturan Role Access dan User Access digunakan  untuk mengatur role dan user mana saja yang dapat atau tidak dapat mengakses module ini. Untuk menambahkan role yang dapat mengakses, klik tombol +Add pada section Role Access.
					 		<p><img src="plansys/modules/help/img/11-1-3.png"> 
						<li>Lalu pilih role yang diperbolehkan mengakses module tersebut.
					 		<p><img src="plansys/modules/help/img/11-1-4.png"> 
						<li>Lalu pilih permission (Allow / Deny / Custom) untuk role tersebut.
					 		<p><img src="plansys/modules/help/img/11-1-5.png"> 
							<ul><li>Jika memilih Custom akan muncul 2 pilihan yaitu Redirect atau Custom Code. 
						 			<p><img src="plansys/modules/help/img/11-1-5-i.png"> 
								<li>Jika memilih Custom Code maka akan muncul editor script seperti di bawah ini lalu edit script sesuai kebutuhan.
					 				<p><img src="plansys/modules/help/img/11-5-ii.png"> 
							</ul>
						<li>Pada section User Access di sebelah kanan, klik +Add untuk menambahkan user. 
					 		<p><img src="plansys/modules/help/img/11-1-6.png"> 
						<li>Langkah yang sama dengan menambahkan role access di atas, pilih user.
					 		<p><img src="plansys/modules/help/img/11-1-7.png"> 
						<li>Pilih permissions (Allow / Deny / Custome Action) untuk user tersebut.
					 		<p><img src="plansys/modules/help/img/11-1-8.png"> 
							<ul><li>Jika memilih Custom akan muncul 2 pilihan yaitu Redirect atau Custom Code. 
							 		<p><img src="plansys/modules/help/img/11-1-8-i.png"> 
								<li>Jika memilih Custom Code maka akan muncul editor script seperti di bawah ini lalu edit script sesuai kebutuhan .
									<p><img src="plansys/modules/help/img/11-1-8-ii.png"> 
							</ul>
						<li>Catatan dalam setting Role Access dan User Access :
							<ul><li>Jika Role Deny, maka silahkan pilih user yang diperbolehkan.Jika tidak maka akan seluruh user dalm role tsb.
								<li>Jika role Allow, tapi ingin membantasi sebagin user ya pilih user unutuk Deny. Jik tidak ada user yg di deny, maka semua user akan allow
					</ol>
					
					<h4 id="11.2">11.2. Membatasi Akses View (Form/Index)</h4><a href="#bab11"><i> back to top >></i></a>
					<p>Setelah mematasi akses suatu modul, pembatasn akses juga perlu diberikan ke suatu view (form/index). Misalnya, dalam satu module terdapat 2 role (kepala dan admin). Tentunya perlu ada perbedaan view (form/index) untuk kepala dan admin. Sebagai contoh, berikut pemberian batasan akses ke user kagud1 dan admin1. 
						<ul><li>Menu user kagud1 : Dashboard, Dashboard, Stok, Brg Masuk, Brg Keluar, Supplier, Laporan.
							<li>Menu user admin1 : Stok, Brg Masuk, Brg Keluar, Supplier, Laporan.
						</ul>

					<p>Langkah-langkah untuk membatasi view (form/index) adalah sebagai berikut :
					<ol><li>Pastikan menu untuk masing-masing role sudah dibuat.
							<p><img src="plansys/modules/help/img/11-2-1.png"> 
						<li>Pastikan masing-masing menu tersebut sudah didefinisikan sebagai menu tree di masing-masing pengaturan role.
							<p><img src="plansys/modules/help/img/11-2-2.png"> 	
					 	<li>Sebenarnya, 2 langkah di atas sudah dapat membatasi akses view (form/index).
							<ul><li>User kagud1 akan memiliki tmapilan menu seperti berikut :
							 		<p><img src="plansys/modules/help/img/11-2-3-i.png"> 
								<li>User admin1 akan memiliki tmapilan menu seperti berikut :
					 				<p><img src="plansys/modules/help/img/11-2-3-ii.png"> 
					 		</ul>
							<p>Akan tetapi sebenarnya user admin1 masih dapat mengakses view (form/index) yang dimiliki user kagud1 melalui memanggil url view tersebut. 
					 		<p><img src="plansys/modules/help/img/11-2-3.png"> 
						<li>Maka dari itu, perlu ditambahkan script pada controller untuk model (tabel database) yang hanya boleh di akses oleh role tertentu. Tambahkan script di bawah ini ke dalam setiap class function yang mengakses model tersebut (index/new/etc..) pada bagian awal baris isi script.
							<p><img src="plansys/modules/help/img/11-2-4.png"> 
						<li>Sebagai contoh, isi dari clss function New pada SupplieController.php akan seperti ini :
					 		<p><img src="plansys/modules/help/img/11-2-5.png"> 
						<li>Buat form GudangNoAccess pada Form Builder. GudangNoAccess adalah form yang akan dirender plansys jika diakses oleh user yang memiliki role tidak sama dengan role gudang.kagud. 
					 		<p><img src="plansys/modules/help/img/11-2-6.png"> 
						<li>Tambahkan pesan pada halaman GudangNoAcceess, misalnya seperti berikut :
					 		<p><img src="plansys/modules/help/img/11-2-7.png"> 
						<li>Maka ketika user yang tidak memiliki role gudang.kagud (misal: user admin1) maka akan muncul tampilan berikut dan view (form/index) supplier tidak akan dapat diakses oleh user tersebut dengan melalui url.
							<p><img src="plansys/modules/help/img/11-2-8.png"> 
					 </ol>
				</div>		
				',
            ),
        );
    }

}