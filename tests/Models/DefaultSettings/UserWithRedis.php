<?php

namespace Lukasss93\ModelSettings\Tests\Models\DefaultSettings;

use Lukasss93\ModelSettings\Traits\HasSettingsRedis;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserWithRedis
 * @package Lukasss93\ModelSettings\Tests\Models
 * @method static first()
 */
class UserWithRedis extends Model
{
    use HasSettingsRedis;

    protected $table = 'users';

    protected $guarded = [];

    protected $fillable = ['id', 'name'];

    public function defaultSettings(): array
    {
        return [
            'config' => [
                'email' => 'gmail',
                'file' => 'aws',
            ],
        ];
    }

    public function settingsRules(): array
    {
        return [
            'user' => [
                'array',
            ],
            'user.email' => [
                'string',
                'email',
            ],
            'user.age' => 'integer',
        ];
    }
}
