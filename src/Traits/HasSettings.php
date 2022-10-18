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

    public function getRules(): array
    {
        return $this->settingsRules();
    }

    public function getDefaultSettings(): array
    {
        if (property_exists($this, 'defaultSettings')
            && is_array($this->defaultSettings)) {
            return Arr::wrap($this->defaultSettings);
        } elseif (($defaultSettings = config('model_settings.defaultSettings.' . $this->getTable()))
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
}
