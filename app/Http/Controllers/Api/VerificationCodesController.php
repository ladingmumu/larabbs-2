<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\VerificationCodeRequest;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easy_sms)
    {
        $phone = $request->phone;
        $expire_minutes = 2;

        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

            try {
                $result = $easy_sms->send($phone, [
                    'content' => " {$code}为您的登录验证码，请于{$expire_minutes}分钟内填写。如非本人操作，请忽略本短信。 ",
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $response = $exception->getResponse();
                $result = json_decode($response->getBody()->getContents(), true);
                return $this->response->errorInternal($result['msg'] ?? '短信发送异常');
            }
        }

        $key = 'verificationCode_' . str_random(15);
        $expired_at = now()->addMinutes($expire_minutes);
        // 缓存验证码 10 分钟过期
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expired_at);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expired_at->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
