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
        → git commit + git push origin main
        → GitHub (Maverickframe/maverick)
        → GitHub Action (.github/workflows/deploy-staging.yml)
        → Rocket Staging (e2i1j0xyf5-staging.onrocket.site)
        → manual Rocket "Publish to Production" button
        → Production (maverickframe.com)
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

| Env | URL | Files location | Who writes here |
|---|---|---|---|
| **Local** | `http://mfs-local.local/` | `~/Local Sites/mfs-local/app/public/wp-content/themes/maverickframe/` | Cowork / Claude Code on local files |
| **GitHub** | `github.com/Maverickframe/maverick` | (cloud, branch `main`) | `git push` from local |
| **Staging** | `https://e2i1j0xyf5-staging.onrocket.site/` | Rocket server, path `/e2i1j0xyf5-staging.onrocket.site/wp-content/themes/maverickframe/` | GitHub Action (auto on push) + Vibe AI MCP for content |
| **Production** | `https://maverickframe.com/` | Rocket server, path `/wp-content/themes/maverickframe/` (root) | Rocket "Publish to Production" button + Vibe AI MCP for content |

**LocalWP** is the local dev environment (formerly Local by Flywheel). Site
name is `mfs-local`. Live Link is **off by default** — when on, WordPress
rewrites siteurl to `bouncy-subway.localsite.io` and `http://mfs-local.local/`
starts redirecting. Toggle off in LocalWP UI; if `siteurl` got stuck, fix in
Site Shell: `wp option update siteurl http://mfs-local.local && wp option update home http://mfs-local.local`.

The **Vibe AI MCP** (WPVibe) is connected to both staging and prod. Default
rule: content goes through it; code does not.

---

## 4. Layout

```
maverickframe/
├── front-page.php, index.php, page.php, single-*.php   # WP entry points
├── header.php, footer.php, functions.php
├── style.css                       # WP theme header only — real CSS is built
├── inc.vite.php                    # Vite asset enqueue logic
├── CLAUDE.md                       # this file
├── components/
│   ├── (legacy partials)
│   └── new-design/                 # everything for the new design lives here
│       ├── common/                 # header, footer, modals, breadcrumbs
│       ├── blog/                   # blog-specific partials
│       ├── success-stories/        # case-page partials
│       ├── services/, team/, …
├── components/blocks/              # ACF Gutenberg blocks (used in success-stories content)
├── templates/                      # page-template-* used by `templates/template-*.php`
├── src/
│   ├── scss/                       # all SCSS sources (main.scss, new.scss, blocks.scss + new/*)
│   └── js/bundle.js                # main JS entry
├── build/                          # Vite output, gitignored
│   ├── assets/                     # compiled css/js with content-hashed names
│   ├── fonts/                      # inter-tight-v9-*.woff2
│   └── img/                        # icons, logos, page-specific imagery
├── acf-json/                       # ACF field group definitions (CODE, not content)
├── blog-v1-overrides.css           # blog single-post override (see §8)
├── blog-v1-enhancements.js         # blog single-post enhancements (see §8)
├── .github/workflows/deploy-staging.yml  # CI: main → staging via FTPS
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
  files.

- **WP Rocket cache lives between deploys.** After a code deploy that
  affects rendered HTML, in LocalWP Shell run `wp rocket clean --confirm`,
  `wp cache flush`, `wp rewrite flush --hard`. On staging/prod use the
  "Clear Cache" button in Rocket dashboard.

- **`File unchanged since last read` from WPVibe.** The MCP caches reads
  per conversation. If you need to re-read, use a slightly different path
  string (e.g. `./././foo.php`). Path-traversal-style tricks (`..`) are
  rejected.

- **Cowork bash cannot delete files in the LocalWP mount.** Git from the
  sandbox fails on `index.lock` cleanup. Real git commands must be run in
  the user's own Terminal — Cowork can read/write/edit files, but not
  orchestrate git plumbing on the mounted path.

- **`build/` is gitignored.** Don't commit compiled assets. They're
  regenerated by `npm run build` (locally) or by the GitHub Action on
  every push to `main`.

- **Vite needs env vars at build time.** `vite.config.js` reads
  `VITE_ENTRY_POINT`, `VITE_STYLES`, `VITE_STYLES_NEW`,
  `VITE_STYLES_BLOCKS` from `.env` locally. In CI (GitHub Action) they are
  hard-coded in `deploy-staging.yml`. If you change paths in
  `vite.config.js`, update **both** `.env` and the Action's env block.

- **HSTS cache in browser after Live Link.** If LocalWP Live Link was on,
  the browser remembers `mfs-local.local` should redirect to the tunnel.
  Clear via `chrome://net-internals/#hsts` → Delete `mfs-local.local`. Or
  use Incognito.

- **CSS Grid + `grid-row: 1 / span N`:** if the spanning element is taller
  than the auto-flowed siblings, grid resizes the implicit rows to fill
  the span — you get huge vertical gaps between siblings. Fix: drop the
  grid, use `position: relative` + an absolutely positioned prefix. (V1
  in-article CTA in blog redesign was the example.)

---

## 8. Blog redesign — single-blog template

Older project. `single-blog.php` was extensively redesigned with two
external files:

- `blog-v1-overrides.css` — enqueued only on `is_singular('blog')` from
  `functions.php` (hook: `blog_v1_overrides_enqueue`).
- `blog-v1-enhancements.js` — same hook.

Files contain numbered "ITERATION" comment blocks. Final state includes:
dark editorial hero, breadcrumbs at bottom (text strip), TOC sidebar with
author-mini + reading-status + scroll-spied Contents + "Was this helpful?"
feedback, sidebar CTA 4-stage scroll-rotator (NEW HERE → RESOURCE → SOCIAL
PROOF → TALK TO US), 5 in-article CTAs (numbered, left-border, pull-quote,
bordered card, hr-sandwich) each with pulsing green square live-indicator
before eyebrow, compact FAQ with brand-blue +/× indicator, Read Next as
3-column with vertical dividers.

These files are in production and were pulled into local via the latest
prod-snapshot SFTP sync. Verify on http://mfs-local.local/blog/[any-post]/
that they're loading.

---

## 9. Deploy mechanics — how code reaches each environment

### Local → GitHub
Standard git. `main` is the only long-lived branch. Feature branches OK,
merge via PR (or fast-forward for solo work). Auth via `gh auth login`
(GitHub CLI) or PAT.

### GitHub → Staging (automatic via Action)
`.github/workflows/deploy-staging.yml` runs on every push to `main`:

1. Checkout repo
2. Setup Node.js 20
3. `npm ci` (clean install)
4. `npm run build` (Vite compiles SCSS/JS into `build/`)
5. FTPS upload to staging via `SamKirkland/FTP-Deploy-Action@v4.3.5`

**Required secrets** in GitHub repo Settings → Secrets and variables →
Actions:

- `STAGING_FTP_HOST` = `65.181.120.39`
- `STAGING_FTP_USER` = `dim-staging@e2i1j0xyf5-staging.onrocket.site`
- `STAGING_FTP_PASSWORD` = (set when creating the FTP account in Rocket
  dashboard while in Staging environment context)

**Deploy target path** on Rocket server:
`/e2i1j0xyf5-staging.onrocket.site/wp-content/themes/maverickframe/`

**How Rocket separates environments via SFTP**: FTP accounts created
while the Rocket dashboard is in Staging context are namespaced to a
separate filesystem prefix (`e2i1j0xyf5-staging.onrocket.site/`). FTP
accounts created in Production context land at the server root (which IS
production's `public_html/`). The `dim-staging` FTP user sees both
environments from the SFTP root, but the deploy path explicitly targets
the staging subdirectory.

State file `.ftp-deploy-state.json` lives on the server — Action uses it
to incremental-deploy only changed files after first full push.

Excludes: `.git/`, `node_modules/`, `.env*`, `.github/`, `CLAUDE.md`,
`README.md`, `.DS_Store`. Everything else gets deployed including
`build/` (regenerated fresh in CI).

### Staging → Production
**Manual** via Rocket dashboard → switch site env to **Staging** → click
**"Publish to Production"** button. There is no automated path from
staging to prod; this is intentional gate-keeping.

### Vibe AI MCP (WPVibe) — for content only
WPVibe has `edit_file`, `write_file`, `publish_draft_theme` tools. Since
this project moved to git-based code deploys, **do not use these tools
for theme code** — only for content via REST API (creating posts, ACF
values, media uploads).

### Legacy `deploy.yml` is gone
A previous developer had `.github/workflows/deploy.yml` doing SSH push to
a non-Rocket server. It's been removed. If you see it reappear in some
old branch, delete it.

---

## 10. Personnel

- **Owner / lead:** Dima Kuzmenko (kuzmenkodmitry@gmail.com). Works mainly
  through Cowork + Vibe AI MCP, doesn't write code by hand.
- **DeLaPablo** — marketer-hybrid. 90% content work (posts, ACF values,
  media). 10% light technical (Image schema, CSS fixes). Uses Antigravity
  IDE + Claude Code. GitHub access: add as collaborator with Write role
  on `Maverickframe/maverick` when needed.
- **Лёшин** — previous developer. No longer on the project. Old GitHub
  repo (separate from `Maverickframe/maverick`) may still exist but is
  archived from our side.

Roles map to cycles: marketers stay on Cycle 2 (content); only Dima and
contracted developers touch Cycle 1 (code).

---

## 11. When in doubt

- **Code change?** → `git status` first. Edit file, commit, push, watch
  the Action in `github.com/Maverickframe/maverick/actions`.
- **Content change?** → ask "staging or prod?" before writing via WPVibe.
- **"Why is my change invisible?"** → check that you targeted
  `html.single-X` not `body.single-X`; clear WP Rocket cache; hard-refresh
  browser (Cmd+Shift+R).
- **"WPVibe says File unchanged"** → vary the path string.
- **"Local git won't commit from Cowork bash"** → run it in real Terminal.
- **"GitHub Action failed at npm run build"** → check `vite.config.js`
  for new env vars not set in `deploy-staging.yml`.
- **"GitHub Action failed at FTPS deploy"** → secrets wrong, or
  `dim-staging` FTP password rotated. Re-set in Rocket dashboard and
  update `STAGING_FTP_PASSWORD` secret in GitHub.
- **"Staging shows old version after deploy"** → clear Rocket CDN cache
  via dashboard, plus WP Rocket via WP-admin or `wp rocket clean
  --confirm`.

---

_Last updated: 2026-06-02. Edit in place when something changes — don't
append "as of 2026-08" updates, just rewrite the relevant section._
