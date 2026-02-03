<?php

namespace Fixtures\Hello\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 */
class User extends Model
{

    protected $fillable = [
        'id',
    ];
    protected $table = 'fake_users';
    public $timestamps = false;

    public static function find($id)
    {
        return new static(['id' => $id]);
    }
}
