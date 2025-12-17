import serial
import requests
import time
import re

# Konfigurasi
PORT = 'COM13'  # Ganti sesuai port Anda (cek di Device Manager / Arduino IDE)
BAUDRATE = 115200  # Sesuaikan dengan baudrate di Arduino (biasanya 115200 untuk ESP32)
LARAVEL_API_URL = 'http://localhost:8000/api/sensor'  # Endpoint sesuai routes/api.php

# Inisialisasi serial
try:
    ser = serial.Serial(PORT, BAUDRATE, timeout=1)
    print(f"✅ Terhubung ke {PORT} @ {BAUDRATE} baud")
except Exception as e:
    print(f"❌ Gagal konek ke serial: {e}")
    print("💡 Pastikan Arduino IDE ditutup dan port COM benar")
    exit(1)

print(f"📡 Mendengarkan data dari serial monitor...")
print(f"🌐 Akan mengirim ke: {LARAVEL_API_URL}")
print("Tekan Ctrl+C untuk stop\n")

while True:
    try:
        if ser.in_waiting > 0:
            # Baca sampai newline
            line = ser.readline().decode('utf-8', errors='ignore').strip()
            
            # Filter hanya data CSV (format: angka,angka,angka,...)
            # Contoh: "25.5,12.3,18.7,50.2,45.1,85.0,1200.5"
            if line and ',' in line:
                # Validasi format: minimal ada beberapa angka dipisahkan koma
                csv_pattern = r'^[\d\.\-\+]+(,[\d\.\-\+]+)+$'
                if re.match(csv_pattern, line):
                    print(f"📥 Data diterima: {line}")

                    # Hitung jumlah koma untuk validasi
                    comma_count = line.count(',')
                    if comma_count == 6:  # Harus ada 6 koma untuk 7 nilai
                        try:
                            # Kirim ke Laravel
                            response = requests.post(
                                LARAVEL_API_URL,
                                json={'data': line},  # Gunakan JSON agar lebih konsisten
                                headers={'Content-Type': 'application/json'},
                                timeout=5
                            )
                            
                            if response.status_code == 201:
                                print("✅ Berhasil dikirim ke Laravel")
                            elif response.status_code == 200:
                                print("✅ Berhasil dikirim ke Laravel (200)")
                            else:
                                print(f"❌ Gagal: {response.status_code} - {response.text}")
                        except requests.exceptions.RequestException as e:
                            print(f"⚠️ Error kirim ke Laravel: {e}")
                    else:
                        print(f"⚠️ Format tidak valid: harus 7 nilai (ditemukan {comma_count + 1} nilai)")
                # Jika bukan data CSV, bisa diabaikan atau ditampilkan untuk debug
                # else:
                #     print(f"📋 Raw: {line}")  # Uncomment untuk melihat semua output serial

    except KeyboardInterrupt:
        print("\n🛑 Program dihentikan.")
        ser.close()
        break
    except Exception as e:
        print(f"⚠️ Error: {e}")
        time.sleep(1)