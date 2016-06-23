<?php

class HelpTutorialBab14 extends Form {
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
					<h3>Bab XIV. SERVICE MANAGEMENT </h3>
					<h4>14.1. Konsep Service Management</h4>
					<p>Service management merupakan suatu tools untuk mengelola background process untuk membantu berjalannya sistem informasi yang dikembangkan. 

					<h4>14.2. Create Service</h4>
					<p>Langkah-langkah untuk membuat Service baru adalah sebagai berikut :
					<ol><li>Login sebagai Dev
						<li>Pada meu Builder, pilih submenu Service Manager. 
					 		<p><img src="plansys/modules/help/img/14-2-2.png"> 
						<li>Pada halaman Service Manager, klik New Service.
					 		<p><img src="plansys/modules/help/img/14-2-3.png"> 
						<li>Akan muncul kotak New Servie, isikan nama service pada Service Name >> pilih Module (defultnya pilih App).
					 		<p><img src="plansys/modules/help/img/14-2-4.png"> 
						<li>Lalu pilih Command. Command dapat dipilih dari Command yang sudah tersimpan di sistem aplikasi atau menambah Command baru. 
					 		<p><img src="plansys/modules/help/img/14-2-5.png"> 
							<ul><li>Jika memilih Command yang sudah ada, maka pilihan Action dalam Command tersebut akan muncul di pilihan Action. 
							 		<p><img src="plansys/modules/help/img/14-2-5-i.png"> 
								<li>Jika ingin menambah Command baru, tekan tombol +Add New Command >> isi kolom Command dan Action.
					 				<p><img src="plansys/modules/help/img/14-2-5-ii.png"> 
							</ul>
						<li>Setelah mengisi Command dan Action, maka akan muncul beberapa pilihan pengaturan di sisi kanan.
					 		<p><img src="plansys/modules/help/img/14-2-6.png"> 
						<li>Pada run Schedule pilih jadwal yang sesui kebutuhan (misal Every X Minutes untuk menjalankan service 	setiap X menit).
					 		<p><img src="plansys/modules/help/img/14-2-7.png"> 
						<li>Isikan berapa nilai periode penjadwalan pada Period Run (misal 5 menit agar service berjalan setiap 5 menit sekali).
					 		<p><img src="plansys/modules/help/img/14-2-8.png"> 
						<li>Lalu pilih Run Instance, ada 2 pilihan yaitu Single Instance atau Parallel Instance.
					 		<p><img src="plansys/modules/help/img/14-2-9.png"> 
							<ul><li>Single Intance digunakan untuk mengizinkan service dapat berjalan secara berurutan dan tidak ada service yang sama berjalan dalam waktu yang sama (seri). Jika memilih Single Instance, maka akan muncul dua pilihan selanjutnya pada If instance is still running yaitu Do not run process dan Kill running instance and run process.  Sebagai contoh Service Email Reminder (misal ER 1) ini berjalan dan service Email Reminder selanjutnya (ER 2) mulai berjalan padahal  service ER 1 belum selesai maka eksekusi sistem aplikasi tergantung 2 pilihan :
									<p><img src="plansys/modules/help/img/14-2-9-i.png"> 	 
									<ul><li>Do not run process
											<p>Pilih ini jika mengharuskan ER 2 dapat bejalan setelah ER 1 selesai.
										<li>Kill running instance and run process
											<p>Pilih ini jika mengizinkan untuk menghentikan ER 1 dan akan mulai menjalankan ER 2.
									</ul>
								<li>Parallel Instance digunakan untuk mengizinkan service berjalan meskipun proses lain (dengan service yang sama) belum selesai atau sedang proses berjalan.
									<p><img src="plansys/modules/help/img/14-2-9-ii.png"> 	 
					 		</ul>

						<li>Lalu tekan Save Service
					 		<p><img src="plansys/modules/help/img/14-2-10.png"> 	 
						<li>Maka service akan tersimpan dan akan langsung dibuka dalam editor
					 		<p><img src="plansys/modules/help/img/14-2-11-i.png"> 	 
							<p>Sebagai contoh isi service untuk mengirimkan email yang isinya mengambil dari email template berikut :
							<p><img src="plansys/modules/help/img/14-2-11-ii.png"> 	 
							<p>Setelah selesai mengedit script dan simpan dengan menekan CTRL + S.

					<h4>14.3. Run & Stop Service</h4>
					<p>Buka Service Manager >> pilih service yang akan dijalankan atau dimatikan, klik Edit Code. Lalu akan membuka script service melalui editor. Untuk menjalankan (Run) atau menghentikan (Stop) service, caranya sebagai berikut :
					<ol><li>Tekan Run untuk menjalankan service.
						 	<p><img src="plansys/modules/help/img/14-3-1.png"> 	 
						<li>Tekan Stop untuk menghentikan service.
							<p><img src="plansys/modules/help/img/14-3-1.png"> 	 
					 </ol>

					<h4>14.4. Update Service</h4>
					<p>Langkah-langkah untuk memperbarui/mengupdate service adalah sebagai berikut :
					<lo><li>Buka Service Manager
						<li>Pilih service yang akan diUpdate >>klik Edit Code
						<li>Jika service sedang berjalan, matikan service terlebih dahulu dengan menekan tombol Stop.
					 		<p><img src="plansys/modules/help/img/14-4-3.png"> 	 
						<li>Lalu edit script service melalui editor yang disediakan.
						<li>Tekan CTRL + S untuk menyimpan perubahan script.
						<li>Jalankan kembali service dengan menekan tombol Run.
					 		<p><img src="plansys/modules/help/img/14-4-3.png"> 	 
					</ol>
					
					<h4>14.5. Delete Service</h4>
					<p>Untuk menghapus Service yang tidak dipakai adalah sebagai berikut :
					<ol><li>Buka Service Manager >> pilih Service yang akan dihapus, klik Edit Code.
					 		<p><img src="plansys/modules/help/img/14-5-1.png"> 	 
						<li>Setelah service terbuka, klik Edit Service di sebelah pojok kanan atas.
					 		<p><img src="plansys/modules/help/img/14-5-2.png"> 	 
						<li>Maka akan muncul kotak Edit Service, klik tombol merah Delete This Service
					 		<p><img src="plansys/modules/help/img/14-5-3.png"> 	 
						<li>Akan muncul pop up konfirmasi delete service, ketikkan DELETE >> klik OK.
							<p><img src="plansys/modules/help/img/14-5-4.png"> 	 
					 </ol>
				<div>				
				',
            ),
        );
    }
}