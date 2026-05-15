<?php

namespace Dcodegroup\CloudFrontUrlSigner;

interface UrlSigner
{
    /**
     * Get a secure URL to a controller action.
     *
     * @param  mixed  $expiration
     */
    public function sign(string $url, $expiration): string;
}
