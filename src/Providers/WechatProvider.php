<?php

namespace Aiddroid\Social\Providers;

use Aiddroid\Social\User;
use GuzzleHttp\Psr7\Response;

/**
 * Class WechatProvider.
 */
class WechatProvider extends AbstractProvider
{
    /**
     * The auth scopes.
     *
     * @var array
     */
    protected $scopes = ['snsapi_login'];

    /**
     * The user openid.
     *
     * @var
     */
    protected $openid;

    /**
     * Get auth url.
     *
     * @param $state
     *
     * @return mixed
     */
    protected function getAuthUrl()
    {
        $params = $this->buildAuthParams();

        return 'https://open.weixin.qq.com/connect/qrconnect'.'?'.http_build_query($params).'#wechat_redirect';
    }

    /**
     * Build auth params.
     *
     * @return array
     */
    protected function buildAuthParams()
    {
        $params = [
            'appid'         => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'response_type' => $this->responseType,
            'scope'         => $this->getScopeParams(),
        ];

        if (!$this->isStateless()) {
            $params['state'] = $this->getState();
        }

        return $params;
    }

    /**
     * Get access token url.
     *
     * @return mixed
     */
    protected function getAccessTokenUrl()
    {
        return 'https://api.weixin.qq.com/sns/oauth2/access_token';
    }

    /**
     * Build get access token params.
     *
     * @param $code
     *
     * @return mixed
     */
    protected function buildGetAccessTokenParams($code)
    {
        $params = [
            'appid'      => $this->clientId,
            'secret'     => $this->clientSecret,
            'code'       => $code,
            'grant_type' => $this->grantType,
        ];

        return $params;
    }

    /**
     * Parse access token from response.
     *
     * @param Response $response
     *
     * @return mixed
     */
    protected function parseAccessToken(Response $response)
    {
        $result = json_decode($response->getBody(), true);
        $this->openid = isset($result['openid']) ? $result['openid'] : null;

        return isset($result['access_token']) ? $result['access_token'] : null;
    }

    /**
     * Get user's profile url.
     *
     * @return mixed
     */
    protected function getUserProfileUrl()
    {
        return 'https://api.weixin.qq.com/sns/userinfo';
    }

    /**
     * Build get user's profile params.
     *
     * @param $accessToken
     *
     * @return mixed
     */
    protected function buildGetUserProfileParams($accessToken)
    {
        $params = [
            'access_token' => $accessToken,
            'openid'       => $this->openid,
        ];

        return $params;
    }

    /**
     * Parse user's profile from response.
     *
     * @param Response $response
     *
     * @return mixed
     */
    protected function parseUser(Response $response)
    {
        $userProfile = json_decode($response->getBody(), true);
        $user = new User($userProfile['openid'], $userProfile['nickname'], $userProfile['headimgurl'], $userProfile);

        return $user;
    }
}
