<?php
/**
 * Decorate tweet text plugin.
 *
 * Copyright (c) 2015,2016 Satoshi Fukutomi <info@fuktommy.com>.
 */
function smarty_modifier_decorate_tweet_text($tweet)
{
    $text = $tweet['full_text'];

    $rules = [];
    foreach ($tweet['entities'] as $type => $rule) {
        foreach ($rule as $r) { 
            $r['_type'] = $type;
            $rules[] = $r;
        }
    }
    usort($rules, function ($a, $b) {
        return $b['indices'][0] - $a['indices'][0];
    });
    foreach ($rules as $rule) {
        if ($rule['_type'] === 'urls') {
            $anchor = '<a href="' . htmlspecialchars($rule['url'])
                    . '" title="' . htmlspecialchars($rule['expanded_url'])
                    . '">' . htmlspecialchars($rule['display_url']) . '</a>';
        } elseif ($rule['_type'] === 'hashtags') {
            $anchor = '<a href="https://twitter.com/hashtag/'
                    . rawurlencode($rule['text'])
                    . '">#' . htmlspecialchars($rule['text']) . '</a>';
        } elseif ($rule['_type'] === 'user_mentions') {
            $anchor = '<a href="https://twitter.com/'
                    . rawurlencode($rule['screen_name'])
                    . '" title="' . htmlspecialchars($rule['name'])
                    . '">@' . htmlspecialchars($rule['screen_name']) . '</a>';
        } elseif ($rule['_type'] === 'media') {
            $anchor = '<a href="' . htmlspecialchars($rule['url'])
                    . '" title="' . htmlspecialchars($rule['expanded_url'])
                    . '">' . htmlspecialchars($rule['display_url']) . '</a>';
        } else {
            continue;
        }
        $text = mb_substr($text, 0, $rule['indices'][0], 'utf8')
              . $anchor
              . mb_substr($text, $rule['indices'][1], null, 'utf8');
    }

    $text = nl2br($text);
    return $text;
}
