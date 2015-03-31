{* -*- coding: utf-8 -*- *}
{* Copyright (c) 2011-2015 Satoshi Fukutomi <info@fuktommy.com>. *}
{strip}
    {$entry.text}

    {if $entry.entities.media}
        <ul>
        {foreach from=$entry.entities.media item="media"}
            <li><a href="{$media.expanded_url|escape}"><img src="{$media.media_url_https|escape}" alt=""></a></li>
        {/foreach}
        </ul>
    {/if}
{/strip}
