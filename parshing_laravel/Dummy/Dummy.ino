// ===== BLYNK (PAKAI AUTH TOKEN) =====
// Nilai ini akan diisi oleh Laravel (template placeholder)
#define BLYNK_TEMPLATE_ID "{{BLYNK_TEMPLATE_ID}}"
#define BLYNK_TEMPLATE_NAME "{{BLYNK_TEMPLATE_NAME}}"
#define BLYNK_AUTH_TOKEN "{{BLYNK_AUTH_TOKEN}}"

#include <Arduino.h>
#include <HardwareSerial.h>
#include <WiFi.h>
#include <BlynkSimpleEsp32.h>
#include <HTTPClient.h>

// ===== WIFI =====
// Nilai ini akan diisi oleh Laravel (template placeholder)
const char* ssid = "{{WIFI_SSID}}"; 
const char* pass = "{{WIFI_PASS}}";

// ===== LARAVEL API =====
// Nilai ini akan diisi oleh Laravel (template placeholder)
const char* laravelApiUrl = "http://{{LARAVEL_IP}}:{{LARAVEL_PORT}}/api/sensor";

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

// ===== Buffer untuk data LoRa =====
#define BUFFER_SIZE 128
char loraBuffer[BUFFER_SIZE];
int bufferIndex = 0;

volatile bool newDataAvailable = false;

// Variabel global untuk data
float g_temperature = 0.0;
float g_batV       = 0.0;
float g_panelV     = 0.0;
float g_panelW     = 0.0;
float g_chargingW  = 0.0;
float g_batPct     = 0.0;
float g_batWh      = 0.0;

// Statistik
unsigned long totalPacketsReceived = 0;
unsigned long validPackets = 0;
unsigned long invalidPackets = 0;

void setup() {
    Serial.begin(115200);
    delay(1000);

    // LoRa: RX=20, TX=21
    LoRa.begin(9600, SERIAL_8N1, 20, 21);
    delay(500);

    Serial.println("\n╔═══════════════════════════════════════╗");
    Serial.println("║   RECEIVER MODE - DATA CAPTURE        ║");
    Serial.println("╚═══════════════════════════════════════╝");

    // Connect ke WiFi
    Serial.print(" Menghubungkan ke WiFi");
    WiFi.begin(ssid, pass);
    int retry = 0;
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
        retry++;
        if (retry > 30) {
            Serial.println("\n❌ Gagal konek WiFi - Lanjut tanpa WiFi");
            break;
        }
    }
    
    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\n✅ WiFi terhubung!");
        Serial.print("📡 IP Address: ");
        Serial.println(WiFi.localIP());

        // Connect ke Blynk
        Serial.print("☁️  Menghubungkan ke Blynk");
        Blynk.config(BLYNK_AUTH_TOKEN);
        if (Blynk.connect(3333)) {
            Serial.println(" ✅");
        } else {
            Serial.println(" ⚠️ Timeout (lanjut tanpa Blynk)");
        }
    }
    
    // Reset buffer
    memset(loraBuffer, 0, BUFFER_SIZE);
    bufferIndex = 0;
    
    Serial.println("\n✅ Sistem siap menerima data dari Transmitter");
    Serial.println("Format data: TEMP,BAT_V,PANEL_V,PANEL_W,CHARGE_W,BAT_%,BAT_Wh\n");
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

// ===== Fungsi validasi dan parsing data CSV =====
bool parseLoRaData(const String& rawData) {
    // Validasi panjang minimal
    if (rawData.length() < 10) {
        Serial.println("❌ Data terlalu pendek");
        return false;
    }

    // Hitung jumlah koma
    int commaCount = 0;
    for (unsigned int i = 0; i < rawData.length(); i++) {
        if (rawData.charAt(i) == ',') commaCount++;
    }
    
    // Harus ada 6 koma untuk 7 nilai
    if (commaCount != 6) {
        Serial.printf("❌ Format salah: ditemukan %d koma (harus 6)\n", commaCount);
        return false;
    }

    // Parse setiap nilai
    int indices[7];
    indices[0] = 0;
    int commaNum = 0;
    
    // Temukan posisi semua koma
    for (unsigned int i = 0; i < rawData.length() && commaNum < 6; i++) {
        if (rawData.charAt(i) == ',') {
            commaNum++;
            indices[commaNum] = i + 1;
        }
    }
    
    // Extract dan parse setiap field
    String fields[7];
    for (int i = 0; i < 6; i++) {
        int start = indices[i];
        int end = rawData.indexOf(',', start);
        fields[i] = rawData.substring(start, end);
        fields[i].trim();
    }
    fields[6] = rawData.substring(indices[6]);
    fields[6].trim();
    
    // Validasi: semua field harus berisi angka
    for (int i = 0; i < 7; i++) {
        if (fields[i].length() == 0) {
            Serial.printf("❌ Field %d kosong\n", i);
            return false;
        }
        
        // Cek apakah string adalah angka valid
        bool isValid = false;
        for (unsigned int j = 0; j < fields[i].length(); j++) {
            char c = fields[i].charAt(j);
            if (isdigit(c) || c == '.' || c == '-') {
                isValid = true;
            } else if (c != ' ') {
                isValid = false;
                break;
            }
        }
        
        if (!isValid) {
            Serial.printf("❌ Field %d bukan angka: '%s'\n", i, fields[i].c_str());
            return false;
        }
    }
    
    // Konversi ke float
    g_temperature = fields[0].toFloat();
    g_batV        = fields[1].toFloat();
    g_panelV      = fields[2].toFloat();
    g_panelW      = fields[3].toFloat();
    g_chargingW   = fields[4].toFloat();
    g_batPct      = fields[5].toFloat();
    g_batWh       = fields[6].toFloat();
    
    return true;
}

// ===== Fungsi kirim ke Laravel =====
void sendToLaravel(const String& csvData) {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("⚠️ WiFi terputus, skip Laravel");
        return;
    }

    HTTPClient http;
    http.setTimeout(8000);
    
    http.begin(laravelApiUrl);
    http.addHeader("Content-Type", "application/json");

    String escapedCsv = escapeJsonString(csvData);
    String jsonPayload = "{\"data\":\"" + escapedCsv + "\"}";

    int httpResponseCode = http.POST(jsonPayload);

    if (httpResponseCode > 0) {
        if (httpResponseCode == 200 || httpResponseCode == 201) {
            Serial.println("  ✅ Laravel OK");
        } else {
            Serial.printf("  ⚠️ Laravel code: %d\n", httpResponseCode);
        }
    } else {
        Serial.printf("  ❌ Laravel error: %s\n", http.errorToString(httpResponseCode).c_str());
    }

    http.end();
}

// ===== Fungsi print data dengan format cantik =====
void printDataFormatted() {
    // Tentukan status berdasarkan suhu
    String status = "✅ NORMAL";
    if (g_temperature >= 65.0f) {
        status = "🔴 DANGER";
    } else if (g_temperature >= 50.0f) {
        status = "🔶 WARNING";
    } else if (g_temperature >= 35.0f) {
        status = "⚠️  WARMING";
    }
    
    Serial.println("\n┌─────────────────────────────────────┐");
    Serial.printf("│ %s              │\n", status.c_str());
    Serial.println("├─────────────────────────────────────┤");
    Serial.printf("│ 🌡️  Suhu      : %6.1f °C         │\n", g_temperature);
    Serial.printf("│ 🔋 Baterai   : %6.2f V (%5.1f%%)  │\n", g_batV, g_batPct);
    Serial.printf("│ ☀️  Panel     : %6.2f V           │\n", g_panelV);
    Serial.printf("│ ⚡ Daya Panel : %6.2f W           │\n", g_panelW);
    Serial.printf("│ 🔌 Charging   : %6.2f W           │\n", g_chargingW);
    Serial.printf("│ 💾 Energi Bat : %6.1f Wh          │\n", g_batWh);
    Serial.println("└─────────────────────────────────────┘");
    
    // Statistik
    Serial.printf("📊 Paket: Total=%lu | Valid=%lu | Invalid=%lu\n\n", 
                  totalPacketsReceived, validPackets, invalidPackets);
}

// ===== Fungsi kirim ke Blynk =====
void sendToBlynk() {
    if (Blynk.connected()) {
        Blynk.virtualWrite(VPIN_TEMP, g_temperature);
        Blynk.virtualWrite(VPIN_BAT_VOLT, g_batV);
        Blynk.virtualWrite(VPIN_PANEL_VOLT, g_panelV);
        Blynk.virtualWrite(VPIN_PANEL_POWER, g_panelW);
        Blynk.virtualWrite(VPIN_CHG_POWER, g_chargingW);
        Blynk.virtualWrite(VPIN_BAT_PCT, g_batPct);
        Blynk.virtualWrite(VPIN_BAT_WH, g_batWh);
        Serial.println("  ✅ Blynk OK");
    } else {
        Serial.println("  ⚠️ Blynk terputus");
    }
}

void loop() {
    // Baca data dari LoRa byte per byte
    while (LoRa.available()) {
        char c = LoRa.read();
        
        // Jika newline, proses data
        if (c == '\n' || c == '\r') {
            if (bufferIndex > 0) {
                loraBuffer[bufferIndex] = '\0';  // Null terminate
                
                String receivedData = String(loraBuffer);
                receivedData.trim();
                
                // Buang karakter kontrol yang tersisa
                receivedData.replace("\r", "");
                receivedData.replace("\n", "");
                
                if (receivedData.length() > 0) {
                    totalPacketsReceived++;
                    
                    Serial.print("📡 [RX] ");
                    Serial.println(receivedData);
                    
                    // Parse data
                    if (parseLoRaData(receivedData)) {
                        validPackets++;
                        newDataAvailable = true;
                        
                        // Tampilkan data
                        printDataFormatted();
                        
                        // Kirim ke Blynk
                        sendToBlynk();
                        
                        // Kirim ke Laravel
                        sendToLaravel(receivedData);
                        
                    } else {
                        invalidPackets++;
                        Serial.println("❌ Parsing gagal\n");
                    }
                }
                
                // Reset buffer
                bufferIndex = 0;
                memset(loraBuffer, 0, BUFFER_SIZE);
            }
        }
        // Jika bukan newline, tambahkan ke buffer
        else if (bufferIndex < BUFFER_SIZE - 1) {
            loraBuffer[bufferIndex++] = c;
        }
        // Buffer overflow protection
        else {
            Serial.println("⚠️ Buffer overflow, reset buffer");
            bufferIndex = 0;
            memset(loraBuffer, 0, BUFFER_SIZE);
        }
    }
    
    // Blynk run
    if (Blynk.connected()) {
        Blynk.run();
    }
    
    // Small delay untuk stabilitas
    delay(10);
}