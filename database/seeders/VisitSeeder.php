<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class VisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = Faker::create();

        // Get all patient IDs
        $patientIds = DB::table('patients')->pluck('id')->toArray();

        if (empty($patientIds)) {
            $this->command->error('No patients found. Please seed patients first.');
            return;
        }

        $complaints = [
            'Fever and headache for 3 days',
            'Persistent cough and chest pain',
            'Abdominal pain and vomiting',
            'Back pain and difficulty walking',
            'Skin rash and itching',
            'Dizziness and fatigue',
            'Sore throat and difficulty swallowing',
            'Shortness of breath',
            'Joint pain and swelling',
            'Severe headache and nausea',
            'Chest pain radiating to left arm',
            'Lower abdominal pain',
            'Persistent diarrhea',
            'High fever with chills',
            'Ear pain and discharge',
        ];

        $histories = [
            'Symptoms started 3 days ago, progressively worsening',
            'Gradual onset over the past week',
            'Sudden onset this morning',
            'Intermittent symptoms for the past month',
            'Chronic condition, worsening recently',
            'Post-traumatic, after minor accident',
            'Recurrent episodes over the past 6 months',
            'First time experiencing these symptoms',
        ];

        $allergies = [
            'Penicillin',
            'Sulfa drugs',
            'Aspirin',
            'Ibuprofen',
            'Peanuts',
            'No known drug allergies',
            'Latex allergy',
            'Dust and pollen',
        ];

        $physicalExams = [
            'Temperature: 38.5°C, BP: 120/80 mmHg, Pulse: 88 bpm. General appearance: Alert and oriented. Respiratory: Clear bilateral breath sounds. Cardiovascular: Regular rhythm, no murmurs.',
            'Temperature: 37.2°C, BP: 130/85 mmHg, Pulse: 76 bpm. Abdomen: Soft, tender in right lower quadrant. No rebound tenderness.',
            'Temperature: 39.1°C, BP: 110/70 mmHg, Pulse: 102 bpm. Throat: Erythematous with white exudate on tonsils. Cervical lymph nodes enlarged.',
            'Temperature: 36.8°C, BP: 125/82 mmHg, Pulse: 72 bpm. Chest: Bilateral wheezing on auscultation. No use of accessory muscles.',
            'Temperature: 37.5°C, BP: 118/76 mmHg, Pulse: 80 bpm. Skin: Erythematous macular rash on trunk and extremities.',
        ];

        $labTests = [
            'Complete Blood Count: WBC 12,000/μL, Hemoglobin 13.5 g/dL, Platelets 250,000/μL',
            'Malaria test: Positive for Plasmodium falciparum',
            'Urinalysis: pH 6.0, Protein negative, Glucose negative, WBC 5-10/hpf',
            'Liver Function Tests: ALT 35 U/L, AST 28 U/L, Bilirubin 0.8 mg/dL',
            'Lipid Profile: Total Cholesterol 180 mg/dL, LDL 110 mg/dL, HDL 45 mg/dL',
            'Blood Sugar: Fasting 95 mg/dL, Random 130 mg/dL',
            'Stool Analysis: No parasites seen, occult blood negative',
            'Rapid Strep Test: Positive',
        ];

        $imaging = [
            'Chest X-ray: Clear lung fields, normal heart size',
            'Abdominal Ultrasound: No significant abnormalities',
            'X-ray: No fractures or dislocations detected',
            'CT Scan: Unremarkable findings',
            'Not indicated at this time',
            'MRI scheduled for next week',
            'Chest X-ray: Mild infiltrates in right lower lobe',
            'Ultrasound: Normal findings',
        ];

        $diagnoses = [
            'Acute upper respiratory tract infection',
            'Malaria (Uncomplicated)',
            'Acute gastroenteritis',
            'Urinary tract infection',
            'Hypertension',
            'Type 2 Diabetes Mellitus',
            'Acute pharyngitis',
            'Musculoskeletal strain',
            'Allergic dermatitis',
            'Migraine headache',
            'Acute bronchitis',
            'Peptic ulcer disease',
        ];

        $diagnosisTypes = [
            'Clinical',
            'Laboratory confirmed',
            'Radiological',
            'Presumptive',
            'Differential',
        ];

        $prescriptions = [
            'Paracetamol 500mg TDS for 5 days, Amoxicillin 500mg TDS for 7 days',
            'Artemether-Lumefantrine (Coartem) - Complete dose as per weight, Paracetamol 1g PRN',
            'ORS solution, Metronidazole 400mg TDS for 5 days, Probiotics OD',
            'Ciprofloxacin 500mg BD for 7 days, Plenty of fluids',
            'Amlodipine 5mg OD, Lifestyle modifications advised',
            'Metformin 500mg BD, Dietary counseling provided',
            'Ibuprofen 400mg TDS PRN, Rest and plenty of fluids',
            'Cetirizine 10mg OD for 7 days, Hydrocortisone cream BD',
            'Diclofenac 50mg TDS for 5 days, Hot compress advised',
            'Omeprazole 20mg OD for 14 days, Antacids PRN',
        ];

        for ($i = 0; $i < 70; $i++) {
            DB::table('visits')->insert([
                'patient_id' => $faker->randomElement($patientIds),
                'complaints' => $faker->randomElement($complaints),
                'history_of_presenting_illness' => $faker->randomElement($histories),
                'allergies' => $faker->optional(0.7)->randomElement($allergies),
                'physical_examination' => $faker->randomElement($physicalExams),
                'lab_test' => $faker->randomElement($labTests),
                'imaging' => $faker->randomElement($imaging),
                'diagnosis' => $faker->randomElement($diagnoses),
                'type_of_diagnosis' => $faker->randomElement($diagnosisTypes),
                'prescriptions' => $faker->randomElement($prescriptions),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('70 visits seeded successfully!');
    }
}