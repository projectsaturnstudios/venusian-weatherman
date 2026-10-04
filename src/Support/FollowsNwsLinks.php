<?php

namespace ProjectSaturnStudios\Weatherman\Support;

use ProjectSaturnStudios\Weatherman\Exceptions\WeathermanException;
use ProjectSaturnStudios\Weatherman\NwsClient;

trait FollowsNwsLinks
{
    protected function nws(): NwsClient
    {
        if (is_null($this->client)) {
            throw WeathermanException::followUnavailable();
        }

        return $this->client;
    }

    protected function link(string $name, ?string $url): string
    {
        if (is_null($url) || $url === '') {
            throw WeathermanException::missingLink($name);
        }

        return $url;
    }
}
