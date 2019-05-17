<?php

namespace Saippuakauppias\SafeBrowsing;

use Saippuakauppias\SafeBrowsing\Type\PlatformType;
use Saippuakauppias\SafeBrowsing\Type\ThreatEntryType;
use Saippuakauppias\SafeBrowsing\Type\ThreatType;
use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;

class Client
{
    protected $host = 'safebrowsing.googleapis.com';

    protected $url = '/v4/threatMatches:find?key=';

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
                $this->getRequestUrl(),
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

    public function setHost($host) {
        $this->host = $host;
    }

    protected function getRequestUrl() {
        return 'https://' . $this->host . $this->url . $this->config['api_key'];
    }

    /**
     * @return RequestFactory
     */
    protected function getRequestFactory()
    {
        return MessageFactoryDiscovery::find();
    }
}