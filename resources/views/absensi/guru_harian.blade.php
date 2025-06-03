@extends('layouts.app') {{-- Asumsi layout utama Anda --}}

@section('title', 'Laporan Absensi Harian')

@section('styles')
<style>
    /* Tambahkan style jika perlu untuk modal atau elemen baru */
    .modal-body .form-label { margin-bottom: 0.3rem; }
    .modal-body .form-control, .modal-body .form-select { margin-bottom: 0.7rem; }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Laporan Absensi Harian Siswa</h4>
            {{-- TOMBOL TAMBAH MANUAL --}}
            <button class="btn btn-light btn-sm" id="btnShowManualAddModal"><i class="fas fa-plus-circle me-1"></i> Tambah Manual</button>
        </div>
        <div class="card-body">
            <div id="alertReport" class="alert d-none"></div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="reportDate" class="form-label">Pilih Tanggal:</label>
                    <input type="date" class="form-control" id="reportDate" name="report_date" value="{{ \Carbon\Carbon::now(env('APP_TIMEZONE', 'UTC'))->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label for="searchInput" class="form-label">Cari Nama Siswa:</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Masukkan nama atau ID siswa">
                </div>
                <div class="col-md-3 align-self-end">
                    <button class="btn btn-primary w-100" id="btnShowReport"><i class="fas fa-search me-1"></i> Tampilkan</button>
                </div>
            </div>

            <div id="loadingReport" class="text-center py-3" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>
                <p class="mt-2">Memuat laporan absensi...</p>
            </div>

            <div class="table-responsive mt-3" id="reportTableContainer" style="display: none;">
                <h5 class="mb-2">Laporan untuk Tanggal: <span id="displayReportDate"></span></h5>
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar/Sakit</th>
                            <th>Lokasi Masuk</th>
                            <th>Work Code</th>
                            <th>Catatan</th>
                            <th>Aksi</th> {{-- KOLOM BARU UNTUK AKSI --}}
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        {{-- Data akan diisi oleh JavaScript --}}
                    </tbody>
                </table>
            </div>
            <div id="noDataMessage" class="alert alert-info mt-3" style="display: none;">
                Tidak ada data absensi untuk tanggal yang dipilih.
            </div>
        </div>
    </div>
</div>

{{-- MODAL UNTUK EDIT ABSENSI --}}
<div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-labelledby="editAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAttendanceModalLabel">Edit Absensi Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="alertModalEdit" class="alert d-none"></div>
                <input type="hidden" id="editAttendanceId">
                <div class="mb-3">
                    <label class="form-label">Siswa:</label>
                    <p><strong id="editStudentName"></strong> (<span id="editStudentId"></span>)</p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="editStatus" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="editStatus">
                            <option value="HADIR">HADIR</option>
                            <option value="SAKIT">SAKIT</option>
                            <option value="IZIN">IZIN</option>
                            <option value="ALPHA">ALPHA</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="editWorkCode" class="form-label">Work Code</label>
                        <input type="text" class="form-control" id="editWorkCode">
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6">
                        <label for="editClockInTime" class="form-label">Jam Masuk (HH:MM)</label>
                        <input type="time" class="form-control" id="editClockInTime">
                    </div>
                    <div class="col-md-6">
                        <label for="editClockOutTime" class="form-label">Jam Keluar/Sakit (HH:MM)</label>
                        <input type="time" class="form-control" id="editClockOutTime">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="editNotes" class="form-label">Catatan</label>
                    <textarea class="form-control" id="editNotes" rows="2"></textarea>
                </div>
                <fieldset class="border p-2 mb-2">
                    <legend class="w-auto px-2 h6">Info Lokasi (Opsional)</legend>
                    <div class="row">
                         <div class="col-md-6">
                            <label for="editLatitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="editLatitude" placeholder="-6.12345">
                        </div>
                        <div class="col-md-6">
                            <label for="editLongitude" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="editLongitude" placeholder="106.12345">
                        </div>
                    </div>
                    <div class="mt-1">
                        <label for="editAddress" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="editAddress" placeholder="Jl. Contoh No. 123">
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveAttendanceChanges">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL UNTUK TAMBAH ABSENSI MANUAL --}}
<div class="modal fade" id="manualAddAttendanceModal" tabindex="-1" aria-labelledby="manualAddAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manualAddAttendanceModalLabel">Tambah Absensi Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="alertModalManualAdd" class="alert d-none"></div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="manualStudentId" class="form-label">ID Siswa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="manualStudentId" placeholder="Masukkan ID Siswa">
                         {{-- TODO: Idealnya ini adalah input autocomplete/select2 dari daftar siswa --}}
                    </div>
                     <div class="col-md-6">
                        <label for="manualStudentName" class="form-label">Nama Siswa (Opsional)</label>
                        <input type="text" class="form-control" id="manualStudentName" placeholder="Akan terisi jika ID ditemukan">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="manualDate" class="form-label">Tanggal Absensi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="manualDate" value="{{ \Carbon\Carbon::now(env('APP_TIMEZONE', 'UTC'))->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="manualStatus" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="manualStatus">
                            <option value="HADIR">HADIR</option>
                            <option value="SAKIT">SAKIT</option>
                            <option value="IZIN">IZIN</option>
                            <option value="ALPHA">ALPHA</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                     <div class="col-md-6">
                        <label for="manualWorkCode" class="form-label">Work Code</label>
                        <input type="text" class="form-control" id="manualWorkCode">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="manualClockInTime" class="form-label">Jam Masuk (HH:MM)</label>
                        <input type="time" class="form-control" id="manualClockInTime">
                    </div>
                    <div class="col-md-6">
                        <label for="manualClockOutTime" class="form-label">Jam Keluar/Sakit (HH:MM)</label>
                        <input type="time" class="form-control" id="manualClockOutTime">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="manualNotes" class="form-label">Catatan</label>
                    <textarea class="form-control" id="manualNotes" rows="2"></textarea>
                </div>
                 <fieldset class="border p-2 mb-2">
                    <legend class="w-auto px-2 h6">Info Lokasi (Opsional)</legend>
                    <div class="row">
                         <div class="col-md-6">
                            <label for="manualLatitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="manualLatitude" placeholder="-6.12345">
                        </div>
                        <div class="col-md-6">
                            <label for="manualLongitude" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="manualLongitude" placeholder="106.12345">
                        </div>
                    </div>
                     <div class="mt-1">
                        <label for="manualAddress" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="manualAddress" placeholder="Jl. Contoh No. 123">
                    </div>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSubmitManualAdd">Simpan Absensi Manual</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
{{-- Jika Anda menggunakan Bootstrap 5 JS Bundle untuk modal --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> 
<script>
$(document).ready(function() {
    const token = localStorage.getItem('token');
    const reportDateInput = $('#reportDate');
    const btnShowReport = $('#btnShowReport');
    const loadingReportDiv = $('#loadingReport');
    const reportTableContainer = $('#reportTableContainer');
    const reportTableBody = $('#reportTableBody');
    const displayReportDate = $('#displayReportDate');
    const alertReportDiv = $('#alertReport');
    const noDataMessageDiv = $('#noDataMessage');
    const searchInput = $('#searchInput');

    // Modal Elements
    const editAttendanceModal = new bootstrap.Modal(document.getElementById('editAttendanceModal'));
    const manualAddAttendanceModal = new bootstrap.Modal(document.getElementById('manualAddAttendanceModal'));
    const alertModalEdit = $('#alertModalEdit');
    const alertModalManualAdd = $('#alertModalManualAdd');

    // Edit Modal Fields
    const editAttendanceIdInput = $('#editAttendanceId');
    const editStudentNameEl = $('#editStudentName');
    const editStudentIdEl = $('#editStudentId');
    const editStatusSelect = $('#editStatus');
    const editWorkCodeInput = $('#editWorkCode');
    const editClockInTimeInput = $('#editClockInTime');
    const editClockOutTimeInput = $('#editClockOutTime');
    const editNotesTextarea = $('#editNotes');
    const editLatitudeInput = $('#editLatitude');
    const editLongitudeInput = $('#editLongitude');
    const editAddressInput = $('#editAddress');
    const btnSaveAttendanceChanges = $('#btnSaveAttendanceChanges');

    // Manual Add Modal Fields
    const btnShowManualAddModalEl = $('#btnShowManualAddModal');
    const manualStudentIdInput = $('#manualStudentId');
    const manualStudentNameInput = $('#manualStudentName');
    const manualDateInput = $('#manualDate');
    const manualStatusSelect = $('#manualStatus');
    const manualWorkCodeInput = $('#manualWorkCode');
    const manualClockInTimeInput = $('#manualClockInTime');
    const manualClockOutTimeInput = $('#manualClockOutTime');
    const manualNotesTextarea = $('#manualNotes');
    const manualLatitudeInput = $('#manualLatitude');
    const manualLongitudeInput = $('#manualLongitude');
    const manualAddressInput = $('#manualAddress');
    const btnSubmitManualAdd = $('#btnSubmitManualAdd');

    // Store all fetched report data to easily find item for editing
    let currentReportData = []; 

    function showAlert(message, type = 'danger', targetAlertDiv = alertReportDiv) {
        targetAlertDiv.removeClass('d-none alert-success alert-info alert-warning alert-danger')
                      .addClass(`alert-${type}`)
                      .html(message)
                      .fadeIn();
        setTimeout(() => targetAlertDiv.fadeOut().addClass('d-none'), 7000);
    }

    function formatTime(isoString) {
        if (!isoString) return '-';
        try {
            return new Date(isoString).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: "{{ env('APP_TIMEZONE', 'Asia/Jakarta') }}" }); // Pastikan timezone benar
        } catch (e) { return isoString; } 
    }
    
    // Helper untuk format waktu ke HH:MM untuk input type="time"
    function formatTimeToInput(isoString) {
        if (!isoString) return '';
        try {
            const date = new Date(isoString);
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${hours}:${minutes}`;
        } catch (e) { return ''; }
    }
    
    // Helper untuk format HH:MM ke ISO string UTC untuk dikirim ke API
    // Menggunakan tanggal dari reportDateInput atau manualDateInput sebagai basis
    function formatInputTimeToISO(timeString, dateString) { // timeString "HH:mm", dateString "YYYY-MM-DD"
        if (!timeString || !dateString) return null;
        try {
            // Gabungkan tanggal dan waktu, lalu konversi ke UTC ISO string
            // Penting: Asumsikan dateString adalah dalam timezone aplikasi, dan waktu juga.
            // Kemudian konversi ke UTC untuk penyimpanan.
            const localDateTime = new Date(`${dateString}T${timeString}:00`); // Buat objek Date dalam timezone browser/sistem
            if (isNaN(localDateTime)) return null; // Invalid date
            
            // Untuk konsistensi dengan backend yang mungkin mengharapkan UTC dari Carbon:
            // Kita bisa membuat UTC string dengan menganggap input sudah merepresentasikan waktu di APP_TIMEZONE
            // lalu ubah ke UTC. Carbon di backend akan parse ISO string ini.
            const appTimezone = "{{ env('APP_TIMEZONE', 'Asia/Jakarta') }}";
            const dateObj = new Date(Date.parse(dateString + 'T' + timeString + ':00' + getOffset(appTimezone)));
            return dateObj.toISOString();

        } catch (e) {
            console.error("Error formatting input time to ISO:", e);
            return null;
        }
    }
    // Helper untuk mendapatkan offset timezone (misal +07:00)
    function getOffset(timeZone) {
        const date = new Date();
        const utcDate = new Date(date.toLocaleString('en-US', { timeZone: 'UTC' }));
        const tzDate = new Date(date.toLocaleString('en-US', { timeZone }));
        const offset = (tzDate.getTime() - utcDate.getTime()) / (60 * 60 * 1000);
        const sign = offset < 0 ? '-' : '+';
        const absOffset = Math.abs(offset);
        const hours = Math.floor(absOffset);
        const minutes = (absOffset - hours) * 60;
        return `${sign}${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
    }


    function formatDisplayDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            // Asumsi dateString adalah YYYY-MM-DD
            const date = new Date(dateString + 'T00:00:00'); // Tambahkan T00:00:00 agar tidak ada isu timezone parsing
            return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', timeZone: "{{ env('APP_TIMEZONE', 'Asia/Jakarta') }}"});
        } catch (e) { return dateString; }
    }

    function fetchDailyReport() {
        const selectedDate = reportDateInput.val();
        if (!selectedDate) {
            showAlert('Silakan pilih tanggal terlebih dahulu.', 'warning');
            return;
        }

        if (!token) {
            showAlert('Token tidak ditemukan. Silakan login kembali.');
            return;
        }

        loadingReportDiv.show();
        reportTableContainer.hide();
        reportTableBody.empty(); 
        noDataMessageDiv.hide();
        alertReportDiv.addClass('d-none');
        displayReportDate.text(formatDisplayDate(selectedDate));

        $.ajax({
            url: '/api/attendance/report/daily', // Endpoint API dari controller
            method: 'GET',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
            data: { date: selectedDate },
            success: function(reportDataFromServer) {
                loadingReportDiv.hide();
                currentReportData = reportDataFromServer || []; // Simpan data asli

                if (currentReportData && currentReportData.length > 0) {
                    applyClientSideFilterAndDisplay(currentReportData);
                } else {
                    noDataMessageDiv.text('Tidak ada data absensi untuk tanggal ' + formatDisplayDate(selectedDate) + '.').show();
                    reportTableContainer.hide();
                }
            },
            error: function(err) {
                // ... (error handling yang sudah ada)
                loadingReportDiv.hide();
                reportTableContainer.hide();
                let message = 'Gagal memuat laporan harian.';
                if (err.status === 401) {
                    message = 'Sesi Anda berakhir. Silakan login kembali.';
                    localStorage.removeItem('token');
                } else if (err.responseJSON) {
                    if (err.responseJSON.errors && err.responseJSON.errors.date) {
                        message = err.responseJSON.errors.date.join(', ');
                    } else if (err.responseJSON.message) {
                        message = err.responseJSON.message;
                    }
                }
                showAlert(message);
            }
        });
    }
    
    function applyClientSideFilterAndDisplay(sourceData) {
        let filteredData = sourceData;
        const searchTerm = searchInput.val().toLowerCase();
        const selectedDate = reportDateInput.val(); // Untuk pesan no data

        if (searchTerm) {
            filteredData = sourceData.filter(item =>
                (item.studentName && item.studentName.toLowerCase().includes(searchTerm)) ||
                (item.studentId && item.studentId.toLowerCase().includes(searchTerm))
            );
        }

        reportTableBody.empty(); // Bersihkan sebelum append
        if (filteredData.length > 0) {
            $.each(filteredData, function(index, item) {
                const clockInLat = parseFloat(item.clockInLatitude);
                const clockInLng = parseFloat(item.clockInLongitude);
                let locationText = item.clockInAddress || '-';
                if (!isNaN(clockInLat) && !isNaN(clockInLng)) {
                     // Koreksi URL Peta: menggunakan format standar dan template literal yang benar
                    locationText = `${item.clockInAddress ? item.clockInAddress + '<br>' : ''}${clockInLat.toFixed(5)}, ${clockInLng.toFixed(5)} <a href="https://maps.google.com/?q=${clockInLat},${clockInLng}" target="_blank" class="ms-1 small text-nowrap">(Lihat Peta)</a>`;
                }

                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.studentName || 'N/A'} <small class="d-block text-muted">(${item.studentId || 'N/A'})</small></td>
                        <td><span class="badge bg-${item.status === 'HADIR' ? 'success' : item.status === 'SAKIT' ? 'danger' : item.status === 'IZIN' ? 'warning' : 'secondary'}">${item.status || 'N/A'}</span></td>
                        <td>${formatTime(item.clockInTime)}</td>
                        <td>${formatTime(item.clockOutTime)}</td>
                        <td>${locationText}</td>
                        <td>${item.workCode || '-'}</td>
                        <td style="max-width: 200px; overflow-wrap: break-word;">${item.notes || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-edit" data-id="${item.id}" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </td>
                    </tr>
                `;
                reportTableBody.append(row);
            });
            reportTableContainer.show();
            noDataMessageDiv.hide();
        } else {
            noDataMessageDiv.text('Tidak ada data absensi yang cocok dengan pencarian untuk tanggal ' + formatDisplayDate(selectedDate) + '.').show();
            reportTableContainer.hide();
        }
    }

    btnShowReport.click(fetchDailyReport);
    searchInput.on('keyup', function() { // Menggunakan keyup untuk filter client-side yang lebih responsif
        applyClientSideFilterAndDisplay(currentReportData);
    });

    // --- LOGIKA UNTUK EDIT ABSENSI ---
    reportTableBody.on('click', '.btn-edit', function() {
        const attendanceId = $(this).data('id');
        const recordToEdit = currentReportData.find(item => item.id === attendanceId);

        if (recordToEdit) {
            alertModalEdit.addClass('d-none'); // Sembunyikan alert sebelumnya
            editAttendanceIdInput.val(recordToEdit.id);
            editStudentNameEl.text(recordToEdit.studentName || 'N/A');
            editStudentIdEl.text(recordToEdit.studentId || 'N/A');
            editStatusSelect.val(recordToEdit.status || 'HADIR');
            editWorkCodeInput.val(recordToEdit.workCode || '');
            editClockInTimeInput.val(formatTimeToInput(recordToEdit.clockInTime));
            editClockOutTimeInput.val(formatTimeToInput(recordToEdit.clockOutTime));
            editNotesTextarea.val(recordToEdit.notes || '');
            editLatitudeInput.val(recordToEdit.clockInLatitude || '');
            editLongitudeInput.val(recordToEdit.clockInLongitude || '');
            editAddressInput.val(recordToEdit.clockInAddress || '');
            
            editAttendanceModal.show();
        } else {
            showAlert('Data absensi tidak ditemukan untuk diedit.', 'warning');
        }
    });

    btnSaveAttendanceChanges.click(function() {
        const attendanceId = editAttendanceIdInput.val();
        const clockInISO = formatInputTimeToISO(editClockInTimeInput.val(), reportDateInput.val()); // Tanggal dari reportDateInput
        const clockOutISO = formatInputTimeToISO(editClockOutTimeInput.val(), reportDateInput.val());

        const updatedData = {
            status: editStatusSelect.val(),
            work_code: editWorkCodeInput.val() || null,
            clock_in_time: clockInISO,
            clock_out_time: clockOutISO,
            notes: editNotesTextarea.val() || null,
            latitude: editLatitudeInput.val() ? parseFloat(editLatitudeInput.val()) : null,
            longitude: editLongitudeInput.val() ? parseFloat(editLongitudeInput.val()) : null,
            address: editAddressInput.val() || null,
        };
        
        // Validasi sederhana di frontend
        if (!updatedData.status) {
             showAlert('Status tidak boleh kosong.', 'warning', alertModalEdit);
             return;
        }

        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

        $.ajax({
            url: `/api/absensi/update/${attendanceId}`, // Sesuaikan dengan route API Anda
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            contentType: 'application/json', // Kirim sebagai JSON
            data: JSON.stringify(updatedData),
            success: function(response) {
                showAlert('Absensi berhasil diperbarui.', 'success', alertModalEdit);
                editAttendanceModal.hide();
                fetchDailyReport(); // Refresh tabel
            },
            error: function(err) {
                let message = 'Gagal memperbarui absensi.';
                 if (err.responseJSON) {
                    message = err.responseJSON.message || (err.responseJSON.errors ? JSON.stringify(err.responseJSON.errors) : message);
                }
                showAlert(message, 'danger', alertModalEdit);
            },
            complete: function() {
                 btnSaveAttendanceChanges.prop('disabled', false).html('Simpan Perubahan');
            }
        });
    });

    // --- LOGIKA UNTUK TAMBAH ABSENSI MANUAL ---
    btnShowManualAddModalEl.click(function() {
        alertModalManualAdd.addClass('d-none');
        // Reset form manual add
        $('#manualAddAttendanceModal').find('input[type="text"], input[type="time"], input[type="date"], textarea').val('');
        $('#manualAddAttendanceModal').find('select').prop('selectedIndex', 0);
        manualDateInput.val(reportDateInput.val()); // Default ke tanggal laporan yang sedang dilihat
        manualAddAttendanceModal.show();
    });

    btnSubmitManualAdd.click(function() {
        const studentId = manualStudentIdInput.val();
        const selectedDate = manualDateInput.val();
        const clockInISO = formatInputTimeToISO(manualClockInTimeInput.val(), selectedDate);
        const clockOutISO = formatInputTimeToISO(manualClockOutTimeInput.val(), selectedDate);

        const manualData = {
            student_id: studentId,
            student_name: manualStudentNameInput.val() || null, // Opsional di frontend, backend bisa fetch jika kosong
            date: selectedDate, // YYYY-MM-DD
            status: manualStatusSelect.val(),
            work_code: manualWorkCodeInput.val() || null,
            clock_in_time: clockInISO, // Akan jadi ISO string UTC atau null
            clock_out_time: clockOutISO, // Akan jadi ISO string UTC atau null
            notes: manualNotesTextarea.val() || null,
            latitude: manualLatitudeInput.val() ? parseFloat(manualLatitudeInput.val()) : null,
            longitude: manualLongitudeInput.val() ? parseFloat(manualLongitudeInput.val()) : null,
            address: manualAddressInput.val() || null,
        };

        if (!manualData.student_id || !manualData.date || !manualData.status) {
            showAlert('ID Siswa, Tanggal, dan Status wajib diisi.', 'warning', alertModalManualAdd);
            return;
        }
        if (manualData.status === 'HADIR' && !manualData.clock_in_time) {
            showAlert('Jam Masuk wajib diisi jika status HADIR.', 'warning', alertModalManualAdd);
            return;
        }


        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

        $.ajax({
            url: '/api/absensi/manual-add', // Sesuaikan dengan route API Anda
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            contentType: 'application/json',
            data: JSON.stringify(manualData),
            success: function(response) {
                showAlert('Absensi manual berhasil ditambahkan.', 'success', alertModalManualAdd);
                manualAddAttendanceModal.hide();
                // Jika tanggal yang ditambahkan sama dengan tanggal laporan, refresh
                if (manualData.date === reportDateInput.val()) {
                    fetchDailyReport();
                } else {
                    // Jika beda tanggal, cukup tampilkan pesan sukses atau arahkan ke tanggal baru
                    showAlert('Absensi manual untuk tanggal ' + formatDisplayDate(manualData.date) + ' berhasil. Silakan tampilkan laporan untuk tanggal tersebut.', 'info');
                }
            },
            error: function(err) {
                let message = 'Gagal menambahkan absensi manual.';
                if (err.responseJSON) {
                    message = err.responseJSON.message || (err.responseJSON.errors ? JSON.stringify(err.responseJSON.errors) : message);
                }
                showAlert(message, 'danger', alertModalManualAdd);
            },
            complete: function() {
                btnSubmitManualAdd.prop('disabled', false).html('Simpan Absensi Manual');
            }
        });
    });
    
    // Font Awesome (jika belum ada di layout utama Anda)
    // $('<link/>', {rel:'stylesheet', type:'text/css', href:'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'}).appendTo('head');


    // Langsung tampilkan laporan untuk tanggal hari ini saat halaman dimuat
    fetchDailyReport();
});
</script>
@endpush