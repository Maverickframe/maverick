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
        → branch fix/<task> → git push → PR to main
        → Dima merges + runs manual "Deploy theme to PRODUCTION" Action
        → Production (maverickframe.com) — deploy auto-purges caches
```

(Full process: `WORKFLOW.md`. Staging was decommissioned 2026-06-24.)

### Cycle 2 — CONTENT (rows in the WP database)
Anything edited through WP-admin: post bodies, **values** of ACF/SCF fields,
media library, menus, taxonomies, options, SEO meta.

```
Cowork + Vibe AI MCP (WPVibe)  → Production directly
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
  service templates, presentation-design, 404. Everything else still uses legacy.
- **CPT `portfolio` is DELETED (2026-07-12)** — 91 posts removed, URLs
  301'd in Rank Math. The live `/gallery/` runs on the SEPARATE CPT
  `gallery`. Native `post` type is hidden from admin — editorial content
  lives only in CPT `blog`.
- 404: "scene failed to render" concept, own `error.scss` bundle. The
  worldwide-rendering block uses `renderReveal.js` (canvas 2D tear-to-reveal,
  replaced the three.js particle sphere); it stays a static image while the
  ACF `img` value is the legacy dots-SVG — activate by swapping the value to a
  raster render (Cycle 2).

---

## 3. Environments

| Env | URL | Files location | Who writes here |
|---|---|---|---|
| **Local** | `http://mfs-local.local/` | `~/Local Sites/mfs-local/app/public/wp-content/themes/maverickframe/` | Cowork / Claude Code on local files |
| **GitHub** | `github.com/Maverickframe/maverick` | (cloud, branch `main`) | `git push` from local |
| **Production** | `https://maverickframe.com/` | Rocket server, path `/wp-content/themes/maverickframe/` (root) | manual "Deploy theme to PRODUCTION" GitHub Action + Vibe AI MCP for content |

**Staging is GONE** (decommissioned 2026-06-24, workflow deleted). Any
`e2i1j0xyf5-staging.onrocket.site` references in old docs/branches are dead.

**LocalWP** is the local dev environment (formerly Local by Flywheel). Site
name is `mfs-local`. Live Link is **off by default** — when on, WordPress
rewrites siteurl to `bouncy-subway.localsite.io` and `http://mfs-local.local/`
starts redirecting. Toggle off in LocalWP UI; if `siteurl` got stuck, fix in
Site Shell: `wp option update siteurl http://mfs-local.local && wp option update home http://mfs-local.local`.

The **Vibe AI MCP** (WPVibe) is connected to prod. Default rule: content
goes through it; code does not.

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
├── .github/workflows/deploy-production.yml  # manual Action: main → prod via FTPS + cache purge
└── package.json, vite.config.js    # build config
```

---

## 5. Tech stack

- **WordPress** 6.x
- **Vite 6** for asset build (`npm start` dev, `npm run build` prod)
- **Sass** (dart-sass)
- **ACF/SCF Pro** with JSON sync to `acf-json/`
- **Rank Math** for SEO — since 2026-07-10 the ONLY source of SEO meta:
  legacy ACF fields `title` (SEO Title), `no_index`, `meta_description`
  and the `document_title` filter are retired.
- **No JS animation/slider libraries** (since 2026-07-09): GSAP, Splide
  and three.js are removed from the runtime entirely. Sliders are CSS
  scroll-snap (`.mfs-snap`) or pure-CSS marquees (`.mfs-marquee`); reveals
  are vanilla IntersectionObserver + CSS. Don't reintroduce libraries.
- **Video: native `<video>` + hls.js** (`mfs-video.js`), NOT the Bunny
  iframe player (removed 2026-07-05). hls.js is a lazy chunk loaded on
  first play; Bunny manifests load cross-origin (CSP `blob:` allowed via
  `.htaccess` — LiteSpeed `Header set` beats PHP header overrides).
- **NO caching plugin — WP Rocket was removed COMPLETELY 2026-07-12**
  (plugin + advanced-cache.php dropin + options + tables). There is no
  "Clear Cache" button anywhere — don't look for one. Page cache always
  lived on the **Rocket.net edge (Cloudflare Enterprise) + host
  mu-plugin** (`cdn-cache-management`); WP Rocket never wrote origin
  files on prod. Rocket's useful features now live in the THEME, each
  with a kill-switch:
  - Defer all JS → `script_loader_tag` filter in functions.php
    (`?mfs_scriptdefer=0`)
  - Delay GTM + HubSpot until first interaction / 10s → `inc.delay.php`
    (`?mfs_delay=0`)
  - Front-page CSS → ATF/BTF split: `front-atf.scss` (blocking) +
    `front-btf.scss` (async) (`?mfs_split=0`). A new above-the-fold
    block goes INTO front-atf and OUT of front-btf.
  - Prefetch → WP 6.8 native Speculation Rules site-wide + tuning in
    `inc.prefetch.php` (grep HTML for `type="speculationrules"` before
    "adding" prefetch — it's already there)
  - Minification → Vite only (`/build/`); revisions cap → 5.
- **Web fonts are GONE (2026-07-12):** Inter Tight + Red Hat Display
  removed; site runs on the system font stack. Don't reintroduce fonts
  or preloads. Zero font-CLS by construction.
- **Plugins: 8 active, none loads front-end assets** (polylang-pro,
  secure-custom-fields, rank-math + pro, vibe-ai + mcp-abilities-rankmath,
  svg-support (temp), cdn-cache-management mu). **NEVER delete Polylang
  Pro** — it runtimes /es/ /de/ routing, hreflang, translation links.
  Backups = Rocket.net daily (~06:39 UTC), no backup plugin.
- **HubSpot tracking is self-hosted** in the theme (`wp_footer`, loader
  only) — the `leadin` plugin is DELETED, don't reinstall it. Forms are
  server-side; HubSpot "Collected Forms" must stay OFF (duplicates ours).
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

- **Cache purge rules (post-Rocket, 2026-07-12):**
  - Editing a post via editor / ACF `savePost` **auto-purges** (save-hook
    flushes origin + edge) — for normal content work touch nothing.
  - Direct SQL writes and THEME DEPLOYS fire no save-hook → need the
    two-layer flush: origin delete via Rocket file API FIRST, then
    `purge_everything` (`rocket-purge.py`; the deploy Action's purge step
    does the same — needs repo secrets `ROCKET_USER`/`ROCKET_PASS`).
    A bare `purge_everything` alone re-caches the stale origin for up to
    30 days (`s-maxage=2592000`).
  - Some generated layers (e.g. old CSS baked into critical/min files)
    survive everything except folder deletion via file API + purge +
    anonymous warm-up.

- **Verify live results ANONYMOUSLY on the canonical URL, no query.**
  A logged-in browser bypasses ALL cache and lies "fresh"; any `?query`
  bypasses too. Reliable trick from a logged-in tab:
  `fetch(url, {credentials:'omit'})` — real guest HTML + `cf-cache-status`
  header. (`web_fetch` caches per-URL — vary with a unique `?x=N`.)

- **`File unchanged since last read` from WPVibe.** The MCP caches reads
  per conversation. If you need to re-read, use a slightly different path
  string (e.g. `./././foo.php`). Path-traversal-style tricks (`..`) are
  rejected.

- **Cowork bash cannot delete files in the LocalWP mount.** Git from the
  sandbox fails on `index.lock` cleanup. Real git commands must be run in
  the user's own Terminal — Cowork can read/write/edit files, but not
  orchestrate git plumbing on the mounted path.

- **`build/` is gitignored.** Don't commit compiled assets. They're
  regenerated by `npm run build` (locally) or by the deploy Action.

- **Vite needs env vars at build time.** `vite.config.js` reads
  `VITE_ENTRY_POINT`, `VITE_STYLES`, `VITE_STYLES_NEW`,
  `VITE_STYLES_BLOCKS` from `.env` locally. In CI (GitHub Action) they are
  hard-coded in `deploy-production.yml`. If you change paths in
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

- **WP Rocket LazyLoad ignores `loading="eager"`** but skips images with
  `fetchpriority="high"`. Any image that must never be lazy (logo, hero
  slides) needs `data-no-lazy="1"` / class `skip-lazy` explicitly —
  `eager_attachment()` adds them. (Removing fetchpriority from the logo made
  Rocket lazyload it → the logo became the mobile LCP element.)

- **Splide is GONE (2026-07-09)** — so are GSAP and three.js, and with
  them the whole family of Splide gotchas (role="group" a11y patch,
  delayAutoScrollStart, clones seams). Sliders are `.mfs-snap` (CSS
  scroll-snap, `initSnap`) or `.mfs-marquee` (pure-CSS, track rendered
  twice, second pass aria-hidden). A dynamically injected `.mfs-snap`
  must be `initSnap`'d **synchronously right after innerHTML**, not in
  rAF (modal stats dots never built otherwise).

- **Hero marquee shows TAIL slides early:** the CSS marquee track is
  duplicated and one column runs backwards, so tail slides are visible
  within seconds. Eager-load first 4 AND last 2 slides per column in
  hero-front.php — eager-ing only the head leaves the real LCP slide lazy.

- **Code-split bundle = `type="module"` + Rocket JS-minify exclusion.**
  The entry has `import.meta`/dynamic `import()` (SyntaxError as a classic
  script) — `inc.vite.php` swaps the tag. WP Rocket → Excluded JavaScript
  Files must keep `/wp-content/themes/maverickframe/build/(.*).js`, else
  main.js is served from `/cache/min/…` and relative chunk URLs 404.
  In bundle.js, modules that rely on DOMContentLoaded (videoPlay,
  visualResultsGallery, sticky-cta) must stay statically imported — an
  async chunk can arrive after the event and never init.

- **Kill-switches (all theme mechanisms have one):** `?mfs_dequeue=0`
  (WP-core CSS dequeue on front/services/solutions — pages with real core
  Gutenberg blocks keep it), `?mfs_defer=0` (mega-menu moved to end of
  `<body>` for GEO), `?mfs_scriptdefer=0` (defer-all-JS), `?mfs_delay=0`
  (GTM/HubSpot delay), `?mfs_split=0` (front ATF/BTF CSS split).

- **WP 6.7 core auto-sizes is disabled** (`wp_img_tag_add_auto_sizes` →
  `__return_false`): it prepended `auto,` to sizes of every lazy image and
  made the hero marquee pull the full 818w candidate instead of 300/600w.
  Intermediate sizes 300w/600w exist so DPR1 fits containers.

- **ESM entry must be enqueued with version `null`** — a `?ver=` query on
  the module script caused the entry to load twice (every "Load more"
  click = 2 fetches).

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

> **The current process lives in `WORKFLOW.md`.** Short version: branch
> `fix/<task>` off fresh `main` per task → PR to `main` → Dima merges and
> runs the manual **"Deploy theme to PRODUCTION"** Action
> (`deploy-production.yml`: npm ci → vite build → FTPS to prod → cache
> auto-purge, see §7 T46). Batch small fixes into one branch instead of
> deploying one-liners. `multilang` is retired (rebase-duplicate commits →
> false PR conflicts) and was reset to main.
>
> **Staging and its pipeline were decommissioned 2026-06-24** —
> `deploy-staging.yml` is deleted, the staging site/FTP creds are dead.
> History (FTPS setup, reviewer gating): git log of this file.

### Local → GitHub
Standard git. `main` is the only long-lived branch. Feature branches OK,
merge via PR. Auth via `gh auth login` (GitHub CLI) or PAT.

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

## 11. SEO publishing checklist — Rank Math on every post

Whenever a new post (success-stories, blog, page) is created via WPVibe
or admin, the following Rank Math fields MUST be set. They are NOT
optional — a post without them is half-published.

### 11.1 SEO Title (`rank_math_title`)
- **≤ 60 characters / ≤ 580 px** — Rank Math snippet preview must show
  the green bar. 82 chars / 746 px is red — too long.
- Must include the **focus keyword** near the start.
- Must end with brand suffix: `| Maverick Frame Studio` (23 chars).
- Must differ from `H1` on the page (anti-cannibalisation — one phrase
  per post can't compete with itself).
- Example: `Private Padel Court CGI for Estates | Maverick Frame Studio`

### 11.2 Meta description (`rank_math_description`)
- **≤ 160 characters / ≤ 920 px** — green bar. 194 chars / 1168 px is red.
- Must include the focus keyword once.
- Sell the value prop, not the headline — pre-construction CGI, the
  geographic angle, the deliverable.
- Example: `Photorealistic private padel court CGI for a tropical villa
  estate — surface, landscape, and lighting validated before construction
  begins.`

### 11.3 Focus keyword (`rank_math_focus_keyword`)
- One long-tail phrase per post (no head-term cases — those go to service
  pages).
- Lowercase, matches the most-searched form of the keyphrase.
- Example: `private padel court CGI`

### 11.4 Slug
- Must contain the focus keyword.
- No stop-words (`a`, `the`, `for`) unless they're part of the keyphrase.
- **All URLs strictly lowercase** — uppercase creates duplicates
  (200 + canonical).
- Example: `private-padel-court-cgi`

### 11.4a Writing blog ACF fields (since 2026-07-12)
Blog ACF (hero, FAQ, schema, read_time) is written via plain REST:
one `POST /wp/v2/blog/{id}` with body `{"acf":{…}}` — no browser, no
nonce, no FormData. Do NOT touch `post_content` through this call.
Rank Math meta is a separate path (not via ACF).

### 11.5 Schema markup — custom JSON-LD only, NEVER via Rank Math
**Hard rule (from `maverickframe-publish` skill):**
Schema.org on maverickframe.com is **custom JSON-LD only**, placed in
the ACF **Schema** field `field_64ea0fc545b5a` as a single
`<script type="application/ld+json">…</script>` tag containing one
`@graph` array with all node types merged together. Never set
`rank_math_schema_*` postmeta. Never enable Rank Math's Schema module
on a post. If a previous run left Rank Math schemas, clear them:

```js
codemode.rest_api({
  site_url: "https://maverickframe.com",
  method: "POST",
  route: "/rankmath/v1/updateMeta",
  body: JSON.stringify({
    objectType: "post", objectID: <ID>,
    meta: {
      rank_math_schema_Article: "",
      rank_math_schema_FAQPage: "",
      rank_math_rich_snippet: "off",
      rank_math_snippet_article_type: ""
    }
  })
})
```

**Why this rule exists:**
- The site renders custom JSON-LD from the ACF Schema field already.
  Letting Rank Math also emit Article/FAQPage/Breadcrumb produces
  **duplicate schema nodes** — Google Rich Results Test flags this
  and the SEO-analysis skill's Phase 15 audit specifically scans for
  it: "No duplicate/conflicting schema — same type not emitted twice
  with different data; Rank Math NOT also emitting FAQPage/Breadcrumb."
- The `@graph` pattern keeps WebPage + Article + BreadcrumbList +
  ImageObject + VideoObject (Bunny videos) + FAQPage merged into one
  document — one source of truth per page, not several competing
  scripts.
- ACF Schema field is **NOT exposed via REST** (it's a textarea
  postmeta with TinyMCE on top). Save it through the wp-admin
  post.php FormData pattern documented in `maverickframe-publish`
  Phase 7 ("Schema merge playbook").

**What to put in the @graph for each post type:**
- **Case page (success-stories):** WebPage + Article (or
  CollectionPage) + BreadcrumbList + ImageObject (featured image)
  + VideoObject (only if a Bunny video is embedded — native
  `<video>`/mfs-video player; include `duration`) + FAQPage (only if a
  FAQ block is present).
- **Blog post:** WebPage + BlogPosting + BreadcrumbList +
  ImageObject (featured) + FAQPage if FAQ block.
- **Front page (page ID 6):** uses the same field, same merge rule.

**Always merge, never overwrite** the existing `@graph` — re-read
the field, parse JSON, push/replace specific nodes, re-stringify,
re-save. Single-shot overwrites lose hand-tuned values.

For the full pipeline (build → save via post.php → clear WP Rocket
→ verify via Rich Results Test), call the `maverickframe-publish`
skill (Phase 7 + Schema merge playbook section).

### 11.6 Verification before Publish
Always view the Rank Math sidebar snippet preview in the post editor
before flipping `status: draft → publish`. All three bars (Title, URL,
Description) must be green or yellow — never red.

---

## 12. Case-page block patterns (success-stories CPT)

Standard case-page block order, used by `single-success-stories.php` via
`the_content()`. Build cases from this exact list — never invent new
section types without first adding the block under `components/blocks/`.

1. `acf/hero-block` — hero with breadcrumbs, H1, description, tags
2. `acf/client-context` — Client & Market Context (intro + 2 images)
3. `acf/business-challenge` — narrative + big_text pull quote + visual
4. `acf/project-objectives` — **ALWAYS 4 items** (this is a hard rule;
   3 leaves a visually empty card; 5 overflows the grid)
5. `acf/services-provided` — services list with links to /services/
6. `acf/production-process` — 4 phases (slider)
7. `acf/key-visuals` — 5 items (grid of key decisions)
8. `acf/visual-results` — 6+ items (gallery, "Show more" after 7th)
9. `acf/strategic-cgi` — narrative + pull quote + media
10. `acf/result-business-impact` — 3 stat items + photo
11. `acf/marketing-sales` — narrative + image + quote
12. `acf/key-insight` — single pull quote
13. `acf/team-items` — auto-populated from team CPT (no data)
14. `acf/faq` — 5 questions (long-tail SEO, must mirror page-relevant Q&A)

Block ACF field keys are extracted from `acf-json/` — see
`ACF-BLOCKS-REFERENCE.md` in the team handoff package.

### Building Gutenberg block markup programmatically
ACF blocks are stored in `post_content` as:
```
<!-- wp:acf/<block-name> {"name":"acf/<block-name>","data":{...},"mode":"preview"} /-->
```
The `data` object has flat keys. Repeater fields use `<key>_<N>_<sub>`
indexing (0-based). For each field include both `name` and `_name`
(field key reference). Field keys live in `acf-json/group_<id>.json`.

## 13. Image processing for WP uploads

Original PSD exports and PNG renders from the studio are often **5-15
MB each** — we never upload them raw. Convert via ImageMagick before
upload to media library.

### Target sizes
- **Hero / featured image:** 300-500 KB, max-width 2400 px, WebP q=82
- **Inline content images (Key Visuals, Visual Results, Strategic CGI):**
  150-400 KB, max-width 1920 px, WebP q=80
- **Context / small detail images:** 100-200 KB, max-width 1920 px, q=78

### Generated intermediate sizes (2026-07-02)
`functions.php` filters `wp_editor_set_quality` → WordPress re-encodes
**WebP intermediate sizes at q=68** (default 82 left hero variants at
150-185 KB). To recompress the EXISTING library in place, run **Force
Regenerate Thumbnails** (plugin active) — filenames/URLs stay the same.
There is no way to replace upload files from outside on Rocket.net
(Imagify needs an account; no file access to uploads/), so quality filter
+ regenerate is the canonical recompression path. Don't downsize served
variants (large→medium) — retina blur.

### Conversion command (one image)
```bash
convert source.png -resize 2400x\> -quality 82 -define webp:method=6 target.webp
```

### Batch script — see `scripts/img-to-webp.sh` (if added) or run inline:
```bash
for f in *.png; do
  convert "$f" -resize 1920x\> -quality 80 -define webp:method=6 "${f%.png}.webp"
done
```

### Filename rules (SEO)
- Lowercase, hyphen-separated, descriptive.
- Include the case slug or product context.
- Example: `private-padel-court-cgi-tropical-villa-hero.webp` (not
  `hero1.webp` or `IMG_1234.webp`).

### Alt-text rules
- One sentence, 80-160 chars, descriptive of what's *in* the image.
- Include 1 SEO keyword variant if natural.
- Never leave alt empty on a content image.

### Upload via REST after Chrome upload
After uploading via wp-admin Media Library (Chrome MCP), set alt-text +
title via the WPVibe REST: `POST /wp/v2/media/<id>` with
`{ alt_text, title }` in the body (string-serialized JSON — see
`maverickframe-publish` skill for exact pattern).

## 14. When in doubt

- **Code change?** → `git status` first. Edit file, commit, push, watch
  the Action in `github.com/Maverickframe/maverick/actions`.
- **Content change?** → WPVibe writes to prod; double-check the target
  post/field before saving.
- **"Why is my change invisible?"** → check that you targeted
  `html.single-X` not `body.single-X`; verify ANONYMOUSLY on the
  canonical URL (logged-in view lies, §7); content edits self-purge,
  code/SQL changes need the two-layer flush (§7).
- **"WPVibe says File unchanged"** → vary the path string.
- **"Local git won't commit from Cowork bash"** → run it in real Terminal.
- **"GitHub Action failed at npm run build"** → check `vite.config.js`
  for new env vars not set in `deploy-production.yml`.
- **"GitHub Action failed at FTPS deploy"** → secrets wrong or FTP
  password rotated. Re-set in Rocket dashboard and update the secret in
  GitHub.
- **"Prod shows old version after deploy"** → the deploy Action's purge
  step does the two-layer flush (origin file API → purge_everything); if
  it still looks stale, run `rocket-purge.py` manually and verify the
  canonical anonymously (§7).
- **"Styles randomly missing on prod"** → front page only: is the block
  in `front-atf.scss` but still listed in `front-btf.scss` (or vice
  versa)? Check the ATF/BTF split first; kill-switch `?mfs_split=0` for
  A/B diagnosis.
- **"Lighthouse in DevTools shows a disaster, PSI is fine"** → DevTools
  runs on your machine/network: right after a purge the first hit is a
  cold cache (TTFB ~2.5s). Absolute numbers: PSI + field CrUX data only.
  Always measure logged-out, from the second run after a purge.

---

_Last updated: 2026-07-13 (synced with Dima's handoff 07-10→07-12:
WP Rocket removed entirely — cache is Rocket.net edge + theme features
with kill-switches; plugins 23→8; web fonts → system stack; portfolio
CPT deleted; blog ACF via plain REST; verify anonymously. Edit in place
when something changes — don't append "as of" updates, just rewrite the
relevant section._
