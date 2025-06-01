<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Detail Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Ensure you have a valid CSRF token here if not using a framework that handles it automatically --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .modal-body .form-label {
            font-weight: 500;
        }
        .checkbox-group label {
            margin-right: 15px;
            font-weight: normal; /* Ensure labels are not too bold */
        }
        .checkbox-group .form-check {
            margin-bottom: 0.5rem; /* Add some space between checkbox items */
        }
    </style>
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="bg-white rounded shadow p-4">
            <h1 class="mb-4">Data Detail Siswa</h1>

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" id="addDetailSiswaBtn" class="btn btn-success">
                        Tambah Detail Siswa
                    </button>
                </div>
                <form id="searchForm" class="d-flex">
                    <input type="text" id="searchInput" class="form-control me-2" placeholder="Cari Alamat Asal atau No. Telp" value="">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </form>
            </div>

            <div id="alertContainer">
                {{-- Pesan sukses/error akan ditampilkan di sini oleh JavaScript --}}
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Alamat Asal</th>
                            <th>Nomor Telp/HP</th>
                            <th>Jarak Rumah (KM)</th>
                            <th>Tempat Tinggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="detailSiswaTableBody">
                        <tr>
                            <td colspan="6" class="text-center">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="paginationContainer" class="mt-3"></div>
        </div>
    </div>

    <div class="modal fade" id="detailSiswaModal" tabindex="-1" aria-labelledby="detailSiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailSiswaModalLabel">Form Detail Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formDetailSiswa">
                        <input type="hidden" id="formSiswaId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="formAlamatAsal" class="form-label">Alamat Asal</label>
                                <textarea class="form-control" id="formAlamatAsal" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="formNomorTelpHp" class="form-label">Nomor Telp/HP Asal</label>
                                <input type="text" class="form-control" id="formNomorTelpHp" placeholder="Contoh: 08123456789">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Termasuk Daerah Asal:</label>
                            <div class="checkbox-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_asal" value="Dalam kota" id="daDalamKota">
                                    <label class="form-check-label" for="daDalamKota">Dalam kota</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_asal" value="Pinggir kota" id="daPinggirKota">
                                    <label class="form-check-label" for="daPinggirKota">Pinggir kota</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_asal" value="Luar kota" id="daLuarKota">
                                    <label class="form-check-label" for="daLuarKota">Luar kota</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_asal" value="Pinggir sungai" id="daPinggirSungai">
                                    <label class="form-check-label" for="daPinggirSungai">Pinggir sungai</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_asal" value="Daerah pegunungan" id="daPegunungan">
                                    <label class="form-check-label" for="daPegunungan">Daerah pegunungan</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="formAlamatSekarang" class="form-label">Alamat Sekarang (Jika Berbeda)</label>
                                <textarea class="form-control" id="formAlamatSekarang" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="formNomorTelpHpSekarang" class="form-label">Nomor Telp/HP Sekarang</label>
                                <input type="text" class="form-control" id="formNomorTelpHpSekarang" placeholder="Contoh: 08123456789">
                            </div>
                        </div>
                         <div class="mb-3">
                            <label class="form-label d-block">Termasuk Daerah Sekarang:</label>
                            <div class="checkbox-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_sekarang" value="Dalam kota" id="dsDalamKota">
                                    <label class="form-check-label" for="dsDalamKota">Dalam kota</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_sekarang" value="Pinggir kota" id="dsPinggirKota">
                                    <label class="form-check-label" for="dsPinggirKota">Pinggir kota</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_sekarang" value="Luar kota" id="dsLuarKota">
                                    <label class="form-check-label" for="dsLuarKota">Luar kota</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_sekarang" value="Pinggir sungai" id="dsPinggirSungai">
                                    <label class="form-check-label" for="dsPinggirSungai">Pinggir sungai</label>
                                </div>
                                 <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_sekarang" value="Daerah pegunungan" id="dsPegunungan">
                                    <label class="form-check-label" for="dsPegunungan">Daerah pegunungan</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="formJarakRumahSekolah" class="form-label">Jarak Rumah ke Sekolah (KM)</label>
                                <input type="number" class="form-control" id="formJarakRumahSekolah" placeholder="Angka saja, contoh: 5">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="formTempatTinggal" class="form-label">Tempat Tinggal</label>
                                <select class="form-select" id="formTempatTinggal">
                                    <option value="">Pilih...</option>
                                    <option value="Rumah sendiri">Rumah sendiri</option>
                                    <option value="Rumah dinas">Rumah dinas</option>
                                    <option value="Rumah kontrakan">Rumah kontrakan</option>
                                    <option value="Rumah nenek/kakek">Rumah nenek/kakek</option>
                                    <option value="Kamar kost">Kamar kost</option>
                                    <option value="Lainnya (teks)">Lainnya (teks)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Alat/Sarana ke Sekolah:</label>
                            <div class="checkbox-group">
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="alat_sarana_ke_sekolah" value="Jalan kaki" id="asJalanKaki"><label class="form-check-label" for="asJalanKaki">Jalan kaki</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="alat_sarana_ke_sekolah" value="Naik sepeda" id="asNaikSepeda"><label class="form-check-label" for="asNaikSepeda">Naik sepeda</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="alat_sarana_ke_sekolah" value="Naik sepeda motor" id="asNaikMotor"><label class="form-check-label" for="asNaikMotor">Naik sepeda motor</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="alat_sarana_ke_sekolah" value="Diantar orang tua" id="asDiantarOrtu"><label class="form-check-label" for="asDiantarOrtu">Diantar orang tua</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="alat_sarana_ke_sekolah" value="Naik taksi/ojek" id="asTaksiOjek"><label class="form-check-label" for="asTaksiOjek">Naik taksi/ojek</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="alat_sarana_ke_sekolah" value="Naik mobil pribadi" id="asMobilPribadi"><label class="form-check-label" for="asMobilPribadi">Naik mobil pribadi</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="alat_sarana_ke_sekolah" value="Lainnya (teks)" id="asLainnya"><label class="form-check-label" for="asLainnya">Lainnya (teks)</label></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Tinggal Bersama:</label>
                             <div class="checkbox-group">
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Ayah dan ibu kandung" id="tbAyahIbuKandung"><label class="form-check-label" for="tbAyahIbuKandung">Ayah & Ibu Kandung</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Ayah kandung dan ibu tiri" id="tbAyahKandungIbuTiri"><label class="form-check-label" for="tbAyahKandungIbuTiri">Ayah Kandung & Ibu Tiri</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Ayah tiri dan ibu kandung" id="tbAyahTiriIbuKandung"><label class="form-check-label" for="tbAyahTiriIbuKandung">Ayah Tiri & Ibu Kandung</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Ayah kandung saja" id="tbAyahKandungSaja"><label class="form-check-label" for="tbAyahKandungSaja">Ayah Kandung Saja</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Ibu kandung saja" id="tbIbuKandungSaja"><label class="form-check-label" for="tbIbuKandungSaja">Ibu Kandung Saja</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Nenek/Kakek" id="tbNenekKakek"><label class="form-check-label" for="tbNenekKakek">Nenek/Kakek</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Saudara kandung" id="tbSaudaraKandung"><label class="form-check-label" for="tbSaudaraKandung">Saudara Kandung</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Sendiri" id="tbSendiri"><label class="form-check-label" for="tbSendiri">Sendiri</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Wali (teks)" id="tbWali"><label class="form-check-label" for="tbWali">Wali (teks)</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tinggal_bersama" value="Lainnya (teks)" id="tbLainnya"><label class="form-check-label" for="tbLainnya">Lainnya (teks)</label></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="formRumahTerbuatDari" class="form-label">Rumah Terbuat Dari</label>
                            <select class="form-select" id="formRumahTerbuatDari">
                                <option value="">Pilih...</option>
                                <option value="Tembok beton">Tembok beton</option>
                                <option value="Setengah kayu">Setengah kayu</option>
                                <option value="Kayu">Kayu</option>
                                <option value="Bambu">Bambu</option>
                                <option value="Lainnya (teks)">Lainnya (teks)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Alat/Fasilitas Dimiliki:</label>
                            <div class="checkbox-group">
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Kamar sendiri" id="afKamarSendiri"><label class="form-check-label" for="afKamarSendiri">Kamar sendiri</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Ruang belajar sendiri" id="afRuangBelajar"><label class="form-check-label" for="afRuangBelajar">Ruang belajar sendiri</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Perpustakaan keluarga" id="afPerpus"><label class="form-check-label" for="afPerpus">Perpustakaan keluarga</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Radio/TV/parabola" id="afRadioTv"><label class="form-check-label" for="afRadioTv">Radio/TV/parabola</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Ruang tamu" id="afRuangTamu"><label class="form-check-label" for="afRuangTamu">Ruang tamu</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Almari pribadi" id="afAlmariPribadi"><label class="form-check-label" for="afAlmariPribadi">Almari pribadi</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Gitar/piano alat musik" id="afAlatMusik"><label class="form-check-label" for="afAlatMusik">Gitar/piano alat musik</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Komputer/laptop/LCD" id="afKomputer"><label class="form-check-label" for="afKomputer">Komputer/laptop/LCD</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Kompor/kompor gas" id="afKompor"><label class="form-check-label" for="afKompor">Kompor/kompor gas</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Ruang makan sendiri" id="afRuangMakan"><label class="form-check-label" for="afRuangMakan">Ruang makan sendiri</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Almari es" id="afAlmariEs"><label class="form-check-label" for="afAlmariEs">Almari es</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Sepeda" id="afSepeda"><label class="form-check-label" for="afSepeda">Sepeda</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Sepeda motor" id="afSepedaMotor"><label class="form-check-label" for="afSepedaMotor">Sepeda motor</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Mobil" id="afMobil"><label class="form-check-label" for="afMobil">Mobil</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="alat_fasilitas_dimiliki" value="Berlangganan surat kabar/majalah (teks)" id="afSuratKabar"><label class="form-check-label" for="afSuratKabar">Berlangganan surat kabar/majalah (teks)</label></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" form="formDetailSiswa">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('detailSiswaTableBody');
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const alertContainer = document.getElementById('alertContainer');
        const addDetailSiswaBtn = document.getElementById('addDetailSiswaBtn');

        const detailSiswaModalElement = document.getElementById('detailSiswaModal');
        const detailSiswaModal = new bootstrap.Modal(detailSiswaModalElement);
        const formDetailSiswa = document.getElementById('formDetailSiswa');
        const detailSiswaModalLabel = document.getElementById('detailSiswaModalLabel');

        // Form fields
        const formSiswaId = document.getElementById('formSiswaId');
        const formAlamatAsal = document.getElementById('formAlamatAsal');
        const formNomorTelpHp = document.getElementById('formNomorTelpHp');
        const formAlamatSekarang = document.getElementById('formAlamatSekarang');
        const formNomorTelpHpSekarang = document.getElementById('formNomorTelpHpSekarang');
        const formJarakRumahSekolah = document.getElementById('formJarakRumahSekolah');
        const formTempatTinggal = document.getElementById('formTempatTinggal');
        const formRumahTerbuatDari = document.getElementById('formRumahTerbuatDari');

        const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenEl ? csrfTokenEl.getAttribute('content') : null;

        if (!csrfToken) {
            console.warn('CSRF token not found. Form submissions might fail if CSRF protection is enabled on the server.');
        }

        function showAlert(message, type = 'success') {
            alertContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        }

        function getCheckboxValues(name) {
            const checked = [];
            document.querySelectorAll(`input[name="${name}"]:checked`).forEach(checkbox => {
                checked.push(checkbox.value);
            });
            return checked;
        }

        function setCheckboxValues(name, values) {
            document.querySelectorAll(`input[name="${name}"]`).forEach(checkbox => {
                checkbox.checked = values && Array.isArray(values) && values.includes(checkbox.value);
            });
        }

        async function fetchDetailSiswa(searchQuery = '') {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Memuat data...</td></tr>';
            try {
                let url = '/api/detail-siswa'; // This API endpoint fetches the list for the table
                if (searchQuery) {
                    url += `?search=${encodeURIComponent(searchQuery)}`;
                }

                const response = await fetch(url);
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: `HTTP error! status: ${response.status}` }));
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }
                const data = await response.json();

                tableBody.innerHTML = ''; 

                if (!Array.isArray(data) || data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data detail siswa.</td></tr>';
                    return;
                }

                data.forEach(siswa => {
                    const row = tableBody.insertRow();
                    row.insertCell().textContent = siswa.id ? siswa.id.substring(0, 8) + '...' : '-';
                    row.insertCell().textContent = siswa.alamat_asal ? (siswa.alamat_asal.length > 30 ? siswa.alamat_asal.substring(0, 27) + '...' : siswa.alamat_asal) : '-';
                    row.insertCell().textContent = siswa.nomor_telp_hp || '-';
                    row.insertCell().textContent = (siswa.jarak_rumah_sekolah !== null && siswa.jarak_rumah_sekolah !== undefined ? siswa.jarak_rumah_sekolah : '-');
                    row.insertCell().textContent = siswa.tempat_tinggal || '-';

                    const actionsCell = row.insertCell();
                    actionsCell.classList.add('text-center');
                    const actionsDiv = document.createElement('div');
                    actionsDiv.classList.add('d-flex', 'justify-content-center', 'gap-2');

                    const viewButton = document.createElement('button');
                    viewButton.textContent = 'Lihat/Edit Form'; // Changed text for clarity
                    viewButton.classList.add('btn', 'btn-sm', 'btn-info');
                    
                    // MODIFICATION HERE:
                    // This will redirect to the Laravel route that serves the siswa.edit form
                    // Ensure this URL pattern matches your route definition in web.php
                    viewButton.addEventListener('click', () => {
                        // Assuming your Laravel route for the edit form is /siswa/{id}/edit
                        // If you have a named route like 'siswa.edit', and you can pass the base URL 
                        // or the route pattern from PHP to JS, that would be more robust.
                        // For now, we construct it directly:
                        window.location.href = `/siswa/${siswa.id}/edit`; 
                    });
                    actionsDiv.appendChild(viewButton);

                    // The "Edit" button below might become redundant if "Lihat/Edit Form" goes to the full edit page.
                    // Or, you can keep this "Edit" button to directly open the modal for quick edits 
                    // of only the "detail" fields shown in this modal, as it currently does.
                    // For this change, I'm assuming the "Lihat/Edit Form" button is the primary way to get to the full form.
                    const quickEditButton = document.createElement('button');
                    quickEditButton.textContent = 'Edit Detail Cepat'; // Renamed for clarity
                    quickEditButton.classList.add('btn', 'btn-sm', 'btn-warning');
                    quickEditButton.addEventListener('click', () => openEditModal(siswa.id)); // This opens the modal
                    actionsDiv.appendChild(quickEditButton);

                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Hapus';
                    deleteButton.classList.add('btn', 'btn-sm', 'btn-danger');
                    deleteButton.addEventListener('click', () => deleteDetailSiswa(siswa.id));
                    actionsDiv.appendChild(deleteButton);

                    actionsCell.appendChild(actionsDiv);
                });
            } catch (error) {
                console.error('Error fetching detail siswa:', error);
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>';
                showAlert(`Gagal memuat data: ${error.message}`, 'danger');
            }
        }

        // The showDetail function is no longer directly called by the "Lihat" button.
        // You can keep it if you have other uses for it, or remove it.
        // For instance, if you want a very quick, non-editable summary.
        async function showDetail(id) {
            try {
                const response = await fetch(`/api/detail-siswa/${id}`); // This API still needs to exist if this function is kept
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: `HTTP error! status: ${response.status}` }));
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                let detailText = `
                    ID: ${data.id}
                    Alamat Asal: ${data.alamat_asal || '-'}
                    No. Telp Asal: ${data.nomor_telp_hp || '-'}
                    Termasuk Daerah Asal: ${(data.termasuk_daerah_asal && Array.isArray(data.termasuk_daerah_asal) ? data.termasuk_daerah_asal.join(', ') : '-') || '-'}
                    Alamat Sekarang: ${data.alamat_sekarang || '-'}
                    No. Telp Sekarang: ${data.nomor_telp_hp_sekarang || '-'}
                    Termasuk Daerah Sekarang: ${(data.termasuk_daerah_sekarang && Array.isArray(data.termasuk_daerah_sekarang) ? data.termasuk_daerah_sekarang.join(', ') : '-') || '-'}
                    Jarak Rumah ke Sekolah: ${data.jarak_rumah_sekolah ?? '-'} KM
                    Alat/Sarana ke Sekolah: ${(data.alat_sarana_ke_sekolah && Array.isArray(data.alat_sarana_ke_sekolah) ? data.alat_sarana_ke_sekolah.join(', ') : '-') || '-'}
                    Tempat Tinggal: ${data.tempat_tinggal || '-'}
                    Tinggal Bersama: ${(data.tinggal_bersama && Array.isArray(data.tinggal_bersama) ? data.tinggal_bersama.join(', ') : '-') || '-'}
                    Rumah Terbuat Dari: ${data.rumah_terbuat_dari || '-'}
                    Alat/Fasilitas Dimiliki: ${(data.alat_fasilitas_dimiliki && Array.isArray(data.alat_fasilitas_dimiliki) ? data.alat_fasilitas_dimiliki.join(', ') : '-') || '-'}
                `;
                alert('Detail Siswa:\n' + detailText.replace(/^\s+/gm, ''));
            } catch (error) {
                console.error('Error fetching detail:', error);
                showAlert(`Gagal memuat detail siswa. ${error.message}`, 'danger');
            }
        }
// ... (rest of your JavaScript: deleteDetailSiswa, addDetailSiswaBtn listener, openEditModal, formDetailSiswa listener, etc.) ...

// Initial fetch
fetchDetailSiswa();

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchQuery = searchInput.value;
            fetchDetailSiswa(searchQuery);
        });

        async function showDetail(id) {
            try {
                const response = await fetch(`/api/detail-siswa/${id}`);
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: `HTTP error! status: ${response.status}` }));
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                let detailText = `
                    ID: ${data.id}
                    Alamat Asal: ${data.alamat_asal || '-'}
                    No. Telp Asal: ${data.nomor_telp_hp || '-'}
                    Termasuk Daerah Asal: ${(data.termasuk_daerah_asal && Array.isArray(data.termasuk_daerah_asal) ? data.termasuk_daerah_asal.join(', ') : '-') || '-'}
                    Alamat Sekarang: ${data.alamat_sekarang || '-'}
                    No. Telp Sekarang: ${data.nomor_telp_hp_sekarang || '-'}
                    Termasuk Daerah Sekarang: ${(data.termasuk_daerah_sekarang && Array.isArray(data.termasuk_daerah_sekarang) ? data.termasuk_daerah_sekarang.join(', ') : '-') || '-'}
                    Jarak Rumah ke Sekolah: ${data.jarak_rumah_sekolah ?? '-'} KM
                    Alat/Sarana ke Sekolah: ${(data.alat_sarana_ke_sekolah && Array.isArray(data.alat_sarana_ke_sekolah) ? data.alat_sarana_ke_sekolah.join(', ') : '-') || '-'}
                    Tempat Tinggal: ${data.tempat_tinggal || '-'}
                    Tinggal Bersama: ${(data.tinggal_bersama && Array.isArray(data.tinggal_bersama) ? data.tinggal_bersama.join(', ') : '-') || '-'}
                    Rumah Terbuat Dari: ${data.rumah_terbuat_dari || '-'}
                    Alat/Fasilitas Dimiliki: ${(data.alat_fasilitas_dimiliki && Array.isArray(data.alat_fasilitas_dimiliki) ? data.alat_fasilitas_dimiliki.join(', ') : '-') || '-'}
                `;
                alert('Detail Siswa:\n' + detailText.replace(/^\s+/gm, ''));
            } catch (error) {
                console.error('Error fetching detail:', error);
                showAlert(`Gagal memuat detail siswa. ${error.message}`, 'danger');
            }
        }

        async function deleteDetailSiswa(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus detail siswa ini?')) {
                return;
            }
            try {
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }

                const response = await fetch(`/api/detail-siswa/${id}`, {
                    method: 'DELETE',
                    headers: headers
                });
                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.message || 'Gagal menghapus data.');
                }
                showAlert(result.message || 'Data berhasil dihapus.');
                fetchDetailSiswa(searchInput.value);
            } catch (error) {
                console.error('Error deleting detail siswa:', error);
                showAlert(error.message || 'Terjadi kesalahan saat menghapus data.', 'danger');
            }
        }

        addDetailSiswaBtn.addEventListener('click', function() {
            formDetailSiswa.reset(); 
            formSiswaId.value = ''; 
            detailSiswaModalLabel.textContent = 'Tambah Detail Siswa';
            // Clear all checkbox groups
            ['termasuk_daerah_asal', 'termasuk_daerah_sekarang', 'alat_sarana_ke_sekolah', 'tinggal_bersama', 'alat_fasilitas_dimiliki'].forEach(name => {
                setCheckboxValues(name, []);
            });
            formDetailSiswa.dataset.mode = 'create'; 
            detailSiswaModal.show();
        });

        async function openEditModal(id) {
            formDetailSiswa.reset();
            try {
                const response = await fetch(`/api/detail-siswa/${id}`);
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: `Error fetching data for edit: ${response.status}` }));
                    throw new Error(errorData.message || `Error fetching data for edit: ${response.status}`);
                }
                const data = await response.json();

                detailSiswaModalLabel.textContent = 'Edit Detail Siswa';
                formSiswaId.value = data.id;
                formAlamatAsal.value = data.alamat_asal || '';
                formNomorTelpHp.value = data.nomor_telp_hp || '';
                formAlamatSekarang.value = data.alamat_sekarang || '';
                formNomorTelpHpSekarang.value = data.nomor_telp_hp_sekarang || '';
                formJarakRumahSekolah.value = data.jarak_rumah_sekolah ?? '';
                formTempatTinggal.value = data.tempat_tinggal || '';
                formRumahTerbuatDari.value = data.rumah_terbuat_dari || '';

                setCheckboxValues('termasuk_daerah_asal', data.termasuk_daerah_asal || []);
                setCheckboxValues('termasuk_daerah_sekarang', data.termasuk_daerah_sekarang || []);
                setCheckboxValues('alat_sarana_ke_sekolah', data.alat_sarana_ke_sekolah || []);
                setCheckboxValues('tinggal_bersama', data.tinggal_bersama || []);
                setCheckboxValues('alat_fasilitas_dimiliki', data.alat_fasilitas_dimiliki || []);
                
                formDetailSiswa.dataset.mode = 'edit'; 
                detailSiswaModal.show();

            } catch (error) {
                console.error('Error preparing edit modal:', error);
                showAlert(`Gagal memuat data untuk diedit. ${error.message}`, 'danger');
            }
        }

        formDetailSiswa.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = {
                alamat_asal: formAlamatAsal.value || null,
                nomor_telp_hp: formNomorTelpHp.value || null,
                termasuk_daerah_asal: getCheckboxValues('termasuk_daerah_asal'),
                alamat_sekarang: formAlamatSekarang.value || null,
                nomor_telp_hp_sekarang: formNomorTelpHpSekarang.value || null,
                termasuk_daerah_sekarang: getCheckboxValues('termasuk_daerah_sekarang'),
                jarak_rumah_sekolah: formJarakRumahSekolah.value ? parseInt(formJarakRumahSekolah.value, 10) : null,
                alat_sarana_ke_sekolah: getCheckboxValues('alat_sarana_ke_sekolah'),
                tempat_tinggal: formTempatTinggal.value || null,
                tinggal_bersama: getCheckboxValues('tinggal_bersama'),
                rumah_terbuat_dari: formRumahTerbuatDari.value || null,
                alat_fasilitas_dimiliki: getCheckboxValues('alat_fasilitas_dimiliki'),
            };
             // Filter out null values if your backend doesn't expect them for optional fields
            // Or ensure backend handles nulls gracefully as per 'nullable' validation
            for (const key in formData) {
                if (formData[key] === null && !Array.isArray(formData[key])) { // Keep empty arrays
                    // delete formData[key]; // Option 1: Remove if backend doesn't want null
                } else if (Array.isArray(formData[key]) && formData[key].length === 0) {
                    // formData[key] = null; // Option 2: Send null for empty arrays if backend expects that for 'nullable|array'
                }
            }


            const mode = formDetailSiswa.dataset.mode;
            const id = formSiswaId.value;
            let url = '/api/detail-siswa';
            let method = 'POST';

            if (mode === 'edit' && id) {
                url += `/${id}`;
                method = 'PUT';
            }

            try {
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                };
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }

                const response = await fetch(url, {
                    method: method,
                    headers: headers,
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (!response.ok) {
                    let errorMessage = result.message || (method === 'POST' ? 'Gagal menambahkan data.' : 'Gagal memperbarui data.');
                    if (result.errors) { 
                        const errorList = Object.values(result.errors).flat().join('\n');
                        errorMessage += `\n\nKesalahan Validasi:\n${errorList}`;
                    }
                    throw new Error(errorMessage);
                }

                showAlert(result.message || 'Data berhasil disimpan.');
                detailSiswaModal.hide();
                fetchDetailSiswa(searchInput.value);

            } catch (error) {
                console.error('Error saving detail siswa:', error);
                showAlert(error.message, 'danger');
            }
        });

        fetchDetailSiswa();
    });
    </script>
</body>
</html>