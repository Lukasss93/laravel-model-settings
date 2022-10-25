<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Lukasss93\ModelSettings\Tests\Models;
use Lukasss93\ModelSettings\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

uses(TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getModelByType(string $type): Model
{
    return match ($type) {
        'table' => Models\UsersWithTable::first(),
        'text_field' => Models\UserWithTextField::first(),
        'redis' => Models\UserWithRedis::first(),
        default => Models\UserWithField::first(),
    };
}

function getModelWithDefaultSettingsByType(string $type): Model
{
    return match ($type) {
        'table' => Models\DefaultSettings\UsersWithTable::first(),
        'text_field' => Models\DefaultSettings\UserWithTextField::first(),
        'redis' => Models\DefaultSettings\UserWithRedis::first(),
        default => Models\DefaultSettings\UserWithField::first(),
    };
}
