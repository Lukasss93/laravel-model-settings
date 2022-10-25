<?php

use Lukasss93\ModelSettings\Exceptions\ModelSettingsException;
use Lukasss93\ModelSettings\Tests\Models\UserWithField as User;

beforeEach(function () {
    $this->model = User::first();
});

it('can set settings as array', function () {
    $testArray = ['a' => 'b'];

    $this->model->settings = $testArray;
    $this->model->save();

    expect($this->model->settings()->all())->toEqual($testArray);
});

it('throws exception if settings field is missing', function () {
    $this->model->settingsFieldName = 'test';
    $this->model->settings()->all();
})->throws(ModelSettingsException::class, 'Unknown field');

it('can persist settings', function () {
    $testArray = [
        'user' => [
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => "john@doe.com",
        ],
        'project' => [
            'name' => 'Project One',
            'description' => 'Test Description',
        ],
    ];

    $this->model->settings()->apply($testArray);
    expect($this->model->fresh()->settings()->all())->toEqual($testArray);

    $this->model->settings()->delete();
    $this->model->setPersistSettings(false);
    $this->model->settings()->apply($testArray);
    expect($this->model->fresh()->settings()->all())->toEqual([]);

    $this->model->setPersistSettings(false);
    $this->model->settings()->apply($testArray);
    $this->model->save();
    expect($this->model->fresh()->settings()->all())->toEqual($testArray);

    $this->model->settings()->delete();
    $this->model->fresh();
    $this->model->setPersistSettings(true);
    $this->model->settings()->apply($testArray);
    expect($this->model->fresh()->settings()->all())->toEqual($testArray);
});