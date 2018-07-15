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

	public function show($id){
		$groups = Group::where('id', $id)->get();
		if(count($groups) == 0){
			if(!isset($type)){
				return view('error', ['message' => 'Group không tồn tại hoặc đã bị xóa!']);
			}	
		}

        $user = auth()->user();
		$type = \DB::table('group_user')->where('group_id', $id)->where('user_id', $user->id)->first();
		if(!isset($type)){
			return view('error', ['message' => 'Bạn không có quyền truy cập group này!']);
		}
		$user->type = $type->type;

        return view('group.show', ['groups' => $groups, 'user' => $user]);
	}

    public function store(Request $request)
    {
    	// dd($request);
        $group = Group::create(['name' => request('name'), 'created_by' => auth()->user()->id, 'updated_by' => auth()->user()->id]);

        $users = collect(request('users'));
        $users2 = collect(request('users2'));
        // $users->push(auth()->user()->id);

        $group->users()->attach(auth()->user()->id, ['type' => 0]);
        $group->users()->attach($users, ['type' => 1]);
        $group->users()->attach($users2, ['type' => 2]);

        broadcast(new GroupCreated($group))->toOthers();

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
        Group::deleting(function($group)
		{
		    $group->users()->detach();
		});
        $group->delete();

        // redirect
        Session::flash('message', 'Xóa group thành công!');
        return Redirect::to('groups');
    }
}
