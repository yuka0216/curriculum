<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Profile;
use App\ProfileHistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
    //
    public function add()
    {
      return view('admin.profile.create');
    }
    
    public function edit(Request $request)
    {
      $profile = Profile::find($request->id);
      if (empty($profile)){
          abort(404);
      } 
      return view('admin.profile.edit', ['profile_form' =>$profile]);
    }
    
    public function create(Request $request)
    {
      
      $this->validate($request, Profile::$rules);
      $profile = new Profile;
      $form = $request->all();
      
      unset($form['_token']);   
      
      $profile->fill($form);
      $profile->save();
      
      return redirect('admin/profile/create');
    }
    
    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        //$cond_title=ユーザーが入力した検索値
        if ($cond_title != '') {
          $posts = Profile::where('title', $cond_title)->get();
          //$cond_titleがあればそれに一致するレコードを、なければすべてのレコードを取得する
          
        } else {
          $posts = Profile::all();
        }
        return view('admin.profile.index',['posts' => $posts,
        'cond_title' => $cond_title]);
        }
    
    public function update(Request $request)
    {
        $this->validate($request, Profile::$rules);
        $profile = Profile::find($request->id);
        $profile_form = $request->all();
        
        unset($profile_form['_token']);
        
        $profile->fill($profile_form)->save();
        
        $history = new ProfileHistory;
        $history->profile_id = $profile->id;
        $history->edited_at = Carbon::now();
        $history->save();
        
        return redirect('admin/profile/edit?id=' .$profile->id);
    }
    public function delete(Request $request)
    {
        $profile = Profile::find($request->id);
        $profile->delete();
        return redirect('admin/profile/');
    }
    
}