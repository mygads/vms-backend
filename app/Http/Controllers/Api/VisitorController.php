<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Visitor;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\VisitorResource;
use Illuminate\Support\Facades\Storage;

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
            // 'visitor_img' => 'required|string', // Base64 image string
        ]);

        // Generate visitor_id based on visitor_needs
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
                $prefix = 'VG'; // Default prefix if none of the specified needs match
        }

        // Get the latest visitor ID with the specified prefix
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

        // Process the base64 image if it exists
        // $imagePath = null;
        // if ($request->visitor_img) {
        //     $imageData = $request->visitor_img;

        //     // Extract base64 data
        //     $imageParts = explode(";base64,", $imageData);
        //     $imageBase64 = base64_decode($imageParts[1]);

        //     // Use visitor_name to create a unique name for the image
        //     $sanitizedVisitorName = Str::slug($request->visitor_name, '_');
        //     $imageName = $sanitizedVisitorName . '_' . uniqid() . '.jpeg';
        //     $filePath = "visitor_images/{$imageName}";

        //     // Store the image in the public disk
        //     Storage::disk('public')->put($filePath, $imageBase64);
        //     $imagePath = $filePath;
        // }

        // Create a new visitor record
        $visitor = Visitor::create([
            'visitor_id'       => $visitorId,
            'visitor_name'     => $request->visitor_name,
            'visitor_from'     => $request->visitor_from,
            'visitor_host'     => $request->visitor_host,
            'visitor_needs'    => $request->visitor_needs,
            'visitor_amount'   => $request->visitor_amount,
            'visitor_vehicle'  => $request->visitor_vehicle,
            'department'       => $request->department,
            // 'visitor_img'      => $imagePath,
            'visitor_date'     => Carbon::today(),
            'visitor_checkin'  => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '"' . $visitor->visitor_name . '" Check In',
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
