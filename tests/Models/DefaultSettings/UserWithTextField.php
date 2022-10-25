<?php

namespace Lukasss93\ModelSettings\Tests\Models\DefaultSettings;

use Lukasss93\ModelSettings\Traits\HasSettingsField;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserWithTextField
 * @package Lukasss93\ModelSettings\Tests\Models
 * @method static first()
 */
class UserWithTextField extends Model
{
    use HasSettingsField;

    //protected $persistSettings = true;

    protected $table = 'users_with_text_field';

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
