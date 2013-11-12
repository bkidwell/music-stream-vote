<?php
namespace GlumpNet\WordPress\MusicStreamVote;

/**
 * Help pages
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */
class Help {

    /**
     * Filesystem folder where docs are found
     * @var string
     */
    private $docs_dir = '';

    /**
     * List of pages found in docs folder (each page is a 2-item list with base filename, title)
     * @var array
     */
    private $pages = array();

    /**
     * Constructor
     */
    public function __construct() {
        $this->docs_dir = PLUGIN_DIR . 'docs/';

        $this->pages[] = array( 'README', 'Readme' );
        $d = dir( $this->docs_dir );
        while ( ($entry = $d->read()) !== FALSE ) {
            if ( substr( $entry, -3 ) != '.md' ) { continue; }
            if ( $entry == 'README.md' ) { continue; }
            $filename = substr( $entry, 0, strlen( $entry ) - 3 );
            $f = fopen( $this->docs_dir . $entry, "r");
            $title = trim( str_replace( '#', '', fgets( $f, 4096 ) ) );
            if ( $title ) {
                $this->pages[] = array( $filename, $title );
            }
            fclose( $f );
        }
        $d->close();
    }

    /**
     * Render a help page. Called by Settings->display_settings() .
     * @return [type] [description]
     */
    public function render() {
        require_once ( PLUGIN_DIR . 'lib/Michelf/Markdown.php' );
        require_once ( PLUGIN_DIR . 'lib/Michelf/MarkdownExtra.php' );
        require_once ( PLUGIN_DIR . 'lib/Michelf/smartypants.php' );

        $page_name = $_GET['help'];
        $markdown = file_get_contents( "$this->docs_dir$page_name.md" );
        $html = \Michelf\Markdown::defaultTransform( $markdown );
        $html = \SmartyPants( $html );
        $pages = $this->pages;

        // -- Quick and dirty transformations:

        // Get rid of H1
        $html = preg_replace( '|<h1>.*?</h1>|', '', $html );
        // Demote H2 .. H5 to H3 .. H6 (because WordPress settings pages start with H2)
        $html = preg_replace_callback( '|<h(\d+)>(.*)</h\d+>|', function ( $matches ) {
            $level = $matches[1] + 1;
            return "<h$level>$matches[2]</h$level>";
        }, $html );
        // Rewrite href in hyperlinks for pages in same folder
        $html = preg_replace_callback( '|<a(.+?)href="([^/\.]*.md)"(.*?)>(.*?)</a>|', function ( $matches ) {
            $href = $_SERVER['SCRIPT_NAME'] . '?page=' . PLUGIN_SLUG . '&help=' .
                substr( $matches[2], 0, strlen( $matches[2] ) - 3 );
            return "<a$matches[1]href=\"$href\"$matches[3]>$matches[4]</a>";
        }, $html );
        // Rewrite src in images in same folder
        $html = preg_replace_callback( '|<img(.+?)src="([^/\.]*.png)"(.*?)/>|', function ( $matches ) {
            $src = PLUGIN_URL . 'docs/' . $matches[2];
            return "<img$matches[1]src=\"$src\"$matches[3]/>";
        }, $html );

        $page_title = '';
        foreach ( $this->pages as $page ) {
            if ( $page[0] == $page_name ) {
                $page_title = \SmartyPants( esc_html( $page[1] ) );
            }
        }

        include( PLUGIN_DIR . 'views/help.php' );
    }
}
