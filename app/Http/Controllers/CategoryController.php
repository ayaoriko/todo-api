<?php

namespace App\Http\Controllers;

use App\Models\Category; // Categoryモデルを使用するために追加
use App\Models\Todo; // Todoモデルを使用するために追加

use Illuminate\Http\Request; // リクエストボディを受け取るときに利用

class CategoryController extends Controller
{
    // カテゴリーの一覧取得
    public function index()
    {
        $categories = Category::all()->index();
        return response()->json($categories);
    }

    // カテゴリーの新規作成
    public function store(Request $request)
    {
        $data = Category::create(['name' => $request->name]);
        return response()->json($data, 201);
    }

    // カテゴリーの更新
    // find()→$idが存在しない場合に$categoryがnullになってエラーになります。
    // findOrFail()→$idが存在しない場合に自動で404エラーを返してくれます。

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);
        return response()->json($category, 200);
    }

    // カテゴリーの削除
    // Category::destroy($id) :存在しないIDでもエラーにならず、そのまま処理が終わる。
    //Category::findOrFail($id)->delete() 存在しないIDの場合は自動で404を返される。
    public function destroy(Request $request, $id)
    {
        // 削除する前にcategory_idが$idのTodoを全部category_id: 0に更新する
        Todo::where('category_id', $id)->update(['category_id' => 0]);

        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'deleted'], 200);
    }
}
