<?php
header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json");

// Restricted words list
$restricted_words = ["hack", "crack", "carding", "illegal", "virus", "exploit", "hacking", "phishing", "botnet", "ddos", "sql injection"];

if(isset($_GET['text'])) {
    $text = strtolower($_GET['text']); // Convert to lowercase for case-insensitive matching

    // Check for restricted words
    foreach ($restricted_words as $word) {
        if (strpos($text, $word) !== false) {
            echo json_encode(["response" => "❌ Sorry! I can't help with that."]);
            exit;
        }
    }

    // API Request
    $api_url = "https://fam-official.serv00.net/sim/famofcai.php?text=" . urlencode($_GET['text']);

    // SSL Bypass Context
    $context = stream_context_create([
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ]
    ]);

    $response = file_get_contents($api_url, false, $context);

    if ($response) {
        $json_data = json_decode($response, true);

        if ($json_data && isset($json_data['message'])) {
            // Proper Formatting Fix
            $clean_message = trim($json_data['message']); 
            $clean_message = htmlspecialchars($clean_message, ENT_QUOTES, 'UTF-8'); // Fix special characters

            // New Line Fix
            $clean_message = nl2br($clean_message);  

            echo json_encode(["response" => $clean_message]);
        } else {
            echo json_encode(["error" => "Invalid API response"]);
        }
    } else {
        echo json_encode(["error" => "API request failed"]);
    }
} else {
    echo json_encode(["error" => "No text provided!"]);
}
?>