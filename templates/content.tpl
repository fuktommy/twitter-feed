{* -*- coding: utf-8 -*- *}
{* Copyright (c) 2011-2015 Satoshi Fukutomi <info@fuktommy.com>. *}
{strip}
    {$entry|@decorate_tweet_text}

    {if $entry.in_reply_to_status_id_str}
        <div>
            - Reply to <a href="https://twitter.com/{$entry.in_reply_to_screen_name|escape:"url"}/status/{$entry.in_reply_to_status_id_str|escape:"url"}">@{$entry.in_reply_to_screen_name|escape}</a>
        </div>
    {/if}
    {if $entry.retweeted_status.in_reply_to_status_id_str}
        <div>
            - Reply to <a href="https://twitter.com/{$entry.retweeted_status.in_reply_to_screen_name|escape:"url"}/status/{$entry.retweeted_status.in_reply_to_status_id_str|escape:"url"}">@{$entry.retweeted_status.in_reply_to_screen_name|escape}</a>
        </div>
    {/if}

    {if $entry.entities.media && ! $entry.retweeted_status}
        <ul>
        {foreach from=$entry.entities.media item="media"}
            <li><a href="{$media.expanded_url|escape}"><img src="{$media.media_url_https|escape}" alt=""></a></li>
        {/foreach}
        </ul>
    {/if}
{/strip}
