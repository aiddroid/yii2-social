<?php
/**
 * Created by PhpStorm.
 * User: allen
 * Date: 2016/12/16
 * Time: 20:16.
 */

namespace Providers;

use Aiddroid\Social\Providers\WeiboProvider;

class WeiboProviderStub extends WeiboProvider
{
    protected function getAuthUrl($state)
    {
        return 'http://auth.url';
    }
}
