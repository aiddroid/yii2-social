<?php

namespace Aiddroid\Social\Providers;

use Aiddroid\Social\User;
use GuzzleHttp\Psr7\Response;

/**
 * Class FacebookProvider.
 */
class FacebookProvider extends AbstractProvider
{
    /**
     * The profile fields.
     *
     * @var string
     */
    protected $profileFields = 'id,name,picture';

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

        return 'https://www.facebook.com/v2.8/dialog/oauth'.'?'.http_build_query($params);
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
        return 'https://graph.facebook.com/v2.8/oauth/access_token';
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

        return isset($result['access_token']) ? $result['access_token'] : null;
    }

    /**
     * Get user's profile url.
     *
     * @return mixed
     */
    protected function getUserProfileUrl()
    {
        return 'https://graph.facebook.com/v2.8/me';
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
            'fields'       => $this->profileFields,
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
        $user = new User($userProfile['id'], $userProfile['name'], $userProfile['picture'], $userProfile);

        return $user;
    }
}
