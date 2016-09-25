<?php
/*
 * Twitter Json Feed.
 *
 * Copyright (c) 2011-2016 Satoshi Fukutomi <info@fuktommy.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHORS AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHORS OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */
namespace Fuktommy\TwitterFeed\Model;
use Abraham\TwitterOAuth\TwitterOAuth;
use Fuktommy\WebIo\Resource;

/**
 * Twitter Json Feed Fetcher.
 *
 * This class save date to cache, and so on.
 */
class JsonFeedFetcher
{
    /**
     * @var string
     */
    private $_cacheDir;

    /**
     * @var int
     */
    private $_cacheTime = 600;

    /**
     * @var Fuktommy\WebIo\Resource
     */
    private $_resource;

    /**
     * Constructor
     * @param Fuktommy\WebIo\Resource
     */
    public function __construct(Resource $resource)
    {
        $this->_resource = $resource;
        $this->_cacheDir = $resource->config['twitter_cache_dir'];
    }

    /**
     * Fetch feed.
     * @param string $userId
     * @return Fuktommy\TwitterFeed\Model\JsonFeed
     * @throws InvalidArgumentException
     */
    public function fetchFeed($userId)
    {
        if (! preg_match('/^[0-9A-Za-z_]+$/', $userId)) {
            throw new \InvalidArgumentException("{$userId} is invalid user id");
        }
        $oldJson = $this->_readCache($userId);
        $json = $this->_getJsonUsingCache($userId);
        $feed = json_decode($json, true);
        if (empty($feed)) {
            $this->_resource->getLog('twitterfeed')
                 ->warning("empty json for {$userId}");
            return new JsonFeed(array(), false);
        }
        return new JsonFeed($feed, $oldJson !== $json);
    }

    private function _cacheFileOf($userId)
    {
        return "{$this->_cacheDir}/{$userId}.txt";
    }

    private function _readCache($userId)
    {
        $cacheFile = $this->_cacheFileOf($userId);
        if (is_file($cacheFile)) {
            return file_get_contents($cacheFile);
        } else {
            return '';
        }
    }

    private function _getJsonUsingCache($userId)
    {
        $cacheFile = $this->_cacheFileOf($userId);
        $readFromCache = is_file($cacheFile)
                      && (time() < filemtime($cacheFile) + $this->_cacheTime);
        if ($readFromCache) {
            return file_get_contents($cacheFile);
        }

        $log = $this->_resource->getLog('twitterfeed');
        $lock = fopen("{$this->_cacheDir}/lock", 'w');
        $lockSuccess = flock($lock, LOCK_EX|LOCK_NB);
        if (! $lockSuccess) {
            fclose($lock);
            $log->info("lock failed for {$userId}");
            return $this->_readCache($userId);
        }

        touch($cacheFile);
        $consumerKey = $this->_resource->config['twitter_consumer_key'];
        $consumerSecret = $this->_resource->config['twitter_consumer_secret'];
        $accessToken = $this->_resource->config['twitter_access_token'];
        $accessTokenSecret = $this->_resource->config['twitter_access_token_secret'];

        $log->info("accessing json for {$userId}");
        try {
            $connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
            $obj = $connection->get('statuses/user_timeline', [
                'screen_name' => $userId,
                'count' => '50',
                'tweet_mode' => 'extended',
            ]);
            $json = json_encode($obj);
        } catch (\ErrorException $e) {
            $headerStr = implode(', ', $http_response_header);
            $log->warning("{$e->getMessage()} for {$userId}, {$headerStr}");
            return $this->_readCache($userId);
        }
        if (empty($json)) {
            $headerStr = implode(', ', $http_response_header);
            $log->warning("empty json from api for {$userId}, {$headerStr}");
            return $this->_readCache($userId);
        }
        file_put_contents($cacheFile, $json);
        flock($lock, LOCK_UN);
        fclose($lock);
        return $json;
    }
}
