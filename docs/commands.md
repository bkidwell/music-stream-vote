# Commands #

The following is a list of commands that users in IRC can say to the IRC bot. The **names** of these commands are configurable in the Settings screen on the Dashboard; the default names are used here.

## Admin Commands ##

In order to use these admin commands, you must:

* Be authenticated with NickServ
* Be listed in "IRC &rarr; List of users who can give admin commands" in the Settings screen

``!set key1 value1; key2 value2; ...``

:   Set options. After "!set", give a list of which values you want to set to which [keys](?page=musicstreamvote&help=option_names), separated by semicolons. (If you need to put a semicolon into a value, escape it with "\\"; escape a "\\" as "\\\\".)

    For options that are can be true or false, specify them as "1" or "0".

    Example: ``!set irc_channels #newchannel; irc_msg_type privmsg``

``!say #CHANNEL text``

:   Say text in #CHANNEL.

``!do #CHANNEL text``

:   Do ``/me text`` in #CHANNEL.

## User Commands ##

These can be used by anyone in the IRC chat room. You can configure the response for each command in the "Responses" of the "Settings" Dashboard page.

``(say hi)``

:   Special command that is triggered by starting an IRC message with the bot's nick.

``!help``

:   Display the bot's quick help text.

``!np``

:   Repeat last track change announcement.

``!vote value comment``

:   Cast vote of *value* for current track, with *comment*. *value* can be from -5 to +5. (The '+' is optional.)

``!unvote``

:   Undo last vote if it's not too old.

``!l``

:   Case +3 vote (like).

``!h``

:   Case -3 vote (hate).

``!stats``

:   Display quick top 10 list.
