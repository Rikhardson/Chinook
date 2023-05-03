<?php

require_once "functions.php";

if (isset($_GET['PlaylistId'])) {
    $db = openDb();
    $playlist_id = $_GET['PlaylistId'];

    $sql = "SELECT tracks.Name, tracks.Composer
    FROM playlist_track
    JOIN tracks ON playlist_track.TrackId = tracks.TrackId
    WHERE playlist_track.PlaylistId = :PlaylistId";

    $stmt = $db->prepare($sql);
    $stmt->execute(['PlaylistId' => $playlist_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        echo "<h1>Playlist Tracks</h1>";
        foreach ($result as $row) {
            echo "<p><b>{$row['Name']}</b><br> ({$row['Composer']})</p>";
        }
    } else {
        echo "<p>No tracks found for playlist ID {$playlist_id}</p>";
    }
} else {
    echo "<p>Playlist ID not provided</p>";
}