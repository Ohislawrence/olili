<!-- Copilot instructions for tutorapp (Laravel + Inertia + Vue3) -->
# Copilot Instructions — tutorapp

Purpose: help AI coding agents be immediately productive in this repository (Laravel 12 + Inertia + Vue3).

- **Big picture**: This is a monolithic Laravel app with Inertia/Vue3 frontend. Server-side controllers (app/Http/Controllers) render data and the frontend uses Inertia pages under `resources/js/Pages`. Routes are split by purpose in `routes/` (e.g. `student.php`, `admin.php`, `web.php`). Jobs live in `app/Jobs` and background processing is via Laravel Queues.

- **Key files to read first**:
  - `composer.json` — notable scripts: `composer dev` runs `php artisan serve`, `php artisan queue:listen`, `php artisan pail` and `npm run dev` (concurrently).
  - `package.json` & `vite.config.js` — front-end build (`npm run dev`, `npm run build`) and `resources/js/app.js` (Inertia page resolution).
  - `phpunit.xml` — tests default to in-memory sqlite and many test-related env flags.
  - `routes/*.php` — routing organization and route name patterns (`student.*`, `exam-preps.*`, etc.).

- **Frontend conventions**:
  - Inertia pages live in `resources/js/Pages`. The Inertia resolver: `resolvePageComponent('./Pages/${name}.vue', import.meta.glob('./Pages/**/*.vue'))`.
  - Use named routes from Ziggy via `ZiggyVue` (imported in `resources/js/app.js`). Example page path: `resources/js/Pages/Student/ExamPreps/Show.vue`.
  - Tailwind + Tiptap are the standard UI/editor stacks (see `package.json`).

- **Backend conventions & patterns**:
  - Role-based authorization via `spatie/laravel-permission` (look for `role:` middleware and `hasRole()` checks).
  - Subscription/membership checks via middleware keys like `subscription:take_exam_prep`.
  - Background work: jobs in `app/Jobs` and queued generation tasks (e.g. `GenerateCourseContentJob`). Composer `dev` runs a `queue:listen` for local development.

- **Integration points**:
  - Payments: Paystack client `yabacon/paystack-php` and webhook route `/webhook/paystack`.
  - Web push: `laravel-notification-channels/webpush` and custom service at `app/Services/WebPush`.
  - PWA support via `ladumor/laravel-pwa` and service worker files in `public/`.

- **Build / dev / test commands** (examples):
  - Full concurrent dev (server, queue, pail, vite):
    - `composer run dev` (defined in `composer.json` scripts)
  - Frontend only: `npm run dev` (Vite)
  - Build frontend: `npm run build`
  - Run unit/feature tests: `composer run test` or `php artisan test` (phpunit uses in-memory sqlite)
  - Install/setup helper: `composer run setup` (installs deps, copies env, migrates, runs `npm install` + build)

- **Editing / adding pages and controllers** (concrete examples):
  - To add a new student-facing page: create `app/Http/Controllers/Student/MyController.php`, add route in `routes/student.php` with `->name('student.my-route')`, and add the Inertia view `resources/js/Pages/Student/MyRoute.vue`.
  - To link to a named route from Vue, use Ziggy: `route('student.exam-preps.show', examPrep.id)`.

- **Testing notes**:
  - `phpunit.xml` sets `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` — tests expect an ephemeral DB.
  - Use factories under `database/factories` and seeders in `database/seeders` when creating fixtures.

- **What NOT to change lightly**:
  - The Inertia page path resolver in `resources/js/app.js` and `vite.config.js` plugin settings — changing these requires updating many imports.
  - `composer.json` dev script ordering: it assumes `npm run dev` exposes the Vite dev server used by Inertia; replacing it breaks local developer flows.

If any section is unclear or you want examples expanded (routing examples, common refactors, or a checklist for PRs), tell me which part to expand.
