@extends('layouts.app') {{-- Asumsi Anda memiliki layout utama --}}

@section('content')
<div class="container">
    <h1>Tambah Kasus Siswa Baru</h1>

    <div id="createCaseAlert" class="alert d-none"></div>

    {{-- Form action dan method tidak akan digunakan oleh JS, tapi baik untuk fallback/no-JS --}}
    <form id="createCaseForm">
        {{-- CSRF token tidak diperlukan jika API Anda stateless dan dilindungi JWT,
             namun jika form ini juga bisa disubmit secara tradisional, @csrf tetap berguna.
             Untuk AJAX ke API JWT, kita tidak akan mengirim _token ini. --}}
        {{-- @csrf --}}

        <div class="mb-3">
            <label for="student_user_id" class="form-label">ID Siswa (Sub JWT Siswa) <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="student_user_id" name="student_user_id" required>
            <div class="invalid-feedback" data-for="student_user_id"></div>
            <small class="form-text text-muted">Masukkan ID unik siswa (misalnya dari sistem pengguna Anda).</small>
        </div>

        

        <div class="mb-3">
            <label for="topic" class="form-label">Topik/Masalah <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="topic" name="topic" required>
            <div class="invalid-feedback" data-for="topic"></div>
        </div>

        <div class="mb-3">
            <label for="case_date" class="form-label">Tanggal Kasus <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="case_date" name="case_date" value="{{ date('Y-m-d') }}" required>
            <div class="invalid-feedback" data-for="case_date"></div>
        </div>

        <div class="mb-3">
            <label for="follow_up" class="form-label">Tindak Lanjut <span class="text-danger">*</span></label>
            <textarea class="form-control" id="follow_up" name="follow_up" rows="4" required></textarea>
            <div class="invalid-feedback" data-for="follow_up"></div>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Catatan Tambahan (Opsional)</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            <div class="invalid-feedback" data-for="notes"></div>
        </div>

        <button type="submit" class="btn btn-primary" id="btnSimpanKasus">Simpan Kasus</button>
        <a href="{{ route('create') }}" class="btn btn-secondary">Batal</a> {{-- Sesuaikan rute kembali --}}
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function() {
    const token = localStorage.getItem('token');
    const createCaseForm = $('#createCaseForm');
    const createCaseAlert = $('#createCaseAlert');
    const btnSimpanKasus = $('#btnSimpanKasus');

    if (!token) {
        createCaseAlert.removeClass('d-none alert-success').addClass('alert-danger').text('Autentikasi gagal. Token tidak ditemukan. Silakan login kembali.');
        btnSimpanKasus.prop('disabled', true);
        // Opsional: redirect ke login setelah beberapa detik
        // setTimeout(function() { window.location.href = '/login'; }, 3000);
        return;
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
                // Fallback jika tidak ada container spesifik
                inputField.after(`<div class="invalid-feedback d-block">${errors[field].join(', ')}</div>`);
            }
        }
    }

    createCaseForm.submit(function(e) {
        e.preventDefault();
        clearValidationErrors();
        createCaseAlert.addClass('d-none').removeClass('alert-success alert-danger').text('');
        btnSimpanKasus.prop('disabled', true).text('Menyimpan...');

        // Mengumpulkan data form
        const formData = {
            student_user_id: $('#student_user_id').val(),
            topic: $('#topic').val(),
            case_date: $('#case_date').val(),
            follow_up: $('#follow_up').val(),
            notes: $('#notes').val()
        };

        $.ajax({
            url: '/api/student-cases', // Endpoint API POST Anda (KasusSiswaController@store)
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
                // 'Content-Type': 'application/json' // jQuery akan set ini otomatis jika data adalah objek
            },
            data: formData, // jQuery akan serialize ini dengan benar untuk x-www-form-urlencoded atau multipart jika ada file
                            // Jika API Anda strict mengharapkan JSON, gunakan JSON.stringify(formData) dan set Content-Type
            success: function(res) {
                createCaseAlert.removeClass('d-none alert-danger').addClass('alert-success').text(res.message || 'Kasus siswa berhasil disimpan!');
                createCaseForm[0].reset(); // Reset form
                // Opsional: Arahkan ke halaman daftar atau detail setelah sukses
                setTimeout(function() {
                    window.location.href = "{{ route('create') }}"; // Sesuaikan rute kembali
                }, 2000);
            },
            error: function(err) {
                let message = 'Terjadi kesalahan saat menyimpan data.';
                if (err.status === 401) {
                    message = 'Sesi Anda tidak valid atau telah berakhir. Silakan login kembali.';
                    // Opsional: redirect ke login
                    // setTimeout(function() { localStorage.removeItem('token'); window.location.href = '/login'; }, 3000);
                } else if (err.status === 422 && err.responseJSON && err.responseJSON.errors) {
                    // Handle validation errors
                    displayValidationErrors(err.responseJSON.errors);
                    message = 'Data yang Anda masukkan tidak valid. Mohon periksa kembali.';
                } else if (err.responseJSON && err.responseJSON.message) {
                    message = err.responseJSON.message;
                }
                createCaseAlert.removeClass('d-none alert-success').addClass('alert-danger').text(message);
            },
            complete: function() {
                btnSimpanKasus.prop('disabled', false).text('Simpan Kasus');
            }
        });
    });

    
});
</script>
@endpush