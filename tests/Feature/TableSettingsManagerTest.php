<?php

use Lukasss93\ModelSettings\Models\ModelSettings;
use Lukasss93\ModelSettings\Tests\Models\UsersWithTable;
use Lukasss93\ModelSettings\Tests\Models\UsersWithTableAndDefaultSettings;

beforeEach(function () {
    $this->testArray = [
        'user' => [
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => "john@doe.com",
        ],
    ];
});

test('specific default value', function () {
    $this->model = UsersWithTableAndDefaultSettings::first();

    expect($this->model->settings()->all())->toBe($this->model->defaultSettings());

    expect($this->model->settings()->all())->toBe($this->model->config()->all());

    $this->model->settings()->apply($this->testArray);
    expect($this->model->settings()->all())->toBe(array_merge($this->model->defaultSettings(), $this->testArray));

    expect(cache()->has($this->model->getSettingsCacheKey()))->toBeTrue();
    expect(cache()->get($this->model->getSettingsCacheKey()))->toBe($this->testArray);

    expect(config('model_settings.settings_table_use_cache'))->toBeTrue();

    config()->set('model_settings.settings_table_use_cache', false);
    expect(config('model_settings.settings_table_use_cache'))->toBeFalse();
    expect($this->model->settings()->all())->toBe(array_merge($this->model->defaultSettings(), $this->testArray));
});

test('settings table count', function () {
    $this->model = UsersWithTable::first();

    $this->model->settings()->apply($this->testArray);
    expect(ModelSettings::all()->count())->toBe(1);
    $this->model->settings()->apply($this->testArray);
    expect(ModelSettings::all()->count())->toBe(1);

    expect($this->model->settings()->all())->toBe($this->testArray);
    expect($this->model->modelSettings()->count())->toBe(1);

    $this->model->settings()->delete();
    expect($this->model->settings()->all())->toBe([]);
    expect($this->model->modelSettings()->count())->toBe(0);

    $this->model->settings()->apply($this->testArray);
    expect($this->model->settings()->all())->toBe($this->testArray);
    expect($this->model->modelSettings()->count())->toBe(1);

    $this->model->settings()->clear();
    expect($this->model->settings()->all())->toBe([]);
    expect($this->model->modelSettings()->count())->toBe(0);
});

test('add eager constraints', function () {
    set_error_handler(static function (int $errno, string $errstr) {
        throw new Exception($errstr, $errno);
    }, E_USER_WARNING);
    
    $this->model = UsersWithTableAndDefaultSettings::first();

    $this->model->settings()->apply($this->testArray);
    expect(ModelSettings::all()->count())->toBe(1);

    $this->expectExceptionMessage('addEagerConstraints');

    $this->model->load('settings')->settings()->set('test', 'test');
    $this->model->load('modelSettings')->settings()->set('test', 'test');
});
