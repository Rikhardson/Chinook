<?php

function openDB() {
    $db = new PDO("mysql:host=127.0.0.1;dbname=chinook;charset=utf8","root","");
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    return $db;
}

function selectAsJson(object $db,string $sql): void {
  $query = $db->query($sql);
  $results = $query->fetchAll(PDO::FETCH_ASSOC);
  header('HTTP/1.1 200 OK');
  echo json_encode($results);
}

function executeInsert(object $db,string $sql): int {
  $query = $db->query($sql);  
  return $db->lastInsertId();
}

function returnError(PDOException $pdoex): void {
  header('HTTP/1.1 500 Internal Server Error');
  $error = array('error' => $pdoex->getMessage());
  echo json_encode($error);
  exit;
}