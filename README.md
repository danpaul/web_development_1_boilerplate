## About

Hello world banking example using Ollama with local Qwen 0.6b model to answer questions from FAQ context and conditionally invoke a tool to check account balances.

This is a test to see if the lightweight model can do something interactive and call tools. Findings show that with careful context definition and clear tool description, the model typically chooses the correct answer, at least in well-specified scenarios.

This is okay for testing/educational purposes but not suitable for a real production application.

## Setup

Based on this ChatGPT chat: https://chatgpt.com/s/t_6abb8de9dc2881919a7a66f4803d3951

Install Ollama and make sure it is running.

Run:

- `ollama pull qwen3:0.6b`
- `docker-compose up`

Review code: `app\public\ollama.php`

---

tool calling (what is my balance?): view-source:localhost/?question=what%20is%20my%20balance?

faq answer (how to i open a savings account?): localhost/?question=how%20do%20i%20open%20a%20savings%20account
