# Troubleshooting: Connection Refused Error

## Problem
Error: `❌ Error kirim ke Laravel: connection refused`

## Solusi yang Sudah Diterapkan ✅
1. **IP Address sudah diperbaiki** dari `192.168.1.100` ke `192.168.1.7`
2. **Debug logging ditambahkan** untuk memudahkan troubleshooting

## Langkah-langkah untuk Memastikan Koneksi Berhasil:

### 1. Pastikan Laravel Server Berjalan dengan Benar

**❌ SALAH (hanya bisa diakses dari localhost):**
```bash
php artisan serve
```

**✅ BENAR (bisa diakses dari network):**
```bash
cd projectku
php artisan serve --host=0.0.0.0 --port=8000
```

Atau jika sudah di folder projectku:
```bash
php artisan serve --host=0.0.0.0
```

**Kenapa?** 
- `--host=0.0.0.0` membuat server bisa diakses dari network, bukan hanya localhost
- Tanpa ini, ESP32 tidak bisa mengakses server Laravel

### 2. Cek IP Address Komputer

IP komputer Anda: **192.168.1.7**

Untuk mengecek lagi:
```bash
ipconfig | findstr /i "IPv4"
```

### 3. Pastikan ESP32 dan Komputer dalam Network yang Sama

- ESP32 harus terhubung ke WiFi yang sama dengan komputer
- WiFi: "Mantap" (pastikan ESP32 terhubung)

### 4. Cek Firewall Windows

Firewall mungkin memblokir port 8000. Lakukan salah satu:

**Opsi A: Tambahkan exception untuk PHP**
1. Windows Defender Firewall → Advanced settings
2. Inbound Rules → New Rule
3. Program → Browse ke `C:\php\php.exe` (atau path PHP Anda)
4. Allow connection

**Opsi B: Nonaktifkan firewall sementara (untuk testing)**

### 5. Test Koneksi dari Browser

Buka browser di komputer yang sama:
```
http://192.168.1.7:8000/api/sensor
```

Ini akan error (karena butuh POST), tapi jika bisa diakses berarti server berjalan dengan benar.

### 6. Test dengan curl (Opsional)

```bash
curl -X POST http://192.168.1.7:8000/api/sensor -H "Content-Type: application/json" -d "{\"data\":\"25.5,12.3,18.7,50.2,45.1,85.0,1200.5\"}"
```

### 7. Cek Serial Monitor untuk Debug Info

Setelah upload kode Arduino yang baru, Serial Monitor akan menampilkan:
- IP Address ESP32
- URL yang dituju
- Data yang dikirim
- Response dari server

Ini akan membantu troubleshooting lebih lanjut.

## Checklist Sebelum Upload Arduino:

- [ ] Laravel server berjalan dengan `--host=0.0.0.0`
- [ ] IP di kode Arduino sesuai dengan IP komputer (192.168.1.7)
- [ ] ESP32 terhubung ke WiFi yang sama
- [ ] Firewall tidak memblokir port 8000
- [ ] Test API dari browser/curl berhasil

## Jika Masih Error:

1. **Cek Serial Monitor** - lihat debug info yang ditampilkan
2. **Cek Laravel log**: `projectku/storage/logs/laravel.log`
3. **Cek apakah ESP32 mendapat IP** - lihat di Serial Monitor
4. **Coba ping dari komputer ke ESP32** (jika bisa)


