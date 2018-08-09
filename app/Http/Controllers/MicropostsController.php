<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class MicropostsController extends Controller
{
    public function index(){
        $data =[];
        if(\Auth::check()){
            $user = \Auth::user();
            $microposts = $user->feed_microposts()->orderBy('created_at','desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'microposts' => $microposts
            ];
            $data += $this->counts($user);
            return view('users.show',$data);
        }else{
            return view('welcome');
        }
    }
    
    public function favorite(){
        $data =[];
        if(\Auth::check()){
            $user = \Auth::user();
            $favorits = $user->favorite_microposts()->orderBy('created_at','desc')->paginate(10);
            
            $data = [
                'user' => $user,
                'favorits' => $favorits
            ];
            $data += $this->counts($user);
            return view('users.favoritings',$data);
        }else{
            return view('welcome');
        }
    }
    
    public function store(Request $request){
        $this->validate($request,[
           'content' => 'required|max:191',
        ]);
        
        $request->user()->microposts()->create([
           'content' => $request->content,
        ]);
        
        return redirect()->back();
    }
    
    public function destroy($id){
        $micropost = \App\Micropost::find($id);
        
        if(\Auth::id() === $micropost->user_id){
            $micropost->delete();
        }
        
        return redirect()->back();
    }
}
