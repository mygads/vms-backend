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
use charlieuki\ReceiptPrinter\ReceiptPrinter as ReceiptPrinter;

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
        // Validate the incoming request data
        $request->validate([
            'visitor_name'    => 'required|string|max:255',
            'visitor_date'    => 'required|date',
            'visitor_from'    => 'nullable|string|max:255',
            'visitor_host'    => 'required|string|max:255',
            'visitor_needs'   => 'nullable|string|max:255',
            'visitor_amount'  => 'nullable|integer',
            'visitor_vehicle' => 'nullable|string|max:10',
        ]);

        // Determine the prefix based on visitor needs
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

        // Get the current year in two-digit format
        $currentYearShort = Carbon::now()->format('y'); // e.g., '24' for 2024

        // Construct the visitorPrefix including the year
        $visitorPrefix = $prefix . $currentYearShort; // e.g., 'MT24'

        // Retrieve the latest visitor ID with the same prefix
        $latestVisitor = Visitor::where('visitor_id', 'like', "$visitorPrefix%")
            ->orderBy('visitor_id', 'desc')
            ->first();

        // Calculate the new visitor number
        $newNumber = $latestVisitor
            ? ((int)substr($latestVisitor->visitor_id, strlen($visitorPrefix))) + 1
            : 1;

        // Create the new visitor ID
        $visitorId = $visitorPrefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Create the visitor record in the database
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

        // Return a JSON response without QR code or view rendering
        return response()->json([
            'success' => true,
            'message' => "\"{$visitor->visitor_name}\" Check In",
            'data'    => VisitorResource::make($visitor)
        ]);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'visitor_name'    => 'required|string|max:255',
    //         'visitor_date'    => 'required|date',
    //         'visitor_from'    => 'nullable|string|max:255',
    //         'visitor_host'    => 'required|string|max:255',
    //         'visitor_needs'   => 'nullable|string|max:255',
    //         'visitor_amount'  => 'nullable|integer',
    //         'visitor_vehicle' => 'nullable|string|max:10',
    //     ]);

    //     $prefix = '';
    //     switch ($request->visitor_needs) {
    //         case 'Meeting':
    //             $prefix = 'MT';
    //             break;
    //         case 'Delivery':
    //             $prefix = 'DL';
    //             break;
    //         case 'Contractor':
    //             $prefix = 'CT';
    //             break;
    //         default:
    //             $prefix = 'VG';
    //     }

    //     $latestVisitor = Visitor::where('visitor_id', 'like', "$prefix%")
    //         ->orderBy('visitor_id', 'desc')
    //         ->first();

    //     $newNumber = $latestVisitor
    //         ? ((int)substr($latestVisitor->visitor_id, 2)) + 1
    //         : 1;

    //     $visitorId = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

    //     $visitor = Visitor::create([
    //         'visitor_id'       => $visitorId,
    //         'visitor_name'     => $request->visitor_name,
    //         'visitor_from'     => $request->visitor_from,
    //         'visitor_host'     => $request->visitor_host,
    //         'visitor_needs'    => $request->visitor_needs,
    //         'visitor_amount'   => $request->visitor_amount,
    //         'visitor_vehicle'  => $request->visitor_vehicle,
    //         'department'       => $request->department,
    //         'visitor_date'     => Carbon::today(),
    //         'visitor_checkin'  => Carbon::now(),
    //     ]);

    //     // Generate QR code data URL
    //     $qrCode = new QrCode($visitor->visitor_id);
    //     $writer = new PngWriter();
    //     $qrCodeData = $writer->write($qrCode)->getString();
    //     $qrCodeDataUrl = 'data:image/png;base64,' . base64_encode($qrCodeData);

    //     // Return the Blade view that includes the JavaScript for printing
    //     return view('print_receipt', [
    //         'visitor'       => $visitor,
    //         'qrCodeDataUrl' => $qrCodeDataUrl
    //     ]);
    // }

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
