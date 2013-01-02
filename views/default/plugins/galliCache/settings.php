<?php
/**
 *	galliCache
 *	Author : Mahin Akbar | Team Webgalli
 *	Team Webgalli | Elgg developers and consultants
 *	Mail : info@webgalli.com
 *	Web	: http://webgalli.com
 *	Skype : 'team.webgalli'
 *	@package galliCache plugin
 *	Licence : GPLV2
 *	Copyright : Team Webgalli 2011-2015
 */
 
// set default values
if (!isset($vars['entity']->validity)) {
	$vars['entity']->validity = 86400;
}

$content = "<div class='elgg-message'>Need help in configuring the plugin? See <a href='http://www.webgalli.com/blog/elgg-gallicache-plugin-a-performance-booster-for-your-elgg-websites/'>galliCache Tutorials</a>.</div>";

$content .= '<div>';
$content .= elgg_echo('galliCache:validity');
$content .= elgg_view('input/text', array( 'name' => 'params[validity]', 'value' => $vars['entity']->validity));
$content .= '</div>';

$content .= '<div>';
$content .= elgg_echo('galliCache:skipcontexts');
$content .= elgg_view('input/text', array( 'name' => 'params[skipcontexts]', 'value' => $vars['entity']->skipcontexts));
$content .= '</div>';

echo elgg_view_module('inline',elgg_echo('galliCache:header'),$content);