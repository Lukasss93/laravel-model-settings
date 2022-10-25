<?php

namespace Glorand\Model\Settings\Tests\Models;

use Glorand\Model\Settings\Contracts\SettingsManagerContract;
use Glorand\Model\Settings\Traits\HasSettingsTable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersWithTable
 * @package Glorand\Model\Settings\Tests\Models
 * @method static first()
 * @method SettingsManagerContract config()
 */
class UsersWithTableAndDefaultSettings extends Model
{
    use HasSettingsTable;

    public $invokeSettingsBy = 'config';

    protected $table = 'users_with_table';

    protected $guarded = [];

    protected $fillable = ['id', 'name'];

    public function defaultSettings(): array
    {
        return [
            'project' => 'Main Project',
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
