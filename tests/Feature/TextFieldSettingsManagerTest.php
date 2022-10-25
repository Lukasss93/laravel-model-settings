<?php

use Lukasss93\ModelSettings\Exceptions\ModelSettingsException;
use Lukasss93\ModelSettings\Tests\Models\UserWithTextField as User;

beforeEach(function () {
    $this->model = User::first();
});

test('settings is not a valid json', function () {
    $this->model->settings = 'Invalid Json';
    $this->model->save();

    expect($this->model->settings()->all())->toBe([]);
});

test('model array settings', function () {
    $testArray = ['a' => 'b'];
    $this->model->settings = $testArray;
    $this->model->save();

    expect($this->model->settings()->all())->toBe($testArray);
});

test('settings missing settings field', function () {
    $this->model->settingsFieldName = 'test';
    $this->model->settings()->all();
})->throws(ModelSettingsException::class, 'Unknown field');

test('persistence', function () {
    $testArray = [
        'user' => [
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => "john@doe.com",
        ],
    ];

    $this->model->settings()->apply($testArray);
    expect($this->model->fresh()->settings()->all())->toBe($testArray);

    $this->model->settings()->delete();

    $this->model->setPersistSettings(false);
    $this->model->settings()->apply($testArray);
    expect($this->model->fresh()->settings()->all())->toBe([]);

    $this->model->setPersistSettings(false);
    $this->model->settings()->apply($testArray);
    $this->model->save();
    expect($this->model->fresh()->settings()->all())->toBe($testArray);

    $this->model->settings()->delete();

    $this->model->fresh();
    $this->model->setPersistSettings(true);
    $this->model->settings()->apply($testArray);
    expect($this->model->fresh()->settings()->all())->toBe($testArray);
});