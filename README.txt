=== Music Stream Vote ===
Contributors: bkidwell@github
Donate link: http://rynothebearded.com/
Tags: IceCast, music, radio, IRC, bot, vote, top-ten
Requires at least: 3.6.0
Tested up to: 3.6.1
Stable tag: master
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

UNFINISHED! Collects and displays votes for the track currently playing on your IceCast music radio station. Votes are collected via a bot in your station's IRC channel. Current stats can be shown in WordPress or in IRC.

== Description ==

When complete, this plugin will host a voting backend to collect votes for what is now playing on an IceCast Internet radio station. Votes will be collected via an embedded IRC bot in the ./bot folder of the plugin (not implemented yet) and stored in custom MySQL tables in the WordPress installation's MySQL database.

It is assumed that most interaction between the radio station and the users happens in an IRC channel, so this is where voting will happen. (We may add the ability to vote via the WordPress site as well, but for now we are focusing on IRC's authentication and abuse-prevention mechanisms to keep it simple.) The IRC bot will be fairly dumb, only relaying vote request and responses to the WordPress backend and allowing "current top ten" queries. The IRC bot will also take the task of polling the IceCast stream's XML status file to create "new track start" events.

WordPress widgets and shortcodes will be provided to display current top n lists like "most played", "highest voted", etc.

== Requirements ==

Requires PHP 5.4.

The package 'php5-json' is not installed by default in Ubuntu. Make sure PHP's json library is installed in your OS.

== Implemented so far ==

* Most of IRC bot: help, vote, stats
* Options screen
* Polling stream_title from IceCast status URL
* WordPress writes URL and password to bootstrap file for bot
* Bot's bootstrap script loads all options from WordPress into appropriate places in bot

== To do ==

* Create WordPress display code for widgets or shortcodes to display vote/play tallies.
* Documentation: requirements, install, screenshots
* Implement new voting era time: stats are counted only for events after a chosen time
* Implement historical stats query
* Implement cleanup when plugin is uninstalled http://codex.wordpress.org/Function_Reference/register_uninstall_hook
* Implement options export / import
* Implement history export / import
