import os
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    """Sets background color of a cell."""
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=120, bottom=120, left=150, right=150):
    """Sets cell margins in twips (1 pt = 20 twips)."""
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'''
        <w:tcMar {nsdecls("w")}>
            <w:top w:w="{top}" w:type="dxa"/>
            <w:bottom w:w="{bottom}" w:type="dxa"/>
            <w:left w:w="{left}" w:type="dxa"/>
            <w:right w:w="{right}" w:type="dxa"/>
        </w:tcMar>
    ''')
    tcPr.append(tcMar)

def set_table_borders(table, color="CCCCCC", sz="4", val="single"):
    """Sets thin subtle borders for the table."""
    tblPr = table._tbl.tblPr
    borders = parse_xml(f'''
        <w:tblBorders {nsdecls("w")}>
            <w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
            <w:left w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
            <w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
            <w:right w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
            <w:insideH w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
            <w:insideV w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
        </w:tblBorders>
    ''')
    tblPr.append(borders)

def make_callout(doc, title, text, bg_color="F0FDF4", border_color="16A34A"):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    set_cell_background(cell, bg_color)
    set_cell_margins(cell, top=160, bottom=160, left=200, right=200)
    
    # Left thick border
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(f'''
        <w:tcBorders {nsdecls("w")}>
            <w:top w:val="none"/>
            <w:left w:val="single" w:sz="24" w:space="0" w:color="{border_color}"/>
            <w:bottom w:val="none"/>
            <w:right w:val="none"/>
        </w:tcBorders>
    ''')
    tcPr.append(borders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    run_t = p.add_run(f"📌 {title}: ")
    run_t.bold = True
    run_t.font.name = "Calibri"
    run_t.font.size = Pt(10)
    run_t.font.color.rgb = RGBColor(30, 41, 59)
    
    run_txt = p.add_run(text)
    run_txt.font.name = "Calibri"
    run_txt.font.size = Pt(9.5)
    run_txt.font.color.rgb = RGBColor(71, 85, 105)
    
    doc.add_paragraph() # spacing

def format_row(row, bg_hex, is_header=False, col_widths=None):
    for i, cell in enumerate(row.cells):
        set_cell_background(cell, bg_hex)
        set_cell_margins(cell, top=100, bottom=100, left=140, right=140)
        cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
        if col_widths and i < len(col_widths):
            cell.width = col_widths[i]
        for p in cell.paragraphs:
            p.paragraph_format.space_before = Pt(1)
            p.paragraph_format.space_after = Pt(1)
            p.paragraph_format.line_spacing = 1.15
            for r in p.runs:
                r.font.name = "Calibri"
                if is_header:
                    r.font.size = Pt(9.5)
                    r.bold = True
                    r.font.color.rgb = RGBColor(255, 255, 255)
                else:
                    r.font.size = Pt(9)

def main():
    doc = Document()
    
    # Page setup - A4 Portrait with 1 inch margin
    for section in doc.sections:
        section.page_width = Inches(8.27)  # A4 width
        section.page_height = Inches(11.69) # A4 height
        section.top_margin = Inches(0.8)
        section.bottom_margin = Inches(0.8)
        section.left_margin = Inches(0.8)
        section.right_margin = Inches(0.8)

    # Styles
    # Title
    title_p = doc.add_paragraph()
    title_p.paragraph_format.space_before = Pt(0)
    title_p.paragraph_format.space_after = Pt(4)
    title_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_title = title_p.add_run("TABEL PENJELASAN ALUR SISTEM INFORMASI\nPEMERIKSAAN KESEHATAN MAHASISWA (SIPKESMA)")
    r_title.bold = True
    r_title.font.name = "Calibri"
    r_title.font.size = Pt(18)
    r_title.font.color.rgb = RGBColor(16, 42, 67) # Navy

    sub_p = doc.add_paragraph()
    sub_p.paragraph_format.space_before = Pt(2)
    sub_p.paragraph_format.space_after = Pt(16)
    sub_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_sub = sub_p.add_run("Dokumentasi Alur Bisnis, Peran Aktor (Mahasiswa Baru & Admin/Petugas), dan Siklus Status Sistem\nUniversitas / Institusi Pendidikan — Berbasis Web Framework Laravel")
    r_sub.font.name = "Calibri"
    r_sub.font.size = Pt(11)
    r_sub.font.italic = True
    r_sub.font.color.rgb = RGBColor(71, 85, 105)

    make_callout(
        doc,
        "Tentang Dokumen Ini",
        "Dokumen ini memuat tabel penjelasan alur sistem SIPKESMA secara komprehensif, mencakup 2 peran utama (Mahasiswa Baru dan Admin/Petugas Medis), pemetaan tahapan operasional, interaksi antar-peran, transisi status registrasi, hingga pemetaan fungsi teknis sistem. Format ini dirancang khusus agar rapi, mudah dibaca, dan siap digunakan untuk laporan teknis, skripsi, maupun materi presentasi.",
        bg_color="EFF6FF",
        border_color="2563EB"
    )

    # =========================================================================
    # BAGIAN 1: TABEL RINGKASAN ALUR SISTEM END-TO-END (FLOWCHART SUMMARY)
    # =========================================================================
    h1 = doc.add_paragraph()
    h1.paragraph_format.space_before = Pt(14)
    h1.paragraph_format.space_after = Pt(6)
    r = h1.add_run("1. Tabel Ringkasan Alur Sistem End-to-End SIPKESMA")
    r.bold = True
    r.font.name = "Calibri"
    r.font.size = Pt(14)
    r.font.color.rgb = RGBColor(16, 42, 67)

    desc1 = doc.add_paragraph("Tabel berikut menyajikan urutan proses utama dari awal mahasiswa mengakses sistem hingga penerbitan sertifikat kesehatan digital:")
    desc1.paragraph_format.space_after = Pt(8)

    t1_headers = ["No", "Tahapan Alur", "Aktor Utama", "Input / Aksi Aktor", "Proses & Validasi Sistem", "Output / Hasil", "Status Registrasi"]
    t1_data = [
        [
            "1",
            "Akses & Autentikasi (Landing Page & Login)",
            "Mahasiswa Baru &\nAdmin",
            "Mengakses URL web, memilih opsi Login (Email & Password atau Google OAuth) atau Register akun baru.",
            "Sistem memvalidasi kredensial, mendeteksi role pengguna (student vs staff/admin), dan mengarahkan ke dashboard masing-masing.",
            "Akses masuk ke Dashboard Mahasiswa atau Dashboard Petugas/Admin.",
            "registered / -"
        ],
        [
            "2",
            "Pengisian Data Diri / Profil",
            "Mahasiswa Baru",
            "Mengisi formulir biodata (NIM, Nama, Fakultas, Prodi, Jalur Masuk, Jenis Kelamin, No HP, Alamat).",
            "Validasi kelengkapan data & format NIM. Menyimpan data profil ke tabel `student_profiles`.",
            "Profil mahasiswa tersimpan lengkap (100%). Notifikasi berhasil disimpan.",
            "profile_completed"
        ],
        [
            "3",
            "Pengisian Riwayat Medis & BMI",
            "Mahasiswa Baru",
            "Menginput golongan darah, tinggi badan (TB), berat badan (BB), riwayat penyakit, riwayat alergi, riwayat pengobatan.",
            "Sistem menghitung Indeks Massa Tubuh (BMI = BB / TB^2) secara otomatis, menetapkan kategori BMI (Normal/Overweight/Underweight), dan menyimpan ke `health_histories`.",
            "Kartu data riwayat kesehatan terekam dengan nilai dan kategori BMI otomatis.",
            "health_completed"
        ],
        [
            "4",
            "Pemilihan Jadwal Sesi Skrining",
            "Mahasiswa Baru",
            "Memilih tanggal dan sesi jam pemeriksaan kesehatan yang masih memiliki kuota.",
            "Sistem mengecek ketersediaan kuota real-time pada `test_sessions`. Mencegah over-capacity.",
            "Jadwal sesi tes terkunci sementara untuk mahasiswa terkait.",
            "scheduled"
        ],
        [
            "5",
            "Konfirmasi & Kunci Pendaftaran",
            "Mahasiswa Baru",
            "Melakukan review ringkasan biodata, riwayat kesehatan, dan jadwal; kemudian menekan tombol 'Kunci Pendaftaran'.",
            "Sistem mengunci data pendaftaran (read-only) agar data valid tidak dapat diubah lagi secara mandiri oleh mahasiswa.",
            "Status registrasi dikunci; rincian tagihan dan nomor rekening pembayaran ditampilkan.",
            "locked"
        ],
        [
            "6",
            "Pembayaran & Upload Bukti Transfer",
            "Mahasiswa Baru",
            "Melakukan transfer biaya pemeriksaan dan mengunggah foto/file bukti transfer (struk/screenshot m-banking).",
            "Sistem memvalidasi ekstensi file (JPG/PNG/PDF), mengunggah ke direktori storage, dan mencatat transaksi ke tabel `payments`.",
            "File bukti transfer tersimpan; notifikasi status menunggu verifikasi muncul.",
            "pending_verification"
        ],
        [
            "7",
            "Verifikasi Pembayaran",
            "Admin / Petugas",
            "Admin memeriksa kesesuaian nominal, rekening tujuan, dan bukti transfer melalui menu Verifikasi Pembayaran.",
            "Admin memilih tombol Approve atau Reject:\n- Jika Approve: Sistem men-generate Nomor Antrean unik (Queue Code) dan mengaktifkan tiket.\n- Jika Reject: Menginput catatan alasan tolak & mengembalikan status agar upload ulang.",
            "Status pembayaran disetujui (cleared) atau ditolak (awaiting_payment).",
            "cleared (jika lolos)\natau locked (jika ditolak)"
        ],
        [
            "8",
            "Penerbitan Tiket Antrean & QR Digital",
            "Mahasiswa Baru",
            "Melihat tampilan tiket antrean di portal dan mengunduh berkas e-Tiket fisik dalam format PDF.",
            "Sistem meng-generate QR Code berenkode token registrasi yang memuat data peserta dan sesi jadwal.",
            "File PDF e-Tiket siap cetak/disimpan di smartphone untuk dibawa saat hari pelaksanaan tes.",
            "cleared"
        ],
        [
            "9",
            "Check-in di Klinik Kampus",
            "Mahasiswa Baru &\nPetugas/Admin",
            "Mahasiswa hadir di lokasi tes dan menunjukkan QR Code tiket kepada petugas meja registrasi.",
            "Petugas memindai QR Code via scanner/kamera. Sistem memvalidasi data registrasi dan mengubah status kehadiran menjadi checked-in.",
            "Mahasiswa resmi tercatat hadir di klinik dan masuk antrean panggilan stasiun pemeriksaan.",
            "checked_in"
        ],
        [
            "10",
            "Pemeriksaan Medis (3 Stasiun)",
            "Mahasiswa Baru &\nPetugas/Admin",
            "Mahasiswa menjalani 3 pos pemeriksaan:\n1. Stasiun Urin\n2. Stasiun NAPZA / Narkoba\n3. Stasiun Pemeriksaan Fisik",
            "Petugas medis menginput hasil klinis tiap stasiun ke sistem:\n- Urin (pH, protein, leukosit, glukosa)\n- NAPZA (Amphetamine, THC, Morphine, dll)\n- Fisik (Tekanan darah, denyut nadi, buta warna, visus mata, catatan dokter).",
            "Data rekam medis skrining tersimpan pada `station_results`. Progres stasiun terpantau real-time oleh mahasiswa.",
            "in_progress"
        ],
        [
            "11",
            "Evaluasi Kelulusan & Penyelesaian",
            "Admin / Dokter",
            "Mengevaluasi hasil keseluruhan dari ketiga stasiun skrining.",
            "Sistem memverifikasi apakah semua stasiun berstatus Lulus (Pass). Admin menekan konfirmasi Selesai (Completed) atau Follow-up jika butuh penanganan lanjut.",
            "Status akhir registrasi diubah menjadi Completed atau Follow-up.",
            "completed / follow_up"
        ],
        [
            "12",
            "Penerbitan & Unduh Sertifikat Digital",
            "Mahasiswa Baru &\nPublik / Verifikator",
            "Mahasiswa mengunduh e-Sertifikat Kesehatan (PDF). Pihak eksternal/verifikator memindai QR Code sertifikat.",
            "Sistem membuat dokumen PDF Sertifikat resmi ber-QR Code verifikasi unik. Endpoint publik memvalidasi keaslian dokumen.",
            "Sertifikat Kesehatan Digital resmi yang terotentikasi dan siap digunakan untuk syarat registrasi akademik kampus.",
            "completed"
        ]
    ]

    table1 = doc.add_table(rows=len(t1_data)+1, cols=len(t1_headers))
    table1.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table1)
    
    col_w1 = [Inches(0.4), Inches(1.3), Inches(1.0), Inches(1.5), Inches(1.6), Inches(1.3), Inches(0.9)]
    
    # Header
    for j, h_text in enumerate(t1_headers):
        table1.cell(0, j).paragraphs[0].text = h_text
    format_row(table1.rows[0], "1E3A8A", is_header=True, col_widths=col_w1)
    
    # Rows
    for i, row_data in enumerate(t1_data):
        row = table1.rows[i+1]
        for j, val in enumerate(row_data):
            row.cells[j].paragraphs[0].text = val
        bg = "F8FAFC" if i % 2 == 1 else "FFFFFF"
        format_row(row, bg, is_header=False, col_widths=col_w1)

    doc.add_page_break()

    # =========================================================================
    # BAGIAN 2: TABEL RINCIAN ALUR MAHASISWA BARU (ROLE: STUDENT)
    # =========================================================================
    h2 = doc.add_paragraph()
    h2.paragraph_format.space_before = Pt(8)
    h2.paragraph_format.space_after = Pt(6)
    r2 = h2.add_run("2. Tabel Rincian Alur Sistem — Peran: Mahasiswa Baru (Peserta Skrining)")
    r2.bold = True
    r2.font.name = "Calibri"
    r2.font.size = Pt(14)
    r2.font.color.rgb = RGBColor(16, 42, 67)

    desc2 = doc.add_paragraph("Bagian ini merinci 11 tahapan yang dilalui oleh mahasiswa baru secara berurutan pada portal SIPKESMA beserta validasi dan prasyaratnya:")
    desc2.paragraph_format.space_after = Pt(8)

    t2_headers = ["Tahap", "Aktivitas Mahasiswa", "Halaman / Menu", "Prasyarat Sistem", "Deskripsi Alur Operasional", "Tindakan Jika Terjadi Error / Tolak"]
    t2_data = [
        [
            "Tahap 1",
            "Pendaftaran Akun & Autentikasi",
            "/register\n/login\n/auth/google",
            "Memiliki email aktif dan belum terdaftar di sistem.",
            "1. Mahasiswa membuka halaman register.\n2. Mengisi Nama, Email, dan Password (atau klik tombol Google OAuth).\n3. Akun dibuat dengan role default 'student'.\n4. Sistem melakukan login otomatis dan mengarahkan ke halaman Beranda.",
            "Validasi error jika format email tidak sesuai atau email sudah pernah terdaftar."
        ],
        [
            "Tahap 2",
            "Pengisian Profil Mahasiswa",
            "/profil",
            "Sudah berhasil login ke dalam sistem.",
            "1. Mengisi formulir identitas: NIM, Nama Lengkap, Program Studi, Fakultas, Jenis Kelamin, Tempat/Tanggal Lahir, No Handphone, dan Alamat Domisili.\n2. Klik 'Simpan Profil'.\n3. Data tersimpan di basis data `student_profiles`.",
            "Jika NIM sudah terdaftar pada akun lain, sistem menolak dengan pesan duplikasi NIM."
        ],
        [
            "Tahap 3",
            "Pengisian Riwayat Kesehatan",
            "/riwayat-kesehatan",
            "Tahap 2 (Profil) telah tersimpan.",
            "1. Memilih Golongan Darah (A, B, AB, O, Rhesus).\n2. Mengisi Tinggi Badan (cm) dan Berat Badan (kg).\n3. Mengisi riwayat penyakit berat/menahun, riwayat operasi, riwayat konsumsi obat rutin, dan alergi makanan/obat.\n4. Sistem menghitung Indeks Massa Tubuh (BMI) secara otomatis.\n5. Klik 'Simpan Riwayat Medis'.",
            "Validasi numerik pada kolom TB dan BB (misal TB minimal 50 cm, BB minimal 20 kg)."
        ],
        [
            "Tahap 4",
            "Pemilihan Jadwal Sesi Skrining",
            "/jadwal",
            "Tahap 3 (Riwayat Medis) telah terisi.",
            "1. Mahasiswa melihat daftar tanggal dan sesi jam pemeriksaan kesehatan yang dibuka oleh panitia.\n2. Setiap sesi menampilkan kapasitas total dan sisa kuota yang tersedia.\n3. Mahasiswa memilih satu sesi dan menekan 'Pilih Jadwal Ini'.\n4. Sistem mencatat `test_session_id` ke dalam pendaftaran.",
            "Jika kuota sesi telah habis (0 sisa), tombol pemilihan otomatis terkunci/dinonaktifkan."
        ],
        [
            "Tahap 5",
            "Konfirmasi & Kunci Pendaftaran",
            "/konfirmasi",
            "Tahap 4 (Jadwal) telah terpilih.",
            "1. Sistem menampilkan halaman ringkasan menyeluruh: Biodata, Riwayat Medis, dan Jadwal Tes terpilih.\n2. Mahasiswa memeriksa kebenaran seluruh data.\n3. Memberikan centang pada pernyataan persetujuan kebenaran data.\n4. Menekan tombol 'Kunci Pendaftaran'.\n5. Status terkunci; perubahan data ditutup.",
            "Sistem memunculkan modal dialog peringatan bahwa setelah dikunci data tidak dapat disunting kembali."
        ],
        [
            "Tahap 6",
            "Pembayaran Biaya & Upload Bukti",
            "/pembayaran",
            "Tahap 5 (Pendaftaran) telah dikunci.",
            "1. Mahasiswa melihat rincian biaya skrining kesehatan dan nomor rekening bank universitas.\n2. Mahasiswa melakukan transfer bank / QRIS.\n3. Mengunggah berkas foto/scan bukti bayar (format JPG/PNG/PDF, maks 2MB).\n4. Mengisi nama pengirim dan tanggal transfer.\n5. Klik 'Kirim Bukti Pembayaran'.",
            "Validasi ukuran file (>2MB) atau ekstensi file tidak diizinkan. Sistem meminta upload ulang file yang valid."
        ],
        [
            "Tahap 7",
            "Menunggu Verifikasi Pembayaran",
            "/pembayaran\n/beranda",
            "Bukti pembayaran telah berhasil diunggah.",
            "1. Status registrasi berubah menjadi 'pending_verification'.\n2. Halaman menampilkan banner status bahwa berkas pembayaran sedang ditinjau oleh bendahara/admin.\n3. Mahasiswa dapat melihat preview bukti yang diunggah sembari menunggu persetujuan.",
            "Jika bukti bayar ditolak oleh admin (misal bukti buram/nominal salah), status kembali ke awaiting_payment dengan pesan koreksi dari admin."
        ],
        [
            "Tahap 8",
            "Perolehan Tiket Antrean & Download PDF",
            "/tiket\n/tiket/download",
            "Pembayaran telah disetujui (cleared) oleh admin.",
            "1. Sistem membuka kunci menu Tiket Antrean.\n2. Mahasiswa memperoleh Nomor Urut Antrean (Queue Code) dan QR Code unik peserta.\n3. Menampilkan rincian tanggal, sesi waktu, dan lokasi klinik.\n4. Mahasiswa dapat menekan tombol 'Download PDF Tiket' untuk dicetak.",
            "Menu tiket tidak dapat diakses sebelum status pembayaran resmi disetujui (status: cleared)."
        ],
        [
            "Tahap 9",
            "Check-in di Klinik Kampus",
            "/status",
            "Tiba di klinik kampus pada hari dan jam sesi terpilih.",
            "1. Mahasiswa datang ke klinik membawa tiket (fisik atau tampilan smartphone).\n2. Menyerahkan QR Code kepada petugas di Meja Check-in.\n3. Petugas memindai QR Code.\n4. Status registrasi berubah menjadi 'checked_in'.\n5. Mahasiswa diarahkan menuju ruang tunggu pemeriksaan.",
            "Jika mahasiswa salah datang jadwal/sesi, scanner admin akan menampilkan peringatan perbedaan sesi jadwal."
        ],
        [
            "Tahap 10",
            "Pemeriksaan 3 Stasiun Medis & Pantau Status",
            "/status",
            "Telah berhasil check-in di meja klinik.",
            "1. Mahasiswa masuk ke pos pemeriksaan bergiliran:\n   - Pos 1: Pengambilan & Uji Urin\n   - Pos 2: Skrining NAPZA / 5 Parameter Narkoba\n   - Pos 3: Pemeriksaan Fisik, Tensi, Visus & Buta Warna\n2. Mahasiswa dapat membuka menu '/status' di smartphone untuk memantau status pos yang telah selesai diinput.",
            "Jika salah satu tes terindikasi abnormal, dokter akan memberikan rujukan re-test atau pemeriksaan lanjutan."
        ],
        [
            "Tahap 11",
            "Unduh Sertifikat Kesehatan Digital",
            "/sertifikat\n/sertifikat/download",
            "Ketiga stasiun dinyatakan Lulus (Pass) dan status: completed.",
            "1. Sistem membuka tombol akses Sertifikat Kesehatan.\n2. Mahasiswa dapat melihat e-Sertifikat resmi yang ditandatangani secara digital.\n3. Menekan tombol 'Unduh Sertifikat PDF'.\n4. Dokumen memuat nomor sertifikat resmi dan QR Code autentikasi untuk keperluan verifikasi akademik.",
            "Jika status registrasi masih in_progress atau follow_up, tombol download sertifikat terkunci dan menampilkan pemberitahuan."
        ]
    ]

    table2 = doc.add_table(rows=len(t2_data)+1, cols=len(t2_headers))
    table2.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table2)
    col_w2 = [Inches(0.6), Inches(1.3), Inches(0.9), Inches(1.2), Inches(2.2), Inches(1.2)]

    for j, h_text in enumerate(t2_headers):
        table2.cell(0, j).paragraphs[0].text = h_text
    format_row(table2.rows[0], "0369A1", is_header=True, col_widths=col_w2)

    for i, row_data in enumerate(t2_data):
        row = table2.rows[i+1]
        for j, val in enumerate(row_data):
            row.cells[j].paragraphs[0].text = val
        bg = "F0F9FF" if i % 2 == 1 else "FFFFFF"
        format_row(row, bg, is_header=False, col_widths=col_w2)

    doc.add_page_break()

    # =========================================================================
    # BAGIAN 3: TABEL RINCIAN ALUR ADMIN & PETUGAS (ROLE: ADMIN / STAFF)
    # =========================================================================
    h3 = doc.add_paragraph()
    h3.paragraph_format.space_before = Pt(8)
    h3.paragraph_format.space_after = Pt(6)
    r3 = h3.add_run("3. Tabel Rincian Alur Sistem — Peran: Admin / Petugas Medis (Pengelola Sistem)")
    r3.bold = True
    r3.font.name = "Calibri"
    r3.font.size = Pt(14)
    r3.font.color.rgb = RGBColor(16, 42, 67)

    desc3 = doc.add_paragraph("Bagian ini merinci aktivitas operasional dan administratif yang dilakukan oleh Admin dan Petugas Medis:")
    desc3.paragraph_format.space_after = Pt(8)

    t3_headers = ["Kode", "Aktivitas Petugas / Admin", "Menu & URL", "Data Masukan (Input)", "Logika Sistem & Proses Validasi", "Hasil & Dampak Sistem"]
    t3_data = [
        [
            "ADM-01",
            "Login & Monitoring Dashboard Eksekutif",
            "Dashboard Petugas\n/petugas\n/admin",
            "Kredensial akun admin/petugas.",
            "Autentikasi role admin/staff. Sistem mengagregasi data real-time: total pendaftar, pembayaran pending, kehadiran hari ini, status pemeriksaan per stasiun, dan rasio kelulusan.",
            "Tampilan metrik ringkasan kartu statistik, grafik progres, dan daftar antrean terbaru."
        ],
        [
            "ADM-02",
            "Manajemen Kuota & Jadwal Sesi Tes",
            "Kelola Jadwal\n/petugas/jadwal",
            "Tanggal sesi, jam mulai, jam selesai, lokasi ruang, dan kuota peserta maksimal.",
            "Sistem memvalidasi agar tidak terjadi bentrok jam pada ruang yang sama. Menyimpan master jadwal ke tabel `test_sessions`.",
            "Sesi skrining baru aktif dan dapat langsung dipilih oleh mahasiswa baru pada portal mahasiswa."
        ],
        [
            "ADM-03",
            "Verifikasi Bukti Pembayaran Peserta",
            "Verifikasi Bayar\n/petugas/pembayaran",
            "Pemeriksaan foto bukti transfer, nominal, dan nama pengirim.",
            "1. Jika disetujui (Approve): Sistem mengubah status payment -> approved, registrasi -> cleared, dan meng-generate nomor antrean otomatis (`queue_code`).\n2. Jika ditolak (Reject): Memasukkan catatan alasan penolakan, status pembayaran -> rejected, registrasi -> awaiting_payment.",
            "Jika Approve: Mahasiswa langsung dapat melihat dan mengunduh tiket QR.\nJika Reject: Mahasiswa menerima notifikasi untuk mengunggah ulang bukti pembayaran."
        ],
        [
            "ADM-04",
            "Pemindaian Meja Check-in (Presensi Fisik)",
            "Scanner Check-in\n/petugas/checkin",
            "Scan QR Code e-Tiket mahasiswa atau input manual Nomor Antrean / NIM.",
            "Sistem mencari data pendaftaran berdasarkan token QR atau kode antrean. Memvalidasi apakah peserta hadir pada sesi yang benar dan status pendaftarannya 'cleared'.",
            "Status registrasi diubah menjadi 'checked_in'. Waktu kehadiran tercatat (`checked_in_at`). Mahasiswa resmi terdaftar di daftar antrean stasiun medis."
        ],
        [
            "ADM-05",
            "Panggilan Antrean Layar & Suara",
            "Antrean Display\n/petugas/antrian",
            "Memilih nomor antrean peserta yang siap dipanggil.",
            "Sistem memperbarui display antrean publik, memicu pemanggilan suara/notifikasi nomor antrean menuju ruang pemeriksaan klinik.",
            "Nomor urut peserta tampil aktif di layar display ruang tunggu."
        ],
        [
            "ADM-06",
            "Input Hasil Medis Stasiun 1 (Tes Urin)",
            "Stasiun Urin\n/petugas/stasiun/urin",
            "Parameter uji urin: pH, Berat Jenis, Glukosa, Protein, Keton, Bilirubin, Urobilinogen, Sedimen.",
            "Validasi isian medis, penetapan kesimpulan stasiun (Pass/Fail/Need Retest), penyimpanan ke tabel `station_results` dengan parameter `station_name='urin'`.",
            "Hasil tes urin tersimpan; progres stasiun 1 mahasiswa tercatat selesai."
        ],
        [
            "ADM-07",
            "Input Hasil Medis Stasiun 2 (Tes NAPZA)",
            "Stasiun NAPZA\n/petugas/stasiun/napza",
            "Parameter 5/6 Panel Narkoba: Amphetamine (AMP), Methamphetamine (MET), THC/Ganja, Morphine (MOP), Benzodiazepine (BZO), Cocaine (COC).",
            "Pengecekan hasil Negatif / Positif per panel. Jika semua negatif maka kesimpulan 'Pass'. Jika ada positif, status flagged dan memerlukan konfirmasi laboratorium lanjutan.",
            "Hasil tes bebas narkoba tersimpan di `station_results` dengan parameter `station_name='napza'`."
        ],
        [
            "ADM-08",
            "Input Hasil Medis Stasiun 3 (Fisik & Tanda Vital)",
            "Stasiun Fisik\n/petugas/stasiun/fisik",
            "Tekanan Darah (Sistol/Diastol), Denyut Nadi, Suhu, Pernapasan, Pemeriksaan Mata/Visus, Buta Warna (Ishihara), Riwayat Fisik Umum, Catatan Dokter.",
            "Penyimpanan parameter fisik ke `station_results` dengan `station_name='fisik'`. Evaluasi apakah kondisi fisik memenuhi syarat studi perkuliahan.",
            "Rekam medis fisik lengkap tersimpan. Progres ketiga stasiun kini mencapai 100%."
        ],
        [
            "ADM-09",
            "Evaluasi Akhir & Finalisasi Skrining",
            "Peserta / Stasiun\n/petugas/peserta",
            "Review hasil gabungan ketiga stasiun pemeriksaan.",
            "Dokter/Admin memverifikasi kelengkapan nilai stasiun. Jika ketiga stasiun berstatus Pass, admin menandai 'Selesai' (status -> completed). Sistem secara otomatis mengaktifkan modul penerbitan sertifikat digital.",
            "Penerbitan nomor surat keterangan sehat dan pembukaan hak unduh e-Sertifikat bagi mahasiswa."
        ],
        [
            "ADM-10",
            "Rekapitulasi Data & Ekspor Laporan (CSV)",
            "Rekap Peserta\n/petugas/peserta/export",
            "Filter berdasarkan tanggal, fakultas, program studi, atau status hasil tes.",
            "Query agregasi data seluruh peserta, data identitas, data pembayaran, dan nilai klinis stasiun. Konversi dataset ke format CSV / Excel.",
            "File CSV rekapitulasi terunduh untuk arsip administrasi universitas dan laporan dinas terkait."
        ],
        [
            "ADM-11",
            "Manajemen Master Admin (CRUD Penuh)",
            "Admin Panel\n/admin/pengguna\n/admin/profil\n/admin/registrasi\n/admin/pembayaran\n/admin/hasil-skrining",
            "Pengelolaan master pengguna, reset password, pengubahan role (student/staff/admin), pengeditan data jika terjadi kesalahan input manual, dan audit log.",
            "Hak akses super-admin dengan validasi otorisasi `role:admin`. Semua mutasi data memiliki relasi integritas referensial (foreign keys).",
            "Kontrol operasional penuh terhadap basis data sistem SIPKESMA."
        ]
    ]

    table3 = doc.add_table(rows=len(t3_data)+1, cols=len(t3_headers))
    table3.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table3)
    col_w3 = [Inches(0.6), Inches(1.4), Inches(1.1), Inches(1.4), Inches(2.0), Inches(1.2)]

    for j, h_text in enumerate(t3_headers):
        table3.cell(0, j).paragraphs[0].text = h_text
    format_row(table3.rows[0], "991B1B", is_header=True, col_widths=col_w3)

    for i, row_data in enumerate(t3_data):
        row = table3.rows[i+1]
        for j, val in enumerate(row_data):
            row.cells[j].paragraphs[0].text = val
        bg = "FEF2F2" if i % 2 == 1 else "FFFFFF"
        format_row(row, bg, is_header=False, col_widths=col_w3)

    doc.add_page_break()

    # =========================================================================
    # BAGIAN 4: TABEL MATRIKS SIKLUS HIDUP STATUS SISTEM (REGISTRATION STATE MACHINE)
    # =========================================================================
    h4 = doc.add_paragraph()
    h4.paragraph_format.space_before = Pt(8)
    h4.paragraph_format.space_after = Pt(6)
    r4 = h4.add_run("4. Tabel Matriks Siklus Hidup Status Registrasi (State Machine Lifecycle)")
    r4.bold = True
    r4.font.name = "Calibri"
    r4.font.size = Pt(14)
    r4.font.color.rgb = RGBColor(16, 42, 67)

    desc4 = doc.add_paragraph("Tabel berikut menjelaskan siklus transisi status (lifecycle status registrasi) dari awal pendaftaran sampai selesai:")
    desc4.paragraph_format.space_after = Pt(8)

    t4_headers = ["No", "Kode Status", "Label / Badge UI", "Aktor Pemicu", "Kondisi / Event Pemicu", "Status Selanjutnya", "Hak Akses Fitur Terbuka"]
    t4_data = [
        [
            "1",
            "draft / registered",
            "Akun Terdaftar",
            "Mahasiswa Baru",
            "Mahasiswa selesai registrasi dan login pertama kali ke sistem.",
            "data_completed",
            "Dapat mengisi Data Diri dan Riwayat Kesehatan."
        ],
        [
            "2",
            "data_completed",
            "Data Terisi",
            "Mahasiswa Baru",
            "Profil dan Riwayat Kesehatan tersimpan lengkap.",
            "scheduled",
            "Membuka menu pemilihan Jadwal Sesi Tes."
        ],
        [
            "3",
            "scheduled",
            "Jadwal Dipilih",
            "Mahasiswa Baru",
            "Memilih salah satu sesi skrining yang kuotanya tersedia.",
            "locked",
            "Membuka menu ringkasan dan tombol Kunci Pendaftaran."
        ],
        [
            "4",
            "locked",
            "Pendaftaran Dikunci",
            "Mahasiswa Baru",
            "Mahasiswa menekan tombol konfirmasi Kunci Pendaftaran.",
            "pending_verification",
            "Data terkunci read-only; membuka menu instruksi pembayaran & unggah bukti transfer."
        ],
        [
            "5",
            "pending_verification",
            "Menunggu Verifikasi",
            "Mahasiswa Baru",
            "Mahasiswa mengunggah berkas bukti transfer pembayaran.",
            "cleared (Approve)\natau locked (Reject)",
            "Portal menunggu; bukti bayar masuk ke antrean verifikasi Admin."
        ],
        [
            "6",
            "cleared",
            "Pembayaran Disetujui",
            "Admin / Petugas",
            "Admin menekan tombol Approve pada bukti bayar mahasiswa.",
            "checked_in",
            "Nomor Antrean terbit; Tiket Antrean & e-Tiket PDF aktif untuk diunduh."
        ],
        [
            "7",
            "checked_in",
            "Hadir di Klinik",
            "Petugas Meja Check-in",
            "Petugas memindai QR Code tiket di meja klinik kampus.",
            "in_progress",
            "Tercatat hadir; nama mahasiswa masuk ke antrean stasiun skrining medis."
        ],
        [
            "8",
            "in_progress",
            "Sedang Pemeriksaan",
            "Petugas Medis Stasiun",
            "Petugas menginput hasil pemeriksaan pos medis ke sistem.",
            "completed / follow_up",
            "Mahasiswa dapat memantau indikator status tiap stasiun secara langsung di menu '/status'."
        ],
        [
            "9",
            "completed",
            "Selesai / Lulus",
            "Dokter / Admin",
            "Seluruh 3 stasiun pemeriksaan berstatus Lulus (Pass).",
            "Selesai (Final)",
            "Menu Sertifikat Kesehatan Digital terbuka; mahasiswa dapat mengunduh e-Sertifikat resmi ber-QR Code."
        ],
        [
            "10",
            "follow_up",
            "Perlu Tindak Lanjut",
            "Dokter / Admin",
            "Ditemukan parameter abnormal pada stasiun uji yang memerlukan pemeriksaan ulang atau konseling.",
            "completed (setelah follow-up)",
            "Instruksi penanganan khusus di klinik; sertifikat ditangguhkan hingga pemeriksaan selesai."
        ]
    ]

    table4 = doc.add_table(rows=len(t4_data)+1, cols=len(t4_headers))
    table4.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table4)
    col_w4 = [Inches(0.4), Inches(1.2), Inches(1.1), Inches(1.0), Inches(1.5), Inches(1.2), Inches(1.3)]

    for j, h_text in enumerate(t4_headers):
        table4.cell(0, j).paragraphs[0].text = h_text
    format_row(table4.rows[0], "334155", is_header=True, col_widths=col_w4)

    for i, row_data in enumerate(t4_data):
        row = table4.rows[i+1]
        for j, val in enumerate(row_data):
            row.cells[j].paragraphs[0].text = val
        bg = "F1F5F9" if i % 2 == 1 else "FFFFFF"
        format_row(row, bg, is_header=False, col_widths=col_w4)

    doc.add_page_break()

    # =========================================================================
    # BAGIAN 5: TABEL PEMETAAN RUTE URL SISTEM & KONTROLER LARAVEL
    # =========================================================================
    h5 = doc.add_paragraph()
    h5.paragraph_format.space_before = Pt(8)
    h5.paragraph_format.space_after = Pt(6)
    r5 = h5.add_run("5. Tabel Pemetaan Teknis Endpoint & Kontroler (Arsitektur Laravel)")
    r5.bold = True
    r5.font.name = "Calibri"
    r5.font.size = Pt(14)
    r5.font.color.rgb = RGBColor(16, 42, 67)

    desc5 = doc.add_paragraph("Tabel ini menyajikan integrasi alur sistem dengan kode sumber aplikasi (Laravel Routes & Controllers):")
    desc5.paragraph_format.space_after = Pt(8)

    t5_headers = ["Modul Alur", "HTTP Method & URL", "Controller & Method", "Middleware Otorisasi", "Deskripsi Fungsi Backend"]
    t5_data = [
        [
            "Landing & Public",
            "GET /\nGET /verifikasi-sertifikat",
            "HomeController@index\nCertificateVerificationController@verify",
            "web (public)",
            "Menampilkan profil layanan SIPKESMA dan halaman publik validasi QR Code sertifikat."
        ],
        [
            "Autentikasi",
            "GET/POST /login\nGET/POST /register\nGET /auth/google/callback",
            "AuthController@showLogin, login\nAuthController@showRegister, register\nAuthController@handleGoogleCallback",
            "guest",
            "Otentikasi pengguna manual dan integrasi Single Sign-On via Google OAuth 2.0."
        ],
        [
            "Mahasiswa: Profil",
            "GET /profil\nPOST /profil",
            "Student\\ProfileController@index\nStudent\\ProfileController@store",
            "auth, role:student",
            "Menampilkan formulir biodata dan menyimpan pembaharuan data mahasiswa ke tabel database."
        ],
        [
            "Mahasiswa: Medis",
            "GET /riwayat-kesehatan\nPOST /riwayat-kesehatan",
            "Student\\HealthHistoryController@index\nStudent\\HealthHistoryController@store",
            "auth, role:student",
            "Kalkulasi Indeks Massa Tubuh (BMI) dan penyimpanan rekam riwayat kesehatan mahasiswa."
        ],
        [
            "Mahasiswa: Jadwal",
            "GET /jadwal\nPOST /jadwal/pilih",
            "Student\\ScheduleController@index\nStudent\\ScheduleController@select",
            "auth, role:student",
            "Pengecekan kuota sesi real-time dan pengaitan sesi tes dengan registrasi mahasiswa."
        ],
        [
            "Mahasiswa: Kunci",
            "GET /konfirmasi\nPOST /konfirmasi/kunci",
            "Student\\ConfirmationController@index\nStudent\\ConfirmationController@lock",
            "auth, role:student",
            "Kunci data pendaftaran (status -> locked) agar data tidak dapat diubah kembali."
        ],
        [
            "Mahasiswa: Bayar",
            "GET /pembayaran\nPOST /pembayaran/upload",
            "Student\\PaymentController@index\nStudent\\PaymentController@store",
            "auth, role:student",
            "Menyimpan berkas bukti transfer ke storage dan membuat record verifikasi pembayaran."
        ],
        [
            "Mahasiswa: Tiket",
            "GET /tiket\nGET /tiket/download",
            "Student\\TicketController@index\nStudent\\TicketController@downloadPdf",
            "auth, role:student",
            "Generate QR Code tiket peserta dan render berkas PDF e-Tiket antrean."
        ],
        [
            "Mahasiswa: Status",
            "GET /status",
            "Student\\StatusTrackerController@index",
            "auth, role:student",
            "Menampilkan visualisasi alur pelacakan status pos pemeriksaan secara real-time."
        ],
        [
            "Mahasiswa: Sertifikat",
            "GET /sertifikat\nGET /sertifikat/download",
            "Student\\CertificateController@index\nStudent\\CertificateController@download",
            "auth, role:student",
            "Render dan unduh e-Sertifikat Kesehatan PDF ber-QR Code resmi institusi."
        ],
        [
            "Petugas: Verifikasi",
            "GET /petugas/pembayaran\nPOST /petugas/pembayaran/{id}/approve\nPOST /petugas/pembayaran/{id}/reject",
            "Staff\\PaymentVerificationController@index\nStaff\\PaymentVerificationController@approve\nStaff\\PaymentVerificationController@reject",
            "auth, role:staff|admin",
            "Pemeriksaan bukti transfer, generate nomor antrean (queue_code) saat approve, dan catat penolakan."
        ],
        [
            "Petugas: Check-in",
            "GET /petugas/checkin\nPOST /petugas/checkin/process",
            "Staff\\CheckInController@index\nStaff\\CheckInController@process",
            "auth, role:staff|admin",
            "Pemindaian QR tiket dan mutasi status registrasi menjadi checked_in."
        ],
        [
            "Petugas: Antrean",
            "GET /petugas/antrian\nPOST /petugas/antrian/{id}/call",
            "Staff\\QueueDisplayController@index\nStaff\\QueueDisplayController@call",
            "auth, role:staff|admin",
            "Layar display nomor antrean dan pemanggilan giliran peserta menuju klinik."
        ],
        [
            "Petugas: 3 Stasiun",
            "GET /petugas/stasiun/{station_name}\nPOST /petugas/stasiun/{station_name}/{id}",
            "Staff\\StationController@index\nStaff\\StationController@storeResult",
            "auth, role:staff|admin",
            "Form input hasil klinis Stasiun Urin, Stasiun NAPZA, dan Stasiun Fisik."
        ],
        [
            "Petugas: Rekap",
            "GET /petugas/peserta\nGET /petugas/peserta/export",
            "Staff\\ParticipantController@index\nStaff\\ParticipantController@exportCsv",
            "auth, role:staff|admin",
            "Monitoring seluruh data peserta dan ekspor rekapitulasi data skrining ke CSV."
        ],
        [
            "Petugas: Sesi",
            "GET/POST /petugas/jadwal\nPUT/DELETE /petugas/jadwal/{session}",
            "Staff\\TestSessionController@index, store\nStaff\\TestSessionController@update, destroy",
            "auth, role:staff|admin",
            "Manajemen master jadwal dan kuota kapasitas harian pemeriksaan."
        ],
        [
            "Admin: CRUD Master",
            "GET/POST/PUT/DELETE /admin/pengguna\nGET/PUT/DELETE /admin/profil\nGET/PUT/DELETE /admin/kesehatan\nGET/PUT/DELETE /admin/registrasi\nGET/PUT/DELETE /admin/pembayaran\nGET/PUT/DELETE /admin/hasil-skrining",
            "Admin\\AdminController (dashboard, users, profiles, healthHistories, registrations, payments, results)",
            "auth, role:admin",
            "Hak akses super-admin untuk manajemen penuh seluruh tabel dan entitas data sistem."
        ]
    ]

    table5 = doc.add_table(rows=len(t5_data)+1, cols=len(t5_headers))
    table5.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table5)
    col_w5 = [Inches(1.1), Inches(1.6), Inches(1.8), Inches(1.1), Inches(2.1)]

    for j, h_text in enumerate(t5_headers):
        table5.cell(0, j).paragraphs[0].text = h_text
    format_row(table5.rows[0], "1E293B", is_header=True, col_widths=col_w5)

    for i, row_data in enumerate(t5_data):
        row = table5.rows[i+1]
        for j, val in enumerate(row_data):
            row.cells[j].paragraphs[0].text = val
        bg = "F8FAFC" if i % 2 == 1 else "FFFFFF"
        format_row(row, bg, is_header=False, col_widths=col_w5)

    # Catatan Penutup
    doc.add_paragraph()
    make_callout(
        doc,
        "Kesimpulan & Catatan Implementasi",
        "Alur sistem SIPKESMA menerapkan prinsip separation of concerns yang ketat antara hak akses peserta dan pengelola. Integritas data dijamin melalui tahapan penguncian (lock mechanism) dan verifikasi berlapis (pembayaran, meja check-in, dan validasi medis 3 stasiun) sebelum dokumen sertifikat resmi diterbitkan. Seluruh tabel di atas telah selaras dengan arsitektur basis data dan rute aplikasi Laravel SIPKESMA.",
        bg_color="FEF3C7",
        border_color="D97706"
    )

    # Save to file
    output_path = r"d:\sipkesma\Tabel_Penjelasan_Alur_Sistem_SIPKESMA.docx"
    doc.save(output_path)
    print(f"Document successfully created at: {output_path}")

if __name__ == "__main__":
    main()
