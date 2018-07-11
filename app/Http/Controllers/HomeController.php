<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use App\Conversation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = auth()->user()->groups;

        return view('home', ['groups' => $groups]);
    }

    public function getLastest($group, $id){
        $conversation = Conversation::where('group_id', $group)->where('type', $id)->orderBy('created_at','desc')->first();
        if($conversation)
            return $conversation->load('user');
        else
            return null;
    }
}
