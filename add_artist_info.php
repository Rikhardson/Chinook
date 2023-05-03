<?php

require_once "functions.php";

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$artist_name = $data["artist_name"];
$album_title = $data["album_title"];
$tracks = $data["tracks"];

$db = openDb();
$db->beginTransaction();

try {

  $stmt = $db->prepare("INSERT INTO artists (Name) VALUES (:name)");
  $stmt->bindValue(':name', $artist_name);
  $stmt->execute();

  $artist_id = $db->lastInsertID();

  $stmt = $db->prepare("INSERT INTO albums (Title, ArtistId) VALUES (:title, :artist_id)");
  $stmt->bindValue(':title', $album_title);
  $stmt->bindValue(':artist_id', $artist_id);
  $stmt->execute();

  $album_id = $db->lastInsertID();

  $stmt = $db->prepare("INSERT INTO tracks (Name, MediaTypeId, GenreId, Composer, Milliseconds, Bytes, UnitPrice) 
                        VALUES (:name, :media_type_id, :genre_id, :composer, :milliseconds, :bytes, :unit_price)");

  foreach ($tracks as $track) {
    $stmt->bindValue(':name', $track["name"]);
    $stmt->bindValue(':media_type_id', $track["media_type_id"]);
    $stmt->bindValue(':genre_id', $track["genre_id"]);
    $stmt->bindValue(':composer', $track["composer"]);
    $stmt->bindValue(':milliseconds', $track["milliseconds"]);
    $stmt->bindValue(':bytes', $track["bytes"]);
    $stmt->bindValue(':unit_price', $track["unit_price"]);
    $stmt->execute();
  }

  $db->commit();

  http_response_code(200);
  echo json_encode(array("message" => "Artist, album and tracks added successfully."));
} catch (Exception $e) {
  $db->rollBack();

  http_response_code(500);
  echo json_encode(array("message" => "Error adding artist, album and tracks: " . $e->getMessage()));
}