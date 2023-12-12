<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $string = fake()->regexify('LD\d{4}[a-z]{2}[!@#$%^&*]');
        return [
            'voucher_no' => $string,
            'total' => rand(10000,900000),
            'note' => fake()->text(70)
        ];
    }
}
