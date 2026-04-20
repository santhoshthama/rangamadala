# Director Documentation Index

This folder contains implementation-focused documentation for the Director module.

## Files

- `DIRECTOR_VIEWS_INPUT_GUIDE.md`
  - Main guide for **view-layer work** (input fields, form actions, required params, flash/errors, safe edit checklist).
  - Use this first when your task is: “add input field”, “change form”, “update CRUD form UI”.

- `DIRECTOR_MVC_INPUT_MAPPING.md`
  - Appendix that maps each important Director form to:
    - controller method
    - model method
    - high-level database tables touched
  - Use this when reviewer asks: “How does this field reach DB?”

## Quick start for tomorrow code check

1. Open `DIRECTOR_VIEWS_INPUT_GUIDE.md`.
2. Find the exact view/form you are changing.
3. Follow the **Input Field Addition Checklist**.
4. If asked about backend linkage, open `DIRECTOR_MVC_INPUT_MAPPING.md`.

## Scope

- Module: `app/views/director/*`, `app/controllers/director*`, `app/models/M_*` used by Director.
- Focus: practical CRUD input-field changes with MVC-safe boundaries.
