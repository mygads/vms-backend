<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Visitor;
use Illuminate\Http\Request;
use App\Http\Resources\VisitorResource;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Rawilk\Printing\Facades\Printing;

class VisitorController
{
    // View List Data Visitor
    public function index()
    {
        $data_visitor = Visitor::whereDate('visitor_date', Carbon::today())
                                ->orderby('visitor_checkin', 'asc')
                                ->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List Visitor Successfully',
            'data' => VisitorResource::collection($data_visitor)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string|max:255',
            'visitor_date' => 'required|date',
            'visitor_from' => 'nullable|string|max:255',
            'visitor_host' => 'required|string|max:255',
            'visitor_needs' => 'nullable|string|max:255',
            'visitor_amount' => 'nullable|integer',
            'visitor_vehicle' => 'nullable|string|max:10',
        ]);

        $prefix = '';
        switch ($request->visitor_needs) {
            case 'Meeting':
                $prefix = 'MT';
                break;
            case 'Delivery':
                $prefix = 'DL';
                break;
            case 'Contractor':
                $prefix = 'CT';
                break;
            default:
                $prefix = 'VG';
        }

        $latestVisitor = Visitor::where('visitor_id', 'like', "$prefix%")
            ->orderBy('visitor_id', 'desc')
            ->first();

        if ($latestVisitor) {
            $lastNumber = (int)substr($latestVisitor->visitor_id, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $visitorId = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        $visitor = Visitor::create([
            'visitor_id'       => $visitorId,
            'visitor_name'     => $request->visitor_name,
            'visitor_from'     => $request->visitor_from,
            'visitor_host'     => $request->visitor_host,
            'visitor_needs'    => $request->visitor_needs,
            'visitor_amount'   => $request->visitor_amount,
            'visitor_vehicle'  => $request->visitor_vehicle,
            'department'       => $request->department,
            'visitor_date'     => Carbon::today(),
            'visitor_checkin'  => Carbon::now(),
        ]);

        // Generate QR code using endroid/qr-code
        $qrCode = new QrCode($visitor->visitor_id);
        $writer = new PngWriter();
        $qrCodeData = $writer->write($qrCode)->getString();
        $qrCodeDataUrl = 'data:image/png;base64,' . base64_encode($qrCodeData);

        // Generate PDF from the blade view
        $pdf = Pdf::loadView('print_receipt', ['visitor' => $visitor, 'qrCodeDataUrl' => $qrCodeDataUrl]);
        $filePath = storage_path("app/public/receipts/{$visitorId}.pdf");
        $pdf->save($filePath);

        // composer require rawilk/laravel-printing composer --ignore-platform-req=ext-intl composer require mike42/escpos-php

        // Printer ID (replace with your thermal printer's ID)
        $printerId = 'YOUR_PRINTER_ID';

        // Use Laravel Printing to send the PDF to the thermal printer
        Printing::newPrintTask()
            ->printer($printerId)
            ->file($filePath)
            ->send();

        // Temporarily comment out the deletion for testing
        unlink($filePath);

        return response()->json([
            'success' => true,
            'message' => "\"{$visitor->visitor_name}\" Check In",
            'data' => new VisitorResource($visitor)
        ]);
    }

    public function update($visitor_id)
    {
        $visitor = Visitor::where('visitor_id', $visitor_id)->firstOrFail();

        $visitor->update([
            'visitor_checkout' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '"' . $visitor->visitor_name . '" Check Out',
            'data' => new VisitorResource($visitor)
        ]);
    }

    public function printVisitor($visitor_id)
    {
        // Fetch visitor data based on the visitor ID
        $visitor = Visitor::find($visitor_id);

        if (!$visitor) {
            return response()->json(['error' => 'Visitor not found'], 404);
        }

        // Return visitor data as JSON
        return response()->json($visitor);
    }

    public function display()
    {
        $data_visitor = Visitor::with('visitor')->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List Visitor Successfully',
            'data' => VisitorResource::collection($data_visitor)
        ]);
    }
}
