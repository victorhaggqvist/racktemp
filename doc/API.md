RackTemp API
============

Docs for RackTemps REST API

#Authentication
HTTP Basic Authentication using username and password genarated with API key.
See example, timestamp as user and token as password.

A generated keyset is valid for give of take 16 hour, if you have your timezone configured correctly.
```php
<?php
$apikey = 'a_sample_key';
// value: a_sample_key
$timestamp = time();
// value: 1404784904
$token = hash('sha512', $timestamp . $apikey);
// value: '528451de76b5374c80e64e706442ba6042bc897a0d56f3c63740bbc1fa58edc950b113e61f67f80d1e4bb10449834ce68c2d357329706ac905286dbc9b87634b'
?>
```

#Methods
`GET /`
Correct request will be awarded with message telling that your authentication wass successfull.

`GET /test/[whatever-you-like]`
Another test to verify that your url routing settings are correct.

##Graph
`GET /graph`
Designed to give output suitable for [c3](http://c3js.org/).

Alailable spans
`GET /graph/span/hour`
`GET /graph/span/day`
`GET /graph/span/week`
`GET /graph/span/month`
