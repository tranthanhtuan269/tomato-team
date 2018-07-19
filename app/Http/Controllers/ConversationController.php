<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Events\NewMessage;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function store()
    {
        $conversation = Conversation::create([
            'message' => request('message'),
            'group_id' => request('group_id'),
            'type' => request('type'),
            'user_id' => auth()->user()->id,
        ]);

        broadcast(new NewMessage($conversation))->toOthers();

        return $conversation->load('user');
    }

    public function getLastest($group, $id){
        $conversation = Conversation::where('group_id', $group)->where('type', $id)->orderBy('created_at','desc')->first();
        if($conversation)
            return $conversation->load('user');
        else
            return null;
    }
}
