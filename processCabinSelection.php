<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cabinId'])) {
    $cabinId = $_POST['cabinId'];
    
    // Process the cabin selection
    // For example, save the selection to the database or perform other actions

    // Send a response back to the client
    echo json_encode(['status' => 'success', 'message' => 'Cabin selected successfully', 'cabinId' => $cabinId]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
