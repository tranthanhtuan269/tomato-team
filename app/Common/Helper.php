<?php

namespace App\Common;

use DB;
use App\Group;
use App\Conversation;

class Helper
{
    static function CheckStatusGroup($group){
        $listConversation = DB::table('conversations')->where('conversation', '>', -1)->select('conversation')->orderBy('conversation')->where('group_id', $group)->distinct()->get();
        foreach($listConversation as $itemConversation){
            for($i = 0; $i < 3; $i++){
                $conv = Conversation::where('group_id', $group)->where('conversation', $itemConversation->conversation)->where('type', $i)->orderBy('created_at','desc')->first();
                if($conv->status == 0){
                    return 0;
                }
            }
        }
        return 1;
    }
}
