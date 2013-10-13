<?php
namespace GlumpNet\WordPress\MusicStreamVote;

class Util {
	public static function json_last_error_msg() {
        static $errors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
	}

    public function fix_wp_slashes() {
        function stripslashes_array(&$arr) {
            foreach ($arr as $k => &$v) {
                $nk = stripslashes($k);
                if ($nk != $k) {
                    $arr[$nk] = &$v;
                    unset($arr[$k]);
                }
                if (is_array($v)) {
                    stripslashes_array($v);
                } else {
                    $arr[$nk] = stripslashes($v);
                }
            }
        }

        stripslashes_array($_POST);
        stripslashes_array($_GET);
        stripslashes_array($_REQUEST);
        stripslashes_array($_COOKIE);
    }
}
