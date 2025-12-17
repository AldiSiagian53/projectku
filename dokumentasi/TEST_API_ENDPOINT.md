# Test API Endpoint

## Masalah: "Not Found" saat mengakses `http://192.168.1.7:8000/api/sensor`

## ✅ Solusi yang Sudah Diterapkan

Route API sudah ditambahkan ke `bootstrap/app.php`. Sekarang route `/api/sensor` sudah terdaftar.

## ⚠️ PENTING: Route ini adalah POST, bukan GET!

Jika Anda mengakses dari browser dengan GET, akan muncul "not found" karena route hanya menerima POST.

## Cara Test yang Benar:

### 1. Pastikan Laravel Server Berjalan
```bash
cd projectku
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. Test dengan PowerShell (Windows)
```powershell
$body = @{
    data = "25.5,12.3,18.7,50.2,45.1,85.0,1200.5"
} | ConvertTo-Json

Invoke-WebRequest -Uri http://localhost:8000/api/sensor -Method POST -Body $body -ContentType 'application/json'
```

### 3. Test dengan Postman atau Tools Lain
- Method: **POST**
- URL: `http://192.168.1.7:8000/api/sensor`
- Headers: `Content-Type: application/json`
- Body (JSON):
```json
{
    "data": "25.5,12.3,18.7,50.2,45.1,85.0,1200.5"
}
```

### 4. Test dari Arduino/ESP32
Kode Arduino yang sudah dibuat akan otomatis mengirim POST request ke endpoint ini.

## Cek Route yang Tersedia:

```bash
cd projectku
php artisan route:list --path=api
```

Harusnya muncul:
```
POST  api/sensor  SolarController@store
```

## Restart Laravel Server

Setelah mengubah `bootstrap/app.php`, **restart Laravel server**:
1. Stop server (Ctrl+C)
2. Jalankan lagi: `php artisan serve --host=0.0.0.0 --port=8000`

## Catatan:

- Browser hanya bisa melakukan GET request, jadi jika Anda buka di browser akan muncul "not found"
- Endpoint ini hanya menerima POST request
- Untuk testing dari browser, Anda bisa buat route GET terpisah untuk testing (opsional)


