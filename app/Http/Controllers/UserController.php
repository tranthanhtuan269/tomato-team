<?php

namespace App\Http\Controllers;

use Request;
use App\User;
use Validator;
Use Redirect;
Use Session;
Use App\Http\Requests\StoreUser;
Use App\Http\Requests\UpdateUser;

class UserController extends Controller
{
	public function index(){
		$users = User::all();
		return view('user.index', ['users' => $users]);
	}

    public function create(){
    	return view('user.create');
    }

    public function store(StoreUser $request){
        // store
        $user = new User;
        $user->name         = Request::get('name');
        $user->email        = Request::get('email');
        $user->password     = bcrypt(Request::get('password'));
        $user->phone        = Request::get('phone');
        $user->permission   = Request::get('permission');
        $user->languages    = Request::get('languages');
        $user->save();

        // redirect
        Session::flash('message', 'Successfully created user!');
        return Redirect::to('users');
    }

    public function edit($id){
        $user = User::find($id);

    	return view('user.edit', ['user' => $user]);
    }

    public function update(UpdateUser $request, $id){
        $user = User::find($id);
        $user->name         = Request::get('name');
        $user->email        = Request::get('email');
        $user->password     = bcrypt(Request::get('password'));
        $user->phone        = Request::get('phone');
        $user->permission   = Request::get('permission');
        $user->languages    = Request::get('languages');
        $user->save();

        // redirect
        Session::flash('message', 'Successfully updated user!');
        return Redirect::to('users');
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
