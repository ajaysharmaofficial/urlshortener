<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Url;
use App\Models\User;
use App\Models\Company;

class UrlFactory extends Factory
{
    protected $model = Url::class;

    public function definition()
    {
        return [
            'original_url' => $this->faker->url(),
            'short_code'  => $this->faker->unique()->regexify('[A-Za-z0-9]{6}'),
            'hits'       => 0,
            'created_by'    => User::factory(),
            'company_id' => Company::factory(),
        ];
    }
}