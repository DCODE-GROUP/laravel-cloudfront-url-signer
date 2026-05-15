<?php

use Dcodegroup\CloudFrontUrlSigner\UrlSigner;

if (! function_exists('sign')) {
    /**
     * A helper method to sign an URL using a CloudFront canned policy.
     *
     * @param  DateTime|int|null  $expiration
     */
    function sign(string $url, $expiration = null): string
    {
        return app(UrlSigner::class)->sign($url, $expiration);
    }
}
