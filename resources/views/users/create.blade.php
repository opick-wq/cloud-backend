@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

@section('content')
    <h1>Tambah Pengguna Baru</h1>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="card mb-3">
            <div class="card-header">Informasi Identitas</div>
            <div class="card-body">
                {{-- Form untuk field 'identitas' (mungkin perlu dipecah lagi jika kompleks) --}}
                <div class="mb-3">
                    <label for="identitas" class="form-label">Identitas (JSON Object)</label>
                    <textarea class="form-control" id="identitas" name="identitas" rows="3" placeholder='{"nik": "...", "nisn": "..."}'>{{ old('identitas') }}</textarea>
                    <small class="form-text text-muted">Masukkan dalam format JSON object. Contoh: `{"nik": "1234567890123456", "nisn": "1234567890"}`</small>
                </div>
                <div class="mb-3">
                    <label for="jenisKelamin" class="form-label">Jenis Kelamin</label>
                    <select class="form-select" id="jenisKelamin" name="jenisKelamin">
                        <option value="Laki-laki" {{ old('jenisKelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenisKelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tempatLahir" class="form-label">Tempat Lahir</label>
                    <input type="text" class="form-control" id="tempatLahir" name="tempatLahir" value="{{ old('tempatLahir') }}">
                </div>
                <div class="mb-3">
                    <label for="tanggalLahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggalLahir" name="tanggalLahir" value="{{ old('tanggalLahir') }}">
                </div>
                <div class="mb-3">
                    <label for="agama" class="form-label">Agama</label>
                    <input type="text" class="form-control" id="agama" name="agama" value="{{ old('agama') }}">
                </div>
                <div class="mb-3">
                    <label for="sukuBangsa" class="form-label">Suku Bangsa</label>
                    <input type="text" class="form-control" id="sukuBangsa" name="sukuBangsa" value="{{ old('sukuBangsa') }}">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Informasi Sekolah</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="masukSekolahTanggal" class="form-label">Tanggal Masuk Sekolah</label>
                    <input type="date" class="form-control" id="masukSekolahTanggal" name="masukSekolahTanggal" value="{{ old('masukSekolahTanggal') }}">
                </div>
                <div class="mb-3">
                    <label for="asalSekolah" class="form-label">Asal Sekolah</label>
                    <input type="text" class="form-control" id="asalSekolah" name="asalSekolah" value="{{ old('asalSekolah') }}">
                </div>
                <div class="mb-3">
                    <label for="statusSiswa" class="form-label">Status Siswa</label>
                    <input type="text" class="form-control" id="statusSiswa" name="statusSiswa" value="{{ old('statusSiswa') }}">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Informasi Tempat Tinggal</div>
            <div class="card-body">
                {{-- Form untuk field 'tempatTinggal' (mungkin perlu dipecah lagi jika kompleks) --}}
                <div class="mb-3">
                    <label for="tempatTinggal" class="form-label">Tempat Tinggal (JSON Object)</label>
                    <textarea class="form-control" id="tempatTinggal" name="tempatTinggal" rows="3" placeholder='{"provinsi": "...", "kota": "..."}'>{{ old('tempatTinggal') }}</textarea>
                    <small class="form-text text-muted">Masukkan dalam format JSON object. Contoh: `{"provinsi": "Jawa Barat", "kota": "Bandung"}`</small>
                </div>
                <div class="mb-3">
                    <label for="alamatAsal" class="form-label">Alamat Asal</label>
                    <input type="text" class="form-control" id="alamatAsal" name="alamatAsal" value="{{ old('alamatAsal') }}">
                </div>
                <div class="mb-3">
                    <label for="nomorTelpAsal" class="form-label">Nomor Telepon Asal</label>
                    <input type="text" class="form-control" id="nomorTelpAsal" name="nomorTelpAsal" value="{{ old('nomorTelpAsal') }}">
                </div>
                <div class="mb-3">
                    <label for="daerahAsal" class="form-label">Daerah Asal</label>
                    <select class="form-select" id="daerahAsal" name="daerahAsal[]" multiple>
                        <option value="Kota A" {{ in_array('Kota A', old('daerahAsal', [])) ? 'selected' : '' }}>Kota A</option>
                        <option value="Kota B" {{ in_array('Kota B', old('daerahAsal', [])) ? 'selected' : '' }}>Kota B</option>
                        <option value="Kota C" {{ in_array('Kota C', old('daerahAsal', [])) ? 'selected' : '' }}>Kota C</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa daerah jika ada (tahan Ctrl/Cmd untuk multi-pilih).</small>
                </div>
                <div class="mb-3">
                    <label for="alamatSekarang" class="form-label">Alamat Sekarang</label>
                    <input type="text" class="form-control" id="alamatSekarang" name="alamatSekarang" value="{{ old('alamatSekarang') }}">
                </div>
                <div class="mb-3">
                    <label for="nomorTelpSekarang" class="form-label">Nomor Telepon Sekarang</label>
                    <input type="text" class="form-control" id="nomorTelpSekarang" name="nomorTelpSekarang" value="{{ old('nomorTelpSekarang') }}">
                </div>
                <div class="mb-3">
                    <label for="daerahSekarang" class="form-label">Daerah Sekarang</label>
                    <select class="form-select" id="daerahSekarang" name="daerahSekarang[]" multiple>
                        <option value="Kota X" {{ in_array('Kota X', old('daerahSekarang', [])) ? 'selected' : '' }}>Kota X</option>
                        <option value="Kota Y" {{ in_array('Kota Y', old('daerahSekarang', [])) ? 'selected' : '' }}>Kota Y</option>
                        <option value="Kota Z" {{ in_array('Kota Z', old('daerahSekarang', [])) ? 'selected' : '' }}>Kota Z</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa daerah jika ada (tahan Ctrl/Cmd untuk multi-pilih).</small>
                </div>
                <div class="mb-3">
                    <label for="jarakRumahSekolah" class="form-label">Jarak Rumah ke Sekolah (km)</label>
                    <input type="number" class="form-control" id="jarakRumahSekolah" name="jarakRumahSekolah" step="0.1" value="{{ old('jarakRumahSekolah') }}">
                </div>
                <div class="mb-3">
                    <label for="saranaKeSekolah" class="form-label">Sarana ke Sekolah</label>
                    <select class="form-select" id="saranaKeSekolah" name="saranaKeSekolah[]" multiple>
                        <option value="Jalan Kaki" {{ in_array('Jalan Kaki', old('saranaKeSekolah', [])) ? 'selected' : '' }}>Jalan Kaki</option>
                        <option value="Sepeda" {{ in_array('Sepeda', old('saranaKeSekolah', [])) ? 'selected' : '' }}>Sepeda</option>
                        <option value="Motor" {{ in_array('Motor', old('saranaKeSekolah', [])) ? 'selected' : '' }}>Motor</option>
                        <option value="Mobil" {{ in_array('Mobil', old('saranaKeSekolah', [])) ? 'selected' : '' }}>Mobil</option>
                        <option value="Angkutan Umum" {{ in_array('Angkutan Umum', old('saranaKeSekolah', [])) ? 'selected' : '' }}>Angkutan Umum</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa sarana jika ada (tahan Ctrl/Cmd untuk multi-pilih).</small>
                </div>
                <div class="mb-3">
                    <label for="tempatTinggalDi" class="form-label">Tempat Tinggal Saat Ini</label>
                    <input type="text" class="form-control" id="tempatTinggalDi" name="tempatTinggalDi" value="{{ old('tempatTinggalDi') }}">
                </div>
                <div class="mb-3">
                    <label for="tinggalBersama" class="form-label">Tinggal Bersama</label>
                    <select class="form-select" id="tinggalBersama" name="tinggalBersama[]" multiple>
                        <option value="Orang Tua" {{ in_array('Orang Tua', old('tinggalBersama', [])) ? 'selected' : '' }}>Orang Tua</option>
                        <option value="Wali" {{ in_array('Wali', old('tinggalBersama', [])) ? 'selected' : '' }}>Wali</option>
                        <option value="Sendiri" {{ in_array('Sendiri', old('tinggalBersama', [])) ? 'selected' : '' }}>Sendiri</option>
                        <option value="Saudara" {{ in_array('Saudara', old('tinggalBersama', [])) ? 'selected' : '' }}>Saudara</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa opsi jika ada (tahan Ctrl/Cmd untuk multi-pilih).</small>
                </div>
                <div class="mb-3">
                    <label for="rumahTerbuatDari" class="form-label">Rumah Terbuat Dari</label>
                    <input type="text" class="form-control" id="rumahTerbuatDari" name="rumahTerbuatDari" value="{{ old('rumahTerbuatDari') }}">
                </div>
                <div class="mb-3">
                    <label for="fasilitasRumah" class="form-label">Fasilitas Rumah</label>
                    <select class="form-select" id="fasilitasRumah" name="fasilitasRumah[]" multiple>
                        <option value="Listrik" {{ in_array('Listrik', old('fasilitasRumah', [])) ? 'selected' : '' }}>Listrik</option>
                        <option value="Air PDAM" {{ in_array('Air PDAM', old('fasilitasRumah', [])) ? 'selected' : '' }}>Air PDion</option>
                        <option value="Sumur" {{ in_array('Sumur', old('fasilitasRumah', [])) ? 'selected' : '' }}>Sumur</option>
                        <option value="Internet" {{ in_array('Internet', old('fasilitasRumah', [])) ? 'selected' : '' }}>Internet</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa fasilitas jika ada (tahan Ctrl/Cmd untuk multi-pilih).</small>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Data Keluarga</div>
            <div class="card-body">
                <h4>Orang Tua / Wali</h4>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Ayah</h5>
                        <div class="mb-3">
                            <label for="ayah_nama" class="form-label">Nama Ayah</label>
                            <input type="text" class="form-control" id="ayah_nama" name="dataKeluarga[orangTuaWali][ayah][nama]" value="{{ old('dataKeluarga.orangTuaWali.ayah.nama') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_tanggalLahir" class="form-label">Tanggal Lahir Ayah</label>
                            <input type="date" class="form-control" id="ayah_tanggalLahir" name="dataKeluarga[orangTuaWali][ayah][tanggalLahir]" value="{{ old('dataKeluarga.orangTuaWali.ayah.tanggalLahir') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_agama" class="form-label">Agama Ayah</label>
                            <input type="text" class="form-control" id="ayah_agama" name="dataKeluarga[orangTuaWali][ayah][agama]" value="{{ old('dataKeluarga.orangTuaWali.ayah.agama') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_pendidikan" class="form-label">Pendidikan Ayah</label>
                            <input type="text" class="form-control" id="ayah_pendidikan" name="dataKeluarga[orangTuaWali][ayah][pendidikan]" value="{{ old('dataKeluarga.orangTuaWali.ayah.pendidikan') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_pekerjaan" class="form-label">Pekerjaan Ayah</label>
                            <input type="text" class="form-control" id="ayah_pekerjaan" name="dataKeluarga[orangTuaWali][ayah][pekerjaan]" value="{{ old('dataKeluarga.orangTuaWali.ayah.pekerjaan') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_sukuBangsa" class="form-label">Suku Bangsa Ayah</label>
                            <input type="text" class="form-control" id="ayah_sukuBangsa" name="dataKeluarga[orangTuaWali][ayah][sukuBangsa]" value="{{ old('dataKeluarga.orangTuaWali.ayah.sukuBangsa') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_alamat" class="form-label">Alamat Ayah</label>
                            <input type="text" class="form-control" id="ayah_alamat" name="dataKeluarga[orangTuaWali][ayah][alamat]" value="{{ old('dataKeluarga.orangTuaWali.ayah.alamat') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5>Ibu</h5>
                        <div class="mb-3">
                            <label for="ibu_nama" class="form-label">Nama Ibu</label>
                            <input type="text" class="form-control" id="ibu_nama" name="dataKeluarga[orangTuaWali][ibu][nama]" value="{{ old('dataKeluarga.orangTuaWali.ibu.nama') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_tanggalLahir" class="form-label">Tanggal Lahir Ibu</label>
                            <input type="date" class="form-control" id="ibu_tanggalLahir" name="dataKeluarga[orangTuaWali][ibu][tanggalLahir]" value="{{ old('dataKeluarga.orangTuaWali.ibu.tanggalLahir') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_agama" class="form-label">Agama Ibu</label>
                            <input type="text" class="form-control" id="ibu_agama" name="dataKeluarga[orangTuaWali][ibu][agama]" value="{{ old('dataKeluarga.orangTuaWali.ibu.agama') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_pendidikan" class="form-label">Pendidikan Ibu</label>
                            <input type="text" class="form-control" id="ibu_pendidikan" name="dataKeluarga[orangTuaWali][ibu][pendidikan]" value="{{ old('dataKeluarga.orangTuaWali.ibu.pendidikan') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_pekerjaan" class="form-label">Pekerjaan Ibu</label>
                            <input type="text" class="form-control" id="ibu_pekerjaan" name="dataKeluarga[orangTuaWali][ibu][pekerjaan]" value="{{ old('dataKeluarga.orangTuaWali.ibu.pekerjaan') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_sukuBangsa" class="form-label">Suku Bangsa Ibu</label>
                            <input type="text" class="form-control" id="ibu_sukuBangsa" name="dataKeluarga[orangTuaWali][ibu][sukuBangsa]" value="{{ old('dataKeluarga.orangTuaWali.ibu.sukuBangsa') }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_alamat" class="form-label">Alamat Ibu</label>
                            <input type="text" class="form-control" id="ibu_alamat" name="dataKeluarga[orangTuaWali][ibu][alamat]" value="{{ old('dataKeluarga.orangTuaWali.ibu.alamat') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5>Wali (Opsional)</h5>
                        <div class="mb-3">
                            <label for="wali_nama" class="form-label">Nama Wali</label>
                            <input type="text" class="form-control" id="wali_nama" name="dataKeluarga[orangTuaWali][wali][nama]" value="{{ old('dataKeluarga.orangTuaWali.wali.nama') }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_tanggalLahir" class="form-label">Tanggal Lahir Wali</label>
                            <input type="date" class="form-control" id="wali_tanggalLahir" name="dataKeluarga[orangTuaWali][wali][tanggalLahir]" value="{{ old('dataKeluarga.orangTuaWali.wali.tanggalLahir') }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_agama" class="form-label">Agama Wali</label>
                            <input type="text" class="form-control" id="wali_agama" name="dataKeluarga[orangTuaWali][wali][agama]" value="{{ old('dataKeluarga.orangTuaWali.wali.agama') }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_pendidikan" class="form-label">Pendidikan Wali</label>
                            <input type="text" class="form-control" id="wali_pendidikan" name="dataKeluarga[orangTuaWali][wali][pendidikan]" value="{{ old('dataKeluarga.orangTuaWali.wali.pendidikan') }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_pekerjaan" class="form-label">Pekerjaan Wali</label>
                            <input type="text" class="form-control" id="wali_pekerjaan" name="dataKeluarga[orangTuaWali][wali][pekerjaan]" value="{{ old('dataKeluarga.orangTuaWali.wali.pekerjaan') }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_sukuBangsa" class="form-label">Suku Bangsa Wali</label>
                            <input type="text" class="form-control" id="wali_sukuBangsa" name="dataKeluarga[orangTuaWali][wali][sukuBangsa]" value="{{ old('dataKeluarga.orangTuaWali.wali.sukuBangsa') }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_alamat" class="form-label">Alamat Wali</label>
                            <input type="text" class="form-control" id="wali_alamat" name="dataKeluarga[orangTuaWali][wali][alamat]" value="{{ old('dataKeluarga.orangTuaWali.wali.alamat') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Catatan Lain</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="anakKe" class="form-label">Anak Ke-</label>
                    <input type="number" class="form-control" id="anakKe" name="catatanLain[anakKe]" value="{{ old('catatanLain.anakKe') }}">
                </div>
                <h4>Saudara Kandung</h4>
                <div id="saudaraKandungContainer">
                    {{-- Dynamic fields for saudaraKandung will be added here via JavaScript if needed, or manually if few --}}
                    {{-- For simplicity, we'll add one static example. You'd need JS for dynamic adds. --}}
                    <div class="border p-3 mb-2">
                        <div class="mb-3">
                            <label for="saudara_nama_0" class="form-label">Nama Saudara 1</label>
                            <input type="text" class="form-control" id="saudara_nama_0" name="catatanLain[saudaraKandung][0][nama]" value="{{ old('catatanLain.saudaraKandung.0.nama') }}">
                        </div>
                        <div class="mb-3">
                            <label for="saudara_tanggalLahir_0" class="form-label">Tanggal Lahir Saudara 1</label>
                            <input type="date" class="form-control" id="saudara_tanggalLahir_0" name="catatanLain[saudaraKandung][0][tanggalLahir]" value="{{ old('catatanLain.saudaraKandung.0.tanggalLahir') }}">
                        </div>
                        <div class="mb-3">
                            <label for="saudara_jenisKelamin_0" class="form-label">Jenis Kelamin Saudara 1</label>
                            <select class="form-select" id="saudara_jenisKelamin_0" name="catatanLain[saudaraKandung][0][jenisKelamin]">
                                <option value="Laki-laki" {{ old('catatanLain.saudaraKandung.0.jenisKelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('catatanLain.saudaraKandung.0.jenisKelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="saudara_kandungSiri_0" class="form-label">Kandung / Tiri</label>
                            <input type="text" class="form-control" id="saudara_kandungSiri_0" name="catatanLain[saudaraKandung][0][kandungSiri]" value="{{ old('catatanLain.saudaraKandung.0.kandungSiri') }}">
                        </div>
                        <div class="mb-3">
                            <label for="saudara_pekerjaanSekolah_0" class="form-label">Pekerjaan / Sekolah</label>
                            <input type="text" class="form-control" id="saudara_pekerjaanSekolah_0" name="catatanLain[saudaraKandung][0][pekerjaanSekolah]" value="{{ old('catatanLain.saudaraKandung.0.pekerjaanSekolah') }}">
                        </div>
                        <div class="mb-3">
                            <label for="saudara_tingkat_0" class="form-label">Tingkat</label>
                            <input type="text" class="form-control" id="saudara_tingkat_0" name="catatanLain[saudaraKandung][0][tingkat]" value="{{ old('catatanLain.saudaraKandung.0.tingkat') }}">
                        </div>
                        <div class="mb-3">
                            <label for="saudara_kawinBelum_0" class="form-label">Status Pernikahan</label>
                            <input type="text" class="form-control" id="saudara_kawinBelum_0" name="catatanLain[saudaraKandung][0][kawinBelum]" value="{{ old('catatanLain.saudaraKandung.0.kawinBelum') }}">
                        </div>
                    </div>
                    {{-- Add more saudaraKandung blocks as needed, or use JavaScript for dynamic addition --}}
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Keadaan Jasmani</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="tinggiBadan" class="form-label">Tinggi Badan (cm)</label>
                    <input type="number" class="form-control" id="tinggiBadan" name="keadaanJasmani[tinggiBadan]" value="{{ old('keadaanJasmani.tinggiBadan') }}">
                </div>
                <div class="mb-3">
                    <label for="beratBadan" class="form-label">Berat Badan (kg)</label>
                    <input type="number" class="form-control" id="beratBadan" name="keadaanJasmani[beratBadan]" value="{{ old('keadaanJasmani.beratBadan') }}">
                </div>
                <div class="mb-3">
                    <label for="golonganDarah" class="form-label">Golongan Darah</label>
                    <input type="text" class="form-control" id="golonganDarah" name="keadaanJasmani[golonganDarah]" value="{{ old('keadaanJasmani.golonganDarah') }}">
                </div>
                <div class="mb-3">
                    <label for="bentukMata" class="form-label">Bentuk Mata</label>
                    <input type="text" class="form-control" id="bentukMata" name="keadaanJasmani[bentukMata]" value="{{ old('keadaanJasmani.bentukMata') }}">
                </div>
                <div class="mb-3">
                    <label for="bentukMuka" class="form-label">Bentuk Muka</label>
                    <input type="text" class="form-control" id="bentukMuka" name="keadaanJasmani[bentukMuka]" value="{{ old('keadaanJasmani.bentukMuka') }}">
                </div>
                <div class="mb-3">
                    <label for="rambut" class="form-label">Rambut</label>
                    <input type="text" class="form-control" id="rambut" name="keadaanJasmani[rambut]" value="{{ old('keadaanJasmani.rambut') }}">
                </div>
                <div class="mb-3">
                    <label for="warnaKulit" class="form-label">Warna Kulit</label>
                    <input type="text" class="form-control" id="warnaKulit" name="keadaanJasmani[warnaKulit]" value="{{ old('keadaanJasmani.warnaKulit') }}">
                </div>

                <h4>Cacat Tubuh</h4>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="cacatTubuh_yaTidak" name="cacatTubuh[yaTidak]" value="1" {{ old('cacatTubuh.yaTidak') ? 'checked' : '' }}>
                    <label class="form-check-label" for="cacatTubuh_yaTidak">Ada Cacat Tubuh?</label>
                </div>
                <div class="mb-3">
                    <label for="cacatTubuh_keterangan" class="form-label">Keterangan Cacat Tubuh</label>
                    <input type="text" class="form-control" id="cacatTubuh_keterangan" name="cacatTubuh[keterangan]" value="{{ old('cacatTubuh.keterangan') }}">
                </div>

                <h4>Kacamata</h4>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="kacamata_yaTidak" name="kacamata[yaTidak]" value="1" {{ old('kacamata.yaTidak') ? 'checked' : '' }}>
                    <label class="form-check-label" for="kacamata_yaTidak">Menggunakan Kacamata?</label>
                </div>

                <h4>Kelainan Mata (Jika Menggunakan Kacamata)</h4>
                <div class="mb-3">
                    <label for="kelainan_minus" class="form-label">Minus</label>
                    <input type="number" class="form-control" id="kelainan_minus" name="kelainan[minus]" step="0.1" value="{{ old('kelainan.minus') }}">
                </div>
                <div class="mb-3">
                    <label for="kelainan_plus" class="form-label">Plus</label>
                    <input type="number" class="form-control" id="kelainan_plus" name="kelainan[plus]" step="0.1" value="{{ old('kelainan.plus') }}">
                </div>
                <div class="mb-3">
                    <label for="kelainan_silinder" class="form-label">Silinder</label>
                    <input type="number" class="form-control" id="kelainan_silinder" name="kelainan[silinder]" step="0.1" value="{{ old('kelainan.silinder') }}">
                </div>

                <div class="mb-3">
                    <label for="sakitSering" class="form-label">Sakit yang Sering Diderita</label>
                    <input type="text" class="form-control" id="sakitSering" name="sakitSering" value="{{ old('sakitSering') }}">
                </div>

                <h4>Sakit Keras (Riwayat)</h4>
                <div id="sakitKerasContainer">
                    {{-- Dynamic fields for sakitKeras. Add one static example. --}}
                    <div class="border p-3 mb-2">
                        <div class="mb-3">
                            <label for="sakitKeras_jenisPenyakit_0" class="form-label">Jenis Penyakit 1</label>
                            <input type="text" class="form-control" id="sakitKeras_jenisPenyakit_0" name="sakitKeras[0][jenisPenyakit]" value="{{ old('sakitKeras.0.jenisPenyakit') }}">
                        </div>
                        <div class="mb-3">
                            <label for="sakitKeras_usia_0" class="form-label">Usia Saat Sakit 1</label>
                            <input type="number" class="form-control" id="sakitKeras_usia_0" name="sakitKeras[0][usia]" value="{{ old('sakitKeras.0.usia') }}">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="sakitKeras_opname_0" name="sakitKeras[0][opname]" value="1" {{ old('sakitKeras.0.opname') ? 'checked' : '' }}>
                            <label class="form-check-label" for="sakitKeras_opname_0">Pernah Opname?</label>
                        </div>
                        <div class="mb-3">
                            <label for="sakitKeras_opnameRS_0" class="form-label">Nama RS (Jika Opname)</label>
                            <input type="text" class="form-control" id="sakitKeras_opnameRS_0" name="sakitKeras[0][opnameRS]" value="{{ old('sakitKeras.0.opnameRS') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Penguasaan Bahasa</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="bahasaIndonesia" class="form-label">Bahasa Indonesia</label>
                    <input type="text" class="form-control" id="bahasaIndonesia" name="penguasaanBahasa[bahasaIndonesia]" value="{{ old('penguasaanBahasa.bahasaIndonesia') }}">
                </div>
                <div class="mb-3">
                    <label for="bahasaRumah" class="form-label">Bahasa di Rumah</label>
                    <input type="text" class="form-control" id="bahasaRumah" name="penguasaanBahasa[bahasaRumah]" value="{{ old('penguasaanBahasa.bahasaRumah') }}">
                </div>
                <div class="mb-3">
                    <label for="bahasaDaerah" class="form-label">Bahasa Daerah</label>
                    <select class="form-select" id="bahasaDaerah" name="penguasaanBahasa[bahasaDaerah][]" multiple>
                        <option value="Jawa" {{ in_array('Jawa', old('penguasaanBahasa.bahasaDaerah', [])) ? 'selected' : '' }}>Jawa</option>
                        <option value="Sunda" {{ in_array('Sunda', old('penguasaanBahasa.bahasaDaerah', [])) ? 'selected' : '' }}>Sunda</option>
                        <option value="Batak" {{ in_array('Batak', old('penguasaanBahasa.bahasaDaerah', [])) ? 'selected' : '' }}>Batak</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa bahasa daerah jika ada.</small>
                </div>
                <div class="mb-3">
                    <label for="bahasaAsing" class="form-label">Bahasa Asing</label>
                    <select class="form-select" id="bahasaAsing" name="penguasaanBahasa[bahasaAsing][]" multiple>
                        <option value="Inggris" {{ in_array('Inggris', old('penguasaanBahasa.bahasaAsing', [])) ? 'selected' : '' }}>Inggris</option>
                        <option value="Mandarin" {{ in_array('Mandarin', old('penguasaanBahasa.bahasaAsing', [])) ? 'selected' : '' }}>Mandarin</option>
                        <option value="Jepang" {{ in_array('Jepang', old('penguasaanBahasa.bahasaAsing', [])) ? 'selected' : '' }}>Jepang</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa bahasa asing jika ada.</small>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Hobi, Kegemaran, Cita-Cita</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="hobi" class="form-label">Hobi</label>
                    <input type="text" class="form-control" id="hobi" name="hobiKegemaranCitaCita[hobi]" value="{{ old('hobiKegemaranCitaCita.hobi') }}">
                </div>
                <div class="mb-3">
                    <label for="citaCita" class="form-label">Cita-Cita</label>
                    <input type="text" class="form-control" id="citaCita" name="hobiKegemaranCitaCita[citaCita]" value="{{ old('hobiKegemaranCitaCita.citaCita') }}">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Keadaan Pendidikan</div>
            <div class="card-body">
                <h4>Pelajaran SD yang Disukai</h4>
                <div id="pelajaranSDDisukaiContainer">
                    {{-- Dynamic fields for pelajaranSDDisukai --}}
                    <div class="border p-3 mb-2">
                        <div class="mb-3">
                            <label for="pelajaranSDDisukai_pelajaran_0" class="form-label">Pelajaran 1</label>
                            <input type="text" class="form-control" id="pelajaranSDDisukai_pelajaran_0" name="keadaanPendidikan[pelajaranSDDisukai][0][pelajaran]" value="{{ old('keadaanPendidikan.pelajaranSDDisukai.0.pelajaran') }}">
                        </div>
                        <div class="mb-3">
                            <label for="pelajaranSDDisukai_alasan_0" class="form-label">Alasan 1</label>
                            <input type="text" class="form-control" id="pelajaranSDDisukai_alasan_0" name="keadaanPendidikan[pelajaranSDDisukai][0][alasan]" value="{{ old('keadaanPendidikan.pelajaranSDDisukai.0.alasan') }}">
                        </div>
                    </div>
                </div>

                <h4>Pelajaran SD yang Tidak Disukai</h4>
                <div id="pelajaranSDTidakDisukaiContainer">
                    {{-- Dynamic fields for pelajaranSDTidakDisukai --}}
                    <div class="border p-3 mb-2">
                        <div class="mb-3">
                            <label for="pelajaranSDTidakDisukai_pelajaran_0" class="form-label">Pelajaran 1</label>
                            <input type="text" class="form-control" id="pelajaranSDTidakDisukai_pelajaran_0" name="keadaanPendidikan[pelajaranSDTidakDisukai][0][pelajaran]" value="{{ old('keadaanPendidikan.pelajaranSDTidakDisukai.0.pelajaran') }}">
                        </div>
                        <div class="mb-3">
                            <label for="pelajaranSDTidakDisukai_alasan_0" class="form-label">Alasan 1</label>
                            <input type="text" class="form-control" id="pelajaranSDTidakDisukai_alasan_0" name="keadaanPendidikan[pelajaranSDTidakDisukai][0][alasan]" value="{{ old('keadaanPendidikan.pelajaranSDTidakDisukai.0.alasan') }}">
                        </div>
                    </div>
                </div>

                <h4>Prestasi SD</h4>
                <div id="prestasiSDContainer">
                    {{-- Dynamic fields for prestasiSD --}}
                    <div class="border p-3 mb-2">
                        <div class="mb-3">
                            <label for="prestasiSD_namaKejuaraan_0" class="form-label">Nama Kejuaraan 1</label>
                            <input type="text" class="form-control" id="prestasiSD_namaKejuaraan_0" name="keadaanPendidikan[prestasiSD][0][namaKejuaraan]" value="{{ old('keadaanPendidikan.prestasiSD.0.namaKejuaraan') }}">
                        </div>
                        <div class="mb-3">
                            <label for="prestasiSD_tingkat_0" class="form-label">Tingkat 1</label>
                            <input type="text" class="form-control" id="prestasiSD_tingkat_0" name="keadaanPendidikan[prestasiSD][0][tingkat]" value="{{ old('keadaanPendidikan.prestasiSD.0.tingkat') }}">
                        </div>
                        <div class="mb-3">
                            <label for="prestasiSD_raihanPrestasi_0" class="form-label">Raihan Prestasi 1</label>
                            <input type="text" class="form-control" id="prestasiSD_raihanPrestasi_0" name="keadaanPendidikan[prestasiSD][0][raihanPrestasi]" value="{{ old('keadaanPendidikan.prestasiSD.0.raihanPrestasi') }}">
                        </div>
                        <div class="mb-3">
                            <label for="prestasiSD_tahunKelas_0" class="form-label">Tahun / Kelas 1</label>
                            <input type="text" class="form-control" id="prestasiSD_tahunKelas_0" name="keadaanPendidikan[prestasiSD][0][tahunKelas]" value="{{ old('keadaanPendidikan.prestasiSD.0.tahunKelas') }}">
                        </div>
                    </div>
                </div>

                <h4>Masalah Belajar</h4>
                <div class="mb-3">
                    <label for="kegiatanBelajar" class="form-label">Kegiatan Belajar</label>
                    <input type="text" class="form-control" id="kegiatanBelajar" name="masalahBelajar[kegiatanBelajar]" value="{{ old('masalahBelajar.kegiatanBelajar') }}">
                </div>
                <div class="mb-3">
                    <label for="dilaksanakanSetiap" class="form-label">Dilaksanakan Setiap</label>
                    <select class="form-select" id="dilaksanakanSetiap" name="masalahBelajar[dilaksanakanSetiap][]" multiple>
                        <option value="Hari" {{ in_array('Hari', old('masalahBelajar.dilaksanakanSetiap', [])) ? 'selected' : '' }}>Hari</option>
                        <option value="Minggu" {{ in_array('Minggu', old('masalahBelajar.dilaksanakanSetiap', [])) ? 'selected' : '' }}>Minggu</option>
                        <option value="Bulan" {{ in_array('Bulan', old('masalahBelajar.dilaksanakanSetiap', [])) ? 'selected' : '' }}>Bulan</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa opsi jika ada.</small>
                </div>
                <div class="mb-3">
                    <label for="kesulitanBelajar" class="form-label">Kesulitan Belajar</label>
                    <input type="text" class="form-control" id="kesulitanBelajar" name="masalahBelajar[kesulitanBelajar]" value="{{ old('masalahBelajar.kesulitanBelajar') }}">
                </div>
                <div class="mb-3">
                    <label for="hambatanBelajar" class="form-label">Hambatan Belajar</label>
                    <input type="text" class="form-control" id="hambatanBelajar" name="masalahBelajar[hambatanBelajar]" value="{{ old('masalahBelajar.hambatanBelajar') }}">
                </div>

                <h4>Prestasi SMP</h4>
                <div id="prestasiSMPContainer">
                    {{-- Dynamic fields for prestasiSMP --}}
                    <div class="border p-3 mb-2">
                        <div class="mb-3">
                            <label for="prestasiSMP_namaKejuaraan_0" class="form-label">Nama Kejuaraan 1</label>
                            <input type="text" class="form-control" id="prestasiSMP_namaKejuaraan_0" name="keadaanPendidikan[prestasiSMP][0][namaKejuaraan]" value="{{ old('keadaanPendidikan.prestasiSMP.0.namaKejuaraan') }}">
                        </div>
                        <div class="mb-3">
                            <label for="prestasiSMP_tingkat_0" class="form-label">Tingkat 1</label>
                            <input type="text" class="form-control" id="prestasiSMP_tingkat_0" name="keadaanPendidikan[prestasiSMP][0][tingkat]" value="{{ old('keadaanPendidikan.prestasiSMP.0.tingkat') }}">
                        </div>
                        <div class="mb-3">
                            <label for="prestasiSMP_raihanPrestasi_0" class="form-label">Raihan Prestasi 1</label>
                            <input type="text" class="form-control" id="prestasiSMP_raihanPrestasi_0" name="keadaanPendidikan[prestasiSMP][0][raihanPrestasi]" value="{{ old('keadaanPendidikan.prestasiSMP.0.raihanPrestasi') }}">
                        </div>
                        <div class="mb-3">
                            <label for="prestasiSMP_tahunKelas_0" class="form-label">Tahun / Kelas 1</label>
                            <input type="text" class="form-control" id="prestasiSMP_tahunKelas_0" name="keadaanPendidikan[prestasiSMP][0][tahunKelas]" value="{{ old('keadaanPendidikan.prestasiSMP.0.tahunKelas') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Data Bimbingan</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="aktivitasSetelahSekolah" class="form-label">Aktivitas Setelah Sekolah</label>
                    <select class="form-select" id="aktivitasSetelahSekolah" name="dataBimbingan[aktivitasSetelahSekolah][]" multiple>
                        <option value="Les Tambahan" {{ in_array('Les Tambahan', old('dataBimbingan.aktivitasSetelahSekolah', [])) ? 'selected' : '' }}>Les Tambahan</option>
                        <option value="Kursus" {{ in_array('Kursus', old('dataBimbingan.aktivitasSetelahSekolah', [])) ? 'selected' : '' }}>Kursus</option>
                        <option value="Olahraga" {{ in_array('Olahraga', old('dataBimbingan.aktivitasSetelahSekolah', [])) ? 'selected' : '' }}>Olahraga</option>
                        <option value="Kerja Paruh Waktu" {{ in_array('Kerja Paruh Waktu', old('dataBimbingan.aktivitasSetelahSekolah', [])) ? 'selected' : '' }}>Kerja Paruh Waktu</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa aktivitas jika ada.</small>
                </div>
                <div class="mb-3">
                    <label for="citaCitaBimbingan" class="form-label">Cita-Cita (Bimbingan)</label>
                    <input type="text" class="form-control" id="citaCitaBimbingan" name="dataBimbingan[citaCita]" value="{{ old('dataBimbingan.citaCita') }}">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Simpan Pengguna</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection