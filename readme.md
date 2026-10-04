# Event Ticketing System

Event Ticketing System adalah aplikasi web untuk melakukan pemesanan tiket event secara online. Aplikasi ini memiliki dua sisi pengguna, yaitu **Admin** dan **User**.

Project ini dibuat untuk memenuhi tugas praktikum **Pengembangan Perangkat Lunak Web** dengan menerapkan konsep **Object-Oriented Programming (OOP) PHP**, CRUD, database PostgreSQL, serta transaksi pemesanan tiket.

---

## Demo

### User

Halaman utama untuk pengguna dalam melihat event dan melakukan pemesanan tiket.

[**Akses Website User**](https://tiketevent.onrender.com/user/index.php)

### Admin

Halaman admin untuk mengelola event, kategori, tiket, dan transaksi.

[**Akses Admin**](https://tiketevent.onrender.com/admin/index.php)

**Login Admin:**

- **Email:** `admin@gmail.com`
- **Password:** `password`

---

## 👥 Anggota Kelompok

| No. | NIM | Nama | Role |
|---:|---|---|---|
| 1 | 434251135 | Rama Miqdad Fadhilul Umam | Ketua |
| 2 | 434251114 | CHOIRUL UMAM| Anggota | Anggota |
| 3 | 434251120 | IBNU HEDIANSYAH ABIMANYU| Anggota |
| 4 | 434251126 | MUHAMMAD FARID HAQIQI | Anggota |
| 5 | 434251130 | NAOMI MAKAYLA PUTRI KARMAWAN | Anggota |
| 6 | 434251137 | SURYA IVANTO| Anggota |
| 7 | 434251138 | VELISA DWI AGUSTIN| Anggota | Anggota |
| 8 | 434251143 | ZALFA ZAHIYA NUR WIDIANTI| Anggota |
| 9 | 434251147 | FAKHRI MAULANA | Anggota |

---

## 👥 Role Pengguna

### 1. Admin

Admin bertanggung jawab untuk mengelola data dan transaksi dalam sistem.

Fitur Admin:

- Login Admin
- Dashboard
- CRUD Category
- CRUD Event
- CRUD Ticket
- Melihat transaksi
- Melihat detail transaksi

### 2. User

User dapat melakukan pemesanan tiket tanpa harus melakukan login.

Fitur User:

- Melihat halaman utama
- Melihat event
- Melihat detail event
- Memilih tiket
- Mengisi data pemesan
- Memilih metode pembayaran
- Melakukan pemesanan tiket
- Melihat hasil transaksi

---

## 🎫 Fitur Utama

### Admin Dashboard

Dashboard menyediakan informasi mengenai:

- Total Event
- Total Category
- Total Ticket
- Total Tiket Terjual
- Total Pendapatan
- Event yang Akan Datang
- Transaksi Terbaru

### Category Management

Admin dapat:

- Menambahkan category
- Melihat category
- Mengubah category
- Menghapus category

### Event Management

Admin dapat:

- Menambahkan event
- Melihat event
- Mengubah event
- Menghapus event
- Menentukan category event
- Menentukan tanggal dan waktu event
- Menambahkan lokasi event
- Menambahkan gambar event
- Mengatur status event

### Ticket Management

Admin dapat:

- Menambahkan ticket
- Melihat ticket
- Mengubah ticket
- Menghapus ticket
- Menentukan harga tiket
- Mengatur stok tiket

### Ticket Ordering

User dapat:

1. Memilih event
2. Melihat detail event
3. Memilih jenis dan jumlah tiket
4. Mengisi data pemesan
5. Memilih metode pembayaran
6. Melakukan pembayaran simulasi
7. Mendapatkan hasil transaksi

---

## 💳 Metode Pembayaran

Sistem menyediakan pembayaran simulasi dengan beberapa metode:

- Transfer Bank
- E-Wallet
- QRIS

> Pembayaran pada aplikasi ini masih berupa simulasi dan belum terhubung dengan payment gateway.

---

## 🛠️ Teknologi

Project ini menggunakan:

| Teknologi | Keterangan |
|---|---|
| PHP | Backend |
| PostgreSQL | Database |
| HTML | Struktur halaman |
| Bootstrap 5 | UI |
| Bootstrap Icons | Icon |
| Git & GitHub | Version Control |

---

## 🧱 Konsep Pemrograman

Project menerapkan beberapa konsep OOP PHP, yaitu:

- Class
- Object
- Constructor
- Inheritance
- Abstract Class
- Interface
- Encapsulation
- Method
- Exception Handling
- Autoloading
- Database Connection
- CRUD
- Database Transaction

Struktur model menggunakan:

```text
Crudable
    ↓
BaseModel
    ↓
├── User
├── Category
├── Event
├── Ticket
├── Order
└── Payment