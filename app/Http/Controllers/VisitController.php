<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('visits/Index', [
            'visits' => Visit::with('patient')->latest()->get()->all()
        ]);
    }

    /**
     * Search for a patient by phone number or patient number.
     */
    public function searchPatient(Request $request)
    {
        $search = $request->input('search');

        if (empty($search)) {
            return response()->json(['patient' => null]);
        }

        $patient = Patient::where('phone_number', 'like', '%' . $search . '%')
            ->orWhere('number', 'like', '%' . $search . '%')
            ->first();

        return response()->json(['patient' => $patient]);
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
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'complaints' => 'required|string',
            'history_of_presenting_illness' => 'required|string|max:255',
            'allergies' => 'nullable|string',
            'physical_examination' => 'required|string',
            'lab_test' => 'required|string',
            'imaging' => 'required|string',
            'diagnosis' => 'required|string',
            'type_of_diagnosis' => 'required|string|in:Clinical,Laboratory confirmed,Radiological,Presumptive,Differential',
            'prescriptions' => 'required|string',
        ]);

        Visit::create($validated);

        return redirect()->route('visits.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'complaints' => 'required|string',
            'history_of_presenting_illness' => 'required|string|max:255',
            'allergies' => 'nullable|string',
            'physical_examination' => 'required|string',
            'lab_test' => 'required|string',
            'imaging' => 'required|string',
            'diagnosis' => 'required|string',
            'type_of_diagnosis' => 'required|string|in:Clinical,Laboratory confirmed,Radiological,Presumptive,Differential',
            'prescriptions' => 'required|string',
        ]);

        $visit->update($validated);

        return redirect()->route('visits.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        $visit->delete();

        return redirect()->route('visits.index');
    }
}