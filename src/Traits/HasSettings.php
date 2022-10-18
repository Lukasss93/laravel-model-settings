<?php

namespace Glorand\Model\Settings\Traits;

use Glorand\Model\Settings\Contracts\SettingsManagerContract;
use Illuminate\Support\Arr;

/**
 * @property array $settingsRules
 * @property array $defaultSettings
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
trait HasSettings
{
    public function settingsRules(): array
    {
        return [];
    }

    public function getDefaultSettings(): array
    {
        if (property_exists($this, 'defaultSettings')
            && is_array($this->defaultSettings)) {
            return Arr::wrap($this->defaultSettings);
        } elseif (($defaultSettings = config('model_settings.defaultSettings.'.$this->getTable()))
            && is_array($defaultSettings)) {
            return Arr::wrap($defaultSettings);
        }

        return [];
    }

    public function __call($name, $args)
    {
        if (isset($this->invokeSettingsBy) && $name === $this->invokeSettingsBy) {
            return $this->settings();
        }

        return is_callable(['parent', '__call']) ? parent::__call($name, $args) : null;
    }

    abstract public function getSettingsValue(): array;

    abstract public function settings(): SettingsManagerContract;

    public static function bootHasSettings()
    {
        $init = function ($model) {
            if (property_exists($model, 'initSettings') && $model->initSettings) {
                $model->initSettings();
            }
        };

        static::created($init);
        static::updated($init);
    }

    public function initSettings(): void
    {
        if (!$this->modelSettings()->exists()) {
            $this->settings()->apply($this->getDefaultSettings());
        }
    }

    public static function initSettingsForAll(): void
    {
        static::all()->each(function ($model) {
            $model->initSettings();
        });
    }
}
