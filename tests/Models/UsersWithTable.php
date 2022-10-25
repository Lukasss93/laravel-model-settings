<?php

namespace Lukasss93\ModelSettings\Tests\Models;

use Lukasss93\ModelSettings\Contracts\SettingsManagerContract;
use Lukasss93\ModelSettings\Traits\HasSettingsTable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersWithTable
 * @package Lukasss93\ModelSettings\Tests\Models
 * @method static first()
 * @method SettingsManagerContract config()
 */
class UsersWithTable extends Model
{
    use HasSettingsTable;

    public $invokeSettingsBy = 'config';

    protected $table = 'users_with_table';

    protected $guarded = [];

    protected $fillable = ['id', 'name'];

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
