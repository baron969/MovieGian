# Analisis Evolusi Perangkat Lunak

## 1. Kondisi Awal
Sebelum melakukan *refactoring*, saya menyadari bahwa kode awal yang saya tulis memiliki beberapa kelemahan struktural (sering disebut *Code Smells*), terutama pada pendekatan *Fat Controller*:
- **Pelanggaran *Single Responsibility Principle* (SRP):** File `MovieController` memikul terlalu banyak tanggung jawab. Selain mengurus alur HTTP, controller tersebut juga menangani logika bisnis yang kompleks (seperti manipulasi string dan penanganan upload file) serta aturan validasi data.
- **Logika Validasi yang Tercampur:** Aturan validasi input ditulis secara langsung di dalam method `store` dan `update`. Hal ini membuat fungsi tersebut menjadi sangat panjang dan sulit dibaca ulang.
- **Ketergantungan Kuat (*Tightly Coupled*) pada ORM:** Logika aplikasi masih berinteraksi langsung dengan database menggunakan model Eloquent (seperti `Movie::create()`) di dalam Controller maupun Service awal.
- **Kesulitan dalam Pengujian (*Hard to Test*):** Sangat sulit bagi saya untuk melakukan *Unit Testing* pada logika bisnis saja, karena logika tersebut tertanam bersamaan dengan alur HTTP Request dan koneksi database secara langsung.

## 2. Perubahan yang Di Lakukan
Untuk mengatasi masalah di atas, saya menerapkan arsitektur *Clean Layered Architecture* secara bertahap melalui beberapa teknik *refactoring*:
- **Penerapan *Service Layer* (`MovieService.php`)**
  - **Perubahan:** Memindahkan seluruh logika bisnis (seperti pemrosesan penyimpanan, pembaruan, penghapusan data, dan manajemen upload gambar) dari `MovieController` ke kelas baru bernama `MovieService`.
  - **Alasan:** Menerapkan prinsip *Separation of Concerns*. Controller kini hanya bertugas mengatur lalu lintas *request* dan *response*, sedangkan *Service* bertugas memikirkan "bagaimana" data diproses.
- **Pembuatan *Form Request Validation* (`StoreMovieRequest.php` & `UpdateMovieRequest.php`)**
  - **Perubahan:** Memindahkan semua aturan validasi dari dalam controller ke kelas Request yang terpisah.
  - **Alasan:** Hal ini membuat controller menjadi jauh lebih bersih. Framework akan secara otomatis mengeksekusi validasi ini sebelum masuk ke controller.
- **Implementasi *Repository Pattern* (`MovieRepository.php` & `MovieRepositoryInterface.php`)**
  - **Perubahan:** Membuat antarmuka (Interface) sebagai kontrak standar, lalu memindahkan semua query database (Eloquent) dari *Service* ke dalam *Repository*. Selanjutnya, saya melakukan *binding* di `AppServiceProvider.php`.
  - **Alasan:** Ini adalah bentuk penerapan *Dependency Inversion*. Memastikan bahwa lapisan *Service* hanya bergantung pada "kontrak" (abstraksi), bukan pada implementasi query database yang sesungguhnya.
- **Pembersihan Controller (*Thin Controller*)**
  - **Perubahan:** Method di `MovieController` (seperti `store`, `update`, `delete`) sekarang hanya berisi 2-3 baris kode yang memanggil *dependency* dari `MovieService`.
  - **Alasan:** Menjadikan kode sangat deskriptif dan ekspresif.

## 3. Dampak Refactoring
Setelah melakukan serangkaian *refactoring* di atas, saya merasakan dampak yang sangat positif pada kualitas perangkat lunak:
- **Kemudahan Pemeliharaan (*Maintainability*):** Sangat meningkat. Jika terjadi *bug* pada proses upload foto, pengembang tahu persis harus mencarinya di `MovieService`. Jika ada perbaikan query database, hanya perlu mengedit `MovieRepository`. Proses pelacakan masalah menjadi jauh lebih cepat.
- **Keterbacaan Kode (*Readability*):** Meningkat signifikan. Kode di dalam `MovieController` sekarang menjadi sangat ringkas (*Thin Controller*). Kode membaca seperti cerita yang logis: menerima data validasi -> meminta service memproses data -> mengembalikan respon, tanpa terdistraksi oleh ratusan baris kode teknis.
- **Kemudahan Pengembangan (*Scalability*):** Arsitektur sistem menjadi sangat modular. Jika suatu saat perlu membuat REST API untuk aplikasi *Mobile*, cukup membuat API Controller baru dan menyuntikkan (inject) `MovieService` yang sama. Logika bisnis dapat digunakan kembali 100% tanpa redundansi.

## 4. Potensi Pengembangan di Masa Depan
Dengan pondasi arsitektur yang sudah rapi dan terpisah, sistem kini sangat siap untuk menerima pola pengembangan tingkat lanjut di masa depan:
- **Penulisan Automated Testing (TDD):** Karena sudah menerapkan *Repository Pattern*, penulisan *Unit Test* untuk *Service Layer* kini dapat dilakukan dengan sangat mudah menggunakan metode *Mocking* tanpa perlu menyentuh database sama sekali.
- **Penerapan *Data Transfer Object* (DTO):** Ke depannya, data *array* yang dikirim dari Controller ke Service dapat dibungkus menggunakan objek DTO agar tipe datanya lebih ketat (*type-safe*) dan lebih aman.
- **Penggunaan *Events* dan *Listeners*:** Jika fitur bertambah kompleks (misalnya aplikasi perlu mengirim notifikasi setelah film ditambahkan), proses yang berat tersebut dapat dipindahkan menjadi tugas *asynchronous* menggunakan *Events* agar *loading* aplikasi pengguna tetap cepat.

# Structure Folder Setelah Refactoring
```text
app/
├── Http/
│   ├── Controllers/
│   │   └── MovieController.php
│   └── Requests/
│       ├── StoreMovieRequest.php
│       └── UpdateMovieRequest.php
├── Interfaces/
│   └── MovieRepositoryInterface.php
├── Repositories/
│   └── MovieRepository.php
└── Services/
    └── MovieService.php

resources/
└── views/
    ├── components/
    │   ├── alert.blade.php
    │   └── movie-card.blade.php
    ├── layout/
    │   └── template.blade.php
    ├── homepage.blade.php
    └── data-movies.blade.php
```

# Screenshot Perubahan
- **Landing Page**
- **Halaman Input Movie**
- **Halaman Data Movie**
- **Search Page**
- **Struktur Folder**
- **Aktifitas Pull Request dan Keterangan Commit (Merged ke Main)**
- **Gambar Activity Github (branch develop)**
