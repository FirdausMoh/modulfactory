<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


class EmployeeController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        {
//         // RAW SQL QUERY
//     $employees = DB::select('
//     select *, employees.id as employee_id, positions.name as position_name
//     from employees
//     left join positions on employees.position_id = positions.id
// ');


        $pageTitle = 'Employee List';

        // // Query Builder
        // $employees = DB::table('employees')
        //     ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
        //     ->select('employees.*', 'employees.id as employee_id', 'positions.name as position_name')
        //     ->get();

            // ELOQUENT
            $employees = Employee::all();
        return view('employee.index', [
        'pageTitle' => $pageTitle,
        'employees' => $employees
        ]);

    }
}

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        // $pageTitle = 'Create Employee';
        // // RAW SQL Query
        // $positions = DB::select('select * from positions');

        $pageTitle = 'Create Employee';

        // Query Builder
        // $positions = DB::table('positions')->get();

        // ELOQUENT
        $positions = Position::all();

        return view('employee.create', compact('pageTitle', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    //     // query
    //     // INSERT QUERY
    // DB::table('employees')->insert([
    //     'firstname' => $request->firstName,
    //     'lastname' => $request->lastName,
    //     'email' => $request->email,
    //     'age' => $request->age,
    //     'position_id' => $request->position,
    // ]);

     // Get File
     $file = $request->file('cv');

     if ($file != null) {
         $originalFilename = $file->getClientOriginalName();
         $encryptedFilename = $file->hashName();

         // Store File
         $file->store('public/files');
     }

     // ELOQUENT
     $employee = New Employee;
     $employee->firstname = $request->firstName;
     $employee->lastname = $request->lastName;
     $employee->email = $request->email;
     $employee->age = $request->age;
     $employee->position_id = $request->position;

     if ($file != null) {
        $employee->original_filename = $originalFilename;
        $employee->encrypted_filename = $encryptedFilename;
    }

     $employee->save();


    return redirect()->route('employees.index');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
//             // RAW SQL QUERY
//     $employee = collect(DB::select('
//     select *, employees.id as employee_id, positions.name as position_name
//     from employees
//     left join positions on employees.position_id = positions.id
//     where employees.id = ?
// ', [$id]))->first();

        $pageTitle = 'Employee Detail';

        // // Query Builder
        // $employee = DB::table('employees')
        //     ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
        //     ->select('employees.*', 'employees.id as employee_id', 'positions.name as position_name')
        //     ->where('employees.id', '=', $id)
        //     ->first();

         // ELOQUENT
    $employee = Employee::find($id);


        return view('employee.show', compact('pageTitle', 'employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $pageTitle = 'Edit Employee';
    //     // RAW SQL Query
    //     $positions = DB::select('select * from positions');

    //     $employee = collect(DB::select('
    //     select *, employees.id as employee_id, positions.name as position_name
    //     from employees
    //     left join positions on employees.position_id = positions.id
    //     where employees.id = ?
    // ', [$id]))->first();

        // // Query Builder
        // $employee = DB::table('employees')
        //     ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
        //     ->select('employees.*', 'employees.id as employee_id', 'positions.name as position_name')
        //     ->where('employees.id', '=', $id)
        //     ->first();

        // $positions = DB::table('positions')->get();


     // ELOQUENT
     $positions = Position::all();
     $employee = Employee::find($id);


        return view('employee.edit', compact('pageTitle', 'employee', 'positions'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // validasi input
        $messages = [
            'required' => ':attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar.',
            'numeric' => 'Isi :attribute dengan angka.'
        ];

        // Validasi menggunakan Validator
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);


        // kembali ke halaman sebelumnya with error
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // // UPDATE QUERY
        // DB::table('employees')
        //     ->where('id', $id)
        //     ->update([
        //         'firstname' => $request->firstName,
        //         'lastname' => $request->lastName,
        //         'email' => $request->email,
        //         'age' => $request->age,
        //         'position_id' => $request->position,
        //     ]);

         // Get File
         $file = $request->file('cv');

         if ($file != null) {
             $originalFilename = $file->getClientOriginalName();
             $encryptedFilename = $file->hashName();

             // Store File
             $file->store('public/files');
         }

         // ELOQUENT
    $employee = Employee::find($id);
    $employee->firstname = $request->firstName;
    $employee->lastname = $request->lastName;
    $employee->email = $request->email;
    $employee->age = $request->age;
    $employee->position_id = $request->position;
    if ($file != null) {
        $employee->original_filename = $originalFilename;
        $employee->encrypted_filename = $encryptedFilename;
    }
    $employee->save();

        return redirect()->route('employees.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    // Menghapus file dari direktori upload
    $uploadPath = storage_path('app/public/files/');
    $uploadFileName = DB::table('employees')->where('id', $id)->value('encrypted_filename');
    $uploadFilePath = $uploadPath . $uploadFileName;
    if (File::exists($uploadFilePath)) {
        File::delete($uploadFilePath);
    }

    // Menghapus file dari direktori publik
    $publicPath = public_path('files/');
    $publicFileName = DB::table('employees')->where('id', $id)->value('encrypted_filename');
    $publicFilePath = $publicPath . $publicFileName;
    if (File::exists($publicFilePath)) {
        File::delete($publicFilePath);
    }

        // ELOQUENT
        Employee::find($id)->delete();

            return redirect()->route('employees.index');
    }

    /**
     * Download file.
     */
    public function downloadFile($employeeId)
    {
        $employee = Employee::find($employeeId);
        $encryptedFilename = 'public/files/'.$employee->encrypted_filename;
        $downloadFilename = Str::lower
        ($employee->firstname.'_'.$employee->lastname.'_cv.pdf');

        if(Storage::exists($encryptedFilename)) {
            return Storage::download($encryptedFilename, $downloadFilename);
        }
    }
}
