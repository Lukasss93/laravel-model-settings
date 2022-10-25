<?php

use Lukasss93\ModelSettings\Traits\HasSettingsField;
use Lukasss93\ModelSettings\Traits\HasSettingsRedis;
use Lukasss93\ModelSettings\Traits\HasSettingsTable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Lunaweb\RedisMock\MockPredisConnection;

beforeEach(function () {
    $this->testArray = [
        'user' => [
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => "john@doe.com",
            'age' => 27,
        ],
        'project' => [
            'name' => 'Project One',
            'description' => 'Test Description',
        ],
    ];
});

test('init', function () {
    $traits = class_uses(getModelByType('redis'));
    expect($traits)->toHaveKey(HasSettingsRedis::class);

    $traits = class_uses(getModelByType('field'));
    expect($traits)->toHaveKey(HasSettingsField::class);

    $traits = class_uses(getModelByType('text_field'));
    expect($traits)->toHaveKey(HasSettingsField::class);

    $traits = class_uses(getModelByType('table'));
    expect($traits)->toHaveKey(HasSettingsTable::class);

    expect(Redis::connection())->toBeInstanceOf(MockPredisConnection::class);
});

test('empty', function ($type) {
    $model = getModelByType($type);

    expect($model->settings()->clear()->empty())->toBeTrue();
    expect($model->settings()->apply($this->testArray)->empty())->toBeFalse();
})->with('model_types');

test('exist', function ($type) {
    $model = getModelByType($type);

    expect($model->settings()->clear()->exist())->toBeFalse();
    expect($model->settings()->apply($this->testArray)->exist())->toBeTrue();
})->with('model_types');

test('has', function ($type) {
    $model = getModelByType($type);

    expect($model->settings()->apply($this->testArray)->all())->toBe($this->testArray);
    expect($model->settings()->has('user.first_name'))->toBeTrue();
    expect($model->settings()->has('user.role'))->toBeFalse();
})->with('model_types');

test('all', function ($type) {
    $model = getModelByType($type);

    expect($model->settings()->clear()->all())->toBe([]);
    expect($model->settings()->apply($this->testArray)->all())->toBe($this->testArray);
})->with('model_types');

test('get', function ($type) {
    $model = getModelByType($type);

    $model->settings()->clear();
    expect($model->settings()->all())->toBe([]);
    expect($model->settings()->get('user'))->toBeNull();

    $model->settings()->apply($this->testArray);
    expect($model->settings()->get('user.first_name'))->toBe('John');
})->with('model_types');

test('get multiple', function ($type) {
    $model = getModelByType($type);

    $model->settings()->clear();
    expect($model->settings()->all())->toBe([]);

    $values = $model->settings()->getMultiple(['user.first_name', 'user.last_name'], 'def_val');
    expect($values)->toBe([
        'user' => [
            'first_name' => 'def_val',
            'last_name' => 'def_val',
        ],
    ]);

    $model->settings()->apply($this->testArray);
    $values = $model->settings()->getMultiple(
        ['user', 'project.name', 'date'],
        'def_val'
    );
    expect($values)->toBe([
        'user' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'age' => 27,
        ],
        'project' => [
            'name' => 'Project One',
        ],
        'date' => 'def_val',
    ]);
})->with('model_types');

test('apply', function ($type) {
    $model = getModelByType($type);
    $model->settings()->apply($this->testArray);
    expect($model->fresh()->settings()->all())->toBe($this->testArray);
})->with('model_types');

test('update', function ($type) {
    $model = getModelByType($type);

    $model->settings()->clear();
    expect($model->settings()->all())->toBe([]);

    $model->settings()->set('user.age', 18);
    expect($model->settings()->all())->toBe(['user' => ['age' => 18]]);

    $model->settings()->update('user.age', 19);
    expect($model->settings()->all())->toBe(['user' => ['age' => 19]]);
})->with('model_types');

test('set', function ($type) {
    $model = getModelByType($type);

    $model->settings()->clear();
    expect($model->settings()->all())->toBe([]);

    $model->settings()->set('user.age', 18);
    expect($model->settings()->all())->toBe(['user' => ['age' => 18]]);
})->with('model_types');

test('set multiple', function ($type) {
    $model = getModelByType($type);

    $model->settings()->clear();
    expect($model->settings()->all())->toBe([]);

    $testData = [
        'a' => 'a',
        'b' => 'b',
    ];
    $model->settings()->setMultiple($testData);
    expect($model->settings()->all())->toBe($testData);

    $model->settings()->setMultiple($this->testArray);
    expect($model->settings()->all())->toBe(array_merge($testData, $this->testArray));
})->with('model_types');

test('clear', function ($type) {
    $model = getModelByType($type);

    $model->settings()->clear()->apply($this->testArray);
    expect($model->settings()->all())->toBe($this->testArray);

    $model->settings()->clear();
    expect($model->settings()->all())->toBe([]);
})->with('model_types');

test('delete', function ($type) {
    $model = getModelByType($type);

    $model->settings()->apply($this->testArray);
    expect($model->settings()->all())->toBe($this->testArray);
    expect($model->settings()->get('user.first_name'))->toBe('John');

    $model->settings()->delete('user.first_name');
    expect($model->settings()->get('user.first_name'))->toBeNull();

    $model->settings()->delete();
    expect($model->settings()->all())->toBe([]);
})->with('model_types');

test('delete multiple', function ($type) {
    $model = getModelByType($type);

    $model->settings()->apply($this->testArray);
    expect($model->settings()->all())->toBe($this->testArray);

    $model->settings()->deleteMultiple(['user.first_name', 'user.last_name']);
    $testData = $model->settings()->get('user');

    expect($testData)
        ->not->toHaveKeys(['first_name', 'last_name'])
        ->toHaveKey('email');
})->with('model_types');

test('validate data', function ($type) {
    $model = getModelByType($type);

    $model->settings()->clear();
    expect($model->settings()->all())->toBe([]);

    $model->settings()->apply($this->testArray);

    $model->settings()->clear();
    expect($model->settings()->all())->toBe([]);

    try {
        $model->settings()->set('user.age', 'string');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('user.age');
    }

    $testArray = $this->testArray;
    $testArray['user']['age'] = 'string';
    $model->settings()->apply($testArray);

})->with('model_types')->throws(ValidationException::class);

test('default value', function ($type) {
    $model = getModelWithDefaultSettingsByType($type);

    $defaultSettingsTestArray = [
        'config' => [
            'email' => 'gmail',
            'file' => 'aws',
        ],
    ];

    $model->settings()->clear();
    expect($model->settings()->all())->toBe($defaultSettingsTestArray);

    $model->settings()->apply($this->testArray);
    expect($model->settings()->all())->toBe(array_merge($defaultSettingsTestArray, $this->testArray));

    $model->settings()->clear();
    $applyData = [
        'config' => [
            'email' => 'gmail-updated',
            'user' => 'john doe',
        ],
        'hi' => 'hello',
    ];

    expect($model->settings()->all())->toBe($defaultSettingsTestArray);

    $model->settings()->apply($applyData);
    expect($model->settings()->all())->toBe([
        'config' => [
            'email' => 'gmail-updated',
            'file' => 'aws',
            'user' => 'john doe',
        ],
        'hi' => 'hello',
    ]);

})->with('model_types');