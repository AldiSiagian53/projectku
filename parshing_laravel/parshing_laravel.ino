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

// ===== Fungsi parsing data LoRa =====
void parseLoRaData(const String& rawData) {
    int commaCount = 0;
    for (char c : rawData) if (c == ',') commaCount++;
    if (commaCount != 6) {
        Serial.println("❌ Format data tidak valid (comma != 6)");
        return;
    }

    int i1 = rawData.indexOf(',');
    int i2 = rawData.indexOf(',', i1 + 1);
    int i3 = rawData.indexOf(',', i2 + 1);
    int i4 = rawData.indexOf(',', i3 + 1);
    int i5 = rawData.indexOf(',', i4 + 1);
    int i6 = rawData.indexOf(',', i5 + 1);

    g_temperature = rawData.substring(0, i1).toFloat();
    g_batV       = rawData.substring(i1 + 1, i2).toFloat();
    g_panelV     = rawData.substring(i2 + 1, i3).toFloat();
    g_panelW     = rawData.substring(i3 + 1, i4).toFloat();
    g_chargingW  = rawData.substring(i4 + 1, i5).toFloat();
    g_batPct     = rawData.substring(i5 + 1, i6).toFloat();
    g_batWh      = rawData.substring(i6 + 1).toFloat();

    newDataAvailable = true;
    Serial.println("📡 Data berhasil diparsing → siap kirim ke Blynk & Laravel");
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

    // Format JSON: {"data": "csv_string"}
    String jsonPayload = "{\"data\":\"" + csvData + "\"}";
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
    // Debug: tampilkan RAW data yang masuk
    if (LoRa.available()) {
        String rawData = LoRa.readStringUntil('\n');
        rawData.trim();

        Serial.print("<< RAW LORA: ");
        Serial.println(rawData);

        if (rawData.length() > 0) {
            parseLoRaData(rawData);
            
            // Langsung kirim CSV ke Laravel juga
            sendToLaravel(rawData);
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