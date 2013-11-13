# Viewing Historical Data #

As of version 1.1 of Music Stream Vote, there is limited ability built-in to query your historical vote data other than an overall "top 100" list. As a workaround, it is recommended that you use a tool such as [phpMyAdmin](http://www.phpmyadmin.net/home_page/index.php) to query your data directly in the database.

The following a couple of examples to get you going.

Show all votes ever, for a particular song:

    SELECT t.artist, t.title, v.*
    FROM wp_musvote_track t
    LEFT JOIN wp_musvote_vote v ON v.track_id=t.id
    WHERE t.stream_title='Paul and Storm - The Captain\'s Wife\'s Lament'
    ORDER BY v.time_utc ASC

All tracks receiving votes on a particular day, sorted by vote total:

    SELECT t.id, t.artist, t.title, SUM(v.value) vote_total
    FROM wp_musvote_track t
    LEFT JOIN wp_musvote_vote v on v.track_id=t.id
    WHERE v.time_utc >= '2013-11-08 05:00:00' AND v.time_utc < '2013-11-09 05:00:00' /* midnight to midnight in America/New_York */
    AND v.deleted = 0
    GROUP BY t.id
    ORDER BY vote_total DESC, t.artist, t.title
