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
- **WP Rocket** for caching — CSS delivery runs in **"Load CSS
  asynchronously"** mode (critical CSS inline + async front.css); do NOT
  switch to Remove Unused CSS: **Used CSS** mode strips inline
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

- **WP Rocket LazyLoad ignores `loading="eager"`** but skips images with
  `fetchpriority="high"`. Any image that must never be lazy (logo, hero
  slides) needs `data-no-lazy="1"` / class `skip-lazy` explicitly —
  `eager_attachment()` adds them. (Removing fetchpriority from the logo made
  Rocket lazyload it → the logo became the mobile LCP element.)

- **Splide v4 puts `role="group|tabpanel"` on every slide** — invalid on
  `<li>` (axe aria-allowed-role), fails PSI's Agentic Browsing
  "accessibility tree" audit. Fixed by `src/js/components/splide-a11y.js`
  (patches `Splide.prototype.mount`); it MUST be imported in every file
  that creates Splide instances (currently sliders.js, modals.js).

- **Hero marquee loops both ways:** one column auto-scrolls backwards and
  shows clones of the TAIL slides within seconds. Eager-load first 4 AND
  last 2 slides per column in hero-front.php — eager-ing only the head
  leaves the real LCP slide lazy.

- **AutoScroll marquees start delayed** (~3s or first interaction,
  `delayAutoScrollStart()` in sliders.js, `autoStart: false` in configs):
  an immediately-moving marquee pins lab Speed Index at ~6s forever. Keep
  this when touching sliders.

- **Code-split bundle = `type="module"` + Rocket JS-minify exclusion.**
  The entry has `import.meta`/dynamic `import()` (SyntaxError as a classic
  script) — `inc.vite.php` swaps the tag. WP Rocket → Excluded JavaScript
  Files must keep `/wp-content/themes/maverickframe/build/(.*).js`, else
  main.js is served from `/cache/min/…` and relative chunk URLs 404.
  In bundle.js, modules that rely on DOMContentLoaded (videoPlay,
  visualResultsGallery, sticky-cta) must stay statically imported — an
  async chunk can arrive after the event and never init.

- **Purge order after changing WP Rocket settings:** Rocket clear → open a
  page logged-out once (regenerates page cache) → THEN Cloudflare "Purge
  Everything". Purging CDN first re-caches the stale HTML at the edge.

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

> **SUPERSEDED (2026-07-02): the current process lives in `WORKFLOW.md`.**
> Short version: branch `fix/<task>` off fresh `main` per task → PR to
> `main` → Dima merges and runs the manual "Deploy theme to PRODUCTION"
> Action → CDN Purge Everything + WP Rocket Clear. `multilang` is retired
> (it accumulated rebase-duplicate commits → false PR conflicts) and was
> reset to main. Batch small fixes into one branch instead of deploying
> one-liners. The staging pipeline below no longer exists.

### Local → GitHub
Standard git. `main` is the only long-lived branch. Feature branches OK,
merge via PR (or fast-forward for solo work). Auth via `gh auth login`
(GitHub CLI) or PAT.

### GitHub → Staging (gated by required reviewer)
`.github/workflows/deploy-staging.yml` runs on every push to `main`, but
the job uses `environment: staging` — it **pauses before checkout/build/
deploy** and waits for a required reviewer (Dima) to click "Approve" in
GitHub Actions → Environments → staging. Reject → nothing deploys.
Configure reviewers in repo Settings → Environments → staging.

Required reviewers on private repos requires GitHub Team plan (or
public repo). If the org is on free private — either upgrade or change
the workflow to `workflow_dispatch`-only.

Once approved, the job:

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
- Example: `private-padel-court-cgi`

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
  + VideoObject (only if a Bunny Stream iframe is embedded — include
  `duration`) + FAQPage (only if a FAQ block is present).
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
- **"Lighthouse in DevTools shows a disaster, PSI is fine"** → DevTools
  runs on your machine/network: right after a purge the first hit is a
  cold cache (TTFB ~2.5s), and on a slow connection `splide.refresh()` on
  window load produces phantom CLS on the hero columns. Absolute numbers:
  PSI + field CrUX data only. Always measure logged-out.

---

_Last updated: 2026-07-02. Edit in place when something changes — don't
append "as of 2026-08" updates, just rewrite the relevant section._
