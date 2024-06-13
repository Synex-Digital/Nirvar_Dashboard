<?php

namespace App\Http\Controllers;

use App\Models\Drugs;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DrugsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $drugs = Drugs::all();

        return view('dashboard.admin.drug.index',[
            'drugs' => $drugs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',

        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->messages() as  $messages) {
                foreach ($messages as $message) {
                    flash()->options([
                        'position' => 'bottom-right',
                    ])->error($message);
                }
            }

            return back()->withErrors($validator)->withInput();
        }
        $drug = new Drugs();
        $drug->name = $request->name;
        $drug->save();
        flash()->options(['position' => 'bottom-right'])->success('Added Successfully');
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Drugs $drugs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Drugs::find($id);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',

        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->messages() as  $messages) {
                foreach ($messages as $message) {
                    flash()->options([
                        'position' => 'bottom-right',
                    ])->error($message);
                }
            }

            return back()->withErrors($validator)->withInput();
        }
        $drug = Drugs::find($id);
        $drug->name = $request->name;
        $drug->save();
        flash()->options(['position' => 'bottom-right'])->success('Updated Successfully');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Drugs::find($id)->delete();
        flash()->options(['position' => 'bottom-right'])->success('Deleted Successfully');
        return back();

    }
}
