<?php

namespace App\Services;

class UTMService
{
    /**
     * @param $url
     * @return bool
     */
    public function checkUtm($url): bool
    {
        $utmParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        $parsedUrl = parse_url($url);
        $queryParams = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        }
        return count(array_intersect_key($queryParams, array_flip($utmParams))) > 0;
    }

    /**
     * @param $url
     * @param $utmParams
     * @return string
     */
    public function addUtm($url, $utmParams): string
    {
        $parsedUrl = parse_url($url);
        $queryParams = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        }
        $queryParams = array_merge($queryParams, $utmParams);
        $queryString = http_build_query($queryParams);
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . ($parsedUrl['path'] ?? "") . '?' . $queryString;
    }

}
