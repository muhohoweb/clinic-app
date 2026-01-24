<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $visitIds = DB::table('visits')->pluck('id')->toArray();

        if (empty($visitIds)) {
            $this->command->error('No visits found. Please seed visits first.');
            return;
        }

        $paymentModes = ['cash', 'mpesa', 'bank_transfer', 'insurance'];
        $consultationFees = [500, 800, 1000, 1200, 1500, 2000, 2500, 3000];

        foreach ($visitIds as $visitId) {
            if ($faker->boolean(90)) { // 90% of visits have payments
                $amountCharged = $faker->randomElement($consultationFees);

                // Determine payment status with proper weights
                $rand = $faker->numberBetween(1, 100);
                if ($rand <= 70) {
                    $paymentStatus = 'full';    // 70% paid in full
                } elseif ($rand <= 90) {
                    $paymentStatus = 'partial'; // 20% partial payment
                } else {
                    $paymentStatus = 'none';    // 10% no payment yet
                }

                $amountPaid = match($paymentStatus) {
                    'full' => $amountCharged,
                    'partial' => $amountCharged * $faker->randomFloat(2, 0.3, 0.8),
                    'none' => 0
                };

                $balance = $amountCharged - $amountPaid;

                DB::table('payments')->insert([
                    'visit_id' => $visitId,
                    'amount_charged' => $amountCharged,
                    'amount_paid' => $amountPaid,
                    'mode_of_payment' => $faker->randomElement($paymentModes),
                    'balance' => $balance,
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                    'updated_at' => now(),
                ]);
            }
        }

        $paymentCount = DB::table('payments')->count();
        $this->command->info("{$paymentCount} payments seeded successfully!");
    }
}