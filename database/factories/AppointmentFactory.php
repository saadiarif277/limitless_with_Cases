<?php

namespace Database\Factories;

use App\Models\AppointmentType;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Fetch a random patient user with 'Patient' role
        $patientRole = Role::where('name', 'Patient')->first();
        $patientUser = User::role($patientRole)->inRandomOrder()->first();

        // Generate start and end dates within the next 3 months
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $startDate->setTime((int)$startDate->format('H'), 0); // This sets the minutes and seconds to 00:00
        $endDate = (clone $startDate)->modify('+1 hour');

        return [
            'description' => fake()->optional()->paragraph(),
            'appointment_type_id' => AppointmentType::inRandomOrder()->first()->getKey(),
            'clinic_id' => Clinic::inRandomOrder()->first()->getKey(),
            'patient_user_id' => $patientUser ? $patientUser->getKey() : null,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}
