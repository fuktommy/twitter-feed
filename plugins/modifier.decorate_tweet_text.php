<?php
/**
 * Decorate tweet text plugin.
 */
function smarty_modifier_decorate_tweet_text($entry)
{
    if (empty($entry['retweeted_status'])) {
        $tweet = $entry;
    } else {
        $tweet = $entry['retweeted_status'];
    }
    $text = $tweet['text'];

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

    if (! empty($entry['retweeted_status'])) {
        $origUrl = 'https://twitter.com/'
                 . rawurlencode($entry['retweeted_status']['user']['screen_name'])
                 . '/status/' . rawurlencode($entry['retweeted_status']['id_str']);
        $text = 'RT <cite><a href="'
              . htmlspecialchars($origUrl)
              . '" title="' . htmlspecialchars($entry['retweeted_status']['user']['name'])
              . '">@' . htmlspecialchars($entry['retweeted_status']['user']['screen_name'])
              . '</a></cite>: <blockquote cite="' . htmlspecialchars($origUrl)
              . '"><div>' . $text . '</div></blockquote>';
    }

    $text = nl2br($text);
    return $text;
}
