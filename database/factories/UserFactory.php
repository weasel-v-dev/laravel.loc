<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    private static $primaryKey = 1;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->email(),
            'address' => [
                'street' => $this->faker->streetAddress(),
                'suite' => $this->faker->randomLetter(),
                'city' => $this->faker->city(),
                'zipcode' => $this->faker->postcode,
                'geo' => [
                    'lat' => $this->faker->randomFloat(3, 1, 1000),
                    'lng' => $this->faker->randomFloat(3, 1, 1000)
                ]
            ],
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->name,
            'company' => [
                'name' => $this->faker->company(),
                'catchPhrase' =>  $this->faker->text(),
                'bs' => $this->faker->randomLetter(),
            ]
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
