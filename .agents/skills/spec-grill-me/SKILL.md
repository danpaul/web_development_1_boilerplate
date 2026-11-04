---
name: spec-grill-me
description: >-
  Clarifies a spec by asking the user batches of questions, then rewriting
  the spec into Goal, Requirements, Acceptance Criteria, and Out of Scope.
  Does not write a review. Use when the user says "grill me", @-mentions this
  skill, or explicitly asks to grill or clarify a spec.
disable-model-invocation: true
---

# Spec grill-me

Turn a draft spec into an implementable spec by questioning the user, then updating that spec file in place.

Do not implement the spec. Do not create `plan.md` or `task.md`. Do not write `review.md`.

If anything is unclear, please ask me instead of guessing.

You may add additional details to the spec if needed but try to keep it concise without losing essential details. Do not invent a product decision that the spec does not already contain.

## When invoked

1. Identify the spec (`@` mention, path, or `specs/<id>/spec.md`). If none is clear, ask which spec to grill.
2. Read the spec, `AGENTS.md`, and the code/conventions it depends on.
3. Split content into: already decided, implied by the repo, and would force an implementer to guess.

## Question rounds

Ask **3–6 questions per round**, then wait for answers. Prefer the AskQuestion tool when options are few; otherwise ask conversationally.

Ask only decisions that change **scope, files, API, UX, constraints, or acceptance**. Do not ask:

- What the spec already states
- What the repo already decides (fold those facts into the spec)
- Pure implementation trivia (library internals, exact class strings unless the spec cares)

After each round of answers:

1. Update the spec **in place**.
2. Summarize what you added or tightened (a few bullets).
3. Start another round if remaining gaps would still force guessing.

Stop when **Goal**, **Requirements**, and **Acceptance Criteria** are specific enough to implement without guessing, or when the user says stop. A placeholder **Out of Scope** section does not block that. Then tell the user the next step is **spec-plan**.

## Writing the spec

Keep only these headings. Do not add or rename them. Drop any other heading, including a title.

- **Goal** — a short statement of what the feature accomplishes. Replace the template prompt.
- **Requirements** — plain bullets. These may name libraries, files, and other interfaces the product cannot change. Replace every placeholder bullet.
- **Acceptance Criteria** — plain bullets that state an observable outcome. Replace every placeholder bullet.
- **Out of Scope** — plain bullets for exclusions the user named. If they named none, leave this section exactly as the template wrote it.

Do not number requirements or acceptance criteria. Do not use "The system shall" or Given/when/then.

Fix incomplete sentences and typos while updating.

Fold in matching `AGENTS.md` / codebase conventions. If the spec would contradict them, ask before choosing a side.

Match the specificity of `specs/001-component-structure/spec.md` (files, APIs, behaviour, acceptance criteria) — not that file's heading layout. Record unchangeable files, libraries, and APIs under **Requirements**.

Keep the spec concise. Prefer bullets over prose except in **Goal**. Do not add rationale, alternatives considered, or implementation steps unless the user asked for them.
