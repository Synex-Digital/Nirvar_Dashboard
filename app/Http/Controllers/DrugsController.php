<?php

namespace App\Http\Controllers;

use App\Models\Drugs;
use Illuminate\Http\Request;

class DrugsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   $drugs = Drugs::all();
        return view('dashboard.drug.index',[
            'drugs' => $drugs
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
