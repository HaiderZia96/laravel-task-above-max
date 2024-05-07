<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data=[
            'page_title'=>'Employee',
            'p_title'=>'Employee',
            'p_summary'=>'List of Employees',
            'p_description'=>null,
            'url'=>route('employee.create'),
            'url_text'=>'Add New',
        ];
        return view('employee.index')->with($data);
    }

    public function getIndex(Request $request)
    {
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Employee::select('employees.*')->count();
        // Total records with filter
        $totalRecordswithFilter = Employee::select('employees.*')
            ->where(function ($q) use ($searchValue){
                $q->where('employees.first_name', 'like', '%' .$searchValue . '%');
            })
            ->count();
        // Fetch records
        $records = Employee::select('employees.*')
            ->where(function ($q) use ($searchValue){
                $q->where('employees.first_name', 'like', '%' .$searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName,$columnSortOrder)
            ->get();

        $data_arr = array();

        foreach($records as $record){
            $id = $record->id;
            $first_name = $record->first_name;
            $last_name = $record->last_name;
            $email = $record->email;
            if(isset($record['companyID']['name'])){
                $company_id = $record['companyID']['name'];
            }
            else{
                $company_id = "";
            }
            $phone = $record->phone;

            $data_arr[] = array(
                "id" => $id,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "email" => $email,
                "phone" => $phone,
                "company_id" => $company_id,
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        echo json_encode($response);
        exit;
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = array(
            'page_title'=>'Employee',
            'p_title'=>'Employee',
            'p_summary'=>'Add Employee',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('employee.store'),
            'url'=>route('employee.index'),
            'url_text'=>'View All',
            'enctype' => 'multipart/form-data', // With attachment like file or images in form
        );
        return view('employee.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'company_id' => 'required|int',
        ]);

        $arr =  [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'company_id' => $request->input('company_id'),
            'created_by' => Auth::user()->id,
        ];

        $record = Employee::create($arr);
//        $messages =  [
//            array(
//                'message' => 'Record created successfully',
//                'message_type' => 'success'
//            ),
//        ];
//        Session::flash('messages', $messages);

        return redirect()->route('employee.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = Employee::select('employees.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $company = Company::select( 'companies.id as company_id','companies.name as company_name')
            ->where('companies.id', '=', $record->company_id)
            ->first();
        $data = array(
            'page_title'=>'Employee',
            'p_title'=>'Employee',
            'p_summary'=>'Show Employee',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('employee.update',$record->id),
            'url'=>route('employee.index'),
            'url_text'=>'View All',
            'data'=>$record,
            'company'=>$company,
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('employee.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $record = Employee::select('employees.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $company = Company::select( 'companies.id as company_id','companies.name as company_name')
            ->where('companies.id', '=', $record->company_id)
            ->first();

        $data = array(
            'page_title'=>'Employee',
            'p_title'=>'Employee',
            'p_summary'=>'Edit Employee',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('employee.update',$record->id),
            'url'=>route('employee.index'),
            'url_text'=>'View All',
            'data'=>$record,
            'company'=>$company,
            'enctype' => 'multipart/form-data', // With attachment like file or images in form
        );
        return view('employee.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = Employee::select('employees.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }

        $arr =  [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'company_id' => $request->input('company_id'),
        ];
//        dd($arr);
        $record->update($arr);
//        $messages =  [
//            array(
//                'message' => 'Record updated successfully',
//                'message_type' => 'success'
//            ),
//        ];
//        Session::flash('messages', $messages);

        return redirect()->route('employee.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = Employee::select('employees.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }

        $record->delete();

//        $messages =  [
//            array(
//                'message' => 'Record deleted successfully',
//                'message_type' => 'success'
//            ),
//        ];
//        Session::flash('messages', $messages);

        return redirect()->route('employee.index');
    }

    public function getEmployeeCompanyIndexSelect(Request $request)
    {
        $data = [];

        if ($request->has('q')) {
            $search = $request->q;
            $data = Company::select('companies.id as company_id', 'companies.name as company_name')
                ->where(function ($q) use ($search) {
                    $q->where('companies.name', 'like', '%' . $search . '%');
                })
                ->get();
        }

        return response()->json($data);

    }
}
