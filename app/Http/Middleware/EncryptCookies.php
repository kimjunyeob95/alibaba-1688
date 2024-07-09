<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Decrypt the given cookie and return the value.
     *
     * @param  string  $name
     * @param  string  $value
     * @return mixed
     */
    protected function decryptCookie($name, $value)
    {
        $decryptList = ['1688_session', 'XSRF-TOKEN'];

        /**
         * 다른 서브 도메인의 쿠키를 복호화하면 whatap에 에러로그가 발생해 특정 쿠키만 복호화
         */
        if( in_array($name, $decryptList) ){
            return parent::decryptCookie($name, $value);
        }
    }
}
