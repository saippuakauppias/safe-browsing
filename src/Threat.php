<?php

namespace Saippuakauppias\SafeBrowsing;

class Threat
{
    /**
     * @var
     */
    private $match;

    public function __construct($match)
    {
        $this->match = $match;
    }

    /**
     * @return string
     */
    public function getThreatType()
    {
        return $this->match['threatType'];
    }

    /**
     * @return string
     */
    public function getPlatformType()
    {
        return $this->match['platformType'];
    }

    /**
     * @return string
     */
    public function getThreatEntryType()
    {
        return $this->match['threatEntryType'];
    }
}