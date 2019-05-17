# Safe Browsing

Client to use [Google's](https://developers.google.com/safe-browsing/v4/) and [Yandex](https://tech.yandex.ru/safebrowsing/) Safe Browsing API (v4). This library is fork of the [bitecodes/safe-browsing](https://github.com/bitecodes/safe-browsing) with some improvements and updates.

## Requirements

1. PHP7+ (tested only on PHP7.2 and PHP7.3)

2. [guzzlehttp/guzzle](guzzlehttp/guzzle) >= v6

## Installation

The preferred way to install this library is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require saippuakauppias/safe-browsing
```

or add

```
"saippuakauppias/safe-browsing": "^1.0"
```

to the `require` section of your `composer.json` file.

## Usage

```php
require 'vendor/autoload.php';

use Saippuakauppias\SafeBrowsing\Client as SBClient;
use Http\Adapter\Guzzle6\Client as GuzzleClient;


// config for Safe Browsing request
$config = [
    'api_key'           => '0123456789', // see API Keys section
    'client_id'         => 'ShortClientName', // change to your client name
    'client_version'    => '1.0.0', // change to your client version
];

// create HTTP Client
$guzzle = new GuzzleClient();

// create Safe Browsing Client from HTTP Client and config
$sbc = new SBClient($guzzle, $config);

// if you need to check urls in Yandex Safe Browsing
// uncomment next line (and change API Key of course!):
// $sbc->setHost('sba.yandex.net');

// urls array (up to 500)
$urls_need_check = [
    // yandex safe browsing test urls
    "https://ydx-phish-shavar.cepera.ru",
    "https://ydx-malware-driveby-shavar.cepera.ru",

    // google safe browsing test urls
    // (see all in: https://testsafebrowsing.appspot.com )
    "https://testsafebrowsing.appspot.com/s/phishing.html",
    "https://testsafebrowsing.appspot.com/s/malware.html",
];

// check urls in SB (execute 'threatMatches:find' request)
$result = $sbc->lookup($urls_need_check);

// example: result as php array
var_dump($result->getContent());
// array(1) {
//   ["matches"]=>
//   array(2) {
//     [0]=>
//     array(5) {
//       ["threatType"]=>
//       string(18) "SOCIAL_ENGINEERING"
//       ["platformType"]=>
//       string(12) "ANY_PLATFORM"
//       ["threat"]=>
//       array(1) {
//         ["url"]=>
//         string(52) "https://testsafebrowsing.appspot.com/s/phishing.html"
//       }
//       ["cacheDuration"]=>
//       string(4) "300s"
//       ["threatEntryType"]=>
//       string(3) "URL"
//     }
//     [1]=>
//     array(5) {
//       ["threatType"]=>
//       string(7) "MALWARE"
//       ["platformType"]=>
//       string(12) "ANY_PLATFORM"
//       ["threat"]=>
//       array(1) {
//         ["url"]=>
//         string(51) "https://testsafebrowsing.appspot.com/s/malware.html"
//       }
//       ["cacheDuration"]=>
//       string(4) "300s"
//       ["threatEntryType"]=>
//       string(3) "URL"
//     }
//   }
// }


// example: show url valid or not one by one
foreach($urls_need_check as $test_url) {
    echo $test_url . ' is valid: ' . (int) $result->isValid($test_url)  . PHP_EOL;
}
// https://ydx-phish-shavar.cepera.ru is valid: 1
// https://ydx-malware-driveby-shavar.cepera.ru is valid: 1
// https://testsafebrowsing.appspot.com/s/phishing.html is valid: 0
// https://testsafebrowsing.appspot.com/s/malware.html is valid: 0
```

## Usage Yandex Safe Browsing

To use Yandex Safe Browsing, you must change the API Key and replace the base host via the method:

```php
$sbc->setHost('sba.yandex.net');
```

## API Keys

Get Google API Key [here](https://console.cloud.google.com/apis/library/safebrowsing.googleapis.com).

Get Yandex API Key [here](https://developer.tech.yandex.ru/).


## Misc

[Usage limits](https://developers.google.com/safe-browsing/v4/usage-limits) for Google SafeBrowsing.
