# LaporFasum: Public Facility Reporting System

## Short Description
**LaporFasum** is a web-based platform designed to streamline the reporting of public facility damages such as roads, streetlights, and drainage systems. The system provides an accessible reporting channel for citizens, allowing reports to be routed to specific government agencies and assigned directly to field officers for repair. It features GPS-based location tagging and real-time status tracking to ensure transparency and efficiency in infrastructure maintenance.

## Team
* **Reza Muthahhari Purnomo (F1D02410088)**       - Project Leader: Backend Developer
* **Lalu Taufik Dewo Bayuaji (F1D02410069)**      - Team Member:    Frontend Developer
* **Halis Ibrahim Kumala Chandra (F1D02410049)**  - Team Member:    Frontend Developer

## Sitemap and Features

### 1. Citizen Portal (Public)
* **Create Report:** Interface for submitting new reports.
* **Photo Upload:** Attach images of facility damage.
* **GPS Tagging:** Integration with Leaflet.js and GPS for precise coordinates.

### 2. Admins
* **Report Management:** Overview and monitoring of all incoming infrastructure reports.
* **Task Routing & Assignment:** Forwarding reports to specific agencies or assigning them directly to field officers.
* **Verification:** Reviewing and approving/rejecting completion proofs submitted by officers.
* **Internal Messaging & Notifications:** Send and receive formal messages or feedback regarding report progress and task assignments.

### 3. Field Officers
* **Task List:** View and manage assigned repair tasks.
* **Message & Notification Center:** Access instructions, task feedback, and internal messages from administrators via an integrated interface.
* **Evidence Submission:** Upload photos of completed repairs for administrative verification.

## Tech Stack
* **Language:** PHP (Native)
* **Backend:** PHP 8.2+
* **Frontend:** HTML5, CSS3, JavaScript
* **Maps API:** Leaflet.js & OpenStreetMap
* **Database:** MySQL

## DBMS Configuration & Table Specification

### Configuration
* **Database Name:** `db_lapor_fasum`

### Table Specification
#### 1. `users`
Stores all account credentials and roles.
* `id_user` Primary Key
* `nama_lengkap` 
* `username`
* `password` 
* `role` 

#### 2. `instansi`
List of government agencies.
* `id_instansi` Primary Key
* `nama_instansi` 

#### 3. `kategori`
Damage categories linked to specific agencies.
* `id_kategori` Primary Key
* `nama_kategori`
* `id_instansi` 

#### 4. `laporan`
The core table for reporting data.
* `id_laporan` Primary Key
* `tanggal_lapor` 
* `foto` 
* `keluhan` 
* `id_kategori`
* `latitude`, `longitude` 
* `status`
* `id_petugas` 
* `foto_bukti`
* `pesan_admin` 

#### 5. `riwayat_laporan`
Audit logs and notification data.
* `id_riwayat` Primary Key
* `id_laporan` 
* `id_user`
* `id_petugas_penerima` 
* `aksi` 
* `keterangan` 
* `tanggal_aksi` 

#### 6. `admin`
Stores the specific assignments of Central Operators and Agency Administrators to their respective agencies.
* `id_admin` Primary Key
* `id_user` 
* `id_instansi`

#### 7. `petugas`
Stores the specific assignments of Field Officers to their respective agencies.
* `id_petugas` Primary Key
* `id_user`
* `id_instansi` 
