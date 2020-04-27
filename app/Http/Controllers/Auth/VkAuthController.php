<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function __construct()
    {
        $this->key = env('VKONTAKTE_KEY');
        $this->secret = env('VKONTAKTE_SECRET');
        $this->redirectUri = config('app.url') . '/' . self::REDIRECT_URI;
    }

    /**
     * VK Login and register attempt
     * Request documentation can be found here: https://vk.com/dev/PHP_SDK
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function vkLogin()
    {
        $oauth = new VKOAuth();

        $redirectUrl = $oauth->getAuthorizeUrl(
            VKOAuthResponseType::CODE,
            $this->key,
            $this->redirectUri,
            VKOAuthDisplay::PAGE,
            []//Scope
        );

        return redirect($redirectUrl);
    }

    public function vkCallback(Request $request)
    {
        if ($request->missing('code')) {
            return redirect('/');
        }

        $oauth = new VKOAuth();

        $response = $oauth->getAccessToken(
            $this->key,
            $this->secret,
            $this->redirectUri,
            $request->code
        );

        dd($response);
        $access_token = $response['access_token'];
    }
}
