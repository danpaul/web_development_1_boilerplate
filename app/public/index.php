<?php

$question = $_GET['question'] ?? '';

if (empty($question)) {
    die('Please provide a question parameter in the query string.');
}

$faqPath = __DIR__ . '/../src/docs/customer_banking_faq.md';
$faq = file_get_contents($faqPath);

if ($faq === false) {
    die('Could not load the FAQ document.');
}

$prompt = <<<PROMPT
You are a helpful banking customer support assistant.
Answer the customer's question using ONLY the FAQ context below.
If the answer is not in the FAQ, say you do not have that information and suggest contacting support.
Keep the answer concise and clear.

FAQ context:
{$faq}

Customer question:
{$question}
PROMPT;

$response = file_get_contents(
    'http://host.docker.internal:11434/api/generate',
    false,
    stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode([
                'model' => 'qwen3:0.6b',
                'prompt' => $prompt,
                'stream' => false,
            ]),
        ],
    ])
);

if ($response === false) {
    die('Failed to reach the Ollama API.');
}

$result = json_decode($response, true);

header('Content-Type: application/json');
echo json_encode([
    'question' => $question,
    'answer' => $result['response'] ?? null,
], JSON_PRETTY_PRINT);
