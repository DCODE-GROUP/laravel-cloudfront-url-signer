<?php

namespace Dcodegroup\CloudFrontUrlSigner\Tests;

use Dcodegroup\CloudFrontUrlSigner\CloudFrontUrlSignerServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            CloudFrontUrlSignerServiceProvider::class,
        ];
    }
}
