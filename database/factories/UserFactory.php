<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\State;
use App\Models\LawFirm;
use Spatie\Permission\Models\Role;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->secondaryAddress(),
            'city' => fake()->city(),
            'state_id' => State::inRandomOrder()->first()->id ?? 1,
            'zip_code' => fake()->postcode(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'phone_number' => fake()->phoneNumber(),
            'height' => fake()->numberBetween($min = 150, $max = 200),
            'weight' => fake()->numberBetween($min = 50, $max = 100),
            'law_firm_id' => LawFirm::inRandomOrder()->first()->id ?? null,
            'birthdate' => fake()->date($format = 'Y-m-d', $max = '2003-01-01'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Assign a random role to the user
            $role = Role::inRandomOrder()->first();
            
            if ($role) {
                $user->assignRole($role->name);
            }
        });
    }
}
