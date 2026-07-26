<?php
/**
 * Title: Contacts page
 * Slug: construction/contacts-page
 * Categories: contact, construction
 * Description: Standalone contacts page with email, phone, address, and CTA.
 *
 * @package Construction
 */

$lang = construction_current_lang();
echo construction_contacts_page_content_for_lang( $lang ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
