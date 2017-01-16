<?php

namespace Aiddroid\Social\Providers;

use Aiddroid\Social\User;
use GuzzleHttp\Psr7\Response;

/**
 * Class GithubProvider.
 */
class GithubProvider extends AbstractProvider
{
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

        return 'https://github.com/login/oauth/authorize'.'?'.http_build_query($params);
    }

    protected function buildAuthParams()
    {
        $params = [
            'client_id'    => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope'        => $this->getScopeParams(),
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
        return 'https://github.com/login/oauth/access_token';
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
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUrl,
            'code'          => $code,
            'state'         => $this->getState(),
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
        parse_str($response->getBody(), $result);

        return isset($result['access_token']) ? $result['access_token'] : null;
    }

    /**
     * Get user's profile url.
     *
     * @return mixed
     */
    protected function getUserProfileUrl()
    {
        return 'https://api.github.com/user';
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
        $user = new User($userProfile['id'], $userProfile['login'], $userProfile['avatar_url'], $userProfile);

        return $user;
    }
}
