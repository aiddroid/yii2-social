<?php

namespace Aiddroid\Social\Providers;

use Aiddroid\Social\User;
use GuzzleHttp\Psr7\Response;

/**
 * Class QQProvider.
 */
class QQProvider extends AbstractProvider
{
    /**
     * The Base url of APIs.
     *
     * @var string
     */
    protected $baseUrl = 'https://graph.qq.com';

    /**
     * The auth page display type.
     *
     * @var string
     */
    protected $display = 'default';

    /**
     * User open id.
     *
     * @var
     */
    protected $openId;

    /**
     * Get the auth url.
     *
     * @param $state
     *
     * @return string
     */
    protected function getAuthUrl()
    {
        $params = $this->buildAuthParams();

        return $this->buildUrl('/oauth2.0/authorize', $params);
    }

    /**
     * Build auth params.
     *
     * @return array
     */
    protected function buildAuthParams()
    {
        $params = [
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'scope'         => $this->getScopeParams(),
            'display'       => $this->display,
            'response_type' => $this->responseType,
        ];

        if (!$this->isStateless()) {
            $params['state'] = $this->getState();
        }

        return $params;
    }

    /**
     * Build full request url from API.
     *
     * @param $uri
     * @param array $params
     *
     * @return string
     */
    protected function buildUrl($uri, $params = [])
    {
        return $this->baseUrl.$uri.'?'.http_build_query($params);
    }

    protected function getAccessToken($code)
    {
        $response = $this->getHttpClient()->get($this->getAccessTokenUrl(), [
            'query' => $this->buildGetAccessTokenParams($code),
        ]);

        return $this->parseAccessToken($response);
    }

    /**
     * Get access token url.
     *
     * @return string
     */
    protected function getAccessTokenUrl()
    {
        return $this->buildUrl('/oauth2.0/token');
    }

    /**
     * Build get access token params.
     *
     * @param $code
     *
     * @return array
     */
    protected function buildGetAccessTokenParams($code)
    {
        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => $this->grantType,
            'code'          => $code,
            'redirect_uri'  => $this->redirectUrl,
        ];

        return $params;
    }

    /**
     * Parse access token from reponse.
     *
     * @param Response $response
     *
     * @return null
     */
    protected function parseAccessToken(Response $response)
    {
        parse_str($response->getBody(), $result);

        return isset($result['access_token']) ? $result['access_token'] : null;
    }

    /**
     * Get user's profile url.
     *
     * @return string
     */
    protected function getUserProfileUrl()
    {
        return $this->buildUrl('/user/get_user_info');
    }

    /**
     * Build get user's profile params.
     *
     * @param $accessToken
     *
     * @return array
     */
    protected function buildGetUserProfileParams($accessToken)
    {
        $response = $this->getHttpClient()->get($this->getOpenIdUrl(), [
            'query' => $this->buildGetOpenIdParams($accessToken),
        ]);

        $openid = $this->parseOpenId($response);

        $params = [
            'openid'             => $openid,
            'oauth_consumer_key' => $this->clientId,
            'access_token'       => $accessToken,
        ];

        return $params;
    }

    /**
     * Get open id url.
     *
     * @return string
     */
    protected function getOpenIdUrl()
    {
        return $this->buildUrl('/oauth2.0/me');
    }

    /**
     * Build get open id params.
     *
     * @param $accessToken
     *
     * @return array
     */
    protected function buildGetOpenIdParams($accessToken)
    {
        $params = [
            'access_token' => $accessToken,
        ];

        return $params;
    }

    /**
     * Parse open id.
     *
     * @param Response $response
     *
     * @return null
     */
    protected function parseOpenId(Response $response)
    {
        preg_match('/"openid":"(?P<openid>\w+)"/i', trim($response->getBody()), $matches);
        $openid = isset($matches['openid']) ? $matches['openid'] : null;
        $this->openId = $openid;

        return $openid;
    }

    /**
     * Parse user's profile from response.
     *
     * @param User $user
     */
    protected function parseUser(Response $response)
    {
        $userProfile = json_decode($response->getBody(), true);
        $user = new User($this->openId, $userProfile['nickname'], $userProfile['figureurl_qq_1'], $userProfile);

        return $user;
    }
}
