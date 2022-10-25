<?php

namespace Glorand\Model\Settings\Tests\Models\DefaultSettings;

use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserWithField
 * @package Glorand\Model\Settings\Tests\Models
 * @method static first()
 */
class UserWithField extends Model
{
    use HasSettingsField;

    //protected $persistSettings = true;

    protected $table = 'users_with_field';

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
