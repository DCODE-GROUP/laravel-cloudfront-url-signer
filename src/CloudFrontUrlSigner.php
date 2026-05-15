<?php

namespace Dcodegroup\CloudFrontUrlSigner;

use DateTime;
use Dcodegroup\CloudFrontUrlSigner\Exceptions\InvalidExpiration;

class CloudFrontUrlSigner implements UrlSigner
{
    /**
     * CloudFront client object.
     *
     * @var \Aws\CloudFront\UrlSigner
     */
    private $urlSigner;

    public function __construct(\Aws\CloudFront\UrlSigner $urlSigner)
    {
        $this->urlSigner = $urlSigner;
    }

    /**
     * Get a secure URL to a controller action.
     *
     * @param  DateTime|int|null  $expiration
     *
     * @throws InvalidExpiration
     */
    public function sign(string $url, $expiration = null): string
    {
        $expiration = $this->getExpirationTimestamp($expiration ??
            config('cloudfront-url-signer.default_expiration_time_in_days'));

        return $this->urlSigner->getSignedUrl($url, $expiration);
    }

    /**
     * Check if a timestamp is in the future.
     */
    protected function isFuture(int $timestamp): bool
    {
        return ((int) $timestamp) >= (new DateTime)->getTimestamp();
    }

    /**
     * Retrieve the expiration timestamp for a link based on an absolute DateTime or a relative number of days.
     *
     * @param  DateTime|int  $expiration  The expiration date of this link.
     *                                    - DateTime: The value will be used as expiration date
     *                                    - int: The expiration time will be set to X days from now
     *
     * @throws InvalidExpiration
     */
    protected function getExpirationTimestamp(DateTime|int $expiration): int
    {
        if (is_int($expiration)) {
            $expiration = (new DateTime)->modify((int) $expiration.' days');
        }

        if (! $this->isFuture($expiration->getTimestamp())) {
            throw new InvalidExpiration('Expiration date must be in the future');
        }

        return $expiration->getTimestamp();
    }
}
