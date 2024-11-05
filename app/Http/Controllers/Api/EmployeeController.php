<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Resources\EmployeeResource;
use App\Http\Controllers\Controller;

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
            'nik'            => 'required|integer|max:11', // nik is not unique
            'email'          => 'required|email|unique:employees,email',
            'department'     => 'required|string|max:255',
            'phone_number'   => 'required|integer|max:11',
            'employee_code'  => 'required|string|max:50', // employee_code is not unique
        ]);

        // Create a new employee record with validated data
        $employee = Employee::create($validatedData);

        // Return a JSON response
        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'data'    => new EmployeeResource($employee),
        ]);
    }

    public function edit($name)
    {
        // Find the employee by name
        $employee = Employee::where('name', $name)->first();

        return response()->json([
            'success' => true,
            'message' => 'Employee data retrieved successfully',
            'data'    => new EmployeeResource($employee),
        ]);
    }

    public function update(Request $request, $name)
    {
        // Find the employee by name
        $employee = Employee::where('name', $name)->first();

        // Validate only phone number and employee code fields for updates
        $validatedData = $request->validate([
            'phone_number'   => 'required|integer|max:11',
            'employee_code'  => 'required|string|max:50',
            'nik'            => 'required|integer|max:11',
        ]);

        // Update the employee record with validated data
        $employee->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'data'    => new EmployeeResource($employee),
        ]);
    }

    public function destroy($name)
    {
        // Find the employee by name
        $employee = Employee::where('name', $name)->first();

        // Delete the employee record
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }
}
