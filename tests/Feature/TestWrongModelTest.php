<?php

use Lukasss93\ModelSettings\Exceptions\ModelSettingsException;
use Lukasss93\ModelSettings\Managers\FieldSettingsManager;
use Lukasss93\ModelSettings\Tests\Models\WrongUser;
use Lukasss93\ModelSettings\Tests\Models\WrongUserWithField;

it('throws exception if model does not use HasSettings trait', function () {
    new FieldSettingsManager(WrongUser::first());
})->throws(ModelSettingsException::class, 'missing HasSettings');

it('throws exception if model does not have settings field', function () {
    $model = WrongUserWithField::first();
    $model->settings()->all();
})->throws(ModelSettingsException::class, 'Unknown field');

