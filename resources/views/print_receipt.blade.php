<!DOCTYPE html>
<html>
<head>
    <title>Print Receipt</title>
    <style>
        /* Existing CSS styles */
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
            margin-bottom: 30px;
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
            margin-bottom: 40px;
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
    <!-- Include the Epson ePOS SDK JavaScript file -->
    <script type="text/javascript" src="{{ asset('epson_js_sdk/epos-2.27.0.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Printer's IP address (replace with your printer's IP)
            var printerIp = '192.168.1.100';

            // Initialize the ePOS-Print API
            var ePosDev = new epson.ePOSDevice();

            ePosDev.connect(printerIp, 8008, function(data) {
                if (data === 'OK' || data === 'SSL_CONNECT_OK') {
                    // Connection successful
                    ePosDev.createDevice('local_printer', ePosDev.DEVICE_TYPE_PRINTER, {'crypto': false, 'buffer': false}, deviceCallback);
                } else {
                    // Connection failed
                    alert('Failed to connect to the printer: ' + data);
                    // Redirect or perform other actions after failure
                    window.location.href = '/tablet';
                }
            });

            function deviceCallback(deviceObj, errorCode) {
                if (deviceObj === null) {
                    alert('Failed to create device object: ' + errorCode);
                    // Redirect or perform other actions after failure
                    window.location.href = '/tablet';
                    return;
                }

                var printer = deviceObj;
                printer.onreceive = function(response) {
                    if (response.success) {
                        console.log('Print Success');
                    } else {
                        console.log('Print Failed: ' + response.code);
                    }
                    // Disconnect after printing
                    ePosDev.disconnect();
                    // Redirect or perform other actions after printing
                    window.location.href = '/tablet';
                };

                // Start building the print data
                printer.addTextAlign(printer.ALIGN_CENTER);

                // Add logo if available
                @php
                    $logo_path = public_path('images/logo-sanoh.png');
                    if (file_exists($logo_path)) {
                        $logo_data = base64_encode(file_get_contents($logo_path));
                        $logo_data_uri = 'data:image/png;base64,' . $logo_data;
                    } else {
                        $logo_data_uri = '';
                    }
                @endphp

                @if ($logo_data_uri)
                    var logo = new Image();
                    logo.src = '{{ $logo_data_uri }}';
                    logo.onload = function() {
                        printer.addImage(logo, 0, 0, logo.width, logo.height, printer.COLOR_1, printer.MODE_MONO);
                        addReceiptContent(printer);
                    };
                @else
                    addReceiptContent(printer);
                @endif

                function addReceiptContent(printer) {
                    // Visitor ID
                    printer.addTextSize(2, 2);
                    printer.addTextStyle(false, false, true, printer.COLOR_1);
                    printer.addText('{{ $visitor->visitor_id }}\n');
                    printer.addTextSize(1, 1);
                    printer.addTextStyle(false, false, false, printer.COLOR_1);

                    // Visitor Information
                    printer.addText('Tanggal Masuk: {{ $visitor->visitor_checkin }}\n');
                    printer.addText('Nama Tamu: {{ $visitor->visitor_name }}\n');
                    printer.addText('Asal Perusahaan: {{ $visitor->visitor_from }}\n');
                    printer.addText('Host: {{ $visitor->visitor_host }} - {{ $visitor->department }}\n');
                    printer.addText('Keperluan: {{ $visitor->visitor_needs }}\n');
                    printer.addText('Jumlah Tamu: {{ $visitor->visitor_amount }}\n');

                    // QR Code
                    printer.addFeedLine(1);
                    printer.addSymbol('{{ $visitor->visitor_id }}', printer.SYMBOL_QRCODE_MODEL_2, printer.LEVEL_DEFAULT, 8, 8, printer.PARAM_DEFAULT);
                    printer.addFeedLine(1);

                    // Signature Section
                    printer.addText('TANDA TANGAN\n');
                    printer.addText('-------------------------------\n');
                    printer.addText('Visitor         Host          Security\n');
                    printer.addFeedLine(3); // Space for signatures

                    // Footer Notices
                    printer.addTextAlign(printer.ALIGN_CENTER);
                    printer.addTextStyle(false, false, true, printer.COLOR_1);
                    printer.addText('Dilarang mengambil gambar atau foto di area perusahaan tanpa izin\n');
                    printer.addTextStyle(false, false, false, printer.COLOR_1);
                    printer.addText('(Taking pictures or photos in the company area without permission is prohibited)\n');
                    printer.addFeedLine(1);
                    printer.addTextStyle(false, false, true, printer.COLOR_1);
                    printer.addText('NOTE: Form harus kembali ke pos security\n');
                    printer.addTextStyle(false, false, false, printer.COLOR_1);
                    printer.addText('(Please return this form to security post)\n');
                    printer.addTextAlign(printer.ALIGN_LEFT);

                    // Cut and Print
                    printer.addCut(printer.CUT_FEED);
                    printer.send();
                }
            }
        });
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
