<?php

use Mockery as m;
use Providers\WeiboProviderStub;
use Symfony\Component\HttpFoundation\Request;

class SocialTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testSocialAuthRedirectResponse()
    {
        $request = Request::create('foo');
        $request->setSession($session = m::mock(\Symfony\Component\HttpFoundation\Session\SessionInterface::class));
        $session->shouldReceive('set')->once();

        $provider = new WeiboProviderStub($request, 'clientId', 'clientSecret', 'redirectUrl', ['display' => 'wap']);
        $response = $provider->redirect();

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\RedirectResponse::class, $response);
        $this->assertSame('http://auth.url', $response->getTargetUrl());
    }
}
