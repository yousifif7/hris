# McCrory Center HR Portal — User Guide

A practical walkthrough of how the system works for the HR team, from the moment a candidate applies to the day they become an employee.

This is not a developer guide. It's written for the people who use the portal every day.

---

## The big picture

The system carries one candidate through a fixed series of stages. As you move them from one stage to the next, **the system automatically does the right thing in the background** — sends the right email, generates the right link, creates the right records.

Your main job is to move candidates forward when they're ready. The automation handles the rest.

Here is the journey, top to bottom:

```
Applied  →  Pre-Screening  →  Pre-Interview Questions  →  Verification & Review
                                                                     ↓
   Hired  ←  Pre-Onboard Documents (candidate portal opens here)  ←  Offer Letter
```

---

## Stage 1 — A candidate applies

**Where this happens:** the public application page, which lives at a private URL (e.g. `/apply/<token>`) that only people you share it with can use.

**What the candidate does**
- Fills in name, contact info, address, optional resume upload.
- Submits.

**What the system does for you**
- Creates a new candidate record with status **Hiring**.
- Assigns it round-robin to one of your active HR/admin users.
- Notifies the assigned person and all admins.
- Saves the resume against the candidate.

**Where you'll see them**
- Staff Portals page (`/hris/staff-portals`) — they appear in the list.
- The assigned HR person sees an in-app notification.

> **Tip:** If you need to regenerate the public application link, do it from **Settings → Apply Link**.

---

## Stage 2 — You move them to Pre-Screening

Open the candidate (Staff Portals → click their name), then change their status to **Pre-Screening**.

**What the system does automatically**
- Generates a private scheduling link for them.
- Builds out 30 days of available interview slots based on your **Weekly Availability** setting.
- Emails them the `invite` template, which includes the scheduling link.

**What you should do**
- Review the application.
- Use the right sidebar to add a comment, log a call/email, or assign a task if needed.

> **If they don't respond:** the daily automation will follow up automatically based on your **No-Response Followup** setting. No action needed from you.

---

## Stage 3 — The candidate self-books their interview

The candidate clicks the scheduling link in their invite email and picks a time from the slots you defined.

**What the system does automatically**
- Creates an Interview record.
- Marks the slot as booked.
- Moves the candidate to status **Interview Scheduled**.
- The interview will appear on your **Calendar** and **Interviews** pages.

**What you should do**
- Hold the interview.
- After the interview, decide whether to move them forward.

---

## Stage 4 — You move them to Pre-Interview Questions

After a good interview, change the status to **Pre-Interview Questions**.

**What the system does automatically**
- Generates a private pre-screening / employment-application link.
- Emails them the `prescreening` template, which includes that link.

**What the candidate does**
- Fills the long employment-application form (work history, references, license info, agreements, signature).
- Submits.

**What the system does when they submit**
- Stores the full submission as an audit snapshot.
- **Copies the relevant fields onto their record**, so when you open their Pre-Interview Questions tab in the portal, you see the answers populated (years of experience, education, availability, address, licenses, etc.).
- Moves them automatically to **Verification and Review**.
- Notifies all admins.

**What you should do**
- Open the candidate's detail page.
- Click **View Application** at the top — this opens the full submission as the candidate filled it.
- Review.

---

## Stage 5 — Verification and Review

When the candidate enters this stage, the system creates three pending background-check tasks for you:

- MDHHS
- SAM / OIG
- NPDB

**What you should do in this stage**
- Run each background check and update its status in the candidate's record.
- Upload supporting documents in the **Verification and Review** tab.
- Request and review references.
- Confirm I-9 verification documents.
- Tick off items in the **Onboarding Documents Checklist**.

There is no automatic forward movement here — you advance to the next stage manually once you're satisfied.

---

## Stage 6 — Offer Letter

This is the most important manual stage. Before you change the status, **fill in the Offer Letter tab** on the candidate's detail page.

**Fields to fill on the Offer Letter tab**
- Date
- McCrory Center (location)
- Operations Manager
- Clinical Supervisor
- Anticipated Start Date
- Amount
- Payment Frequency (Hourly, Weekly, Bi-Weekly, etc.)
- Company Representative
- Deadline Date For Acceptance

**Once the fields are filled, change the status to Offer Letter.**

**What the system does automatically**
- Creates the formal Offer record (also visible on the Offers page).
- Generates the candidate's private acceptance link.
- Sends the candidate the **offer** email template from Settings, with placeholders (their name, amount, start date, etc.) filled in from the fields above.

> The email body comes from the global **offer** template in Settings → Email Templates. Update that template if you want to change what's sent.

---

## Stage 7 — The candidate accepts (or declines)

The candidate clicks the link in their offer email. They see the offer details. They click **Accept** or **Decline**.

### If they decline
- Status moves to **Applicant Declined**.
- A follow-up email is sent.
- Admins are notified.
- That's the end of this candidate's journey.

### If they accept (this is the new part — pay attention here)

The system does **all** of this automatically, in one step:

1. Marks the Offer as **accepted**.
2. Moves the candidate to **Pre-Onboard Documents**.
3. **Creates a User account for the candidate** — same email, random temporary password.
4. **Emails the candidate their login credentials** using the `candidate_portal_credentials` template (this template is auto-created the first time, so you don't have to set it up).
5. Generates the onboarding task list from your Onboarding Templates.
6. Sends the `onboarding` email.
7. Notifies all admins that the offer was accepted.

The candidate now has access to their own **Candidate Portal**.

---

## Stage 8 — Pre-Onboard Documents (the Candidate Portal is now open)

This is where the candidate does a lot of the paperwork themselves, freeing you up.

### What the candidate sees when they log in

They go to `/login`, enter their email + temporary password, and land on `/candidate-portal`. The page shows:

- A welcome card with their current status.
- **Their offer details** — amount, frequency, start date, the letter body — read-only.
- **Personal Information** — editable: first/last name, phone, address.
- **Emergency Contacts** — editable: two contacts.
- **References** — editable.
- **Documents** — they can upload: College Degree, Transcripts, CPR Certification, Child Registry Clearance, TB Test, DWIHN Transcripts, I-9, Recipient Rights Training, Annual CEUs.
- **Acknowledgements** — checkbox to confirm review of the Employee Handbook.
- **Your HR Contact panel** — read-only display of their assigned HR person, Operations Manager, Clinical Supervisor, Company Rep.
- A button to **change their password**.

### What the candidate cannot change

- Their status.
- Who they're assigned to.
- Their offer amount, frequency, start date, or the letter body.
- Their supervisors.
- Their email (it's their login — they have to contact you to change it).

### What you should do in this stage

- Watch documents arrive in the candidate's detail page (they update in real-time on your side).
- Cross-check Compliance Agreements (BAA, NDA, Handbook acknowledgement).
- Manage the Clinical Staff Documents tab (licenses, insurance, Medversant).
- Fill in Emergency Contact info if the candidate hasn't.
- Move them through Training and Development, Financial and Payroll, Post-Offer Documents, and DWC Trainings as their info comes in.

> Each upload from the candidate is automatically logged on the activity stream so you have an audit trail of who uploaded what and when.

---

## Stage 9 — Hired

When everything is in place — documents collected, agreements signed, trainings recorded — you finalize them.

**Two ways to do this**

1. **The full path:** click **Convert to Employee** in the candidate's More menu, supply department/access info if you have it. This is the recommended path.
2. **The quick path:** change the status to **Hired** directly.

**What the system does automatically**
- **Reuses the candidate's existing portal account** (created back at Stage 7) and **promotes their role from candidate to employee**. No new account, no new password, no duplicate.
- Creates the formal Employee record, pulling pay, start date, location from their Offer.
- Notifies all admins that the candidate has been converted.
- Only sends a new `portal_credentials` email if the candidate somehow never had an account (the rare case where HR skipped the acceptance step).

The new employee will now land on `/portal` (the employee portal) next time they log in, instead of the candidate portal.

---

## A few things worth knowing

### Settings page
Most of the automation behavior is configured in **Settings**:
- Company name, app URL, default offer deadline, default interview duration, no-response follow-up days.
- Weekly Availability (used to generate scheduling slots).
- Email templates — every email the system sends is editable here. You can change subject lines, body text, and use the same `{{token}}` syntax described in the Offer Letter section.

### Automations page
The **Automations** page (`/hris/automations`) shows the time-based rules that run daily at 8:00 AM:
- Sending no-response follow-ups to candidates who haven't booked an interview.
- Expiring offer letters that have passed their deadline.
- Warning about expiring trainings.

You can enable/disable each rule from this page.

### The activity stream
Every candidate has a stream of events on the right side of their detail page:
- Status changes.
- Emails sent.
- Documents uploaded (by HR or by the candidate).
- Field changes for important fields (assignments, expiration dates, documents).
- Comments and logged activities.

This is your audit trail. Anything significant that happens to a candidate appears here.

### What happens if I move a status backwards?
The system fires the side-effects of whatever status you set, every time you set it. So if you bump someone back to Pre-Screening, they will get the invite email again and new slots will be generated. Be intentional with status changes.

### What if the candidate loses their portal password?
On the login page, they currently don't have a self-service reset. The simplest fix today: open their user record, reset the password, and let them know. (A self-service reset is a possible future addition.)

### What if I change a candidate's email after they have a portal account?
The candidate's User account still has the old email — that's still their login. If you need to change their login email, you have to update the User record itself, not just the candidate record. Reach out to your admin if you're unsure.

---

## Quick reference — what status triggers what

| Status you set | What happens automatically |
|---|---|
| **Hiring** | (Default for new applicants — no email) |
| **Pre-Screening** | Scheduling link + 30 days of slots + invite email |
| **Interview Scheduled** | (Set automatically when candidate books) |
| **Pre-Interview Questions** | Prescreening link + prescreening email |
| **Verification and Review** | Three background-check tasks created |
| **Offer Letter** | Offer record + offer link + offer email |
| **Pre-Onboard Documents** | **Candidate portal account created + credentials emailed** + onboarding tasks + onboarding email |
| **Rejected** | Rejection email |
| **Applicant Declined** | Declined-followup email + admin notify |
| **Hired** | Employee record created + existing portal account promoted to employee |

---

If something in the system isn't behaving the way this guide describes, it's a bug — please report it so we can fix it rather than working around it.
