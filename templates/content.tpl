{* -*- coding: utf-8 -*- *}
{* Copyright (c) 2011-2016 Satoshi Fukutomi <info@fuktommy.com>. *}
{strip}
{if ! empty($entry.user.protected)}
    PROTECTED
{elseif ! empty($entry.retweeted_status)}
    <div>RT <cite><a href="{$entry.retweeted_status|@tweet_url|escape}" title="{$entry.retweeted_status.user.name|escape}">@{$entry.retweeted_status.user.screen_name|escape}</a></cite>:
    <blockquote cite="{$entry.retweeted_status|@tweet_url|escape}"><div>
        ...
        {*
        {include file="content.tpl" entry=$entry.retweeted_status}
        *}
    </div></blockquote>
    </div>
{else}
    {$entry|@decorate_tweet_text}

    {if $entry.in_reply_to_status_id_str}
        <div>
            - Reply to <a href="https://twitter.com/{$entry.in_reply_to_screen_name|escape:"url"}/status/{$entry.in_reply_to_status_id_str|escape:"url"}">@{$entry.in_reply_to_screen_name|escape}</a>
        </div>
    {/if}

    {if ! empty($entry.extended_entities.media)}
        <ul>
        {foreach from=$entry.extended_entities.media item="media"}
            {if $media.type === "photo"}
                <li><a href="{$media.expanded_url|escape}"><img src="{$media.media_url_https|escape}" width="{$media.sizes.medium.w|escape}" height="{$media.sizes.medium.h|escape}" alt=""></a></li>
            {elseif $media.type === "animated_gif"}
                <li>gif:<br><a href="{$media.expanded_url|escape}"><img src="{$media.media_url_https|escape}" width="{$media.sizes.medium.w|escape}" height="{$media.sizes.medium.h|escape}" alt=""></a></li>
            {elseif $media.type === "video"}
                <li>video:<br><a href="{$media.expanded_url|escape}"><img src="{$media.media_url_https|escape}" width="{$media.sizes.medium.w|escape}" height="{$media.sizes.medium.h|escape}" alt=""></a></li>
            {/if}
        {/foreach}
        </ul>
    {/if}

    {if ! empty($entry.quoted_status)}
        <div>
        - Quoted from <cite><a href="{$entry.quoted_status|@tweet_url|escape}" title="{$entry.quoted_status.user.name|escape}">@{$entry.quoted_status.user.screen_name|escape}</a></cite>:
        <blockquote cite="{$entry.quoted_status|@tweet_url|escape}"><div>
            ...
            {*
            {include file="content.tpl" entry=$entry.quoted_status}
            *}
        </div></blockquote>
        </div>
    {/if}
{/if}
{/strip}
