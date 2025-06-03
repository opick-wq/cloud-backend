@extends('layouts.app') {{-- Asumsi Anda memiliki layout utama --}}

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Riwayat Kasus Saya</h1>
        {{-- Siswa biasanya tidak menambah kasus sendiri --}}
    </div>

    <div id="myCasesAlert" class="alert d-none"></div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Topik/Masalah</th>
                <th>Tanggal Kasus</th>
                <th>Ditangani Oleh (Guru BK)</th>
                <th>Tindak Lanjut Ringkas</th>
            </tr>
        </thead>
        <tbody id="myCasesTableBody">
            {{-- Data akan diisi oleh JavaScript --}}
            <tr>
                <td colspan="6" class="text-center" id="loadingMyCases">Memuat riwayat kasus Anda...</td>
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
    const myCasesTableBody = $('#myCasesTableBody');
    const loadingMyCasesRow = $('#loadingMyCases');
    const myCasesAlert = $('#myCasesAlert');

    // Fungsi untuk menampilkan alert
    function showAlert(message, type = 'danger') {
        myCasesAlert.removeClass('d-none alert-success alert-info alert-warning alert-danger')
                    .addClass(`alert-${type}`)
                    .text(message)
                    .show();
    }

    // Fungsi untuk memformat tanggal
    function formatReadableDate(isoString) {
        if (!isoString) return 'N/A';
        try {
            const dateObj = new Date(isoString);
            return dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        } catch (e) {
            return isoString; // fallback
        }
    }

    if (!token) {
        showAlert('Autentikasi gagal. Token tidak ditemukan. Anda akan diarahkan ke halaman login.');
        loadingMyCasesRow.hide();
        setTimeout(function() {
            window.location.href = "{{ route('login') }}"; // Sesuaikan dengan rute halaman login web Anda
        }, 3000);
        return;
    }

    // Mengambil data kasus siswa dari API
    // Endpoint API GET /api/student-cases akan otomatis memfilter berdasarkan token siswa
    $.ajax({
        url: '/api/student-cases',
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        success: function(res) {
            loadingMyCasesRow.hide();
            myCasesTableBody.empty(); // Bersihkan isi tabel

            if (res && res.length > 0) {
                $.each(res, function(index, caseData) {
                    let followUpShort = caseData.followUp ? caseData.followUp.substring(0, 70) + (caseData.followUp.length > 70 ? '...' : '') : '';
                    
                    // Rute untuk melihat detail kasus (mengarah ke halaman view detail)
                    let detailUrl = `/jurnal-kasus/${caseData.id}/detail-page`; // Sesuaikan dengan rute web untuk show detail

                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${caseData.studentName || 'N/A'}</td>
                            <td>${caseData.topic || 'N/A'}</td>
                            <td>${formatReadableDate(caseData.caseDate)}</td>
                            <td>${caseData.guruBkName || 'N/A'}</td>
                            <td>${followUpShort}</td>
                        </tr>
                    `;
                    myCasesTableBody.append(row);
                });
            } else {
                myCasesTableBody.html('<tr><td colspan="6" class="text-center">Anda tidak memiliki riwayat kasus yang tercatat.</td></tr>');
            }
        },
        error: function(err) {
            loadingMyCasesRow.hide();
            let message = 'Gagal memuat riwayat kasus Anda.';
            if (err.status === 401) {
                message = 'Sesi Anda telah berakhir atau token tidak valid. Anda akan diarahkan ke halaman login.';
                setTimeout(function() {
                    localStorage.removeItem('token'); // Hapus token yang salah
                    window.location.href = "{{ route('login') }}"; // Sesuaikan
                }, 3000);
            } else if (err.responseJSON && err.responseJSON.message) {
                message = err.responseJSON.message;
            }
            showAlert(message);
            myCasesTableBody.html(`<tr><td colspan="6" class="text-center">${message}</td></tr>`);
        }
    });
});
</script>
@endpush