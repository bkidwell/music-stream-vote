<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * (Read-only) list of Option GROUPS -> NAMES -> {'T'itle, 'H'int, html 'C'lass, 'D'efault, 'R'equires restart}
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class OptionDefs {
/**
 * Class payload
 * @var array
 */
public static $option_defs = array(


'IRC' => array(

    'irc_nick' => array(
        't' => 'Nick',                 # display title
        'h' => 'Bot\'s name in chat.', # form hint
        'c' => '',                     # HTML input class
        'd' => 'votebot',              # default
        'r' => TRUE,                   # requires restart
    ),

    'irc_nickserv_password' => array(
        't' => 'Password to authenticate with NickServ',
        'h' => '',
        'c' => '',
        'd' => '',
        'r' => TRUE,
    ),

    'irc_realname' => array(
        't' => 'Real name',
        'h' => 'For whois queries. Maybe include URL to your radio station web site.',
        'c' => 'msv-input-wide',
        'd' => 'Music Stream Vote ( https://github.com/bkidwell/music-stream-vote )',
        'r' => TRUE,
    ),

    'irc_server' => array(
        't' => 'IRC server hostname',
        'h' => '',
        'c' => '',
        'd' => '',
        'r' => TRUE,
    ),

    'irc_port' => array(
        't' => 'IRC server port',
        'h' => '',
        'c' => '',
        'd' => '',
        'r' => TRUE,
    ),

    'irc_channels' => array(
        't' => 'List of channels to join',
        'h' => 'Separated by spaces. Ex: "#music #chatter".',
        'c' => '',
        'd' => '',
        'r' => TRUE,
    ),

    'irc_ident' => array(
        't' => 'Ident string',
        'h' => 'Appears in long IRC username string after nick.',
        'c' => '',
        'd' => 'musicstreamvote',
        'r' => TRUE,
    ),

),
'WordPress Integration' => array(

    'web_service_url' => array(
        't' => 'URL of WordPress site',
        'h' => '',
        'c' => 'msv-input-wide',
        'd' => '',
        'r' => TRUE,
    ),

    'web_service_password' => array(
        't' => 'WordPress vote service password',
        'h' => 'Used only by the IRC bot to talk to WordPress.',
        'c' => '',
        'd' => '',
        'r' => TRUE,
    ),

),
'Stream Info' => array(

    'stream_status_url' => array(
        't' => 'URL of stream status file',
        'h' => 'An \'.xspf\' file',
        'c' => 'msv-input-wide',
        'd' => '',
        'r' => TRUE,
    ),

    'stream_status_poll_interval_sec' => array(
        't' => 'Stream status polling interval (seconds)',
        'h' => '',
        'c' => '',
        'd' => '10',
        'r' => TRUE,
    ),

),
'Commands' => array(

    'cmd_help' => array(
        't' => 'Help command',
        'h' => 'Example: !help',
        'c' => '',
        'd' => '!help',
        'r' => TRUE,
    ),
    'cmd_help_switch' => array(
        't' => 'Help command enabled',
        'h' => '',
        'c' => '',
        'd' => '1',
        'r' => TRUE,
    ),

    'cmd_nowplaying' => array(
        't' => 'Now Playing command',
        'h' => 'Example: !np',
        'c' => '',
        'd' => '!np',
        'r' => TRUE,
    ),
    'cmd_nowplaying_switch' => array(
        't' => 'Now Playing command enabled',
        'h' => '',
        'c' => '',
        'd' => '1',
        'r' => TRUE,
    ),

    'cmd_vote' => array(
        't' => 'Vote command',
        'h' => 'Example: !vote !v',
        'c' => '',
        'd' => '!vote !v',
        'r' => TRUE,
    ),
    'cmd_vote_switch' => array(
        't' => 'Vote command enabled',
        'h' => '',
        'c' => '',
        'd' => '1',
        'r' => TRUE,
    ),

    'cmd_unvote' => array(
        't' => 'Unvote command',
        'h' => 'Example: !unvote',
        'c' => '',
        'd' => '!unvote',
        'r' => TRUE,
    ),
    'cmd_unvote_switch' => array(
        't' => 'Unvote command enabled',
        'h' => '',
        'c' => '',
        'd' => '1',
        'r' => TRUE,
    ),

    'cmd_like' => array(
        't' => 'Like command',
        'h' => 'Example: !l',
        'c' => '',
        'd' => '!l',
        'r' => TRUE,
    ),
    'cmd_like_switch' => array(
        't' => 'Like command enabled',
        'h' => '',
        'c' => '',
        'd' => '1',
        'r' => TRUE,
    ),

    'cmd_hate' => array(
        't' => 'Hate command',
        'h' => 'Example: !h',
        'c' => '',
        'd' => '!h',
        'r' => TRUE,
    ),
    'cmd_hate_switch' => array(
        't' => 'Hate command enabled',
        'h' => '',
        'c' => '',
        'd' => '1',
        'r' => TRUE,
    ),

    'cmd_stats' => array(
        't' => 'Stats command',
        'h' => 'Example: !stats',
        'c' => '',
        'd' => '!stats',
        'r' => TRUE,
    ),
    'cmd_stats_switch' => array(
        't' => 'Stats command enabled',
        'h' => '',
        'c' => '',
        'd' => '1',
        'r' => TRUE,
    ),

),
'Responses' => array(

    'txt_sayhi' => array(
        't' => 'Say hi',
        'h' => 'What to say when a user is trying to find the help command.',
        'c' => 'msv-input-wide',
        'd' => 'Hello ${nick}. I\'m a bot for voting on the music being played. Say "${cmd_help}" for help.',
        'r' => FALSE,
    ),
    'txt_sayhi_switch' => array(
        't' => 'Say hi private reply',
        'h' => '',
        'c' => '',
        'd' => '0',
        'r' => FALSE,
    ),

    'txt_help' => array(
        't' => 'Help',
        'h' => '',
        'c' => 'msv-input-tall',
        'd' =>
"<b>!help</b>    Display this help.
<b>!np</b>    Repeat last \"Now playing\" announcement.
<b>!vote [integer]</b> (or <b>!v [integer]</b>)    Vote for currently playing song, where [integer] is from -5 (worst) to 5 (best).
<b>!unvote</b>    Undo your last vote.
<b>!l</b>    Vote current song with value +3.
<b>!h</b>    Vote current song with value -3.
<b>!stats</b>    Show some stats.",
        'r' => FALSE,
    ),
    'txt_help_switch' => array(
        't' => 'Help private reply',
        'h' => '',
        'c' => '',
        'd' => '0',
        'r' => FALSE,
    ),

    'txt_now_playing' => array(
        't' => 'Now playing',
        'h' => '<b>Now playing:</b> ${stream_title}',
        'c' => 'msv-input-wide',
        'd' => '<b>Now playing:</b> ${stream_title}',
        'r' => FALSE,
    ),
    'txt_now_playing_switch' => array(
        't' => 'Now playing private reply',
        'h' => '',
        'c' => '',
        'd' => '0',
        'r' => TRUE,
    ),

    'txt_vote_response' => array(
        't' => 'Vote response',
        'h' => 'Example: <b>${nick}</b> voted ${value} for ${stream_title}.',
        'c' => 'msv-input-wide',
        'd' => '<b>${nick}</b> voted ${value} for ${stream_title}.',
        'r' => FALSE,
    ),
    'txt_vote_switch' => array(
        't' => 'Vote private reply',
        'h' => '',
        'c' => '',
        'd' => '0',
        'r' => FALSE,
    ),

    'txt_revote_response' => array(
        't' => 'Re-Vote response',
        'h' => 'Example: ${nick} changed vote to ${value} for ${stream_title}.',
        'c' => 'msv-input-wide',
        'd' => '<b>${nick}</b> changed vote to ${value} for ${stream_title}.',
        'r' => FALSE,
    ),
    'txt_revote_switch' => array(
        't' => 'Re-Vote private reply',
        'h' => '',
        'c' => '',
        'd' => '0',
        'r' => FALSE,
    ),

    'txt_unvote_response' => array(
        't' => 'Unvote response',
        'h' => 'Example: <b>${nick}</b> undid a vote for ${stream_title}.',
        'c' => 'msv-input-wide',
        'd' => '<b>${nick}</b> undid a vote for ${stream_title}.',
        'r' => FALSE,
    ),
    'txt_unvote_switch' => array(
        't' => 'Unvote private reply',
        'h' => '',
        'c' => '',
        'd' => '0',
        'r' => FALSE,
    ),

    'txt_stats' => array(
        't' => 'Stats',
        'h' => 'Example: ${begin_repeat,10}#${num} ${title}${end_repeat}. Other variables: ${artist}, ${stream_title}. <em>Note: the output will be limited to 255 characters</em>',
        'c' => 'msv-input-wide',
        'd' => '${begin_repeat,10}#${num} ${title}${end_repeat}',
        'r' => FALSE,
    ),
    'txt_stats_switch' => array(
        't' => 'Stats private reply',
        'h' => '',
        'c' => '',
        'd' => '0',
        'r' => FALSE,
    ),

),
'Miscellaneous' => array(

    'restart_poll_interval_sec' => array(
        't' => 'Bot restart file polling interval',
        'h' => 'Only for when WordPress and the bot are on the same host.',
        'c' => '',
        'd' => '10',
        'r' => TRUE,
    ),

),


);
}
