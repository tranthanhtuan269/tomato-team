<?php

namespace App\Http\Controllers;

use App\Events\GroupCreated;
use App\Group;
use Illuminate\Http\Request;
use Auth;
use DB;

class GroupController extends Controller
{
	public function index(){
		if(Auth::check()){
			if(auth()->user()->permission == 0){
				// $groups = Group::orderBy('created_at', 'desc')->get();
				$sql = "SELECT ";
				$sql .= "groups.id as id, ";
				$sql .= "groups.name as name, ";
				$sql .= "groups.status as status, ";
				$sql .= "groups.created_at as created_at, ";
				$sql .= "GROUP_CONCAT(CONCAT(users.name, ' ( ', (case when (group_user.type = 0) THEN 'admin' ELSE case when (group_user.type = 1) THEN 'source team' ELSE 'target team' END END), ' )') SEPARATOR ' <br /> ') as users ";
				$sql .= "FROM groups ";
				$sql .= "JOIN group_user on groups.id = group_user.group_id ";
				$sql .= "JOIN users on group_user.user_id = users.id ";
				$sql .= "GROUP BY groups.id, groups.name, groups.status, groups.created_at";
				$groups = DB::select($sql);
			}else{
				$groups = auth()->user()->groups;
			}
			return view('group.index', ['groups' => $groups]);
		}else{
			return view('error', ['message' => 'Bạn không có quyền truy cập group này!']);
		}
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

    public function store()
    {
        $group = Group::create(['name' => request('name')]);

        $users = collect(request('users'));
        $users->push(auth()->user()->id);

        $group->users()->attach($users);

        broadcast(new GroupCreated($group))->toOthers();

        return $group;
    }
}
