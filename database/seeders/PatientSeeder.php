<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $kenyanNames = [
            'male' => ['James', 'John', 'David', 'Peter', 'Daniel', 'Joseph', 'Samuel', 'Michael', 'Robert', 'Paul'],
            'female' => ['Mary', 'Jane', 'Grace', 'Faith', 'Lucy', 'Sarah', 'Ruth', 'Rebecca', 'Esther', 'Ann']
        ];

        $kenyaCounties = [
            'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Machakos',
            'Nyeri', 'Meru', 'Kakamega', 'Kitale', 'Garissa', 'Embu', 'Kericho'
        ];

        $medicalConditions = [
            'Routine checkup', 'Malaria treatment', 'Diabetes management',
            'Hypertension monitoring', 'Prenatal care', 'Respiratory infection',
            'Dental checkup', 'Vaccination', 'Physical therapy', 'Minor surgery follow-up'
        ];

        for ($i = 0; $i < 200; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $faker->randomElement($kenyanNames[$gender]);
            $lastName = $faker->lastName;

            DB::table('patients')->insert([
                'name' => $firstName . ' ' . $lastName,
                'age' => $faker->numberBetween(1, 90),
                'number' => 'P' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'gender' => $gender,
                'phone_number' => '+254' . $faker->numberBetween(700000000, 799999999),
                'residence' => $faker->randomElement($kenyaCounties),
                'information' => $faker->optional(0.7)->sentence(10) ??
                    $faker->randomElement($medicalConditions) . '. ' . $faker->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}