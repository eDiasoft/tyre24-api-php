<p align="center">
  <img src="https://user-images.githubusercontent.com/7081446/223246488-77debf08-5f0b-47da-b15b-a51b6038352f.png" width="128" height="128"/>
</p>
<p align="center"></p>
<h1 align="center">Tyre24 API client for PHP</h1>

![tyre24cover_readme](https://user-images.githubusercontent.com/7081446/223845481-77f883b0-6764-4224-b64f-2a0204c66e57.png)


<a href="https://www.buymeacoffee.com/shuch3n" target="_blank">
<img width="100" alt="yellow-button" src="https://user-images.githubusercontent.com/7081446/223840887-a22159f2-4830-44d5-ad68-98eaea370e66.png">
</a>

<p>
The Tyre24 API client enables developers to rapidly interact with the Tyre24 API and access data about products, orders, shipping, and more. This client was explicitly designed for Tyre24, a well-known tire and wheel marketplace.<br />
<br />
This project is an open-source. Developers can contribute and suggest new features, report issues, and provide feedback. This collaborative approach helps to keep the Tyre24 API Client up-to-date and secure, and it is essential for developers who want to work with the Tyre24 marketplace. 
</p>

## Requirements ##
To use the Tyre24 API client, the following things are required:

+ Get yourself a Tyre24 [supplier account](https://supplier.alzura.com).
+ Now you're ready to use the Tyre24 API client.
+ PHP >= 7.4
+ Up-to-date OpenSSL (or other SSL/TLS toolkit)

## Composer Installation ##

By far the easiest way to install the Tyre24 API client is to require it with [Composer](http://getcomposer.org/doc/00-intro.md).

    $ composer require ediasoft/tyre24-api-php:^1.0

    {
        "require": {
            "ediasoft/tyre24-api-php": "^1.0"
        }
    }

## Getting started ##

Initializing the Tyre24 API client, and retrieve your authenticate token.

```php
<?php

use eDiasoft\Tyre24\Tyre24Client;

$client = Tyre24Client::authenticate(env('TYRE24_USER'), env('TYRE24_PASSWORD'));

//Retrieve latest orders
$latestOrders = $client->orders->latestOrders('de');
``` 
## License
Tyre24 API PHP Client is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support ##
Contact: edisoft.com — info@edisoft.com — +31 10 84 342 77
