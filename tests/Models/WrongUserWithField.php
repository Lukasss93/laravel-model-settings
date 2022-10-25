<?php

namespace Lukasss93\ModelSettings\Tests\Models;

use Lukasss93\ModelSettings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WrongUserWithField
 * @package Lukasss93\ModelSettings\Tests\Models
 * @method static first()
 */
class WrongUserWithField extends Model
{
    use HasSettingsField;

    protected $table = 'wrong_users';

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
