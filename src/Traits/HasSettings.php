<?php

namespace Lukasss93\ModelSettings\Traits;

use Lukasss93\ModelSettings\Contracts\SettingsManagerContract;

trait HasSettings
{
    public function settingsRules(): array
    {
        return [];
    }

    public function defaultSettings(): array
    {
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
            $this->settings()->apply($this->defaultSettings());
        }
    }
}
