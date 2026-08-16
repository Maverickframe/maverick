<?php
/**
 * TOMBSTONE — do not put logic here, and do not delete it yet.
 *
 * This was the lead endpoint of every form on the site, under a name left over
 * from amoCRM (retired 2026-07-15). On 2026-08-16 the handler moved to
 * forms/lead.php; this file stays because the old path is hardcoded in
 * src/js/components/contacts.js and therefore sits inside JS bundles ALREADY
 * CACHED in visitors' browsers. Deleting it would silently drop live leads from
 * everyone who has not refreshed the bundle yet.
 *
 * When to delete: once the access logs show no POSTs to /forms/amo.php for a
 * couple of weeks. Check before deleting, do not guess.
 */

require __DIR__ . '/lead.php';
