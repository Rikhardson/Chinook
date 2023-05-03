<?php

require_once "functions.php";

if (empty($_GET['artist_id'])) {
    http_response_code(400);
    echo "Error: artist_id parameter is missing.";
    exit();
  }

  $db = openDb();
  $artist_id = $_GET['artist_id'];
  $sql_artist = "SELECT Name FROM artists WHERE ArtistId = ?";
  $stmt_artist = $db->prepare($sql_artist);
  $stmt_artist->execute([$artist_id]);
  $artist_name = $stmt_artist->fetchColumn();

  $sql_albums = "
    SELECT a.AlbumId, a.Title, t.Name
    FROM albums a
    INNER JOIN tracks t ON t.AlbumId = a.AlbumId
    WHERE a.ArtistId = ?
    ORDER BY a.AlbumId, t.TrackId
  ";
  $stmt_albums = $db->prepare($sql_albums);
  $stmt_albums->execute([$artist_id]);

  $data = [];
  $current_album = null;
  while ($row = $stmt_albums->fetch(PDO::FETCH_ASSOC)) {

    if ($row['AlbumId'] !== $current_album) {
      $current_album = $row['AlbumId'];
      $data[] = [
        'album_title' => $row['Title'],
        'tracks' => []
      ];
    }

    $track = $row['Name'];
    $data[count($data) - 1]['tracks'][] = $track;
  }
  

  header('Content-Type: application/json');
  echo json_encode([
    'artist_name' => $artist_name,
    'albums' => $data
  ]);