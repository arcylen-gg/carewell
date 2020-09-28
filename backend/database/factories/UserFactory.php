<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\UserPosition::class, function (Faker $faker) {
    return [
        'name' => $faker->jobTitle
    ];
});

$factory->define(App\Models\UserPositionAccess::class, function (Faker $faker) {
    return [
        'page_id' => 1,
        'user_position_id' => 1,
        'code' => 'dashboard',
        'types' => 'test',
    ];
});

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'user_position_id' => mt_rand(2, 11),
        'first_name' => $faker->firstName,
        'middle_name' => $faker->firstNameFemale,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // password: secret
        'crypt' => Crypt::encrypt('secret'), // password: secret
        'remember_token' => str_random(10)
    ];
});

$factory->define(App\Models\Page::class, function (Faker $faker) {
    return [
        'name'          => 'Home',
        'type'          => 'primary',
        'parent_id'     => 0,
        'icon'          => 'dashboard',
        'color'          => 'dashboard',
        'url'           => 'dashboard/home'
    ];
});

$factory->define(App\Models\BenefitType::class, function (Faker $faker) {
    return [
        'name' => 'test'
    ];
});


$factory->define(App\Models\PaymentMode::class, function (Faker $faker) {
    return [
        'name' => 'test'
    ];
});

$factory->define(App\Models\PreExisting::class, function (Faker $faker) {
    return [
        'name' => 'test',
        'number' => '0'
    ];
});

$factory->define(App\Models\PaymentMethod::class, function (Faker $faker) {
    return [
        'name' => 'test',
    ];
});

$factory->define(App\Models\ProcedureType::class, function (Faker $faker) {
    return [
        'name' => 'test',
    ];
});

$factory->define(App\Models\Procedure::class, function (Faker $faker) {
    return [
        'procedure_type_id' => 1,
        'name' => 'test',
    ];
});

$factory->define(App\Models\DiagnosisList::class, function (Faker $faker) {
    return [
        'name' => $faker->streetName,
        'parent_id' => null,
        'description' => $faker->sentence(mt_rand(10, 15), true) ,
    ];
});

$factory->define(App\Models\Provider::class, function (Faker $faker) {
    return [
        'name' => 'dogoma',
        'rate_rvs' => 2001,
        'tel_number' => '1234567',
        'contact_person' => 'security guard',
        'contact_number' => '163',
        'email' => 'securityguard@gmail.com',
        'address' => 'guard house'
    ];
});

$factory->define(\App\Models\ProviderPayee::class, function (Faker $faker) {
    return [
        'provider_id' => 1,
        'name' => "payee1"
    ];
});

$factory->define(\App\Models\Doctor::class, function (Faker $faker) {
    return [
        'first_name' => 'doc',
        'middle_name' => 'doc',
        'last_name' => 'doc',
        'email' => 'doc@doc.com',
        'contact_number' => '123',
        'tin' => '123',
        'tax' => 'VATABLE'
    ];
});

$factory->define(\App\Models\DoctorsProvider::class, function (Faker $faker) {
    return [
        'provider_id' => 1,
        'doctor_id' => 1
    ];
});
    
 

    
