# Hacking #

## Database Schema ##

See [./classes/Db.php](https://github.com/bkidwell/music-stream-vote/blob/master/classes/Db.php) .

## WordPress Plugin ##

The startup file for the plugin is [./music-stream-vote.php](https://github.com/bkidwell/music-stream-vote/blob/master/music-stream-vote.php) . After defining a bunch of constants and a class loader function for the ``GlumpNet\WordPress`` namespace, it instantiates these main classes:

### Classes ###

* [Db](https://github.com/bkidwell/music-stream-vote/blob/master/classes/Db.php) -- Defines schema for the plugin's three MySQL tables as SQL code, and uses WordPress' [dbDelta](http://codex.wordpress.org/Creating_Tables_with_Plugins) method to ensure that the table definitions are up to date after any code changes.
* [Settings](https://github.com/bkidwell/music-stream-vote/blob/master/classes/Settings.php) -- Installs Music Stream Vote's settings page in the WordPress admin screens.
* [BotService](https://github.com/bkidwell/music-stream-vote/blob/master/classes/BotService.php) -- Installs a web service into the WordPress general request handler to handle API calls from the IRC bot.
* [ShortCodes](https://github.com/bkidwell/music-stream-vote/blob/master/classes/ShortCodes.php) -- Installs a handful of WordPress [shortcodes to query stored data](shortcodes.md).

Additional support classes are instantiated at the points in the code where they're needed.

* [Help](https://github.com/bkidwell/music-stream-vote/blob/master/classes/Help.php) -- [Singleton](http://en.wikipedia.org/wiki/Singleton_pattern) that helps load the Help pages into the Settings screen.
* [OptionDefs](https://github.com/bkidwell/music-stream-vote/blob/master/classes/OptionDefs.php) -- List of all the Options on the Settings screen, including title, code name, hint, default value, and whether a restart is needed.
* [Options](https://github.com/bkidwell/music-stream-vote/blob/master/classes/Options.php) -- Singleton to abstract the plugin's configuration options as a single WordPress Plugion Option in the ``wp_options`` table.
* [Play](https://github.com/bkidwell/music-stream-vote/blob/master/classes/Play.php), [Track](https://github.com/bkidwell/music-stream-vote/blob/master/classes/Track.php), [Vote](https://github.com/bkidwell/music-stream-vote/blob/master/classes/Vote.php) -- Collections of static methods to access the Play, Track, and Vote tables in the database.
* [State](https://github.com/bkidwell/music-stream-vote/blob/master/classes/State.php) -- A singleton like ``Options`` but for runtime state instead of configuration.
* [Util](https://github.com/bkidwell/music-stream-vote/blob/master/classes/Util.php) -- Some **static** utility methods.

### Views ###

* [help.php](https://github.com/bkidwell/music-stream-vote/blob/master/views/help.php) formats the pages in the "Full Documentation" link from the Settings screen.
* [settings.php](https://github.com/bkidwell/music-stream-vote/blob/master/views/settings.php) formats the Settings screen.

### Web Service API for the IRC Bot ###

As mentioned above, [./classes/BotService.php](https://github.com/bkidwell/music-stream-vote/blob/master/classes/BotService.php) provides a web service API that the IRC bot interacts with.

``BotService``'s constructor hooks into WordPress' ``parse_request`` event. When there is a ``POST`` argument in the HTTP request called ``musicstreamvote_botcall`` set to ``1``, then the normal WordPress request handler is abandoned, and the web service call handler in this class is executed instead.

The web service call handler in the constructor takes a [JSON](http://en.wikipedia.org/wiki/Json) string in the ``POST`` argument ``args`` as a set of arguments and passes them to the function in ``BotService`` matchine the ``POST`` argument ``method``, prefixed with "web_". So if ``method`` is "checkin", then ``BotService->web_checkin()`` is called.

Each of the "web_..." methods returns a PHP Key-Value array, which is returned to in the HTTP response as a JSON string.

## IRC Bot ##

The IRC bot is built as a module for the [PHP-IRC](http://www.phpbots.org/) bot framework. The entire framework is included in [./php-irc-2.2.1](https://github.com/bkidwell/music-stream-vote/tree/master/php-irc-2.2.1) .

[./php-irc-2.2.1/run-bot.sh](https://github.com/bkidwell/music-stream-vote/tree/master/php-irc-2.2.1/run-bot.sh) does the following:

1. Delete ``modules/musicstreamvote/restart`` if it exists.

2. Run [modules/musicstreamvote/bootstrap.php](https://github.com/bkidwell/music-stream-vote/blob/master/php-irc-2.2.1/modules/musicstreamvote/bootstrap.php) to fetch configuration options from WordPress.

3. Run [modules/bot.php](https://github.com/bkidwell/music-stream-vote/blob/master/php-irc-2.2.1/bot.php) (PHP-IRC's main executable) until the bot terminates.

4. Go back to step 1 if ``restart`` exists. (It was put there by WordPress to signal a reboot was needed; the bot saw it and self-terminated.)

All of the interesting code in the Music Stream Vote IRC bot is contained in [modules/musicstreamvote/musicstreamvote.php](https://github.com/bkidwell/music-stream-vote/blob/master/php-irc-2.2.1/modules/musicstreamvote/musicstreamvote.php).

A config file ``musicstreamvote.conf`` placed in ``modules/musicstreamvote`` by [bootstrap.php](https://github.com/bkidwell/music-stream-vote/blob/master/php-irc-2.2.1/modules/musicstreamvote/bootstrap.php) routes all the channel/private message commands, and other IRC events supported by the bot ("!help", "!vote", etc.) to methods contained in [musicstreamvote.php](https://github.com/bkidwell/music-stream-vote/blob/master/php-irc-2.2.1/modules/musicstreamvote/musicstreamvote.php).

The module's ``init()`` method loads the WordPress plugins' options from ``options.json.php`` which was written by ``bootstrap.php``.

The framework takes care of logging into the IRC server and joining the specified channels. Furthermore, it periodically checks to make sure it hasn't been kicked or disconnected, and tries to rejoin channels or reconnect if necessary.

The module doesn't do any real work until an IRC channel join event triggers the ``evt_logged_in()`` method. Then the module starts keeping track of all the channels it's actually logged into and begins polling the IceCast stream status URL for ``stream_title`` changes. It also begins polling the local ``restart`` file which may be placed in its module folder when WordPress wants the bot to restart.

When ``stream_title`` *does* change, the event is passed to WordPress for processing, and a "Now Playing..." announcement is made.

Commands trigger methods such as ``cmd_help`` and ``cmd_vote``, which mostly just route the command to WordPress. WordPress' response indicates whether the reply should be sent to the same context (a channel or a private message) as the request came from, or always in a private message to the user to prevent the bot from being too chatty in the main chat channel. (This is configurable in the WordPress plugin's Settings screen.)

``cmd_vote`` is a special case because in order to make a full request to WordPress, the IRC bot needs to know the full IRC "identity" of the user that made the request (in order to be able to audit later for votes made by bad users who were not registered with NickServ or log in from known bad networks). ``cmd_vote`` stores the pending vote in memory, and kicks off a ``WHOIS`` command. ``evt_raw()`` receives all raw messages from the IRC server and routes ``WHOIS`` responses to ``evt_whois``, which matches up the response with the pending vote and passes all teh data to ``cmd_vote_finish()``, which calls WordPress.
