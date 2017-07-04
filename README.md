# CorsBundle

Handling CORS Requests

[![Total Downloads][badge-totalDownloads-img]][badge-totalDownloads-url]

# Installation

Add composer dependency:
```
composer.phar require sokil/cors-bundle
```

Register bundle in your AppKernel:

```php
<?php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Sokil\CorsBundle\CorsBundle(),
        );
    }
}
```

# Configuration

Configure bundle in your `/app/config/config.yml`:

```yaml
cors:
  allowedOrigins: # list of hosts, allowed to do CORS requests to your app. 
    - https://fb.com
    - https://google.com
  withCredentials: true # allow send cookies to your hosts between requests
  maxAge: 86400 # agte of prefligt request cache
```

# Useage

Listener `CorsRequestListener` is listened to kernel events of requests and add headers if this is CORS request and CORS allowed for that host.

[badge-totalDownloads-img]: http://img.shields.io/packagist/dt/sokil/cors-bundle.svg?1
[badge-totalDownloads-url]: https://packagist.org/packages/sokil/cors-bundle
