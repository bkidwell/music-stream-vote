# Installation #

You will install **Music Stream Vote** on one or two hosts. The WordPress backend of the application will be called ``BACKEND`` and the IRC bot will be called ``BOT``.

This howto assumes you already have an **IceCast** Internet music radio station running and an **IRC** chat room.

## IRC Nick for the Bot ##

1. Login to your chat server using your favorite IRC chat client, using the **nick** that will be the identity of your IRC bot.

2. ``/query NickServ`` and setup a password. In the query window say ``help`` if you need help.

3. Make a note of the **nick** and **password** and quit your IRC client.

## WordPress Backend ##

1. Install WordPress on ``BACKEND`` if you have not already.

2. At the command prompt, run ``php -i`` and make sure you have the PHP extension ``json`` installed. If not, use your system's normal procedure to find and install that extension. If you don't have a ``php`` command itself, you need to install the ``php-cli`` package first.

3. In WordPress, go **Dashboard** → **Plugins** → **Add New** → **Upload**.

4. In another window, go to [Music Stream Vote Releases on GitHub](https://github.com/bkidwell/music-stream-vote/releases) and copy the URL for the most recent Zip file to the clipboard.

5. Go back to WordPress and paste the URL into the **Install Plugins** screen. Install the plugin and then **Activate** it on the **Installed Plugins** screen.

6. Go to **Dashboard** → **Settings** → **Music Stream Vote** → **IRC**. Fill in:
   * **Nick:** IRC nick
   * **Password to authenticate with NickServ:** Password from above
   * **Real Name:** Something descriptive, including the WordPress site URL
   * **IRC Server hostname and port:** Your IRC server
   * **List of channels to join:** Your radio station's chat room, including the prefix "#"

7. Go to the **Stream Info** settings tab and fill in **URL of stream status file** with the URL of the IceCast status XSPF file.

8. **Save Changes** and now you're all set to startup the bot.

## IRC Bot ##

Where will the IRC bot live? If you are planning to run it on a different machine from the WordPress web site, you need to copy ``$WORDPRESS/wp-content/plugins/music-stream-vote/php-irc-2.2.1`` to a convenient place on the ``BOT`` host like the bot system account's ``~/Apps`` folder.

The bot will connect to WordPress using a URL and password that has already been stored in ``php-irc-2.2.1/modules/musicstreamvote/bootstrap.conf.php``.

1. With an SSH client, login to the ``BOT`` host as the user that the bot will run as.

2. (If ``BOT`` is different from ``BACKEND``) at the command prompt, run ``php -i`` and make sure you have the PHP extension ``json`` installed. If not, use your system's normal procedure to find and install that extension. If you don't have a ``php`` command itself, you need to install the ``php-cli`` package first.

3. CD to the folder where you installed the bot. (If it's the same host as WordPress, that would be the ``php-irc-2.2.1`` folder in the WordPress plugin mentioned in the first paragraph of this section.

4. Run ``./run-bot.sh``. It will connect to the IRC server, join the chat room, check in with WordPress, and begin polling the IceCast server for track title changes and listening in the chat room for votes.

To stop the bot, just type CTRL-C in the window where the ``run-bot.sh`` is running.

### Running the IRC Bot as a Service ###

It is possible to run ``run-bot.sh`` as a service in the background; consult your operating system's documentation for instructions on how to install an arbitrary task as a service. Make sure that service runs as the same user that owns all the files in ``php-irc-2.2.1``.

As a quick cheat, you can use the [Screen](http://en.wikipedia.org/wiki/GNU_Screen) command to start a virtual terminal and disconnect from it without destroying it.

1. ``screen`` -- Navigate through the intro screen if this is your first time using this command.

2. ``cd $PHP_IRC_DIRECTORY``.

3. ``./run-bot.sh``.

4. Type CTRL-A D, to disconnect from Screen.

It will run until the host shuts down. To reconnect to the Screen session just type ``screen`` again.

### Restarting the IRC Bot ###

Certain Settings changes in the WordPress plugin do not require a restart of the IRC bot:

* Responses:
    * Say hi
    * Help
    * *not* Now Playing (Now Playing requires a restart.)
    * Vote response
    * Re-vote response
    * Unvote response
    * Stats

If you change any other settings, the WordPress plugin will create an empty file ``$WORDPRESS/wp-content/plugins/music-stream-vote/php-irc-2.2.1/modules/musicstreamvote/restart`` which will be detected by the bot and trigger a restart **only if the bot is on the same host**.

If the bot is running on a different host, you must go to where ``run-bot.sh`` is running, terminate it with CTRL-C (or a service control command if you installed it as a service), and start it again manually.

## Creating WordPress Pages ##

Once you have the plugin and the IRC bot up and running, you should probably do something like the following:

1. Place the ``[recent_tracks]`` shortcode on your home page.
2. Create a "Last 24 Hours" page and place the ``[last_day]`` shortcode on it.
3. Create a "Top 100 By Vote" page and place the ``[top_hundred]`` shortcode on it.
4. Create a page with a title like "IRC Help" explaining how to get to the chat room and how to use the bot's commands there -- including the actual command names you assigned to all the commands (which are configurable on the Settings screen).
