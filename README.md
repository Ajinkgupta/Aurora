# Aurora — a modern theme for Question2Answer

Aurora is a premium, app-style responsive theme for [Question2Answer](https://www.question2answer.org/).
It replaces Q2A's classic top-bar forum layout with a left **sidebar + content + right-rail** shell on
desktop and a focused, single-column experience with a floating action button on mobile. It ships with
a built-in **light/dark mode**, glassmorphic cards, gradient accents and icon-driven navigation.

![version](https://img.shields.io/badge/version-1.0-2563eb) ![Q2A](https://img.shields.io/badge/Q2A-%E2%89%A5%201.7-3b82f6) ![license](https://img.shields.io/badge/license-GPLv2-555)

---

## Project description

Most Q2A themes still look like 2000s-era message boards. Aurora is a ground-up reskin that gives a
Q2A site the feel of a modern web app — closer to a Linear/Notion-style dashboard than a traditional
forum — without touching the Q2A core. Everything is implemented as a standard drop-in theme: a PHP
class that overrides the base theme's layout hooks, one stylesheet, and a small progressive-enhancement
script. No plugins, no core patches, no build step.

The goal is a clean, content-first reading experience: a persistent navigation sidebar, calm neutral
surfaces with the brand colour used sparingly as an accent, generous spacing, and tap-friendly controls
that collapse gracefully down to a phone.

## Features

- **Light & dark mode** — auto-detects the OS preference, remembers the user's choice in
  `localStorage`, and applies the scheme before first paint so there is no flash. Toggle lives in the
  top bar and syncs across open tabs.
- **App-shell layout** — fixed left sidebar (brand, "Ask Question" CTA, icon nav, user chip), a sticky
  top bar (menu toggle, search, theme switch, user nav), the main content column, and a right rail for
  widgets/side panel.
- **Mobile drawer + FAB** — on small screens the sidebar becomes a slide-in drawer (hamburger + scrim,
  closes on Escape/tap/nav-click) and a floating **+** action button gives one-tap access to "Ask".
- **Glassmorphic cards** — question list, post view, answers, comments and widgets are rendered as
  rounded, blurred glass cards with soft shadows.
- **Icon-driven navigation** — main/user nav items are mapped to [Remix Icon](https://remixicon.com/)
  glyphs; voting becomes thumbs up/down, "select answer" becomes a check, favourite becomes a bookmark.
- **Collapsed post actions** — per-post edit/flag/comment actions are tucked behind a `⋮` ("more")
  menu to keep cards uncluttered.
- **Consolidated stats** — votes, answer count and view count are grouped into a single stats footer on
  each card.
- **Typography** — [Plus Jakarta Sans](https://fonts.google.com/specimen/Plus+Jakarta+Sans) via Google
  Fonts, with system-font fallbacks.
- **Design tokens** — all colour, radius, spacing, blur and shadow values are CSS custom properties, so
  re-skinning is a matter of editing variables.

## Requirements

- Question2Answer **1.7** or newer.
- Outbound access to two CDNs at render time: Google Fonts (Plus Jakarta Sans) and jsDelivr
  (Remix Icon 4.5.0). Both can be self-hosted if your site must avoid third-party requests — see
  [Customising](#customising).

## Installation

1. Copy the `Aurora` folder into your Q2A site's `qa-theme/` directory:

   ```
   qa-theme/Aurora/
   ├── metadata.json
   ├── qa-theme.php
   ├── qa-styles.css
   └── js/
       └── aurora.js
   ```

2. In Q2A, go to **Admin → General → Site theme** and select **Aurora**.
3. (Optional) Upload your logo under **Admin → General**; Aurora places it on a light "logo chip" so a
   dark/colour logo stays visible against the sidebar.

The folder name (`Aurora`) must match the theme's registered name.

## File overview

| File | Purpose |
| --- | --- |
| `metadata.json` | Theme manifest (name, description, version, author, license, min Q2A version). |
| `qa-theme.php` | `qa_html_theme` class extending `qa_html_theme_base`; builds the app shell and overrides nav, voting, buttons, stats and search rendering. |
| `qa-styles.css` | All styling, organised into 14 sections (design tokens → base → app shell → cards → voting → buttons → tags → forms → post view → widgets → pagination → footer → mobile nav → responsive). |
| `js/aurora.js` | Progressive enhancement: dark-mode toggle, mobile drawer, sticky-header shadow on scroll, and the `⋮` action menus. Loaded with `defer`. |

Both `qa-styles.css` and `js/aurora.js` are cache-busted by file modification time during development
(see `css_name()` and `head_script()` in `qa-theme.php`).

## Customising

- **Colours / radius / shadows** — edit the `:root` and `[data-theme="dark"]` token blocks at the top
  of `qa-styles.css`. The accent (`--accent`, `--accent-grad`) and the surface/line/text tokens drive
  almost everything.
- **Theme colour for mobile browsers** — `head_metas()` sets `<meta name="theme-color">` (`#2563eb`).
- **Navigation icons** — extend the `$nav_icons` map in `qa-theme.php` to assign Remix Icon glyphs to
  additional nav keys; custom links fall back to an external-link icon.
- **Self-hosting fonts/icons** — replace the Google Fonts and Remix Icon URLs in `head_css()` (and the
  `$remixicon_css` property) with local copies if you don't want CDN requests.

> Note: `head_custom()` injects a small block of `!important` overrides specifically to neutralise
> leftover CSS from a previously active "Donut" theme that a production database injected ahead of
> Aurora's stylesheet. If you install Aurora on a clean site you can safely trim that block.

## License

Released under the **GNU General Public License v2 (or later)**, the same license as Question2Answer.

## Credits

- Built on the Question2Answer base theme (`qa_html_theme_base`).
- Icons by [Remix Icon](https://remixicon.com/). Typeface: [Plus Jakarta Sans](https://fonts.google.com/specimen/Plus+Jakarta+Sans).
- Author: Aurora — <https://thevibe.engineer>
