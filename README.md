# PHP HTTP Proxy

Need to be able to forward proxy HTTP requests for webservices or just serving HTML? Does `mod_proxy` not happen to be available, nor are you able to ask the sysadmin to enable it? No SSH access for setting up a real HTTP proxy, or does the firewall happen to block all the connections for you?

Then this fake PHP HTTP Proxy project is for you!

Installation
---


#### Via git:

TBD

Security notes
---
This script is not going to work right off the bat. For security reasons, by default a password is required to be included as a query parameter for each request that is made. To set this password, copy `include/config.dist.php` to `include/config.php` (unversioned) and edit it and change `$conf['expectedPasswordHash']` to a value generated by PHP `password_hash()` or `crypt()`. Then every time you invoke the script, you must pass the password the hash is for via the `pass` query parameter. It is also then advised to visit the proxy script through HTTPS to avoid having the password intercepted.

For more information on the password hash format, visit this link: [http://www.php.net/manual/en/faq.passwords.php](http://www.php.net/manual/en/faq.passwords.php)

You can also disable the requirement for passwords entirely by setting `$conf['requirePassword']` to FALSE.

Usage
---

This script can work in two ways:

#### Anatomy of a proxy request

### Standard style


In this style, you have the freedom to choose what URL to visit in your browser via query parameters.

Navigate to `http://path/to/this/project/proxy.php/scheme=http&pass=<passwordGoesHere>/url-of-choice.com` which will visit `http://url-of-choice.com` for you.

First off, you can see that there is a slash (/) after the php file. It is the default apache behavior (even if `mod_rewrite` is disabled) that it executes the php script and adds what is remaining to `$_SERVER['PATH_INFO']`.

Also note that after the slash after the script name comes a path part that looks like a query string. This is what I call "fake GET", and this allows us to pass data to our script without interfering with the real GET parameters that are to be forwarded when the proxy script makes the actual request.

The important fake GET parameters for now are:

* `pass` This is where you enter your password for the script
* `scheme` This is the protocol without `://` used for the target URL.
* `opts` Option characters. This will be mentioned later.

After the fake GET parameters comes the actual URL to call, not including the protocol, but including the query string, and using real slashes for path separators.

### Rewriting style (part of a URL to another)

This actually combines the above style with Apache RewriteRules (or whatever similar service your webserver supports).

With this you can rewrite anything from say `http://domain-with-proxy.com/a/b/c/<whatever>` to `http://the-true-domain.com/d/e/<whatever>` including query parameters.

The recommended Apache RewriteRule for this is:

```apache
RewriteRule ^a/b/c/(.*)$ /path/to/php-http-proxy/proxy.php/pass=<password>&opts=ur&scheme=http/the-true-domain.com/d/e/$1 [L,NE]
```

The benefit of this is you can proxy everything from a given path onto a completely different URL. But you need to make up a RewriteRule per destination to rewrite to in advance.

Proxifying content
---

Ah, but what good does serving proxified HTML or SOAP WSDL content do if inside them the links to resources won't work anyway?

Well good news: the script also optionally rewrites headers and attributes in XML/HTML content to make sure everything possible goes through this proxy script. Even cookies!

To enable this, include the `u` option character in the `opts` query parameter. For more information, read the [Extra options](#extra-options) section.

Note that this will only work if:

* Every URL used is an absolute URL, or
* In the case of HTML, a base href works too

Also, JavaScript may not entirely work if it is relying on absolute URLs placed in variables in HTML `<script>` tags.

A list of headers modified before requests are sent to the true url:
c
* By default: 
    * The target URL of course.
    * `Host` is rewritten to the target domain.
    * `Content-Type` is always force set.
    * `X-Forwarded-For` is set to the requester's IP address. If this field already existed, then according to the Apache documentation, the requester's IP address is instead appended to the list of IP addresses in this field.
    * `Referer` is proxified

A list of headers modified before the response is sent back to the requester:

* By default:
    * `Content-Type`. This is especially important if `u` is set and the content is rewritten.
    * `Set-Cookie`. The domain and path are rewritten. When using the proxy script in the Standard style, remember having the slash after the name of the script file still makes apache execute the script; well according to HTTP rules the .php part has no special meaning, and since you have (/) separated parts following, it was easy to abuse the rules of HTTP for easy cookie proxying even when using the Standard proxying style.
    * `Transfer-Encoding`. If it is "chunked", it is discarded entirely. Some curl requests result in "chunked" content which breaks HTTP when sending that header out. So I'm removing it entirely. If anyone knows a better way of handling this, please say so.
* If `u` is set:
    * The URLs in the content are attempted to be rewritten so any linked resource.

Extra options
---

There are several options you can pass to do several things. Each option consists of a character and can be passed to the `opts` query parameter. The possible options are the following:

* `u` Attempt to rewrite all XML/HTML attributes to make all urls in them proxified (encapsulating them in the proxy script).

HTTP clients
---

This script supports 2 HTTP clients: cURL and `file_get_contents()`.

cURL is used by default. If it is not available, the `file_get_contents()` function and stream wrappers are used instead. Note though that you need at least PHP `5.3.4` for `file_get_contents()` otherwise following `Location`s cannot be disabled and any HTTP responsed with redirects will cause problems.


Config
---
For information on configuration, read [include/config.dist.php](include/config.dist.php)
