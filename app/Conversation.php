<?php

namespace App;

use App\Group;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['message', 'user_id', 'group_id', 'type', 'conversation', 'active', 'update_by'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateBy()
    {
        return $this->belongsTo(User::class, 'update_by');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
