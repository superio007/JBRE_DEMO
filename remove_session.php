<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cabinId'])) {
    $cabinId = $_POST['cabinId'];
    if (isset($_SESSION['selectedDataArray'])) {
        foreach ($_SESSION['selectedDataArray'] as $key => $sess) {
            if ($sess['cabinId'] == $cabinId) {
                unset($_SESSION['selectedDataArray'][$key]);
                break;
            }
        }
        // Reindex the array
        $_SESSION['selectedDataArray'] = array_values($_SESSION['selectedDataArray']);
    }
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
