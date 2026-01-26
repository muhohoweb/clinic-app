<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Get some visits
        $visits = Visit::with('patient')->take(10)->get();

        foreach ($visits as $visit) {
            $amountCharged = rand(1000, 10000);
            $amountPaid = rand(500, $amountCharged);
            $balance = $amountCharged - $amountPaid;

            $paymentMethods = ['cash', 'mpesa', 'bank_transfer', 'insurance'];
            $method = $paymentMethods[array_rand($paymentMethods)];

            $paymentData = [
                'visit_id' => $visit->id,
                'amount_charged' => $amountCharged,
                'amount_paid' => $amountPaid,
                'balance' => $balance,
                'mode_of_payment' => $method,
            ];

            if ($method === 'mpesa') {
                $paymentData['mpesa_phone_number'] = $visit->patient->phone_number;
                $paymentData['mpesa_transaction_id'] = 'MPE' . time() . rand(1000, 9999);
            }

            Payment::create($paymentData);
        }
    }
}