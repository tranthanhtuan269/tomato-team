<?php

namespace App\Http\Controllers;

use App\Events\GroupCreated;
use App\Group;
use Illuminate\Http\Request;
use Auth;

class GroupController extends Controller
{
	public function index(){
		if(Auth::check()){
			if(auth()->user()->permission == 0){
				$groups = Group::get();
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
