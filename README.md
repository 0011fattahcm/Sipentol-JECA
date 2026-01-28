# SiPentol JECA

SiPentol is a CakePHP 5 web application built for LPK JECA to manage an end-to-end online registration pipeline.  
It supports both **User** and **Admin** roles—from account registration and verification, application form completion, online test scheduling, to re-registration (daftar ulang) document submission and admin verification.

---

## Key Features

### User Side
- **Authentication**
  - Register account
  - Email/OTP verification (if enabled in your setup)
  - Login / Logout
  - Forgot password (if enabled)
- **Registration Form (Pendaftaran)**
  - Fill and update biodata and required information
  - Upload required documents (depending on configured fields)
- **Online Test**
  - View test schedule and test access details provided by admin
  - Status flow support (e.g., waiting, closed)
- **Daftar Ulang (Re-Registration)**
  - Access is opened only when admin approves / user passes the stage
  - Upload required files:
    - Application form PDF
    - Agreement letter PDF
    - Parent consent PDF
    - Payment proof image
  - Submit / revise files based on admin feedback (Need Fix)

### Admin Side
- **Dashboard & Monitoring**
  - Quick visibility into user pipeline stages
- **Pendaftaran Management**
  - Review user registration form data and uploaded files
- **Online Test Management**
  - Create/update test schedule window
  - Assign test access ID and test URL
  - Close test
- **Daftar Ulang Management**
  - Review uploaded documents
  - Verify documents (mark as Verified)
  - Request revision (Need Fix) with admin note
  - Open access for Daftar Ulang when user reaches the required stage
- **Mailing (Email Broadcast)**
  - Send emails to specific users or all users
  - Supports placeholders (e.g., name, test access info)
  - Stores email content and send statistics (success/failed/total)

---

## Tech Stack
- **Backend**: CakePHP 5 (PHP 8+ recommended)
- **Database**: MySQL/MariaDB
- **Frontend**: TailwindCSS (UI styling)
- **Mailer**: CakePHP Mailer + SMTP transport (configurable)

---

## Status Workflow (High-Level)

SiPentol uses a staged pipeline (actual keys may vary based on your implementation):

1. `pendaftaran` → user fills registration form  
2. `menunggu_tes` → waiting for admin to schedule online test  
3. `tes` → test schedule and access available  
4. `menunggu_hasil` → test closed, waiting for results  
5. `daftar_ulang` / `lulus_tes` → admin opens re-registration access  
6. `onboarding` / `aktif` → user fully verified and activated  

> Notes: The app supports compatibility with “legacy” statuses and can normalize them on dashboard load.

---

## Project Structure (Common CakePHP Layout)

src/
Controller/
Admin/
Model/
Entity/
Table/
templates/
layout/
Admin/
webroot/
config/
logs/
tmp/


---

## Requirements
- PHP 8.1+ (recommended)
- Composer
- MySQL/MariaDB
- SMTP credentials for email sending (optional but recommended)

---

## Installation (Local)

1) Clone repository
```bash
git clone https://github.com/0011fattahcm/Sipentol-JECA.git
cd Sipentol-JECA
Install dependencies

composer install
Configure environment

Copy and edit your app config (example depends on your setup):

config/app_local.php (database, email transport, security keys)

Run migrations / setup database

If you use Cake migrations:

bin/cake migrations migrate
Start development server

bin/cake server
Email Placeholders (Mailing)
You can use placeholders inside subject/body:

{{nama_lengkap}}

{{email}}

{{test_access_id}}

{{test_url}}

These will be replaced automatically per recipient.

Testing Checklist (For Non-IT Staff)
User Flow
Register → verify → login

Fill Registration Form → save → reopen and check persisted data

Check test info (when admin sets schedule/access)

If test closed → status changes to “waiting result”

When admin opens Daftar Ulang access → user can upload required documents

Admin requests fix → user revises and resubmits

Admin Flow
View incoming pendaftaran data and attachments

Create/update online test schedule & access

Close test and mark progression stage

Open Daftar Ulang access for eligible users

Verify / Need Fix with notes

Mailing: send email to selected users/all users, confirm stats & recipients are correct

Security Notes
Do not commit secrets (SMTP password, database credentials).

Ensure tmp/, logs/, and uploaded files are properly handled in production.

Use HTTPS in production.

