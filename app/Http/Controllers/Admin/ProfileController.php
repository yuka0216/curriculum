<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Profile;

class ProfileController extends Controller
{
    //
    public function add()
    {
      return view('admin.profile.create');
    }
    

    
    public function edit()
    {
      return view('admin.profile.edit');
    }
    
    public function update()
    {
      return redirect('admin/profile/edit');
    }
    
    public function create(Request $request) 
    //この文の意味が分からない$requestになにが入っているのか
    {
      $this->validate($request, Profile::$rules);  //ここも
      $profile = new Profile;   //ここも
      $form = $request->all();   //ここも
      
      unset($form['_token']);    
      //tokenを削除する理由はなんでか
      //そもそもtokenとは?（フォームから送信された）データ全てのことでしょうか。）
      unset($form['image']);     //imageもなぜ削除するのか。
      
      $profile->fill($form);
      $profile->save();
      
      return redirect('admin/profile/create');
    }
}