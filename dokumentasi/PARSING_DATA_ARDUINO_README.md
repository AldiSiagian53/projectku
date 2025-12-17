# Parsing Data Arduino ke Laravel

## Setup yang sudah dilakukan:

### 1. Database Migration ✅
- Migration untuk tabel `sensor_data` sudah dibuat
- Jalankan migration: `php artisan migrate`

### 2. Model SensorData ✅
- Model sudah dibuat dengan fillable fields
- Mendukung semua field: temperature, bat_v, panel_v, panel_power, charging_power, bat_percent, bat_wh

### 3. API Endpoint ✅
- Endpoint: `POST /api/sensor`
- Controller: `SolarController@store`
- Mendukung 2 format:
  - CSV string: `{"data": "25.5,12.3,18.7,50.2,45.1,85.0,1200.5"}`
  - JSON object: `{"temperature": 25.5, "bat_v": 12.3, ...}`

## Cara Menggunakan:

### Opsi 1: Menggunakan Python Bridge (Recommended untuk Serial Monitor)

1. **Install Python dependencies:**
```bash
pip install pyserial requests
```

2. **Edit `solar_brige/forward_to_laravel.py`:**
   - Ubah `PORT = 'COM4'` sesuai port Arduino Anda
   - Ubah `BAUDRATE = 115200` sesuai baudrate Serial Monitor Anda
   - Pastikan `LARAVEL_API_URL` sesuai dengan URL server Laravel Anda

3. **Jalankan Python script:**
```bash
python solar_brige/forward_to_laravel.py
```

4. **Buka Serial Monitor di Arduino IDE:**
   - Pastikan baudrate sesuai (115200 atau sesuai setting)
   - Data akan otomatis dikirim ke Laravel

### Opsi 2: Menggunakan Kode Arduino yang Dimodifikasi

1. **Edit `arduino_code_with_laravel.ino`:**
   - Ubah `laravelApiUrl` dengan IP server Laravel Anda
     - Jika Laravel di komputer yang sama: cek IP dengan `ipconfig` (Windows) atau `ifconfig` (Linux/Mac)
     - Contoh: `"http://192.168.1.100:8000/api/sensor"`
   
2. **Upload ke ESP32:**
   - Buka file `arduino_code_with_laravel.ino` di Arduino IDE
   - Upload ke ESP32 Anda

3. **Data akan otomatis dikirim ke:**
   - Blynk (seperti sebelumnya)
   - Laravel API (secara otomatis)

## Format Data yang Diharapkan:

Data dari Arduino harus dalam format CSV dengan 7 nilai dipisahkan koma:
```
temperature,batV,panelV,panelW,chargingW,batPct,batWh
```

Contoh:
```
25.5,12.3,18.7,50.2,45.1,85.0,1200.5
```

## Testing:

### Test dengan curl:
```bash
curl -X POST http://localhost:8000/api/sensor \
  -H "Content-Type: application/json" \
  -d '{"data": "25.5,12.3,18.7,50.2,45.1,85.0,1200.5"}'
```

### Atau dengan JSON format:
```bash
curl -X POST http://localhost:8000/api/sensor \
  -H "Content-Type: application/json" \
  -d '{
    "temperature": 25.5,
    "bat_v": 12.3,
    "panel_v": 18.7,
    "panel_power": 50.2,
    "charging_power": 45.1,
    "bat_percent": 85.0,
    "bat_wh": 1200.5
  }'
```

## Catatan Penting:

1. **Jalankan migration terlebih dahulu:**
   ```bash
   cd projectku
   php artisan migrate
   ```

2. **Pastikan Laravel server berjalan:**
   ```bash
   php artisan serve
   ```

3. **Untuk Python bridge:**
   - Pastikan Arduino IDE ditutup (agar port tidak terpakai)
   - Pastikan port COM benar (cek di Device Manager)

4. **Untuk kode Arduino langsung:**
   - ESP32 harus terhubung ke WiFi
   - Server Laravel harus bisa diakses dari network ESP32 (jangan pakai localhost, pakai IP komputer)

## Troubleshooting:

- **Error: Port sudah digunakan**
  - Tutup Arduino IDE
  - Pastikan tidak ada program lain yang menggunakan port COM

- **Error: Cannot connect to Laravel**
  - Pastikan Laravel server berjalan
  - Cek firewall Windows
  - Untuk kode Arduino: pastikan IP server benar dan bisa diakses dari ESP32

- **Data tidak masuk ke database**
  - Cek log Laravel: `storage/logs/laravel.log`
  - Pastikan migration sudah dijalankan
  - Cek format data CSV harus sesuai (7 nilai, dipisahkan koma)




