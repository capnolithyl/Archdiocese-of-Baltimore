# Archdiocese of Baltimore

Custom PHP WordPress theme based on **_tw**, using Tailwind CSS 4, `theme.json` and ACF Pro. This repository is the site-specific theme, not the shared block plugin or WordPress database.

## Current source-code status (November 18, 2026)

| Area | Status |
| --- | --- |
| Theme/build | Present: PHP theme, Tailwind/PostCSS, esbuild, lint/format config, GitHub Actions |
| Design system | Present: Baltimore palette, Lato + EB Garamond, base styles and archive/card styles |
| ACF options | Present: header images, footer options, contact details, office hours and social links |
| Menus | Primary, utility, footer and services locations registered; **final header/footer UI is unfinished** |
| WordPress templates | Present: page, single, home/posts index, archive, category/tag/taxonomy fallback, search, 404, index |
| Archive display | Custom listing cards, archive headers, excerpts, pagination and empty states implemented; visual QA remains |
| Search | Standard search form and search results page implemented; **live typeahead search is not implemented** |
| Comments | Disabled by `theme/inc/comments.php`: discussion support, admin links/screens, API routes and feeds |
| Homepage | **Approved homepage composition is not built yet** |
| Custom Gutenberg/ACF blocks | **Theme-owned:** `dpi/hero`, `dpi/five-pillars`, `dpi/feature-banner`, `dpi/community-slider`, `dpi/stats`, preserving original block names and ACF field keys. Other `dpi/*` blocks remain in the plugin. |
| GIS | External existing plugin; integration and configuration later |
| Staff / ministries | No theme CPT implementation; planned as reusable plugin modules |

This table is based on code in this repository, **not a live WordPress/site test**.

The five theme-owned DPI blocks are styled primarily through **Tailwind utility classes in their PHP templates**. Copied block CSS files were removed; the Slick carousel vendor stylesheet remains. The separate `dpi-blocks` plugin may still enqueue its CSS when other plugin-owned blocks are used.

## Sprint 1: homepage by December 4, 2026

Complete the design-aligned header/footer and theme shell, reusable homepage blocks, Gutenberg page assembly, functional live search suggestions, and desktop/mobile QA. Sample data is sufficient for component testing. The content team owns real copy and data; migration, GIS, archives for new CPTs and production launch are outside this sprint.

Design reference: https://motto-lever-19676077.figma.site/

## Development

Requires a WordPress installation, ACF Pro and Node.js/npm. Run commands from the repository root:

```bash
npm ci
npm run dev
npm run watch
```

Theme code lives in `theme/`. Generated theme CSS and compiled editor JavaScript are ignored by Git.

## Build and deploy

```bash
npm run bundle     # build theme and create baltimore.zip
npm run deploy     # build + SFTP upload using local settings
npm run ship       # build, commit on main, push and deploy
```

Never commit SFTP credentials; the deploy script reads local ignored settings. GitHub Actions validates production bundles but does not deploy.

See [docs/architecture.md](docs/architecture.md) for code ownership, plugin boundaries, deployment conventions and architectural decisions.
