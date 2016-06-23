<?php

class HelpTutorialBab4 extends Form {
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
					<h3>Bab IV. MODEL MANAGEMENT</h3>
					<h4>4.1. Konsep Model</h4>
					<p>Model merupakan representative dari Reational Database yang terbentuk dari relasi antar tabel (termasuk Primary Key, Foreign Key, dan lainnya). Model berisi informasi kolom-kolom dan tiap kolom dapat diberikan rule. Model terdiri dari beberapa bagian sebagai berikut : 
					<table>
						<tr><td>~</td><td>Rule</td>
							<td>Rule merupakan aturan yang akan diterapkan. Rule ini dapat diberikan di masing-masing kolom.</td>
						</tr>
						<tr><td>~</td><td>Relation</td>
							<td>Relation merupakan hubungan yang dibentuk untuk menghubungkan antar tabel.</td>
						</tr>					
						<tr><td>~</td><td>Field</td>
							<td>ield-field yang ada mengambil dari kolom-kolom dalam tabel yang direpresentative kan oleh Model.</td>
						</tr>
						<tr><td>~</td><td>Property</td>
							<td>Property dapat digunakan untuk membuat kolom maya yang tidak ada dalam database.</td>
						</tr>
						<tr><td>~</td><td>Method</td>
							<td>Method merupakan fungsi PHP, salah satu bentuk method adalah fungsi Validasi.</td>
						</tr>
						<tr><td>~</td><td style="width:120px">(SQL)Structure Query Language</td>
							<td>Structure Query Language merupakan struktur bahasa untuk memanggil/mendapatkan informasi dari database, yang didapatkan dari satu atau beberapa tabel yang memiliki relasi (hubungan).</td>
						</tr>
					</table>

					<h4>4.2.	Create Model</h4>
					<p>Setelah login dengan username ‘dev’ yang memiliki akses sebagai developer, kita harus membuat Model sebelum View/Form dan Controller. Langkah-langkahnya sebagai berikut :
					<ol>
						<li>Pada halaman Plansys, pilih menu Builder, pilih submenu Model Builder.
						<li>Lalu akan masuk ke halaman Model, klik kanan pada App >> klik New Model
						<li>Akan muncul pop up Generate New Model, isikan nama Model pada Model Name. Pastikan nama tabel pada Table Name sudah sesuai. Sedangkan pada Soft Delete terdapat pilihan Yes/No, pilih Yes jika menginginkan ketika data dalam tabel tersebut dihapus maka akan terhapus juga data tersebut dalam database. Lalu Klik Save. 
						<p><img src="plansys/modules/help/img/4-2-3.png">
						<li>Maka file Model akan dibuatkan Plansys secara otomatis.
						<p><img src="plansys/modules/help/img/4-2-4.png">
					</ol>

					
					<h4>4.3.	Update Model</h4>
					<p>Cara memperbarui (update) Model dapat dilakukan dengan 2 cara yaitu :
					<ul><li>Jika ingin melakukan update untuk beberapa data, dapat dilakukan dengan cara mengedit secara manual melalui editor yang disediakan.
						<li>Jika ingin melakukan memperbarui seluruh data dalam Model tersebut, dapat dilakukan dengan cara membuat baru Model yang sama dan sistem akan me-replace Model yang lama tersebut. Pilihan ini juga berlaku ketika ada perubahan kolom dalam tabel database berupa penambahan relasi.
					</ul>
					<p>Langkah untuk mengubah Model secara manual adalah sebagai berikut :
					<ol><li>Buka Model dari menu Model >> App >> Nama Model yang akan diubah, maka terbuka Model tersebut dalam Editor dan apa langsung di edit dalam editor tersebut.
						<p><img src="plansys/modules/help/img/4-3-1.png">
						<li>Model ini digenerate langsung dari table yang telah dibuat di database. Tekan Ctrl+S untuk menyimpan perubahan/melakukan update. 
					</ol>
					
					<h4>4.4.	Delete Model</h4>
					<p>Langkah untuk menghapus (Delete) Model yang tidak digunakan sebagai berikut :
					<ol><li>Masuk ke menu Builder >> sub menu Model
						<li>Klik App >> Klik kanan pada model yang ingin dihapus >> pilih Delete Model.
						<li>Maka akan muncul kotak konfirmasi penghapusan Model, ketikkan DELETE
						<p><img src="plansys/modules/help/img/4-4-3.png">
						<li>Lalu klik OK untuk menghapus Model tersebut. 
					</ol>

					
				</div>
				
				',
            ),
        );
    }

}