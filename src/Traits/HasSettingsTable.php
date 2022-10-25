<?php

namespace Lukasss93\ModelSettings\Traits;

use Lukasss93\ModelSettings\Contracts\SettingsManagerContract;
use Lukasss93\ModelSettings\Managers\TableSettingsManager;
use Lukasss93\ModelSettings\Models\ModelSettings;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Cache;

/**
 * Trait HasSettingsTable
 * @package Lukasss93\ModelSettings\Traits
 * @property ModelSettings $modelSettings
 * @property array $settings
 * @method morphOne($model, $name)
 */
trait HasSettingsTable
{
    use HasSettings;

    /**
     * @return \Lukasss93\ModelSettings\Contracts\SettingsManagerContract
     * @throws \Lukasss93\ModelSettings\Exceptions\ModelSettingsException
     */
    public function settings(): SettingsManagerContract
    {
        return new TableSettingsManager($this);
    }

    /**
     * @return array
     */
    public function getSettingsValue(): array
    {
        if (config('model_settings.settings_table_use_cache')) {
            return Cache::rememberForever($this->getSettingsCacheKey(), function () {
                return $this->__getSettingsValue();
            });
        }

        return $this->__getSettingsValue();
    }

    private function __getSettingsValue(): array
    {
        if ($modelSettings = $this->modelSettings()->first()) {
            return $modelSettings->settings;
        }

        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function modelSettings(): MorphOne
    {
        return $this->morphOne(ModelSettings::class, 'model');
    }

    public function getSettingsCacheKey(): string
    {
        return config('model_settings.settings_table_cache_prefix') . $this->getTable() . '::' . $this->getKey();
    }

    abstract public function getTable();
}
