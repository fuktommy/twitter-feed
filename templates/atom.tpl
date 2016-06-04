{* -*- coding: utf-8 -*- *}
{* Copyright (c) 2011-2016 Satoshi Fukutomi <info@fuktommy.com>. *}
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="/atomfeed.xsl" type="text/xsl"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title>{$feed[0].user.name|escape} - @{$feed[0].user.screen_name|escape} - twitter</title>
  <subtitle>{$feed[0].user.description|escape}</subtitle>
  {if $feed[0].user.screen_name === $config.twitterfeed_default_userid}
    <link rel="self" href="{$config.site_top}" />
  {else}
    <link rel="self" href="{$config.site_top}{$userId|escape}" />
  {/if}
  <link rel="alternate" href="https://twitter.com/{$userId|escape:"url"}" type="text/html"/>
  <updated>{$feed[0].created_at|atom_date|escape}</updated>
  <generator>https://github.com/fuktommy/twitter-feed</generator>
  <id>tag:fuktommy.com,2015:twitter/feed</id>
  <author><name>{$feed[0].user.name|escape}</name></author>
  <icon>{$config.site_top}favicon.ico</icon>
{foreach from=$feed item="entry"}
  {include assign="content" file="content.tpl" entry=$entry}
  <entry>
    <title>{$content|strip_tags|regex_replace:'/\s+/':' '|htmlspecialchars_decode:$smarty.const.ENT_QUOTES|regex_replace:'/\s+/':' '|trim|mbtruncate:60|escape|default:"untitled"}</title>
    <link rel="alternate" href="https://twitter.com/{$entry.user.screen_name|escape:"url"}/status/{$entry.id_str|escape:"url"}"/>
    <summary type="html">{$content|strip_tags|regex_replace:'/\s+/':' '|escape}</summary>
    <content type="html"><![CDATA[
        {$content|replace:"]]>":""}
    ]]></content>
    <published>{$entry.created_at|atom_date|escape}</published>
    <updated>{$entry.created_at|atom_date|escape}</updated>
    {strip}
    <author><name>
        {if $entry.retweeted_status}
            {$entry.retweeted_status.user.screen_name|escape} - {$entry.retweeted_status.user.name|escape}
        {else}
            {$entry.user.screen_name|escape} - {$entry.user.name|escape}
        {/if}
    </name></author>
    {/strip}
    <id>tag:fuktommy.com,2015:twitter/feed/{$entry.id_str|escape}</id>
    {if ! $entry.retweeted_status && $entry.user.screen_name === $config.twitterfeed_default_userid && empty($entry.retweeted_status)}
        <rights>{$config.rights|escape}</rights>
    {/if}
  </entry>
{/foreach}
</feed>
