<?php

namespace Tests\Unit;

use App\Exceptions\ErrorJsonException;
use App\Services\LinkValidationService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;

class LinkValidationServiceTest extends TestCase
{
    protected LinkValidationService $linkValidationService;
    protected $clientMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientMock = $this->createMock(Client::class);
        $this->linkValidationService = new LinkValidationService($this->clientMock);
    }

    public function testValidatePortNotSetThrowsException()
    {
        $this->expectException(ErrorJsonException::class);

        $this->linkValidationService->validatePortNotSet('http://example.com:8080');
    }

    public function testValidateUrlIsNotIpThrowsException()
    {
        $this->expectException(ErrorJsonException::class);

        $this->linkValidationService->validateUrlIsNotIp('http://192.168.1.1');
    }

    public function testCheckOpenLinkThrowsExceptionOnRedirect()
    {
        $this->expectException(ErrorJsonException::class);

        $this->clientMock->method('get')->willThrowException(new RequestException(
            'Redirect',
            new \GuzzleHttp\Psr7\Request('GET', 'http://example.com'),
            new \GuzzleHttp\Psr7\Response(302)
        ));

        $this->linkValidationService = new LinkValidationService($this->clientMock);
        $this->linkValidationService->checkOpenLink('http://example.com');
    }

    public function testCheckOpenLinkThrowsExceptionOnInvalidLink()
    {
        $this->expectException(ErrorJsonException::class);

        $this->clientMock->method('get')->willThrowException(new RequestException(
            'Error',
            new \GuzzleHttp\Psr7\Request('GET', 'http://invalid-link.com')
        ));

        $this->linkValidationService->checkOpenLink('http://invalid-link.com');
    }

    public function testValidateLinkSuccess()
    {
        $this->clientMock->method('get')->willReturn(new \GuzzleHttp\Psr7\Response(200));

        $this->linkValidationService->validateLink('http://example.com');

        $this->assertTrue(true);
    }
}
