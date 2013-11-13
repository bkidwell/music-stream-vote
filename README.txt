=== Music Stream Vote ===
Contributors: bkidwell
Donate link: http://www.glump.net
Tags: IceCast, music, radio, IRC, bot, vote, top-ten
Requires at least: 3.6.0
Tested up to: 3.7.1
Stable tag: master
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

*Music Stream Vote* is an application for collecting votes on the currently playing track on any Internet radio station running Icecast that has an IRC chat room and a WordPress web site.

== Description ==

Features:

* Poll IceCast ``.xspf`` URL for name of currently playing track every *n* seconds.
* Announce track changes in an IRC chat room and post them to WordPress.
* Allow chat room users to post votes from -5 to +5 on each played track. (One vote per person per play, undoable and changeable.)
* Configurable IRC commands and responses (enable, disable, change response text) for "Say hi", "Help", "Vote", "Unvote", "Now Playing", and "Stats".
* Report Top 10 tracks in chat room.
* Report Top 100 tracks and last 24 hours of play on web site.

See [full documentation](https://github.com/bkidwell/music-stream-vote/tree/master/docs) on the [GitHub project page](https://github.com/bkidwell/music-stream-vote) .

== Requirements ==

* An Internet radio station running software like IceCast that provides a "Now Playing" [XSPF](https://en.wikipedia.org/wiki/XML_Shareable_Playlist_Format) file at a static URL.
* An IRC chat room where you have *permission* to run a bot.
* A WordPress web site.
    * PHP 5.4+ (WordPress requires 5.2.4 but Music Stream Vote requires 5.4).
    * MySQL 5.0+.
* A host to run the IRC bot -- preferably the same as the WordPress site, but not necessarily.
    * PHP 5.4+
    * Linux or a Unix OS on the bot host. Not a strict requirement, but at the moment the control script and instructions will require some expertise to adapt it to Windows.

== Changelog ==

= 1.1 =
* In progress...

= 1.0 =
* Initial release.
