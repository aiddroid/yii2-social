# Social

A php library help you to link website with social network via oauth.

## INSTALLATION
1.install via composer
```
composer require 'aiddroid/yii2-social:dev-master'
```

## USAGE
social networks support: facebook google github weibo wechat qq

1.common config
```php
<?php

//client config
$config = [
    'weibo' => [
        'client_id' => '384556****',
        'client_secret' => '507a34706a4cfffb87f7f06*****',
        'redirect_url' => 'http://localhost.dev/callback.php',
        'addition' => [
            'scopes' => ['email']
        ]
    ],
    'qq' => [
        'client_id' => '10115****',
        'client_secret' => '2ee893df483e0a5a521d768683******',
        'redirect_url' => 'http://localhost.dev/callback.php',
    ],
    'wechat' => [
        'client_id' => 'wx8b160f63e30*****',
        'client_secret' => 'f88eede680e8e6e7a7ea4b8******',
        'redirect_url' => 'http://localhost.dev/callback.php',
    ],
    'github' => [
        'client_id' => '41473a449fb5d6******',
        'client_secret' => '5fea02f02998356b039b75f6d861f*******',
        'redirect_url' => 'http://localhost.dev/callback.php'
    ],
    'facebook' => [
        'client_id' => '99544295*****',
        'client_secret' => '5ccff1d484c5ae417f7d1aa*******',
        'redirect_url' => 'http://localhost.dev/callback.php'
    ],
    'google' => [
        'client_id' => '8983768*****-qf*******.apps.googleusercontent.com',
        'client_secret' => 'M5XhtghGBZC_Vwv*******',
        'redirect_url' => 'http://localhost.dev/callback.php'
    ]
];

```

2.auth.php (Should redirect users to third-party auth page)
```php
<?php
//github demo
$social = new Social($config);
$provider = $social->driver('github');
return $provider->redirect('http://localhost/callback.php');
```

3.callback.php (If auth success, users will be redirect to this page)
```php
<?php
//github demo
$social = new Social($config);
$provider = $social->driver('github');
if(isset($_GET['code'])){
    $user = $provider->getUser();
}
```

```
{
    "id": 3241146,
    "nickname": "aiddroid",
    "avatar": "https://avatars.githubusercontent.com/u/3241146?v=3",
    "attributes": {
        "login": "aiddroid",
        "id": 3241146,
        "avatar_url": "https://avatars.githubusercontent.com/u/3241146?v=3",
        "gravatar_id": "",
        "url": "https://api.github.com/users/aiddroid",
        ......
        "created_at": "2013-01-11T04:08:15Z",
        "updated_at": "2016-12-13T07:46:50Z"
    }
}
```

4.extend your own social network provider
- 1.extend a provider from Aiddroid\Social\Providers\AbstractProvider

- 2.usage
    ```php
    <?php
    
    $social = new Social($config);
    $social->setDriverProvider('dummysocial', Aiddroid\Social\Providers\DummysocialProvider::class);
    $provider = $social->driver('dummysocial');
    
    $provider->redirect('http://localhost.dev/callback.php')
    ```

##How to contribute?
You can contribute via github pulls. Any help appreciated (^_^)

##Have any questions?
Mail to [aiddroid@gmail.com](mailto:aiddroid@gmail.com)

##READ MORE
- google https://developers.google.com/identity/protocols/OAuth2
- facebook https://developers.facebook.com/docs/facebook-login
- github https://developer.github.com/v3/oauth/
- weibo http://open.weibo.com
- wechat https://open.weixin.qq.com
- qq http://wiki.connect.qq.com
