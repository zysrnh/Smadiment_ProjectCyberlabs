<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site under maintenance — SMADIMENT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            height: 100vh;
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #2d3748;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .container {
            text-align: center;
            max-width: 700px;
            width: 100%;
        }

        /* ── Gear Illustration ── */
        .gears-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 32px;
            position: relative;
            height: 140px;
        }

        .gear {
            position: absolute;
            animation: spin 10s linear infinite;
        }

        /* Roda Gigi Abu-abu (Besar) */
        .gear-large {
            width: 100px;
            height: 100px;
            fill: #e2e8f0;
            left: calc(50% - 60px);
            z-index: 1;
        }

        /* Roda Gigi Kuning (Kecil) */
        .gear-small {
            width: 70px;
            height: 70px;
            fill: #f6ad55;
            left: calc(50% + 5px);
            top: 20px;
            z-index: 2;
            animation-direction: reverse;
            animation-duration: 6s;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ── Typography ── */
        h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 800;
            color: #3b4559;
            margin-bottom: 24px;
            line-height: 1.1;
            letter-spacing: -1px;
        }

        p {
            font-size: 1.25rem;
            color: #718096;
            margin-bottom: 48px;
            max-width: 550px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        /* ── Buttons ── */
        .btn-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 36px;
            border-radius: 8px;
            font-size: 1.05rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            font-family: inherit;
            min-width: 160px;
        }

        .btn-dark {
            background-color: #212836;
            color: #ffffff;
            border: 1px solid #212836;
        }

        .btn-dark:hover {
            background-color: #1a202c;
            transform: translateY(-1px);
        }

        .btn-outline {
            background-color: #f8fafc;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }

        .btn-outline:hover {
            background-color: #edf2f7;
            border-color: #cbd5e0;
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            h1 { font-size: 2.5rem; letter-spacing: 0; }
            p { font-size: 1.1rem; }
            .btn-group { flex-direction: column; width: 100%; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Animated Gears -->
        <div class="gears-container">
            <!-- Large Gear -->
            <svg class="gear gear-large" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.47,5.34 14.86,5.1L14.47,2.44C14.43,2.21 14.23,2.05 14,2.05H10C9.77,2.05 9.57,2.21 9.53,2.44L9.14,5.1C8.53,5.34 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.53,18.66 9.14,18.9L9.53,21.56C9.57,21.79 9.77,21.95 10,21.95H14C14.23,21.95 14.43,21.79 14.47,21.56L14.86,18.9C15.47,18.66 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
            </svg>
            <!-- Small Gear -->
            <svg class="gear gear-small" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.47,5.34 14.86,5.1L14.47,2.44C14.43,2.21 14.23,2.05 14,2.05H10C9.77,2.05 9.57,2.21 9.53,2.44L9.14,5.1C8.53,5.34 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.53,18.66 9.14,18.9L9.53,21.56C9.57,21.79 9.77,21.95 10,21.95H14C14.23,21.95 14.43,21.79 14.47,21.56L14.86,18.9C15.47,18.66 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
            </svg>
        </div>

        <h1>Site is under maintenance</h1>
        <p>We're working hard to improve the user experience. Stay tuned!</p>

        <div class="btn-group">
            
            <button onclick="location.reload()" class="btn btn-outline">Reload</button>
        </div>
    </div>

</body>
</html>
