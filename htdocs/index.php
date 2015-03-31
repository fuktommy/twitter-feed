<?php
/* Twitter Feed.
 *
 * Copyright (c) 2011-2015 Satoshi Fukutomi <info@fuktommy.com>.
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
namespace Fuktommy\TwitterFeed;

require_once __DIR__ . '/../libs/bootstrap.php';
use Fuktommy\TwitterFeed\Bootstrap;
use Fuktommy\PubSubHubbub;
use Fuktommy\WebIo;


/**
 * Twitter Feed.
 */
class TwitterFeedAction implements WebIo\Action
{
    /**
     * Execute
     * @param Fuktommy\WebIo\Context $context
     */
    public function execute(WebIo\Context $context)
    {
        // For errors and debug output.
        $context->putHeader('Content-Type', 'text/plain; charset=utf-8');

        $allowedUserIds = $context->config['twitterfeed_userids'];
        $userId = $context->get('get', 'id', $context->config['twitterfeed_default_userid']);
        if ((! preg_match('/^[0-9A-Za-z_]+$/', $userId)) || (! in_array($userId, $allowedUserIds))) {
            $context->putHeader('HTTP/1.0 404 Not Found');
            $context->putHeader('Content-Type', 'text/html; charset=utf-8');
            $smarty = $context->getSmarty();
            $smarty->assign('config', $context->config);
            $smarty->display('notfound.tpl');
            return;
        }

        $feedFetcher = new Model\JsonFeedFetcher($context->getResource());
        $feed = $feedFetcher->fetchFeed($userId);
        if (empty($feed->content)) {
            $context->getLog('twitterfeed')->warning("Cannot parse json: {$userId}");
        }

        if ($context->get('get', 'debug')) {
            echo json_encode($feed->content, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), "\n";
            return;
        }

        if ($feed->updated) {
            $publisher = new PubSubHubbub\Publisher($context->getResource());
            $publisher->publish($userId);
        }

        $context->putHeader('Content-Type', 'text/xml; charset=utf-8');
        $smarty = $context->getSmarty();
        $smarty->assign('config', $context->config);
        $smarty->assign('feed', $feed->content);
        $smarty->assign('userId', $userId);
        $smarty->display('atom.tpl');
    }
}


Controller::factory()->run(new TwitterFeedAction(), Bootstrap::getContext());
