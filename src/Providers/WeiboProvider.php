<?php

namespace Aiddroid\Social\Providers;

use Aiddroid\Social\User;
use GuzzleHttp\Psr7\Response;

/**
 * Class WeiboProvider.
 */
class WeiboProvider extends AbstractProvider
{
    /**
     * The Base url of APIs.
     *
     * @var string
     */
    protected $baseUrl = 'https://api.weibo.com';

    /**
     * The auth page display type.
     *
     * @var string
     */
    protected $display = 'default';

    /**
     * The indicator for force login.
     *
     * @var bool
     */
    protected $forcelogin = false;

    /**
     * The auth page language.
     *
     * @var string
     */
    protected $language = 'zh';

    /**
     * User id.
     *
     * @var
     */
    protected $uid;

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

        return $this->buildUrl('/oauth2/authorize', $params);
    }

    /**
     * Build auth params.
     *
     * @return array
     */
    protected function buildAuthParams()
    {
        $params = [
            'client_id'    => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope'        => $this->getScopeParams(),
            'display'      => $this->display,
            'forcelogin'   => $this->forcelogin,
            'language'     => $this->language,
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

    /**
     * Get access token url.
     *
     * @return string
     */
    protected function getAccessTokenUrl()
    {
        return $this->buildUrl('/oauth2/access_token');
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
            'redirect_uri'  => $this->redirectUrl,
            'code'          => $code,
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
        $result = json_decode($response->getBody(), true);
        $this->uid = isset($result['uid']) ? $result['uid'] : null;

        return isset($result['access_token']) ? $result['access_token'] : null;
    }

    /**
     * Get user's profile url.
     *
     * @return string
     */
    protected function getUserProfileUrl()
    {
        return $this->buildUrl('/2/users/show.json');
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
        $params = [
            'access_token' => $accessToken,
            'uid'          => $this->uid,
        ];

        return $params;
    }

    /**
     * Parse user's profile from response.
     *
     * @param User $user
     */
    protected function parseUser(Response $response)
    {
        $userProfile = json_decode($response->getBody(), true);
        $user = new User($userProfile['id'], $userProfile['screen_name'], $userProfile['profile_image_url'], $userProfile);

        return $user;
    }
}
