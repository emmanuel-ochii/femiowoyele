# ADR 0001: Monorepo Laravel API and Vue SPA

## Status

Accepted

## Context

The specification calls for a Vue 3 SPA, Laravel REST API under `/api`, Sanctum auth, MySQL, and a domain-based folder structure. Network access was unavailable during setup, so the implementation uses the locally available Laravel 12 + Sanctum scaffold rather than downloading Laravel 11.

## Decision

Use a monorepo with `backend/` for Laravel and `frontend/` for Vue/Vite. Public content is read through `/api/*`; admin CMS mutations are protected under `/api/admin/*` using Sanctum tokens and a `manage-content` gate.

## Consequences

The application remains aligned with the requested Laravel/Sanctum architecture and can be moved to Laravel 11 later if an exact framework pin is required. The split frontend/backend structure keeps deployment flexible and preserves a clean API contract.
