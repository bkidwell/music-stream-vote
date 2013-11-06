<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class OptionDefs {
public static $option_defs = array(


'IRC' => array(

    'irc_nick' => array(
        't' => 'Nick',                 # display title
        'h' => 'Bot\'s name in chat.', # form hint
        'c' => '',                     # HTML input class
        'd' => 'votebot',
    ),

    'irc_nickserv_password' => array(
        't' => 'Password to authenticate with NickServ',
        'h' => '',
        'c' => '',
        'd' => '',
    ),

    'irc_realname' => array(
        't' => 'Real name',
        'h' => 'For whois queries. Maybe include URL to your radio station web site.',
        'c' => 'msv-input-wide',
        'd' => 'Music Stream Vote ( https://github.com/bkidwell/music-stream-vote )',
    ),

    'irc_server' => array(
        't' => 'IRC server hostname',
        'h' => '',
        'c' => '',
        'd' => '',
    ),

    'irc_port' => array(
        't' => 'IRC server port',
        'h' => '',
        'c' => '',
        'd' => '',
    ),

    'irc_channels' => array(
        't' => 'List of channels to join',
        'h' => 'Separated by spaces. Ex: "#music #chatter".',
        'c' => '',
        'd' => '',
    ),

    'irc_ident' => array(
        't' => 'Ident string',
        'h' => 'Appears in long IRC username string after nick.',
        'c' => '',
        'd' => 'musicstreamvote',
    ),

),
'WordPress Integration' => array(

    'web_service_url' => array(
        't' => 'URL of WordPress site',
        'h' => '',
        'c' => 'msv-input-wide',
        'd' => '',
    ),

    'web_service_password' => array(
        't' => 'WordPress vote service password',
        'h' => 'Used only by the IRC bot to talk to WordPress.',
        'c' => '',
        'd' => '',
    ),

),
'Stream Info' => array(

    'stream_status_url' => array(
        't' => 'URL of stream status file',
        'h' => 'An \'.xspf\' file',
        'c' => 'msv-input-wide',
        'd' => '',
    ),

    'stream_status_poll_interval_sec' => array(
        't' => 'Stream status polling interval (seconds)',
        'h' => '',
        'c' => '',
        'd' => '10',
    ),

),
'Command Names' => array(

    'cmd_help' => array(
        't' => 'Help command',
        'h' => 'Example: !help',
        'c' => '',
        'd' => '!help',
    ),

    'cmd_nowplaying' => array(
        't' => 'Now Playing command',
        'h' => 'Example: !np',
        'c' => '',
        'd' => '!np',
    ),

    'cmd_vote' => array(
        't' => 'Vote command',
        'h' => 'Example: !vote !v',
        'c' => '',
        'd' => '!vote !v',
    ),

    'cmd_like' => array(
        't' => 'Like command',
        'h' => 'Example: !l',
        'c' => '',
        'd' => '!l',
    ),

    'cmd_hate' => array(
        't' => 'Hate command',
        'h' => 'Example: !h',
        'c' => '',
        'd' => '!h',
    ),

    'cmd_stats' => array(
        't' => 'Stats command',
        'h' => 'Example: !stats',
        'c' => '',
        'd' => '!stats',
    ),

),
'Output Strings' => array(

    'txt_help' => array(
        't' => 'Help',
        'h' => '',
        'c' => 'msv-input-tall',
        'd' =>
"<b>!help</b>    Display this help.
<b>!np</b>    Repeat last \"Now playing\" announcement.
<b>!vote [integer]</b> (or <b>!v [integer]</b>)    Vote for currently playing song, where [integer] is from -5 (worst) to 5 (best).
<b>!l</b>    Vote current song with value +3.
<b>!h</b>    Vote current song with value -3.
<b>!stats</b>    Show some stats."
    ),

    'txt_now_playing' => array(
        't' => 'Now playing',
        'h' => '<b>Now playing:</b> ${stream_title}',
        'c' => 'msv-input-wide',
        'd' => '<b>Now playing:</b> ${stream_title}.',
    ),

    'txt_vote_response' => array(
        't' => 'Vote response',
        'h' => 'Example: ${nick} voted ${value} for ${stream_title}',
        'c' => 'msv-input-wide',
        'd' => '<b>${nick}</b> voted ${value} for ${stream_title}',
    ),

    'txt_revote_response' => array(
        't' => 'Re-Vote response',
        'h' => 'Example: ${nick} changed vote to ${value} for ${stream_title}',
        'c' => 'msv-input-wide',
        'd' => '<b>${nick}</b> changed vote to ${value} for ${stream_title}',
    ),

),
'Miscellaneous' => array(

    'restart_poll_interval_sec' => array(
        't' => 'Bot restart file polling interval',
        'h' => 'Only for when WordPress and the bot are on the same host.',
        'c' => '',
        'd' => '10',
    ),

),


);
}
