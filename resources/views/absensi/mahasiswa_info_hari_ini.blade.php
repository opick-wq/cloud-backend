@extends('layouts.app') {{-- Asumsi layout utama Anda --}}

@section('title', 'Info Absensi Hari Ini')

@section('styles')
<style>
    #mapPreview {
        width: 100%;
        height: 250px; /* Sesuaikan tinggi peta */
        background-color: #e9e9e9;
        border: 1px solid #ccc;
        margin-top: 10px;
        text-align: center;
        line-height: 250px; /* Vertically center text */
        color: #666;
    }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Presensi Info - {{ \Carbon\Carbon::now(env('APP_TIMEZONE', 'Asia/Jakarta'))->isoFormat('dddd, D MMMM YYYY') }}</h4>
                </div>
                <div class="card-body">
                    <div id="alertInfo" class="alert d-none"></div>
                    <div id="loadingInfo" class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        <p class="mt-2">Memuat data absensi Anda...</p>
                    </div>

                    <div id="attendanceData" style="display: none;">
                        <div class="mb-3">
                            <label for="workCode" class="form-label">Pilih Work Code <span class="text-danger">*</span>:</label>
                            <select class="form-select" id="workCode" name="work_code">
                                <option value="">-- Pilih Work Code --</option>
                                <option value="SEKOLAH">Sekolah</option>
                                <option value="MAGANG">Magang</option>
                                <option value="SEKOLAH ONLINE">Sekolah Online</option>
                            </select>
                        </div>

                        <div class="row text-center mb-3">
                            <div class="col">
                                <strong>Jam Masuk:</strong>
                                <p id="clockInTime" class="fs-5">-</p>
                            </div>
                            <div class="col">
                                <strong>Jam Keluar/Sakit:</strong>
                                <p id="clockOutTime" class="fs-5">-</p>
                            </div>
                        </div>
                        <p class="text-center"><strong>Status:</strong> <span id="status" class="badge bg-secondary">Belum ada data</span></p>
                        
                        {{-- Tempat untuk menampilkan Peta dan Info Lokasi --}}
                        <div id="mapSection" class="mt-3" style="display:none;">
                            <h5>Lokasi Absen:</h5>
                            <div id="mapPreview">
                                Peta akan tampil di sini setelah absensi atau saat memuat data.
                            </div>
                            <p class="text-center small text-muted mt-1">
                                Koordinat: <span id="locationCoords">-</span> | 
                                <a href="#" id="mapLink" target="_blank">Buka di Google Maps</a>
                            </p>
                            <p id="locationAddress" class="text-center small text-muted">-</p>
                        </div>

                        <div id="notesInfo" class="mb-3" style="display:none;">
                            <strong>Catatan:</strong> <p id="notesText" class="fst-italic">-</p>
                        </div>
                    </div>

                    <div id="actionButtons" class="mt-3" style="display: none;">
                        <button class="btn btn-success w-100 mb-2" id="btnDatang">Datang (Ambil Lokasi Saat Ini)</button>
                        <button class="btn btn-danger w-100" id="btnSakit">Sakit</button>
                        <div class="mb-3 mt-2" id="notesSakitContainer" style="display:none;">
                            <label for="notes_sakit" class="form-label">Catatan Sakit <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="notes_sakit" name="notes_sakit" rows="2" placeholder="Contoh: Demam, perlu istirahat."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function() {
    const token = localStorage.getItem('token');
    const loadingDiv = $('#loadingInfo');
    const attendanceDataDiv = $('#attendanceData');
    const actionButtonsDiv = $('#actionButtons');
    const alertDiv = $('#alertInfo');

    const clockInTimeEl = $('#clockInTime');
    const clockOutTimeEl = $('#clockOutTime');
    const statusEl = $('#status');
    
    const mapSectionEl = $('#mapSection');
    const mapPreviewEl = $('#mapPreview');
    const locationCoordsEl = $('#locationCoords');
    const mapLinkEl = $('#mapLink');
    const locationAddressEl = $('#locationAddress');
    
    const notesInfoEl = $('#notesInfo');
    const notesTextEl = $('#notesText');

    const workCodeSelect = $('#workCode');
    const btnDatang = $('#btnDatang');
    const btnSakit = $('#btnSakit');
    const notesSakitContainer = $('#notesSakitContainer');
    const notesSakitInput = $('#notes_sakit');

    // Variabel untuk menyimpan koordinat saat ini (jika diperlukan)
    let currentLatitude = null;
    let currentLongitude = null;

    function showAlert(message, type = 'danger') {
        alertDiv.removeClass('d-none alert-success alert-info alert-warning alert-danger')
                .addClass(`alert-${type}`)
                .html(message)
                .fadeIn();
        setTimeout(() => alertDiv.fadeOut().addClass('d-none'), 5000);
    }

    function formatTimeWIB(isoString) {
        if (!isoString) return '-';
        try {
            return new Date(isoString).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' });
        } catch (e) { return '-'; }
    }

    // Fungsi untuk menampilkan peta embed atau link
    function displayMap(latitude, longitude, address = null) {
        if (latitude && longitude) {
            const lat = parseFloat(latitude).toFixed(6);
            const lon = parseFloat(longitude).toFixed(6);
            locationCoordsEl.text(`${lat}, ${lon}`);
            const gmapsLink = `https://www.google.com/maps?q=${lat},${lon}`;
            mapLinkEl.attr('href', gmapsLink);
            
            // Google Maps Embed API - mode 'view' tidak perlu API Key
            // q parameter adalah apa yang dicari, centerpoint parameter tidak ada di mode view
            // view?q=lat,lng&zoom=15
            const mapEmbedUrl = `https://www.google.com/maps/embed/v1/view?key=YOUR_API_KEY_IF_NEEDED_FOR_OTHER_MODES&center=${lat},${lon}&zoom=17`;
            // Karena mode view lebih baik pakai q=lat,lng
            const mapEmbedUrlSimple = `https://maps.google.com/maps?q=${lat},${lon}&hl=es;z=14&amp;output=embed`;

            // Kita pakai iframe dengan src yang lebih sederhana yang biasanya work
            mapPreviewEl.html(`<iframe width="100%" height="100%" frameborder="0" style="border:0" src="https://maps.google.com/maps?q=${lat},${lon}&z=15&output=embed" allowfullscreen></iframe>`);
            
            locationAddressEl.text(address || 'Alamat tidak tersedia');
            mapSectionEl.show();
        } else {
            mapPreviewEl.html('Koordinat lokasi tidak tersedia untuk ditampilkan di peta.');
            locationCoordsEl.text('-');
            mapLinkEl.attr('href', '#');
            locationAddressEl.text('-');
            mapSectionEl.show(); // Tetap tampilkan section tapi dengan pesan
        }
    }


    function updateAttendanceUI(data) {
        if (data && data.id) {
            clockInTimeEl.text(formatTimeWIB(data.clockInTime));
            clockOutTimeEl.text(formatTimeWIB(data.clockOutTime));
            statusEl.text(data.status || 'N/A').removeClass('bg-secondary bg-success bg-danger bg-warning').addClass(
                data.status === 'HADIR' ? 'bg-success' :
                data.status === 'SAKIT' ? 'bg-danger' :
                data.status === 'IZIN' ? 'bg-warning' : 'bg-secondary'
            );
            workCodeSelect.val(data.workCode || '');

            displayMap(data.clockInLatitude, data.clockInLongitude, data.clockInAddress);

            if (data.notes) {
                notesTextEl.text(data.notes);
                notesInfoEl.show();
            } else {
                notesInfoEl.hide();
            }

            if (data.status === 'HADIR' || data.status === 'SAKIT') { // Jika sudah hadir atau sudah sakit hari ini
                btnDatang.hide();
                workCodeSelect.prop('disabled', true);
                if (data.status === 'HADIR') {
                    btnSakit.show().text('Saya Sakit (Sudah Datang)');
                    notesSakitContainer.show();
                } else { // Jika status SAKIT
                    btnSakit.hide();
                    notesSakitContainer.hide();
                }
            } else { // Belum ada absensi atau status lain yang memungkinkan aksi
                btnDatang.show();
                btnSakit.show().text('Saya Sakit (Tidak Masuk)');
                notesSakitContainer.hide();
                workCodeSelect.prop('disabled', false);
                mapSectionEl.hide(); // Sembunyikan peta jika belum absen
            }
            actionButtonsDiv.show();
        } else { // Belum ada data absensi
            clockInTimeEl.text('-');
            clockOutTimeEl.text('-');
            statusEl.text('Belum Absen').addClass('bg-secondary');
            mapPreviewEl.html('Peta akan tampil di sini setelah Anda melakukan absensi Datang.');
            locationCoordsEl.text('-');
            mapLinkEl.attr('href', '#');
            locationAddressEl.text('-');
            mapSectionEl.hide(); // Sembunyikan peta jika belum absen
            notesInfoEl.hide();
            btnDatang.show();
            btnSakit.show().text('Saya Sakit (Tidak Masuk)');
            notesSakitContainer.hide();
            workCodeSelect.prop('disabled', false);
            actionButtonsDiv.show();
        }
        loadingDiv.hide();
        attendanceDataDiv.show();
    }

    function fetchTodaysAttendance() {
        loadingDiv.show();
        attendanceDataDiv.hide();
        actionButtonsDiv.hide();
        alertDiv.addClass('d-none');

        if (!token) {
            showAlert('Token tidak ditemukan. Silakan login kembali.');
            loadingDiv.hide();
            return;
        }

        $.ajax({
            url: '/api/attendance/today',
            method: 'GET',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
            success: function(res) {
                updateAttendanceUI(res);
            },
            error: function(err) {
                loadingDiv.hide();
                actionButtonsDiv.show();
                attendanceDataDiv.show();
                if (err.status === 404) {
                    updateAttendanceUI(null);
                } else if (err.status === 401) {
                    showAlert('Sesi Anda berakhir. Silakan login kembali.');
                    localStorage.removeItem('token');
                } else {
                    showAlert(err.responseJSON?.message || 'Gagal memuat data absensi.');
                }
            }
        });
    }

    function getCurrentLocation(callbackSuccess, callbackError) {
        showAlert('Mengambil lokasi Anda saat ini...', 'info');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    alertDiv.addClass('d-none'); // Sembunyikan alert "Mengambil lokasi..."
                    currentLatitude = position.coords.latitude;
                    currentLongitude = position.coords.longitude;
                    displayMap(currentLatitude, currentLongitude, "Lokasi Anda saat ini"); // Tampilkan peta langsung
                    callbackSuccess({
                        latitude: currentLatitude,
                        longitude: currentLongitude
                    });
                },
                function(error) {
                    let message = "Tidak dapat mengakses lokasi: ";
                    switch(error.code) {
                        case error.PERMISSION_DENIED: message += "Izin lokasi ditolak."; break;
                        case error.POSITION_UNAVAILABLE: message += "Informasi lokasi tidak tersedia."; break;
                        case error.TIMEOUT: message += "Timeout saat mengambil lokasi."; break;
                        default: message += "Error tidak diketahui."; break;
                    }
                    callbackError(message);
                },
                { timeout:15000, enableHighAccuracy: true, maximumAge: 0 } // maximumAge: 0 untuk memaksa lokasi baru
            );
        } else {
            callbackError("Geolocation tidak didukung oleh browser ini.");
        }
    }

    function submitAttendance(actionType, notes = null) {
        if (!token) {
            showAlert('Token tidak ditemukan. Silakan login kembali.');
            return;
        }
        btnDatang.prop('disabled', true);
        btnSakit.prop('disabled', true);
        showAlert('Sedang memproses absensi...', 'info');

        const attendancePayload = {
            action: actionType,
            work_code: workCodeSelect.val(),
            notes: notes
        };

        if (actionType === 'DATANG') {
            // GPS diambil oleh fungsi getCurrentLocation yang dipanggil oleh btnDatang.click
            // dan disimpan di currentLatitude, currentLongitude
            if (currentLatitude && currentLongitude) {
                attendancePayload.latitude = currentLatitude;
                attendancePayload.longitude = currentLongitude;
                // Anda bisa menambahkan reverse geocoding di sini atau di server
                // attendancePayload.address = "Alamat dari lat/long";
                sendSubmitRequest(attendancePayload);
            } else {
                 // Jika currentLatitude/Longitude null, coba ambil lagi (sebagai fallback)
                 // atau tampilkan error bahwa lokasi belum didapatkan.
                 // Untuk saat ini, tombol Datang seharusnya hanya aktif setelah lokasi didapat.
                 // Namun, kita buat pengaman:
                getCurrentLocation(
                    function(location) {
                        attendancePayload.latitude = location.latitude;
                        attendancePayload.longitude = location.longitude;
                        sendSubmitRequest(attendancePayload);
                    },
                    function(errorMsg) {
                        showAlert(`Lokasi GPS diperlukan dan gagal diambil. ${errorMsg}`, 'danger');
                        btnDatang.prop('disabled', false); // Enable tombol lagi
                        btnSakit.prop('disabled', false); // Enable tombol lagi
                    }
                );
            }
        } else if (actionType === 'SAKIT') {
            sendSubmitRequest(attendancePayload);
        }
    }

    function sendSubmitRequest(payload) {
         $.ajax({
            url: '/api/attendance/submit',
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
            data: payload,
            success: function(res) {
                showAlert(res.message || 'Aksi berhasil dicatat.', 'success');
                fetchTodaysAttendance(); // Muat ulang data absensi
            },
            error: function(err) {
                let errorMsg = 'Gagal mencatat aksi.';
                if (err.responseJSON) {
                    if (err.responseJSON.errors) {
                        let validationMessages = [];
                        for (const key in err.responseJSON.errors) {
                            if (key !== 'is_wfh') {
                                validationMessages.push(err.responseJSON.errors[key].join(', '));
                            }
                        }
                        errorMsg = validationMessages.length > 0 ? validationMessages.join('<br>') : errorMsg;
                    } else if (err.responseJSON.message) {
                        errorMsg = err.responseJSON.message;
                    }
                }
                showAlert(errorMsg);
                // Karena tombol di-disable di submitAttendance, fetchTodaysAttendance akan re-enable berdasarkan UI
                fetchTodaysAttendance(); 
            }
        });
    }

    btnDatang.click(function() {
        if (!workCodeSelect.val()) {
            showAlert('Silakan pilih Work Code terlebih dahulu.', 'warning');
            return;
        }
        // Ambil lokasi dulu, baru submit
        getCurrentLocation(
            function(location) { // success callback dari getCurrentLocation
                // location.latitude dan location.longitude sudah diset ke currentLatitude/Longitude
                // jadi kita bisa langsung panggil submitAttendance
                submitAttendance('DATANG');
            },
            function(errorMsg) { // error callback dari getCurrentLocation
                showAlert(`Gagal mengambil lokasi: ${errorMsg}. Absensi Datang tidak bisa dilakukan.`, 'danger');
                // Tombol akan di-enable lagi oleh fetchTodaysAttendance jika gagal.
            }
        );
    });

    btnSakit.click(function() {
        const currentStatusText = statusEl.text().trim();
        if (currentStatusText !== 'HADIR' && notesSakitContainer.is(':hidden')) {
            notesSakitContainer.show();
            showAlert('Silakan isi catatan sakit Anda lalu tekan tombol "Saya Sakit" lagi.', 'info');
            return;
        }
        const notesValue = notesSakitInput.val().trim();
        if (!notesValue) {
            showAlert('Catatan sakit tidak boleh kosong.', 'warning');
            notesSakitInput.focus();
            return;
        }
        submitAttendance('SAKIT', notesValue);
    });

    fetchTodaysAttendance();
});
</script>
@endpush