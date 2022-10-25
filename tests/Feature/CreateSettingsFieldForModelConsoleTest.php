<?php

beforeEach(function () {
    $this->table = 'users_with_field';
    $this->fieldName = 'custom_settings_field';
    $this->alreadyExistsFieldName = 'settings';
});

it('test empty table', function () {
    $this->artisan('model-settings:model-settings-field')
        ->expectsQuestion('What is the name of the table?', '')
        ->assertExitCode(1);
});

it('test missing table', function () {
    $this->artisan('model-settings:model-settings-field')
        ->expectsQuestion('What is the name of the table?', $this->table.'_wrong')
        ->assertExitCode(2);
});

it('test already exists field', function () {
    $this->artisan('model-settings:model-settings-field')
        ->expectsQuestion('What is the name of the table?', $this->table)
        ->expectsQuestion('What is the name of the settings field name?', $this->alreadyExistsFieldName)
        ->assertExitCode(3);
});

it('test create migration file', function () {
    $this->artisan('model-settings:model-settings-field')
        ->expectsQuestion('What is the name of the table?', $this->table)
        ->expectsQuestion('What is the name of the settings field name?', $this->fieldName)
        ->assertExitCode(0);
});