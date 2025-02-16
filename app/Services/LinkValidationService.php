<?php

namespace App\Services;

use App\Exceptions\ErrorJsonException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class LinkValidationService
{
    public function __construct(private Client $client)
    {
    }

    /**
     * @param string $link
     * @return void
     * @throws ErrorJsonException
     */
    public function validateLink(string $link): void
    {
        $this->validatePortNotSet($link);
        $this->validateUrlIsNotIp($link);
        $this->checkOpenLink($link);
    }

    /**
     * @param string $link
     * @return void
     * @throws ErrorJsonException
     */
    public function validatePortNotSet(string $link): void
    {
        $pattern = '/:[0-9]+/';
        if (preg_match($pattern, $link)) {
            throw new ErrorJsonException('Not allowed to specify port', 400);
        }
    }

    /**
     * @param $url
     * @return void
     * @throws ErrorJsonException
     */
    public function validateUrlIsNotIp($url): void
    {
        $patternIp = '/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(?:\/[0-9]+)?$/';
        $host = parse_url($url, PHP_URL_HOST);
        if ($host && preg_match($patternIp, $host)) {
            throw new ErrorJsonException('Not allowed to use ip as link', 400);
        }
    }

    /**
     * @param $link
     * @return void
     * @throws ErrorJsonException
     */
    public function checkOpenLink($link): void
    {
        try {
            $response = $this->client->get($link, [
                'allow_redirects' => [
                    'max' => 0,
                ],
            ]);
            if (!$response->getStatusCode() == 200) {
                throw new ErrorJsonException('You must provide valid link with correct HTTP request', 400);
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                if ($e->getResponse()->getStatusCode() >= 300 && $e->getResponse()->getStatusCode() < 400) {
                    throw new ErrorJsonException('Not allowed to use redirect links', 400);
                }
            }
            throw new ErrorJsonException('Can\'t open provided link', 400);
        } catch (GuzzleException $e) {
            throw new ErrorJsonException('Can\'t open provided link', 400);
        }

    }



}
