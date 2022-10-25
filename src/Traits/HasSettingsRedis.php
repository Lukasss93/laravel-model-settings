<?php

namespace Lukasss93\ModelSettings\Traits;

use Lukasss93\ModelSettings\Contracts\SettingsManagerContract;
use Lukasss93\ModelSettings\Managers\RedisSettingsManager;
use Illuminate\Support\Facades\Redis;

/**
 * Trait HasSettingsRedis
 * @package Lukasss93\ModelSettings\Traits
 * @property array $settings
 */
trait HasSettingsRedis
{
    use HasSettings;

    /**
     * @return \Lukasss93\ModelSettings\Contracts\SettingsManagerContract
     * @throws \Lukasss93\ModelSettings\Exceptions\ModelSettingsException
     */
    public function settings(): SettingsManagerContract
    {
        return new RedisSettingsManager($this);
    }

    public function getSettingsValue(): array
    {
        $redisValue = Redis::connection()->get($this->cacheKey());
        $value = json_decode($redisValue, true);

        return is_array($value) ? $value : [];
    }

    public function cacheKey(string $key = null): string
    {
        return sprintf(
            "r-k-%s:%s",
            $this->getTable(),
            $this->getKey()
        ) . $key;
    }

    abstract public function getTable();

    abstract public function getKey();
}
