<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;
use App\Models\User;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verify_data = \Cache::get($request->verification_key);

        if (!$verify_data) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verify_data['code'], $request->verification_code)) {
            // 返回 401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verify_data['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return $this->response->created();
    }
}