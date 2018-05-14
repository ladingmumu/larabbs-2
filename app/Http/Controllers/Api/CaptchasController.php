<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Requests\Api\CaptchaRequest;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, CaptchaBuilder $captcha_builder)
    {
        $key = 'captcha_' . str_random(15);
        $phone = $request->phone;

        $captcha = $captcha_builder->build();
        $expired_at = now()->addMinutes(2);
        \Cache::put($key, ['phone' => $phone, 'code' => $captcha->getPhrase()], $expired_at);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expired_at->toDateTimeString(),
            'captcha_image_content' => $captcha->inline(),
        ];

        return $this->response->array($result)->setStatusCode(201);
    }
}
