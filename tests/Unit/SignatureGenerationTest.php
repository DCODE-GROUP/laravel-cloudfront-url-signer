<?php

use Dcodegroup\CloudFrontUrlSigner\CloudFrontUrlSigner;
use Dcodegroup\CloudFrontUrlSigner\Exceptions\InvalidExpiration;
use Dcodegroup\CloudFrontUrlSigner\Exceptions\InvalidKeyPairId;

beforeEach(function () {
    $this->dummyPrivateKeyPath = 'tests/dummy-key.pem';
    $this->dummyKeyPairId = 'dummyKeyPairId';
    $this->dummyUrl = 'http://myapp.com';

    config(['cloudfront-url-signer.key_pair_id' => $this->dummyKeyPairId]);
    config(['cloudfront-url-signer.private_key_path' => $this->dummyPrivateKeyPath]);
});

it('registered cloudfront url signer in the container', function () {
    $instance = $this->app['cloudfront-url-signer'];

    expect($instance)->toBeInstanceOf(CloudFrontUrlSigner::class);
});

it('will throw an exception for an empty key pair id', function () {
    $this->expectException(InvalidKeyPairId::class);

    config(['cloudfront-url-signer.key_pair_id' => '']);

    sign($this->dummyUrl);
});

it('cant sign an url that expires at a certain time', function () {
    $expiration = DateTime::createFromFormat('d/m/Y H:i:s', '10/08/2025 18:15:44',
        new DateTimeZone('Europe/Brussels'));

    $signedUrl = sign($this->dummyUrl, $expiration);

    expect(getSignedUrlExpirationTimestamp($signedUrl))->toEqual($expiration->getTimestamp());
})->throws(InvalidExpiration::class, 'Expiration date must be in the future');

it('can sign an url that expires after a relative amount of days', function () {
    $expiration = 30;

    $signedUrl = sign($this->dummyUrl, $expiration);

    expect((new DateTime)->modify($expiration.' days')->getTimestamp() - getSignedUrlExpirationTimestamp($signedUrl))->toBeLessThanOrEqual(60);
});

it('does not allow expiration in the past when integer is given', function () {
    $this->expectException(InvalidExpiration::class);

    $expiration = -5;

    sign($this->dummyUrl, $expiration);
});

it('does not allow expiration in the past when datetime is given', function () {
    $this->expectException(InvalidExpiration::class);

    $expiration = DateTime::createFromFormat('d/m/Y H:i:s', '10/08/2005 18:15:44');

    sign($this->dummyUrl, $expiration);
});

function getSignedUrlExpirationTimestamp(string $signedUrl): int
{
    $parts = parse_url($signedUrl);
    parse_str($parts['query'], $queryParams);

    return (int) $queryParams['Expires'];
}
