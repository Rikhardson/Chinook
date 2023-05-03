<?php

require_once "functions.php";

$artist_id = $_GET["ArtistId"];

$db = openDb();
$db->beginTransaction();

try {
    
  $db->exec("
  DELETE FROM invoice_items
  WHERE TrackId IN (
    SELECT TrackId
    FROM tracks
    WHERE AlbumId IN (
      SELECT AlbumId
      FROM albums
      WHERE ArtistId = $artist_id
    )
  )
");

  $db->exec("
    DELETE FROM playlist_track
    WHERE TrackId IN (
      SELECT TrackId
      FROM tracks
      WHERE AlbumId IN (
        SELECT AlbumId
        FROM albums
        WHERE ArtistId = $artist_id
      )
    )
  ");

  $db->exec("
    DELETE FROM tracks
    WHERE AlbumId IN (
      SELECT AlbumId
      FROM albums
      WHERE ArtistId = $artist_id
    )
  ");

  $db->exec("
    DELETE FROM albums
    WHERE ArtistId = $artist_id
  ");

  $db->exec("
    DELETE FROM artists
    WHERE ArtistId = $artist_id
  ");

  $db->commit();

  http_response_code(200);
  echo json_encode(array("message" => "Artist and related data removed successfully."));
} catch (Exception $e) {

  $db->rollBack();

  http_response_code(500);
  echo json_encode(array("message" => "Error removing artist: " . $e->getMessage()));
}