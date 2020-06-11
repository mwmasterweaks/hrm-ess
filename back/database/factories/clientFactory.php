<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Client;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'location' => $faker->address,
        'contact' => "09" . rand(110101100, 990401999),
        'package_id' => rand(1, 24),
        'modem_id' => rand(1, 4),
        'communication_protocol' => "Internet",
        'package_type_id' => rand(1, 4),
    ];
});
