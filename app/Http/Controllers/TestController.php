<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conversation;
use DB;

class TestController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function test(Request $request){
        if(!isset($request->group) || !isset($request->lang)){
            return 'Yêu cầu không hợp lệ!';
        }

        $group = 11;
        $groupName = $request->group;
        $langSource = 'KOR';
        $langTarget = 'VIE';
        $listReturn = [];
        $htmlOutput = "";

        $filename = $groupName . ' - ' . strtoupper($request->lang) . '.doc';

        header("Content-Type: application/vnd.msword");
        header("Expires: 0");//no-cache
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");//no-cache
        header("content-disposition: attachment;filename=" . $filename);
        
        $listConversation = DB::table('conversations')->select('conversation')->orderBy('conversation')->where('group_id', $group)->distinct()->get();
        foreach($listConversation as $itemConversation){
            if($request->lang == 'vie'){
                $langSource = 'KOR';
                $langTarget = 'VIE';
                $htmlOutput .= "<tr>";
                for($i = 0; $i < 2; $i++){
                    $conv = Conversation::where('group_id', $group)->where('conversation', $itemConversation->conversation)->where('type', $i)->orderBy('created_at','desc')->first();
                    $htmlOutput .= "<td style='border: 1px solid #ddd;padding: 8px;'>";
                    if($conv){
                        $htmlOutput .= $conv->message;
                    }else{
                        $htmlOutput .= '';
                    }
                    $htmlOutput .= "</td>";
                }
                $htmlOutput .= "</tr>";
            }else{
                $langSource = 'KOR';
                $langTarget = 'ENG';
                $htmlOutput .= "<tr>";
                for($i = 0; $i < 3; $i += 2){
                    $conv = Conversation::where('group_id', $group)->where('conversation', $itemConversation->conversation)->where('type', $i)->orderBy('created_at','desc')->first();
                    $htmlOutput .= "<td style='border: 1px solid #ddd;padding: 8px;'>";
                    if($conv){
                        $htmlOutput .= $conv->message;
                    }else{
                        $htmlOutput .= '';
                    }
                    $htmlOutput .= "</td>";
                }
                $htmlOutput .= "</tr>";
            }
        }

        echo "<html>";
        echo "<meta charset='utf-8'>";
        echo "<table style='border-collapse: collapse;width: 100%;'>";
        echo "<tr>";
        echo "<th style='border: 1px solid #ddd;padding: 8px;padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #ffff00;color: #000;text-align:center;'>";
        echo "$langSource";
        echo "</th>";
        if($langTarget == 'vie')
        echo "<th style='border: 1px solid #ddd;padding: 8px;padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #ff0000;color: #000;text-align:center;'>";
        else
        echo "<th style='border: 1px solid #ddd;padding: 8px;padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #009933;color: #000;text-align:center;'>";
        echo "$langTarget";
        echo "</th>";
        echo "</tr>";
        echo $htmlOutput;
        echo "</table>";
        echo "</html>";
    }
}
