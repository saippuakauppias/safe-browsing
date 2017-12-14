<?php

namespace BiteCodes\SafeBrowsing;

use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @var ResponseInterface
     */
    protected $rawResponse;

    protected $content;

    /**
     * Response constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->rawResponse = $response;
    }

    public function allValid()
    {
        return $this->getContent() === [];
    }

    /**
     * @param null $url
     *
     * @return bool
     */
    public function isValid($url = null)
    {
        if (!$url) {
            $isValid = $this->allValid();
        } else {
            if ($this->findMatch($url)) {
                $isValid = false;
            } else {
                $isValid = true;
            }
        }

        return $isValid;
    }

    /**
     * @param $url
     *
     * @return Threat|null
     */
    public function getThreat($url)
    {
        return $this->findMatch($url);
    }

    /**
     * @return ResponseInterface
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * @return array
     */
    protected function getContent()
    {
        if (is_null($this->content)) {
            $this->content = json_decode($this->rawResponse->getBody()->getContents(), true);
        }

        return $this->content;
    }

    /**
     * @param $url
     *
     * @return Threat|null
     */
    protected function findMatch($url)
    {
        if ($this->allValid()) {
            return null;
        }

        foreach ($this->getContent()['matches'] as $match) {
            if (isset($match['threat']['url']) && $match['threat']['url'] == $url) {
                return new Threat($match);
            }
        }
    }
}
