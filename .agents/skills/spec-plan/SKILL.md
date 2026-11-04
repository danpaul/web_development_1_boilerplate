---
name: spec-plan
description: >-
  Turns an implementable spec into colocated plan.md and task.md. Asks only
  about implementation gaps (files, APIs, order, how). Does not implement.
  Use when the user says "plan", @-mentions this skill, or asks to generate
  a plan from a spec.
disable-model-invocation: true
---

# Spec plan

Turn an implementable spec into a colocated `plan.md` and `task.md`.

Do not implement the spec. Do not edit the spec.

If anything is unclear, ask instead of guessing.

Keep the plan and tasks concise without losing essential details.

## When invoked

1. Identify the spec (`@` mention, path, or `specs/<id>/spec.md`). If none is clear, ask which spec to plan.
2. Read the spec, `AGENTS.md`, and the code/conventions it depends on.
3. If **Goal**, **Requirements**, or **Acceptance Criteria** are still the template prompt, a placeholder bullet, or too vague to implement without product guessing, stop. Tell the user to run **spec-grill-me** first. Do not write `plan.md` or `task.md`.
4. If `plan.md` or `task.md` already exists in that folder, ask before overwriting.
5. Split remaining gaps into: already decided by the spec/repo, vs implementation choices that would force guessing.

A still-placeholder **Out of Scope** section is not a set of real exclusions. Do not copy its placeholder into the plan.

## Question rounds

Ask **3–6 questions per round**, then wait. Prefer the AskQuestion tool when options are few; otherwise ask conversationally.

Ask only **implementation** decisions: files, APIs, types, order, how. Do not re-grill product/scope. Do not ask:

- What the spec already states
- What the repo already decides (fold those facts into the plan)
- Pure trivia the implementer can choose without changing the outcome

After each round of answers:

1. Write or update `plan.md` and `task.md`.
2. Summarize what you added or tightened (a few bullets).
3. Start another round if remaining gaps would still force an implementer to guess.

Stop when both files are specific enough to implement without guessing, or when the user says stop.

## Writing plan.md

Match `specs/001-component-structure/plan.md` (specificity and heading style):

- Title, link to `./spec.md`, one-line summary of what will be done
- **Technical context** — stack, existing files, constraints (facts)
- **Target structure** — files to add/change/delete
- Implementation details — enough "how" to build (types, class maps, export shape, exact paths). Use short code sketches when they pin a choice.
- **Implementation order** — numbered steps
- **Out of scope** — work the spec does not require, including a filled **Out of Scope** section, plus anything implied by the repo

Prefer bullets over prose. Do not add rationale or alternatives unless the user asked.

## Writing task.md

Always write `task.md` in the same folder. Match `specs/001-component-structure/task.md`:

- Source links: `./plan.md` and `./spec.md`
- Grouped checkbox sections (feature areas, then a **Verify** section)
- Each item is a concrete, checkable action an implementer can mark done
- Leave checkboxes unchecked
- Repeat hard constraints that prevent wrong work (e.g. "do not mount X on a page")

Derive tasks from the plan. Do not invent work that is not in the spec or plan.
