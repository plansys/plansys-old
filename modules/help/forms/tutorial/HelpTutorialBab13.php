<?php

class HelpTutorialBab13 extends Form {
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
					<h3 id="bab13">Bab XIII. EMAIL BUILDER</h3>		
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#13.1">13.1. Konsep Email Builder</a><br>
							<a href="#13.2">13.2. Create Email Template<</a><br>
							<a href="#13.3">13.3. Update Email Template</a><br>
							<a href="#13.4">13.4. Delete Email Template</a><br>
						</td>					
					</table>
				</div>				
				<div class=\"isi\">
					<hr>					
					<h4 id="13.1">13.1. Konsep Email Builder</h4><a href="#bab13"><i> back to top >></i></a>
					<p>Email Builder digunakan untuk membuat template email. Template email berisi script HTML dan dapat di Preview langsung dari Plansys. Untuk proses pengiriman email dihandle oleh controller. Dalam controller tersebut akan mendefinisikan Subject Email, To (penerima email), dan Parameter (parameter dapat berisi : konten email, footer, ataupun informasi lainnya yang ditampung dalam sebuah variabel).

					<h4 id="13.2">13.2. Create Email Template</h4><a href="#bab13"><i> back to top >></i></a>
					<p>Langkah-langkah untuk membuat Email Template adalah sebagai berikut :
					<ol><li>Login sebagai Dev
						<li>Pada menu Builder, pilih Email Builder.
						 	<p><img src="plansys/modules/help/img/13-2-2.png"> 
						<li>Lalu klik kanan App >> Klik New Email.
						 	<p><img src="plansys/modules/help/img/13-2-3.png"> 
						<li>Pada kotak New Email Template, isikan nama template email >> klik Save. 
						 	<p><img src="plansys/modules/help/img/13-2-4.png"> 
						<li>Pada daftar Email Template >> App, akan ditambahkan template email yang telah dibuat. Lalu klik template email >> Edit script template email tersebut melalui editor yang disediakan, bagian yang perlu diEdit antara lain :
							<ul><li>Bagian Title	: isi title menjadi Subject Email
								<li>Bagian Style	: mengatur design style (script CSS)
								<li>Bagian Body	: berisi konten email yang didapatkan dari isi variable dimana variabel 
							ini berisi informasi maupun data hasil query. Variabel tersebut       didefinisikan pada bagian if (@$isPreview){}
						 	</ul>
						 	<p><img src="plansys/modules/help/img/13-2-5.png"> 
						<li>Setelah mengedit script, lalu tekan  CTRL + S untuk  menyimpannya.
						<li>Untuk melihat Preview template email ini, tekan tombol hijau Preview di pojok kanan atas.
						 	<p><img src="plansys/modules/help/img/13-2-7.png"> 
						<li>Maka akan membuka tab browser baru yang berisi informasi yang ada dalam variabel yang dipanggil di template email tersebut.
						 	<p><img src="plansys/modules/help/img/13-2-8.png"> 
						<li>Lalu templekan template ini pada Controller untuk menjalankannya. Sebagai contoh, ada Email Notifikasi ketika ada panambahan data divisi yang baru, email notifikasi ini dikirim ke email tertentu.
							<p>Berikut isi Script template Email Notifikasi :
						 	<p><img src="plansys/modules/help/img/13-2-9-i.png"> 
							<p>Template Email Notifikasi ini perlu perlu ditempelkan ke Controller Divisi, maka caranya adalah buka DivisiController.php yang ada di direktori  :
						sistem_aplikasi\app\controllers\DivisiController.php)
						 	<p><img src="plansys/modules/help/img/13-2-9-ii.png"> 
						<li>Lalu Edit script pada class Action yang sesuai, contoh ini menggunakan actionNew agar template Email Notifikasi akan dijalakan ketika ada data baru yang telah dibuat. Tambahkan script pada class actionNew :
						 	<p><img src="plansys/modules/help/img/13-2-10-i.png"> 
							<p>Detail script tambahan tersebut :
						 	<p><img src="plansys/modules/help/img/13-2-10-ii.png"> 
							<p>Simpan perubahan tersebut. Lalu tes dengan membuat data baru di Form Divisi.
						<li>Jika berhasil maka aka nada email notifikasi ke email address yang didefinisikan dalam Contoller Divisi tadi.
							<p><img src="plansys/modules/help/img/13-2-11.png"> 
					</ol>	 

					<h4 id="13.3">13.3. Update Email Template</h4><a href="#bab13"><i> back to top >></i></a>
					<p>Langkah-langkah untuk mengubah/memperbarui email template adalah sebagai berikut :
					<ol><li>Login sebagai Dev.
						<li>Pada menu Builder >> submenu Email Builder >> Pada bagian App, klik template email yang akan diEdit. Edit script template email sesuai kebutuhan.
						 	<p><img src="plansys/modules/help/img/13-3-2.png"> 
						<li>Setelah mengedit script, lalu tekan  CTRL + S untuk  menyimpannya.
					</ol>
					
					<h4 id="13.4">13.4. Delete Email Template</h4><a href="#bab13"><i> back to top >></i></a>
					<p>Langkah-langkah untuk menghapus email template adalah sebagai berikut :
					<ol><li>Pada halaman yang sama dengan halaman untuk Update email template.
						<li>Klik kanan pada email template yang akan dihapus >> pilih Delete Email.	
						 	<p><img src="plansys/modules/help/img/13-4-2.png"> 
						<li>Maka muncul pop up konfirmasi, ketikkan DELETE untuk mengkonfirmasi penghapusan >> klik OK.
							<p><img src="plansys/modules/help/img/13-2-3.png"> 						 
					</ol>
				</div>				
				',
            ),
        );
    }
}