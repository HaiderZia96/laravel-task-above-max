<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data=[
            'page_title'=>'Companies',
            'p_title'=>'Companies',
            'p_summary'=>'List of Companies',
            'p_description'=>null,
            'url'=>route('company.create'),
            'url_text'=>'Add New',
        ];
        return view('company.index')->with($data);
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
            $totalRecords = Company::select('companies.*')->count();
        // Total records with filter
        $totalRecordswithFilter = Company::select('companies.*')
            ->where(function ($q) use ($searchValue){
                $q->where('companies.name', 'like', '%' .$searchValue . '%');
            })
            ->count();
        // Fetch records
        $records = Company::select('companies.*')
            ->where(function ($q) use ($searchValue){
                $q->where('companies.name', 'like', '%' .$searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName,$columnSortOrder)
            ->get();

        $data_arr = array();

        foreach($records as $record){
            $id = $record->id;
            $name = $record->name;
            $email = $record->email;

            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "email" => $email,
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
            'page_title'=>'Company',
            'p_title'=>'Company',
            'p_summary'=>'Add Company',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('company.store'),
            'url'=>route('company.index'),
            'url_text'=>'View All',
            'enctype' => 'multipart/form-data', // With attachment like file or images in form
        );
        return view('company.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'logo' => 'required|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
        $logo = null;

        if ($request->hasFile('logo')) {
            // Icon
            $cIcon = $request->file('logo');
            $iconOriginalName = $cIcon->getClientOriginalName();
            $iconName = pathinfo($iconOriginalName, PATHINFO_FILENAME);
            $iconExtension = $cIcon->getClientOriginalExtension();
            $iconOriginalNameSluggy = Str::slug($iconName);
            $cIconFileName = time()   . rand(0, 999999) . '-' . $iconOriginalNameSluggy.'.'.$iconExtension;
            $basePath = 'public';
            $iconPath = $basePath . '/Company';
            $attachmentPath = $iconPath . '/Logos';
            $monthlyAttachmentsPath = $attachmentPath . '/' . date('Y') . '/' . date('m');
            $logo = $request->file('logo')->storeAs(
                $monthlyAttachmentsPath,
                $cIconFileName
            );
//            dd($pathThumbnail);
        }
        //
        $arr =  [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'logo' => $logo
        ];
//        dd($arr);
        $record = Company::create($arr);
//        $messages =  [
//            array(
//                'message' => 'Record created successfully',
//                'message_type' => 'success'
//            ),
//        ];
//        Session::flash('messages', $messages);

        return redirect()->route('company.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = Company::select('companies.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $data = array(
            'page_title'=>'Company',
            'p_title'=>'Company',
            'p_summary'=>'Show Company',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('company.update',$record->id),
            'url'=>route('company.index'),
            'url_text'=>'View All',
            'data'=>$record,
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('company.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $record = Company::select('companies.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $data = array(
            'page_title'=>'Company',
            'p_title'=>'Company',
            'p_summary'=>'Show Company',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('company.update',$record->id),
            'url'=>route('company.index'),
            'url_text'=>'View All',
            'data'=>$record,
            'enctype' => 'multipart/form-data', // With attachment like file or images in form
        );
        return view('company.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = Company::select('companies.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        if ($request->hasFile('logo')) {
            // Icon
            $cIcon = $request->file('logo');
            $iconOriginalName = $cIcon->getClientOriginalName();
            $iconName = pathinfo($iconOriginalName, PATHINFO_FILENAME);
            $iconExtension = $cIcon->getClientOriginalExtension();
            $iconOriginalNameSluggy = Str::slug($iconName);
            $cIconFileName = time()   . rand(0, 999999) . '-' . $iconOriginalNameSluggy.'.'.$iconExtension;
//            $img = date('Y') . '/' . date('m') . '/' . $cIconFileName;
            $basePath = 'public';
            $iconPath = $basePath . '/Company';
            $attachmentPath = $iconPath . '/Logos';
            $monthlyAttachmentsPath = $attachmentPath . '/' . date('Y') . '/' . date('m');
            $iconUrlfileName = $cIconFileName;
            $logo = $request->file('logo')->storeAs(
                $monthlyAttachmentsPath,
                $cIconFileName
            );
            //Unlink previous image
            if (isset($record) && $record->logo) {
                $prevImage = Storage::disk('public')->path('Company/Logos/'.$record->logo);
                if (File::exists($prevImage)) { // unlink or remove previous image from folder
                    File::delete($prevImage);
                }
                $arr['logo'] = $logo;
            }
        }
        else{
            $logo = $record->logo;

        }
        //
        $arr =  [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'logo' => $logo,
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

        return redirect()->route('company.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = Company::select('companies.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        //       Company Logo Remove
        $image_path = Storage::disk('public')->path('Company/Logos/' . $record->logo);
        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        $record->delete();

//        $messages =  [
//            array(
//                'message' => 'Record deleted successfully',
//                'message_type' => 'success'
//            ),
//        ];
//        Session::flash('messages', $messages);

        return redirect()->route('company.index');
    }
}
