<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UsersController extends Controller
{
    public function index(){
        $users = User::paginate(1);
        // $users = User::all();
        
        return view('users.index',[
            'users' => $users,
        ]);
    }
    
    public function show($id){
        $user = User::find($id);
        
        return view('users.show',[
            'user' => $user,
        ]);
    }
}
