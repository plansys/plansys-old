# Plansys UI - React Version #

Versi React ini dibuat untuk mengatasi permasalahan pada implementasi angular
pada form builder yaitu masalah performance dan kompleksitas implementasi.

UI Library *hanya* digunakan untuk menyusun tampilan di *Page* (Bukan 
Form). Untuk menampilkan Page tertentu kita dapat mengunjungi url 
`index.php?r=Page/registration.counter`

Sebuah Page dapat menjadi *Master Page* yang dapat dijadikan template 
oleh Page lain. Page juga dapat di-insert kedalam Page lain sehingga menjadi 
SubPage. SubPage tersebut dapat di loop sehingga menjadi list.

Berikut ini adalah milestone untuk pengerjaan ini:

- JS Dynamic Component Loading                    [x] 
- PHP Page Structure                              [ ]
- PHP Page Renderer                               [ ]
- Page Builder                                    [ ]

## Memulai pengerjaan ##

Untuk memulai pengerjaan, kamu minimal harus menginstall:

- apt install nodejs
- npm install -g yarn
- yarn

setelah itu kamu bisa mengeksekusi perintah berikut
di folder /plansys/components/react/compiler:

- yarn dev: untuk memulai development server di p.plansys.co:8080
- yarn build: untuk menggabungkan dan meminify source code js 
              hasil minify ada di /plansys/components/react/ui