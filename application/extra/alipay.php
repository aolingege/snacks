<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/5/1
 * Time: 14:28
 */
return [
    //应用ID,APPID。
    'app_id' => "2016102100728313",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCKQ2pEIzXM4IoN1cMzOFXdlk8yVX2cKXWITZ92EGAQQcRytaV07yQOaz3UE9KTeT9Nu628G+HZMsJUxQjEUETagmY5nLtbeL35M2UcibYpM3e2gVTtUW86CA65GCdLzUhdIug8yf2F9zWayzG4sHZ9DcTezG6ZjFu+EtDpFgg+CtqY7n/ihjTIqeE1lX0C2ZIKpIYs7QjR8AztB/qRcpOJKRfMKGDgmT9GALN8LeFEYCbQ+W/GJHN8bQ0Bk1Ll6EKQ4cHXZ1Yko+aXaRfbXfUZYgD9hwAVlxtwZndgeFX8KapOCw0J25pzV4WkutIjMlt7I2Q1jaWNoKLuxtz4M2mzAgMBAAECggEAAhnsL4TpeGehMXyiLtEYXPm/0mACPLFUm/GyDrVJAHY/ag7gqNpJjf6LPgHfHWamU6Qai9VQpWBkG62y6Gjf4wJAU3fSUR2QpYzmaHyfTBkAJMHqbIDkU9lzf9SiJEDGbMPvC512QOb05ZlY9Bmac2QWLdylgafkbQsUKbawAWFa/BAOMIp0tgYLW8/yY2aG6jeLqhOgTo8MWIW5d1qHtX5m/x7g97dYYMdX3kTo2i1dFLUVfEOvZe4US6VBvLg71dMxwadVF5YMaY9jq/ShPD0Gkf29wdThwsjcH6u9Tq/KArQTK+z02DAGkdWOcue3pHql+gvoIA8U5uFDdIeYwQKBgQDri3jPkDKi48efdKQk38rn+CJYeNFNRAhlly3h2AHaFEY92XRlBsho/vGFg43BvHu+cMz0Run4SS8Vo09vcTIY6p2xNMffjR0w2gQqx6PUdGHBFtw7FavxN4uVtVhL6uTAqfBv97mqQO0bq+DhOGwSRNIWqvnzfXywqwmXhKYECwKBgQCWRTl6tNv8scxPq4fpRL/uw71TU6XqSS/nME7KT4uyQPAXPk0mXVVwdmyST9Crlr6O6WJopPe9nMIFUYdjdkLfGKLCR96AH3U7frr4jf60eDYEhfHGIzln/ptrTJLvvbXTaPctAaZd6TIv63QVz3yim4MMl3VSdRlrE+O9R5ZR+QKBgQDjEP8TyUSnNsJX+4/JZFwsp04kz8OlorIdjVHT5/JREz5rnVfRlGpanXqjZSCg5Vy9R+ysiDhA+/wB9f87xXmv/2ypSeJspZLAZ0uhGffbdZ5PEASaiNfKn+tWFQ3bkcOX37tDlSJM+G4bQOR2+XdlXSbSZ1yx2AT+IsQKZvvL5QKBgQCPZEUiEz0sV1kX2R2a+XCQ3RVnUxWqh+X/HPjCUr+B/DdeZqPl7QAfjdGymBkN842o/4lZQ7nnpJL70j14KpxLGM4Ox9fIuLv8ZsTxc0XOXjtle48nO+uGkc0qyWoY/RVpQ+tBdiaTzHeIhIxEV7adz/lwZYKdiYIUzGjv8ES/uQKBgCgeWysXjahCQItxx5fTrS8SQFP7Dx5vDW+UkqQ2pbL0AlHyUS7pWJj3AAe3pn4AJZZp4SZPoQP+Z8JPqDA6MrQWHYMi0XkMuMYwLWbGCkmf1MnjUxgOaLXoItjxS/y3jQfeOmHhmOAVkjnEvAh+BWlZxFMv2kiuHRU72bNa0rDI",

    //异步通知地址
    'notify_url' => "http://hfuu.s1.natapp.cc/index/order/alipayNotify",

    //同步跳转
    'return_url' => "http://hfuu.s1.natapp.cc/index/order/paySuccess",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqzWgVL/NWrJAeyEImwtaK3IDwj0dKkqUDIfqqWn5SiLaWMYi9RmKhn+jY9VM7JXEIkYYeVlqIL6Xn7OvYFRTi4buTCXGKvFLn95aDcaur77/S/0ibcdN1K2wIoHzaqQhXAb1ezKxTnFP7OLJsAL22b0NzrQDj2OH9SA06gJb8nHBfR+7Sx7DfwcqE0OtTcDHjbbcB24Qgg/dfItxoEnKuSyRVrf6BtpUnJxSzG/Ge7FfF+VBq8re1t4ZTSxaDEjto071I5VFBxr7I4SyqZsc7WpAmZL8AqUgEbQ1XYBWx2LnpJXM5GQW/thUvcDDqzea7LJNWJOQPM5DaZQgu7QuuwIDAQAB",

    'account'=>'wpicob4444@sandbox.com',

    'password'=>'111111',

    'natapp'=>'natapp -authtoken=8173c15835673ee0'
];
