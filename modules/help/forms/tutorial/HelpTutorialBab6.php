<?php

class HelpTutorialBab6 extends Form {
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
					<h3 id="bab6">Bab VI. VIEW MANAGEMENT</h3>
				</div>
				<div class=\"sub_daftar_isi\">		
					<table>
						<td style="width:20%">
							<a href="#6.1">6.1. Konsep View</a><br>
							<a href="#6.2">6.2. Create View</a><br>	
							<a href="#6.3">6.3. Delete View</a><br>
							<a href="#6.4">6.4. Customize Index</a><br>
							<a href="#6.4.1">6.4.1. Column Width</a><br>
							<a href="#6.4.2">6.4.2. Change & Delete Button</a><br>
							<a href="#6.4.3">6.4.3. Agregat (Count)</a>	<br>
							<a href="#6.4.4">6.4.4. Show Data Source Result</a><br>
							<a href="#6.4.5">6.4.5. Custom GridView</a><br>
							<a href="#6.5">6.5. Toolbar components</a><br>	
							<a href="#6.5.1">6.5.1. Layout</a><br>	
							<a href="#6.5.1.1">6.5.1.1. Ace Editor</a><br>							
						</td>
						<td style="width:20%">						
							<a href="#6.5.1.2">6.5.1.2. Action Bar</a><br>	
							<a href="#6.5.1.3">6.5.1.3. Columns</a><br>	
							<a href="#6.5.1.4">6.5.1.4. Example Field</a><br>
							<a href="#6.5.1.5">6.5.1.5. Popup Window</a><br>	
							<a href="#6.5.1.6">6.5.1.6. Section Header</a><br>
							<a href="#6.5.1.7">6.5.1.7. Text / HTML</a><br>	
							<a href="#6.5.2">6.5.2. Charts</a><br>	
							<a href="#6.5.2.1">6.5.2.1. Area Chart</a><br>							
							<a href="#6.5.2.2">6.5.2.2. Bar Chart</a><br>	
							<a href="#6.5.2.3">6.5.2.3. Chart Group</a><br>	
							<a href="#6.5.2.4">6.5.2.4. Line Chart</a><br>	
							<a href="#6.5.2.5">6.5.2.5. Pie Chart</a><br>	
						</td>
						<td style="width:20%">							
							<a href="#6.5.3">6.5.3. User Interface</a><br>	
							<a href="#6.5.3.1">6.5.3.1. Checkbox List</a><br>	
							<a href="#6.5.3.2">6.5.3.2. Color Picker</a><br>
							<a href="#6.5.3.3">6.5.3.3. Date Time Picker</a><br>	
							<a href="#6.5.3.4">6.5.3.4. Drop Down List</a><br>	
							<a href="#">6.5.3.5. Hidden Field</a><br>							
							<a href="#">6.5.3.6. Icon Picker</a><br>	
							<a href="#">6.5.3.7. Label Field</a><br>	
							<a href="#">6.5.3.8. Link Button</a><br>	
							<a href="#">6.5.3.9. Radio Button List</a><br>	
							<a href="#">6.5.3.10. Relation Field</a><br>	
							<a href="#">6.5.3.11. Repo Browser</a><br>	
						</td>
						<td style="width:20%">							
							<a href="#">6.5.3.12. Submit</a><br>	
							<a href="#">6.5.3.13. Tag Field</a><br>	
							<a href="#">6.5.3.14. Text Area</a><br>	
							<a href="#">6.5.3.15. Text Field</a><br>
							<a href="#">6.5.3.16. Toggle Switch</a><br>	
							<a href="#">6.5.3.17. Upload File</a><br>	
							<a href="#6.5.4">6.5.4. Data & Tables</a><br>	
							<a href="#6.5.4.1">6.5.4.1. Data Filter</a><br>	
							<a href="#6.5.4.2">6.5.4.2. Data Source</a><br>	
							<a href="#6.5.4.3">6.5.4.3. Expression Field</a><br>	
							<a href="#6.5.4.4">6.5.4.4. GridView</a><br>	
							<a href="#6.5.4.5">6.5.4.5. Key Value Grid</a><br>	
						</td>											
						<td style="width:20%">						
							<a href="#6.5.4.6">6.5.4.6. List View</a><br>	
							<a href="#6.5.4.7">6.5.4.7. Sql Criteria</a><br>														
							<a href="#6.5.4.8">6.5.4.8. Sub Form</a><br>	
							<a href="#6.6">6.6. Dashboard</a><br>	
							<a href="#6.6.1">6.6.1. Create Dashboard Page</a><br>	
							<a href="#6.7">6.7. Customize Login Form</a><br>
						</td>					
					</table>	
				</div>
				<div class=\"isi\">
					<hr>				
					<h4 id="6.1">6.1. Konsep View</h4><a href="#bab6"><i> back to top >></i></a>
					<p>View dalam Plansys  disajikan dalam 2 bentuk halaman yaitu Form dan Index. Halaman Form digunakan untuk menambahkan data (Create) dan halaman View digunakan untuk menampilkan data (Read) dari database. Form ini menampilkan field isian informasi yang didapatkan melalui Model. Form merupakan class PHP yang mengextend dari sebuah Model, maka kolom-kolom yang ada pada Model juga akan ada pada View (Form dan Index).
					<h4 id="6.2">6.2. Create View</h4><a href="#bab6"><i> back to top >></i></a>
					<p>Dengan menggunakan Plansys, View  (Index dan Form) dan Controller dapat di generate otomatis dari model yang dipilih dengan CRUD Generator. Akan tetapi jika ada kebutuhan untuk membuat form tambahan dapat dibuatkan form tambahan lagi. Sebelum membuat View (Form/Index) secara manual, Model dan Controllernya juga harus sudah dibuat terlebih dahulu.
					<h5>(a). Menggunakan Module</h5>
					<p>Langkah-langkah untuk membuat View tanpa menggunakan CRUD generator adalah sebagai berikut :
					<h6>a.i. Membuat Form</h6>
					<ol><li>Pilih menu Builder, pilih submenu  Form Builder.
						<li>View (Form dan Index) harus berada dalam satu folder yang sama. Maka harus dibuatkan folder dahulu, langkahnya adalah klik kanan pada modul (misal sdm)>> pilih New Folder.
						<p><img src="plansys/modules/help/img/6-2-a-i-2.png">
						<li>Akan muncul pop up, isikan nama folder >> Klik OK. Nama folder tersebut disesuaikan dengan nama tabel dalama database dan nama tersebut yang akan digunakan dalam pemanggilan URL. Misalnya, nama foldernya adalah keahlian maka default link yang digunakan adalah http://localhost/coba/index.php?r=keahlian
						<p><img src="plansys/modules/help/img/6-2-a-i-3.png">
						<li>Pada submenu Forms, folder baru yang telah dibuat akan tampil tapi masih kosong.
						<p><img src="plansys/modules/help/img/6-2-a-i-4.png">
						<li>Untuk membuat Form, klik kanan pada folder (keahlian) >> pilih New Form.
						<p><img src="plansys/modules/help/img/6-2-a-i-5.png">
						<li>Akan muncul halaman Create New Form, isikan Form pada Form Name >> pada Base Class, pilih Form >> Klik Save. Jika ingin membuat View Index, lakukan langkah (5) dan (6) lagi, bedanya pada langkah (6) isikan KeahlianIndex pada Form Name.
						<p><img src="plansys/modules/help/img/6-2-a-i-6.png">
						<li>Jika sudah berhasil, pada folder tersebut akan berisi 2 file (SdmKeahlianForm) dan (SdmKeahlianIndex).
						<p><img src="plansys/modules/help/img/6-2-a-i-7.png">
						<li>Langkah selanjutnya adalah mengatur desain layout View (Form ataupun Index), caranya langsung klik file yang akan diEdit (misal: SdmKeahlianForm).
						<p><img src="plansys/modules/help/img/6-2-a-i-8.png">
							<p>Pada halaman tool Plansys, tampilan terbagi menjadi 3 bagian :
							<ul><li>Bagian kiri	: Daftar folde beserta isinya	
								<li>Bagian tengah	: Tampilan layout View
								<li>Bagian kanan	: Toolbar dan Properties
							</ul>
						<li>Drag and Drop item yang dibutuhkan dari Toolbar. Toolbar terdiri 4 kategori yaitu Layout, Charts, User Interface, dan Data & Tables.
						<p><img src="plansys/modules/help/img/6-2-a-i-9.png">
						<li>Tambahkan Action Bar agar data yang diisikan dalam form dapat dikelola ke dalam database.
						<p><img src="plansys/modules/help/img/6-2-a-i-10.png">
						<li>Pastikan Field Name yang dipilih, kolom/field yang akan ditambahakan, juga harus ada di tabel dalam database. Agar ketika dalam field name muncul pilihan field/kolom yang tersedia dalam tabel tersebut.
						<p><img src="plansys/modules/help/img/6-2-a-i-11.png">
						<li>Tambahkan field-field yang lain dan atur Properties dari masing-masing field-field tersebut. 
					</ol>
					<h6>a.ii. Membuat Index</h6><a href="#bab6"><i> back to top >></i></a>
					<ol><li>Buka menu Form Builder >> pilih module >> pilih folder (misal: keahlian) >> pilih index (misal: SdmKeahlianIndex).
						<p><img src="plansys/modules/help/img/6-2-a-ii-1.png">
						<li>Tambahkan Action Bar dari Properties ke halaman index.
						<p><img src="plansys/modules/help/img/6-2-a-ii-2.png">
						<li>Secara default Action Bar berisi tombol Save maka perlu diganti menjadi tombol Tambah/New. Carnya, klik pada button Save, lalu akan terbukan Properties tombol tersebut. 
						<li>Ubah properties terutama pada bagian Label dan Options. Pada Options, isikan link tujuan button tersebut.
						<p><img src="plansys/modules/help/img/6-2-a-ii-4.png">
						<li>Tambahkan Data Source, caranya dengan drag and drop Data Source dari Toolbar ke halaman index.
						<p><img src="plansys/modules/help/img/6-2-a-ii-5.png">
						<li>Pada Properties Data Source, masukkan script query sql untuk mendapatkan data yang diinginkan.
						<p><img src="plansys/modules/help/img/6-2-a-ii-6.png">
						<li>Tambahkan GridView pada halaman index, dengan cara drag and drop dari Toolbar.
						<li>Pada Properties GridView, pilih data source yang digunakan pada Data Source Name.
						<p><img src="plansys/modules/help/img/6-2-a-ii-8.png">
						<li>Jika sudah dipilih maka akan muncul tombol Generate Columns.
						<p><img src="plansys/modules/help/img/6-2-a-ii-9.png">
						<li>Sebelum men-generate columns, pastikan tabel tersebut memiliki data meskipun hanya satu row. Jika tabel belum berisi data maka akan muncul pesan warning seperti berikut :
						<p><img src="plansys/modules/help/img/6-2-a-ii-10.png">
						<li>Jika sudah men-generate columns, maka akan muncul kolom-kolom tabel di bawah tombol Generate Columns.
						<p><img src="plansys/modules/help/img/6-2-a-ii-11.png">
						<li>Klik pada kolom, maka akan muncul beberapa pengaturan kolom tersebut.
						<p><img src="plansys/modules/help/img/6-2-a-ii-12.png">
					</ol>	
					<h5>(b). Tanpa Menggunakan Module</h5><a href="#bab6"><i> back to top >></i></a>
					<p>Langkah-langkah untuk membuat View tanpa menggunakan CRUD generator adalah sebagai berikut :
					<h6>b.i. Membuat Form</h6>
					<ol><li>Pilih menu Builder, pilih submenu  Form Builder.
						<p><img src="plansys/modules/help/img/6-2-b-i-1.png">
						<li>View (Form dan Index) harus berada dalam satu folder yang sama. Maka harus dibuatkan folder dahulu, langkahnya adalah klik kanan pada app >> pilih New Folder.
						<p><img src="plansys/modules/help/img/6-2-b-i-2.png">
						<li>Akan muncul pop up, isikan nama folder >> Klik OK. Nama folder tersebut disesuaikan dengan nama tabel dalama database dan nama tersebut yang akan digunakan dalam pemanggilan URL. Misalnya, nama foldernya adalah keahlian maka default link yang digunakan adalah http://localhost/coba/index.php?r=jabatan
						<p><img src="plansys/modules/help/img/6-2-b-i-3.png">
						<li>Pada submenu Forms, folder baru yang telah dibuat akan tampil tapi masih kosong.
						<p><img src="plansys/modules/help/img/6-2-b-i-4.png">
						<li>Untuk membuat Form, klik kanan pada folder >> pilih New Form.
						<p><img src="plansys/modules/help/img/6-2-b-i-5.png">
						<li>Akan muncul halaman Create New Form, isikan Form pada Form Name >> pada Base Class, pilih Form >> Klik Save.						
						<li>Jika ingin membuat View Index, lakukan langkah (5) dan (6) lagi, bedanya pada langkah (6) isikan Index pada Form Name.
						<p><img src="plansys/modules/help/img/6-2-b-i-6.png">						
						<li>Jika sudah berhasil, pada folder tersebut akn berisi 2 file (AppJabatanForm) dan (AppJabatanIndex).
						<p><img src="plansys/modules/help/img/6-2-b-i-7.png">
						<li>Langkah selanjutnya adalah mengatur desain layout View (Form ataupun Index), caranya langsung klik file yang akan diEdit (misal AppJabatanForm).
						<p><img src="plansys/modules/help/img/6-2-b-i-8.png">
							<p>Pada halaman tool Plansys, tampilan terbagi menjadi 3 bagian :
							<ul><li>Bagian kiri	: Daftar folde beserta isinya
								<li>Bagian tengah	: Tampilan layout View
								<li>Bagian kanan	: Toolbar dan Properties
							</ul>
						<li>Drag and Drop item yang dibutuhkan dari Toolbar. Toolbar terdiri 4 kategori yaitu Layout, Charts, User Interface, dan Data & Tables.
						<li>Tambahkan Action Bar agar data yang diisikan dalam form dapat dikelola ke dalam database.
						<li>Pastikan Field Name yang dipilih, kolom/field yang akan ditambahakan, juga harus ada di tabel dalam database. Agar ketika dalam field name muncul pilihan field/kolom yang tersedia dalam tabel tersebut.
						<li>Tambahkan field-field yang lain dan atur Properties dari masing-masing field-field tersebut.
					</ol>
					<h6>b.ii. Membuat Index</h6>
					<ol><li>Buka menu Form Builder >> pilih app >> pilih folder (misal: jabatan) >> pilih index (misal: AppJabatanIndex).
						<p><img src="plansys/modules/help/img/6-2-b-ii-1.png">
						<li>Tambahkan Action Bar dari Properties ke halaman index.
						<p><img src="plansys/modules/help/img/6-2-b-ii-2.png">
						<li>Secara default Action Bar berisi tombol Save maka perlu diganti menjadi tombol Tambah/New. Carnya, klik pada button Save, lalu akan terbukan Properties tombol tersebut. 
						<li>Ubah properties terutama pada bagian Label dan Options. Pada Options, isikan link tujuan button tersebut.
						<p><img src="plansys/modules/help/img/6-2-b-ii-4.png">
						<li>Tambahkan Data Source, caranya dengan drag and drop Data Source dari Toolbar ke halaman index.
						<p><img src="plansys/modules/help/img/6-2-b-ii-5.png">
						<li>Pada Properties Data Source, masukkan script query sql untuk mendapatkan data yang diinginkan.
						<li>Tambahkan GridView pada halaman index, dengan cara drag and drop dari Toolbar.
						<li>Pada Properties GridView, pilih data source yang digunakan pada Data Source Name.
						<p><img src="plansys/modules/help/img/6-2-b-ii-8.png">
						<li>Jika sudah dipilih maka akan muncul tombol Generate Columns.
						<p><img src="plansys/modules/help/img/6-2-b-ii-9.png">
						<li>Sebelum men-generate columns, pastikan tabel tersebut memiliki data meskipun hanya satu row. Jika tabel belum berisi data maka akan muncul pesan warning seperti berikut :
						<p><img src="plansys/modules/help/img/6-2-b-ii-10.png">
						<li>Jika sudah men-generate columns, maka akan muncul kolom-kolom tabel di bawah tombol Generate Columns.
						<p><img src="plansys/modules/help/img/6-2-b-ii-11.png">
						<li>Klik pada kolom, maka akan muncul beberapa pengaturan kolom tersebut. 
						<p><img src="plansys/modules/help/img/6-2-b-ii-12.png">
					</ol>
					
					<h4 id="6.3">6.3. Delete View</h4><a href="#bab6"><i> back to top >></i></a>
					<h5>(a) Menggunakan Module</h5>
					<p>Langkah untuk menghapus (Delete) View, baik Form maupun View caranya adalah sebagai berikut :
					<ol><li>Buka Form Builder >> pilih modul >> pilih folder.
						<li>Klik kanan pada file (Form ataupun View) yang akan dihapus.
						<p><img src="plansys/modules/help/img/6-3-a-2.png">
						<li>Pilih Delete.
						<li>Akan muncul kotak konfirmasi penghapusan.
						<li>Klik OK.
					</ol>
					<h5>(b) Tanpa Menggunakan Module</h5>
					<p>Langkah untuk menghapus (Delete) View, baik Form maupun View caranya adalah sebagai berikut :
					<ol><li>Buka Form Builder >> pilih app >> pilih folder.
						<li>Klik kanan pada file (Form ataupun View) yang akan dihapus.
						<p><img src="plansys/modules/help/img/6-3-b-2.png">
						<li>Pilih Delete.
						<li>Akan muncul kotak konfirmasi penghapusan.
						<li>Klik OK.
					</ol>
					
					<h4 id="6.4">6.4. Customize Index</h4><a href="#bab6"><i> back to top >></i></a>
					<h5 id="6.4.1>6.4.1. Column Width</h5><a href="#bab6"><i> back to top >></i></a>
					<p>Langkah-langkah untuk mengatur lebar kolom pada halaman index adalah sebagai berikut :
					<ol><li>Pada Form Builder, buka file Index yang akan diatur lebar kolomnya.
						<li>Klik GridView (jika halaman index menggunakan jenis GridView).
						<li>Pada bagian Properties, klik kolom yang akan diatur.
						<li>Buka bagian Options.
						<li>Ketikkan width di sebelah kiri dan nilainya dalam satuan pixel di sebelah kanan (misal : 200 untuk mengatur lebar kolom 200 px).
						<p><img src="plansys/modules/help/img/6-1-4-5.png">
					</ol>
 
					<h5 id="6.4.2">6.4.2. Change & Delete Button</h5>
					<p>Langkah-langkah untuk menambahkan icon link Change (Update) dan Delete seperti ini  untuk mengelola data yang dimaksud adalah sebagai berikut.
					<ol><li>Pada Form Builder, buka file Index yang akan diatur lebar kolomnya.
						<li>Klik GridView (jika halaman index menggunakan jenis GridView).
						<li>Pada bagian Properties, tambahkan kolom baru.
						<li>Klik tombol +Add untuk menambah kolom.
						<p><img src="plansys/modules/help/img/6-4-2-4.png">
						<li>Klik kolom kosong baru teresbut, isikan script pada Options seperti berikut :
							<p>Tombol Edit
							<ul><li>mode	: edit-button
								<li>editUrl	: nama_module/nama_controller/update&id={{row.id}}	
							</ul>
							<p>Tombol Hapus
							<ul><li>mode	: del-button
								<li>delUrl	: nama_module/nama_controller/delete&id={{row.id}}	
							</ul>
							<p><img src="plansys/modules/help/img/6-4-2-5.png">
					</ol>
					
					<h5 id="6.4.3">6.4.3. Agregat (Count)</h5>
					<p>Langkah-langkah untuk membuat agregat (count) pada halaman index adalah sebagai berikut :
					<ol><li>Buka Form Builder >> pilih file index yang akan diberikan agregat.
						<li>Klik pada DataSource.
						<li>Pada bagian Properties, tambahkan Grouping dengan mengklik tombol Edit pada Grouping.
							<p><img src="plansys/modules/help/img/6-4-3-3.png">						
						<li>Lalu klik tombol +Add. 
							<p><img src="plansys/modules/help/img/6-4-3-4.png">
						<li>Maka akan muncul properties Agregat, klik tombol +Add pada bagian bawah Agregat.
							<p><img src="plansys/modules/help/img/6-4-3-5.png">
						<li>Pilih kolom yang ditampilkan pada index yang akan menginformasikan judul agregat dan  nilai agregat. Sebagai contoh, halaman index menampilkan 2 kolom (judul dan isi). Agregat akan diatur menampilkna judul agregat pada kolom judul dan nilai count data akan ditampilkan pada kolom isi.
							<p><img src="plansys/modules/help/img/6-4-3-6.png">
						<li>Maka perbandingan tampilan index tersebut adalah sebagai berikut :
							<p>Tanpa Agregat
							<p><img src="plansys/modules/help/img/6-4-3-7-i.png">
							<p>Dengan Agregat (Count)
							<p><img src="plansys/modules/help/img/6-4-3-7-ii.png">
						<li>Dapat ditambahkan grouping agar lebih spesifik tampilan indexnya, caranya dengan klik lagi tombol +Add pada Grouping >> pIlih kolom yang menjadi acuan grouping (misal kategori_id).
							<p><img src="plansys/modules/help/img/6-4-3-8.png">
						<li>Maka tampilan index tersebut akan menjadi seperti di bawah ini : 						 
							<p><img src="plansys/modules/help/img/6-4-3-9.png">
					</ol>
					
					<h5 id="6.4.4">6.4.4. Show Data Source Result</h5><a href="#bab6"><i> back to top >></i></a>
					<p>Untuk menampilkan isi data dari DataSource dalam bentuk teks tanpa tabel maupun datagrid, langkah-langkahnya sebagai berikut :
					<ol><li>Buka Form Builder
						<li>Buka halaman index yang akan diatur.
						<li>Tambahkan komponen Text HTML.
						<li>Isikan script seperti berikut :
							<p><img src="plansys/modules/help/img/6-4-4-4.png">
						<li>Sebagai contoh, index akan menampilkan isi kolom judul dan kategori dari tabel blog.
							<p><img src="plansys/modules/help/img/6-4-4-5-i.png">
							<p>Maka tampilan index tersebut akan seperti berikut :
							<p><img src="plansys/modules/help/img/6-4-4-5-ii.png">					
					</ol>
					
					<h5 id="6.4.5">6.4.5. Custom GridView</h5><a href="#bab6"><i> back to top >></i></a>
					<p>Halaman Form dan View dapat ditampilkan dalam satu halaman. Kebutuhan ini biasanya digunakan untuk form isian dengan kolom yang banyak, seperti form isian nilai siswa. Langkah-langkah untuk membuatnya adalah sebagai berikut :
					<ol><li>Pastikan Modelnya sudah dibuat.
						<li>Bukan menu Form Builder.
						<li>Klik kanan pada app atau module >> klik New CRUD.
						<li>Pada kotak Generate CRUD, pilih model pada Base Model >> pada Master Daa, pilih Yes agar form tersebut juga akan berfungsi sebagai master data >> Klik Next Step.
							<p><img src="plansys/modules/help/img/6-4-5-4.png">
						<li>Pada halaman selanjutnya, akan ditampilkan 3 type yang akan di-generate (folder, master, controller). File dengan type master merupakan form yang dapat menerima inputan data dan menampilkan isi data tersebut pada halaman yang sama >> Klik Generate CRUD.
							<p><img src="plansys/modules/help/img/6-4-5-5.png">
						<li>Proses generate selesai ditandai dengan status OK pada masing-masing type.
							<p><img src="plansys/modules/help/img/6-4-5-6.png">
						<li>Halaman master form tersebut akan tampil sebagai berikut :
							<p><img src="plansys/modules/help/img/6-4-5-7.png">
						<li>Untuk menambahkan row baru, klik tombol +Tambah Nilai Siswa >> Maka akan muncul satu row kosong baru, isikan data.
							<p><img src="plansys/modules/help/img/6-4-5-8.png">
						<li>Untuk menyimpan data, klik tombol hijau Simpan.
							<p><img src="plansys/modules/help/img/6-4-5-9.png">
					</ol>
					<h4 id="6.5">6.5. Toolbar components</h4><a href="#bab6"><i> back to top >></i></a>	
					<h5 id="6.5.1">6.5.1. Layout</h5><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.1.1">6.5.1.1. Ace Editor</h6><a href="#bab6"><i> back to top >></i></a>											
					<h6 id="6.5.1.2">6.5.1.2. Action Bar</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.1.3">6.5.1.3. Columns</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.1.4">6.5.1.4. Example Field</h6><a href="#bab6"><i> back to top >></i></a>
					<h6 id="6.5.1.5">6.5.1.5. Popup Window</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.1.6">6.5.1.6. Section Header</h6><a href="#bab6"><i> back to top >></i></a>
					<h6 id="6.5.1.7">6.5.1.7. Text / HTML</h6><a href="#bab6"><i> back to top >></i></a>	
					<h5 id="6.5.2">6.5.2. Charts</h5><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.2.1">6.5.2.1. Area Chart</h6><a href="#bab6"><i> back to top >></i></a>							
					<h6 id="6.5.2.2">6.5.2.2. Bar Chart</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.2.3">6.5.2.3. Chart Group</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.2.4">6.5.2.4. Line Chart</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.2.5">6.5.2.5. Pie Chart</h6><a href="#bab6"><i> back to top >></i></a>							
					<h5 id="6.5.3">6.5.3. User Interface</h5><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.3.1">6.5.3.1. Checkbox List</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.3.2">6.5.3.2. Color Picker</h6><a href="#bab6"><i> back to top >></i></a>
					<h6 id="6.5.3.3">6.5.3.3. Date Time Picker</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.3.4">6.5.3.4. Drop Down List</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.5. Hidden Field</h6><a href="#bab6"><i> back to top >></i></a>							
					<h6 id="">6.5.3.6. Icon Picker</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.7. Label Field</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.8. Link Button</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.9. Radio Button List</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.10. Relation Field</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.11. Repo Browser</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.12. Submit</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.13. Tag Field</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.14. Text Area</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.15. Text Field</h6><a href="#bab6"><i> back to top >></i></a>
					<h6 id="">6.5.3.16. Toggle Switch</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="">6.5.3.17. Upload File</h6><a href="#bab6"><i> back to top >></i></a>	
					<h3 id="6.5.4">6.5.4. Data & Tables</h5><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.4.1">6.5.4.1. Data Filter</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.4.2">6.5.4.2. Data Source</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.4.3">6.5.4.3. Expression Field</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.4.4">6.5.4.4. GridView</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.4.5">6.5.4.5. Key Value Grid</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.4.6">6.5.4.6. List View</h6><a href="#bab6"><i> back to top >></i></a>	
					<h6 id="6.5.4.7">6.5.4.7. Sql Criteria</h6><a href="#bab6"><i> back to top >></i></a>														
					<h6 id="6.5.4.8">6.5.4.8. Sub Form</h6><a href="#bab6"><i> back to top >></i></a>	
					<h4 id="6.6">6.6. Dashboard</h4><a href="#bab6"><i> back to top >></i></a>	
					<h5 id="6.6.1">6.6.1. Create Dashboard Page</h5><a href="#bab6"><i> back to top >></i></a>	
					<h4 id="6.7">6.7. Customize Login Form</h4><a href="#bab6"><i> back to top >></i></a>
					
				</div>
				
				',
            ),
        );
    }

}