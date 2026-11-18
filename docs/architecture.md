# Archdiocese of Baltimore Project Architecture

Last updated: 2026-08-25

This document is the living technical reference for the Archdiocese of Baltimore WordPress rebuild. It records current ownership boundaries, content models, development/deployment conventions, and major architectural decisions so they do not have to be reconstructed later from code or conversation history.

When implementation and this document disagree, verify the current code and update this document as part of the same change.

## 1. Sources of Truth

The project has two code repositories:

- Theme: `capnolithyl/Archdiocese-of-Baltimore`
- Shared blocks/content plugin: `marcus-diocesan/dpi-blocks`

GitHub is the canonical source for code. The Figma design is the source of truth for visual design and interaction intent, but the Figma Make React/Tailwind prototype is design documentation rather than production architecture.

The production implementation is WordPress/PHP with Tailwind/PostCSS and small, focused JavaScript bundles where required.

## 2. Platform Baseline

- PHP 8.4.
- WordPress 7.0 target.
- Classic PHP theme based on `_tw`; Full Site Editing/block templates are intentionally disabled.
- ACF Pro for structured fields and Options Pages.
- Gravity Forms for forms and form processing.
- Tailwind CSS 4/PostCSS for the theme styling/build layer.
- esbuild for the small JavaScript build used by the theme.
- Normal WordPress Media Library for uploads.
- Native WordPress search unless requirements materially change.
- NGINX/AccelerateWP handle server-level caching; the theme must remain cache-safe but does not own that stack.
- No dedicated SEO plugin is assumed at this stage.
- Google Fonts are used for production typography where required by the approved design.

## 3. Repository and Ownership Boundary

The theme and `dpi-blocks` intentionally have different responsibilities.

### Baltimore theme owns

- Site templates and template parts.
- Header, navigation, search, footer, and other global site UI.
- Baltimore-specific presentation and styling.
- `theme.json` design-system settings.
- Baltimore-specific ACF field groups and Options Pages.
- Theme-owned copies of `dpi/hero`, `dpi/five-pillars`, `dpi/feature-banner`, `dpi/community-slider`, and `dpi/stats` including their ACF fields and runtime assets. When DPI Blocks is active, its template override resolver uses the copies under `dpi-blocks/blocks/`; when inactive, the theme registers the same block names independently.
- Site-specific Gutenberg patterns/compositions.
- Site-specific relationships and integration code where those relationships are not reusable enough to belong in the plugin.
- Presentation/integration of the GIS plugin.
- Theme helpers and rendering utilities that are specific to Baltimore.

### DPI Blocks owns

- Reusable custom Gutenberg/ACF blocks **excluding the five theme-owned copies listed above**.
- Each reusable block's `block.json` registration.
- Reusable block render templates/rendering helpers.
- Reusable block JavaScript/behavior.
- Reusable structural CSS and stable extension hooks where appropriate.
- Reusable block ACF field groups and their Local JSON.
- Reusable opt-in content-type modules such as Ministry, Staff, and Office.
- Generic block/content utilities that are useful across multiple diocesan or institutional projects.

The plugin may expose renderer overrides, filters, or extension hooks so the Baltimore theme can adapt presentation without copying reusable plugin logic into the theme.

A reusable component should not contain Baltimore-specific colors, copy, URLs, or content assumptions unless those values are passed/configured by the site.

## 4. ACF and Local JSON Ownership

ACF Local JSON is split by owner:

- Baltimore-specific field groups and the five copied block field groups: `theme/acf-json/` in the Baltimore theme repository.
- Remaining reusable block/content-module field groups: the `acf-json/` owned by `dpi-blocks`.

Do not keep the same field group in both repositories.

Use structured ACF fields where structure matters. Use native Gutenberg blocks/InnerBlocks for free-form editorial content such as paragraphs, headings, lists, and buttons when editors benefit from flexibility.

Do not build an enormous Flexible Content field as a parallel page builder.

### Naming conventions

Baltimore theme code should use the Baltimore prefix consistently:

- PHP functions: `balt_*`
- Constants: `BALT_*`
- ACF field group keys: `group_balt_*`
- ACF field keys: `field_balt_*`
- Baltimore field names: `balt_*` where a project-specific prefix is useful

Reusable plugin code should retain the DPI naming/namespace conventions:

- Block namespace: `dpi/*`
- Reusable ACF group keys: `group_dpi_*`
- Reusable ACF field keys: `field_dpi_*`
- Reusable field names/helpers should use the plugin's `dpi_*` convention where appropriate

Prefer readable semantic names over names tied to temporary colors, page names, or Figma frame labels.

## 5. Custom Block Philosophy

A Figma section is not automatically a custom block. Create a reusable block when the component has a stable identity, reusable structure, meaningful editor controls, and/or behavior worth encapsulating.

Rules for shared blocks:

- Reuse recognizable content patterns instead of creating a different block for every page.
- Do not create a universal section block with dozens of unrelated toggles.
- Query canonical WordPress content rather than duplicating Staff, Ministry, Office, or News records inside page-level repeaters.
- Use InnerBlocks for rich free-form body content when appropriate.
- Keep block JavaScript scoped to the block instance and safe for multiple instances on one page.
- Load block-specific assets only where useful/practical.
- Blocks must tolerate missing optional fields and unusually long editor content.
- Accessibility is part of the component contract, not a later styling pass.
- Baltimore-specific visual styling belongs in the theme; generic structure/behavior belongs in the plugin.

Site-specific page patterns belong in the Baltimore theme and may compose reusable blocks supplied by `dpi-blocks`.

## 6. Content Model

Create a Custom Post Type when an object has its own identity, URL, metadata, archive/query needs, relationships, or lifecycle. Do not create CPTs merely because the design displays cards.

| Content | Canonical model | Owner | Notes |
| --- | --- | --- | --- |
| News/articles | WordPress Posts | WordPress/theme | Use normal posts unless a later editorial requirement justifies a separate model. |
| Ministries | Ministry CPT | DPI Blocks opt-in module | Public canonical ministry records; Baltimore may extend with site-specific fields/relationships. |
| Offices | Office CPT | DPI Blocks opt-in module | Reusable public office records. The Office module is intentionally reusable for diocesan/institutional projects. |
| Staff | Staff CPT | DPI Blocks opt-in module | Public canonical staff records; query these rather than duplicating staff data in pages. |
| Campaigns | WordPress Pages | WordPress/theme | Use Pages/patterns by default. Create a Campaign CPT only if a real independent lifecycle/archive emerges. |
| Resources | No CPT by default | Page/block context | Store as editable links/files in the relevant page, Office, member-resource area, or reusable block. |
| Parishes | GIS plugin custom data | GIS plugin | Not a theme CPT. |
| Schools | GIS plugin custom data | GIS plugin | Not a theme CPT. |
| Jobs | GIS plugin custom data | GIS plugin | Not a theme CPT. |
| Events | Conditional | Event plugin/custom model if required | Only create a custom Event CPT if this project owns event management. |
| Podcasts/Videos/Press/Letters/Homilies | Conditional shared Media model | TBD | Consider one Media CPT + type taxonomy only if the items genuinely share fields, lifecycle, archive, and editorial behavior. Do not create a CPT for every homepage tab. |

### Relationships

Plan the core institutional relationships around:

```text
Ministry
   ↓
Office
   ↓
Staff
```

Specifically:

- Ministry -> Office.
- Office -> Staff.
- Staff -> Ministry only where a direct relationship is useful beyond Office membership.
- Resource links stay in the relevant page/Office/block context rather than requiring a Resource CPT.
- If a custom Event model is eventually used, Event -> Ministry and optionally Parish may be useful.
- Parish and School relationships must respect the GIS plugin's data model rather than creating parallel theme records.

The exact reusable relationship fields should live in `dpi-blocks` only when they make sense across projects. Baltimore-only relationship assumptions belong in the theme/integration layer.

## 7. Global Theme Options

Use ACF Options only for genuinely global data.

The global settings architecture is:

### Theme Settings

- Contact
  - Address
  - General email
  - Phone number
  - Phone availability/hours
  - 24/7 emergency hotline
  - Fax number
- Office Hours
  - Flexible repeater using a Days/Label value and Hours value
  - Optional notes for holidays, appointments, seasonal schedules, etc.
- Social Media
  - Repeater
  - Font Awesome Brands icon using the installed ACF Font Awesome field
  - Destination link

### Header Settings

- Primary site logo.
- Alternate site logo for contrasting/sticky header states.
- Default featured image used by presentation components when a supported content item has no featured image.

A dedicated social/OpenGraph fallback image may be added later if the social-sharing crop needs to differ from the normal featured-image fallback.

### Footer Settings

- Footer logo.
- Footer blurb.
- Admin shortcuts to Social Media, Contact Information, Footer Menu, and Our Services Menu editing.
- Privacy Policy link.
- Terms of Service link.
- Accessibility link.

Do not add speculative global settings. Donation settings are not required yet. Emergency-alert settings remain owned by the existing alert plugin. Integration IDs/settings should be added only when editors legitimately need to manage them.

## 8. GIS Integration Boundary

The existing GIS plugin owns the directory application and data model for Parishes, Schools, and Jobs.

### GIS plugin owns

- Parish, School, and Job data stored in its custom database tables.
- Data management/update logic.
- Querying and filtering business logic.
- Location search and geolocation behavior.
- Map provider, markers, and map behavior.
- Result-list/detail-panel data and selection behavior.
- Parish- and School-specific filter semantics.
- Any REST/AJAX endpoints used by the application.
- Shareable filter URL state. If this behavior needs to be added or corrected, implement it in the GIS plugin rather than page-level theme code.
- Performance logic specific to directory data, such as bounding large responses, geocoding/cache behavior, and stale-request handling.

### Baltimore theme owns

- Figma-aligned layout and visual presentation around the plugin output.
- Typography, spacing, colors, and responsive presentation.
- Integration into the site's page shell.
- Accessible presentation improvements that do not duplicate the plugin's business/data model.

### GIS rules

- Find a Parish and Find a School remain separate pages using the existing plugin interfaces.
- Jobs remain plugin-owned; do not create a separate Job CPT in the theme.
- Do not duplicate Parish/School schedules, language data, contact data, or other plugin-managed fields in ACF merely to style them.
- Essential directory information must remain usable without direct map manipulation and should remain useful if the map fails.
- Map libraries should load only on pages that use them.
- Geolocation is an optional convenience, not the only search method.

## 9. Theme Build Commands

Install dependencies with:

```bash
npm ci
```

Common commands:

```bash
npm run dev
```

Build the development CSS/JS outputs once.

```bash
npm run watch
```

Watch the Tailwind/PostCSS and esbuild inputs during active development.

```bash
npm run lint
npm run lint-fix
```

Check/fix ESLint and Prettier rules.

```bash
npm run prod
```

Build production/minified CSS and JavaScript.

```bash
npm run bundle
```

Run the production build and create `baltimore.zip`.

The bundle packages the contents of `theme/` under a top-level `baltimore/` directory. During bundling, the version constant in the packaged `functions.php` is replaced with a date/time-derived value used for cache busting compiled assets.

## 10. Deployment

Deployment is intentionally local rather than performed by a GitHub-hosted runner because server SFTP access is IP-restricted.

Local SFTP credentials/configuration must never be committed. The deploy script reads the ignored `.vscode/sftp.json` by default, or another config path through `DEPLOY_SFTP_CONFIG`.

One-time Python dependency setup:

```bash
npm run deploy:setup
```

### Work-in-progress deployment

```bash
npm run deploy
```

This performs a production bundle and uploads it, but it does not create a Git commit or push to GitHub. Use it for intentional staging synchronization while work is still in progress.

### Ship committed work

```bash
npm run ship
```

`ship` requires the current branch to be `main` and performs the following sequence:

1. Inspect the working tree.
2. If changes exist, prompt for a commit message.
3. Build the exact production bundle before committing.
4. Commit the changes if needed.
5. Push `main` to GitHub.
6. Deploy the already-built bundle over SFTP.

If the build, commit, push, or deployment fails, the ship process aborts.

### Deployment path safety

The deploy script validates that the locally configured project path ends in:

```text
/wp-content/themes/baltimore
```

The built theme contents are synchronized to its nested:

```text
/theme
```

child directory. Stale files are pruned only inside that nested deployment directory. The parent project directory is not used as a general deletion target.

VS Code SFTP `uploadOnSave` may be used as a fast local development convenience, but it is not the canonical deployment/source-of-truth mechanism and does not replace Git or the full deploy/prune workflow.

## 11. Git and CI Workflow

- GitHub is canonical for both repositories.
- Use `main` plus focused feature branches; no separate `develop` branch is required.
- Significant work should use a focused feature branch and pull request.
- Use clear commit messages such as `feat:`, `fix:`, `perf:`, `docs:`, and similar intent-oriented prefixes.
- Never commit passwords, API keys, SFTP credentials, production-only paths, or other secrets.

The Baltimore repository GitHub Actions workflow validates the production theme build on pull requests to `main`, pushes to `main`, and manual workflow runs using Node.js 22, `npm ci`, and `npm run bundle`.

GitHub Actions validates the build but does not deploy the site.

## 12. Environment and Launch Model

There is one WordPress site environment for this project. The current staging installation will become production at launch rather than deploying the theme/content into a separate production WordPress instance.

This makes launch primarily a domain/DNS/environment transition plus final QA rather than a second WordPress migration.

Before launch, confirm backups/restore capability, remove staging-only indexing restrictions, verify the final domain/SSL configuration, and run production smoke tests.

## 13. Architectural Decision Log

This section records decisions that materially affect implementation. Add or supersede entries when major decisions change instead of relying on memory.

| Date | Status | Decision |
| --- | --- | --- |
| 2026-08 | Accepted | Figma is the visual source of truth; the Figma Make React project is not production architecture. |
| 2026-08 | Accepted | The production site uses a classic PHP WordPress theme, not Full Site Editing/block templates. |
| 2026-08 | Accepted | Tailwind/PostCSS is the theme styling/build layer; do not introduce a competing frontend framework without a concrete requirement. |
| 2026-08 | Superseded | Reusable Gutenberg/ACF blocks live in the separate `dpi-blocks` plugin rather than the Baltimore theme. |
| 2026-11-18 | Accepted | Five blocks (`dpi/hero`, `dpi/five-pillars`, `dpi/feature-banner`, `dpi/community-slider`, `dpi/stats`) now live in the Baltimore theme, retaining their identifiers and ACF field keys. Other plugin modules remain separate. |
| 2026-08 | Accepted | Baltimore-specific ACF Local JSON belongs in the theme; reusable plugin fields belong in `dpi-blocks`. |
| 2026-08 | Accepted | Ministry, Staff, and Office are canonical CPTs supplied through opt-in reusable DPI Blocks content modules, with Baltimore-specific extensions where necessary. |
| 2026-08 | Accepted | News remains WordPress Posts; Campaigns remain Pages by default; Resources are not a CPT by default. |
| 2026-08 | Conditional | Event gets a custom CPT only if this project owns event management. |
| 2026-08 | Conditional | A shared Media CPT is created only if Podcasts/Videos/Press/Letters/Homilies genuinely share a data model and lifecycle. |
| 2026-08 | Accepted | Parish, School, and Job directory data/business logic remain owned by the existing GIS plugin and its custom database tables. |
| 2026-08 | Accepted | Gravity Forms owns form processing; the theme owns form presentation. |
| 2026-08 | Accepted | Normal WordPress Media Library and native WordPress search are the baseline. |
| 2026-08 | Accepted | Server caching is owned by NGINX/AccelerateWP rather than theme code. |
| 2026-08 | Accepted | GitHub uses `main` plus focused feature branches; GitHub Actions validates builds while local SFTP handles deployment. |
| 2026-08 | Accepted | The staging WordPress installation becomes production at launch; there is no separate production WordPress instance in the baseline plan. |
| 2026-08 | Accepted | ACF global options are limited to genuine global settings such as contact information, office hours, social links, header images, and footer settings. Existing specialized plugins retain ownership of their own global settings, including alerts. |

## 14. Maintaining This Document

Update this file whenever a change affects one or more of the following:

- repository ownership boundaries;
- CPT/taxonomy ownership;
- important content relationships;
- block architecture or naming conventions;
- GIS/plugin integration boundaries;
- build/deployment behavior;
- environment/launch strategy;
- major platform dependencies;
- a previously accepted architectural decision.

Small implementation details do not need an ADR entry. Decisions that would surprise a future developer, change where code/data belongs, or make a later refactor expensive do.
