# Responsive UI design

## Goal

Make every existing Blade screen usable at 320px, 375px, 390px, 768px, and desktop without changing role permissions or business flows.

## Layout

`layouts/app.blade.php` becomes responsive shell. Desktop keeps 300px sidebar. Below `md`, header gets menu trigger, sidebar becomes focusable off-canvas drawer with backdrop, and Escape/backdrop/link closes it. Content padding uses `px-4 sm:px-6 lg:px-8`; header logos shrink safely.

## Screen patterns

Headers and toolbars stack vertically below `sm`; hero sections use responsive padding and height. Form grids collapse to one column below `sm`. Modals retain viewport padding, scroll vertically, and use full available mobile width.

Desktop tables remain at `md` and above. Below `md`, workflow data displays role-specific cards with key identity, status, context, and existing action controls. This applies first to employee IDP submission/uploads and manager review/evaluation, then admin monitoring and evaluation lists. No data or action becomes unavailable on mobile.

## Shared behavior

Use existing Blade, Tailwind CDN, and vanilla JavaScript only. Keep existing table search/pagination. Improve its mobile controls without adding dependencies. No backend, route, authorization, or schema changes.

## Errors and accessibility

Mobile menu has accessible label, expanded state, keyboard Escape close, backdrop close, and focusable controls. Existing form validation and async-upload error handling remain. Buttons retain touch-safe spacing.

## Verification

Run Blade/PHP test suite and front-end build. Manually verify all four roles at 320px, 375px, 390px, 768px, and desktop: navigation, IDP form, upload, manager review, admin table, modal, logout.
