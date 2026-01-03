<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grafana Dashboard - Solar Monitoring</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- FONT MONTSERRAT --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    {{-- TAILWIND --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: "Montserrat", sans-serif !important;
            margin: 0;
            padding: 0;
        }
        .grafana-container {
            width: 100%;
            height: 100vh;
            border: none;
        }
    </style>
</head>
<body>
    {{-- SIDEBAR COMPONENT --}}
    <x-sidebar />

    {{-- GRAFANA IFRAME --}}
    <main class="md:ml-64 w-full" style="height: calc(100vh - 0px);">
        <div class="p-4">
            <h1 class="text-2xl font-bold mb-4">Solar Monitoring - Grafana Dashboard</h1>
            <div class="bg-white rounded-lg shadow-lg" style="height: calc(100vh - 100px);">
                <iframe 
                    src="{{ $grafanaUrl }}/d/{{ $dashboardUid }}?orgId=1&kiosk=tv" 
                    class="grafana-container w-full h-full rounded-lg"
                    frameborder="0"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </main>

    <script>
        // Auto-refresh iframe setiap 30 detik untuk update data
        setInterval(() => {
            const iframe = document.querySelector('.grafana-container');
            if (iframe) {
                iframe.src = iframe.src;
            }
        }, 30000);
    </script>
</body>
</html>





