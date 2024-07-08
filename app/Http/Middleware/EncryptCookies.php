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
        // 쿠키의 도메인을 가져옵니다.
        $cookieDomain = $this->getCookieDomain($name);

        // 현재 애플리케이션의 도메인과 비교합니다.
        if ($cookieDomain !== config('session.domain')) {
            // 도메인이 다르면 복호화를 하지 않습니다.
            return $value;
        }

        // 도메인이 같으면 정상적으로 복호화합니다.
        return parent::decryptCookie($name, $value);
    }

    /**
     * Get the domain of the given cookie.
     *
     * @param  string  $name
     * @return string|null
     */
    protected function getCookieDomain($name)
    {
        // 여기서 쿠키의 도메인을 추출하는 로직을 추가
        if (isset($_COOKIE[$name])) {
            // 도메인 추출 로직을 추가합니다.
            if( isset($_SERVER['HTTP_REFERER']) ){
                return parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
            }
        }

        return null;
    }
}
