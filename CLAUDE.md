# CLAUDE.md — Maverick Frame Studio theme

Read this file at the start of every new session. It tells you what this
project is, what's been decided, and what's been learned the hard way.

Keep it short. Edit it, don't append. Move historical detail into git log /
commit messages, not here.

---

## 1. Two-cycle rule — the most important thing in this file

Changes in this project fall into **two completely separate cycles** that
do not share a deploy path. Mixing them is the most common way to lose work.

### Cycle 1 — CODE (files in the theme)
Anything in this folder: `.php`, `.scss`, `.js`, `.css`, `.json` (incl.
`acf-json/`), images in `public/img/` or `build/img/`.

```
LocalWP (mfs-local.local) → Claude Code edits files locally
        → git commit + git push
        → GitHub
        → GitHub Action / SSH deploy → Staging
        → manual Rocket "push to live" → Production
```

### Cycle 2 — CONTENT (rows in the WP database)
Anything edited through WP-admin: post bodies, **values** of ACF/SCF fields,
media library, menus, taxonomies, options, SEO meta.

```
Cowork + Vibe AI MCP (WPVibe)  → Staging or Production directly
```

No git. No LocalWP roundtrip. The local DB is just a snapshot frozen at
migration time and intentionally drifts from prod.

### Mixed case — new ACF field + values
- Field definition (the JSON in `acf-json/`) → Cycle 1.
- Values you fill into that field on a specific post → Cycle 2.

---

## 2. Project at a glance

- WordPress theme for `maverickframe.com` — a 3D rendering / CGI studio.
- Custom theme, no parent. Vite-built SCSS + JS bundle.
- Two design generations coexist in the same theme:
  - **Legacy** templates and SCSS — older landing pages.
  - **New design** — selected via `isNewDesign()` in `functions.php`.
- New design covers: front-page, blog, success-stories, team, gallery,
  service templates, presentation-design. Everything else still uses legacy.

---

## 3. Environments

| Env | URL | Role | Who/what writes here |
|---|---|---|---|
| Local | `http://mfs-local.local/` | Dev preview | Claude Code via local files |
| Local Live Link | `https://bouncy-subway.localsite.io/` (rotates) | Share-with-team preview | (read-only for outsiders) |
| Staging | `https://e2i1j0xyf5-staging.onrocket.site/` | QA before prod | Auto-deploy from GitHub `main` (target) + Vibe AI MCP for content |
| Production | `https://maverickframe.com/` | Live | Rocket "push to live" from staging + Vibe AI MCP for content |

The Vibe AI MCP (a.k.a. WPVibe) is connected to **both** staging and prod.
That means a careless command can hit prod directly. Default rule: always
work on staging unless you're explicitly told to touch production.

---

## 4. Layout

```
maverickframe/
├── front-page.php, index.php, page.php, single-*.php   # WP entry points
├── header.php, footer.php, functions.php
├── style.css                       # WP theme header only — real CSS is built
├── inc.vite.php                    # Vite asset enqueue logic
├── components/
│   ├── (legacy partials)
│   └── new-design/                 # Everything for the new design lives here
│       ├── common/                 # header, footer, modals, breadcrumbs
│       ├── blog/                   # blog-specific partials (hero-post, sidebar-cta, articles-item, faq, …)
│       ├── services/, team/, …
├── templates/                      # page-template-* used by `templates/template-*.php`
├── src/
│   ├── scss/
│   │   ├── main.scss               # legacy entry
│   │   ├── new.scss                # new-design entry (this is the one prod loads on homepage)
│   │   ├── blocks.scss             # gutenberg block styles entry
│   │   └── new/                    # all new-design SCSS, BEM organised
│   └── js/bundle.js                # main JS entry
├── build/                          # Vite output, gitignored
│   ├── assets/                     # compiled css/js with content-hashed names
│   ├── fonts/                      # inter-tight-v9-*.woff2
│   └── img/                        # icons, logos, page-specific imagery
├── acf-json/                       # ACF field group definitions (CODE, not content)
├── blog-v1-overrides.css           # see §8 — current blog redesign lives here
├── blog-v1-enhancements.js         # see §8 — current blog redesign lives here
├── .github/workflows/deploy.yml    # legacy SSH-deploy workflow (target branch: master — see §9)
└── package.json, vite.config.js    # build config
```

---

## 5. Tech stack

- **WordPress** 6.x
- **Vite 6** for asset build (`npm start` dev, `npm run build` prod)
- **Sass** (dart-sass)
- **ACF/SCF Pro** with JSON sync to `acf-json/`
- **Rank Math** for SEO
- **WP Rocket** for caching — note that **Used CSS** mode strips inline
  `<style>` tags, so all CSS must be enqueued, not inlined.
- Hosting: **Rocket.net** (managed WP, Cloudflare Enterprise edge)
- Local dev: **LocalWP** (formerly Local by Flywheel)

---

## 6. Conventions

- **Class names: BEM.** `block__element--modifier`. New design follows this
  strictly; legacy is mixed.
- **Page-scope selector: `html.single-blog`, not `body.single-blog`.**
  The theme sets these classes on `<html>` in `header.php` from
  `get_page_template_slug()` and `is_singular()`. `body` only has `id="top"`.
  Targeting `body.X` will fail silently — this has bitten us before.
- **Component first, override later.** When a partial under
  `components/new-design/blog/` already exists, modify the partial in place
  rather than adding more CSS overrides on top.
- **ACF fields are added in WP-admin on LOCAL only.** Saving the field
  group writes JSON to `acf-json/`. Then commit the JSON. Don't edit ACF
  groups directly on staging or prod — the JSON desyncs and the next deploy
  wipes the change.

---

## 7. Gotchas we've already paid for — don't repeat

- **Tables (`wp-block-table`):** the theme draws cell separators with
  `::before` / `::after` pseudo-elements that have a `background-color`,
  *not* borders. Setting `border: 0 !important` does nothing. To kill the
  lines, set `content: none !important` on the pseudo-elements.
  Source: `src/scss/new/pages/blog-page.scss` ~lines 199-260.

- **Link triple-underline:** the theme has a global `a { text-decoration:
  underline }`. Adding `border-bottom` on top creates a second line. The
  arrow `<span>` inherits and gets a third. Pick **one** of the two and
  kill the rest on `*` inside the link.

- **WP Rocket "Used CSS" strips inline `<style>`.** Anything you put in
  `wp_head` as an inline tag will disappear on cached pages. Enqueue real
  files (we do this with `blog-v1-overrides.css`).

- **`File unchanged since last read` from WPVibe.** The MCP caches reads
  per conversation. If you need to re-read, use a slightly different path
  string (e.g. `./././foo.php`). Path-traversal-style tricks (`..`) are
  rejected. Each unique string is treated as a fresh read.

- **Cowork bash cannot delete files in the LocalWP mount.** Git from the
  sandbox fails on `index.lock` cleanup. Real git commands must be run by
  the user in their own Terminal — Cowork can read/write/edit files, but
  not orchestrate git plumbing on the mounted path.

- **`build/` is gitignored.** Don't commit compiled assets. They're
  regenerated by `npm run build` (locally) or by the deploy hook on the
  server (see `.github/workflows/deploy.yml`).

- **CSS Grid + `grid-row: 1 / span N`:** if the spanning element is taller
  than the auto-flowed siblings, grid happily resizes the implicit rows to
  fill the span. You get huge unwanted vertical gaps between siblings.
  Fix: drop the grid for that layout, use `position: relative` + an
  absolutely positioned prefix. (V1 in-article CTA was the example.)

---

## 8. Active project — blog redesign (single-blog template)

We've been redesigning `single-blog.php`. Most changes live in two files:

- `blog-v1-overrides.css` — enqueued only on `is_singular('blog')` from
  `functions.php` (hook: `blog_v1_overrides_enqueue`).
- `blog-v1-enhancements.js` — same hook.

These files contain numbered "ITERATION" comment blocks. Latest iteration's
status:

- Dark editorial hero, breadcrumbs moved to bottom (variant A: text strip
  `Home · Blog · Title`).
- TOC sidebar with author-mini at top, reading-status, scroll-spied
  `Contents`, "Was this helpful?" feedback at bottom. Border colour
  `#0A0A0A` (matches right-CTA sidebar dark).
- Right sidebar CTA is a **4-stage rotator** driven by scroll progress
  (NEW HERE → RESOURCE → SOCIAL PROOF → TALK TO US). One `<aside>`,
  CSS grid `1/1` stack, JS swaps `is-active`.
- **5 in-article CTAs** (visually distinct) injected by JS at evenly-
  spaced H2 breaks:
  - v1 numbered `01` editorial
  - v2 left-border editor's note
  - v3 centred italic pull-quote with em-dash
  - v4 bordered card (no fill, hover → blue border + soft shadow)
  - v5 hr-sandwich, centred
  Each has a **pulsing green square dot** before the eyebrow (Hatamex-style
  "live" indicator).
- FAQ compacted: 11px brand-blue eyebrow, single column, `+` indicator
  rotates to `×` on open, hard-collapse via `max-height: 0; opacity: 0`.
- Read Next: 3-column with vertical dividers, no images, no author block.
- Footer "Let's create visuals that sell" and bottom "View full profile"
  block hidden on `html.single-blog`.

**Important:** these changes currently exist **only on staging**, not in
this local copy. If you need to mirror them locally for the team, pull
`blog-v1-overrides.css` and `blog-v1-enhancements.js` from staging
(via WPVibe site `https://e2i1j0xyf5-staging.onrocket.site`).

---

## 9. Deploy mechanics

- **`.github/workflows/deploy.yml`** exists from a previous dev. It deploys
  on push to **`master`** via SSH (uses GitHub Secrets: SSH_PRIVATE_KEY,
  HOST, USER, APP_PATH). The current local repo is on **`main`**. If
  reconnecting to an existing GitHub remote, either:
  - rename branch to `master`, or
  - update the workflow trigger to `branches: [main]`.
  Right now there's no remote at all — fresh `git init`, single commit.

- **Rocket.net push-to-live** is the final step. Always click it manually,
  after staging QA.

- **Vibe AI MCP** (WPVibe) has tools `edit_file`, `write_file`,
  `publish_draft_theme`. These bypass git entirely and write to the WP
  install. Use only for content (REST API stuff) once the proper code
  workflow is in place; **don't** use the file-writing tools for theme
  code anymore — that's what git is for now.

---

## 10. Personnel

- **Owner:** Dima Kuzmenko (kuzmenkodmitry@gmail.com).
- Other devs may have legacy GitHub repo access — ask before assuming
  there's no existing remote (see §9 about the workflow file).

---

## 11. When in doubt

- Code change? → `git status` first.
- Content change? → ask "staging or prod?" before writing.
- "Why is my change invisible?" → check that you targeted `html.single-X`
  not `body.single-X`, and that WP Rocket's Used CSS isn't stripping it.
- "WPVibe says File unchanged" → vary the path string.
- "Local git won't commit" → run it in your real Terminal, not Cowork bash.

---

_Last updated: 2026-06-02. Edit in place when something changes — don't
append "as of 2026-08" updates, just rewrite the relevant section._
