<?php
/**
 * HubSpot private-app token — for the CRM Contacts API fallback that creates
 * contacts for PHONE-ONLY leads (the Forms API cannot create a contact without
 * an email, so those leads need the CRM API instead).
 *
 * Setup: HubSpot → Settings → Integrations → Private Apps → Create a private app,
 * scope `crm.objects.contacts.write`, copy the access token and paste it below.
 *
 * Empty token  -> phone-only leads are skipped (logged); email leads unaffected.
 * EU portal: CRM API base stays api.hubapi.com (token routes to the right region).
 */
return [
    'private_token' => '', // TODO: paste HubSpot private-app token (e.g. pat-eu1-xxxxxxxx-...)
];
