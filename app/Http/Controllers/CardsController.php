<?php

namespace App\Http\Controllers;

use Auth;
use App\Card;
use App\Listing;
use Validator;

use Illuminate\Http\Request;

class CardsController extends Controller
{
    // クラスが呼ばれると最初にする処理
    public function __construct()
    {
        // ログインしていなかったらログインページに遷移
        $this->middleware('auth');
    }
    
    public function new ($listing_id)
    {
        // card/new.blade.phpを表示
        return view('card/new', ['listing_id' => $listing_id]);
    }
    
    public function store(Request $request)
    {
        // 入力値チェック
        $validator = Validator::make($request->all(), ['card_title' => 'required|max:255', 'card_memo' => 'required|max:255',]);
        
        // バリデーション結果がエラーの場合
        if ($validator->fails())
        {
            // エラーを表示し現在のページを再表示。その際にユーザーの入力値を表示
            return redirect()->back()->withErrors($validator->errors())->withInput(); 
        }
        
        //$cards->listing_id = $request->listing_id; がエラーになる。
        //$request->listing_id では取り出せない？
        //viewからformで変数コントローラには渡せないのかな？
        //listing_idカラムの値はauto_increnentにすればよいのでは？やってみよう。
        //それだとlistingテーブルのidと関連付かないけどいいんだっけ？
        // Card
        $cards = new Card;
        $cards->title = $request->card_title;
        $cards->listing_id = $request->listing_id; 
        $cards->memo = $request->card_memo;
        
        $cards->save();
        
        return redirect('/');
        
    }
    
    public function show($listing_id, $card_id)
    {
        $listing = Listing::find($listing_id);
        $card = Card::find($card_id);
        
        return view('card/show', ['listing' => $listing, 'card' => $card]);
    }
    
    public function edit($listing_id, $card_id){
        $listings = Listing::where('user_id', Auth::user()->id)->get();
        $listing = Listing::find($listing_id);
        $card = Card::find($card_id);
        
        return view ('card/edit', ['listings' => $listings, 'listing' => $listing, 'card' => $card]);
    }
    
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), ['card_title' => 'required|max:255', 'card_memo' => 'required|max:255']);
        
        if ($validator -> fails())
        {
            return redirect()->back()->withErrors($validators->errors())->withInput();
            
        }
        
        $card = Card::find($request->id);
            $card->title = $request->card_title;
            $card->memo = $request->card_memo;
            $card->listing_id = $request->listing_id;
            
            $card->save();
            
            return redirect('/');
    }
    
    public function destroy($listing_id, $card_id) 
    {
        $card = Card::find($card_id);
        $card->delete();
        
        return redirect('/');
        
    }
    
    
}
