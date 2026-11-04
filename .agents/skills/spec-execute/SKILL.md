---
name: spec-execute
description: >-
  Implements remaining unchecked tasks from a colocated task.md. Marks items
  done as it goes. Does not commit. Use when the user says "spec-execute",
  @-mentions this skill, or asks to implement a task list.
disable-model-invocation: true
---

# Spec execute

Implement remaining unchecked tasks from a spec's `task.md`.

Do not rewrite the spec, plan, or tasks. Only mark checkboxes in `task.md`.
Do not commit unless the user separately asks.

If anything is unclear, ask instead of guessing.

## When invoked

1. Identify the spec (`@` mention, path, or `specs/<id>/task.md`). If none is clear, ask which spec to execute.
2. Read `task.md`, `plan.md`, `spec.md`, `AGENTS.md`, and the code they depend on.
3. If `task.md` is missing, or too vague to implement without guessing, stop. Tell the user to run **spec-plan** first. Do not implement.
4. If every item is already checked, stop. Tell the user there is nothing left to execute.
5. Follow hard constraints at the top of `task.md` (the lines that are not checkboxes).
6. Skip already-checked items. Execute all remaining unchecked items in order, including **Verify**.

## Implementing

- Do only what `task.md` lists. Do not invent extra work from the plan or spec.
- Follow numbered sections and items in order.
- After each item is actually done, mark it `[x]` in `task.md`. Do not check an item ahead of the work.
- Match the checkbox style already in that file (`- [x] `).
- If a remaining item would still force guessing (missing path, API, behavior), stop. Ask, or tell the user to run **spec-plan**. Do not improvise.
- If a task contradicts `AGENTS.md` or the spec, ask before choosing a side.

## Verify

Run every remaining **Verify** item as written (commands, checks, confirmations). Do not treat the skill as done until those items are checked off.

## Afterward

Summarize what was implemented and any Verify results (a few bullets). Do not commit.
