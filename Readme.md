Apache Status Parser for PHP
============================

This is a simple class put together to curl the apache status page and parse out some of the content.

Usage
-----

```php
$status = new ApacheStatus('localhost');
echo $status->getUtilization();
```