// ===== BLYNK (PAKAI AUTH TOKEN) =====

#define BLYNK_TEMPLATE_ID "TMPL6FfpVGtdO"

#define BLYNK_TEMPLATE_NAME "monitoring panel surya berbasis iot"

#define BLYNK_AUTH_TOKEN "OyicpMEsfuJ_ZK1_k1zqXn6AYn5yPcXR"   // GUNAKAN AUTH TOKEN BUKAN DEVICE KEY

#include <Arduino.h>
#include <HardwareSerial.h>
#include <WiFi.h>
#include <BlynkSimpleEsp32.h>
#include <HTTPClient.h>

// ===== WIFI =====
const char* ssid = "SEI JODOH"; 
const char* pass = "andytriwinarko";

// ===== LARAVEL API =====
const char* laravelApiUrl = "http://192.168.1.102:8000/api/sensor";  // IP komputer Anda (cek dengan ipconfig)
// Pastikan Laravel server berjalan: php artisan serve --host=0.0.0.0 --port=8000

// ===== LoRa SERIAL =====
HardwareSerial LoRa(1);
// ===== Virtual Pins =====
#define VPIN_TEMP        V0
#define VPIN_BAT_VOLT    V1
#define VPIN_PANEL_VOLT  V2
#define VPIN_PANEL_POWER V3
#define VPIN_CHG_POWER   V4
#define VPIN_BAT_PCT     V5
#define VPIN_BAT_WH      V6

volatile bool newDataAvailable = false;

// Variabel global untuk data
float g_temperature = 0.0;
float g_batV       = 0.0;
float g_panelV     = 0.0;
float g_panelW     = 0.0;
float g_chargingW  = 0.0;
float g_batPct     = 0.0;
float g_batWh      = 0.0;

void setup() {
    Serial.begin(115200);

    // LoRa: RX=20, TX=21
    LoRa.begin(9600, SERIAL_8N1, 20, 21);
    delay(500);

    Serial.println("=== RECEIVER MULAI ===");

    // Connect ke WiFi
    Serial.print("Menghubungkan ke WiFi");
    WiFi.begin(ssid, pass);
    int retry = 0;
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
        retry++;
        if (retry > 30) {
            Serial.println("\n❌ Gagal konek WiFi");
            return;
        }
    }
    Serial.println("\n✅ WiFi terhubung!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());

    // Connect ke Blynk
    Serial.println("Menghubungkan ke Blynk...");
    Blynk.begin(BLYNK_AUTH_TOKEN, ssid, pass);
    Serial.println("✅ Blynk Ready!");
    
    Serial.println("✅ Sistem siap menerima data LoRa dan mengirim ke Blynk & Laravel");
}

// ===== Fungsi escape JSON string =====
String escapeJsonString(const String& str) {
    String escaped = "";
    for (unsigned int i = 0; i < str.length(); i++) {
        char c = str.charAt(i);
        if (c == '"') {
            escaped += "\\\"";
        } else if (c == '\\') {
            escaped += "\\\\";
        } else if (c == '\n') {
            escaped += "\\n";
        } else if (c == '\r') {
            escaped += "\\r";
        } else if (c == '\t') {
            escaped += "\\t";
        } else {
            escaped += c;
        }
    }
    return escaped;
}

// ===== Fungsi parsing data LoRa =====
void parseLoRaData(const String& rawData) {
    // Validasi panjang data minimal
    if (rawData.length() < 10) {
        Serial.println("❌ Data terlalu pendek");
        return;
    }

    // Hitung jumlah koma
    int commaCount = 0;
    for (unsigned int i = 0; i < rawData.length(); i++) {
        if (rawData.charAt(i) == ',') commaCount++;
    }
    
    if (commaCount != 6) {
        Serial.print("❌ Format data tidak valid (comma = ");
        Serial.print(commaCount);
        Serial.println(", harus 6 untuk 7 nilai)");
        Serial.print("Data yang diterima: [");
        Serial.print(rawData);
        Serial.println("]");
        return;
    }

    // Cari posisi setiap koma
    int i1 = rawData.indexOf(',');
    if (i1 < 0) {
        Serial.println("❌ Koma pertama tidak ditemukan");
        return;
    }
    
    int i2 = rawData.indexOf(',', i1 + 1);
    int i3 = rawData.indexOf(',', i2 + 1);
    int i4 = rawData.indexOf(',', i3 + 1);
    int i5 = rawData.indexOf(',', i4 + 1);
    int i6 = rawData.indexOf(',', i5 + 1);

    // Validasi semua index ditemukan
    if (i2 < 0 || i3 < 0 || i4 < 0 || i5 < 0 || i6 < 0) {
        Serial.println("❌ Format CSV tidak lengkap (tidak semua koma ditemukan)");
        return;
    }

    // Parse setiap nilai dengan validasi
    String tempStr = rawData.substring(0, i1);
    tempStr.trim();
    g_temperature = tempStr.toFloat();
    
    tempStr = rawData.substring(i1 + 1, i2);
    tempStr.trim();
    g_batV = tempStr.toFloat();
    
    tempStr = rawData.substring(i2 + 1, i3);
    tempStr.trim();
    g_panelV = tempStr.toFloat();
    
    tempStr = rawData.substring(i3 + 1, i4);
    tempStr.trim();
    g_panelW = tempStr.toFloat();
    
    tempStr = rawData.substring(i4 + 1, i5);
    tempStr.trim();
    g_chargingW = tempStr.toFloat();
    
    tempStr = rawData.substring(i5 + 1, i6);
    tempStr.trim();
    g_batPct = tempStr.toFloat();
    
    tempStr = rawData.substring(i6 + 1);
    tempStr.trim();
    g_batWh = tempStr.toFloat();

    // Debug: tampilkan nilai yang diparsing
    Serial.println("📡 Data berhasil diparsing:");
    Serial.print("  Temperature: "); Serial.println(g_temperature);
    Serial.print("  BatV: "); Serial.println(g_batV);
    Serial.print("  PanelV: "); Serial.println(g_panelV);
    Serial.print("  PanelW: "); Serial.println(g_panelW);
    Serial.print("  ChargingW: "); Serial.println(g_chargingW);
    Serial.print("  BatPct: "); Serial.println(g_batPct);
    Serial.print("  BatWh: "); Serial.println(g_batWh);

    newDataAvailable = true;
    Serial.println("✅ Data siap kirim ke Blynk & Laravel");
}

// ===== Fungsi kirim ke Laravel =====
void sendToLaravel(const String& csvData) {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("⚠️ WiFi tidak terhubung, skip kirim ke Laravel");
        Serial.print("WiFi Status: ");
        Serial.println(WiFi.status());
        return;
    }

    HTTPClient http;
    
    // Enable debugging (opsional)
    http.setTimeout(8000);  // Timeout 1 menit
    
    Serial.println("\n=== MENGIRIM KE LARAVEL ===");
    Serial.print("URL: ");
    Serial.println(laravelApiUrl);
    Serial.print("Data CSV: ");
    Serial.println(csvData);
    Serial.print("ESP32 IP: ");
    Serial.println(WiFi.localIP());
    
    http.begin(laravelApiUrl);
    http.addHeader("Content-Type", "application/json");

    // Format JSON: {"data": "csv_string"} dengan escape karakter khusus
    String escapedCsv = escapeJsonString(csvData);
    String jsonPayload = "{\"data\":\"" + escapedCsv + "\"}";
    Serial.print("JSON Payload: ");
    Serial.println(jsonPayload);

    int httpResponseCode = http.POST(jsonPayload);

    if (httpResponseCode > 0) {
        Serial.print("HTTP Response Code: ");
        Serial.println(httpResponseCode);
        
        if (httpResponseCode == 200 || httpResponseCode == 201) {
            Serial.println("✅ Data berhasil dikirim ke Laravel!");
            String response = http.getString();
            Serial.print("Response: ");
            Serial.println(response);
        } else {
            Serial.print("⚠️ Laravel response code: ");
            Serial.println(httpResponseCode);
            String response = http.getString();
            Serial.print("Response: ");
            Serial.println(response);
        }
    } else {
        Serial.print("❌ Error kirim ke Laravel: ");
        Serial.println(http.errorToString(httpResponseCode));
        Serial.println("💡 Pastikan:");
        Serial.println("   1. Laravel server berjalan: php artisan serve --host=0.0.0.0");
        Serial.println("   2. IP address benar (cek dengan ipconfig)");
        Serial.println("   3. Firewall Windows tidak memblokir port 8000");
        Serial.println("   4. ESP32 dan komputer dalam network yang sama");
    }

    http.end();
    Serial.println("============================\n");
}

void loop() {
    // Baca data dari LoRa serial dengan timeout
    if (LoRa.available()) {
        String rawData = "";
        unsigned long startTime = millis();
        const unsigned long timeout = 1000; // Timeout 1 detik
        
        // Baca data sampai menemukan newline atau timeout
        while (millis() - startTime < timeout) {
            if (LoRa.available()) {
                char c = LoRa.read();
                if (c == '\n' || c == '\r') {
                    break; // Selesai membaca baris
                }
                rawData += c;
            }
            delay(1); // Small delay untuk stability
        }
        
        // Bersihkan whitespace dan karakter kontrol
        rawData.trim();
        
        // Hapus karakter kontrol yang mungkin mengganggu
        rawData.replace("\r", "");
        rawData.replace("\n", "");
        rawData.replace("\t", "");
        
        if (rawData.length() > 0) {
            Serial.print("<< RAW LORA [");
            Serial.print(rawData.length());
            Serial.print(" chars]: ");
            Serial.println(rawData);

            // Validasi format CSV sebelum parsing
            int commaCount = 0;
            for (unsigned int i = 0; i < rawData.length(); i++) {
                if (rawData.charAt(i) == ',') commaCount++;
            }
            
            if (commaCount == 6) {
                parseLoRaData(rawData);
                
                // Kirim CSV ke Laravel hanya jika parsing berhasil
                if (newDataAvailable) {
                    sendToLaravel(rawData);
                }
            } else {
                Serial.print("⚠️ Format tidak valid: ditemukan ");
                Serial.print(commaCount);
                Serial.println(" koma (harus 6)");
            }
        }
    }

    // Blynk loop
    Blynk.run();

    // Kirim ke Blynk jika ada data baru
    if (newDataAvailable) {
        Blynk.virtualWrite(VPIN_TEMP, g_temperature);
        Blynk.virtualWrite(VPIN_BAT_VOLT, g_batV);
        Blynk.virtualWrite(VPIN_PANEL_VOLT, g_panelV);
        Blynk.virtualWrite(VPIN_PANEL_POWER, g_panelW);
        Blynk.virtualWrite(VPIN_CHG_POWER, g_chargingW);
        Blynk.virtualWrite(VPIN_BAT_PCT, g_batPct);
        Blynk.virtualWrite(VPIN_BAT_WH, g_batWh);

        Serial.println("✔ Data dikirim ke Blynk!\n");

        newDataAvailable = false;
    }

    delay(60000);
}