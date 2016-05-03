{* -*- coding: utf-8 -*- *}
{* Copyright (c) 2011-2016 Satoshi Fukutomi <info@fuktommy.com>. *}
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

    {if $entry.extended_entities.media}
        {if $entry.retweeted_status}
            <blockquote cite="https://twitter.com/{$entry.retweeted_status.user.screen_name|escape:"url"}/status/{$entry.retweeted_status.id_str|escape:"url"}">
        {/if}
        <ul>
        {foreach from=$entry.extended_entities.media item="media"}
            {if $media.type === "photo"}
                <li><a href="{$media.expanded_url|escape}"><img src="{$media.media_url_https|escape}" width="{$media.sizes.medium.w|escape}" height="{$media.sizes.medium.h|escape}" alt=""></a></li>
            {elseif $media.type === "animated_gif"}
                <li>gif:<br><a href="{$media.expanded_url|escape}"><img src="{$media.media_url_https|escape}" width="{$media.sizes.medium.w|escape}" height="{$media.sizes.medium.h|escape}" alt=""></a></li>
            {elseif $media.type === "video"}
                <li>video: <a href="{$media.expanded_url|escape}">{$media.display_url|escape}</a></li>
            {/if}
        {/foreach}
        </ul>
        {if $entry.retweeted_status}
            </blockquote>
        {/if}
    {/if}
{/strip}
