<?php

namespace Aiddroid\Social\Providers;

use Aiddroid\Social\User;
use GuzzleHttp\Psr7\Response;

/**
 * Class GoogleProvider.
 */
class GoogleProvider extends AbstractProvider
{
    /**
     * The auth scope.
     *
     * @var array
     */
    protected $scopes = ['profile'];

    /**
     * @var string
     */
    protected $prompt = 'consent';

    /**
     * @var
     */
    protected $loginHint;

    /**
     * @var
     */
    protected $includeGrantedScopes;

    /**
     * @var
     */
    protected $idToken;

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

        return 'https://accounts.google.com/o/oauth2/v2/auth'.'?'.http_build_query($params);
    }

    /**
     * Build auth params.
     *
     * @return array
     */
    protected function buildAuthParams()
    {
        $params = [
            'client_id'              => $this->clientId,
            'redirect_uri'           => $this->redirectUrl,
            'response_type'          => $this->responseType,
            'scope'                  => $this->getScopeParams(),
            'prompt'                 => $this->prompt,
            'login_hint'             => $this->loginHint,
            'include_granted_scopes' => $this->includeGrantedScopes,
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
        return 'https://accounts.google.com/o/oauth2/v4/token';
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
            'grant_type'    => $this->grantType,
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
        $this->idToken = isset($result['id_token']) ? $result['id_token'] : null;

        return isset($result['access_token']) ? $result['access_token'] : null;
    }

    /**
     * Get user's profile url.
     *
     * @return mixed
     */
    protected function getUserProfileUrl()
    {
        return 'https://www.googleapis.com/oauth2/v3/tokeninfo';
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
            'id_token' => $this->idToken,
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
        $user = new User($userProfile['sub'], $userProfile['name'], $userProfile['picture'], $userProfile);

        return $user;
    }
}
