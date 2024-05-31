<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);

    if (isset($data['leadformData'])) {
        $_SESSION['leadformData'] = $data['leadformData'];
        $_SESSION['usersData'] = $data['existingEntries'];
        echo "Lead form session data stored successfully.";
    } else {
        echo "No lead form data provided.";
    }
    exit;
}
?>