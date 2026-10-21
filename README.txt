ROOMIFY - SISTEM PEMINJAMAN RUANGAN
===================================

TEKNOLOGI
- PHP Native (disarankan PHP 8.0+)
- MySQL / MariaDB
- Bootstrap 5.3.8 CDN
- XAMPP

CARA MENJALANKAN
1. Ekstrak folder Roomify ke C:\xampp\htdocs\ atau C:\BARUxampp\htdocs\
2. Jalankan Apache dan MySQL dari XAMPP.
3. Buka phpMyAdmin: http://localhost/phpmyadmin
4. Import file database.sql.
5. Periksa konfigurasi koneksi di config/koneksi.php.
   Default database: roomify
   Username MySQL: root
   Password MySQL: kosong
6. Buka http://localhost/Roomify/setup_admin.php satu kali untuk membuat akun admin awal.
7. Setelah pesan berhasil muncul, hapus setup_admin.php demi keamanan.
8. Buka http://localhost/Roomify/login.php

AKUN ADMIN AWAL
Username: admin
Password: admin123

FITUR
- Login/logout dengan session dan password_hash/password_verify
- Dua level pengguna: admin dan peminjam
- Admin: dashboard, CRUD ruangan, daftar semua booking, setujui/tolak booking, laporan
- Peminjam: dashboard, daftar ruangan, ajukan booking, riwayat, batalkan pengajuan yang masih menunggu
- Pemeriksaan bentrok jadwal untuk booking yang menunggu/disetujui
- Validasi dasar dan prepared statements PDO

CATATAN
- Ini merupakan starter project yang dapat dikembangkan lebih lanjut.
- Ganti password admin setelah login pertama.
- Bootstrap dimuat melalui CDN sehingga membutuhkan koneksi internet untuk styling.
- Aplikasi ini tidak menggunakan pembayaran.
