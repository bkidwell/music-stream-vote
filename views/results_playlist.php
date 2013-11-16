<?php

/**
 * Display results for an artist, title search
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */

namespace GlumpNet\WordPress\MusicStreamVote;

?>

<h3>Votes for <?php echo esc_html( $title ); ?></h3>

<table class="music-results tablesorter"><thead><tr>
<?php foreach ( $cols as $col => $title  ) : ?>
    <th><?php echo esc_html( $title ); ?></th>
<?php endforeach; ?>
</tr></thead><tbody>

<?php foreach ( $plays as $play ) : ?>
    <tr>
    <?php foreach ( $cols as $col => $title  ) : ?>
        <td><?php echo $play[$col]; ?></td>
    <?php endforeach; ?>
    </tr>
<?php endforeach; ?>
</tbody></table>
