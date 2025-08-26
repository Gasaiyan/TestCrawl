<?php
    header('Content-Type: application/json; charset=utf-8');

    $apiKey = "AIzaSyD-U2KV7v_iFzZ8DUj61U-yCCPEURxX7MU";
    $caCertPath = "D:\\laragon\\etc\\ssl\\cacert.pem";
    $model = "gemini-2.5-flash";

    function callGeminiApi(array $data, string $apiKey, string $model, string $caCertPath): ?array {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_CAINFO => $caCertPath,
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close($ch);
            return ["error" => "cURL error: " . $err];
        }
        $responseData = json_decode($response, true);
        curl_close($ch);
        return $responseData;
    }

    // --- Phân nhánh logic ---
    $action = $_POST['action'] ?? '';
    $query  = $_POST['query'] ?? '';

    if ($action === "suggest" && $query) {
        $prompt = "Người dùng muốn tìm: \"$query\". 
        Hãy gợi ý 5 từ khóa liên quan ngắn gọn, mỗi từ khóa trên 1 dòng.";

        $requestData = [
            "contents" => [[
                "role" => "user",
                "parts" => [["text" => $prompt]]
            ]]
        ];

        $apiResponse = callGeminiApi($requestData, $apiKey, $model, $caCertPath);

        if (isset($apiResponse['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $apiResponse['candidates'][0]['content']['parts'][0]['text'];
            $lines = preg_split('/\r\n|\r|\n/', trim($text));
            $suggestions = array_filter(array_map(function($l) {
                return trim(preg_replace('/^[\-\*\•\s]+/u', '', $l));
            }, $lines));
            echo json_encode(["suggestions" => array_values($suggestions)]);
            exit;
        } else {
            echo json_encode(["suggestions" => []]);
            exit;
        }
    }

    // --- Chatbot logic ---
    $prompt = $_POST['prompt'] ?? '';
    $history = json_decode($_POST['history'] ?? '[]', true);

    if (empty($prompt)) {
        echo json_encode(["error" => "Không có nội dung gửi lên."]);
        exit;
    }

    $contents = $history;
    $contents[] = [
        "role" => "user",
        "parts" => [["text" => $prompt]]
    ];

    $requestData = ["contents" => $contents];
    $apiResponse = callGeminiApi($requestData, $apiKey, $model, $caCertPath);

    if (isset($apiResponse['candidates'][0]['content']['parts'][0]['text'])) {
        $aiMessage = $apiResponse['candidates'][0]['content']['parts'][0]['text'];
    } else {
        $aiMessage = "Không có phản hồi văn bản từ AI.";
    }

    $history[] = ["role" => "user", "parts" => [["text" => $prompt]]];
    $history[] = ["role" => "model", "parts" => [["text" => $aiMessage]]];

    echo json_encode([
        "message" => $aiMessage,
        "history" => $history
    ]);
