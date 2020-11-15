<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\News;
use App\History;
use Carbon\Carbon;

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

    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        //$cond_title=ユーザーが入力した検索値
        if ($cond_title != '') {
          $posts = News::where('title', $cond_title)->get();
          //$cond_titleがあればそれに一致するレコードを、なければすべてのレコードを取得する
          
        } else {
          $posts = News::all();
        }
        return view('admin.news.index',['posts' => $posts,
        'cond_title' => $cond_title]);
        }
    public function edit(Request $request)
    {
        $news = News::find($request->id);
        if (empty($news)){
          abort(404);
        }
        return view('admin.news.edit', ['news_form' =>$news]);
    }
    
    public function update(Request $request)
    {
        $this->validate($request, News::$rules);
        $news = News::find($request->id);
        $news_form = $request->all();
        
        if ($request->remove == 'true') {
            $news_form['image_path'] = null;
        } elseif ($request->file('image')) {
            $path = $request->file('image')->store('public/image');
              $news_form['image_path'] = basename($path);
        } else {
              $news_form['image_path'] = $news->image_path;
        }
        
        unset($news_form['image']);
        unset($news_form['remove']);
        unset($news_form['_token']);
        
        $news->fill($news_form)->save();
        
        $history = new History;
        $history->news_id = $news->id;
        $history->edited_at = Carbon::now();
        $history->save();
        
        return redirect('admin/news');
    }
    
    public function delete(Request $request)
    {
        $news = News::find($request->id);
        $news->delete();
        return redirect('admin/news/');
    }
}    