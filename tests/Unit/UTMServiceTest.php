<?php

namespace Tests\Unit;

use App\Services\UTMService;
use PHPUnit\Framework\TestCase;

class UTMServiceTest extends TestCase
{
    protected UTMService $utmService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->utmService = new UTMService();
    }

    public function testCheckUtmReturnsTrueWhenUtmParamsPresent()
    {
        $url = 'https://example.com?utm_source=google&utm_medium=cpc';
        $this->assertTrue($this->utmService->checkUtm($url));
    }

    public function testCheckUtmReturnsFalseWhenNoUtmParams()
    {
        $url = 'https://example.com?foo=bar';
        $this->assertFalse($this->utmService->checkUtm($url));
    }

    public function testAddUtmAddsUtmParamsToUrl()
    {
        $url = 'https://example.com';
        $utmParams = [
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'spring_sale'
        ];
        $expectedUrl = 'https://example.com?utm_source=google&utm_medium=cpc&utm_campaign=spring_sale';
        $this->assertEquals($expectedUrl, $this->utmService->addUtm($url, $utmParams));
    }

    public function testAddUtmMergesExistingAndNewUtmParams()
    {
        $url = 'https://example.com?utm_source=google';
        $utmParams = [
            'utm_medium' => 'cpc',
            'utm_campaign' => 'spring_sale'
        ];
        $expectedUrl = 'https://example.com?utm_source=google&utm_medium=cpc&utm_campaign=spring_sale';
        $this->assertEquals($expectedUrl, $this->utmService->addUtm($url, $utmParams));
    }

    public function testAddUtmDoesNotAffectOtherGetParams()
    {
        $url = 'https://example.com?foo=bar&baz=qux';
        $utmParams = [
            'utm_source' => 'google',
            'utm_medium' => 'cpc'
        ];
        $expectedUrl = 'https://example.com?foo=bar&baz=qux&utm_source=google&utm_medium=cpc';
        $this->assertEquals($expectedUrl, $this->utmService->addUtm($url, $utmParams));
    }

    public function testAddUtmPreservesExistingGetParams()
    {
        $url = 'https://example.com?utm_source=oldsource&foo=bar';
        $utmParams = [
            'utm_medium' => 'cpc',
            'utm_campaign' => 'spring_sale'
        ];
        $expectedUrl = 'https://example.com?utm_source=oldsource&foo=bar&utm_medium=cpc&utm_campaign=spring_sale';
        $this->assertEquals($expectedUrl, $this->utmService->addUtm($url, $utmParams));
    }

}
