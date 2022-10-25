<?php

namespace Lukasss93\ModelSettings\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class WrongUser
 * @package Lukasss93\ModelSettings\Tests\Models
 * @method static first()
 */
class WrongUser extends Model
{
    protected $table = 'wrong_users';

    protected $guarded = [];

    protected $fillable = ['id', 'name'];
}
