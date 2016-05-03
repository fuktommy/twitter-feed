<?php
function smarty_modifier_tweet_url($tweet)
{
    return 'https://twitter.com/'
          . rawurlencode($tweet['user']['screen_name'])
          . '/status/' . rawurlencode($tweet['id_str']);
}
