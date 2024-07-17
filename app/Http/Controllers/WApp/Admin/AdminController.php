<?php

namespace App\Http\Controllers\WApp\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\AdminData;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    private Request $request;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function login(): View
    {   
        $redirect_url = $this->request->get("redirect_url", "");
        return view("admin.login")->with([
            "redirect_url" => $redirect_url
        ]);
    }

    public function signIn()
    {   
        try {
            $validator = Validator::make($this->request->all(), [
                'user_id'  => 'required|string',
                'password' => 'required|string',
            ], [
                'user_id.required'  => '아이디를 입력하세요.',
                'password.required' => '비밀번호를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $credentials = $this->request->only(["user_id", "password"]);
            $user        = AdminData::where('user_id', $credentials['user_id'])->first();
            if (!$user) {
                throw new Exception("해당 아이디의 관리자가 없습니다.");
            }
            if (!Hash::check($credentials['password'], $user->password)) {
                throw new Exception("비밀번호를 재확인 해주세요.");
            }

            session(['adminInfo' => $user]);
            AdminData::where('user_id', $credentials['user_id'])->update([
                "last_logined_at" => Carbon::now()
            ]);

            $redirect_url = $this->request->post("redirect_url");
            if( empty($redirect_url) ){
                $redirect_url = route('product.queryProductDetail');
            }

            $html = "<script>alert('" . $user->name . "님 환영합니다. '); window.location.href='" . $redirect_url . "';</script>";
            return response($html);
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function logout()
    {
        session()->forget('adminInfo');

        $html = "<script>alert('로그아웃되었습니다.'); window.location.href='" . route('wapp.admin.login') . "';</script>";
        return response($html);
    }
}
