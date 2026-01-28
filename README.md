Dokumentasi Project SiPentol JECA

1. Ringkasan

SiPentol adalah web app berbasis CakePHP 5 untuk mengelola alur pendaftaran peserta LPK JECA dari sisi User dan Admin.

Tujuan utama sistem:

User bisa registrasi, verifikasi, login, mengisi form pendaftaran, mengikuti tes online, melakukan daftar ulang setelah lulus, dan onboarding.

Admin bisa memonitor data user, data pendaftaran, tes online, daftar ulang, pengumuman, serta mengirim email (mailing) dan mencatat riwayatnya.

2. Teknologi & Stack

Backend: CakePHP 5 (PHP 8.x)

Auth: cakephp/authentication (session-based)

Database: MySQL/MariaDB

UI: TailwindCSS (CDN atau build sesuai implementasi)

Email: Cake Mailer (SMTP / transport default)

Upload files: melalui CakePHP + filesystem (WWW_ROOT/uploads/...)

3. Struktur Sistem (Role & Akses)
   A. Role: User (Front-site)

Fitur inti user:

Register + verifikasi (OTP/email) (sesuai implementasi di project)

Login

Dashboard user

Form Pendaftaran

Tes Online

Daftar Ulang

Onboarding

Logout

B. Role: Admin (Admin Panel)

Fitur inti admin:

Dashboard admin (ringkasan sistem sesuai kebutuhan)

Users (kelola data user)

Pendaftaran (lihat detail pendaftar + file upload)

Tes Online (buat/atur jadwal tes & status tes)

Daftar Ulang (review berkas, verifikasi/need fix, buka akses)

Pengumuman (broadcast / targeted)

Mailing (kirim email ke user tertentu/semua + riwayat)

Logout

4. Alur Bisnis (Workflow Utama)
   4.1 Alur User (High-Level)

Register → Verifikasi → Login

Isi Form Pendaftaran

Setelah submit pendaftaran → status user berubah menuju menunggu_tes

Admin memberikan akses tes/jadwal → status user menjadi tes

Setelah tes selesai / status tes closed → status user menjadi menunggu_hasil

Jika lulus → admin membuka akses daftar ulang → user bisa masuk Daftar Ulang

User upload berkas daftar ulang → admin review:

Verified → user status aktif

Need Fix → user diminta perbaiki berkas

4.2 Logika Status User (yang sudah kamu pakai di DashboardController)

Status yang digunakan (contoh yang muncul di code):

pendaftaran

menunggu_tes

tes

menunggu_hasil

lulus_tes

daftar_ulang

need_fix (via DU status)

onboarding

aktif

Catatan: status bisa berasal dari gabungan:

data pendaftarans

data online_tests

data daftar_ulangs

legacy users.status

5. Routing Utama (Front-site)

Contoh route mapping yang kamu pakai:

/ → Users@login

/register → Users@register

/dashboard → Dashboard@index

/pendaftaran → Pendaftarans@form

/tes-online → OnlineTests@index

/daftar-ulang → DaftarUlangs@form

/daftar-ulang/draft/{type} → DaftarUlangs@downloadDraft

/onboarding → Onboarding@index

/forgot-password → Users@forgotPassword

/reset-password/\* → Users@resetPassword

Admin panel biasanya via prefix /admin/... (atau sesuai struktur project kamu).

6. Modul-Modul Penting (Controller)
   6.1 UsersController (User)

Umumnya menangani:

register

verify (OTP)

login

logout

forgotPassword

resetPassword

Catatan implementasi login: sudah pernah ada kasus FAILURE_IDENTITY_NOT_FOUND meskipun password verify YES; itu sudah teratasi lewat perbaikan routing/scope dan handling auth.

6.2 DashboardController (User)

Fungsi utama:

Menghitung status user terbaru berdasarkan data pendukung:

Pendaftaran (pendaftarans)

Tes Online (online_tests)

Daftar Ulang (daftar_ulangs)

Menyimpan status ke users.status agar sinkron di semua halaman

Mengambil pengumuman aktif (target semua / tertentu)

Bug yang sudah ditemukan dan diperbaiki:

Error: Call to undefined method Cake\I18n\DateTime::gt()

Solusi: pastikan $now dan $end adalah FrozenTime / method yang valid (gt() tersedia di FrozenTime).

6.3 DaftarUlangsController (User)

Fungsi utama:

Mengunci form jika user belum memenuhi syarat (status belum lulus_tes dsb).

Tidak auto-create record DU jika belum boleh.

Jika sudah boleh → create draft DU jika belum ada.

Upload file:

formulir_pendaftaran_pdf

surat_perjanjian_pdf

surat_persetujuan_orangtua_pdf

bukti_pembayaran_img

Lokasi upload:

webroot/uploads/daftar-ulang/{userId}/...

6.4 Admin\DaftarUlangsController (Admin)

Fungsi utama:

List + search + pagination

View detail, cek kelengkapan

Aksi:

verify → DU verified, user status aktif

needFix → DU need_fix + admin_note, user status daftar_ulang

openAccess → user status lulus_tes (membuka akses form DU)

Catatan penting:

Pernah ada error RecordNotFoundException di admin DU. Biasanya karena get($id) dipanggil untuk ID yang tidak ada atau link ID salah.

6.5 MailingController (Admin)

Fungsi utama:

Index riwayat mailings

Add/kirim email ke:

semua user

user tertentu

View detail mailing + list penerima

Masalah yang sempat terjadi:

Kolom penerima tidak sinkron karena field yang dibaca/ditulis tidak sama.

Di DB kamu ada kolom: target_user_ids, sent_total, sent_success, sent_failed, failed_emails, dll.

Pastikan controller menyimpan ke kolom yang benar (target_user_ids, bukan user_ids_json, dll).

7. Database (Tabel-Tabel Kunci)
   7.1 users

Field penting (minimal):

id

email

password

nama_lengkap

status

is_verified

is_active

7.2 pendaftarans

Contoh field yang terlihat:

id, user_id

nik, nama_lengkap, jenis_kelamin, tanggal_lahir, usia

tinggi_badan, berat_badan

alamat_lengkap, domisili_saat_ini

pendidikan_jenjang, pendidikan_instansi, pendidikan_jurusan

file upload (pas foto/ktp/ijazah/transkrip) sesuai implementasi

7.3 online_tests

Field yang terlihat:

id, user_id

status (waiting/closed)

test_url, test_access_id

test_location_type, test_location_detail

schedule_start, schedule_end

admin_note

created, modified

7.4 daftar_ulangs

Field yang terlihat:

id, user_id

formulir_pendaftaran_pdf

bukti_pembayaran_img

surat_perjanjian_pdf

surat_persetujuan_orangtua_pdf

status (draft/need_fix/verified)

admin_note

created, modified

7.5 pengumuman

Field umum:

id, title, body, is_active

target (semua/tertentu)

target_user_ids

7.6 mailings

Field yang terlihat dari DB:

id

subject

body_html

target (semua/tertentu)

target_user_ids

sent_total, sent_success, sent_failed

failed_emails

created_at

created_by_admin_id

8. Konfigurasi Environment (Contoh)

Sesuaikan dengan hosting kamu.

8.1 Database

config/app_local.php:

host, username, password, database

8.2 Email SMTP

config/app_local.php / EmailTransport:

host smtp

port

username/password

tls/ssl

9. Deployment Notes (Hosting)

Kamu deploy di domain:

sipentol.jecaid.com

Yang wajib dicek di hosting:

webroot mengarah ke public_html/webroot (atau rewrite CakePHP benar)

permission folder:

tmp/

logs/

webroot/uploads/

PHP extension: intl, mbstring, openssl, pdo_mysql

10. UAT / Software Testing (Rangkuman)

Kamu sudah punya tabel UAT lengkap untuk staff non-IT:

User UAT: register/login, pendaftaran, tes online, daftar ulang, onboarding, logout

Admin UAT: users, pendaftaran, tes online, daftar ulang (verify/need fix/open access), pengumuman, mailing

11. Checklist Stabilitas (Sebelum Go-Live)

Login user/admin stabil (tidak loop, tidak failure identity)

Routing scope('/') tidak bentrok dengan fallbacks()

Dashboard tidak memanggil method time yang tidak ada (gt, lt harus sesuai object)

Admin Daftar Ulang tidak 500 saat ID invalid (handle not found)

Mailings menyimpan target_user_ids dan counters benar

Semua link file upload tidak 404 (path relative + permission)
