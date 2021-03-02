<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'passenger_id' => $this->faker->numberBetween(1, 10),
            'state' => $this->faker->randomElement(Booking::STATE),
            'country_id' => $this->faker->numberBetween(1, 10),
            'fare' => $this->faker->randomFloat(2, 1, 100),
            'created_at_local' => now(),
        ];
    }
}
