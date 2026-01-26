<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['visit.patient'])
            ->latest()
            ->paginate(15)
            ->through(function ($payment) {
                return [
                    'id' => $payment->id,
                    'visit_id' => $payment->visit_id,
                    'amount_charged' => $payment->amount_charged,
                    'amount_paid' => $payment->amount_paid,
                    'balance' => $payment->balance,
                    'mode_of_payment' => $payment->mode_of_payment,
                    'mpesa_transaction_id' => $payment->mpesa_transaction_id,
                    'mpesa_phone_number' => $payment->mpesa_phone_number,
                    'notes' => $payment->notes,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                    'visit' => $payment->visit ? [
                        'id' => $payment->visit->id,
                        'diagnosis' => $payment->visit->diagnosis,
                        'complaints' => $payment->visit->complaints,
                        'patient' => $payment->visit->patient ? [
                            'id' => $payment->visit->patient->id,
                            'number' => $payment->visit->patient->number,
                            'name' => $payment->visit->patient->name,
                            'phone_number' => $payment->visit->patient->phone_number,
                        ] : null,
                    ] : null,
                ];
            });

        return Inertia::render('payments/Index', [
            'payments' => $payments
        ]);
    }

    /**
     * Search for a visit by patient phone number or patient number.
     */
    public function searchVisit(Request $request)
    {
        $search = $request->input('search');

        if (empty($search)) {
            return response()->json(['visit' => null]);
        }

        $visit = Visit::with('patient')
            ->whereHas('patient', function ($query) use ($search) {
                $query->where('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('number', 'like', '%' . $search . '%');
            })
            ->latest()
            ->first();

        return response()->json(['visit' => $visit]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'amount_charged' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'mode_of_payment' => 'required|in:cash,mpesa,bank_transfer,insurance',
            'mpesa_transaction_id' => 'nullable|string|max:255',
            'mpesa_phone_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Calculate balance
        $validated['balance'] = $validated['amount_charged'] - $validated['amount_paid'];

        Payment::create($validated);

        return redirect()->route('payments.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'amount_charged' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'mode_of_payment' => 'required|in:cash,mpesa,bank_transfer,insurance',
            'mpesa_transaction_id' => 'nullable|string|max:255',
            'mpesa_phone_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Calculate balance
        $validated['balance'] = $validated['amount_charged'] - $validated['amount_paid'];

        $payment->update($validated);

        return redirect()->route('payments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index');
    }

    /**
     * Initiate M-Pesa STK Push
     */
    public function mpesaStkPush(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        // TODO: Implement actual M-Pesa STK Push integration
        // For now, we'll just simulate success

        // Simulate processing
        sleep(2);

        return response()->json([
            'success' => true,
            'message' => 'STK Push sent successfully. Customer will receive prompt on their phone.',
            'transaction_id' => 'MPE' . time() . rand(1000, 9999),
        ]);
    }
}