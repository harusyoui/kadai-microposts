<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts(){
        return $this->hasMany(Micropost::class);
    }
    
    public function followings(){
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers(){
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId){
        // 既にフォローしているのかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if($exist || $its_me){
            return false;
        }else{
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId){
        // 既にフォローしているのかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if($exist && !$its_me){
            $this->followings()->attach($userId);
            return true;
        }else{
            return false;
        }
    }
    
    public function is_following($userId){
        return $this->followings()->where('follow_id',$userId)->exists();
    }
    
    public function feed_microposts(){
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id',$follow_user_ids);
    }
    
    public function favoritings()
    {
        return $this->belongsToMany(Micropost::class, 'micropost_favorite', 'user_id', 'favorite_id')->withTimestamps();
    }
    
    public function favorite($userId)
    {
        // 既にお気に入りしているかの確認
        $exist = $this->is_favoriting($userId);
    
        if ($exist) {
            // 既にお気に入りしていれば何もしない
            return false;
        } else {
            // 未お気に入りであればお気に入りする
            $this->favoritings()->attach($userId);
            return true;
        }
    }
    
    public function unfavorite($userId)
    {
        // 既にお気に入りしているかの確認
        $exist = $this->is_favoriting($userId);
    
        if ($exist) {
            // 既にお気に入りしていればお気に入りを外す
            $this->favoritings()->detach($userId);
            return true;
        } else {
            // 未お気に入りであれば何もしない
            return false;
        }
    }
    
    public function is_favoriting($userId) {
        return $this->favoritings()->where('favorite_id', $userId)->exists();
    }
    
    public function favorite_microposts(){
        $favorite_micropost_ids = $this->favoritings()->pluck('microposts.id')->toArray();
        $favorite_micropost_ids[] = $this->id;
        return Micropost::whereIn('user_id',$favorite_micropost_ids);
    }
}
