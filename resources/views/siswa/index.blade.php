<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Siswa (API)</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .search-container {
            margin-bottom: 15px;
        }
        #detailSiswa {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            display: none; /* Awalnya disembunyikan */
        }
        #detailSiswa h4, #detailSiswa h5 {
            margin-top: 15px;
            margin-bottom: 10px;
        }
        #detailSiswa p {
            margin-bottom: 5px;
        }
        .form-section-title {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-size: 1.25rem;
            border-bottom: 1px solid #eee;
            padding-bottom: .5rem;
        }
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Data Siswa (API)</h2>

        <div class="search-container">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama, username, dll...">
        </div>

        <div class="alert alert-success d-none" id="successMessage"></div>
        <div class="alert alert-danger d-none" id="errorMessage"></div>

        <div class="table-responsive">
            <table id="siswaTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No. Absen</th>
                        <th>Nama Siswa</th>
                        <th>Profil Siswa</th>
                    </tr>
                </thead>
                <tbody id="siswaTableBody">
                </tbody>
            </table>
        </div>

        <div id="detailSiswa">
            <h4>A. Identitas</h4>
            <p><strong>Nama:</strong> <span id="detailName"></span></p>
            <p><strong>Username:</strong> <span id="detailUsername"></span></p>
            <p><strong>Jenis Kelamin:</strong> <span id="detailJenisKelamin"></span></p>
            <p><strong>Tempat Lahir:</strong> <span id="detailTempatLahir"></span></p>
            <p><strong>Tanggal Lahir:</strong> <span id="detailTanggalLahir"></span></p>
            <p><strong>Agama:</strong> <span id="detailAgama"></span></p>
            <p><strong>Suku Bangsa:</strong> <span id="detailSukuBangsa"></span></p>
            <p><strong>Tanggal Masuk:</strong> <span id="detailTanggalMasuk"></span></p>
            <p><strong>Asal Sekolah:</strong> <span id="detailAsalSekolah"></span></p>
            <p><strong>Status Sebagai:</strong> <span id="detailStatusSebagai"></span></p>

            <h4>B. Data Tempat Tinggal</h4>
            <p><strong>Alamat Asal:</strong> <span id="detailAlamatAsal"></span></p>
            <p><strong>Nomor Telepon HP Asal:</strong> <span id="detailNomorTelpHpAsal"></span></p>
            <p><strong>Termasuk Daerah Asal:</strong> <span id="detailTermasukDaerahAsal"></span></p>
            <p><strong>Alamat Sekarang:</strong> <span id="detailAlamatSekarang"></span></p>
            <p><strong>Nomor Telepon HP Sekarang:</strong> <span id="detailNomorTelpHpSekarang"></span></p>
            <p><strong>Termasuk Daerah Sekarang:</strong> <span id="detailTermasukDaerahSekarang"></span></p>
            <p><strong>Jarak Rumah ke Sekolah (km):</strong> <span id="detailJarakRumahSekolah"></span></p>
            <p><strong>Alat Sarana ke Sekolah:</strong> <span id="detailAlatSaranaKeSekolah"></span></p>
            <p><strong>Tempat Tinggal:</strong> <span id="detailTempatTinggal"></span></p>
            <p><strong>Tinggal Bersama:</strong> <span id="detailTinggalBersama"></span></p>
            <p><strong>Rumah Terbuat Dari:</strong> <span id="detailRumahTerbuatDari"></span></p>
            <p><strong>Alat Fasilitas Dimiliki:</strong> <span id="detailAlatFasilitasDimiliki"></span></p>

            <h4>C. Data Keluarga</h4>
            <h5>Data Ayah</h5>
            <p><strong>Nama Ayah:</strong> <span id="detailAyahNama"></span></p>
            <p><strong>Tanggal Lahir Ayah:</strong> <span id="detailAyahTanggalLahir"></span></p>
            <p><strong>Agama Ayah:</strong> <span id="detailAyahAgama"></span></p>
            <p><strong>Pendidikan Ayah:</strong> <span id="detailAyahPendidikan"></span></p>
            <p><strong>Pekerjaan Ayah:</strong> <span id="detailAyahPekerjaan"></span></p>
            <p><strong>Suku Bangsa Ayah:</strong> <span id="detailAyahSukuBangsa"></span></p>
            <p><strong>Alamat Ayah:</strong> <span id="detailAyahAlamat"></span></p>
            <h5>Data Ibu</h5>
            <p><strong>Nama Ibu:</strong> <span id="detailIbuNama"></span></p>
            <p><strong>Tanggal Lahir Ibu:</strong> <span id="detailIbuTanggalLahir"></span></p>
            <p><strong>Agama Ibu:</strong> <span id="detailIbuAgama"></span></p>
            <p><strong>Pendidikan Ibu:</strong> <span id="detailIbuPendidikan"></span></p>
            <p><strong>Pekerjaan Ibu:</strong> <span id="detailIbuPekerjaan"></span></p>
            <p><strong>Suku Bangsa Ibu:</strong> <span id="detailIbuSukuBangsa"></span></p>
            <p><strong>Alamat Ibu:</strong> <span id="detailIbuAlamat"></span></p>
            <h5>Data Wali</h5>
            <p><strong>Nama Wali:</strong> <span id="detailWaliNama"></span></p>
            <p><strong>Tanggal Lahir Wali:</strong> <span id="detailWaliTanggalLahir"></span></p>
            <p><strong>Agama Wali:</strong> <span id="detailWaliAgama"></span></p>
            <p><strong>Pendidikan Wali:</strong> <span id="detailWaliPendidikan"></span></p>
            <p><strong>Pekerjaan Wali:</strong> <span id="detailWaliPekerjaan"></span></p>
            <p><strong>Suku Bangsa Wali:</strong> <span id="detailWaliSukuBangsa"></span></p>
            <p><strong>Alamat Wali:</strong> <span id="detailWaliAlamat"></span></p>
            <hr>
            <p><strong>Anak ke-:</strong> <span id="detailAnakKe"></span></p>
            <h5>Saudara Kandung</h5>
            <div id="detailSaudaraKandungList"></div>

            <h4>D. Keadaan Jasmani</h4>
            <p><strong>Tinggi Badan (cm):</strong> <span id="detailTinggiBadan"></span></p>
            <p><strong>Berat Badan (kg):</strong> <span id="detailBeratBadan"></span></p>
            <p><strong>Golongan Darah:</strong> <span id="detailGolonganDarah"></span></p>
            <p><strong>Bentuk Mata:</strong> <span id="detailBentukMata"></span></p>
            <p><strong>Bentuk Muka:</strong> <span id="detailBentukMuka"></span></p>
            <p><strong>Rambut:</strong> <span id="detailRambut"></span></p>
            <p><strong>Warna Kulit:</strong> <span id="detailWarnaKulit"></span></p>
            <p><strong>Memiliki Cacat Tubuh:</strong> <span id="detailMemilikiCacatTubuh"></span> (<span id="detailCacatTubuhPenjelasan"></span>)</p>
            <p><strong>Memakai Kacamata:</strong> <span id="detailMemakaiKacamata"></span> (<span id="detailKacamataKelainan"></span>)</p>
            <p><strong>Sakit Yang Sering Diderita:</strong> <span id="detailSakitSeringDiderita"></span></p>
            <h5>Pernah Mengalami Sakit Keras</h5>
            <div id="detailSakitKerasList"></div>

            <h4>E. Penguasaan Bahasa</h4>
            <p><strong>Kemampuan Bahasa Indonesia:</strong> <span id="detailKemampuanBahasaIndonesia"></span></p>
            <p><strong>Bahasa Sehari-hari di Rumah:</strong> <span id="detailBahasaSehariHariDirumah"></span></p>
            <p><strong>Bahasa Daerah Dikuasai:</strong> <span id="detailBahasaDaerahDikuasai"></span> <span id="detailBahasaDaerahLainnyaText"></span></p>
            <p><strong>Bahasa Asing Dikuasai:</strong> <span id="detailBahasaAsingDikuasai"></span> <span id="detailBahasaAsingLainnyaText"></span></p>

            <h4>F. Hobby, Kegemaran, dan Cita-Cita</h4>
            <p><strong>Hobby:</strong> <span id="detailHobby"></span></p>
            <p><strong>Cita-cita:</strong> <span id="detailCitaCita"></span></p>

            <h4>G. Keadaan Pendidikan</h4>
            <p><strong>Pelajaran Disukai Waktu SD:</strong> <span id="detailPelajaranDisukaiSd"></span></p>
            <p><strong>Alasan Pelajaran Disukai SD:</strong> <span id="detailAlasanPelajaranDisukaiSd"></span></p>
            <p><strong>Pelajaran Tidak Disukai Waktu SD:</strong> <span id="detailPelajaranTidakDisukaiSd"></span></p>
            <p><strong>Alasan Pelajaran Tidak Disukai SD:</strong> <span id="detailAlasanPelajaranTidakDisukaiSd"></span></p>
            <h5>Prestasi Waktu SD</h5>
            <div id="detailPrestasiSdList"></div>
            <h5>Masalah Belajar</h5>
            <p><strong>Kegiatan Belajar di Rumah:</strong> <span id="detailKegiatanBelajarDirumah"></span></p>
            <p><strong>Dilaksanakan Setiap:</strong> <span id="detailDilaksanakanSetiapBelajar"></span></p>
            <p><strong>Kesulitan yang Sering Dialami:</strong> <span id="detailKesulitanBelajar"></span></p>
            <p><strong>Hambatan/Gangguan dalam Belajar:</strong> <span id="detailHambatanBelajar"></span></p>
            <h5>Prestasi Selama SMP</h5>
            <div id="detailPrestasiSmpList"></div>

            <br>
            <button class="btn btn-sm btn-warning" id="btnEditDetail" onclick="">Edit</button>
            <button class="btn btn-secondary btn-sm" onclick="$('#detailSiswa').hide();">Kembali</button>
        </div>
    </div>

    <div class="modal fade" id="siswaModal" tabindex="-1" role="dialog" aria-labelledby="siswaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="siswaModalLabel">Edit Data Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="siswaForm">
                    <div class="modal-body">
                        <h5 class="form-section-title">A. Identitas</h5>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">Nama:</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="username">Username:</label>
                                <input type="text" class="form-control" id="username" name="username">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="jenis_kelamin">Jenis Kelamin:</label>
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tempat_lahir">Tempat Lahir:</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
                            </div>
                        </div>
                        <div class="form-row">
                             <div class="form-group col-md-6">
                                <label for="tanggal_lahir">Tanggal Lahir:</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="agama">Agama:</label>
                                <input type="text" class="form-control" id="agama" name="agama">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="suku_bangsa">Suku Bangsa:</label>
                                <input type="text" class="form-control" id="suku_bangsa" name="suku_bangsa">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tanggal_masuk">Tanggal Masuk:</label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="asal_sekolah">Asal Sekolah:</label>
                                <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status_sebagai">Status Sebagai:</label>
                                <select class="form-control" id="status_sebagai" name="status_sebagai">
                                    <option value="Siswa Baru">Siswa Baru</option>
                                    <option value="Pindahan">Pindahan</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="form-section-title">B. Data Tempat Tinggal</h5>
                        <div class="form-group">
                            <label for="alamat_asal">Alamat Asal:</label>
                            <textarea class="form-control" id="alamat_asal" name="alamat_asal"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="nomor_telp_hp">Nomor Telepon HP Asal:</label>
                                <input type="text" class="form-control" id="nomor_telp_hp" name="nomor_telp_hp">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Termasuk Daerah Asal:</label>
                                <div>
                                    <input type="checkbox" name="termasuk_daerah_asal[]" value="Dalam kota"> Dalam kota
                                    <input type="checkbox" name="termasuk_daerah_asal[]" value="Pinggir kota"> Pinggir kota
                                    <input type="checkbox" name="termasuk_daerah_asal[]" value="Luar kota"> Luar kota
                                    <input type="checkbox" name="termasuk_daerah_asal[]" value="Pinggir sungai"> Pinggir sungai
                                    <input type="checkbox" name="termasuk_daerah_asal[]" value="Daerah pegunungan"> Daerah pegunungan
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="alamat_sekarang">Alamat Sekarang:</label>
                            <textarea class="form-control" id="alamat_sekarang" name="alamat_sekarang"></textarea>
                        </div>
                        <div class="form-row">
                             <div class="form-group col-md-6">
                                <label for="nomor_telp_hp_sekarang">Nomor Telepon HP Sekarang:</label>
                                <input type="text" class="form-control" id="nomor_telp_hp_sekarang" name="nomor_telp_hp_sekarang">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Termasuk Daerah Sekarang:</label>
                                <div>
                                     <input type="checkbox" name="termasuk_daerah_sekarang[]" value="Dalam kota"> Dalam kota
                                     <input type="checkbox" name="termasuk_daerah_sekarang[]" value="Pinggir kota"> Pinggir kota
                                     <input type="checkbox" name="termasuk_daerah_sekarang[]" value="Luar kota"> Luar kota
                                     <input type="checkbox" name="termasuk_daerah_sekarang[]" value="Pinggir sungai"> Pinggir sungai
                                     <input type="checkbox" name="termasuk_daerah_sekarang[]" value="Daerah pegunungan"> Daerah pegunungan
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="jarak_rumah_sekolah">Jarak Rumah ke Sekolah (km):</label>
                                <input type="number" class="form-control" id="jarak_rumah_sekolah" name="jarak_rumah_sekolah">
                            </div>
                             <div class="form-group col-md-6">
                                <label>Alat Sarana ke Sekolah:</label>
                                <div>
                                    <input type="checkbox" name="alat_sarana_ke_sekolah[]" value="Jalan kaki"> Jalan kaki
                                    <input type="checkbox" name="alat_sarana_ke_sekolah[]" value="Naik sepeda"> Naik sepeda
                                    <input type="checkbox" name="alat_sarana_ke_sekolah[]" value="Naik sepeda motor"> Naik sepeda motor
                                    <input type="checkbox" name="alat_sarana_ke_sekolah[]" value="Diantar orang tua"> Diantar orang tua
                                    <input type="checkbox" name="alat_sarana_ke_sekolah[]" value="Naik taksi/ojek"> Naik taksi/ojek
                                    <input type="checkbox" name="alat_sarana_ke_sekolah[]" value="Naik mobil pribadi"> Naik mobil pribadi
                                    <input type="checkbox" name="alat_sarana_ke_sekolah[]" value="Lainnya (teks)"> Lainnya (teks)
                                    </div>
                            </div>
                        </div>
                        <div class="form-row">
                             <div class="form-group col-md-6">
                                <label for="tempat_tinggal">Tempat Tinggal:</label>
                                <select class="form-control" id="tempat_tinggal" name="tempat_tinggal">
                                    <option value="Rumah sendiri">Rumah sendiri</option>
                                    <option value="Rumah dinas">Rumah dinas</option>
                                    <option value="Rumah kontrakan">Rumah kontrakan</option>
                                    <option value="Rumah nenek/kakek">Rumah nenek/kakek</option>
                                    <option value="Kamar kost">Kamar kost</option>
                                    <option value="Lainnya (teks)">Lainnya (teks)</option>
                                </select>
                            </div>
                             <div class="form-group col-md-6">
                                <label>Tinggal Bersama:</label>
                                <div>
                                     <input type="checkbox" name="tinggal_bersama[]" value="Ayah dan ibu kandung"> Ayah dan ibu kandung
                                     <input type="checkbox" name="tinggal_bersama[]" value="Ayah kandung dan ibu tiri"> Ayah kandung dan ibu tiri
                                     <input type="checkbox" name="tinggal_bersama[]" value="Ayah tiri dan ibu kandung"> Ayah tiri dan ibu kandung
                                     <input type="checkbox" name="tinggal_bersama[]" value="Ayah kandung saja"> Ayah kandung saja
                                     <input type="checkbox" name="tinggal_bersama[]" value="Ibu kandung saja"> Ibu kandung saja
                                     <input type="checkbox" name="tinggal_bersama[]" value="Nenek/Kakek"> Nenek/Kakek
                                     <input type="checkbox" name="tinggal_bersama[]" value="Saudara kandung"> Saudara kandung
                                     <input type="checkbox" name="tinggal_bersama[]" value="Sendiri"> Sendiri
                                     <input type="checkbox" name="tinggal_bersama[]" value="Wali (teks)"> Wali (teks)
                                     <input type="checkbox" name="tinggal_bersama[]" value="Lainnya (teks)"> Lainnya (teks)
                                </div>
                            </div>
                        </div>
                         <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="rumah_terbuat_dari">Rumah Terbuat Dari:</label>
                                <select class="form-control" id="rumah_terbuat_dari" name="rumah_terbuat_dari">
                                    <option value="Tembok beton">Tembok beton</option>
                                    <option value="Setengah kayu">Setengah kayu</option>
                                    <option value="Kayu">Kayu</option>
                                    <option value="Bambu">Bambu</option>
                                    <option value="Lainnya (teks)">Lainnya (teks)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Alat Fasilitas Dimiliki:</label>
                            <div>
                                <input type="checkbox" name="alat_fasilitas_dimiliki[]" value="Kamar sendiri"> Kamar sendiri
                                <input type="checkbox" name="alat_fasilitas_dimiliki[]" value="Ruang belajar sendiri"> Ruang belajar sendiri
                                </div>
                        </div>


                        <h5 class="form-section-title">C. Data Keluarga</h5>
                        <h6>Data Ayah</h6>
                        <div class="form-row">
                            <div class="form-group col-md-6"><label>Nama Ayah:</label><input type="text" class="form-control" name="data_keluarga[ayah][nama]" id="ayah_nama"></div>
                            <div class="form-group col-md-6"><label>Tgl Lahir Ayah:</label><input type="date" class="form-control" name="data_keluarga[ayah][tanggal_lahir]" id="ayah_tanggal_lahir"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4"><label>Agama Ayah:</label><input type="text" class="form-control" name="data_keluarga[ayah][agama]" id="ayah_agama"></div>
                            <div class="form-group col-md-4"><label>Pendidikan Ayah:</label><input type="text" class="form-control" name="data_keluarga[ayah][pendidikan]" id="ayah_pendidikan"></div>
                            <div class="form-group col-md-4"><label>Pekerjaan Ayah:</label><input type="text" class="form-control" name="data_keluarga[ayah][pekerjaan]" id="ayah_pekerjaan"></div>
                        </div>
                        <div class="form-row">
                             <div class="form-group col-md-6"><label>Suku Bangsa Ayah:</label><input type="text" class="form-control" name="data_keluarga[ayah][suku_bangsa]" id="ayah_suku_bangsa"></div>
                        </div>
                        <div class="form-group"><label>Alamat Ayah:</label><textarea class="form-control" name="data_keluarga[ayah][alamat]" id="ayah_alamat"></textarea></div>

                        <h6>Data Ibu</h6>
                        <div class="form-row">
                            <div class="form-group col-md-6"><label>Nama Ibu:</label><input type="text" class="form-control" name="data_keluarga[ibu][nama]" id="ibu_nama"></div>
                            <div class="form-group col-md-6"><label>Tgl Lahir Ibu:</label><input type="date" class="form-control" name="data_keluarga[ibu][tanggal_lahir]" id="ibu_tanggal_lahir"></div>
                        </div>
                         <div class="form-row">
                            <div class="form-group col-md-4"><label>Agama Ibu:</label><input type="text" class="form-control" name="data_keluarga[ibu][agama]" id="ibu_agama"></div>
                            <div class="form-group col-md-4"><label>Pendidikan Ibu:</label><input type="text" class="form-control" name="data_keluarga[ibu][pendidikan]" id="ibu_pendidikan"></div>
                            <div class="form-group col-md-4"><label>Pekerjaan Ibu:</label><input type="text" class="form-control" name="data_keluarga[ibu][pekerjaan]" id="ibu_pekerjaan"></div>
                        </div>
                        <div class="form-row">
                             <div class="form-group col-md-6"><label>Suku Bangsa Ibu:</label><input type="text" class="form-control" name="data_keluarga[ibu][suku_bangsa]" id="ibu_suku_bangsa"></div>
                        </div>
                        <div class="form-group"><label>Alamat Ibu:</label><textarea class="form-control" name="data_keluarga[ibu][alamat]" id="ibu_alamat"></textarea></div>

                        <h6>Data Wali</h6>
                         <div class="form-row">
                            <div class="form-group col-md-6"><label>Nama Wali:</label><input type="text" class="form-control" name="data_keluarga[wali][nama]" id="wali_nama"></div>
                            <div class="form-group col-md-6"><label>Tgl Lahir Wali:</label><input type="date" class="form-control" name="data_keluarga[wali][tanggal_lahir]" id="wali_tanggal_lahir"></div>
                        </div>
                         <div class="form-row">
                            <div class="form-group col-md-4"><label>Agama Wali:</label><input type="text" class="form-control" name="data_keluarga[wali][agama]" id="wali_agama"></div>
                            <div class="form-group col-md-4"><label>Pendidikan Wali:</label><input type="text" class="form-control" name="data_keluarga[wali][pendidikan]" id="wali_pendidikan"></div>
                            <div class="form-group col-md-4"><label>Pekerjaan Wali:</label><input type="text" class="form-control" name="data_keluarga[wali][pekerjaan]" id="wali_pekerjaan"></div>
                        </div>
                        <div class="form-row">
                             <div class="form-group col-md-6"><label>Suku Bangsa Wali:</label><input type="text" class="form-control" name="data_keluarga[wali][suku_bangsa]" id="wali_suku_bangsa"></div>
                        </div>
                        <div class="form-group"><label>Alamat Wali:</label><textarea class="form-control" name="data_keluarga[wali][alamat]" id="wali_alamat"></textarea></div>

                        <div class="form-group">
                            <label for="anak_ke">Anak ke:</label>
                            <input type="number" class="form-control" id="anak_ke" name="anak_ke">
                        </div>

                        <h6>Saudara Kandung (Contoh 1 Entri)</h6>
                        <p><small>Untuk menambah lebih banyak saudara, fungsionalitas dinamis perlu ditambahkan dengan JavaScript.</small></p>
                        <div class="form-row">
                            <div class="form-group col-md-6"><label>Nama Saudara:</label><input type="text" class="form-control" name="saudara_kandung[0][nama]" id="saudara_0_nama"></div>
                            <div class="form-group col-md-6"><label>Tgl Lahir Saudara:</label><input type="date" class="form-control" name="saudara_kandung[0][tanggal_lahir]" id="saudara_0_tanggal_lahir"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Jenis Kelamin Saudara:</label>
                                <select class="form-control" name="saudara_kandung[0][jenis_kelamin]" id="saudara_0_jenis_kelamin">
                                    <option value="">Pilih</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Status Hubungan:</label>
                                <select class="form-control" name="saudara_kandung[0][status_hubungan]" id="saudara_0_status_hubungan">
                                     <option value="">Pilih</option>
                                    <option value="Kandung">Kandung</option>
                                    <option value="Siri">Siri</option>
                                </select>
                            </div>
                             <div class="form-group col-md-4">
                                <label>Kawin/Belum:</label>
                                 <select class="form-control" name="saudara_kandung[0][status_perkawinan]" id="saudara_0_status_perkawinan">
                                    <option value="">Pilih</option>
                                    <option value="Kawin">Kawin</option>
                                    <option value="Belum">Belum</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6"><label>Pekerjaan/Sekolah Saudara:</label><input type="text" class="form-control" name="saudara_kandung[0][pekerjaan_sekolah]" id="saudara_0_pekerjaan_sekolah"></div>
                            <div class="form-group col-md-6"><label>Tingkat Saudara:</label><input type="text" class="form-control" name="saudara_kandung[0][tingkat]" id="saudara_0_tingkat"></div>
                        </div>


                        <h5 class="form-section-title">D. Keadaan Jasmani</h5>
                        <div class="form-row">
                            <div class="form-group col-md-3"><label for="tinggi_badan">Tinggi Badan (cm):</label><input type="number" class="form-control" id="tinggi_badan" name="tinggi_badan"></div>
                            <div class="form-group col-md-3"><label for="berat_badan">Berat Badan (kg):</label><input type="number" class="form-control" id="berat_badan" name="berat_badan"></div>
                            <div class="form-group col-md-3">
                                <label for="golongan_darah">Gol. Darah:</label>
                                <select class="form-control" id="golongan_darah" name="golongan_darah">
                                    <option value="">Pilih</option> <option value="A">A</option> <option value="B">B</option> <option value="AB">AB</option> <option value="O">O</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rambut">Rambut:</label>
                                <select class="form-control" id="rambut" name="rambut">
                                    <option value="">Pilih</option> <option value="Lurus">Lurus</option> <option value="Keriting">Keriting</option> <option value="Bergelombang">Bergelombang</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4"><label for="bentuk_mata">Bentuk Mata:</label><input type="text" class="form-control" id="bentuk_mata" name="bentuk_mata"></div>
                            <div class="form-group col-md-4"><label for="bentuk_muka">Bentuk Muka:</label><input type="text" class="form-control" id="bentuk_muka" name="bentuk_muka"></div>
                            <div class="form-group col-md-4"><label for="warna_kulit">Warna Kulit:</label><input type="text" class="form-control" id="warna_kulit" name="warna_kulit"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Memiliki Cacat Tubuh:</label>
                                <div>
                                    <input type="radio" name="memiliki_cacat_tubuh" value="Ya" id="cacat_ya"> Ya
                                    <input type="radio" name="memiliki_cacat_tubuh" value="Tidak" id="cacat_tidak" checked> Tidak
                                </div>
                                <label for="cacat_tubuh_penjelasan" class="mt-2">Penjelasan Cacat:</label>
                                <textarea class="form-control" id="cacat_tubuh_penjelasan" name="cacat_tubuh_penjelasan"></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Memakai Kacamata:</label>
                                <div>
                                    <input type="radio" name="memakai_kacamata" value="Ya" id="kacamata_ya"> Ya
                                    <input type="radio" name="memakai_kacamata" value="Tidak" id="kacamata_tidak" checked> Tidak
                                </div>
                                <label for="kacamata_kelainan" class="mt-2">Kelainan Kacamata (Minus/Plus/Silinder):</label>
                                <input type="text" class="form-control" id="kacamata_kelainan" name="kacamata_kelainan">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sakit_sering_diderita">Sakit Yang Sering Diderita:</label>
                            <textarea class="form-control" id="sakit_sering_diderita" name="sakit_sering_diderita"></textarea>
                        </div>
                        <h6>Pernah Mengalami Sakit Keras (Contoh 1 Entri)</h6>
                         <div class="form-row">
                            <div class="form-group col-md-6"><label>Jenis Penyakit:</label><input type="text" class="form-control" name="sakit_keras[0][jenis_penyakit]" id="sakit_keras_0_jenis_penyakit"></div>
                            <div class="form-group col-md-6"><label>Usia saat Sakit:</label><input type="number" class="form-control" name="sakit_keras[0][usia_saat_sakit]" id="sakit_keras_0_usia_saat_sakit"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Opname:</label>
                                <select class="form-control" name="sakit_keras[0][opname]" id="sakit_keras_0_opname">
                                    <option value="">Pilih</option><option value="Ya">Ya</option><option value="Tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6"><label>Opname di RS (Nama RS):</label><input type="text" class="form-control" name="sakit_keras[0][opname_di_rs]" id="sakit_keras_0_opname_di_rs"></div>
                        </div>

                        <h5 class="form-section-title">E. Penguasaan Bahasa</h5>
                        <div class="form-group">
                            <label for="kemampuan_bahasa_indonesia">Kemampuan Bahasa Indonesia:</label>
                            <select class="form-control" id="kemampuan_bahasa_indonesia" name="kemampuan_bahasa_indonesia">
                                <option value="">Pilih</option>
                                <option value="Menguasai">Menguasai</option>
                                <option value="Cukup Menguasai">Cukup Menguasai</option>
                                <option value="Kurang Menguasai">Kurang Menguasai</option>
                                <option value="Tidak Menguasai">Tidak Menguasai</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bahasa_sehari_hari_dirumah">Bahasa Sehari-hari di Rumah:</label>
                            <input type="text" class="form-control" id="bahasa_sehari_hari_dirumah" name="bahasa_sehari_hari_dirumah">
                        </div>
                        <div class="form-group">
                            <label>Bahasa Daerah yang Dikuasai:</label>
                            <div>
                                <input type="checkbox" name="bahasa_daerah_dikuasai[]" value="Bahasa Banjar"> Bahasa Banjar
                                <input type="checkbox" name="bahasa_daerah_dikuasai[]" value="Bahasa Dayak"> Bahasa Dayak
                                <input type="checkbox" name="bahasa_daerah_dikuasai[]" value="Bahasa Jawa"> Bahasa Jawa
                                <input type="checkbox" name="bahasa_daerah_dikuasai[]" value="Bahasa Ambon"> Bahasa Ambon
                                <input type="checkbox" name="bahasa_daerah_dikuasai[]" value="Lainnya" id="bahasa_daerah_lainnya_cb"> Lainnya
                            </div>
                            <input type="text" class="form-control mt-2" id="bahasa_daerah_lainnya_text" name="bahasa_daerah_lainnya_text" placeholder="Sebutkan bahasa daerah lainnya">
                        </div>
                         <div class="form-group">
                            <label>Bahasa Asing yang Dikuasai:</label>
                            <div>
                                <input type="checkbox" name="bahasa_asing_dikuasai[]" value="Bahasa Inggris"> Bahasa Inggris
                                <input type="checkbox" name="bahasa_asing_dikuasai[]" value="Bahasa Arab"> Bahasa Arab
                                <input type="checkbox" name="bahasa_asing_dikuasai[]" value="Bahasa Mandarin"> Bahasa Mandarin
                                <input type="checkbox" name="bahasa_asing_dikuasai[]" value="Bahasa Jerman"> Bahasa Jerman
                                <input type="checkbox" name="bahasa_asing_dikuasai[]" value="Lainnya" id="bahasa_asing_lainnya_cb"> Lainnya
                            </div>
                            <input type="text" class="form-control mt-2" id="bahasa_asing_lainnya_text" name="bahasa_asing_lainnya_text" placeholder="Sebutkan bahasa asing lainnya">
                        </div>

                        <h5 class="form-section-title">F. Hobby, Kegemaran, dan Cita-Cita</h5>
                        <div class="form-group">
                            <label for="hobby">Hobby:</label>
                            <textarea class="form-control" id="hobby" name="hobby"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="cita_cita">Cita-cita:</label>
                            <textarea class="form-control" id="cita_cita" name="cita_cita"></textarea>
                        </div>

                        <h5 class="form-section-title">G. Keadaan Pendidikan</h5>
                        <div class="form-row">
                            <div class="form-group col-md-6"><label for="pelajaran_disukai_sd">Pelajaran Disukai Waktu SD:</label><input type="text" class="form-control" id="pelajaran_disukai_sd" name="pelajaran_disukai_sd"></div>
                            <div class="form-group col-md-6"><label for="alasan_pelajaran_disukai_sd">Alasan Disukai:</label><textarea class="form-control" id="alasan_pelajaran_disukai_sd" name="alasan_pelajaran_disukai_sd"></textarea></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6"><label for="pelajaran_tidak_disukai_sd">Pelajaran Tidak Disukai Waktu SD:</label><input type="text" class="form-control" id="pelajaran_tidak_disukai_sd" name="pelajaran_tidak_disukai_sd"></div>
                            <div class="form-group col-md-6"><label for="alasan_pelajaran_tidak_disukai_sd">Alasan Tidak Disukai:</label><textarea class="form-control" id="alasan_pelajaran_tidak_disukai_sd" name="alasan_pelajaran_tidak_disukai_sd"></textarea></div>
                        </div>

                        <h6>Prestasi Yang Pernah Diperoleh Waktu SD (Contoh 1 Entri)</h6>
                        <div class="form-row">
                            <div class="form-group col-md-6"><label>Nama Kejuaraan (SD):</label><input type="text" class="form-control" name="prestasi_sd[0][nama_kejuaraan]" id="prestasi_sd_0_nama_kejuaraan"></div>
                            <div class="form-group col-md-6"><label>Tingkat (SD):</label><input type="text" class="form-control" name="prestasi_sd[0][tingkat]" id="prestasi_sd_0_tingkat"></div>
                        </div>
                        <div class="form-row">
                             <div class="form-group col-md-6"><label>Raihan Prestasi (SD):</label><input type="text" class="form-control" name="prestasi_sd[0][raihan_prestasi]" id="prestasi_sd_0_raihan_prestasi"></div>
                            <div class="form-group col-md-6"><label>Tahun/Kelas (SD):</label><input type="text" class="form-control" name="prestasi_sd[0][tahun_kelas]" id="prestasi_sd_0_tahun_kelas"></div>
                        </div>

                        <h6>Masalah Belajar</h6>
                        <div class="form-group">
                            <label>Kegiatan Belajar di Rumah:</label>
                            <div>
                                <input type="radio" name="kegiatan_belajar_dirumah" value="Rutin" id="belajar_rutin"> Rutin
                                <input type="radio" name="kegiatan_belajar_dirumah" value="Tidak" id="belajar_tidak"> Tidak
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Dilaksanakan Setiap:</label>
                            <div>
                                <input type="checkbox" name="dilaksanakan_setiap_belajar[]" value="Sore"> Sore
                                <input type="checkbox" name="dilaksanakan_setiap_belajar[]" value="Malam"> Malam
                                <input type="checkbox" name="dilaksanakan_setiap_belajar[]" value="Pagi"> Pagi
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="kesulitan_belajar">Kesulitan yang Sering Dialami dalam Belajar:</label>
                            <textarea class="form-control" id="kesulitan_belajar" name="kesulitan_belajar"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="hambatan_belajar">Hambatan/Gangguan yang Dihadapi dalam Belajar:</label>
                            <textarea class="form-control" id="hambatan_belajar" name="hambatan_belajar"></textarea>
                        </div>

                        <h6>Catatan Prestasi Yang Diperoleh Selama SMP (Contoh 1 Entri)</h6>
                        <div class="form-row">
                            <div class="form-group col-md-6"><label>Nama Kejuaraan (SMP):</label><input type="text" class="form-control" name="prestasi_smp[0][nama_kejuaraan]" id="prestasi_smp_0_nama_kejuaraan"></div>
                            <div class="form-group col-md-6"><label>Tingkat (SMP):</label><input type="text" class="form-control" name="prestasi_smp[0][tingkat]" id="prestasi_smp_0_tingkat"></div>
                        </div>
                        <div class="form-row">
                             <div class="form-group col-md-6"><label>Raihan Prestasi (SMP):</label><input type="text" class="form-control" name="prestasi_smp[0][raihan_prestasi]" id="prestasi_smp_0_raihan_prestasi"></div>
                            <div class="form-group col-md-6"><label>Tahun/Kelas (SMP):</label><input type="text" class="form-control" name="prestasi_smp[0][tahun_kelas]" id="prestasi_smp_0_tahun_kelas"></div>
                        </div>

                        <input type="hidden" id="id" name="id">
                        <input type="hidden" id="_method" name="_method" value="POST"> </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="submitForm()">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            let dataTable = $('#siswaTable').DataTable({
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "url": "/api/siswa",
                    "type": "GET",
                    "dataSrc": ""
                },
                "columns": [
                    {"data": "id"}, // No. Absen
                    {"data": "name"},
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-info" onclick="showData('${row.id}')">Lihat</button>
                                `;
                        }
                    }
                ],
                "search": {
                    "caseInsensitive": true
                }
            });

            $('#searchInput').on('keyup', function() {
                dataTable.search(this.value).draw();
            });
        });

        function safeSetText(elementId, value) {
            const el = $('#' + elementId);
            if (el.length) {
                el.text(value !== null && value !== undefined ? value : '');
            }
        }
        
        function safeSetHtml(elementId, value) {
            const el = $('#' + elementId);
            if (el.length) {
                el.html(value !== null && value !== undefined ? value : '');
            }
        }

        function formatArrayForDisplay(arr, property = null) {
            if (!arr || !Array.isArray(arr) || arr.length === 0) return 'Tidak ada data';
            if (property) {
                return arr.map(item => item[property] || 'N/A').join(', ');
            }
            return arr.join(', ');
        }
        
        function displayList(containerId, dataArray, renderItem) {
            const container = $('#' + containerId);
            container.empty(); // Clear previous items
            if (dataArray && dataArray.length > 0) {
                const ul = $('<ul>').addClass('list-unstyled');
                dataArray.forEach(item => {
                    ul.append($('<li>').html(renderItem(item)));
                });
                container.append(ul);
            } else {
                container.text('Tidak ada data.');
            }
        }


        function showData(id) {
            fetch(`/api/siswa/${id}`)
                .then(response => response.json())
                .then(data => {
                    // A. Identitas
                    safeSetText('detailName', data.name);
                    safeSetText('detailUsername', data.username);
                    safeSetText('detailJenisKelamin', data.jenis_kelamin);
                    safeSetText('detailTempatLahir', data.tempat_lahir);
                    safeSetText('detailTanggalLahir', data.tanggal_lahir);
                    safeSetText('detailAgama', data.agama);
                    safeSetText('detailSukuBangsa', data.suku_bangsa);
                    safeSetText('detailTanggalMasuk', data.tanggal_masuk);
                    safeSetText('detailAsalSekolah', data.asal_sekolah);
                    safeSetText('detailStatusSebagai', data.status_sebagai);

                    // B. Data Tempat Tinggal
                    safeSetText('detailAlamatAsal', data.alamat_asal);
                    safeSetText('detailNomorTelpHpAsal', data.nomor_telp_hp);
                    safeSetText('detailTermasukDaerahAsal', Array.isArray(data.termasuk_daerah_asal) ? data.termasuk_daerah_asal.join(', ') : '');
                    safeSetText('detailAlamatSekarang', data.alamat_sekarang);
                    safeSetText('detailNomorTelpHpSekarang', data.nomor_telp_hp_sekarang);
                    safeSetText('detailTermasukDaerahSekarang', Array.isArray(data.termasuk_daerah_sekarang) ? data.termasuk_daerah_sekarang.join(', ') : '');
                    safeSetText('detailJarakRumahSekolah', data.jarak_rumah_sekolah);
                    safeSetText('detailAlatSaranaKeSekolah', Array.isArray(data.alat_sarana_ke_sekolah) ? data.alat_sarana_ke_sekolah.join(', ') : '');
                    safeSetText('detailTempatTinggal', data.tempat_tinggal);
                    safeSetText('detailTinggalBersama', Array.isArray(data.tinggal_bersama) ? data.tinggal_bersama.join(', ') : '');
                    safeSetText('detailRumahTerbuatDari', data.rumah_terbuat_dari);
                    safeSetText('detailAlatFasilitasDimiliki', Array.isArray(data.alat_fasilitas_dimiliki) ? data.alat_fasilitas_dimiliki.join(', ') : '');

                    // C. Data Keluarga
                    const ayah = data.data_keluarga?.ayah || {};
                    safeSetText('detailAyahNama', ayah.nama);
                    safeSetText('detailAyahTanggalLahir', ayah.tanggal_lahir);
                    safeSetText('detailAyahAgama', ayah.agama);
                    safeSetText('detailAyahPendidikan', ayah.pendidikan);
                    safeSetText('detailAyahPekerjaan', ayah.pekerjaan);
                    safeSetText('detailAyahSukuBangsa', ayah.suku_bangsa);
                    safeSetText('detailAyahAlamat', ayah.alamat);

                    const ibu = data.data_keluarga?.ibu || {};
                    safeSetText('detailIbuNama', ibu.nama);
                    safeSetText('detailIbuTanggalLahir', ibu.tanggal_lahir);
                    safeSetText('detailIbuAgama', ibu.agama);
                    safeSetText('detailIbuPendidikan', ibu.pendidikan);
                    safeSetText('detailIbuPekerjaan', ibu.pekerjaan);
                    safeSetText('detailIbuSukuBangsa', ibu.suku_bangsa);
                    safeSetText('detailIbuAlamat', ibu.alamat);

                    const wali = data.data_keluarga?.wali || {};
                    safeSetText('detailWaliNama', wali.nama);
                    safeSetText('detailWaliTanggalLahir', wali.tanggal_lahir);
                    safeSetText('detailWaliAgama', wali.agama);
                    safeSetText('detailWaliPendidikan', wali.pendidikan);
                    safeSetText('detailWaliPekerjaan', wali.pekerjaan);
                    safeSetText('detailWaliSukuBangsa', wali.suku_bangsa);
                    safeSetText('detailWaliAlamat', wali.alamat);

                    safeSetText('detailAnakKe', data.anak_ke);
                    displayList('detailSaudaraKandungList', data.saudara_kandung, s => 
                        `Nama: ${s.nama || ''}, Tgl Lahir: ${s.tanggal_lahir || ''}, JK: ${s.jenis_kelamin || ''}, Hub: ${s.status_hubungan || ''}, Pekerjaan/Sekolah: ${s.pekerjaan_sekolah || ''}, Tingkat: ${s.tingkat || ''}, Nikah: ${s.status_perkawinan || ''}`
                    );


                    // D. Keadaan Jasmani
                    safeSetText('detailTinggiBadan', data.tinggi_badan);
                    safeSetText('detailBeratBadan', data.berat_badan);
                    safeSetText('detailGolonganDarah', data.golongan_darah);
                    safeSetText('detailBentukMata', data.bentuk_mata);
                    safeSetText('detailBentukMuka', data.bentuk_muka);
                    safeSetText('detailRambut', data.rambut);
                    safeSetText('detailWarnaKulit', data.warna_kulit);
                    safeSetText('detailMemilikiCacatTubuh', data.memiliki_cacat_tubuh);
                    safeSetText('detailCacatTubuhPenjelasan', data.cacat_tubuh_penjelasan);
                    safeSetText('detailMemakaiKacamata', data.memakai_kacamata);
                    safeSetText('detailKacamataKelainan', data.kacamata_kelainan);
                    safeSetText('detailSakitSeringDiderita', data.sakit_sering_diderita);
                    displayList('detailSakitKerasList', data.sakit_keras, s =>
                        `Penyakit: ${s.jenis_penyakit || ''}, Usia: ${s.usia_saat_sakit || ''}, Opname: ${s.opname || ''} (RS: ${s.opname_di_rs || 'N/A'})`
                    );

                    // E. Penguasaan Bahasa
                    safeSetText('detailKemampuanBahasaIndonesia', data.kemampuan_bahasa_indonesia);
                    safeSetText('detailBahasaSehariHariDirumah', data.bahasa_sehari_hari_dirumah);
                    safeSetText('detailBahasaDaerahDikuasai', Array.isArray(data.bahasa_daerah_dikuasai) ? data.bahasa_daerah_dikuasai.join(', ') : '');
                    safeSetText('detailBahasaDaerahLainnyaText', data.bahasa_daerah_dikuasai?.includes('Lainnya') && data.bahasa_daerah_lainnya_text ? `(${data.bahasa_daerah_lainnya_text})` : '');
                    safeSetText('detailBahasaAsingDikuasai', Array.isArray(data.bahasa_asing_dikuasai) ? data.bahasa_asing_dikuasai.join(', ') : '');
                    safeSetText('detailBahasaAsingLainnyaText', data.bahasa_asing_dikuasai?.includes('Lainnya') && data.bahasa_asing_lainnya_text ? `(${data.bahasa_asing_lainnya_text})` : '');


                    // F. Hobby, Kegemaran, dan Cita-Cita
                    safeSetText('detailHobby', data.hobby);
                    safeSetText('detailCitaCita', data.cita_cita);

                    // G. Keadaan Pendidikan
                    safeSetText('detailPelajaranDisukaiSd', data.pelajaran_disukai_sd);
                    safeSetText('detailAlasanPelajaranDisukaiSd', data.alasan_pelajaran_disukai_sd);
                    safeSetText('detailPelajaranTidakDisukaiSd', data.pelajaran_tidak_disukai_sd);
                    safeSetText('detailAlasanPelajaranTidakDisukaiSd', data.alasan_pelajaran_tidak_disukai_sd);
                     displayList('detailPrestasiSdList', data.prestasi_sd, p =>
                        `Kejuaraan: ${p.nama_kejuaraan || ''}, Tingkat: ${p.tingkat || ''}, Prestasi: ${p.raihan_prestasi || ''}, Tahun/Kelas: ${p.tahun_kelas || ''}`
                    );
                    safeSetText('detailKegiatanBelajarDirumah', data.kegiatan_belajar_dirumah);
                    safeSetText('detailDilaksanakanSetiapBelajar', Array.isArray(data.dilaksanakan_setiap_belajar) ? data.dilaksanakan_setiap_belajar.join(', ') : '');
                    safeSetText('detailKesulitanBelajar', data.kesulitan_belajar);
                    safeSetText('detailHambatanBelajar', data.hambatan_belajar);
                    displayList('detailPrestasiSmpList', data.prestasi_smp, p =>
                        `Kejuaraan: ${p.nama_kejuaraan || ''}, Tingkat: ${p.tingkat || ''}, Prestasi: ${p.raihan_prestasi || ''}, Tahun/Kelas: ${p.tahun_kelas || ''}`
                    );

                    $('#btnEditDetail').attr('onclick', `openModal('edit', '${data.id}')`);
                    $('#detailSiswa').show();
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                    $('#errorMessage').text('Gagal mengambil detail siswa.').removeClass('d-none');
                    setTimeout(() => $('#errorMessage').addClass('d-none'), 5000);
                });
        }
        
        function setCheckboxValues(name, values) {
            $(`input[name="${name}[]"]`).prop('checked', false); // Uncheck all first
            if (Array.isArray(values)) {
                values.forEach(value => {
                    $(`input[name="${name}[]"][value="${value}"]`).prop('checked', true);
                });
            }
        }

        function openModal(mode, id) {
            $('#siswaForm')[0].reset(); // Reset form
            // Uncheck all checkboxes explicitly as reset might not cover them perfectly in all browsers
            $('#siswaForm input[type="checkbox"]').prop('checked', false);
            $('#siswaForm input[type="radio"]').prop('checked', false);


            // Since 'create' mode is removed, mode will always be 'edit' from UI.
            // Default to POST for _method, will be set to PUT for edit.
            $('#_method').val('POST'); 
            if (mode === 'edit') {
                $('#siswaModalLabel').text('Edit Data Siswa');
                fetch(`/api/siswa/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        $('#id').val(data.id);
                        // A. Identitas
                        $('#name').val(data.name);
                        $('#username').val(data.username);
                        $('#jenis_kelamin').val(data.jenis_kelamin);
                        $('#tempat_lahir').val(data.tempat_lahir);
                        $('#tanggal_lahir').val(data.tanggal_lahir);
                        $('#agama').val(data.agama);
                        $('#suku_bangsa').val(data.suku_bangsa);
                        $('#tanggal_masuk').val(data.tanggal_masuk);
                        $('#asal_sekolah').val(data.asal_sekolah);
                        $('#status_sebagai').val(data.status_sebagai);

                        // B. Data Tempat Tinggal
                        $('#alamat_asal').val(data.alamat_asal);
                        $('#nomor_telp_hp').val(data.nomor_telp_hp);
                        setCheckboxValues('termasuk_daerah_asal', data.termasuk_daerah_asal);
                        $('#alamat_sekarang').val(data.alamat_sekarang);
                        $('#nomor_telp_hp_sekarang').val(data.nomor_telp_hp_sekarang);
                        setCheckboxValues('termasuk_daerah_sekarang', data.termasuk_daerah_sekarang);
                        $('#jarak_rumah_sekolah').val(data.jarak_rumah_sekolah);
                        setCheckboxValues('alat_sarana_ke_sekolah', data.alat_sarana_ke_sekolah);
                        $('#tempat_tinggal').val(data.tempat_tinggal);
                        setCheckboxValues('tinggal_bersama', data.tinggal_bersama);
                        $('#rumah_terbuat_dari').val(data.rumah_terbuat_dari);
                        setCheckboxValues('alat_fasilitas_dimiliki', data.alat_fasilitas_dimiliki);
                        
                        // C. Data Keluarga
                        const ayah = data.data_keluarga?.ayah || {};
                        $('#ayah_nama').val(ayah.nama);
                        $('#ayah_tanggal_lahir').val(ayah.tanggal_lahir);
                        $('#ayah_agama').val(ayah.agama);
                        $('#ayah_pendidikan').val(ayah.pendidikan);
                        $('#ayah_pekerjaan').val(ayah.pekerjaan);
                        $('#ayah_suku_bangsa').val(ayah.suku_bangsa);
                        $('#ayah_alamat').val(ayah.alamat);

                        const ibu = data.data_keluarga?.ibu || {};
                        $('#ibu_nama').val(ibu.nama);
                        $('#ibu_tanggal_lahir').val(ibu.tanggal_lahir);
                        $('#ibu_agama').val(ibu.agama);
                        $('#ibu_pendidikan').val(ibu.pendidikan);
                        $('#ibu_pekerjaan').val(ibu.pekerjaan);
                        $('#ibu_suku_bangsa').val(ibu.suku_bangsa);
                        $('#ibu_alamat').val(ibu.alamat);

                        const wali = data.data_keluarga?.wali || {};
                        $('#wali_nama').val(wali.nama);
                        $('#wali_tanggal_lahir').val(wali.tanggal_lahir);
                        $('#wali_agama').val(wali.agama);
                        $('#wali_pendidikan').val(wali.pendidikan);
                        $('#wali_pekerjaan').val(wali.pekerjaan);
                        $('#wali_suku_bangsa').val(wali.suku_bangsa);
                        $('#wali_alamat').val(wali.alamat);

                        $('#anak_ke').val(data.anak_ke);

                        if (data.saudara_kandung && data.saudara_kandung.length > 0) {
                            const s = data.saudara_kandung[0]; // Populate first sibling example
                            $('#saudara_0_nama').val(s.nama);
                            $('#saudara_0_tanggal_lahir').val(s.tanggal_lahir);
                            $('#saudara_0_jenis_kelamin').val(s.jenis_kelamin);
                            $('#saudara_0_status_hubungan').val(s.status_hubungan);
                            $('#saudara_0_pekerjaan_sekolah').val(s.pekerjaan_sekolah);
                            $('#saudara_0_tingkat').val(s.tingkat);
                            $('#saudara_0_status_perkawinan').val(s.status_perkawinan);
                        }

                        // D. Keadaan Jasmani
                        $('#tinggi_badan').val(data.tinggi_badan);
                        $('#berat_badan').val(data.berat_badan);
                        $('#golongan_darah').val(data.golongan_darah);
                        $('#bentuk_mata').val(data.bentuk_mata);
                        $('#bentuk_muka').val(data.bentuk_muka);
                        $('#rambut').val(data.rambut);
                        $('#warna_kulit').val(data.warna_kulit);
                        $(`input[name="memiliki_cacat_tubuh"][value="${data.memiliki_cacat_tubuh || 'Tidak'}"]`).prop('checked', true);
                        $('#cacat_tubuh_penjelasan').val(data.cacat_tubuh_penjelasan);
                        $(`input[name="memakai_kacamata"][value="${data.memakai_kacamata || 'Tidak'}"]`).prop('checked', true);
                        $('#kacamata_kelainan').val(data.kacamata_kelainan);
                        $('#sakit_sering_diderita').val(data.sakit_sering_diderita);
                        
                        if (data.sakit_keras && data.sakit_keras.length > 0) {
                            const sk = data.sakit_keras[0];
                            $('#sakit_keras_0_jenis_penyakit').val(sk.jenis_penyakit);
                            $('#sakit_keras_0_usia_saat_sakit').val(sk.usia_saat_sakit);
                            $('#sakit_keras_0_opname').val(sk.opname);
                            $('#sakit_keras_0_opname_di_rs').val(sk.opname_di_rs);
                        }

                        // E. Penguasaan Bahasa
                        $('#kemampuan_bahasa_indonesia').val(data.kemampuan_bahasa_indonesia);
                        $('#bahasa_sehari_hari_dirumah').val(data.bahasa_sehari_hari_dirumah);
                        setCheckboxValues('bahasa_daerah_dikuasai', data.bahasa_daerah_dikuasai);
                        if (data.bahasa_daerah_dikuasai && data.bahasa_daerah_dikuasai.includes('Lainnya')) {
                            $('#bahasa_daerah_lainnya_text').val(data.bahasa_daerah_lainnya_text);
                        }
                        setCheckboxValues('bahasa_asing_dikuasai', data.bahasa_asing_dikuasai);
                         if (data.bahasa_asing_dikuasai && data.bahasa_asing_dikuasai.includes('Lainnya')) {
                            $('#bahasa_asing_lainnya_text').val(data.bahasa_asing_lainnya_text);
                        }


                        // F. Hobby
                        $('#hobby').val(data.hobby);
                        $('#cita_cita').val(data.cita_cita);

                        // G. Keadaan Pendidikan
                        $('#pelajaran_disukai_sd').val(data.pelajaran_disukai_sd);
                        $('#alasan_pelajaran_disukai_sd').val(data.alasan_pelajaran_disukai_sd);
                        $('#pelajaran_tidak_disukai_sd').val(data.pelajaran_tidak_disukai_sd);
                        $('#alasan_pelajaran_tidak_disukai_sd').val(data.alasan_pelajaran_tidak_disukai_sd);

                        if (data.prestasi_sd && data.prestasi_sd.length > 0) {
                            const psd = data.prestasi_sd[0];
                             $('#prestasi_sd_0_nama_kejuaraan').val(psd.nama_kejuaraan);
                             $('#prestasi_sd_0_tingkat').val(psd.tingkat);
                             $('#prestasi_sd_0_raihan_prestasi').val(psd.raihan_prestasi);
                             $('#prestasi_sd_0_tahun_kelas').val(psd.tahun_kelas);
                        }
                        $(`input[name="kegiatan_belajar_dirumah"][value="${data.kegiatan_belajar_dirumah}"]`).prop('checked', true);
                        setCheckboxValues('dilaksanakan_setiap_belajar', data.dilaksanakan_setiap_belajar);
                        $('#kesulitan_belajar').val(data.kesulitan_belajar);
                        $('#hambatan_belajar').val(data.hambatan_belajar);
                         if (data.prestasi_smp && data.prestasi_smp.length > 0) {
                            const psmp = data.prestasi_smp[0];
                             $('#prestasi_smp_0_nama_kejuaraan').val(psmp.nama_kejuaraan);
                             $('#prestasi_smp_0_tingkat').val(psmp.tingkat);
                             $('#prestasi_smp_0_raihan_prestasi').val(psmp.raihan_prestasi);
                             $('#prestasi_smp_0_tahun_kelas').val(psmp.tahun_kelas);
                        }

                        $('#_method').val('PUT');
                    })
                    .catch(error => {
                        console.error("Error fetching data:", error);
                        $('#errorMessage').text('Gagal mengambil data siswa.').removeClass('d-none');
                        setTimeout(() => $('#errorMessage').addClass('d-none'), 5000);
                    });
            }
            $('#siswaModal').modal('show');
        }

        function submitForm() {
            const formData = new FormData($('#siswaForm')[0]);
            const id = formData.get('id');
            // _method value (PUT) is now directly part of formData if you keep the hidden input with name="_method"
            // For fetch, we set the method directly. Let's assume the hidden _method is for potential other uses or old habits.
            const actualMethod = id ? 'PUT' : 'POST'; // Since create button is gone, id should always exist for edit.

            let url = '/api/siswa';
            if (actualMethod === 'PUT' && id) {
                url = `/api/siswa/${id}`;
            } else {
                // This case (create) should not be reached via UI anymore.
                // If it is, it's an issue or an unhandled mode.
                console.error("Submit called in unexpected mode or without ID for update.");
                $('#errorMessage').text('Operasi tidak diizinkan.').removeClass('d-none');
                setTimeout(() => $('#errorMessage').addClass('d-none'), 5000);
                return;
            }
            
            // Convert FormData to a plain object, then handle specific array conversions for checkboxes
            let formObject = Object.fromEntries(formData.entries());

            // Handle checkbox arrays manually because Object.fromEntries only takes the first one
            // if multiple checkboxes have the same name without "[]".
            // With "[]" in the name, formData.getAll() is better.
            ['termasuk_daerah_asal', 'termasuk_daerah_sekarang', 'alat_sarana_ke_sekolah', 'tinggal_bersama', 'alat_fasilitas_dimiliki', 'bahasa_daerah_dikuasai', 'bahasa_asing_dikuasai', 'dilaksanakan_setiap_belajar'].forEach(arrName => {
                formObject[arrName] = formData.getAll(arrName + '[]');
            });


            fetch(url, {
                method: actualMethod, // Use PUT for updates
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json', // Good practice
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(formObject),
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        let errorMsg = 'Terjadi kesalahan.';
                        if (errorData && errorData.message) {
                            errorMsg = errorData.message;
                        }
                        if (errorData && errorData.errors) { // Laravel validation errors
                            const errors = Object.values(errorData.errors).map(err => err.join(', ')).join('; ');
                            errorMsg += ` Detail: ${errors}`;
                        }
                        throw new Error(errorMsg);
                    });
                }
                return response.json();
            })
            .then(data => {
                $('#siswaModal').modal('hide');
                $('#successMessage').text(data.message || 'Data siswa berhasil disimpan.').removeClass('d-none');
                setTimeout(() => $('#successMessage').addClass('d-none'), 5000);
                $('#siswaTable').DataTable().ajax.reload();
                $('#detailSiswa').hide();
            })
            .catch(error => {
                console.error("Error:", error);
                $('#errorMessage').text(error.message).removeClass('d-none');
                setTimeout(() => $('#errorMessage').addClass('d-none'), 5000);
            });
        }

        function deleteData(id) { // Keep this function if needed elsewhere, or remove if no delete button
            if (confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) {
                fetch(`/api/siswa/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Terjadi kesalahan saat menghapus data.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    $('#successMessage').text(data.message || 'Data siswa berhasil dihapus.').removeClass('d-none');
                    setTimeout(() => $('#successMessage').addClass('d-none'), 5000);
                    $('#siswaTable').DataTable().ajax.reload();
                    $('#detailSiswa').hide();
                })
                .catch(error => {
                    console.error("Error:", error);
                    $('#errorMessage').text(error.message).removeClass('d-none');
                    setTimeout(() => $('#errorMessage').addClass('d-none'), 5000);
                });
            }
        }
    </script>
    </body>
</html>