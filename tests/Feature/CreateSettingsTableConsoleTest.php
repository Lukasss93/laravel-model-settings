<?php

use Illuminate\Support\Facades\Schema;

test('empty table', function () {
    config()->set('model_settings.settings_table_name', null);
    expect(config('model_settings.settings_table_name'))->toBe(null);

    $this->artisan('model-settings:model-settings-table')
        ->assertExitCode(1);
});

test('already exists table', function () {
    config()->set('model_settings.settings_table_name', 'model_settings');
    $this->artisan('model-settings:model-settings-table')
        ->assertExitCode(2);
});

test('create migration', function () {
    config()->set('model_settings.settings_table_name', 'model_settings');
    Schema::dropIfExists(config('model_settings.settings_table_name'));
    $this->artisan('model-settings:model-settings-table')
        ->assertExitCode(0);
});

test('with update config', function () {
    expect(config('model_settings.settings_table_name'))->toBe('model_settings');

    $newTableName = 'custom_table_settings';
    config()->set('model_settings.settings_table_name', $newTableName);
    expect(config('model_settings.settings_table_name'))->toBe($newTableName);

    Schema::dropIfExists($newTableName);
    $this->artisan('model-settings:model-settings-table')
        ->assertExitCode(0);
});