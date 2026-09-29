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

function run_tool(string $name): string
{
    return match ($name) {
        'check_balance' => check_balance(),
        default => json_encode(['error' => 'Unknown tool']),
    };
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

Tool policy:
- Call check_balance when the customer asks for their live balance amount. Examples that MUST call the tool: "what is my balance?", "what's my balance?", "how much money do I have?", "how much is in my account?".
- Do not call check_balance for how-to questions. "How do I check my account balance?" must be answered from the FAQ and must not call the tool.
- Never invent a balance. The FAQ does not contain the customer's balance.
- If a how-to question is not in the FAQ, say you do not have that information and suggest contacting support.
- Keep answers concise and clear.

## FAQ

{$faq}

Final check before you answer:
- "what is my balance?" -> call check_balance
- "how much money do I have?" -> call check_balance
- "how do I check my account balance?" -> reply with the FAQ steps: sign in to online banking or the mobile app and select the account. Do not call a tool.
PROMPT;

$emptyParameters = [
    'type' => 'object',
    'properties' => new stdClass(),
    'required' => [],
];

$tools = [
    [
        'type' => 'function',
        'function' => [
            'name' => 'check_balance',
            'description' => 'Get the live numeric balance of the customer account. Call this for what is my balance and how much money do I have. Do not call this for how-to questions.',
            'parameters' => $emptyParameters,
        ],
    ],
];

$messages = [
    ['role' => 'system', 'content' => $systemPrompt],
    ['role' => 'user', 'content' => $question],
];

$model = 'qwen3:0.6b';
$options = [
    'temperature' => 1.0,
    'seed' => 42,
];

$result = call_ollama([
    'model' => $model,
    'messages' => $messages,
    'stream' => false,
    'tools' => $tools,
    'options' => $options,
]);

$toolsUsed = [];
$assistantMessage = $result['message'] ?? [];
$toolCalls = $assistantMessage['tool_calls'] ?? [];

if (!empty($toolCalls)) {
    $messages[] = normalize_assistant_message($assistantMessage);

    foreach ($toolCalls as $toolCall) {
        $name = $toolCall['function']['name'] ?? '';
        $toolResult = run_tool($name);

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
        'model' => $model,
        'messages' => $messages,
        'stream' => false,
        'think' => false,
        'tools' => $tools,
        'options' => $options,
    ]);

    $assistantMessage = $result['message'] ?? [];
}

header('Content-Type: application/json');
echo json_encode([
    'question' => $question,
    'answer' => $assistantMessage['content'] ?? null,
    'tools' => $toolsUsed,
], JSON_PRETTY_PRINT);
