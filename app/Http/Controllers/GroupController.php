<?php

namespace App\Http\Controllers;

use App\Events\GroupCreated;
use App\Events\GroupUpdated;
use App\User;
use App\Group;
use App\Conversation;
use Illuminate\Http\Request;
use Redirect;
use Session;
use Auth;
use DB;

class GroupController extends Controller
{
	public function index(){
		if(Auth::check()){
			if(auth()->user()->permission == 1){
				// $groups = Group::orderBy('created_at', 'desc')->get();
				$sql = "SELECT ";
				$sql .= "groups.id as id, ";
				$sql .= "groups.name as name, ";
				$sql .= "groups.status as status, ";
				$sql .= "groups.created_at as created_at, ";
				$sql .= "GROUP_CONCAT(CONCAT(users.name, ' ( ', (case when (group_user.type = 0) THEN 'admin' ELSE case when (group_user.type = 1) THEN 'source team' ELSE 'target team' END END), ' )') SEPARATOR ' <br /> ') as user_info ";
				$sql .= "FROM groups ";
				$sql .= "JOIN group_user on groups.id = group_user.group_id ";
				$sql .= "JOIN users on group_user.user_id = users.id ";
				$sql .= "GROUP BY groups.id, groups.name, groups.status, groups.created_at";
				$groups = DB::select($sql);
				// dd($groups);
			}else{
				$groups = auth()->user()->groups;
			}
			return view('group.index', ['groups' => $groups]);
		}else{
			return view('error', ['message' => 'Bạn không có quyền truy cập group này!']);
		}
	}

    public function create(){
        $user = auth()->user();
        return view('group.create', ['user' => $user]);
    }

	public function edit($id){
        $group = Group::find($id);
        $user = auth()->user();
		return view('group.edit', ['group' => $group, 'user' => $user, 'users' => $group->users()->get()]);
	}

	public function show($id){
		$groups = Group::where('id', $id)->get();
		if(count($groups) == 0){
			if(!isset($type)){
				return view('error', ['message' => 'Group không tồn tại hoặc đã bị xóa!']);
			}	
		}

        $user = auth()->user();
        if($user->permission == 2){
    		$type = \DB::table('group_user')->where('group_id', $id)->where('user_id', $user->id)->first();
    		if(!isset($type)){
    			return view('error', ['message' => 'Bạn không có quyền truy cập group này!']);
    		}
    		$user->type = $type->type;
        }else{
            $user->type = 0;
        }

        return view('group.show', ['groups' => $groups, 'user' => $user]);
	}

    public function store(Request $request)
    {
        $users = collect(request('users'));
        $users2 = collect(request('users2'));

        // update busy_job
        foreach($users as $user){
            $user = User::find($user);
            $user->busy_job = 1;
            $user->save();
        }

        foreach($users2 as $user){
            $user = User::find($user);
            $user->busy_job = 1;
            $user->save();
        }

        $group = Group::create(['name' => request('name'), 'created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id]);

        $group->users()->attach(auth()->user()->id, ['type' => 0]);
        $group->users()->attach($users, ['type' => 1]);
        $group->users()->attach($users2, ['type' => 2]);

        broadcast(new GroupCreated($group))->toOthers();

        return $group;
    }

    public function update(Request $request, $id)
    {
        $users = collect(request('users'));
        $users2 = collect(request('users2'));

        // update busy_job
        foreach($users as $user){
            $user = User::find($user);
            $user->busy_job = 1;
            $user->save();
        }

        foreach($users2 as $user){
            $user = User::find($user);
            $user->busy_job = 1;
            $user->save();
        }

        $group = Group::find($id);
        $group->name = request('name');
        $group->updated_by = auth()->user()->id;
        $group->save();

        // remove attach
        Group::deleting(function($group)
        {
            $group->users()->detach();
        });

        // add new attach
        $group->users()->attach($users, ['type' => 1]);
        $group->users()->attach($users2, ['type' => 2]);

        broadcast(new GroupUpdated($group))->toOthers();

        return $group;
    }

    public function postDone(Request $request)
    {
        $group = Group::find($request->group_id);
        if(isset($group)){
        	if($request->statusType == "admin"){
            	$group->status_admin = $request->status;
            }elseif ($request->statusType == "source") {
            	$group->status_source = $request->status;
            }elseif ($request->statusType == "target") {
            	$group->status_target = $request->status;
            }
            if($group->status_admin == 1 && $group->status_source == 1 && $group->status_target == 1){
            	$group->status = 1;
            }else if($group->status_admin == 1 && $group->status_source == 3 && $group->status_target == 3){
            	$group->status = 1;
            }else if($group->status_admin == 3 && $group->status_source == 3 && $group->status_target == 3){
            	$group->status = 1;
            }else{
            	$group->status = 0;
            }
            $group->updated_by = auth()->user()->id;
            if($group->save()){
                broadcast(new GroupUpdated($group))->toOthers();
                return $group;
            }
        }
    }

    public function destroy($id)
    {
        // delete
        $group = Group::find($id);

        // free user
        foreach($group->users()->get() as $user){
            $user->busy_job = 0;
            $user->save();
        }

        Group::deleting(function($group)
		{
		    $group->users()->detach();
		});
        $group->delete();

        // redirect
        Session::flash('message', 'Xóa group thành công!');
        return Redirect::to('groups');
    }

    public function import(Request $request){
        if(!request('groupId') || !request('content')) return '';
        $listConv = explode("\n",request('content'));
        $listConversation = [];
        $max = Conversation::where('group_id', request('groupId'))->where('type', 0)->max('conversation');

        if(!isset($max)) $max = -1;
        foreach($listConv as $conv){
            if(strlen($conv) > 0){
                $listConversation[] = $conv;
                $conversation = Conversation::create([
                    'message' => $conv,
                    'group_id' => request('groupId'),
                    'type' => 0,
                    'user_id' => auth()->user()->id,
                    'conversation' => ($max + 1)
                ]);
                $conversation1 = Conversation::create([
                    'message' => '',
                    'group_id' => request('groupId'),
                    'type' => 1,
                    'user_id' => auth()->user()->id,
                    'conversation' => ($max + 1)
                ]);
                $conversation2 = Conversation::create([
                    'message' => '',
                    'group_id' => request('groupId'),
                    'type' => 2,
                    'user_id' => auth()->user()->id,
                    'conversation' => ($max + 1)
                ]);
            }
            $max++;
        }
        return $listConversation;
    }

    public function exportWord(Request $request){
        if(!isset($request->group) || !isset($request->lang)) return 'no file';

        $group = Group::find($request->group);
        if(!isset($group)) return "Group isn't existed!";
        
        $filename = $group->name . ' - ' . strtoupper($request->lang) . '.doc';
        $langSource = 'KOR';
        $langTarget = 'VIE';
        $listReturn = [];
        $htmlOutput = "";

        header("Content-Type: application/vnd.msword");
        header("Expires: 0");//no-cache
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");//no-cache
        header("content-disposition: attachment;filename=" . $filename);
        
        $listConversation = DB::table('conversations')->select('conversation')->orderBy('conversation')->where('group_id', $group->id)->distinct()->get();
        foreach($listConversation as $itemConversation){
            if($request->lang == 'vie'){
                $langSource = 'KOR';
                $langTarget = 'VIE';
                $htmlOutput .= "<tr>";
                for($i = 0; $i < 2; $i++){
                    $conv = Conversation::where('group_id', $group->id)->where('conversation', $itemConversation->conversation)->where('type', $i)->orderBy('created_at','desc')->first();
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
                    $conv = Conversation::where('group_id', $group->id)->where('conversation', $itemConversation->conversation)->where('type', $i)->orderBy('created_at','desc')->first();
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
        echo "<th style='border: 1px solid #ddd;padding: 8px;padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #ffff00;color: #000;text-align:center; width:50%;'>";
        echo "$langSource";
        echo "</th>";
        if($langTarget == 'vie')
        echo "<th style='border: 1px solid #ddd;padding: 8px;padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #ff0000;color: #000;text-align:center; width:50%;'>";
        else
        echo "<th style='border: 1px solid #ddd;padding: 8px;padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #009933;color: #000;text-align:center; width:50%;'>";
        echo "$langTarget";
        echo "</th>";
        echo "</tr>";
        echo $htmlOutput;
        echo "</table>";
        echo "</html>";
    }
}
