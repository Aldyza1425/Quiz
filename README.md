# Media Belajar Quiz - Platform Pengerjaan Soal Online

Platform aplikasi berbasis web untuk manajemen dan pengerjaan quiz secara online, dirancang untuk mempermudah interaksi antara Admin, Guru, dan Siswa dalam proses evaluasi pembelajaran.

## Arsitektur Proyek

Proyek ini menggunakan arsitektur _Separated Frontend & Backend_ (SPA):

- **Backend**: Dikembangkan menggunakan framework **Laravel** (PHP). Berfungsi sebagai RESTful API yang melayani data dan validasi logika bisnis.
- **Frontend**: Dikembangkan menggunakan **Vue.js 3** dengan build tool **Vite**. Antarmuka pengguna yang modern, responsif, dan interaktif menggunakan TailwindCSS.

---

## Struktur Folder

```text
Quiz/
├── Backend/          # Framework Laravel (Backend API)
├── Frontend/         # Vue.js Project (Frontend UI)
└── README.md         # Dokumentasi Utama
```

---

## Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL (XAMPP/Laragon)

### 2. Setup Backend (Laravel)

Pindah ke folder `Backend` dan jalankan perintah berikut:

```bash
cd Backend
composer install
cp .env.example .env
php artisan key:generate
# Sesuaikan pengaturan database di file .env
php artisan migrate --seed
php artisan serve
```

API akan berjalan di `http:localhost:8000`.

### 3. Setup Frontend (Vue.js)

Pindah ke folder `Frontend` dan jalankan perintah berikut:

```bash
cd Frontend
npm install
npm run dev
```

Aplikasi akan berjalan di `http:localhost:5173`.

---

## Fitur Utama

### Admin

- Manajemen Data Guru & Siswa.
- Manajemen Kelas & Tahun Ajaran.
- Dashboard statistik aktivitas sistem.

### Guru

- Pembuatan Bank Soal (Pilihan Ganda & Isian).
- Penjadwalan Quiz untuk Kelas tertentu.
- Monitoring hasil dan skor siswa.

### Siswa

- Pengerjaan Quiz secara _real-time_.
- Riwayat pengerjaan dan perolehan skor.
- Antarmuka pengerjaan yang fokus dan intuitif.

---

## Pengembangan

Untuk menjaga kebersihan kode, semua komentar bersifat "catatan pribadi" atau pembatas struktural telah dibersihkan. Gunakan dokumentasi ini sebagai referensi utama struktur proyek.
