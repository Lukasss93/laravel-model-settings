<?php

namespace Lukasss93\ModelSettings\Managers;

use Lukasss93\ModelSettings\Contracts\SettingsManagerContract;

/**
 * Class FieldSettingsManager
 * @package Lukasss93\ModelSettings\Managers
 * @property \Illuminate\Database\Eloquent\Model|\Lukasss93\ModelSettings\Traits\HasSettingsField $model
 */
class FieldSettingsManager extends AbstractSettingsManager
{
    /**
     * @param  array  $settings
     * @return \Lukasss93\ModelSettings\Contracts\SettingsManagerContract
     */
    public function apply(array $settings = []): SettingsManagerContract
    {
        $this->validate($settings);

        $this->model->{$this->model->getSettingsFieldName()} = json_encode($settings);
        if ($this->model->isPersistSettings()) {
            $this->model->save();
        }

        return $this;
    }
}
