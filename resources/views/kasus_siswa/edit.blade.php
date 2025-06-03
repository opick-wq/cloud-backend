@extends('layouts.app') {{-- Asumsi Anda memiliki layout utama --}}

@section('content')
<div class="container">
    <h1>Edit Kasus Siswa</h1>
    <div id="studentInfo" class="mb-3" style="display: none;">
        <p><strong>Siswa:</strong> <span id="studentNameDisplay">Memuat...</span> (ID: <span id="studentIdDisplay">Memuat...</span>)</p>
    </div>

    <div id="editCaseAlert" class="alert d-none"></div>

    {{-- Form action dan method tidak akan digunakan oleh JS, tapi baik untuk fallback/no-JS --}}
    <form id="editCaseForm">
        {{-- CSRF dan Method Spoofing tidak diperlukan untuk AJAX ke API JWT --}}
        {{-- @csrf --}}
        {{-- @method('PUT') --}}

        {{-- ID Siswa biasanya tidak diubah saat mengedit kasus, akan diambil dari data fetch --}}
        <input type="hidden" id="student_user_id" name="student_user_id">
        <input type="hidden" id="current_case_id" name="current_case_id" value="{{ $caseId ?? '' }}">


        <div class="mb-3">
            <label for="topic" class="form-label">Topik/Masalah <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="topic" name="topic" required>
            <div class="invalid-feedback" data-for="topic"></div>
        </div>

        <div class="mb-3">
            <label for="caseDate" class="form-label">Tanggal Kasus <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="caseDate" name="caseDate" required>
            <div class="invalid-feedback" data-for="caseDate"></div>
        </div>

        <div class="mb-3">
            <label for="followUp" class="form-label">Tindak Lanjut <span class="text-danger">*</span></label>
            <textarea class="form-control" id="followUp" name="followUp" rows="4" required></textarea>
            <div class="invalid-feedback" data-for="followUp"></div>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Catatan Tambahan (Opsional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            <div class="invalid-feedback" data-for="notes"></div>
        </div>

        <button type="submit" class="btn btn-primary" id="btnUpdateKasus">Simpan Perubahan</button>
        {{-- Tombol batal bisa mengarah ke halaman detail atau daftar --}}
        <a href="{{ route('create') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function() {
    const token = localStorage.getItem('token');
    const editCaseForm = $('#editCaseForm');
    const editCaseAlert = $('#editCaseAlert');
    const btnUpdateKasus = $('#btnUpdateKasus');
    const caseId = $('#current_case_id').val(); // Mengambil caseId dari hidden input

    const studentInfoDiv = $('#studentInfo');
    const studentNameDisplay = $('#studentNameDisplay');
    const studentIdDisplay = $('#studentIdDisplay');

    if (!token) {
        showAlert(editCaseAlert, 'Autentikasi gagal. Token tidak ditemukan. Silakan login kembali.', 'danger');
        btnUpdateKasus.prop('disabled', true);
        return;
    }

    if (!caseId) {
        showAlert(editCaseAlert, 'ID Kasus tidak ditemukan. Tidak dapat memuat data.', 'danger');
        btnUpdateKasus.prop('disabled', true);
        return;
    }

    // Fungsi untuk menampilkan alert
    function showAlert(alertElement, message, type = 'danger') {
        alertElement.removeClass('d-none alert-success alert-info alert-warning alert-danger')
                    .addClass(`alert-${type}`)
                    .text(message)
                    .show();
    }

    // Fungsi untuk membersihkan error validasi sebelumnya
    function clearValidationErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    // Fungsi untuk menampilkan error validasi dari API
    function displayValidationErrors(errors) {
        clearValidationErrors();
        for (const field in errors) {
            const inputField = $(`[name="${field}"]`);
            const errorContainer = $(`.invalid-feedback[data-for="${field}"]`);
            inputField.addClass('is-invalid');
            if (errorContainer.length) {
                errorContainer.text(errors[field].join(', '));
            } else {
                inputField.after(`<div class="invalid-feedback d-block">${errors[field].join(', ')}</div>`);
            }
        }
    }

    // 1. Fetch data kasus yang ada untuk mengisi form
    btnUpdateKasus.prop('disabled', true).text('Memuat data...');
    $.ajax({
        url: `/api/student-cases/${caseId}`, // Endpoint API GET Anda (KasusSiswaController@show)
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        },
        success: function(caseData) {
            if (caseData) {
                $('#student_user_id').val(caseData.studentId); // Simpan studentId untuk data update
                $('#topic').val(caseData.topic);
                // Firestore timestamp (RFC3339) perlu di-parse ke format YYYY-MM-DD untuk input type="date"
                if (caseData.caseDate) {
                    try {
                        const dateObj = new Date(caseData.caseDate);
                        // Pastikan formatnya YYYY-MM-DD
                        const year = dateObj.getFullYear();
                        const month = ('0' + (dateObj.getMonth() + 1)).slice(-2);
                        const day = ('0' + dateObj.getDate()).slice(-2);
                        $('#caseDate').val(`${year}-${month}-${day}`);
                    } catch (e) {
                        console.error("Error parsing caseDate:", e);
                        $('#caseDate').val(''); // Atau tampilkan error
                    }
                }
                $('#followUp').val(caseData.followUp);
                $('#notes').val(caseData.notes || '');

                // Tampilkan info siswa
                studentNameDisplay.text(caseData.studentName || 'N/A');
                studentIdDisplay.text(caseData.studentId || 'N/A');
                studentInfoDiv.show();

                showAlert(editCaseAlert, 'Data berhasil dimuat. Silakan edit.', 'success');
            } else {
                showAlert(editCaseAlert, 'Data kasus tidak ditemukan.', 'warning');
            }
        },
        error: function(err) {
            let message = 'Gagal memuat data kasus.';
            if (err.status === 401) {
                message = 'Sesi Anda tidak valid. Silakan login kembali.';
            } else if (err.status === 404) {
                message = 'Kasus tidak ditemukan.';
            } else if (err.responseJSON && err.responseJSON.message) {
                message = err.responseJSON.message;
            }
            showAlert(editCaseAlert, message, 'danger');
        },
        complete: function() {
            btnUpdateKasus.prop('disabled', false).text('Simpan Perubahan');
        }
    });


    // 2. Handle submit form untuk update data
    editCaseForm.submit(function(e) {
        e.preventDefault();
        clearValidationErrors();
        editCaseAlert.addClass('d-none').removeClass('alert-success alert-danger alert-warning').text('');
        btnUpdateKasus.prop('disabled', true).text('Menyimpan...');

        const updatedData = {
            // student_user_id biasanya tidak diubah, tapi jika perlu, ambil dari form
            // student_user_id: $('#student_user_id').val(),
            topic: $('#topic').val(),
            caseDate: $('#caseDate').val(),
            followUp: $('#followUp').val(),
            notes: $('#notes').val()
        };

        $.ajax({
            url: `/api/student-cases/${caseId}`, // Endpoint API PUT/PATCH Anda (KasusSiswaController@update)
            method: 'PUT', // atau 'PATCH' jika API Anda mendukung partial updates
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
                // 'Content-Type': 'application/json' // Jika mengirim JSON.stringify(updatedData)
            },
            data: updatedData,
            success: function(res) {
                showAlert(editCaseAlert, res.message || 'Kasus siswa berhasil diperbarui!', 'success');
                // Opsional: Arahkan ke halaman detail atau daftar setelah sukses
                setTimeout(function() {
                    // Arahkan ke halaman detail kasus yang baru diupdate atau ke daftar
                   // window.location.href = `/jurnal-kasus/${caseId}`; // Ganti dengan rute show view
                    window.location.href = "{{ route('create') }}";
                }, 2000);
            },
            error: function(err) {
                let message = 'Terjadi kesalahan saat memperbarui data.';
                if (err.status === 401) {
                    message = 'Sesi Anda tidak valid. Silakan login kembali.';
                } else if (err.status === 422 && err.responseJSON && err.responseJSON.errors) {
                    displayValidationErrors(err.responseJSON.errors);
                    message = 'Data yang Anda masukkan tidak valid. Mohon periksa kembali.';
                } else if (err.responseJSON && err.responseJSON.message) {
                    message = err.responseJSON.message;
                }
                showAlert(editCaseAlert, message, 'danger');
            },
            complete: function() {
                btnUpdateKasus.prop('disabled', false).text('Simpan Perubahan');
            }
        });
    });
});
</script>
@endpush