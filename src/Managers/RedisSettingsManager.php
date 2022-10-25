<?php

namespace Lukasss93\ModelSettings\Managers;

use Lukasss93\ModelSettings\Contracts\SettingsManagerContract;
use Illuminate\Support\Facades\Redis;

/**
 * Class FieldSettingsManager
 * @package Lukasss93\ModelSettings\Managers
 * @property \Illuminate\Database\Eloquent\Model|\Lukasss93\ModelSettings\Traits\HasSettingsRedis $model
 */
class RedisSettingsManager extends AbstractSettingsManager
{
    public function apply(array $settings = []): SettingsManagerContract
    {
        $this->validate($settings);

        Redis::set($this->model->cacheKey(), json_encode($settings));

        return $this;
    }
}
