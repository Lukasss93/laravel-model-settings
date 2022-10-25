<?php

use Glorand\Model\Settings\Exceptions\ModelSettingsException;
use Glorand\Model\Settings\Managers\FieldSettingsManager;
use Glorand\Model\Settings\Tests\Models\WrongUser;
use Glorand\Model\Settings\Tests\Models\WrongUserWithField;

it('throws exception if model does not use HasSettings trait', function () {
    new FieldSettingsManager(WrongUser::first());
})->throws(ModelSettingsException::class, 'missing HasSettings');

it('throws exception if model does not have settings field', function () {
    $model = WrongUserWithField::first();
    $model->settings()->all();
})->throws(ModelSettingsException::class, 'Unknown field');

