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
            width: 2.8in;
            height: 6in;
            padding: 10px;
            background-color: #FFFFFF;
            font-family: Arial, sans-serif;
            color: #374151;
            margin: 0;
        }
        .logo-container {
            text-align: center;
        }
        .logo {
            width: 84px;
            display: inline-block;
            margin-bottom: 5px;
        }
        .qr-code {
            text-align: center;
            margin-bottom: 5px;
        }
        .qr-code img {
            width: 40%; /* Adjust the QR code size to 50% */
        }
        .visitor-id {
            text-align: center;
            color: #1F2937;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-container {
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .info-text {
            font-size: 12px;
            color: #374151;
            margin-bottom: 8px;
        }
        .bold-text {
            font-weight: bold;
        }
        .signature-section {
            margin-top: 30px;
            margin-bottom: 30px
        }

        .signature-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            color: #374151;
            margin-bottom: 5px;
        }

        .signature-box {
            width: 100%;
        }

        .signature-label {
            font-size: 10px;
            color: #6B7280;
            margin-bottom: 40px; /* Increased spacing to move the line down */
        }

        .signature-line {
            border-top: 1px solid #4B5563;
            width: 80%;
            margin: 0 auto;
            margin-top: 20px;
        }
        .notice {
            text-align: center;
            font-size: 10px;
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
    @php
        $image_path = public_path('images/logo-sanoh.png');
        $image_data = base64_encode(file_get_contents($image_path));
        $logo_src = 'data:image/png;base64,' . $image_data;
    @endphp
    <div class="logo-container">
        <img src="{{ $logo_src }}" class="logo" alt="Logo">
    </div>

    <!-- QR Code -->
    <div class="qr-code">
        <img src="{{ $qrCodeDataUrl }}" alt="QR Code">
    </div>

    <!-- Visitor ID -->
    <div class="visitor-id">{{ $visitor->visitor_id }}</div>

    <!-- Visitor Information -->
    <div class="info-container">
        <div class="info-text">
            <span class="bold-text">Tanggal Masuk:</span> {{ $visitor->visitor_checkin }}
        </div>
        <div class="info-text">
            <span class="bold-text">Nama Tamu:</span> {{ $visitor->visitor_name }}
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
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- Signature cells -->
                <td style="border: none; width: 33%; text-align: center;">
                    <div class="signature-box">
                        <div class="signature-label">Visitor</div>
                        <div class="signature-line"></div>
                    </div>
                </td>
                <td style="border: none; width: 33%; text-align: center;">
                    <div class="signature-box">
                        <div class="signature-label">Host</div>
                        <div class="signature-line"></div>
                    </div>
                </td>
                <td style="border: none; width: 33%; text-align: center;">
                    <div class="signature-box">
                        <div class="signature-label">Security</div>
                        <div class="signature-line"></div>
                    </div>
                </td>
            </tr>
        </table>
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
