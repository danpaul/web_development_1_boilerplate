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

function check_balance(): string
{
    $amount = number_format(mt_rand(100, 99999) / 100, 2);
    return json_encode(['balance' => $amount, 'currency' => 'EUR']);
}

function normalize_assistant_message(array $message): array
{
    // json_decode(..., true) turns empty JSON objects {} into [].
    // Ollama expects tool-call arguments to be an object, so fix that before
    // sending the assistant message back on the follow-up request.
    if (!empty($message['tool_calls']) && is_array($message['tool_calls'])) {
        foreach ($message['tool_calls'] as &$toolCall) {
            $args = $toolCall['function']['arguments'] ?? null;
            if ($args === [] || $args === null || !is_array($args)) {
                $toolCall['function']['arguments'] = new stdClass();
            }
        }
        unset($toolCall);
    }

    return $message;
}

function call_ollama(array $payload): array
{
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($payload),
            'ignore_errors' => true,
        ],
    ]);

    $response = file_get_contents(
        'http://host.docker.internal:11434/api/chat',
        false,
        $context
    );

    if ($response === false) {
        die('Failed to reach the Ollama API.');
    }

    $statusLine = $http_response_header[0] ?? '';
    if (!str_contains($statusLine, '200')) {
        die('Ollama API error (' . $statusLine . '): ' . $response);
    }

    $result = json_decode($response, true);

    if (!is_array($result)) {
        die('Invalid response from the Ollama API.');
    }

    return $result;
}

$systemPrompt = <<<PROMPT
You are a helpful banking customer support assistant.
Answer the customer's question using ONLY the FAQ context below.
If the answer is not in the FAQ, say you do not have that information and suggest contacting support.
Keep the answer concise and clear.
When the customer asks for their current or live account balance, call the check_balance tool instead of only explaining how to check balance in the FAQ.

FAQ context:
{$faq}
PROMPT;

$tools = [
    [
        'type' => 'function',
        'function' => [
            'name' => 'check_balance',
            'description' => 'Get the customer\'s current account balance (demo).',
            'parameters' => [
                'type' => 'object',
                'properties' => new stdClass(),
            ],
        ],
    ],
];

$messages = [
    ['role' => 'system', 'content' => $systemPrompt],
    ['role' => 'user', 'content' => $question],
];

$result = call_ollama([
    'model' => 'qwen3:0.6b',
    'messages' => $messages,
    'stream' => false,
    'tools' => $tools,
]);

$toolsUsed = [];
$assistantMessage = $result['message'] ?? [];
$toolCalls = $assistantMessage['tool_calls'] ?? [];

if (!empty($toolCalls)) {
    $messages[] = normalize_assistant_message($assistantMessage);

    foreach ($toolCalls as $toolCall) {
        $name = $toolCall['function']['name'] ?? '';
        $toolResult = $name === 'check_balance'
            ? check_balance()
            : json_encode(['error' => 'Unknown tool']);

        $toolsUsed[] = [
            'name' => $name,
            'result' => json_decode($toolResult, true),
        ];

        $messages[] = [
            'role' => 'tool',
            'tool_name' => $name,
            'content' => $toolResult,
        ];
    }

    $result = call_ollama([
        'model' => 'qwen3:0.6b',
        'messages' => $messages,
        'stream' => false,
        'tools' => $tools,
    ]);

    $assistantMessage = $result['message'] ?? [];
}

header('Content-Type: application/json');
echo json_encode([
    'question' => $question,
    'answer' => $assistantMessage['content'] ?? null,
    'tools' => $toolsUsed,
], JSON_PRETTY_PRINT);
