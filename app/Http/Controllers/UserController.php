<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
	public function index(){
		$users = User::all();
		return view('user.index', ['users' => $users]);
	}

    public function create(){
    	return view('user.create', ['users' => $users]);
    }

    public function store(Request $request){

    }

    public function edit(){
    	return view('user.edit');
    }

    public function update(Request $request){

    }

    public function destroy($id){
    	// delete
        $user = User::find($id);
        $user->delete();

        // redirect
        Session::flash('message', 'Xóa user thành công!');
        return Redirect::to('users');
    }
}
