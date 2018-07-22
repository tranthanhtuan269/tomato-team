<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Events\NewMessage;
use App\Events\AddConversation;
use Illuminate\Http\Request;
use DB;

class ConversationController extends Controller
{
    public function store(Request $request)
    {
        $conversation = Conversation::create([
            'message' => request('message'),
            'group_id' => request('group_id'),
            'type' => request('type'),
            'user_id' => auth()->user()->id,
            'conversation' => request('conversation'),
        ]);

        broadcast(new NewMessage($conversation))->toOthers();

        return $conversation->load('user');
    }

    public function addConversation()
    {
        $max = Conversation::where('group_id', request('group_id'))->where('type', 0)->max('conversation');
        $listReturn = [];
        $conversation = Conversation::create([
            'message' => '',
            'group_id' => request('group_id'),
            'type' => 0,
            'user_id' => auth()->user()->id,
            'conversation' => ($max + 1)
        ]);
        $conversation1 = Conversation::create([
            'message' => '',
            'group_id' => request('group_id'),
            'type' => 1,
            'user_id' => auth()->user()->id,
            'conversation' => ($max + 1)
        ]);
        $conversation2 = Conversation::create([
            'message' => '',
            'group_id' => request('group_id'),
            'type' => 2,
            'user_id' => auth()->user()->id,
            'conversation' => ($max + 1)
        ]);
        $listReturn[] = $conversation->load('user');
        $listReturn[] = $conversation1->load('user');
        $listReturn[] = $conversation2->load('user');
        broadcast(new AddConversation($conversation))->toOthers();

        return $listReturn;
    }

    public function getListConversation($group){
        $listReturn = [];
        $listConversation = DB::table('conversations')->select('conversation')->orderBy('conversation')->where('group_id', $group)->distinct()->get();
        foreach($listConversation as $itemConversation){
            for($i = 0; $i < 3; $i++){
                $conv = Conversation::where('group_id', $group)->where('conversation', $itemConversation->conversation)->where('type', $i)->orderBy('created_at','desc')->first();
                if($conv){
                    $listReturn[] = $conv->load('user');
                }else{
                    $conv = Conversation::create([
                        'message' => '',
                        'group_id' => $group,
                        'type' => $i,
                        'user_id' => auth()->user()->id,
                        'conversation' => $itemConversation->conversation
                    ]);
                    $listReturn[] = $conv->load('user');
                }
            }
        }
        return $listReturn;
    }

    public function getLastest($group, $conversation, $id){
        $conv = Conversation::where('group_id', $group)->where('conversation', $conversation)->where('type', $id)->orderBy('created_at','desc')->first();
        if($conv){
            return $conv->load('user');
        }
        else{
            $conv = Conversation::create([
                'message' => '',
                'group_id' => $group,
                'type' => $id,
                'user_id' => auth()->user()->id,
                'conversation' => $conversation
            ]);
            return $conv->load('user');
        }
    }
}
