<?php

/** @var Factory $factory */

use App\Models\Contract;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Xguard\Tasklist\Models\Employee;
use Xguard\Tasklist\Models\Task;

$factory->define(Task::class, function (Faker $faker) {

    return [
        'is_recurring' => $faker->boolean(),
        'description' => $faker->sentence(),
        'time' => $faker->dateTimeThisMonth(),
        'contract_id' => factory(Contract::class),
        'job_site_address_id' => null,
        'employee_id' => factory(Employee::class),
        'created_at' => $faker->dateTimeThisMonth(),
        'updated_at' => $faker->dateTimeThisMonth(),
        'deleted_at' => null];
});

