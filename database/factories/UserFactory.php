<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fname = $this->faker->firstName;
        $lname = $this->faker->lastName;
        $fullname = Str::lower($fname).Str::lower($lname);
        $status = $this->faker->numberBetween(0,2);
        switch ($status) {
            case 1:
                $status = 'active';
                break;

            case 2:
                $status = 'inactive';
                break;

                default:
                $status = 'pending';
                break;
        }

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'role' => $this->faker->randomElement(['admin', 'employee']), // Only admin or employee
            'phone_number' => $this->faker->phoneNumber,
            'hire_date' => $this->faker->date(),
            'supervisor_id' => null, // Can be manually assigned if needed
            'password' => Hash::make('password'), // Encrypt password
            'status' => $status, 
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    
    }
}
