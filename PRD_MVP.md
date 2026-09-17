# 📄 Product Requirements Document (PRD)

**Nama Produk:** NetMon Enterprise (Aplikasi Monitoring Perangkat Jaringan)  
**Dokumen Status:** Final  
**Fase:** MVP (Minimum Viable Product)

---

## 1. Ringkasan Produk (Product Summary)
**NetMon Enterprise** adalah aplikasi internal berbasis web yang dirancang untuk memantau kesehatan, ketersediaan, dan kinerja perangkat jaringan perusahaan (seperti *router*, *switch*, *firewall*, dan *access point*) secara *real-time*. 

Saat ini, tim IT sering kali baru mengetahui adanya masalah jaringan setelah mendapat keluhan dari karyawan (reaktif). Aplikasi ini akan mengubah pendekatan tersebut menjadi proaktif dengan menyediakan dasbor terpusat, analitik kinerja, dan sistem peringatan dini (alerting) otomatis. Produk ini bertujuan untuk meminimalkan *downtime*, meningkatkan produktivitas operasional perusahaan, dan menyederhanakan manajemen aset jaringan.

---

## 2. Tujuan & Metrik Keberhasilan (Goal & Success Metrics)

**Tujuan Utama (Goals):**
*   **Proaktif vs Reaktif:** Mendeteksi masalah atau anomali jaringan sebelum berdampak luas pada pengguna akhir.
*   **Efisiensi Waktu:** Mempercepat proses investigasi (*troubleshooting*) oleh tim *Network Engineer*.
*   **Transparansi Sistem:** Memberikan visibilitas penuh terkait status infrastruktur IT kepada manajemen.

**Metrik Keberhasilan (Success Metrics):**

| Metrik | Target Keberhasilan |
| :--- | :--- |
| **MTTD (Mean Time to Detect)** | Mengurangi waktu deteksi gangguan jaringan menjadi di bawah 3 menit. |
| **MTTR (Mean Time to Resolve)** | Menurunkan waktu pemulihan masalah sebesar 30% pada kuartal pertama setelah peluncuran. |
| **System Uptime** | Membantu mempertahankan *Service Level Agreement* (SLA) jaringan internal di angka 99,9%. |
| **Adoption Rate** | 100% tim *Network Operations Center* (NOC) dan IT Support aktif menggunakan aplikasi ini setiap hari. |

---

## 3. Target Pengguna (Target Audience / User Persona)

*   **Network Engineer / NOC:** Memastikan jaringan berjalan lancar 24/7 dan memperbaiki gangguan teknis. Membutuhkan peringatan *real-time* yang akurat, metrik mendetail, dan *log* status perangkat.
*   **IT Manager:** Mengawasi operasional IT, merencanakan kapasitas, dan melaporkan SLA ke manajemen atas. Membutuhkan dasbor tingkat tinggi (*high-level*), laporan *uptime* bulanan, dan ringkasan kesehatan jaringan keseluruhan.
*   **IT Helpdesk:** Menerima keluhan dari karyawan dan memberikan informasi awal. Membutuhkan tampilan sederhana untuk melihat perangkat mana yang sedang *down*.

---

## 4. User Stories & Fitur Utama (Lingkup MVP)

**A. User Login Akses & Role**
*   *User Story:* Sebagai IT Manager, saya ingin melihat ringkasan status semua perangkat di satu layar, sehingga saya tahu kondisi jaringan saat ini, saya juga bisa melakukan manajemen perangkat, Peta Topologi Jaringan Interaktif, Peringatan Otomatis.
*   *Fitur MVP:* Role yang tersedia superadmin, Manager, NOC, IT Support dan juga dengan konsep RBAC.

**B. Dasbor & Visibilitas (Dashboard & Visibility)**
*   *User Story:* Sebagai IT Manager, saya ingin melihat ringkasan status semua perangkat di satu layar, sehingga saya tahu kondisi jaringan saat ini.
*   *Fitur MVP:* Dasbor interaktif dengan indikator warna (Hijau = Normal, Kuning = Warning, Merah = Down).

**C. Manajemen Perangkat (Device Inventory)**
*   *User Story:* Sebagai Network Engineer, saya ingin dapat menambah, mengedit, atau menghapus perangkat menggunakan IP Address dan kredensial SNMP.
*   *Fitur MVP:* Halaman CRUD untuk perangkat. Mendukung protokol standar seperti ICMP dan SNMP.

**D. Peta Topologi Jaringan Interaktif (Interactive Topology Map)**
*   *User Story:* Sebagai Network Engineer, saya ingin melihat pemetaan visual dari infrastruktur jaringan (seperti topologi dual-ISP, *link fiber optic* antar gedung, dan penempatan *switch*), sehingga saya dapat dengan cepat melokalisasi letak titik kegagalan (*point of failure*) saat terjadi gangguan.
*   *Fitur MVP:* Peta topologi jaringan dinamis yang menampilkan koneksi *node* antar perangkat (misal: router Mikrotik RB750Gr3, *core switch*) secara interaktif beserta status *up/down* pada masing-masing jalur komunikasi.

**E. Peringatan Otomatis (Real-time Alerting)**
*   *User Story:* Sebagai NOC, saya ingin menerima notifikasi instan jika sebuah perangkat mati atau utilitas CPU melampaui batas wajar.
*   *Fitur MVP:* Integrasi notifikasi via Email dan Telegram berdasarkan batas ambang yang bisa diatur.

**F. Pemantauan Kinerja (Performance Metrics)**
*   *User Story:* Sebagai Network Engineer, saya ingin melihat grafik lalu lintas data dan utilisasi perangkat untuk menganalisis masalah jaringan.
*   *Fitur MVP:* Grafik riwayat metrik (Ping *latency*, *packet loss*, CPU, Memory) selama 24 jam terakhir.

**G. SLA (Service Level Agreement)**
*   *User Story:* Sebagai seorang IT Manager, saya ingin mengetahui SLA dari setiap device baik itu secara harian, weekly, monthly dan yearly, dan juga saya bisa melihat SLA secara total dari seluruh perangkat, SLA ini ditampilkan dalam bentuk percentile 99,99% dan data bisa di export ke dalam file excel untuk laporan ke pihak manajemen.
*   *Fitur MVP:* list tabel hitungan Downtime dan uptime dalam jam dan persen dan total dalam bentuk jam dan persen juga.

---

## 5. Alur Pengguna (User Flow)

**Skenario: Insiden Jaringan (Penanganan Alert)**
1.  **Deteksi:** Sistem melakukan *polling* dan mendeteksi bahwa *switch* utama atau *link fiber optic* tidak merespons.
2.  **Alert:** Aplikasi mengubah status jalur/perangkat menjadi "Merah" di dasbor topologi dan mengirim notifikasi Telegram ke tim NOC.
3.  **Investigasi:** Teknisi mengklik tautan *alert*, melihat detail perangkat, dan meninjau posisi kegagalan di peta topologi interaktif.
4.  **Tindakan:** Teknisi mengakui (*Acknowledge*) *alert* dan memulai perbaikan via *remote access* terminal (misal via Tailscale).
5.  **Pemulihan:** Sistem mendeteksi perangkat kembali *Up*, mengubah status menjadi "Hijau", dan mengirimkan notifikasi *Recovery*.

---

## 6. Batasan & Hal yang Tidak Masuk Lingkup (Out of Scope)

1.  **Network Configuration Management:** Aplikasi ini hanya bersifat *Read-Only* (memantau). Aplikasi tidak akan digunakan untuk mengubah konfigurasi perangkat secara otomatis (seperti *push script* atau manipulasi *routing*).
2.  **Deep Packet Inspection (DPI):** Aplikasi tidak menganalisis isi paket data secara granular atau memantau log aplikasi web.
3.  **Manajemen Penagihan (Billing/Cost Tracking):** Tidak ada fitur kalkulasi biaya terkait tagihan penggunaan internet atau limit kuota dari ISP di versi MVP ini.

---
**Informasi Persetujuan:**
*   **Penyusun:** M Gudie Fithlail Nst
*   **Tanggal Review:** 16 September 2026
*   **Stakeholder Utama:** M Gudie Fithlail Nst
