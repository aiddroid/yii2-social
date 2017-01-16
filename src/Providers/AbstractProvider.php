<?php

namespace Aiddroid\Social\Providers;

use Aiddroid\Social\Exceptions\InvalidAuthStateException;
use Aiddroid\Social\Interfaces\ProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractProvider.
 */
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The HTTP client.
     *
     * @var
     */
    protected static $httpClient;

    /**
     * The configs.
     *
     * @var
     */
    protected $config;

    /**
     * The clientId or AppId.
     *
     * @var
     */
    protected $clientId;

    /**
     * The clientSecret or AppSecret.
     *
     * @var
     */
    protected $clientSecret;

    /**
     * The auth scopes.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * The scopes seperator.
     *
     * @var string
     */
    protected $scopeSeperator = ',';

    /**
     * The redirect url.
     *
     * @var
     */
    protected $redirectUrl;

    /**
     * The grant type for auth.
     *
     * @var string
     */
    protected $grantType = 'authorization_code';

    /**
     * The auth response type.
     *
     * @var string
     */
    protected $responseType = 'code';

    /**
     * indicator for callback state.
     *
     * @var bool
     */
    protected $stateless = false;

    /**
     * AbstractProvider constructor.
     *
     * @param Request $request
     * @param $clientId
     * @param $clientSecret
     * @param $redirectUrl
     * @param array $additionParams
     */
    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl, $additionParams = [])
    {
        $this->request = $request;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
        $this->setAdditionParams($additionParams);
    }

    /**
     * Set additional params.
     *
     * @param $additionParams
     */
    protected function setAdditionParams($additionParams)
    {
        foreach ($additionParams as $paramName => $paramValue) {
            $this->{$paramName} = $paramValue;
        }
    }

    /**
     * Redirect to auth page.
     *
     * @param string $redirectUrl
     *
     * @return RedirectResponse
     */
    public function redirect($redirectUrl = '')
    {
        $state = null;
        $redirectUrl && $this->redirectUrl = $redirectUrl;
        if (!$this->isStateless()) {
            $this->setState();
        }

        $authUrl = $this->getAuthUrl();

        return (new RedirectResponse($authUrl))->send();
    }

    /**
     * Check if the auth is stateless.
     *
     * @return bool
     */
    protected function isStateless()
    {
        return $this->stateless;
    }

    /**
     * Set auth state.
     */
    protected function setState()
    {
        $state = substr(md5(mt_rand(1, 1000000)), 0, 5);
        $this->request->getSession()->set('social-state', $state);
    }

    /**
     * Get auth state.
     *
     * @return mixed
     */
    protected function getState()
    {
        return $this->request->getSession()->get('social-state');
    }

    /**
     * Get auth url.
     *
     * @param $state
     *
     * @return mixed
     */
    abstract protected function getAuthUrl();

    /**
     * Get the user's profile.
     *
     * @param null $accessToken
     *
     * @return mixed
     */
    public function getUser($accessToken = null)
    {
        if (!$this->isValidState()) {
            throw new InvalidAuthStateException('Invalid auth state.');
        }
        $accessToken = $accessToken ? $accessToken : $this->getAccessToken($this->getCode());
        if (!$accessToken) {
            throw new EmptyAccessTokenException('Empty access token,auth failed.');
        }
        $user = $this->getUserByAccessToken($accessToken);

        return $user;
    }

    /**
     * Check if auth state is valid.
     *
     * @return bool
     */
    protected function isValidState()
    {
        if ($this->isStateless()) {
            return true;
        }

        $state = $this->request->get('state');
        $sessionState = $this->getState();

        return $state && ($state == $sessionState);
    }

    /**
     * Get access token from code.
     *
     * @param $code
     *
     * @return mixed
     */
    protected function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getAccessTokenUrl(), [
            'form_params' => $this->buildGetAccessTokenParams($code),
        ]);

        return $this->parseAccessToken($response);
    }

    /**
     * get http client instance.
     *
     * @return Client
     */
    public function getHttpClient()
    {
        if (!self::$httpClient) {
            self::$httpClient = new Client(['http_errors' => false]);
        }

        return self::$httpClient;
    }

    /**
     * Get access token url.
     *
     * @return mixed
     */
    abstract protected function getAccessTokenUrl();

    /**
     * Build get access token params.
     *
     * @param $code
     *
     * @return mixed
     */
    abstract protected function buildGetAccessTokenParams($code);

    /**
     * Parse access token from response.
     *
     * @param Response $response
     *
     * @return mixed
     */
    abstract protected function parseAccessToken(Response $response);

    /**
     * Get user's profile by access token.
     *
     * @param $accessToken
     *
     * @return mixed
     */
    protected function getUserByAccessToken($accessToken)
    {
        $response = $this->getHttpClient()->get($this->getUserProfileUrl(), [
            'query' => $this->buildGetUserProfileParams($accessToken),
        ]);

        return $this->parseUser($response);
    }

    /**
     * Get user's profile url.
     *
     * @return mixed
     */
    abstract protected function getUserProfileUrl();

    /**
     * Build get user's profile params.
     *
     * @param $accessToken
     *
     * @return mixed
     */
    abstract protected function buildGetUserProfileParams($accessToken);

    /**
     * Parse user's profile from response.
     *
     * @param Response $response
     *
     * @return mixed
     */
    abstract protected function parseUser(Response $response);

    /**
     * Get the auth code.
     *
     * @return mixed
     */
    protected function getCode()
    {
        $code = $this->request->get('code');
        if (!$code) {
            throw new EmptyAuthCodeException('Empty auth code,auth failed');
        }

        return $code;
    }

    /**
     * Get Scopes string.
     *
     * @return string
     */
    protected function getScopeParams()
    {
        return implode($this->scopeSeperator, $this->scopes);
    }
}
