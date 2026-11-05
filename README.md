# Estate CRM — Real Estate Lead & Sales Management

A lightweight, responsive PHP + MySQL CRM starter for a four-person real-estate sales team. It supports project-owner lead assignment, shared lead visibility, manual entry, website/API intake, Meta lead intake through a normalized webhook, follow-ups, call/WhatsApp shortcuts, and a basic admin dashboard.

## Included

- Admin and sales logins (role-based permissions)
- All sales users can view all leads
- Only admin can create projects, create users, and change project/lead ownership
- New leads inherit the sales owner configured on their project
- Lead sources: Meta Ads, Website, Manual, API
- Lead status pipeline, campaign/adset/ad fields, budget, purpose, property type, remarks
- Follow-up date/time list with overdue indication
- Call and WhatsApp shortcuts
- Activity history for lead creation and edits
- Authenticated JSON webhook for website forms, n8n/Make, and Meta lead integrations
- MySQL schema and cPanel installation guide

## Requirements

- PHP 8.1+ (8.2 recommended)
- MySQL 5.7+ or MariaDB 10.4+
- PHP PDO MySQL extension
- HTTPS enabled on the CRM domain/subdomain

## Quick installation (cPanel)

1. Create a MySQL database and database user in cPanel. Grant the user **all privileges** on that database.
2. Import `sql/schema.sql` using phpMyAdmin.
3. Upload the project folder contents to a folder such as `public_html/crm/` or a subdomain document root. The `public/` folder should be the web document root where possible. If using a subfolder, point the domain/subdomain document root to the `public` directory.
4. Copy `app/config.example.php` to `app/config.php` and enter database credentials, the public app URL, timezone, and a long random webhook token. Do not expose `app/config.php` publicly.
5. Visit `https://your-crm-domain/setup` only if URL rewriting is configured, otherwise visit `https://your-crm-domain/index.php?page=setup`.
6. Create the admin account. Then sign in and open **Settings**.
7. Add the four sales executive accounts with individual strong passwords.
8. Add every active property/project and assign its responsible salesperson. New leads for that project will be assigned to that owner.
9. Test lead creation, project assignment, follow-up scheduling, mobile layout, and all five accounts before importing live leads.
10. After setup, restrict or remove access to the setup route. It is automatically blocked once an admin user exists.

### If hosting in a subfolder

The included app uses query-string routing, so no rewrite rules are required. Upload the contents so `index.php` is the default document in the public folder. Ensure the `app/` folder is outside the public document root if possible. If your hosting layout requires the entire folder under `public_html`, deny web access to `app/`, `sql/`, and `docs/` using server rules.

Example Apache `.htaccess` inside the project root (adapt to your hosting layout):

```apache
Options -Indexes
<IfModule mod_authz_core.c>
  <Directory "app">
    Require all denied
  </Directory>
</IfModule>
```

A safer setup is to keep `app`, `sql`, and `docs` one directory above the web root, and expose only `public/`.

## First-time configuration

Edit `app/config.php`:

- `db.host`, `db.name`, `db.user`, `db.pass`
- `app_url` — full HTTPS URL of the CRM (no trailing slash)
- `webhook_token` — a unique secret of 32+ random characters
- `timezone` — default `Asia/Kolkata`

Generate a token with a password manager or a secure random generator. Never put the token in browser-side JavaScript or a public form.

## Project assignment rules

- Every project has one active owner (sales executive).
- Incoming leads with a valid `project_id` are assigned to that project's owner.
- Leads without a project or projects without an owner remain unassigned and appear in the leads list for admin resolution.
- All sales executives can view and update lead details/status/remarks/follow-ups.
- Sales executives cannot change a lead's project or assigned owner. Admin can change project and assignment by editing the lead or changing the project owner in Settings.
- Changing a project's owner affects **future leads**. Existing leads keep their current assigned salesperson until an admin edits/reassigns them.

## Important production hardening before launch

This is a functional MVP/starter, not a fully audited enterprise CRM. Before using it with real customer data:

- Enforce HTTPS and keep PHP/MySQL patched.
- Store `app/`, `sql/`, and `docs/` outside the public web root.
- Restrict webhook access by secret token and, if possible, IP allowlist; rotate token if exposed.
- Configure automated daily database backups and test restoration.
- Add a privacy notice/consent process for lead forms and define retention/deletion practices.
- Add rate limiting, login throttling, password reset flow, email/WhatsApp notifications, and stronger audit logging before scaling.
- Configure Meta access tokens and webhook subscriptions securely on the server or in n8n/Make; do not expose Meta tokens in the browser.
- Validate the Meta form field mapping and test with Meta's test lead tool before going live.

## Current MVP limitations

- Meta integration expects a normalized lead payload at the webhook. Use n8n/Make or a server-side Meta Graph API connector to fetch lead details and post them here. The app does not itself complete Meta OAuth, webhook verification, or Graph API token refresh.
- Dashboard metrics are basic totals; date/campaign conversion reporting and export are planned extensions.
- No built-in scheduled notification sender yet. The follow-up page displays upcoming and overdue tasks; cron/email/WhatsApp reminders can be added as a next phase.
- Duplicate external lead IDs are idempotent. Phone-number similarity is not automatically merged; staff should check phone duplicates before creating manual records.
- Lead list shows up to 300 latest matching records; pagination/export should be added before large-scale use.
