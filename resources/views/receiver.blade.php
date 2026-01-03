<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receiver Control - Eco Power Monitoring</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    {{-- FONT MONTSERRAT --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    {{-- REMIX ICON --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    {{-- TAILWIND --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: "Montserrat", sans-serif !important;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .receiver-button {
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .receiver-button:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }

        .receiver-button:active {
            transform: scale(0.98);
        }

        .status-indicator {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .status-active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .status-inactive {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="text-center w-full max-w-md">
            {{-- HEADER --}}
            <div class="mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4 backdrop-blur-sm">
                    <i class="ri-transmit-line text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">
                    Receiver Control
                </h1>
                <p class="text-white/80 text-lg">
                    Kontrol penerimaan data dari transmitter
                </p>
            </div>

            {{-- STATUS INDICATOR --}}
            <div id="statusIndicator" class="mb-8">
                <div class="inline-flex items-center px-6 py-3 rounded-full bg-white/20 backdrop-blur-sm text-white">
                    <span id="statusDot" class="w-3 h-3 rounded-full mr-3 status-indicator"></span>
                    <span id="statusText" class="font-semibold">Loading...</span>
                </div>
            </div>

            {{-- MAIN BUTTON --}}
            <button 
                id="receiverButton" 
                class="receiver-button w-full py-6 px-8 rounded-2xl text-white text-2xl font-bold flex items-center justify-center space-x-4 mb-6 focus:outline-none focus:ring-4 focus:ring-white/50"
            >
                <i id="buttonIcon" class="ri-play-circle-line text-3xl"></i>
                <span id="buttonText">Loading...</span>
            </button>

            {{-- INFO MESSAGE --}}
            <div id="infoMessage" class="mt-8 p-4 bg-white/10 backdrop-blur-sm rounded-xl text-white/90 text-sm">
                <p id="infoText" class="leading-relaxed">
                    Status sedang dimuat...
                </p>
            </div>

            {{-- LAST UPDATE --}}
            <div class="mt-6 text-white/60 text-xs">
                <p>Terakhir diperbarui: <span id="lastUpdate">-</span></p>
            </div>
        </div>
    </div>

    <script>
        const statusUrl = '{{ route("receiver.status") }}';
        let currentStatus = '{{ $receiver_status }}';
        let isLoading = false;

        // Inisialisasi tampilan berdasarkan status
        function updateUI(status) {
            const button = document.getElementById('receiverButton');
            const buttonText = document.getElementById('buttonText');
            const buttonIcon = document.getElementById('buttonIcon');
            const statusText = document.getElementById('statusText');
            const statusDot = document.getElementById('statusDot');
            const infoText = document.getElementById('infoText');

            if (status === 'active') {
                // Status Aktif - Tombol untuk Stop
                button.className = 'receiver-button status-active w-full py-6 px-8 rounded-2xl text-white text-2xl font-bold flex items-center justify-center space-x-4 mb-6 focus:outline-none focus:ring-4 focus:ring-white/50';
                buttonText.textContent = 'Stop Receiver';
                buttonIcon.className = 'ri-stop-circle-line text-3xl';
                statusText.textContent = 'Receiver Aktif';
                statusDot.className = 'w-3 h-3 rounded-full mr-3 status-indicator bg-green-400';
                infoText.textContent = 'Receiver sedang aktif dan menerima data dari transmitter. Data yang masuk akan diproses dan disimpan ke database. Klik tombol untuk menghentikan receiver.';
            } else {
                // Status Nonaktif - Tombol untuk Start
                button.className = 'receiver-button status-inactive w-full py-6 px-8 rounded-2xl text-white text-2xl font-bold flex items-center justify-center space-x-4 mb-6 focus:outline-none focus:ring-4 focus:ring-white/50';
                buttonText.textContent = 'Start Receiver';
                buttonIcon.className = 'ri-play-circle-line text-3xl';
                statusText.textContent = 'Receiver Nonaktif';
                statusDot.className = 'w-3 h-3 rounded-full mr-3 bg-red-400';
                infoText.textContent = 'Receiver sedang nonaktif. Data dari transmitter tidak akan diproses. Di serial monitor akan menampilkan "N/a" karena tidak ada variabel yang diterima. Klik tombol untuk mengaktifkan receiver.';
            }

            currentStatus = status;
            updateLastUpdateTime();
        }

        // Update waktu terakhir diperbarui (WIB - Asia/Jakarta)
        function updateLastUpdateTime() {
            const now = new Date();
            
            // Format waktu dengan timezone Asia/Jakarta (WIB)
            const timeString = now.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'Asia/Jakarta'
            }) + ' WIB';
            
            document.getElementById('lastUpdate').textContent = timeString;
        }

        // Toggle receiver status
        async function toggleReceiver() {
            if (isLoading) return;

            isLoading = true;
            const button = document.getElementById('receiverButton');
            const buttonText = document.getElementById('buttonText');
            button.disabled = true;
            buttonText.textContent = 'Memproses...';

            try {
                const endpoint = currentStatus === 'active' ? '{{ route("receiver.stop") }}' : '{{ route("receiver.start") }}';
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    // Update status
                    const newStatus = data.receiver_status;
                    updateUI(newStatus);

                    // Tampilkan pesan sukses
                    showMessage('success', data.message);
                } else {
                    showMessage('error', data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('error', 'Gagal menghubungi server. Pastikan koneksi internet stabil.');
            } finally {
                isLoading = false;
                button.disabled = false;
            }
        }

        // Tampilkan pesan sementara
        function showMessage(type, message) {
            const infoMessage = document.getElementById('infoMessage');
            const infoText = document.getElementById('infoText');
            
            if (type === 'success') {
                infoMessage.className = 'mt-8 p-4 bg-green-500/20 backdrop-blur-sm rounded-xl text-white text-sm border border-green-400/50';
            } else {
                infoMessage.className = 'mt-8 p-4 bg-red-500/20 backdrop-blur-sm rounded-xl text-white text-sm border border-red-400/50';
            }
            
            infoText.textContent = message;

            // Kembalikan ke info normal setelah 3 detik
            setTimeout(() => {
                updateUI(currentStatus);
            }, 3000);
        }

        // Cek status receiver secara berkala
        async function checkStatus() {
            try {
                const response = await fetch(statusUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.status === 'success' && data.receiver_status !== currentStatus) {
                    updateUI(data.receiver_status);
                }
            } catch (error) {
                console.error('Error checking status:', error);
            }
        }

        // Event listener untuk button
        document.getElementById('receiverButton').addEventListener('click', toggleReceiver);

        // Inisialisasi UI saat halaman dimuat
        updateUI(currentStatus);

        // Cek status setiap 5 detik
        setInterval(checkStatus, 5000);
    </script>
</body>
</html>

