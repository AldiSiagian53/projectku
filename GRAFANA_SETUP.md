# Setup Grafana untuk Solar Monitoring

## Opsi 1: Menggunakan Dashboard Grafana-like dengan Apache ECharts (Recommended - Tidak Perlu Setup Grafana Server)

Dashboard sudah diupdate menggunakan **Apache ECharts** yang memiliki tampilan mirip Grafana. Tidak perlu setup Grafana server terpisah!

### Akses Dashboard:
```
http://localhost:8000/dashboard-grafana
```

### Fitur:
- ✅ Time-series charts untuk semua data sensor
- ✅ Gauge chart untuk battery level
- ✅ Real-time data update
- ✅ Tampilan mirip Grafana
- ✅ Tidak perlu setup server terpisah

---

## Opsi 2: Setup Grafana Server (Jika Ingin Grafana Penuh)

### Step 1: Install Grafana

**Windows:**
1. Download dari: https://grafana.com/grafana/download?platform=windows
2. Install Grafana
3. Start Grafana service atau jalankan: `grafana-server.exe`

**Docker (Recommended):**
```bash
docker run -d -p 3000:3000 --name=grafana grafana/grafana
```

**Atau menggunakan docker-compose:**
Lihat file `docker-compose.grafana.yml`

### Step 2: Akses Grafana
```
http://localhost:3000
```
- Default username: `admin`
- Default password: `admin`

### Step 3: Tambahkan Datasource

1. Login ke Grafana
2. Go to **Configuration** → **Data Sources** → **Add data source**
3. Pilih **Simple JSON**
4. Isi:
   - **Name**: Laravel Solar Monitoring
   - **URL**: `http://localhost:8000/api/grafana`
   - **Access**: Server (default)
5. Klik **Save & Test**

### Step 4: Buat Dashboard

1. Go to **Dashboards** → **New Dashboard**
2. Klik **Add Visualization**
3. Pilih datasource: **Laravel Solar Monitoring**
4. Query:
   - Target: `temperature` (atau metric lain: `battery_voltage`, `panel_power`, `charging_power`, `battery_percent`, `battery_wh`)
5. Save panel
6. Tambahkan panel lain untuk metric yang berbeda

### Metric yang Tersedia:
- `temperature` - Suhu (°C)
- `battery_voltage` atau `bat_v` - Battery Voltage (V)
- `panel_voltage` atau `panel_v` - Panel Voltage (V)
- `panel_power` atau `panel_w` - Panel Power (kW)
- `charging_power` atau `charging_w` atau `energy_in` - Charging Power (kW)
- `battery_percent` atau `bat_percent` atau `battery_level` - Battery Percentage (%)
- `battery_wh` atau `bat_wh` - Battery Watt-hour (Wh)

### Step 5: Embed Dashboard ke Laravel

Jika sudah membuat dashboard di Grafana:

1. Copy Dashboard UID dari URL Grafana
2. Update `.env`:
   ```
   GRAFANA_URL=http://localhost:3000
   GRAFANA_DASHBOARD_UID=your-dashboard-uid
   ```
3. Akses: `http://localhost:8000/dashboard-grafana`

---

## Opsi 3: Docker Compose (All-in-one)

File `docker-compose.grafana.yml` sudah disediakan untuk setup Grafana dengan mudah.

```bash
docker-compose -f docker-compose.grafana.yml up -d
```

---

## API Endpoints yang Tersedia

### 1. Grafana Simple JSON Datasource
```
POST /api/grafana/query
POST /api/grafana/search
POST /api/grafana/annotations
POST /api/grafana/tag-keys
POST /api/grafana/tag-values
```

### 2. Time-series API (Alternative)
```
GET /api/grafana/timeseries?hours=24
```
Returns JSON dengan semua metrics dalam format time-series.

---

## Troubleshooting

### Grafana tidak bisa connect ke Laravel API
- Pastikan Laravel server berjalan: `php artisan serve --host=0.0.0.0`
- Pastikan CORS sudah dihandle (cek `config/cors.php`)
- Test API langsung: `curl http://localhost:8000/api/grafana/timeseries`

### Tidak ada data di Grafana
- Pastikan sudah ada data di database: `php artisan tinker` → `\App\Models\SensorData::count()`
- Pastikan Arduino sudah mengirim data
- Cek time range di Grafana query

### Dashboard ECharts tidak load
- Buka browser console untuk cek error
- Pastikan API endpoint bisa diakses: `/api/grafana/timeseries`
- Pastikan ada data di database

---

## Rekomendasi

**Untuk development/testing cepat:**
→ Gunakan **Opsi 1** (Dashboard ECharts) - sudah tersedia dan tidak perlu setup tambahan

**Untuk production dengan fitur Grafana penuh:**
→ Gunakan **Opsi 2** (Grafana Server) untuk monitoring yang lebih powerful


