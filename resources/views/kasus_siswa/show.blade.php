@extends('layouts.app') {{-- Asumsi Anda memiliki layout utama --}}

@section('content')
<div class="container">
    <div id="showCaseAlert" class="alert d-none"></div>
    <input type="hidden" id="current_case_id" value="{{ $caseId ?? '' }}">

    <div class="card" id="caseDetailCard" style="display: none;"> {{-- Sembunyikan dulu sampai data dimuat --}}
        <div class="card-header">
            <h1>Detail Kasus Siswa: <span id="caseTopicHeader">Memuat...</span></h1>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Nama Siswa:</strong> <span id="studentNameDisplay">Memuat...</span></p>
                    <p><strong>ID Siswa:</strong> <span id="studentIdDisplay">Memuat...</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Guru BK Pencatat:</strong> <span id="guruBkNameDisplay">Memuat...</span></p>
                    <p><strong>ID Guru BK:</strong> <span id="guruBkIdDisplay">Memuat...</span></p>
                </div>
            </div>
            <hr>
            <p><strong>Tanggal Kasus:</strong> <span id="caseDateDisplay">Memuat...</span></p>
            <p><strong>Topik/Masalah:</strong></p>
            <p><span id="topicDisplay">Memuat...</span></p>

            <p><strong>Tindak Lanjut:</strong></p>
            <p><span id="followUpDisplay">Memuat...</span></p>

            <div id="notesSection" style="display: none;">
                <p><strong>Catatan Tambahan:</strong></p>
                <p><span id="notesDisplay"></span></p>
            </div>
            <hr>
            <p><small>Dicatat pada: <span id="createdAtDisplay">Memuat...</span></small></p>
            <p><small>Diperbarui pada: <span id="updatedAtDisplay">Memuat...</span></small></p>
        </div>
        <div class="card-footer">
            <a href="#" id="editCaseButton" class="btn btn-warning" style="display: none;">Edit Kasus Ini</a>
            <a href="#" id="backToListButton" class="btn btn-secondary">Kembali ke Daftar</a>
        </div>
    </div>
    <div id="loadingMessage" class="text-center mt-5">
        <p>Memuat detail kasus...</p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function() {
    const token = localStorage.getItem('token');
    const caseId = $('#current_case_id').val();
    const showAlertDiv = $('#showCaseAlert');
    const caseDetailCard = $('#caseDetailCard');
    const loadingMessage = $('#loadingMessage');

    // Fungsi untuk menampilkan alert
    function showAlert(message, type = 'danger') {
        showAlertDiv.removeClass('d-none alert-success alert-info alert-warning alert-danger')
                    .addClass(`alert-${type}`)
                    .text(message)
                    .show();
    }

    // Fungsi untuk memformat tanggal dari RFC3339 ke format yang lebih mudah dibaca
    function formatReadableDate(isoString, includeTime = false) {
        if (!isoString) return 'N/A';
        try {
            const dateObj = new Date(isoString);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            if (includeTime) {
                options.hour = '2-digit';
                options.minute = '2-digit';
            }
            return dateObj.toLocaleDateString('id-ID', options);
        } catch (e) {
            console.warn("Format tanggal tidak valid:", isoString);
            return isoString; // fallback
        }
    }

    if (!token) {
        showAlert('Autentikasi gagal. Token tidak ditemukan. Silakan login kembali.');
        loadingMessage.hide();
        // Opsional: redirect ke login setelah beberapa detik
        // setTimeout(function() { window.location.href = '/login'; }, 3000);
        return;
    }

    if (!caseId) {
        showAlert('ID Kasus tidak ditemukan. Tidak dapat memuat detail.');
        loadingMessage.hide();
        return;
    }

    // Ambil detail user login (untuk menentukan role -> tombol edit & kembali)
    let currentUserRole = null;
    $.ajax({
        url: '/api/me', // Endpoint untuk mendapatkan detail user login
        method: 'GET',
        headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        async: false, // Lakukan sinkron agar role diketahui sebelum fetch data kasus (hati-hati dengan performa)
                      // Alternatif: Buat fetch data kasus di dalam success callback ini
        success: function(userRes) {
            if (userRes.user && userRes.user.role) {
                currentUserRole = userRes.user.role;
            }
        },
        error: function(err) {
            console.warn('Tidak dapat mengambil detail user:', err.responseJSON ? err.responseJSON.message : err.statusText);
            // Jika gagal ambil role, tombol edit mungkin tidak tampil, tombol kembali default ke suatu tempat
        }
    });


    // Ambil detail kasus dari API
    $.ajax({
        url: `/api/student-cases/${caseId}`, // Endpoint API GET Anda (KasusSiswaController@show)
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        success: function(caseData) {
            loadingMessage.hide();
            if (caseData) {
                $('#caseTopicHeader').text(caseData.topic || 'N/A');
                $('#studentNameDisplay').text(caseData.studentName || 'N/A');
                $('#studentIdDisplay').text(caseData.studentId || 'N/A');
                $('#guruBkNameDisplay').text(caseData.guruBkName || 'N/A');
                $('#guruBkIdDisplay').text(caseData.guruBkId || 'N/A');
                $('#caseDateDisplay').text(formatReadableDate(caseData.caseDate));
                $('#topicDisplay').text(caseData.topic || 'N/A');
                $('#followUpDisplay').html(caseData.followUp ? caseData.followUp.replace(/\n/g, '<br>') : 'N/A'); // nl2br

                if (caseData.notes) {
                    $('#notesDisplay').html(caseData.notes.replace(/\n/g, '<br>'));
                    $('#notesSection').show();
                } else {
                    $('#notesSection').hide();
                }

                $('#createdAtDisplay').text(formatReadableDate(caseData.createdAt, true));
                $('#updatedAtDisplay').text(formatReadableDate(caseData.updatedAt, true));

                // Tampilkan tombol Edit jika user adalah guru_bk
                if (currentUserRole === 'guru_bk') {
                    $('#editCaseButton').attr('href', `/kasus-siswa/${caseData.id}/edit-page`).show(); // Rute web ke halaman edit
                }

                // Atur tombol Kembali
                let backUrl = "{{ route('create') }}"; // Default untuk guru_bk (sesuaikan nama rute)
                if (currentUserRole === 'siswa') {
                    backUrl = "{{ route('dashboard') }}"; // Rute untuk siswa (sesuaikan nama rute)
                }
                $('#backToListButton').attr('href', backUrl);


                caseDetailCard.show(); // Tampilkan card setelah data dimuat
            } else {
                showAlert('Detail kasus tidak ditemukan.', 'warning');
            }
        },
        error: function(err) {
            loadingMessage.hide();
            let message = 'Gagal memuat detail kasus.';
            if (err.status === 401) {
                message = 'Sesi Anda tidak valid atau token tidak valid. Silakan login kembali.';
                // Opsional: redirect ke login
                // setTimeout(function() { localStorage.removeItem('token'); window.location.href = '/login'; }, 3000);
            } else if (err.status === 403) {
                message = 'Anda tidak memiliki izin untuk melihat kasus ini.';
            } else if (err.status === 404) {
                message = 'Kasus tidak ditemukan.';
            } else if (err.responseJSON && err.responseJSON.message) {
                message = err.responseJSON.message;
            }
            showAlert(message);
        }
    });
});
</script>
@endpush