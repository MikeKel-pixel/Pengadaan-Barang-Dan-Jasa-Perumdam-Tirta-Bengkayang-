# 🏢 PERUMDAM Tirta Bengkayang - Sistem Pengadaan Barang/Jasa

Sistem Informasi Pengadaan Barang/Jasa berbasis web untuk **Perumdam Tirta Bengkayang**. Dibangun menggunakan **Laravel 10** dengan fitur manajemen pengadaan yang lengkap dan transparan.

---

## 📌 Fitur Utama

| Modul | Fitur |
|-------|-------|
| **Authentication** | Login, Register, Logout dengan 5 role berbeda |
| **Role Management** | Admin, Pengadaan, Pimpinan, Vendor, User |
| **Data Master** | CRUD Kategori, Barang, Supplier |
| **Pengajuan** | Buat, edit, hapus, ajukan pengajuan dengan multi item |
| **Approval** | Setujui/tolak pengajuan dengan catatan |
| **Penawaran Vendor** | Input penawaran, pilih vendor terbaik |
| **Dashboard** | Chart statistik per role |
| **Laporan** | Laporan pengajuan & vendor, export CSV |
| **API** | 12 endpoint REST API |
| **Two Factor Auth** | Keamanan tambahan dengan 2FA |
| **Verifikasi Vendor** | User daftar vendor, admin verifikasi |

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 10 |
| Database | MySQL |
| Frontend | Tailwind CSS, Blade |
| Authentication | Laravel Breeze |
| Role & Permission | Spatie Laravel Permission |
| API | REST API (JSON) |
| 2FA | Google2FA |
| Package | DomPDF, Chart.js |

---

## 📋 Role & Akun Default

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@perumdam.com` | `admin123` |
| **Pengadaan** | `pengadaan@perumdam.com` | `pengadaan123` |
| **Pimpinan** | `pimpinan@perumdam.com` | `pimpinan123` |
| **Vendor** | `vendor@perumdam.com` | `vendor123` |
| **User Biasa** | `user@perumdam.com` | `user123` |

---

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/username/perumdam.git
cd perumdam