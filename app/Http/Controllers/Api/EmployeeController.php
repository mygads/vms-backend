<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeResource;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $data_employee = Employee::all();

        return response()->json([
            'success' => true,
            'message' => 'Display List of Employees Successfully',
            'data'    => EmployeeResource::collection($data_employee)
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'           => 'required|string|max:255',
            'nik'            => 'required|string|max:11',
            'email'          => 'required|email|unique:employee,email',
            'department'     => 'required|string|max:255',
            'phone_number'   => 'required|string|max:11',
            'employee_code'  => 'required|string|max:50',
        ]);

        // Create a new employee record with validated data
        $employee = Employee::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'data'    => new EmployeeResource($employee),
        ]);
    }

    public function show($nik)
    {
        // Find the employee by nik
        $employee = Employee::where('nik', $nik)->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Employee data retrieved successfully',
            'data'    => new EmployeeResource($employee),
        ]);
    }

    public function update(Request $request, $nik)
    {
        // Find the employee by nik
        $employee = Employee::where('nik', $nik)->firstOrFail();

        // Validate the fields for updates
        $validatedData = $request->validate([
            'email'          => ['sometimes', 'email', Rule::unique('employee', 'email')->ignore($employee->id)],
            'phone_number'   => 'sometimes|string|max:11',
            'employee_code'  => 'sometimes|string|max:50',
            'department'     => 'sometimes|string|max:255',
        ]);

        // Update the employee record with validated data
        $employee->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'data'    => new EmployeeResource($employee),
        ]);
    }

    public function destroy($nik)
    {
        // Find the employee by nik
        $employee = Employee::where('nik', $nik)->firstOrFail();

        // Delete the employee record
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }
}
