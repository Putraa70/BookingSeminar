# Booking Seminar - Sistem Booking Seminar Ilmu Komputer

Aplikasi *Booking Seminar* adalah sistem berbasis web untuk mengelola pendaftaran, penjadwalan, dan approval seminar tugas akhir mahasiswa Ilmu Komputer. Sistem ini berbasis *PHP Native* dan menggunakan *Bootstrap* untuk tampilan antarmuka, mendukung multi-role (Admin, Mahasiswa, Dosen Pembimbing, Dosen Penguji) dengan flow approval digital dan dashboard masing-masing.

---

## Fitur Utama

- *Registrasi & Login Mahasiswa*
- *Booking Seminar:* Mahasiswa dapat mengajukan permohonan seminar secara online
- *Approval Seminar:* Dosen Pembimbing & Dosen Penguji dapat approve atau reject permohonan seminar
- *Jadwal Seminar:* Semua user dapat melihat jadwal seminar yang sudah disetujui
- *Manajemen User:* Admin dapat mengelola data mahasiswa, dosen, dan peserta seminar
- *Dashboard Role-based:* Setiap user melihat fitur sesuai hak akses

---

## Instalasi

1. *Clone repository ini:*
    bash
    git clone https://github.com/Putraa70/BookingSeminar.git
    
2. *Pindahkan folder* BookingSeminar ke dalam direktori server lokal, misal htdocs (untuk XAMPP) atau www (untuk Laragon).
3. *Buat database baru* di MySQL, misal: bookingseminar.
4. *Import file database:*
    - Buka phpMyAdmin
    - Pilih database bookingseminar
    - Import file:  
      
      BookingSeminar/backup_bookingseminar_YYYYMMDD.sql
      
5. *Edit konfigurasi koneksi database:*
    - File: BookingSeminar/config/database.php
    - Sesuaikan host, user, password, database sesuai server lokal Anda.

    php
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "bookingseminar";
    
6. *Akses aplikasi via browser:*
    
    http://localhost/BookingSeminar/auth/login.php
    

---

---

## Cara Menjalankan

1. Pastikan *web server* (XAMPP/Laragon) dan *MySQL* aktif.
2. Pastikan database sudah diimport.
3. Akses halaman login:
    
    http://localhost/BookingSeminar/auth/login.php
    
4. Login sebagai mahasiswa (register dulu), dosen, atau admin (data awal/admin biasanya diset di database, cek tabel user).
5. Gunakan fitur sesuai role yang didapat.

---

## Alur Sistem & Hak Akses

### *Role*
- *Admin:* Kelola data user (dosen & mahasiswa), dan seminar
- *Mahasiswa:*  
  - Register & login  
  - Ajukan booking seminar  
  - Lihat status pengajuan & jadwal  
  - Batalkan booking
- *Dosen Pembimbing / Penguji:*  
  - Login  
  - Lihat daftar pengajuan seminar  
  - Approve/Reject pengajuan  
  - Hanya jika kedua dosen approve, seminar diterima
  - Jika salah satu menolak, seminar otomatis rejected

### *Alur Approval*
1. *Mahasiswa register* dan login ke dashboard
2. *Booking seminar:* Mengisi form pengajuan seminar
3. *Pengajuan otomatis dikirim ke dosen pembimbing & penguji*
4. *Dosen melakukan approval* via dashboard dosen
5. *Jika dua-duanya approve* → seminar terjadwal  
   *Jika salah satu reject* → seminar otomatis ditolak
6. *Mahasiswa dapat cek status approval* dan melihat jadwal seminar yang diterima

---

## Teknologi yang Digunakan

- *PHP Native* (tanpa framework)
- *MySQL* (database)
- *Bootstrap* (UI/UX frontend)
- *Javascript, HTML, CSS* (frontend dasar)
- *phpMyAdmin* (DB lokal)


---
