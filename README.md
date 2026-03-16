# 作りたいもの
- APIの概要・目的
ReactのTodoリストアプリのバックエンドをSupabaseからLaravelに移行するためのREST API。
- サーバー
未定。ConoHa Claude サーバーを契約中だが、VPSは契約していない。
- APIの仕様
ベースURL：https://example.com/api
形式：JSON
認証：なし

# 制作物
https://github.com/ayaoriko/react-todo-list
- PCスペック
メモリ16GB。HDDは200/250GBなのでかなりギリギリ

#私のスキル感
WordPressエンジニアです。Reactの基礎知識があります。
Laravelはほとんど触ったことがないです。
MVCはざっくりと把握しています。

# 実装機能
- Todo の一覧取得
 - Todo の作成
- Todo の更新（完了・未完了の切り替え、テキスト編集）
- Todo の削除
- カテゴリーの一覧取得
- カテゴリーの作成
- カテゴリーのテキスト編集
- カテゴリーの削除
ユーザー認証,ユーザーごとにTodoを分ける機能はなし


# レスポンスのキー命名規則
ReactはキャメルケースでDB側はスネークケースのため、Laravelのレスポンスはキャメルケースで返す。
is_check → isCheck
category_id → categoryId

# エンドポイント一覧
## Todo

### GET /todos Todo一覧を取得する
リクエストパラメータ：なし
レスポンス（200）：
```
[
{
"id": 1,
"name": "買い物する",
"isCheck": false,
"categoryId": 2
}
]
```
### POST /todos Todoを新規作成する
リクエストボディ：
```
{
"name": "買い物する",
"categoryId": 2
}
```
※is_checkはLaravel側でfalseを自動でセットする。Reactからは送らない。
レスポンス（201）：
```
{
"id": 1,
"name": "買い物する",
"isCheck": false,
"categoryId": 2
}
```
### PUT /todos/{id}/check Todoの完了・未完了を切り替える
リクエストボディ：
```
{
"isCheck": true
}
```
レスポンス（200）：
```
{
"id": 1,
"name": "買い物する",
"isCheck": true,
"categoryId": 2
}
```
### PUT /todos/{id}/name Todoのテキストを編集する
リクエストボディ：
```
{
"name": "更新後のテキスト"
}
```
レスポンス（200）：
```
{
"id": 1,
"name": "更新後のテキスト",
"isCheck": false,
"categoryId": 2
}
```

{ "name": "行動する", "Id": 1 }

### DELETE /todos/{id} Todoを削除する
リクエストパラメータ：なし
レスポンス（200）：
```
{
"message": "deleted"
}
```
## カテゴリー
### GET /categories
カテゴリー一覧を取得する
リクエストパラメータ：なし
レスポンス（200）：
```
[
{
"id": 1,
"name": "仕事"
}
]
```
### POST /categories カテゴリーを新規作成する
リクエストボディ：
```
{
"name": "仕事"
}
レスポンス（201）：
{
"id": 1,
"name": "仕事"
}
```
### PUT /categories/{id} カテゴリー名を更新する
リクエストボディ：
```
{
"name": "プライベート"
}
```
レスポンス（200）：
```
{
"id": 1,
"name": "プライベート"
}
```
### DELETE /categories/{id} カテゴリーを削除する
処理の順番：
該当カテゴリーに紐づくTodoのcategoryIdを0（未分類）に更新する
カテゴリーを削除する
リクエストパラメータ：なし

レスポンス（200）：
```
{
"message": "deleted"
}
```
エラーレスポンス
404 Not Found（指定したidが存在しない場合）
```
{
"message": "Not Found"
}
```
422 Unprocessable Entity（バリデーションエラー）
```
{
"message": "The name field is required.",
"errors": {
"name": ["The name field is required."]
}
}
```
500 Internal Server Error（サーバーエラー）
```
{
"message": "Server Error"
}
```

----

# 初期設定
Composerのインストール

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
composer -v
→ Composerのインストール完了


composer create-project laravel/laravel todo-api
cd todo-api
php artisan serve
→ Laravelのインストール完了

# APIの動かしかた

1. データベースの設定（MySQLを使う）
2. マイグレーション（todosテーブルとcategoriesテーブルの作成）
3. モデルの作成
4. ルーティングの設定
5. コントローラーの作成

## MySQLの導入の仕方
Step 3：MySQLのインストール
brew install mysql
完了したらMySQLを起動します。
brew services start mysql
起動できたか確認します。
mysql -u root
こんな表示が出ればOKです。
mysql>

Step 4：データベースの作成
MySQLにログインした状態で以下を実行してください。
CREATE DATABASE todo_api;
作成できたか確認します。
SHOW DATABASES;
todo_apiが一覧に出ればOKです。
確認できたらMySQLを抜けます。
exit

Step 5：Laravelのデータベース設定
todo-apiフォルダの中に.envというファイルがあります。エディタで開いて、以下の部分を探してください。
```
DB_CONNECTION=sqlite
```
これを丸ごと以下に書き換えてください。
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_api
DB_USERNAME=root
DB_PASSWORD=
```
DB_PASSWORDはローカルのrootユーザーにパスワードを設定していない場合は空欄のままでOKです。

書き換えたら接続確認をします。
```
php artisan migrate
```
→ Laravelのデフォルトテーブルが3つ作成され、database/migrations/フォルダにファイルが3つ追加される

※接続時に Library not loadedのエラーが出たらPHPアップデート後にmigrade
```
brew upgrade php
```

下記コマンドを2つ打つことでdatabase/migrations/フォルダに新しいファイルが2つ作成されます。
※マイグレーション：データベースのテーブル構造を定義するファイルです。「どんなカラムを作るか」をコードで書いて、php artisan migrateを実行するとDBに反映されます。
```
php artisan make:migration create_categories_table
php artisan make:migration create_todos_table
```

作られた2026_03_16_095249_create_todos_table.phpなどのファイルを修正してDBの型を指定
```
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->boolean('is_check')->default(false);
            $table->integer('category_id')->default(0);
        });
    }
```
マイグレーションする
```
php artisan migrate
```


## Table API　で中身を見てみる

では接続設定をしましょう。TablePlusを開いて新しい接続を作成してください。
接続情報はこうなります。
```
Name：todo-api（任意）
Host：127.0.0.1
Port：3306
User：root
Password：空欄
Database：todo_api
```


## モデルを作る
```
php artisan make:model Category
php artisan make:model Todo
```

app/Models/フォルダにファイルが作成されます。

Modelsのファイルにtimestampsを無効にする設定と書き込みを許可するカラム名を指定
app/Models/Todo.php
```
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    public $timestamps = false;
    public $fillable = ['name', 'is_check', 'category_id'];
}

```

## コントローラーを作成します。
```
php artisan make:controller CategoryController
php artisan make:controller TodoController
```

app/Http/Controllers/TodoController.php
```
use App\Models\Todo; // Todoモデルを使用するために追加  

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::all();
        return response()->json($todos);
    }
}

```

## APIの出力方法を指定

コマンド打ってroutes/api.phpを作る
```
php artisan install:api
```
→質問されたらYesとおす

routes/api.php を編集
```
use App\Http\Controllers\TodoController;
Route::get('/todos', [TodoController::class, 'index']);
```

php artisan route:list
で「api/categorie」が表示されているのを確認

php artisan serve
でサーバー起動させて、
http://localhost:8000/api/todos
で表示→空の配列[]が返ってくればOKです。


## カテゴリーの作成（POST /categories）

CategoryController.php
```
class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = Category::create(['name' => $request->name]);
        return response()->json($data, 201);
    }
}
```

routes/api.php
```
Route::post('/categories', [CategoryController::class, 'store']);
```

確認方法
Thunder Clientをインストール
→左側にThunder Clientのアイコンが’表示


Thunder Clientを開いて新しいリクエストを作成してください。

Method：POST
URL：http://localhost:8000/api/categories
Body：JSON形式で以下を入力

{
"name": "仕事"
}
Sendボタンを押すとStatus: 201 Createdと表示され、responceタブやtablePressでも確認できる
http://localhost:8000/api/categories で表示される。

## カテゴリーの更新、削除

CategoryController.php
```
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
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'deleted'], 200);
    }
```

api.php
```
Route::put('/categories/{id}', [CategoryController::class, 'update']);

Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
```

php artisan route:list;で表示されているのを確認
Thunder Clientで下記指定
■更新
・PUTメゾット（Get,POSTのようなやつ）
・URL
http://localhost:8000/api/categories/1
・body
{ "name": "プライベート" }

■削除
・DELETEメゾット（Get,POSTのようなやつ）
・URL
http://localhost:8000/api/categories/1

## キーの変換
DBのカラム名はcategory_id（スネークケース）
Reactから送られてくるキーはcategoryId（キャメルケース）
そのためTodoController.phpにて変換の記述を行う


---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

