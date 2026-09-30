<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Habis — SMADIMENT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            max-width: 480px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
        }

        .icon-wrapper {
            width: 64px;
            height: 64px;
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            color: #ef4444;
        }

        .icon-wrapper svg {
            width: 32px;
            height: 32px;
        }

        .error-code {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #64748b;
            background-color: #f1f5f9;
            padding: 4px 10px;
            border-radius: 4px;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            background-color: #038047;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 4px;
            border: 1px solid #038047;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .btn-primary:hover {
            background-color: #026738;
            border-color: #026738;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            background-color: #ffffff;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .btn-secondary:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        .auto-redirect {
            margin-top: 20px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrapper">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <div>
            <span class="error-code">419 · Sesi Berakhir</span>
        </div>

        <h1>Maaf, Sesi Anda Telah Habis</h1>
        
        <p>Sesi login atau keamanan halaman Anda telah berakhir. Harap login kembali untuk melanjutkan aktivitas Anda di sistem.</p>

        <div class="actions">
            <a href="{{ route('user.login') }}" onclick="goToLogin(); return false;" class="btn-primary" id="loginBtn">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Login Kembali
            </a>
            <button onclick="goToLogin(); return false;" class="btn-secondary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Muat Ulang Halaman
            </button>
        </div>

        <div class="auto-redirect" id="countdownWrapper">
            Otomatis dialihkan ke halaman login dalam <span id="countdown">4</span> detik...
        </div>
    </div>

    <script>
        const loginUrl = "{{ route('user.login') }}";

        function goToLogin() {
            window.location.replace(loginUrl + (loginUrl.includes('?') ? '&' : '?') + 'r=' + Date.now());
        }

        let seconds = 4;
        const countdownEl = document.getElementById('countdown');

        const interval = setInterval(() => {
            seconds--;
            if (countdownEl) countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                goToLogin();
            }
        }, 1000);
    </script>
</body>
</html>
