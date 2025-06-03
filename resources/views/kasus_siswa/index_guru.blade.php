@extends('layouts.app') {{-- Asumsi Anda memiliki layout utama --}}

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Jurnal Kasus Siswa (Admin Guru BK)</h1>
        {{-- Tombol ini bisa dikontrol visibilitasnya dengan JS setelah user role diketahui,
             atau jika ada cara lain untuk mengetahui role di halaman shell ini --}}
        <a href="{{ route('kasus_siswa.create_view') }}" class="btn btn-primary" id="btnTambahKasus" style="display: none;">Tambah Kasus Baru</a>
        {{-- route('kasus_siswa.create_view') adalah rute yang menampilkan form create Blade, BUKAN API store --}}
    </div>

    <div id="casesAlert" class="alert d-none"></div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Siswa</th>
                <th>Guru BK Pencatat</th>
                <th>Topik/Masalah</th>
                <th>Tanggal Kasus</th>
                <th>Tindak Lanjut</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="casesTableBody">
            {{-- Data akan diisi oleh JavaScript --}}
            <tr>
                <td colspan="7" class="text-center" id="loadingCases">Memuat data kasus...</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function() {
    const token = localStorage.getItem('token');
    const casesTableBody = $('#casesTableBody');
    const loadingCasesRow = $('#loadingCases');
    const casesAlert = $('#casesAlert');
    const btnTambahKasus = $('#btnTambahKasus');

    if (!token) {
        casesAlert.removeClass('d-none alert-success alert-info').addClass('alert-danger').text('Autentikasi gagal. Token tidak ditemukan. Anda akan diarahkan ke halaman login.');
        loadingCasesRow.hide();
        setTimeout(function() {
            window.location.href = '/login'; // Sesuaikan dengan rute halaman login Anda
        }, 3000);
        return;
    }

    // Opsional: Ambil detail user untuk mengetahui role dan menampilkan tombol "Tambah Kasus"
    // Ini memerlukan endpoint API baru seperti /api/user/profile atau /api/auth/me
    $.ajax({
        url: '/api/me', // GANTI dengan endpoint untuk mendapatkan detail user login
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        success: function(userRes) {
            if (userRes.user && userRes.user.role === 'guru_bk') {
                btnTambahKasus.show();
            }
        },
        error: function(userErr) {
            console.warn('Tidak dapat mengambil detail user:', userErr.responseJSON ? userErr.responseJSON.message : userErr.statusText);
            // Tetap lanjutkan untuk mengambil data kasus, mungkin user masih valid untuk itu
        }
    });


    // Mengambil data kasus siswa dari API
    $.ajax({
        url: '/api/student-cases', // Endpoint API Anda yang dihandle KasusSiswaController@index (yang return JSON)
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        success: function(res) {
            loadingCasesRow.hide();
            casesTableBody.empty(); // Bersihkan isi tabel sebelum mengisi data baru

            if (res && res.length > 0) {
                $.each(res, function(index, caseData) {
                    // Format tanggal
                    let caseDateFormatted = 'N/A';
                    if (caseData.caseDate) {
                        try {
                            // JavaScript Date parsing and formatting (bisa disesuaikan atau pakai library seperti moment.js)
                            const dateObj = new Date(caseData.caseDate);
                            caseDateFormatted = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                        } catch (e) {
                            console.warn("Format tanggal tidak valid:", caseData.caseDate);
                            caseDateFormatted = caseData.caseDate; // fallback
                        }
                    }

                    // Batasi panjang tindak lanjut
                    let followUpShort = caseData.followUp ? caseData.followUp.substring(0, 50) + (caseData.followUp.length > 50 ? '...' : '') : '';

                    // Rute untuk aksi (pastikan rute ini mengarah ke halaman view, bukan API jika untuk navigasi)
                    // Untuk `destroy`, formnya perlu dibuat dinamis atau pakai AJAX DELETE
                    let viewUrl = `/showkasus/${caseData.id}/edit-page`; // Sesuaikan dengan URL untuk show view
                    let editUrl = `/kasus-siswa/${caseData.id}/edit-page`; // Sesuaikan dengan URL untuk edit view
                    // Untuk delete, lebih baik menggunakan AJAX DELETE atau pastikan formnya benar
                    // Untuk kesederhanaan, kita bisa arahkan ke halaman konfirmasi atau langsung AJAX delete

                    // Form delete akan sedikit lebih kompleks jika dibuat dinamis sepenuhnya di JS.
                    // Alternatif: Buat modal konfirmasi, lalu kirim AJAX DELETE.
                    // Untuk saat ini, kita bisa buat tombol delete yang memanggil fungsi JS.
                    // Contoh form delete dengan POST (memerlukan route yang sesuai):
                    // <form action="/jurnal-kasus/${caseData.id}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin hapus?');">
                    //    <input type="hidden" name="_token" value="{{ csrf_token() }}"> {{-- CSRF token perlu jika form ini di-render server-side sebagian --}}
                    //    <input type="hidden" name="_method" value="DELETE">
                    //    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    // </form>
                    // Jika form ini murni client-side, CSRF token perlu diambil dan disisipkan JavaScript.
                    // Namun, untuk API dengan JWT, CSRF biasanya tidak diperlukan.

                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${caseData.studentName || 'N/A'} <small class="d-block text-muted">(${caseData.studentId || 'N/A'})</small></td>
                            <td>${caseData.guruBkName || 'N/A'}</td>
                            <td>${caseData.topic || ''}</td>
                            <td>${caseDateFormatted}</td>
                            <td>${followUpShort}</td>
                            <td>
                                <a href="${viewUrl}" class="btn btn-info btn-sm">Lihat</a>
                                <a href="${editUrl}" class="btn btn-warning btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm btn-delete-case" data-id="${caseData.id}">Hapus</button>
                            </td>
                        </tr>
                    `;
                    casesTableBody.append(row);
                });
            } else {
                casesTableBody.html('<tr><td colspan="7" class="text-center">Belum ada data kasus siswa yang tercatat.</td></tr>');
            }
        },
        error: function(err) {
            loadingCasesRow.hide();
            let message = 'Gagal memuat data kasus siswa.';
            if (err.status === 401) {
                message = 'Sesi Anda telah berakhir atau token tidak valid. Anda akan diarahkan ke halaman login.';
                setTimeout(function() {
                    localStorage.removeItem('token'); // Hapus token yang salah
                    window.location.href = '/login'; // Sesuaikan dengan rute halaman login Anda
                }, 3000);
            } else if (err.responseJSON && err.responseJSON.message) {
                message = err.responseJSON.message;
            }
            casesAlert.removeClass('d-none alert-success alert-info').addClass('alert-danger').text(message);
            casesTableBody.html(`<tr><td colspan="7" class="text-center">${message}</td></tr>`);
        }
    });

    // Handler untuk tombol delete (menggunakan AJAX DELETE)
    casesTableBody.on('click', '.btn-delete-case', function() {
        const caseId = $(this).data('id');
        if (confirm('Apakah Anda yakin ingin menghapus kasus ini?')) {
            $.ajax({
                url: `/api/student-cases/${id}`, // Endpoint API DELETE Anda
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                success: function(res) {
                    // Refresh data atau hapus baris dari tabel
                    // Cara termudah adalah reload semua data:
                    // window.location.reload(); // atau panggil lagi fungsi fetch data kasus
                    $(this).closest('tr').remove(); // Hapus baris jika berhasil
                    casesAlert.removeClass('d-none alert-danger').addClass('alert-success').text(res.message || 'Kasus berhasil dihapus.');
                     // Muat ulang data kasus agar nomor urut dan data konsisten
                    loadCasesData(); // Anda perlu membungkus kode AJAX GET di atas dalam fungsi loadCasesData()
                }.bind(this), // Bind 'this' agar merujuk ke tombol di dalam success callback
                error: function(err) {
                    let message = 'Gagal menghapus kasus.';
                    if (err.responseJSON && err.responseJSON.message) {
                        message = err.responseJSON.message;
                    }
                    casesAlert.removeClass('d-none alert-success').addClass('alert-danger').text(message);
                }
            });
        }
    });

    // Anda mungkin ingin membungkus logika AJAX GET utama ke dalam sebuah fungsi
    // agar bisa dipanggil ulang, misalnya setelah delete.
    // function loadCasesData() { ... kode AJAX GET di atas ... }
    // loadCasesData(); // Panggil saat document ready

});
</script>
@endpush