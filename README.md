![logo](https://banners.beyondco.de/Laravel%20Model%20Settings.png?theme=dark&packageManager=composer+require&packageName=lukasss93%2Flaravel-model-settings&pattern=architect&style=style_1&description=Model+Settings+for+your+Laravel+app&md=1&showWatermark=0&fontSize=100px&images=cog)

# Laravel Model Settings

![version](https://img.shields.io/packagist/v/lukasss93/laravel-model-settings)
![downloads](https://img.shields.io/packagist/dt/lukasss93/laravel-model-settings?color=red)
![license](https://img.shields.io/github/license/lukasss93/laravel-model-settings)
![php](https://img.shields.io/packagist/php-v/lukasss93/laravel-model-settings)

[![Test](https://github.com/Lukasss93/laravel-model-settings/actions/workflows/test.yml/badge.svg)](https://github.com/Lukasss93/laravel-model-settings/actions/workflows/test.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/667bebe4a32be08138a2/maintainability)](https://codeclimate.com/github/Lukasss93/laravel-model-settings/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/667bebe4a32be08138a2/test_coverage)](https://codeclimate.com/github/Lukasss93/laravel-model-settings/test_coverage)

> Model Settings for your Laravel app

The package requires PHP ^8.0 and follows the FIG standards PSR-1, PSR-2, PSR-4 and PSR-12
to ensure a high level of interoperability between shared PHP.

Bug reports, feature requests, and pull requests can be submitted by following our [Contribution Guide](CONTRIBUTING.md)
.

## ⚠️ Forked repository

This package is a fork of [glorand/laravel-model-settings](https://github.com/glorand/laravel-model-settings) and
contains some breaking changes to make it more flexible and easier to use.

### List of changes between this package and the original one:

- Converted `defaultSettings` property to **method**
- Converted `settingsRules` property to **method**
- Removed `defaultSettings` from `model_settings.php` config file
- Converted **PHPUnit** tests to **PestPHP** tests
- Renamed `Glorand\Model\Settings` namespace to `Lukasss93\ModelSettings`

## 🚀 Installation

```shell
composer require lukasss93/laravel-model-settings
```

## ⚙ Publishing the config file

Publishing the config file is optional:

```shell
php artisan vendor:publish --provider="Lukasss93\ModelSettings\ModelSettingsServiceProvider"
```

## 🌈 Update your Eloquent Models

Your models should use the `HasSettingsField` or `HasSettingsTable` trait.

#### Option 1 - `HasSettingsField` trait

Run the `php artisan model-settings:model-settings-field` in order to create a migration file for a table.\
This command will create a json field (default name `settings`, from config) for the mentioned table.\
The default name of the field is `settings`; change the config or env value `MODEL_SETTINGS_FIELD_NAME` if you want to
rewrite the default name (**before you run the command!**)

You can choose another than default, in this case you have to specify it in you model.
```php
public $settingsFieldName = 'user_settings';
```

Complete example:
```php
use Lukasss93\ModelSettings\Traits\HasSettingsField;

class User extends Model
{
    use HasSettingsField;

    //define only if you select a different name from the default
    public $settingsFieldName = 'user_settings';
}
```

#### Option 2 - `HasSettingsTable` trait
Run before the command `php artisan model-settings:model-settings-table`.\
The command will copy for you the migration class to create the table where the setting values will be stored.\
The default name of the table is `model_settings`; change the config or env value `MODEL_SETTINGS_TABLE_NAME` if you want to rewrite the default name (**before you run the command!**)
```php
use Lukasss93\ModelSettings\Traits\HasSettingsTable;

class User extends Model
{
    use HasSettingsTable;
}
```

#### Option 3 - `HasSettingsRedis` trait
```php
use Lukasss93\ModelSettings\Traits\HasSettingsRedis;

class User extends Model
{
    use HasSettingsRedis;
}
```

## 👓 Usage

```php
$user = App\User::first();
```

#### Check id the settings for the entity is empty
```php
$user->settings()->empty();
```

#### Check settings (exist)
```php
$user->settings()->exist();
```

#### Get all model's settings
```php
$user->settings()->all();
$user->settings()->get();
```

#### Get a specific setting
```php
$user->settings()->get('some.setting');
$user->settings()->get('some.setting', 'default value');
//multiple
$user->settings()->getMultiple(
	[
		'some.setting_1',
		'some.setting_2',
	],
	'default value'
);
```

#### Add / Update setting
```php
$user->settings()->apply((array)$settings);
$user->settings()->set('some.setting', 'new value');
$user->settings()->update('some.setting', 'new value');
//multiple
$user->settings()->setMultiple([
	'some.setting_1' => 'new value 1',
	'some.setting_2' => 'new value 2',
]);
```

#### Check if the model has a specific setting
```php
$user->settings()->has('some.setting');
```

#### Remove a setting from a model
```php
$user->settings()->delete('some.setting');
//multiple
$user->settings()->deleteMultiple([
	'some.setting_1',
	'some.setting_2',
]);
//all
$user->settings()->clear();
```

### ⬇ Persistence for settings field
In case of field settings the auto-save is configurable.

**The ``default`` value is ``true``**

 - Use an attribute on model
```php
protected $persistSettings = true; //boolean
```
 - Environment (.env) variable
 ```dotenv
MODEL_SETTINGS_PERSISTENT=true
```
- Config value - model settings config file

 ```php
'settings_persistent' => env('MODEL_SETTINGS_PERSISTENT', true),
```

If the persistence is `false` you have to save the model after the operation.

### 🛠️ Using another method name other than `settings()`

If you prefer to use another name other than `settings` ,
you can do so by defining a `$invokeSettingsBy` property.
This forward calls (such as `configurations()`) to the `settings()` method.

### 🌠 Default settings

You can set default configs for a model:

```php
use Lukasss93\ModelSettings\Traits\HasSettingsTable;

class User extends Model
{
    use HasSettingsTable;
    
    public function defaultSettings(): array
    {
        return [
            'foo' => 'bar',
        ];
    }
}
```

### 🔍 Validation system for settings data

When you're using the `set()`, `apply()`, `update()` methods thrown an exception when you break a rule.\
You can define rules on model using `settingsRules` public method, and the rules array definition is identical with
the Laravel default [validation rules](https://laravel.com/docs/9.x/validation#available-validation-rules).

```php
class User extends Model
{
    use HasSettingsTable;

    public function defaultSettings(): array
    {
        return [
            'info' => [
                'email' => 'user@test.com'
                'age' => 27,
            ],
            'language' => 'en',
            'max_size' => 12,
        ];
    }

    public function settingsRules(): array
    {
        return [
            'info' => 'array',
            'info.email' => ['string','email'],
            'info.age' => 'integer',
            'language' => 'string|in:en,es,it|max:2',
            'max_size' => 'int|min:5|max:15',
        ];
    }
}

```

## 📃 Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## 📖 License
The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.