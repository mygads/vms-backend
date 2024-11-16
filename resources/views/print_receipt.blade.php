<!DOCTYPE html>
<html>
<head>
    <title>Print Receipt</title>
    <style>
        @page {
            size: 3in 6in; /* Set custom page size */
            margin: 0; /* Remove default margins */
        }
        body {
            width: 3in;
            height: 6in;
            padding: 10px;
            background-color: #FFFFFF;
            font-family: Arial, sans-serif;
            color: #374151;
            margin: 0;
        }
        .logo {
            width: 72px;
            margin: 0 auto 10px auto;
            display: block;
        }
        .qr-code {
            text-align: center;
            margin-bottom: 5px;
        }
        .qr-code img {
            width: 50%; /* Adjust the QR code size to 50% */
        }
        .visitor-id {
            text-align: center;
            color: #1F2937;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-container {
            margin-bottom: 5px;
        }
        .info-text {
            font-size: 10px;
            color: #374151;
            margin-bottom: 8px;
        }
        .bold-text {
            font-weight: bold;
        }
        .signature-section {
            margin-top: 10px;
        }
        .signature-title {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            color: #374151;
            margin-bottom: 5px;
        }
        .signature-container {
            display: flex;
            justify-content: space-between;
            flex-direction: row;
        }
        .signature-box {
            width: 30%;
            align-items: 'center';
        }
        .signature-label {
            font-size: 8px;
            color: #6B7280;
            margin-bottom: 20px;
        }
        .signature-line {
            border-top: 1px solid #4B5563;
            width: 100%;
            margin-top: auto;
        }
        .notice {
            text-align: center;
            font-size: 8px;
            color: #374151;
            margin-top: 10px;
        }
        .italic-text {
            font-style: italic;
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.location.href = '/tablet';
            }
        };
    </script>
</head>
<body>
    <!-- Logo -->
    <img src="{{ asset('public/images/logo-sanoh.png') }}" class="logo" alt="Logo">

    <!-- QR Code -->
    <div class="qr-code">
        <img src="{{ $qrCodeDataUrl }}" alt="QR Code">
    </div>

    <!-- Visitor ID -->
    <div class="visitor-id">{{ $visitor->visitor_id }}</div>

    <!-- Visitor Information -->
    <div class="info-container">
        <div class="info-text">
            <span class="bold-text">Nama:</span> {{ $visitor->visitor_name }}
        </div>
        <div class="info-text">
            <span class="bold-text">Asal Perusahaan:</span> {{ $visitor->visitor_from }}
        </div>
        <div class="info-text">
            <span class="bold-text">Host:</span> {{ $visitor->visitor_host }} - {{ $visitor->department }}
        </div>
        <div class="info-text">
            <span class="bold-text">Keperluan:</span> {{ $visitor->visitor_needs }}
        </div>
        <div class="info-text">
            <span class="bold-text">Jumlah Tamu:</span> {{ $visitor->visitor_amount }}
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-title">TANDA TANGAN</div>
        <div class="signature-container">
            <div class="signature-box">
                <div class="signature-label">Visitor</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Host</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Security</div>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>

    <!-- Notice -->
    <div class="notice">
        <div class="bold-text">
            Dilarang mengambil gambar atau foto di area perusahaan tanpa izin
        </div>
        <div class="italic-text">
            (Taking pictures or photos in the company area without permission is prohibited)
        </div>
    </div>

    <!-- Note -->
    <div class="notice">
        <div class="bold-text">
            NOTE: Form harus kembali ke pos security
        </div>
        <div class="italic-text">
            (Please return this form to security post)
        </div>
    </div>
</body>
</html>