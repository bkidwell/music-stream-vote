# Set Options from IRC Chat #

Once you get the IRC bot up and running, and connected to the IRC server, then you can change these options from within IRC. In a private message window to the bot, or in one of the channels the bot is in, say:

``!set key1 value1; key2 value2; ...``

After "!set", give a list of which values you want to set to which keys, separated by semicolons. (If you need to put a semicolon into a value, escape it with "\\"; escape a "\\" as "\\\\".)

For options that are can be true or false, specify them as "1" or "0".

*You must be authenticated with NickServ and be listed in the List of users who can give admin commands" option.*

Example: ``!set irc_channels #newchannel; irc_msg_type privmsg``

[All option names](?page=musicstreamvote&help=option_names)