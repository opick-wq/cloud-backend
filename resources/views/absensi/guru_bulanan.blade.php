@extends('layouts.app') {{-- Asumsi layout utama Anda --}}

@section('title', 'Laporan Absensi Bulanan')

@section('styles')
<style>
    .table-absensi-bulanan th, .table-absensi-bulanan td {
        text-align: center;
        vertical-align: middle;
        min-width: 35px; /* Lebar minimum untuk kolom tanggal */
        font-size: 0.85rem;
    }
    .table-absensi-bulanan .student-name {
        text-align: left;
        min-width: 150px;
    }
    .weekend-header, .weekend-cell {
        background-color: #f8d7da !important; /* Warna merah muda untuk akhir pekan */
        color: #721c24;
    }
    .status-hadir { color: green; font-weight: bold; }
    .status-sakit { color: red; font-weight: bold; }
    .status-izin { color: blue; font-weight: bold; }
    .status-alpha { color: #6c757d; }
</style>
@endsection

@section('content')
<div class="container-fluid mt-4"> {{-- Gunakan container-fluid untuk tabel lebar --}}
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Laporan Absensi Bulanan Siswa</h4>
        </div>
        <div class="card-body">
            <div id="alertReportMonthly" class="alert d-none"></div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="reportYear" class="form-label">Pilih Tahun:</label>
                    <select class="form-select" id="reportYear" name="report_year">
                        {{-- Tahun akan diisi oleh JavaScript --}}
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="reportMonth" class="form-label">Pilih Bulan:</label>
                    <select class="form-select" id="reportMonth" name="report_month">
                        {{-- Bulan akan diisi oleh JavaScript --}}
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="searchInputMonthly" class="form-label">Cari Nama Siswa:</label>
                    <input type="text" class="form-control" id="searchInputMonthly" placeholder="Masukkan nama siswa">
                </div>
                <div class="col-md-3 align-self-end">
                    <button class="btn btn-primary w-100 mb-2" id="btnShowMonthlyReport">Tampilkan Laporan</button>
                    <button class="btn btn-success w-100" id="btnExportExcel" style="display: none;">Ekspor ke Excel</button>
                </div>
            </div>

            <div id="loadingMonthlyReport" class="text-center py-3" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>
                <p class="mt-2">Memuat laporan absensi bulanan...</p>
            </div>

            <div class="table-responsive mt-3" id="monthlyReportTableContainer" style="display: none;">
                <h5 class="mb-2">Laporan untuk Bulan: <span id="displayReportMonthYear"></span></h5>
                <table class="table table-bordered table-hover table-absensi-bulanan">
                    <thead class="table-light" id="monthlyReportThead">
                        {{-- Header tabel (tanggal) akan diisi oleh JavaScript --}}
                    </thead>
                    <tbody id="monthlyReportTableBody">
                        {{-- Data absensi akan diisi oleh JavaScript --}}
                    </tbody>
                </table>
            </div>
            <div id="noDataMonthlyMessage" class="alert alert-info mt-3" style="display: none;">
                Tidak ada data absensi untuk bulan dan tahun yang dipilih.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script> {{-- Tambahkan library xlsx --}}
<script>
$(document).ready(function() {
    const token = localStorage.getItem('token');
    const reportYearSelect = $('#reportYear');
    const reportMonthSelect = $('#reportMonth');
    const btnShowMonthlyReport = $('#btnShowMonthlyReport');
    const loadingMonthlyReportDiv = $('#loadingMonthlyReport');
    const monthlyReportTableContainer = $('#monthlyReportTableContainer');
    const monthlyReportThead = $('#monthlyReportThead');
    const monthlyReportTableBody = $('#monthlyReportTableBody');
    const displayReportMonthYear = $('#displayReportMonthYear');
    const alertReportMonthlyDiv = $('#alertReportMonthly');
    const noDataMonthlyMessageDiv = $('#noDataMonthlyMessage');
    const searchInputMonthly = $('#searchInputMonthly');
    const btnExportExcel = $('#btnExportExcel');

    // Isi Pilihan Tahun
    const currentYear = new Date().getFullYear();
    for (let y = currentYear; y >= currentYear - 5; y--) {
        reportYearSelect.append(new Option(y, y));
    }
    reportYearSelect.val(currentYear);

    // Isi Pilihan Bulan
    const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    months.forEach((month, index) => {
        reportMonthSelect.append(new Option(month, index + 1));
    });
    reportMonthSelect.val(new Date().getMonth() + 1);


    function showAlert(message, type = 'danger') {
        alertReportMonthlyDiv.removeClass('d-none alert-success alert-info alert-warning alert-danger')
                        .addClass(`alert-${type}`)
                        .html(message)
                        .fadeIn();
        setTimeout(() => alertReportMonthlyDiv.fadeOut().addClass('d-none'), 7000);
    }

    function getStatusSymbol(status) {
        switch(status) {
            case 'HADIR': return '✓'; // Centang hijau
            case 'SAKIT': return 'S'; // S merah
            case 'IZIN': return 'I';   // I biru
            case 'ALPHA': return 'A'; // A abu-abu
            default: return ''; // Kosong jika tidak ada status atau status lain
        }
    }

    function fetchMonthlyReport() {
        const selectedYear = reportYearSelect.val();
        const selectedMonth = reportMonthSelect.val();

        if (!selectedYear || !selectedMonth) {
            showAlert('Silakan pilih tahun dan bulan terlebih dahulu.', 'warning');
            return;
        }
        if (!token) {
            showAlert('Token tidak ditemukan. Silakan login kembali.');
            return;
        }

        loadingMonthlyReportDiv.show();
        monthlyReportTableContainer.hide();
        monthlyReportThead.empty();
        monthlyReportTableBody.empty();
        noDataMonthlyMessageDiv.hide();
        alertReportMonthlyDiv.addClass('d-none');
        btnExportExcel.hide(); // Sembunyikan tombol ekspor saat memuat
        const monthName = months[selectedMonth - 1];
        displayReportMonthYear.text(`${monthName} ${selectedYear}`);

        $.ajax({
            url: '/api/attendance/report/monthly',
            method: 'GET',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
            data: { year: selectedYear, month: selectedMonth },
            success: function(response) {
                loadingMonthlyReportDiv.hide();
                const reportData = response.report;
                const daysInMonth = response.daysInMonth;

                if (reportData && reportData.length > 0 && daysInMonth > 0) {
                    let filteredData = reportData;
                    const searchTerm = searchInputMonthly.val().toLowerCase();

                    if (searchTerm) {
                        filteredData = reportData.filter(item =>
                            (item.studentName && item.studentName.toLowerCase().includes(searchTerm)) ||
                            (item.studentId && item.studentId.toLowerCase().includes(searchTerm))
                        );
                    }

                    if (filteredData.length > 0) {
                        // Buat Header Tabel
                        let headerRow = '<tr><th>No.</th><th class="student-name">Nama Siswa</th>';
                        for (let day = 1; day <= daysInMonth; day++) {
                            const date = new Date(selectedYear, selectedMonth - 1, day);
                            const dayOfWeek = date.getDay(); // 0 (Minggu) - 6 (Sabtu)
                            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                            headerRow += `<th class="${isWeekend ? 'weekend-header' : ''}">${day}</th>`;
                        }
                        headerRow += '<th>Jmlh Hadir</th></tr>';
                        monthlyReportThead.html(headerRow);

                        // Isi Body Tabel
                        monthlyReportTableBody.empty(); // Bersihkan isi tabel sebelum menambahkan data
                        $.each(filteredData, function(index, studentData) {
                            let bodyRow = `<tr><td>${index + 1}</td><td class="student-name">${studentData.studentName || 'N/A'}</td>`;
                            let totalHadir = 0;
                            for (let day = 1; day <= daysInMonth; day++) {
                                const date = new Date(selectedYear, selectedMonth - 1, day);
                                const dayOfWeek = date.getDay();
                                const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                                const status = studentData.days[day] || ''; // Status untuk hari itu
                                if (status === 'HADIR') {
                                    totalHadir++;
                                }
                                bodyRow += `<td class="${isWeekend ? 'weekend-cell' : ''}">${getStatusSymbol(status)}</td>`;
                            }
                            bodyRow += `<td>${totalHadir}</td></tr>`;
                            monthlyReportTableBody.append(bodyRow);
                        });

                        monthlyReportTableContainer.show();
                        btnExportExcel.show(); // Tampilkan tombol ekspor setelah data dimuat
                    } else {
                        noDataMonthlyMessageDiv.text(`Tidak ada data absensi yang cocok dengan pencarian untuk ${monthName} ${selectedYear}.`).show();
                    }
                } else {
                    noDataMonthlyMessageDiv.text(`Tidak ada data absensi untuk ${monthName} ${selectedYear}.`).show();
                }
            },
            error: function(err) {
                loadingMonthlyReportDiv.hide();
                let message = 'Gagal memuat laporan bulanan.';
                if (err.status === 401) {
                    message = 'Sesi Anda berakhir. Silakan login kembali.';
                    localStorage.removeItem('token');
                } else if (err.responseJSON) {
                    if (err.responseJSON.errors) {
                        let validationMessages = [];
                        for (const key in err.responseJSON.errors) {
                            validationMessages.push(err.responseJSON.errors[key].join(', '));
                        }
                        message = validationMessages.join('<br>');
                    } else if (err.responseJSON.message) {
                        message = err.responseJSON.message;
                    }
                }
                showAlert(message);
            }
        });
    }

    btnShowMonthlyReport.click(fetchMonthlyReport);
    searchInputMonthly.on('input', fetchMonthlyReport);

    // Fungsi Ekspor ke Excel
    btnExportExcel.click(function() {
        const selectedYear = reportYearSelect.val();
        const selectedMonth = reportMonthSelect.val();
        const monthName = months[selectedMonth - 1];
        const fileName = `Laporan Absensi Bulanan - ${monthName} ${selectedYear}.xlsx`;

        // Persiapkan data untuk Excel
        let excelData = [];
        excelData.push([`Laporan Absensi Bulanan - ${monthName} ${selectedYear}`]); // Judul
        excelData.push([]); // Baris kosong untuk pemisah

        // Header Tabel
        let headerRow = ['No.', 'Nama Siswa'];
        for (let day = 1; day <= new Date(selectedYear, selectedMonth, 0).getDate(); day++) {
            headerRow.push(day.toString());
        }
        headerRow.push('Jmlh Hadir');
        excelData.push(headerRow);

        // Data Tabel
        $('#monthlyReportTableBody tr').each(function() {
            let rowData = [];
            $(this).find('td').each(function(index) {
                if (index === 0) {
                    rowData.push(parseInt($(this).text())); // No
                } else {
                    rowData.push($(this).text());
                }

            });
            excelData.push(rowData);
        });

        // Buat Workbook
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(excelData);
        XLSX.utils.book_append_sheet(wb, ws, `Absensi ${monthName} ${selectedYear}`);

        // Styling (Sederhana - perlu penyesuaian lebih lanjut untuk styling kompleks)
        // Contoh: Menebalkan judul
        ws['A1'].s = { font: { bold: true, sz: 16 } };

        // Simpan File
        XLSX.writeFile(wb, fileName);
    });

    // Opsional: Langsung tampilkan laporan untuk bulan dan tahun saat ini saat halaman dimuat
    // fetchMonthlyReport();
});
</script>
@endpush