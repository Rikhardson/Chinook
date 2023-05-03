<?php

require_once "functions.php";

$db = openDb();
$invoice_item_id = $_REQUEST['InvoiceLineId'];

$query = "DELETE FROM invoice_items WHERE InvoiceLineId = :invoice_item_id";

$stmt = $db->prepare($query);

$stmt->bindParam(':invoice_item_id', $invoice_item_id);


if($stmt->execute()){
    
    echo "Invoice item with id $invoice_item_id has been removed.";
} else{
    
    echo "Error removing invoice item with id $invoice_item_id.";
}