<?php

namespace Lukasss93\ModelSettings\Managers;

use Lukasss93\ModelSettings\Contracts\SettingsManagerContract;
use Lukasss93\ModelSettings\Models\ModelSettings;

/**
 * Class TableSettingsManager
 * @package Lukasss93\ModelSettings\Managers
 * @property  \Illuminate\Database\Eloquent\Model|\Lukasss93\ModelSettings\Traits\HasSettingsTable $model
 */
class TableSettingsManager extends AbstractSettingsManager
{
    /**
     * @param  array  $settings
     * @return \Lukasss93\ModelSettings\Contracts\SettingsManagerContract
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function apply(array $settings = []): SettingsManagerContract
    {
        $this->validate($settings);

        $modelSettings = $this->model->modelSettings()->first();
        if (!count($settings)) {
            if ($modelSettings) {
                $modelSettings->delete();
            }
        } else {
            if (!$modelSettings) {
                $modelSettings = new ModelSettings();
                $modelSettings->setConnection($this->model->getConnectionName());
                $modelSettings->model()->associate($this->model);
            }
            $modelSettings->settings = $settings;
            $modelSettings->save();
        }

        cache()->forget($this->model->getSettingsCacheKey());

        return $this;
    }
}
