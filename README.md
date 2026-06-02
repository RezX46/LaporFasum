# LaporFasum: Public Facility Reporting System

## Overview

LaporFasum is a web-based application designed to facilitate the reporting and management of public facility damages such as damaged roads, broken streetlights, drainage issues, and other public infrastructure problems. The system enables citizens to submit reports with photos and GPS locations, which are then routed to the responsible government agency for handling.

The application supports report management, officer assignment, repair verification, notifications, and personnel management within each agency.

---

## Team

| Name | Student ID | Role |
|--------|------------|--------|
| Reza Muthahhari Purnomo | F1D02410088 | Project Leader & Backend Developer |
| Lalu Taufik Dewo Bayuaji | F1D02410069 | Frontend Developer |
| Halis Ibrahim Kumala Chandra | F1D02410049 | Frontend Developer |

---

## Key Features

### Public Users
- Submit public facility damage reports.
- Upload supporting photos as evidence.
- Select locations using GPS coordinates or manual address input.
- Receive a tracking code for report monitoring.
- Track report progress through the reporting system.

### Administrators
- View and manage incoming reports.
- Assign reports to field officers.
- Verify repair completion evidence.
- Manage personnel accounts.
- Manage agency-specific categories.
- Send and receive notifications.
- Approve account profile updates.
- Activate or deactivate field officers accounts.

### Field Officers
- View assigned repair tasks.
- Upload repair completion evidence.
- Receive notifications from administrators.
- Monitor task status and progress.

---

## Report Workflow

1. A citizen submits a public facility report.
2. The system generates a tracking code and stores the report with a **Pending** status.
3. The responsible administrator reviews the report.
4. The administrator assigns the report to a field officer.
5. The report status changes to **In Progress**.
6. The field officer performs the repair and uploads completion evidence.
7. The administrator reviews the submitted evidence.
8. The report is marked as **Completed** or returned for revision.

---

## Technology Stack

### Backend
- PHP Native
- PHP 8+

### Frontend
- HTML5
- CSS3
- JavaScript

### Database
- MySQL

### Mapping Service
- Leaflet.js
- OpenStreetMap

### AI Usage
- Google Gemini
- Antigravity

---

## Project Structure

```text
LaporFasum/
│
├── index.php
├── login.php
├── logout.php
├── cek_status.php
├── lapor.php
│
├── dashboard_admin.php
├── dashboard_petugas.php
│
├── personil.php
├── tambah_personil.php
├── edit_personil.php
│
├── kategori.php
├── instansi.php
│
├── detail_laporan.php
├── validasi_laporan.php
│
├── helper_notif.php
├── helper_gambar.php
├── koneksi.php
│
├── assets/
│   ├── css/
|       ├── style.css
│   ├── js/
|       ├── notif.js
│   ├── img/
│   └── uploads/
│
└── database/
    └── db_lapor_fasum.sql
```

---

## Database Schema

### users

Stores administrator and field officer accounts.

| Field | Description |
|---------|-------------|
| id_user | Primary Key |
| nama_lengkap | User's full name |
| username | Login username |
| password | Hashed password |
| role | admin / petugas |
| id_instansi | Assigned agency |
| foto_profil | Profile picture |
| status_akun | Active or inactive account |
| pending_nama | Requested name change |
| pending_username | Requested username change |

---

### instansi

Stores government agencies responsible for handling reports.

| Field |
|---------|
| id_instansi |
| nama_instansi |

---

### kategori

Stores report categories and their responsible agencies.

| Field |
|---------|
| id_kategori |
| nama_kategori |
| id_instansi |

---

### laporan

Stores all public facility reports submitted by citizens.

| Field |
|---------|
| id_laporan |
| kode_lacak |
| tanggal_lapor |
| foto |
| keluhan |
| id_kategori |
| metode_lokasi |
| latitude |
| longitude |
| alamat_manual |
| status |
| id_petugas |
| foto_bukti |
| pesan_admin |

---

### riwayat_laporan

Stores activity logs and report history records to track every important action performed on a report throughout its lifecycle. This table supports transparency, accountability, and notification generation.

| Field | Description |
|---------|-------------|
| id_riwayat | Primary Key |
| id_laporan | Related report ID |
| id_user | User who performed the action |
| id_petugas_penerima | Assigned field officer (if applicable) |
| aksi | Type of action performed |
| keterangan | Additional action details |
| tanggal_aksi | Timestamp of the action |

---

### notifikasi

Stores notifications for administrators and field officers.

| Field |
|---------|
| id_notifikasi |
| id_user |
| id_laporan |
| judul |
| pesan |
| kategori_notif |
| is_read |
| tanggal |

---

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
```

### 2. Import the Database

Import the provided database file into phpMyAdmin.

Database name:

```text
db_lapor_fasum
```

---

### 3. Enable PHP GD Library

Open the PHP configuration file:

```text
xampp/php/php.ini
```

Find the following line:

```ini
;extension=gd
```

Remove the semicolon:

```ini
extension=gd
```

Save the file and restart Apache from the XAMPP Control Panel.

---

### 4. Configure Database Connection

Open:

```php
koneksi.php
```

Configure your database credentials:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "db_lapor_fasum";
```

---

### 5. Move the Project

Place the project folder inside:

```text
xampp/htdocs/
```

---

### 6. Start Services

Start Apache and MySQL using the XAMPP Control Panel.

---

### 7. Access the Application

Open your browser and navigate to:

```text
http://localhost/LaporFasum
```

---

## User Roles

| Role | Description |
|--------|-------------|
| Administrator | Manages reports, personnel, categories, assignments, validations, and notifications. |
| Field Officer | Handles assigned repair tasks and uploads repair evidence. |
| Public User | Submits reports and tracks report progress using a tracking code. |

