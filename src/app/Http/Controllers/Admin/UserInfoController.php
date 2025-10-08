<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\EditUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class UserInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $query = User::search($request);
        $users = $query->select('id', 'name_last', 'name_first', 'email', 'created_at', 'url_transportation_expenses', 'url_schedule')
            ->paginate(20);

        // ページネーション用のパラメータ引き継ぎ
        $append_param = []; // 追加
        $append_param['search_name_last'] = $request->search_name_last;
        $append_param['search_name_first'] = $request->search_name_first;
        $append_param['search_email'] = $request->search_email;
        // 2ページ目以降にパラメーターを引き継ぐ
        $users->appends($append_param); // 追加


        //dd($request->search_name_first);
        return view('admin.userinfo.index', compact('users'))
            ->with('search_name_last', $request->search_name_last)
            ->with('search_name_first', $request->search_name_first)
            ->with('search_email', $request->search_email);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.userinfo.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        // staff_numberカラムでリクエストの値がテーブルに存在しているか確認
        $exCheck = DB::table('users')->where('staff_number', $request->staff_number)->exists();
        //dd($request, $request->name);
        if (!($exCheck)) {
            User::create([
                'name_last' => $request->name_last,
                'name_first' => $request->name_first,
                'staff_number' => $request->staff_number,
                'department_id' => $request->department_id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'url_transportation_expenses' => $request->url_transportation_expenses,
                'url_schedule' => $request->url_schedule
            ]);

            // store処理後はリダイレクトをかける必要あり。
            return to_route('admin.userinfo.index');
        } else {
            return back()->withInput()->withErrors([$request->staff_number => "社員番号は既に使われています。"]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        // $gender = CheckFormService::checkGender($contact);
        // $age = CheckFormService::checkAge($contact);

        return view('admin.userinfo.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.userinfo.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditUserRequest $request, $id)
    {

        $user = User::find($id);

        // 自身のメールアドレスは重複チェックバリデーションの対象外に設定する。
        // 'unique:users,email,' . $user->email . ',email'で設定可能
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->email . ',email']
        ]);
        // パスワードの入力があった場合は、編集対象に追加しパスワードバリデーションを有効にする。
        if ($request->get('password') !== null) {
            $request->validate([
                // 'confirmed'を設定することで、password_confirmationとの一致をチェックしてくれる
                // uncompromised()は過去にデータ漏洩したことがあるかを確認(ninja,password等)
                'password' => ['required', 'max: 128', 'confirmed', Password::min(8)->uncompromised()]
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->name_last = $request->name_last;
        $user->name_first = $request->name_first;
        $user->email = $request->email;
        $user->url_transportation_expenses = $request->url_transportation_expenses;
        $user->url_schedule = $request->url_schedule;

        // 忘れずに変更内容を保存しておく。
        $user->save();

        //return to_route('admin.userinfo.index')
        return redirect('/admin/userinfo/' . $id)
            //->with('message', $request->name_first . " " . $request->name_last . config('constants.admin.message.user_update'));
            ->with('message', strtr(config('constants.admin.message.user_update'), [
                ":name_last" => $request->name_last,
                ":name_first" => $request->name_first,
            ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return to_route('admin.userinfo.index');
    }
}
