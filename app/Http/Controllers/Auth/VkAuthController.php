<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use VK\Client\VKApiClient;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;

class VkAuthController extends Controller
{
    /**
     * Redirect route VK login
     *
     * @type string
     */
    public const REDIRECT_URI = 'login/vkontakte/callback';

    /**
     * VK application client ID.
     *
     * @var string
     */
    private $key;

    /**
     * VKOAuth instance
     *
     * @var VKOAuth
     */
    private $oauth;

    /**
     * VK secret
     *
     * @var string
     */
    private $secret;

    /**
     * VK OAuth request redirection full URL
     *
     * @var string
     */
    private $redirectUri;

    public function __construct(VKOAuth $VKOauth)
    {
        $this->key = env('VKONTAKTE_KEY');
        $this->oauth = $VKOauth;
        $this->redirectUri = config('app.url') . '/' . self::REDIRECT_URI;
        $this->secret = env('VKONTAKTE_SECRET');
    }

    /**
     * VK Login and register attempt
     * Request documentation can be found here: https://vk.com/dev/PHP_SDK
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function vkLogin()
    {
        return redirect($this->getRedirectUriWithCode());
    }


    public function vkCallback(Request $request, VKApiClient $vk)
    {
        if ($request->missing('code')) {
            return redirect('/');
        }

        try {
            $data = $this->getAccessTokenByCode($request->code);
        } catch (\Exception $e) {
            return redirect('/login')->with(
                'alert',
                'Сбой в работе сервера вконтакте. Обратитесь к администратору.');
        }

        $user = User::firstOrCreate(['vk_id' => $data['user_id']]);

        auth()->login($user, true);

        return redirect('/');
    }

    /**
     * Get redirect URI with code GET parameter.
     *
     * @return string
     */
    private function getRedirectUriWithCode()
    {
        return $this->oauth->getAuthorizeUrl(
            VKOAuthResponseType::CODE,
            $this->key,
            $this->redirectUri,
            VKOAuthDisplay::PAGE,
            [] //Scope
        );
    }

    /**
     * Get access Token by received code
     *
     * @param string $code
     * @return mixed
     * @throws \VK\Exceptions\VKClientException
     * @throws \VK\Exceptions\VKOAuthException
     */
    private function getAccessTokenByCode(string $code)
    {
        return $this->oauth->getAccessToken(
            $this->key,
            $this->secret,
            $this->redirectUri,
            $code
        );
    }
}
