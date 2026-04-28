# Laporan UTS Praktek Konstruksi dan Evolusi Perangkat Lunak 2026

## 1. Deskripsi Singkat Aplikasi
Aplikasi ini adalah sistem database Movie berbasis Laravel. Sistem ini memungkinkan pengguna untuk melihat daftar film, melihat detail film, menambah data film beserta foto sampul, mengedit data film, dan menghapusnya.

## 2. Penjelasan Hasil Refactoring (Bagian A)
Berikut adalah refactoring yang dilakukan pada project:
1. **Service Layer**: Logika bisnis untuk mengunggah gambar dan memproses data film telah dipindahkan dari `MovieController` ke dalam `MovieService`. Controller kini menjadi lebih bersih dan hanya bertugas untuk menerima request, memanggil service, dan mengembalikan response.
2. **Repository Pattern**: Semua operasi database (Eloquent ORM) dipisahkan ke dalam `MovieRepository` yang mengimplementasikan `MovieRepositoryInterface`. Ini membuat Service Layer tidak bergantung langsung pada Model, melainkan melalui interface repository.
3. **Perbaikan Struktur Folder**: Dibuat direktori baru `app/Interfaces`, `app/Repositories`, dan `app/Services` untuk memisahkan domain secara logis.
4. **Refactoring View (Blade)**: Tampilan pada `homepage.blade.php` telah dipisahkan ke dalam partial component `movie-card.blade.php`. Selain itu, komponen pesan sukses (`alert.blade.php`) juga dipisahkan agar dapat digunakan kembali (reusable) tanpa ada duplikasi HTML.
5. **Clean Code Improvement**:
   - Mengekstraksi fungsi upload gambar dan hapus gambar ke method privat `uploadImage` dan `deleteImage` dalam `MovieService` untuk mengurangi duplikasi.
   - Mengganti pemanggilan metode eloquent berulang dengan fungsi repository.
   - Membersihkan code logic pada controller dengan mengelompokkan payload (contohnya `$request->except('foto_sampul')`).

## 3. Analisis Evolusi Perangkat Lunak (Bagian B)

**Kondisi Awal**
Sebelum dilakukan refactoring, semua logika terkait dengan validasi, manipulasi file (upload & delete file gambar), dan operasi database terkumpul (tightly coupled) di dalam `MovieController`. Hal ini menyebabkan controller menjadi "Fat Controller", sulit untuk diuji, dan banyak duplikasi kode (misalnya logika upload foto yang sama di `store` dan `update`).

**Dampak Refactoring**
- **Kemudahan Pemeliharaan (Maintainability)**: Dengan memisahkan logic ke layer-layer yang berbeda, mencari bug atau mengubah cara penyimpanan data/file menjadi jauh lebih mudah, tanpa perlu membongkar seluruh class Controller.
- **Keterbacaan Kode (Readability)**: Kode di `MovieController` kini sangat ringkas dan berfokus pada aliran HTTP. Nama fungsi pada `MovieService` dan `MovieRepository` pun dibuat deskriptif, sehingga mudah dipahami.
- **Kemudahan Pengembangan (Scalability)**: Menggunakan pola Repository dan Dependency Injection memungkinkan perubahan pada database (misalnya jika bermigrasi ke database NoSQL atau mengganti ORM) tanpa harus menyentuh logika bisnis.

**Potensi Pengembangan**
Di masa depan, arsitektur ini dapat dengan mudah dikembangkan untuk:
1. Pembuatan REST API: Mengingat logika sudah ada di Service, Controller API baru hanya perlu memanggil service yang sama.
2. Unit Testing: Memok (mocking) operasi database untuk menguji business logic di level Service kini sangat mungkin dilakukan berkat adanya antarmuka `MovieRepositoryInterface`.
3. Penambahan Caching pada tingkat Repository tanpa mengganggu logika Service.

## 4. Struktur Folder Setelah Refactoring
```text
app/
├── Http/
│   └── Controllers/
│       └── MovieController.php
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

## 5. Screenshot Perubahan
(Perubahan dapat dilihat dari riwayat commit pada repositori GitHub ini).
