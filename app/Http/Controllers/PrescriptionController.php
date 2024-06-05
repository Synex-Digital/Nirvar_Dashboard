<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.doctor.prescription.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prescription $prescription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prescription $prescription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription)
    {
        //
    }

    public function selectUsers(Request $request)
    {
        $search = $request->input('q');
        $page = $request->input('page', 1);
        $pageSize = 30; // Adjust the page size as needed

        $query = User::where('role', 'patient');

        if ($search) {
            $query->where('number', 'LIKE', "%{$search}%");
        }

        $total_count = $query->count();
        $users = $query->skip(($page - 1) * $pageSize)->take($pageSize)->get();

        $results = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->number
            ];
        });

        return response()->json([
            'items' => $results,
            'total_count' => $total_count
        ]);
    }
}
