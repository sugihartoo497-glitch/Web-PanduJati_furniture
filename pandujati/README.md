# Pandu Jati Furniture - Fullstack JS/TS + PHP Architecture

Website bisnis mebel & platform pelacakan pesanan **Pandu Jati Furniture Jepara**.
Arsitektur proyek ini dibuat dengan pemisahan yang bersih, transparan, dan mudah dipahami:
- **Frontend**: HTML5 + Modern JavaScript / TypeScript (fetch API, modular & responsive)
- **Backend**: Native PHP dengan MySQL PDO (REST API JSON yang aman & mudah dipelajari)
- **Database**: MySQL `pandu_jati_db`

---

## 🌟 Cara Menjalankan di Laragon

1. Buka aplikasi **Laragon** di laptop Anda.
2. Klik tombol **"Start All"** (pastikan Apache & MySQL aktif).
3. Buka browser dan buka:
   - **Toko & Katalog:** [http://localhost/pandujati/](http://localhost/pandujati/)
   - **Cek Status Pesanan:** [http://localhost/pandujati/cek-pesanan.html?id=PJF-20261008-0001](http://localhost/pandujati/cek-pesanan.html?id=PJF-20261008-0001)
   - **Portal Admin:** [http://localhost/pandujati/admin.html](http://localhost/pandujati/admin.html)

### Akses dari HP (Satu Jaringan Wi-Fi)
Buka browser di HP Anda (Chrome/Safari), ketik:
👉 **`http://192.168.1.3/pandujati/`**

---

## 🔐 Kredensial Login Admin
- **Email:** `admin@pandujati.com`
- **Password:** `admin123`

---

## 📁 Struktur File & Kodingan

```
C:\laragon\www\pandujati\
│
├── api/                           # 🐘 BACKEND (PHP REST API)
│   ├── db.php                     # Koneksi PDO MySQL & fungsi helper JSON
│   ├── products.php               # CRUD produk mebel (GET, POST, PUT, DELETE)
│   ├── orders.php                 # Pembuatan pesanan & update status (POST, GET, PUT)
│   ├── track.php                  # Pelacakan order 8-tahap produksi (GET)
│   ├── custom.php                 # Permohonan custom furniture (POST, GET)
│   ├── auth.php                   # Login, sesi, & logout admin (POST, GET)
│   └── stats.php                  # Statistik KPI omzet & pesanan (GET)
│
├── src/ts/                        # 🔷 FRONTEND TYPESCRIPT SOURCE
│   ├── types.ts                   # Definisi interface & type models (Product, Order, Status)
│   └── api.ts                     # Modul client fetch API dengan tipe TypeScript
│
├── assets/js/                     # ⚡ FRONTEND JAVASCRIPT (ES6 Browser)
│   ├── api.js                     # Client service fetch API untuk komunikasi ke backend PHP
│   ├── app.js                     # Navbar dinamis, footer, floating WA, & helper rupiah
│   ├── home.js                    # Logika render produk unggulan & kategori di index.html
│   ├── katalog.js                 # Logika live search, filter kategori, & sort harga
│   ├── produk.js                  # Logika spesifikasi teknis mebel & direct order
│   ├── pesanan.js                 # Logika checkout, kalkulasi ongkir, & nomor pesanan unik
│   ├── cek-pesanan.js             # Logika render timeline 8-tahap produksi kayu jati
│   ├── custom.js                  # Logika submit tiket custom & forward ke WhatsApp CS
│   └── admin.js                   # Logika dashboard admin, tabel, modal status, & CRUD produk
│
├── index.html                     # Halaman Beranda (Hero, Trust, Produk Unggulan)
├── katalog.html                   # Halaman Katalog Produk
├── produk.html                    # Halaman Detail Produk
├── pesanan.html                   # Halaman Form Pemesanan Online
├── cek-pesanan.html               # Halaman Pelacak 8-Tahap Produksi
├── custom.html                    # Halaman Permintaan Custom Mebel
├── tentang-kami.html              # Profil Brand, Workshop, & Kayu Jati SVLK
├── cara-pemesanan.html            # 6 Langkah Belanja & Rekening Bank
├── kontak.html                    # Alamat Workshop, Peta, & Kontak WA
├── admin.html                     # Portal Back-Office Admin (SPA)
└── database.sql                   # Skema & data awal MySQL
```

---

## 🔄 Alur Kerja Sistem (Bagaimana Frontend & Backend Berkomunikasi)

1. **Frontend (JS/TS):**
   Pelanggan membuka `pesanan.html` dan menekan tombol buat pesanan.
   File `assets/js/pesanan.js` memanggil `PanduApi.createOrder(payload)` di `assets/js/api.js`.

2. **Pengiriman Data (Fetch HTTP):**
   `fetch('api/orders.php', { method: 'POST', body: JSON.stringify(payload) })` mengirim data ke server.

3. **Backend (PHP):**
   `api/orders.php` menerima request, memvalidasi input, membuat nomor pesanan unik `PJF-YYYYMMDD-XXXX`, menyimpan rincian barang ke tabel `orders` & `order_items`, lalu mengembalikan respon JSON `{ "success": true, "data": { ... } }`.

4. **Respon ke Pengguna (UI Update):**
   Frontend JS menerima respon JSON, menyembunyikan form, dan menampilkan nomor pesanan unik beserta tombol langsung kirim ke WhatsApp customer service (`+62 851-9123-5955`).
