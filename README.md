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
| Custom Gutenberg/ACF blocks | **Theme-owned:** `dpi/hero`, `dpi/five-pillars`, `dpi/feature-banner`, `dpi/community-slider`, `dpi/stats`, plus `balt/feature-grid` (Navigate), `balt/image-cta-grid` (Image CTA Grid), `balt/media-center` (Media Center), and `balt/image-text-banner` (Image & Text Banner). Other `dpi/*` blocks remain in the plugin. |
| Media Center | New tabbed ACF block and seven media CPTs with separate hierarchical category taxonomies; visual QA and content entry pending |
| GIS | External existing plugin; integration and configuration later |
| Staff / ministries | No theme CPT implementation; planned as reusable plugin modules |

This table is based on code in this repository, **not a live WordPress/site test**.

The **Hero** (`dpi/hero`) now follows the approved Figma homepage design using Tailwind utilities: a fixed 865px height, cover-cropped video or image slider, 45% media overlay and top gradient, centered white display copy, and reusable `btn btn-outline` / `btn btn-gold` utilities. The other four theme-owned blocks remain unstyled pending Figma implementation. Slick retains its vendor CSS; WordPress still generates its standard block/image classes.

### Navigate / Feature Grid block

The `balt/feature-grid` block is registered from `theme/blocks/feature-grid/`, with its ACF field group in `theme/acf-json/group_balt_feature_grid.json`. Fields: eyebrow, heading, subheading, and a 1–6 item repeater (Font Awesome icon, heading, description, link). The entire card links to the selected destination. Responsive columns adjust to the number of cards (1: centered single; 2: two; 3: three; 4: 2×2; 5: 3+2; 6: 3×2).

**Dependency:** Install and activate the **Advanced Custom Fields: Font Awesome Field** extension alongside ACF Pro. Icon field type: `font-awesome` with `save_format: class`. The extension's `enqueue_fa` option is enabled in the field, so it supplies the icon assets on pages using the block. Without that extension, the icon picker is unavailable.

### Image CTA Grid

The `balt/image-cta-grid` block can be used for any set of four linked image cards. Its current visual layout follows Figma node `320:140`. Its ACF group `group_balt_image_cta_grid` provides an eyebrow, heading, description and CTA link for the centered white/gold panel, plus an image-card repeater with **min 4 / max 4**. Each card stores its background image (Media Library), a short label and a link. The badge colors follow the four design positions (red, gold, blue, slate).

The layout uses Tailwind only: four 460px cards in one row at wide breakpoints, responsive two- and one-column layouts below that, and an 80px overlap of the central panel at desktop widths. ACF currently enforces exactly four items; the renderer already accommodates different counts (one, two or three cards remain centered, and five or more wrap across additional rows). Future requests to change the number of cards only require adjusting the repeater limits. Badge colors repeat after the fourth card. No baked-in Figma stock imagery is shipped: the editor supplies the four images.



### Media Center

The **Media Center** block (\`balt/media-center\`) displays the latest **three published entries per tab**. Editors can configure and reorder **1–7 tabs** using an ACF repeater. Each tab has a searchable **Content Source** dropdown with all available public post types (including standard Posts and other registered CPTs) and their hierarchical categories; an optional label overrides the automatic tab name. For an entire post type, its View All button leads to the post-type archive. For an individual category, it leads to that category archive. Tabs use the same Figma layout regardless of the selected source. Existing blocks with no tab configuration continue to display the original seven media types until editors choose their own tabs. Posts supply their own featured images, titles and publication dates; no hardcoded Figma imagery or Archbishop message is included.

Registered in \`theme/inc/media-post-types.php\`:

| Post type | Archive | Separate category taxonomy |
| --- | --- | --- |
| \`balt_campaign\` | \`/campaigns/\` | \`balt_campaign_category\` |
| \`balt_event\` | \`/events/\` | \`balt_event_category\` |
| \`balt_podcast\` | \`/podcasts/\` | \`balt_podcast_category\` |
| \`balt_video\` | \`/videos/\` | \`balt_video_category\` |
| \`balt_press\` | \`/press/\` | \`balt_press_category\` |
| \`balt_letter\` | \`/letters/\` | \`balt_letter_category\` |
| \`balt_homily\` | \`/homilies/\` | \`balt_homily_category\` |

These start with WordPress standard title, editor, excerpt, featured image, and revisions. Each taxonomy is a separate hierarchical category system, enabled in the block editor. Event dates, episode information, video URLs, audio fields, and other specialized metadata are **not** assumed yet. On an existing WordPress site, visit **Settings → Permalinks → Save Changes** after deployment so the new archive URLs work.

### Image & Text Banner

The reusable \`balt/image-text-banner\` block follows the Figma split-banner layout (\`320:278\`) without any Archbishop-specific hardcoded content. ACF fields: eyebrow, heading, subheading, optional quote, required image, and a 0–2 button repeater containing ACF links. The first CTA uses shared \`btn-red\`, and the second uses shared \`btn-outline-subtle\` (both combined with \`btn\`).

At 1024px and above the content sits on the left and the photo fills the right half of a 1504px-wide container, with an image-side fade and bottom fade. Below 1024px the image moves above the dark text panel and fades into it. Below 640px the CTA links stack. The image is selected through WordPress's Media Library, not copied from the Figma screenshot.

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
