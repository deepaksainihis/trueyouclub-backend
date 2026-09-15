<?php
$groqApiKey = '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';
$url = 'https://api.groq.com/openai/v1/models';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $groqApiKey
]);

$response = curl_exec($ch);
curl_close($ch);

file_put_contents(__DIR__ . '/groq_models.json', $response);
echo "Done";
