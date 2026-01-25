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
        $visits = Visit::with('patient')
            ->latest()
            ->paginate(10)
            ->through(function ($visit) {
                return [
                    'id' => $visit->id,
                    'patient_id' => $visit->patient_id,
                    'complaints' => $visit->complaints,
                    'history_of_presenting_illness' => $visit->history_of_presenting_illness,
                    'allergies' => $visit->allergies,
                    'physical_examination' => $visit->physical_examination,
                    'lab_test' => $visit->lab_test,
                    'imaging' => $visit->imaging,
                    'diagnosis' => $visit->diagnosis,
                    'type_of_diagnosis' => $visit->type_of_diagnosis,
                    'prescriptions' => $visit->prescriptions,
                    'created_at' => $visit->created_at,
                    'updated_at' => $visit->updated_at,
                    'patient' => $visit->patient ? [
                        'id' => $visit->patient->id,
                        'number' => $visit->patient->number,
                        'name' => $visit->patient->name,
                        'age' => $visit->patient->age,
                        'gender' => $visit->patient->gender,
                        'phone_number' => $visit->patient->phone_number,
                        'residence' => $visit->patient->residence,
                    ] : null,
                ];
            });

        return Inertia::render('visits/Index', [
            'visits' => $visits
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