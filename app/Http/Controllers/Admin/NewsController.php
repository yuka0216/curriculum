<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\News;

class NewsController extends Controller
{
    //
    public function add()
    {
      return view('admin.news.create');
    }
    public function create(Request $request) //Requestでユーザー情報を取得、$requestに格納
    //たくさんのユーザー情報の中のどの情報を格納するのかがどこに書いてあるのか？（Controller.php?）
    {
      $this->validate($request, News::$rules);
      //$requestの中身をvalidateする
      //どのカラムにどんなバリデーションをかけるかはNews.phpの$rulesに書いてある
      
      $news = new News;//newはレコードを生成するメソッド
      $form = $request->all(); //入力された値を取得
      
      if(isset($form['image'])) {
        $path = $request->file('image')->store('public/image');
        //issetメソッドで因数の中にでーたがあるかないか判断
        //fileメソッドで画像をアップロード、storeメソッドで保存先を指定
        
        $news->image_path = basename($path);
        //画像のファイル名をテーブルのimage_pathに代入
      
      } else {
        $news->image_path = null;
        //画像がなかった場合テーブルのimage_pathにnullを代入
      }
      
      unset($form['_token']);
      unset($form['image']);
      //$form変数に入っている不要な_tokenとimageのデータを消す
      
      $news->fill($form); //$form変数の配列の中身をカラムに代入
      $news->save(); //保存
      
      return redirect('admin/news/create');
    }
}
