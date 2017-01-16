<?php

namespace Aiddroid\Social;

use Aiddroid\Social\Exceptions\InvalidConfigException;
use Aiddroid\Social\Interfaces\FactoryInterface;
use Aiddroid\Social\Interfaces\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Social.
 */
class Social implements FactoryInterface
{
    /**
     * The provider configs.
     *
     * @var
     */
    protected $config;

    /**
     * The HTTP request instance.
     *
     * @var
     */
    protected $request;

    /**
     * Driver provider map.
     *
     * @var array
     */
    protected $driverProviderMap = [];

    /**
     * The providers.
     *
     * @var
     */
    protected $providers;

    /**
     * Social constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->initalRequest();
        $this->initalDriverProviders();
    }

    /**
     * Set driver provider.
     *
     * @param $driverName
     * @param $providerClassName
     */
    public function setDriverProvider($driverName, $providerClassName)
    {
        $this->driverProviderMap[$driverName] = $providerClassName;
    }

    /**
     * Create provider by name.
     *
     * @param null $providerName
     *
     * @return ProviderInterface
     */
    public function driver($providerName)
    {
        if (!isset($this->providers[$providerName])) {
            $this->providers[$providerName] = $this->createProvider($providerName);
        }

        return $this->providers[$providerName];
    }

    /**
     * Create provider by name.
     *
     * @param $providerName
     *
     * @return mixed
     */
    protected function createProvider($providerName)
    {
        $config = $this->config[$providerName];

        $clientId = isset($config['client_id']) ? $config['client_id'] : null;
        $clientSecret = isset($config['client_secret']) ? $config['client_secret'] : null;
        $redirectUrl = isset($config['redirect_url']) ? $config['redirect_url'] : null;

        if (!$clientId || !$clientSecret || !$redirectUrl) {
            throw new InvalidConfigException("Invalid config for provider '{$providerName}': client_id {$clientId} client_secret {$clientSecret} redirect_uri {$redirectUrl}");
        }

        if (!isset($this->driverProviderMap[$providerName])) {
            throw new InvalidConfigException('Invalid driver: '.$providerName);
        }
        $providerClass = $this->driverProviderMap[$providerName];
        $additionConfig = isset($config['addition']) ? $config['addition'] : [];

        return new $providerClass($this->request, $clientId, $clientSecret, $redirectUrl, $additionConfig);
    }

    /**
     * Init HTTP request.
     */
    protected function initalRequest()
    {
        $this->request = Request::createFromGlobals();
        $session = \Yii::$app->getSession();
        $this->request->setSession($session);
    }

    /**
     * Init driver provider map.
     */
    protected function initalDriverProviders()
    {
        $this->driverProviderMap = [
            'weibo'    => __NAMESPACE__.'\\Providers\\WeiboProvider',
            'wechat'   => __NAMESPACE__.'\\Providers\\WechatProvider',
            'qq'       => __NAMESPACE__.'\\Providers\\QQProvider',
            'github'   => __NAMESPACE__.'\\Providers\\GithubProvider',
            'facebook' => __NAMESPACE__.'\\Providers\\FacebookProvider',
            'google'   => __NAMESPACE__.'\\Providers\\GoogleProvider',
        ];
    }
}
