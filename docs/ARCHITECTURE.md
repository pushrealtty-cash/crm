# CRM architecture and operating rules

## Application layers

1. **Presentation:** responsive HTML/CSS rendered by PHP; desktop sidebar collapses to mobile navigation.
2. **Application:** PHP 8.1+ request handlers, session authentication, role checks, CSRF validation, form validation, and assignment rules.
3. **Data:** MySQL tables for users, projects, leads, activities, and audit log foundation.
4. **Integrations:** authenticated JSON webhook. Meta is connected through a trusted server-side automation (n8n/Make) that retrieves lead details and normalizes the payload; website forms post from the website backend.

## Entity relationships

- One project has zero or one assigned sales owner.
- One sales user can own multiple projects.
- One project can have many leads.
- One sales user can be assigned many leads.
- One lead has many activity records.
- External lead ID + source is unique when an external ID is supplied.

## Assignment policy

`projects.owner_user_id` is the default lead owner. On lead creation, the CRM reads that field and writes it to `leads.assigned_to`. If there is no project owner, the lead remains unassigned. Admin can override the owner on the lead edit form. Sales users cannot change project or owner; they can update contact details, status, remarks, and follow-up information.

Updating a project's owner changes future assignment only. Existing leads retain their current owner so historic ownership and accountability are not silently rewritten.

## Core tables

- `users`: authentication, role and account activation.
- `projects`: property/project master and default salesperson.
- `leads`: customer details, campaign attribution, pipeline stage, owner and next follow-up.
- `activities`: lead history and notes.
- `audit_logs`: reserved for expanded admin/security event logging.

## Suggested next release

1. Meta OAuth + leadgen webhook verification and server-side Graph API retrieval (or keep n8n as the integration bridge).
2. Email/WhatsApp notifications for new assignment and due/overdue follow-ups.
3. CSV import/export and paginated lead table.
4. Campaign reporting: lead-to-contact, interested, site visit and booking rates by campaign/ad set/ad.
5. Duplicate review screen based on normalized phone number, with merge controls.
6. Lead reassignment history and full audit logging.
7. Password reset, login throttling, user deactivation and mandatory password change.
8. Consent fields, retention policy, data export and deletion workflows.
