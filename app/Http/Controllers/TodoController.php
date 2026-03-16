<?php

namespace App\Http\Controllers;

use App\Models\Todo; // Todoモデルを使用するために追加  

use Illuminate\Http\Request;

class TodoController extends Controller
{
    // Todoの一覧取得
    public function index()
    {
        $todos = Todo::all();
        $result = $todos->map(function ($todo) {
            // ここでキーの変換を行う
            // toResponseはクラスのメソッドなので$this->が必要
            // mapではreturnが必須(コールバック関数の戻り値を新しい配列として返す仕組みなので、returnがないとnullの配列になってしまう)
            return $this->toResponse($todo);
        });
        return response()->json($result);
    }

    // Todoの新規作成
    // DBのカラム名はcategory_id（スネークケース）
    // Reactから送られてくるキーはcategoryId（キャメルケース）
    public function store(Request $request)
    {
        $data = Todo::create(['name' => $request->name, 'category_id' => $request->categoryId]);
        return response()->json($this->toResponse($data), 201);
    }

    // Todoのチェックの更新
    public function updateCheck(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->update(['is_check' => $request->isCheck]);
        return response()->json($this->toResponse($todo), 200);
    }

    // Todoの名前の更新
    //$todo = Todo::findOrFail($id);->update(['name' => $request->name]);
    // とすると、$categoryには更新後のカテゴリーの情報が入るのではなく、trueが入ってしまうので、あえて2行に分けてる
    public function updateName(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->update(['name' => $request->name]);
        return response()->json($this->toResponse($todo), 200);
    }

    // Todoの削除
    public function destroy($id)
    {
        Todo::findOrFail($id)->delete();
        return response()->json(['message' => 'deleted'], 200);
    }

    // DBのカラム名はcategory_id（スネークケース）
    // Reactから送られてくるキーはcategoryId（キャメルケース）
    // ここでキーの変換を行う
    private function toResponse($todo)
    {
        return [
            'id' => $todo->id,
            'name' => $todo->name,
            'isCheck' => $todo->is_check,
            'categoryId' => $todo->category_id,
        ];
    }
}
