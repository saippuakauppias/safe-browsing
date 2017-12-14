<?php

namespace BiteCodes\SafeBrowsing;

use BiteCodes\SafeBrowsing\Type\PlatformType;
use BiteCodes\SafeBrowsing\Type\ThreatEntryType;
use BiteCodes\SafeBrowsing\Type\ThreatType;
use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;

class Client
{
    protected static $url = 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var array
     */
    private $config;

    public function __construct(HttpClient $httpClient, array $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    public function lookup(
        $urls,
        array $threatTypes = [
            ThreatType::MALWARE,
            ThreatType::SOCIAL_ENGINEERING,
            ThreatType::POTENTIALLY_HARMFUL_APPLICATION,
            ThreatType::THREAT_TYPE_UNSPECIFIED,
            ThreatType::UNWANTED_SOFTWARE,
        ],
        array $platformTypes = [
            PlatformType::ANY_PLATFORM,
        ],
        array $threatEntryTypes = [
            ThreatEntryType::URL,
            ThreatEntryType::EXECUTABLE,
            ThreatEntryType::THREAT_ENTRY_TYPE_UNSPECIFIED,
        ]
    )
    {
        $request = $this
            ->getRequestFactory()
            ->createRequest(
                'POST',
                self::$url . $this->config['api_key'],
                ['Content-Type' => 'application/json'],
                json_encode([
                    'client'     => [
                        'clientId'      => $this->config['client_id'],
                        'clientVersion' => $this->config['client_version'],
                    ],
                    'threatInfo' => [
                        'threatTypes'      => $threatTypes,
                        'platformTypes'    => $platformTypes,
                        'threatEntryTypes' => $threatEntryTypes,
                        'threatEntries'    => array_map(function ($url) {
                            return ['url' => $url];
                        }, $urls),
                    ],
                ])
            );

        $response = $this->httpClient->sendRequest($request);

        return new Response($response);
    }

    /**
     * @return RequestFactory
     */
    protected function getRequestFactory()
    {
        return MessageFactoryDiscovery::find();
    }
}