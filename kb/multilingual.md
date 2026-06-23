# maverickframe.com — i18n / multilingual layer (handoff)

> Purpose: full state of the i18n layer so a second contributor (German `/de/`
> localization) can branch from a clean point and not break the live Spanish
> `/es/` layer or collide on the same theme files.
>
> Last updated: 2026-06-19. Keep this file current when the i18n layer changes.

---

## 0. Git state (read this first)

- **Working branch:** `multilang` (daily code work happens here, **not** on `main`).
- **multilang HEAD:** `f45a31df096ef937c5657437b7d59af17ec155a1`
  (`f45a31d` — "fix(hubs): readable breadcrumbs + title highlight on cases/services hubs; container_small alignment").
- **Push state:** `multilang` is **fully pushed** — `origin/multilang == f45a31d` (0 ahead / 0 behind).
- **`origin/main`:** `c681c3e2f59fedd7fb65c6e5ce1bff6dd4b05ccf` (`c681c3e`). main is ahead of
  multilang's base; the hub-fix changes are already merged into main (squash/merge → new SHA),
  so by content main ⊇ multilang except the literal `f45a31d` commit object.

**Where to branch the DE work from:** `origin/main` (`c681c3e`) — it is the cleanest fully-merged
point. Do **not** branch from a local working tree (see uncommitted work below).

### `git diff --stat origin/main..multilang` (what's on multilang, not yet in main by SHA)
```
 book-calendar.js                                   |  4 ++--
 components/blocks/cta-form/cta-form.php            |  2 +-
 components/blocks/free-test-render/free-test-render.php |  2 +-
 components/common/modals/modal-book.php            |  2 +-
 components/service-page/price.php                  |  2 +-
 src/js/components/calculator.js                    |  2 +-
 src/js/components/contacts.js                      | 25 +++++-----
 src/js/components/quiz.js                          |  2 +-
 src/scss/new/pages/success-stories.scss            | 27 ++++++++++++
 templates/success-stories.php                      |  2 +-
 templates/template-services-hub.php                | 14 +++++++--
 11 files changed, 61 insertions(+), 23 deletions(-)
```

### Uncommitted local work (LocalWP working tree only — NOT in git, NOT deployed)
This is the in-progress **Solutions** page (separate effort). A fresh branch off `origin/main`
will **not** include it, which is what we want. Listed here only so you know these files are
"hot" locally on this machine:
```
 M components/blocks/hero-front/hero-front.php        (breadcrumbs on inner pages)
 M components/blocks/performance-scale/performance-scale.php (header renders only if subtitle/title set)
 M functions.php                                      (isNewDesign += template-solutions)
 M src/js/components/sliders.js                        (solution-capabilities Splide slider)
 M src/scss/blocks.scss, src/scss/bundles/blocks-frontend.scss (solution-* imports)
 M src/scss/new/blocks/hero-front.scss                (breadcrumb positioning)
 ?? components/blocks/solution-intro|solution-capabilities|solution-benefits/
 ?? src/scss/new/blocks/solution-*.scss
 ?? templates/template-solutions.php
```

> ⚠️ Note for the sandbox: `git` write ops (commit/push/fetch) from the Cowork sandbox fail with
> `Operation not permitted` on `.git/objects`/`index.lock` cleanup in the LocalWP mount. Run all
> real git commands in a normal macOS Terminal.

---

## 1. `mfs_t` and its family — the i18n helpers

All defined in **`functions.php`** (theme root), top of file, each guarded by `function_exists`.

### `mfs_t( $en, $es )` — functions.php:6
```php
function mfs_t( $en, $es ) {
    return ( function_exists('pll_current_language') && pll_current_language() === 'es' ) ? $es : $en;
}
```
- **Binary EN/ES.** Returns `$es` only when Polylang reports current language `es`; otherwise `$en`.
- Used for every **hardcoded UI string** in templates (buttons, labels, "Read the story", etc.).
- **~201 calls across ~70 files** (PHP). High-traffic files: every `components/blocks/*/*.php`,
  `components/common/menu/menu.php`, `components/common/modals/*`, `single-blog.php`,
  `components/contacts-form.php`, `components/portfolio*.php`, `components/cta.php`, etc.
- Find them all: `grep -rn "mfs_t(" --include=*.php .`

### `mfs_eyebrow( $value, $default_en = '' )` — functions.php:15
- Resolves an ACF eyebrow value (or a hardcoded EN default), then on `/es/` maps known EN eyebrow
  labels → ES via an inline array (`Production Process→Proceso de producción`, `Why Choose Us→Por qué
  elegirnos`, `What we do→Qué hacemos`, etc.). Handles empty-field and untranslated-EN cases without
  per-page edits. **3 call sites.**

### `mfs_consent( $html )` — functions.php:39
- On `/es/` swaps the known EN consent prefix ("By clicking, you agree to receive communications…")
  → ES, via a regex that tolerates non-breaking spaces (U+00A0). EN untouched. **2 call sites**
  (cta-form `form_privacy`, modal `book_a_call_privacy`).

### `window.MFS_I18N` (JS strings) — functions.php:53-60
- Printed in `wp_head` (priority 1): `<script>window.MFS_I18N={…}</script>`.
- Keys (built via `mfs_t`): `exploreService`, `bookACall`, `nextReview`.
- Read in `src/js` (2 refs) for strings rendered by JS (modals/sliders); **falls back to English**
  if the global is missing.

> All four resolve language through `pll_current_language()`. There is **no** central language
> registry — they each branch on `=== 'es'`. Beyond these helpers, several templates also do their
> own inline `pll_current_language() === 'es'` checks (e.g. `$bc_is_es` in
> `templates/success-stories.php`, `templates/template-services-hub.php`; date maps in
> `articles-item.php`; menu `menu_items_es` / `menu_resources_es`; gallery category label map).
> **Audit these too when adding DE:** `grep -rn "pll_current_language\|_es\b\|menu_items_es\|menu_resources_es" --include=*.php .`

---

## 2. Polylang configuration (lives in the DB, not in git)

- **Polylang Pro**, directory mode (`/es/` URL prefix; EN has no prefix / is default).
- **Languages & term_ids (taxonomy `language`):** `en = 85`, `es = 88` (identical on local and prod,
  since the local DB is a prod snapshot). Verify in Languages → Languages.
- **Translatable post types:** `blog`, `services`, `solutions`, `success-stories`, `portfolio`, `team`.
  *(team CPT exists as translatable but currently has **EN members only** — no ES team posts.)*
- **NOT translatable:** `gallery` CPT — its categories/items are language-independent and shared
  between EN and `/es/`. Category labels are localized with a PHP map in `gallery-items.php`, but
  `data-tab` must stay `sanitize_title($en_title)` or the JS filter stops matching blocks.
- **Base-slug translations** (Languages → Strings translations, group "URL slugs"):
  - `services` → `servicios`
  - `success-stories` → `casos-de-exito`
  - Regular pages translate their own slug (no string needed): gallery → `galeria`,
    contacts → `contacto`, (solutions → `soluciones` when built).
  - After saving, no `rewrite flush` needed (Polylang applies live); old `/es/services/...`
    301-redirect to the new slug; EN slugs untouched.
- **URLs / hreflang:** ES pages live under `/es/…`; Polylang emits hreflang alternates between
  linked EN/ES posts. On the front end Polylang **filters CPT queries by current language**, so a
  CPT-listing page (blog hub, cases hub) shows only current-language posts — the ES hub is empty
  until ES posts exist (the blog "trending" block is also language-filtered; it has a guard to hide
  when empty).
- **Translation linking via REST (Polylang Pro):** create a translation with a single
  `POST /wp/v2/<type>` including `lang:'es'` and `translations:{ en:<EN_ID> }` (works for pages,
  services, success-stories, blog); the post is immediately language-linked. `template` can be set
  in the same request.

---

## 3. ES page map (PROD — source of truth)

| Section | EN | ES (prod) | ES slug |
|---|---|---|---|
| Home | (front page) | **19799** | `/es/` |
| Services hub | 10 | **20425** | `/es/servicios/` |
| Contacts | 14 | **20424** | `/es/contacto/` |
| Cases hub (success-stories) | — | **20342** | `/es/casos-de-exito/` |
| Cases (6) | — | **20343–20348** | under `/es/casos-de-exito/…` |
| Gallery | — | **20350** | `/es/galeria/` |
| Blog hub | — | **20351** | `/es/blog/` |
| Blog posts (2) | — | **20352** (`que-es-una-infografia-3d`), **20353** (`home-staging-virtual`) | `/es/blog/…` |
| Team | (EN only) | — | not translated |

- ES **home (19799)** had its `rank_math_canonical_url` fixed to self on 2026-06-19 (was pointing at
  the EN home).
- Service-pages ES infrastructure (`/es/` home + individual service pages) existed **before** this
  work; only the missing ES content was added.
- **Local LocalWP has its own, different ES IDs** (an earlier 2026-06-15 local build: hub 20106,
  cases 20102/20107/20109–20112, gallery 20128, blog hub 20129, test post 20130). The local ES posts
  and the prod ES posts are **separate rows with different IDs** — do not assume local IDs == prod IDs
  for ES content. (Non-ES content IDs do match, because local is a prod snapshot.)

---

## 4. Gotchas already paid for (i18n-specific)

- **EN-only CPT + `lang`:** Polylang filters CPT queries by language, so EN-only listings/sections go
  empty in ES context. Add guards (hide section when 0 posts) instead of assuming content exists.
- **Menu top-item = `post_object` → renders as non-clickable `<span>` in ES** when it points at an
  EN-only page (Polylang filters it to null in ES context). Fix: point the top item at the **ES
  translation**. Footer links and menu sub-items build URLs via `get_permalink($id)` directly —
  language-independent, resolve any ID (safe EN fallback where no ES version exists).
- **Top menu item renders as a dropdown `<span>`** (ignoring its own `link`) if it has a non-zero
  `groups_links` counter OR keyname `our_works`/`resources`. To make it a plain clickable link, zero
  the `groups_links` counter. keyname picks the dropdown type: `company`→big-links, `resources`→
  `menu-resources.php`, `our_works`→`menu-our-works.php`.
- **Resources dropdown** (`menu-resources.php`) = ACF link-icons + auto last-4 blog posts (language
  filtered). For ES it needs a `menu_items_es` top item with keyname `resources`, and ES paths in
  `menu_resources_es` (falls back to EN `menu_resources` if empty).
- **`gallery` non-translatable** — see §2; keep `data-tab = sanitize_title($en_title)`.
- **Spanish dates:** `get_the_date('F j, Y')` stays English without an `es_ES` language pack. Format
  Spanish months with a **PHP month map** in `articles-item.php` — do not rely on a locale pack.
- **`home_url()` is Polylang-filtered** → returns `/es/` on Spanish pages. For values JS uses to build
  URLs to theme files (e.g. `contacts.home_url` for POST to `…/forms/amo.php`), use
  `get_option('home')`, **not** `home_url()`, or the request 404s at `/es/wp-content/...`.
- **WP Rocket / CDN cache hides real prod state:** the canonical URL serves cached HTML; append an
  unknown query param (`?nocache=<ts>`) to force fresh PHP and confirm a deploy actually landed.
  `wp rocket clean` via `cli/run` is blocked → clear cache in the Rocket dashboard (+ WP Rocket).
- **Mega-menu empty `post__in`:** `menu-our-works.php` queries cases via `post__in => cases` with
  `posts_per_page => -1`; an empty ACF `cases` makes WP_Query ignore `post__in` and return **all**
  85+ success-stories into the header on every page. Always cap empty `post__in` (code falls back to
  6 latest).
- **Breadcrumbs styling:** reusable class is `ul.hero-block__breadcrumbs` (styles in
  `src/scss/new/blocks/breadcrumbs.scss`, self-contained). The page's SCSS bundle must import
  `breadcrumbs.scss`. On dark heroes they're styled faint light-grey; on **white/light heroes**
  override to dark links / grey current / black chevron (`arrow-right-breadcrumbs-black.svg`) and
  `opacity:1`.

---

## 5. Deploy state (what is where)

- **Production (maverickframe.com):** full ES layer is **live** — `/es/` home (canonical fixed),
  service pages, Servicios hub (20425), Contacto (20424, new-design), cases hub (20342) + 6 cases,
  gallery (20350), blog hub (20351) + 2 articles; ES menu/footer links wired; slug translations
  (servicios, casos-de-exito) applied.
- **`main` (→ staging → prod via Rocket "Publish to Production"):** all merged Cycle-1 code
  (i18n helpers, breadcrumbs/date/listing fixes, blog i18n, mega-menu cap, new-design Contacts
  template + `contacts` Vite bundle, form validation, hub `header_white`/rating-color fixes, and the
  hub breadcrumb/title-highlight/`container_small` fix via the latest PR).
- **`multilang` only (pushed, PR-pending into main if any):** currently equal to main by content.
- **LocalWP only (uncommitted):** the in-progress **Solutions** page (see §0). Not on any branch,
  not deployed.

Deploy flow (Cycle 1 = code): commit on `multilang` → `git push origin multilang` → PR
`multilang → main` → **merge to `main` triggers `deploy-staging.yml`** (needs reviewer approval) →
verify staging → Rocket "Publish to Production". Content (Cycle 2) goes through WPVibe/REST, no git.

---

## 6. Adding a 3rd language (German `/de/`)

### 6.1 Make `mfs_t` multilingual (it is binary EN/ES today)
Backward-compatible upgrade — all ~201 existing `mfs_t($en,$es)` calls keep working (DE falls back to
EN until a 3rd arg is supplied):
```php
function mfs_t( $en, $es = null, $de = null ) {
    $lang = function_exists('pll_current_language') ? pll_current_language() : 'en';
    switch ( $lang ) {
        case 'es': return $es !== null ? $es : $en;
        case 'de': return $de !== null ? $de : $en;
        default:   return $en;
    }
}
```
Then progressively add the German 3rd argument at call sites.

### 6.2 Family helpers
- `mfs_eyebrow`: add a `de` branch with a German eyebrow map (mirror the ES array).
- `mfs_consent`: add the German consent sentence + a `de` branch.
- `window.MFS_I18N`: once `mfs_t` supports DE, pass German 3rd args for `exploreService` /
  `bookACall` / `nextReview`.

### 6.3 Inline language checks (don't miss these)
The codebase has scattered binary `pll_current_language() === 'es'` checks (breadcrumb home link,
`articles-item.php` date map, `menu_items_es`, `menu_resources_es`, gallery label map, the
`get_option('home')` form fix, etc.). Each is a place that currently does "es or default-en" and must
gain a `de` case. Find them: `grep -rn "pll_current_language\|_es\b\|menu_items_es\|menu_resources_es" --include=*.php .`
Consider introducing a tiny `mfs_lang()` wrapper (returns `'en'|'es'|'de'`) and `mfs_is('de')` to
replace raw string comparisons and make future languages cheaper.

### 6.4 Polylang steps for DE
1. Languages → Add new language **German (de)**; note its `term_id` (record it here once created).
2. Strings translations → "URL slugs": set German base slugs (`services`→`leistungen`,
   `success-stories`→`referenzen` or studio's choice — confirm with marketing).
3. Create `/de/` translations of the pages you localize, link them via `translations:{ en:<EN_ID> }`
   (REST pattern in §2).
4. hreflang is automatic once posts are language-linked; verify en/es/de alternates render.
5. Menu/footer: add `menu_items_de` / `menu_resources_de` ACF option sets (mirror the `_es` ones);
   remember the top-item `<span>`/`post_object` gotcha (§4).

### 6.5 Pitfalls of going 3-language
- **Positional API risk:** `mfs_t` is positional. Keep the 3rd param optional with **EN fallback**,
  or many strings silently show blank in DE. Don't reorder params.
- **Two contributors, same theme files:** the Solutions effort (uncommitted locally) touches
  `functions.php`, `hero-front.php`, `performance-scale.php`, `sliders.js`, the SCSS block bundles —
  **DE work will also touch `functions.php` (the helpers).** Coordinate: branch DE from `origin/main`,
  land small focused PRs, and expect a `functions.php` merge around the helper block. Avoid editing
  the `solution-*` files (they're not on a branch yet).
- **Date/locale:** add a German month map like the Spanish one; don't assume a `de_DE` pack.
- **gallery `data-tab`** must remain `sanitize_title($en_title)` across all languages.
- **CPT language filtering** means every `/de/` listing is empty until DE posts exist — reuse the
  existing empty-section guards.
- **Cache:** after DE deploys, bust WP Rocket + CDN; use `?nocache=` to verify.
- **Slug collisions / redirects:** changing/adding slug strings can 301 old URLs — check no ES slug
  is accidentally reused for DE.
