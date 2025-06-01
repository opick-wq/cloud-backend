@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
    <h1>Edit Pengguna: {{ $user['id'] }}</h1>

    <form action="{{ route('users.update', $user['id']) }}" method="POST">
        @csrf
        @method('PUT') {{-- Penting untuk metode UPDATE --}}

        @php
            $fields = $user['fields'];
        @endphp

        <div class="card mb-3">
            <div class="card-header">Informasi Identitas</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="identitas" class="form-label">Identitas (JSON Object)</label>
                    <textarea class="form-control" id="identitas" name="identitas" rows="3" placeholder='{"nik": "...", "nisn": "..."}'>{{ json_encode($fields['identitas']['mapValue']['fields'] ?? []) }}</textarea>
                    <small class="form-text text-muted">Masukkan dalam format JSON object. Contoh: `{"nik": "1234567890123456", "nisn": "1234567890"}`</small>
                </div>
                <div class="mb-3">
                    <label for="jenisKelamin" class="form-label">Jenis Kelamin</label>
                    <select class="form-select" id="jenisKelamin" name="jenisKelamin">
                        <option value="Laki-laki" {{ ($fields['jenisKelamin']['stringValue'] ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ ($fields['jenisKelamin']['stringValue'] ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tempatLahir" class="form-label">Tempat Lahir</label>
                    <input type="text" class="form-control" id="tempatLahir" name="tempatLahir" value="{{ $fields['tempatLahir']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="tanggalLahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggalLahir" name="tanggalLahir" value="{{ $fields['tanggalLahir']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="agama" class="form-label">Agama</label>
                    <input type="text" class="form-control" id="agama" name="agama" value="{{ $fields['agama']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="sukuBangsa" class="form-label">Suku Bangsa</label>
                    <input type="text" class="form-control" id="sukuBangsa" name="sukuBangsa" value="{{ $fields['sukuBangsa']['stringValue'] ?? '' }}">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Informasi Sekolah</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="masukSekolahTanggal" class="form-label">Tanggal Masuk Sekolah</label>
                    <input type="date" class="form-control" id="masukSekolahTanggal" name="masukSekolahTanggal" value="{{ $fields['masukSekolahTanggal']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="asalSekolah" class="form-label">Asal Sekolah</label>
                    <input type="text" class="form-control" id="asalSekolah" name="asalSekolah" value="{{ $fields['asalSekolah']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="statusSiswa" class="form-label">Status Siswa</label>
                    <input type="text" class="form-control" id="statusSiswa" name="statusSiswa" value="{{ $fields['statusSiswa']['stringValue'] ?? '' }}">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Informasi Tempat Tinggal</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="tempatTinggal" class="form-label">Tempat Tinggal (JSON Object)</label>
                    <textarea class="form-control" id="tempatTinggal" name="tempatTinggal" rows="3" placeholder='{"provinsi": "...", "kota": "..."}'>{{ json_encode($fields['tempatTinggal']['mapValue']['fields'] ?? []) }}</textarea>
                    <small class="form-text text-muted">Masukkan dalam format JSON object. Contoh: `{"provinsi": "Jawa Barat", "kota": "Bandung"}`</small>
                </div>
                <div class="mb-3">
                    <label for="alamatAsal" class="form-label">Alamat Asal</label>
                    <input type="text" class="form-control" id="alamatAsal" name="alamatAsal" value="{{ $fields['alamatAsal']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="nomorTelpAsal" class="form-label">Nomor Telepon Asal</label>
                    <input type="text" class="form-control" id="nomorTelpAsal" name="nomorTelpAsal" value="{{ $fields['nomorTelpAsal']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="daerahAsal" class="form-label">Daerah Asal</label>
                    <select class="form-select" id="daerahAsal" name="daerahAsal[]" multiple>
                        @php
                            $selectedDaerahAsal = array_column($fields['daerahAsal']['arrayValue']['values'] ?? [], 'stringValue');
                        @endphp
                        <option value="Kota A" {{ in_array('Kota A', $selectedDaerahAsal) ? 'selected' : '' }}>Kota A</option>
                        <option value="Kota B" {{ in_array('Kota B', $selectedDaerahAsal) ? 'selected' : '' }}>Kota B</option>
                        <option value="Kota C" {{ in_array('Kota C', $selectedDaerahAsal) ? 'selected' : '' }}>Kota C</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa daerah jika ada (tahan Ctrl/Cmd untuk multi-pilih).</small>
                </div>
                <div class="mb-3">
                    <label for="alamatSekarang" class="form-label">Alamat Sekarang</label>
                    <input type="text" class="form-control" id="alamatSekarang" name="alamatSekarang" value="{{ $fields['alamatSekarang']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="nomorTelpSekarang" class="form-label">Nomor Telepon Sekarang</label>
                    <input type="text" class="form-control" id="nomorTelpSekarang" name="nomorTelpSekarang" value="{{ $fields['nomorTelpSekarang']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="daerahSekarang" class="form-label">Daerah Sekarang</label>
                    <select class="form-select" id="daerahSekarang" name="daerahSekarang[]" multiple>
                        @php
                            $selectedDaerahSekarang = array_column($fields['daerahSekarang']['arrayValue']['values'] ?? [], 'stringValue');
                        @endphp
                        <option value="Kota X" {{ in_array('Kota X', $selectedDaerahSekarang) ? 'selected' : '' }}>Kota X</option>
                        <option value="Kota Y" {{ in_array('Kota Y', $selectedDaerahSekarang) ? 'selected' : '' }}>Kota Y</option>
                        <option value="Kota Z" {{ in_array('Kota Z', $selectedDaerahSekarang) ? 'selected' : '' }}>Kota Z</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa daerah jika ada (tahan Ctrl/Cmd untuk multi-pilih).</small>
                </div>
                <div class="mb-3">
                    <label for="jarakRumahSekolah" class="form-label">Jarak Rumah ke Sekolah (km)</label>
                    <input type="number" class="form-control" id="jarakRumahSekolah" name="jarakRumahSekolah" step="0.1" value="{{ $fields['jarakRumahSekolah']['integerValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="saranaKeSekolah" class="form-label">Sarana ke Sekolah</label>
                    <select class="form-select" id="saranaKeSekolah" name="saranaKeSekolah[]" multiple>
                        @php
                            $selectedSaranaKeSekolah = array_column($fields['saranaKeSekolah']['arrayValue']['values'] ?? [], 'stringValue');
                        @endphp
                        <option value="Jalan Kaki" {{ in_array('Jalan Kaki', $selectedSaranaKeSekolah) ? 'selected' : '' }}>Jalan Kaki</option>
                        <option value="Sepeda" {{ in_array('Sepeda', $selectedSaranaKeSekolah) ? 'selected' : '' }}>Sepeda</option>
                        <option value="Motor" {{ in_array('Motor', $selectedSaranaKeSekolah) ? 'selected' : '' }}>Motor</option>
                        <option value="Mobil" {{ in_array('Mobil', $selectedSaranaKeSekolah) ? 'selected' : '' }}>Mobil</option>
                        <option value="Angkutan Umum" {{ in_array('Angkutan Umum', $selectedSaranaKeSekolah) ? 'selected' : '' }}>Angkutan Umum</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa sarana jika ada (tahan Ctrl/Cmd untuk multi-pilih).</small>
                </div>
                <div class="mb-3">
                    <label for="tempatTinggalDi" class="form-label">Tempat Tinggal Saat Ini</label>
                    <input type="text" class="form-control" id="tempatTinggalDi" name="tempatTinggalDi" value="{{ $fields['tempatTinggalDi']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="tinggalBersama" class="form-label">Tinggal Bersama</label>
                    <select class="form-select" id="tinggalBersama" name="tinggalBersama[]" multiple>
                        @php
                            $selectedTinggalBersama = array_column($fields['tinggalBersama']['arrayValue']['values'] ?? [], 'stringValue');
                        @endphp
                        <option value="Orang Tua" {{ in_array('Orang Tua', $selectedTinggalBersama) ? 'selected' : '' }}>Orang Tua</option>
                        <option value="Wali" {{ in_array('Wali', $selectedTinggalBersama) ? 'selected' : '' }}>Wali</option>
                        <option value="Sendiri" {{ in_array('Sendiri', $selectedTinggalBersama) ? 'selected' : '' }}>Sendiri</option>
                        <option value="Saudara" {{ in_array('Saudara', $selectedTinggalBersama) ? 'selected' : '' }}>Saudara</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa opsi jika ada (tahan Ctrl/Cmd untuk multi-pilih).</small>
                </div>
                <div class="mb-3">
                    <label for="rumahTerbuatDari" class="form-label">Rumah Terbuat Dari</label>
                    <input type="text" class="form-control" id="rumahTerbuatDari" name="rumahTerbuatDari" value="{{ $fields['rumahTerbuatDari']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="fasilitasRumah" class="form-label">Fasilitas Rumah</label>
                    <select class="form-select" id="fasilitasRumah" name="fasilitasRumah[]" multiple>
                        @php
                            $selectedFasilitasRumah = array_column($fields['fasilitasRumah']['arrayValue']['values'] ?? [], 'stringValue');
                        @endphp
                        <option value="Listrik" {{ in_array('Listrik', $selectedFasilitasRumah) ? 'selected' : '' }}>Listrik</option>
                        <option value="Air PDAM" {{ in_array('Air PDAM', $selectedFasilitasRumah) ? 'selected' : '' }}>Air PDAM</option>
                        <option value="Sumur" {{ in_array('Sumur', $selectedFasilitasRumah) ? 'selected' : '' }}>Sumur</option>
                        <option value="Internet" {{ in_array('Internet', $selectedFasilitasRumah) ? 'selected' : '' }}>Internet</option>
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
                            <input type="text" class="form-control" id="ayah_nama" name="dataKeluarga[orangTuaWali][ayah][nama]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['nama']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_tanggalLahir" class="form-label">Tanggal Lahir Ayah</label>
                            <input type="date" class="form-control" id="ayah_tanggalLahir" name="dataKeluarga[orangTuaWali][ayah][tanggalLahir]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['tanggalLahir']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_agama" class="form-label">Agama Ayah</label>
                            <input type="text" class="form-control" id="ayah_agama" name="dataKeluarga[orangTuaWali][ayah][agama]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['agama']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_pendidikan" class="form-label">Pendidikan Ayah</label>
                            <input type="text" class="form-control" id="ayah_pendidikan" name="dataKeluarga[orangTuaWali][ayah][pendidikan]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['pendidikan']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_pekerjaan" class="form-label">Pekerjaan Ayah</label>
                            <input type="text" class="form-control" id="ayah_pekerjaan" name="dataKeluarga[orangTuaWali][ayah][pekerjaan]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['pekerjaan']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_sukuBangsa" class="form-label">Suku Bangsa Ayah</label>
                            <input type="text" class="form-control" id="ayah_sukuBangsa" name="dataKeluarga[orangTuaWali][ayah][sukuBangsa]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['sukuBangsa']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ayah_alamat" class="form-label">Alamat Ayah</label>
                            <input type="text" class="form-control" id="ayah_alamat" name="dataKeluarga[orangTuaWali][ayah][alamat]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['alamat']['stringValue'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5>Ibu</h5>
                        <div class="mb-3">
                            <label for="ibu_nama" class="form-label">Nama Ibu</label>
                            <input type="text" class="form-control" id="ibu_nama" name="dataKeluarga[orangTuaWali][ibu][nama]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['nama']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_tanggalLahir" class="form-label">Tanggal Lahir Ibu</label>
                            <input type="date" class="form-control" id="ibu_tanggalLahir" name="dataKeluarga[orangTuaWali][ibu][tanggalLahir]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['tanggalLahir']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_agama" class="form-label">Agama Ibu</label>
                            <input type="text" class="form-control" id="ibu_agama" name="dataKeluarga[orangTuaWali][ibu][agama]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['agama']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_pendidikan" class="form-label">Pendidikan Ibu</label>
                            <input type="text" class="form-control" id="ibu_pendidikan" name="dataKeluarga[orangTuaWali][ibu][pendidikan]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['pendidikan']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_pekerjaan" class="form-label">Pekerjaan Ibu</label>
                            <input type="text" class="form-control" id="ibu_pekerjaan" name="dataKeluarga[orangTuaWali][ibu][pekerjaan]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['pekerjaan']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_sukuBangsa" class="form-label">Suku Bangsa Ibu</label>
                            <input type="text" class="form-control" id="ibu_sukuBangsa" name="dataKeluarga[orangTuaWali][ibu][sukuBangsa]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['sukuBangsa']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="ibu_alamat" class="form-label">Alamat Ibu</label>
                            <input type="text" class="form-control" id="ibu_alamat" name="dataKeluarga[orangTuaWali][ibu][alamat]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['alamat']['stringValue'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5>Wali (Opsional)</h5>
                        <div class="mb-3">
                            <label for="wali_nama" class="form-label">Nama Wali</label>
                            <input type="text" class="form-control" id="wali_nama" name="dataKeluarga[orangTuaWali][wali][nama]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['nama']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_tanggalLahir" class="form-label">Tanggal Lahir Wali</label>
                            <input type="date" class="form-control" id="wali_tanggalLahir" name="dataKeluarga[orangTuaWali][wali][tanggalLahir]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['tanggalLahir']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_agama" class="form-label">Agama Wali</label>
                            <input type="text" class="form-control" id="wali_agama" name="dataKeluarga[orangTuaWali][wali][agama]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['agama']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_pendidikan" class="form-label">Pendidikan Wali</label>
                            <input type="text" class="form-control" id="wali_pendidikan" name="dataKeluarga[orangTuaWali][wali][pendidikan]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['pendidikan']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_pekerjaan" class="form-label">Pekerjaan Wali</label>
                            <input type="text" class="form-control" id="wali_pekerjaan" name="dataKeluarga[orangTuaWali][wali][pekerjaan]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['pekerjaan']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_sukuBangsa" class="form-label">Suku Bangsa Wali</label>
                            <input type="text" class="form-control" id="wali_sukuBangsa" name="dataKeluarga[orangTuaWali][wali][sukuBangsa]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['sukuBangsa']['stringValue'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="wali_alamat" class="form-label">Alamat Wali</label>
                            <input type="text" class="form-control" id="wali_alamat" name="dataKeluarga[orangTuaWali][wali][alamat]" value="{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['alamat']['stringValue'] ?? '' }}">
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
                    <input type="number" class="form-control" id="anakKe" name="catatanLain[anakKe]" value="{{ $fields['catatanLain']['mapValue']['fields']['anakKe']['integerValue'] ?? '' }}">
                </div>
                <h4>Saudara Kandung</h4>
                <div id="saudaraKandungContainer">
                    @if (isset($fields['catatanLain']['mapValue']['fields']['saudaraKandung']['arrayValue']['values']))
                        @foreach ($fields['catatanLain']['mapValue']['fields']['saudaraKandung']['arrayValue']['values'] as $index => $saudara)
                            <div class="border p-3 mb-2">
                                <h5>Saudara {{ $index + 1 }}</h5>
                                <div class="mb-3">
                                    <label for="saudara_nama_{{ $index }}" class="form-label">Nama Saudara {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="saudara_nama_{{ $index }}" name="catatanLain[saudaraKandung][{{ $index }}][nama]" value="{{ $saudara['mapValue']['fields']['nama']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="saudara_tanggalLahir_{{ $index }}" class="form-label">Tanggal Lahir Saudara {{ $index + 1 }}</label>
                                    <input type="date" class="form-control" id="saudara_tanggalLahir_{{ $index }}" name="catatanLain[saudaraKandung][{{ $index }}][tanggalLahir]" value="{{ $saudara['mapValue']['fields']['tanggalLahir']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="saudara_jenisKelamin_{{ $index }}" class="form-label">Jenis Kelamin Saudara {{ $index + 1 }}</label>
                                    <select class="form-select" id="saudara_jenisKelamin_{{ $index }}" name="catatanLain[saudaraKandung][{{ $index }}][jenisKelamin]">
                                        <option value="Laki-laki" {{ ($saudara['mapValue']['fields']['jenisKelamin']['stringValue'] ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ ($saudara['mapValue']['fields']['jenisKelamin']['stringValue'] ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="saudara_kandungSiri_{{ $index }}" class="form-label">Kandung / Tiri</label>
                                    <input type="text" class="form-control" id="saudara_kandungSiri_{{ $index }}" name="catatanLain[saudaraKandung][{{ $index }}][kandungSiri]" value="{{ $saudara['mapValue']['fields']['kandungSiri']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="saudara_pekerjaanSekolah_{{ $index }}" class="form-label">Pekerjaan / Sekolah</label>
                                    <input type="text" class="form-control" id="saudara_pekerjaanSekolah_{{ $index }}" name="catatanLain[saudaraKandung][{{ $index }}][pekerjaanSekolah]" value="{{ $saudara['mapValue']['fields']['pekerjaanSekolah']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="saudara_tingkat_{{ $index }}" class="form-label">Tingkat</label>
                                    <input type="text" class="form-control" id="saudara_tingkat_{{ $index }}" name="catatanLain[saudaraKandung][{{ $index }}][tingkat]" value="{{ $saudara['mapValue']['fields']['tingkat']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="saudara_kawinBelum_{{ $index }}" class="form-label">Status Pernikahan</label>
                                    <input type="text" class="form-control" id="saudara_kawinBelum_{{ $index }}" name="catatanLain[saudaraKandung][{{ $index }}][kawinBelum]" value="{{ $saudara['mapValue']['fields']['kawinBelum']['stringValue'] ?? '' }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Jika tidak ada saudara kandung, tampilkan satu form kosong --}}
                        <div class="border p-3 mb-2">
                            <h5>Saudara 1</h5>
                            <div class="mb-3">
                                <label for="saudara_nama_0" class="form-label">Nama Saudara 1</label>
                                <input type="text" class="form-control" id="saudara_nama_0" name="catatanLain[saudaraKandung][0][nama]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="saudara_tanggalLahir_0" class="form-label">Tanggal Lahir Saudara 1</label>
                                <input type="date" class="form-control" id="saudara_tanggalLahir_0" name="catatanLain[saudaraKandung][0][tanggalLahir]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="saudara_jenisKelamin_0" class="form-label">Jenis Kelamin Saudara 1</label>
                                <select class="form-select" id="saudara_jenisKelamin_0" name="catatanLain[saudaraKandung][0][jenisKelamin]">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="saudara_kandungSiri_0" class="form-label">Kandung / Tiri</label>
                                <input type="text" class="form-control" id="saudara_kandungSiri_0" name="catatanLain[saudaraKandung][0][kandungSiri]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="saudara_pekerjaanSekolah_0" class="form-label">Pekerjaan / Sekolah</label>
                                <input type="text" class="form-control" id="saudara_pekerjaanSekolah_0" name="catatanLain[saudaraKandung][0][pekerjaanSekolah]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="saudara_tingkat_0" class="form-label">Tingkat</label>
                                <input type="text" class="form-control" id="saudara_tingkat_0" name="catatanLain[saudaraKandung][0][tingkat]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="saudara_kawinBelum_0" class="form-label">Status Pernikahan</label>
                                <input type="text" class="form-control" id="saudara_kawinBelum_0" name="catatanLain[saudaraKandung][0][kawinBelum]" value="">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Keadaan Jasmani</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="tinggiBadan" class="form-label">Tinggi Badan (cm)</label>
                    <input type="number" class="form-control" id="tinggiBadan" name="keadaanJasmani[tinggiBadan]" value="{{ $fields['keadaanJasmani']['mapValue']['fields']['tinggiBadan']['integerValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="beratBadan" class="form-label">Berat Badan (kg)</label>
                    <input type="number" class="form-control" id="beratBadan" name="keadaanJasmani[beratBadan]" value="{{ $fields['keadaanJasmani']['mapValue']['fields']['beratBadan']['integerValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="golonganDarah" class="form-label">Golongan Darah</label>
                    <input type="text" class="form-control" id="golonganDarah" name="keadaanJasmani[golonganDarah]" value="{{ $fields['keadaanJasmani']['mapValue']['fields']['golonganDarah']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="bentukMata" class="form-label">Bentuk Mata</label>
                    <input type="text" class="form-control" id="bentukMata" name="keadaanJasmani[bentukMata]" value="{{ $fields['keadaanJasmani']['mapValue']['fields']['bentukMata']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="bentukMuka" class="form-label">Bentuk Muka</label>
                    <input type="text" class="form-control" id="bentukMuka" name="keadaanJasmani[bentukMuka]" value="{{ $fields['keadaanJasmani']['mapValue']['fields']['bentukMuka']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="rambut" class="form-label">Rambut</label>
                    <input type="text" class="form-control" id="rambut" name="keadaanJasmani[rambut]" value="{{ $fields['keadaanJasmani']['mapValue']['fields']['rambut']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="warnaKulit" class="form-label">Warna Kulit</label>
                    <input type="text" class="form-control" id="warnaKulit" name="keadaanJasmani[warnaKulit]" value="{{ $fields['keadaanJasmani']['mapValue']['fields']['warnaKulit']['stringValue'] ?? '' }}">
                </div>

                <h4>Cacat Tubuh</h4>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="cacatTubuh_yaTidak" name="cacatTubuh[yaTidak]" value="1" {{ ($fields['cacatTubuh']['mapValue']['fields']['yaTidak']['booleanValue'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="cacatTubuh_yaTidak">Ada Cacat Tubuh?</label>
                </div>
                <div class="mb-3">
                    <label for="cacatTubuh_keterangan" class="form-label">Keterangan Cacat Tubuh</label>
                    <input type="text" class="form-control" id="cacatTubuh_keterangan" name="cacatTubuh[keterangan]" value="{{ $fields['cacatTubuh']['mapValue']['fields']['keterangan']['stringValue'] ?? '' }}">
                </div>

                <h4>Kacamata</h4>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="kacamata_yaTidak" name="kacamata[yaTidak]" value="1" {{ ($fields['kacamata']['mapValue']['fields']['yaTidak']['booleanValue'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="kacamata_yaTidak">Menggunakan Kacamata?</label>
                </div>

                <h4>Kelainan Mata (Jika Menggunakan Kacamata)</h4>
                <div class="mb-3">
                    <label for="kelainan_minus" class="form-label">Minus</label>
                    <input type="number" class="form-control" id="kelainan_minus" name="kelainan[minus]" step="0.1" value="{{ $fields['kelainan']['mapValue']['fields']['minus']['integerValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="kelainan_plus" class="form-label">Plus</label>
                    <input type="number" class="form-control" id="kelainan_plus" name="kelainan[plus]" step="0.1" value="{{ $fields['kelainan']['mapValue']['fields']['plus']['integerValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="kelainan_silinder" class="form-label">Silinder</label>
                    <input type="number" class="form-control" id="kelainan_silinder" name="kelainan[silinder]" step="0.1" value="{{ $fields['kelainan']['mapValue']['fields']['silinder']['integerValue'] ?? '' }}">
                </div>

                <div class="mb-3">
                    <label for="sakitSering" class="form-label">Sakit yang Sering Diderita</label>
                    <input type="text" class="form-control" id="sakitSering" name="sakitSering" value="{{ $fields['sakitSering']['stringValue'] ?? '' }}">
                </div>

                <h4>Sakit Keras (Riwayat)</h4>
                <div id="sakitKerasContainer">
                    @if (isset($fields['sakitKeras']['arrayValue']['values']))
                        @foreach ($fields['sakitKeras']['arrayValue']['values'] as $index => $sakit)
                            <div class="border p-3 mb-2">
                                <h5>Sakit {{ $index + 1 }}</h5>
                                <div class="mb-3">
                                    <label for="sakitKeras_jenisPenyakit_{{ $index }}" class="form-label">Jenis Penyakit {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="sakitKeras_jenisPenyakit_{{ $index }}" name="sakitKeras[{{ $index }}][jenisPenyakit]" value="{{ $sakit['mapValue']['fields']['jenisPenyakit']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="sakitKeras_usia_{{ $index }}" class="form-label">Usia Saat Sakit {{ $index + 1 }}</label>
                                    <input type="number" class="form-control" id="sakitKeras_usia_{{ $index }}" name="sakitKeras[{{ $index }}][usia]" value="{{ $sakit['mapValue']['fields']['usia']['integerValue'] ?? '' }}">
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="sakitKeras_opname_{{ $index }}" name="sakitKeras[{{ $index }}][opname]" value="1" {{ ($sakit['mapValue']['fields']['opname']['booleanValue'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sakitKeras_opname_{{ $index }}">Pernah Opname?</label>
                                </div>
                                <div class="mb-3">
                                    <label for="sakitKeras_opnameRS_{{ $index }}" class="form-label">Nama RS (Jika Opname)</label>
                                    <input type="text" class="form-control" id="sakitKeras_opnameRS_{{ $index }}" name="sakitKeras[{{ $index }}][opnameRS]" value="{{ $sakit['mapValue']['fields']['opnameRS']['stringValue'] ?? '' }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Jika tidak ada riwayat sakit keras, tampilkan satu form kosong --}}
                        <div class="border p-3 mb-2">
                            <h5>Sakit 1</h5>
                            <div class="mb-3">
                                <label for="sakitKeras_jenisPenyakit_0" class="form-label">Jenis Penyakit 1</label>
                                <input type="text" class="form-control" id="sakitKeras_jenisPenyakit_0" name="sakitKeras[0][jenisPenyakit]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="sakitKeras_usia_0" class="form-label">Usia Saat Sakit 1</label>
                                <input type="number" class="form-control" id="sakitKeras_usia_0" name="sakitKeras[0][usia]" value="">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="sakitKeras_opname_0" name="sakitKeras[0][opname]" value="1">
                                <label class="form-check-label" for="sakitKeras_opname_0">Pernah Opname?</label>
                            </div>
                            <div class="mb-3">
                                <label for="sakitKeras_opnameRS_0" class="form-label">Nama RS (Jika Opname)</label>
                                <input type="text" class="form-control" id="sakitKeras_opnameRS_0" name="sakitKeras[0][opnameRS]" value="">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Penguasaan Bahasa</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="bahasaIndonesia" class="form-label">Bahasa Indonesia</label>
                    <input type="text" class="form-control" id="bahasaIndonesia" name="penguasaanBahasa[bahasaIndonesia]" value="{{ $fields['penguasaanBahasa']['mapValue']['fields']['bahasaIndonesia']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="bahasaRumah" class="form-label">Bahasa di Rumah</label>
                    <input type="text" class="form-control" id="bahasaRumah" name="penguasaanBahasa[bahasaRumah]" value="{{ $fields['penguasaanBahasa']['mapValue']['fields']['bahasaRumah']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="bahasaDaerah" class="form-label">Bahasa Daerah</label>
                    <select class="form-select" id="bahasaDaerah" name="penguasaanBahasa[bahasaDaerah][]" multiple>
                        @php
                            $selectedBahasaDaerah = array_column($fields['penguasaanBahasa']['mapValue']['fields']['bahasaDaerah']['arrayValue']['values'] ?? [], 'stringValue');
                        @endphp
                        <option value="Jawa" {{ in_array('Jawa', $selectedBahasaDaerah) ? 'selected' : '' }}>Jawa</option>
                        <option value="Sunda" {{ in_array('Sunda', $selectedBahasaDaerah) ? 'selected' : '' }}>Sunda</option>
                        <option value="Batak" {{ in_array('Batak', $selectedBahasaDaerah) ? 'selected' : '' }}>Batak</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa bahasa daerah jika ada.</small>
                </div>
                <div class="mb-3">
                    <label for="bahasaAsing" class="form-label">Bahasa Asing</label>
                    <select class="form-select" id="bahasaAsing" name="penguasaanBahasa[bahasaAsing][]" multiple>
                        @php
                            $selectedBahasaAsing = array_column($fields['penguasaanBahasa']['mapValue']['fields']['bahasaAsing']['arrayValue']['values'] ?? [], 'stringValue');
                        @endphp
                        <option value="Inggris" {{ in_array('Inggris', $selectedBahasaAsing) ? 'selected' : '' }}>Inggris</option>
                        <option value="Mandarin" {{ in_array('Mandarin', $selectedBahasaAsing) ? 'selected' : '' }}>Mandarin</option>
                        <option value="Jepang" {{ in_array('Jepang', $selectedBahasaAsing) ? 'selected' : '' }}>Jepang</option>
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
                    <input type="text" class="form-control" id="hobi" name="hobiKegemaranCitaCita[hobi]" value="{{ $fields['hobiKegemaranCitaCita']['mapValue']['fields']['hobi']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="citaCita" class="form-label">Cita-Cita</label>
                    <input type="text" class="form-control" id="citaCita" name="hobiKegemaranCitaCita[citaCita]" value="{{ $fields['hobiKegemaranCitaCita']['mapValue']['fields']['citaCita']['stringValue'] ?? '' }}">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Keadaan Pendidikan</div>
            <div class="card-body">
                <h4>Pelajaran SD yang Disukai</h4>
                <div id="pelajaranSDDisukaiContainer">
                    @if (isset($fields['keadaanPendidikan']['mapValue']['fields']['pelajaranSDDisukai']['arrayValue']['values']))
                        @foreach ($fields['keadaanPendidikan']['mapValue']['fields']['pelajaranSDDisukai']['arrayValue']['values'] as $index => $pelajaran)
                            <div class="border p-3 mb-2">
                                <h5>Pelajaran {{ $index + 1 }}</h5>
                                <div class="mb-3">
                                    <label for="pelajaranSDDisukai_pelajaran_{{ $index }}" class="form-label">Pelajaran {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="pelajaranSDDisukai_pelajaran_{{ $index }}" name="keadaanPendidikan[pelajaranSDDisukai][{{ $index }}][pelajaran]" value="{{ $pelajaran['mapValue']['fields']['pelajaran']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="pelajaranSDDisukai_alasan_{{ $index }}" class="form-label">Alasan {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="pelajaranSDDisukai_alasan_{{ $index }}" name="keadaanPendidikan[pelajaranSDDisukai][{{ $index }}][alasan]" value="{{ $pelajaran['mapValue']['fields']['alasan']['stringValue'] ?? '' }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="border p-3 mb-2">
                            <h5>Pelajaran 1</h5>
                            <div class="mb-3">
                                <label for="pelajaranSDDisukai_pelajaran_0" class="form-label">Pelajaran 1</label>
                                <input type="text" class="form-control" id="pelajaranSDDisukai_pelajaran_0" name="keadaanPendidikan[pelajaranSDDisukai][0][pelajaran]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="pelajaranSDDisukai_alasan_0" class="form-label">Alasan 1</label>
                                <input type="text" class="form-control" id="pelajaranSDDisukai_alasan_0" name="keadaanPendidikan[pelajaranSDDisukai][0][alasan]" value="">
                            </div>
                        </div>
                    @endif
                </div>

                <h4>Pelajaran SD yang Tidak Disukai</h4>
                <div id="pelajaranSDTidakDisukaiContainer">
                    @if (isset($fields['keadaanPendidikan']['mapValue']['fields']['pelajaranSDTidakDisukai']['arrayValue']['values']))
                        @foreach ($fields['keadaanPendidikan']['mapValue']['fields']['pelajaranSDTidakDisukai']['arrayValue']['values'] as $index => $pelajaran)
                            <div class="border p-3 mb-2">
                                <h5>Pelajaran {{ $index + 1 }}</h5>
                                <div class="mb-3">
                                    <label for="pelajaranSDTidakDisukai_pelajaran_{{ $index }}" class="form-label">Pelajaran {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="pelajaranSDTidakDisukai_pelajaran_{{ $index }}" name="keadaanPendidikan[pelajaranSDTidakDisukai][{{ $index }}][pelajaran]" value="{{ $pelajaran['mapValue']['fields']['pelajaran']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="pelajaranSDTidakDisukai_alasan_{{ $index }}" class="form-label">Alasan {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="pelajaranSDTidakDisukai_alasan_{{ $index }}" name="keadaanPendidikan[pelajaranSDTidakDisukai][{{ $index }}][alasan]" value="{{ $pelajaran['mapValue']['fields']['alasan']['stringValue'] ?? '' }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="border p-3 mb-2">
                            <h5>Pelajaran 1</h5>
                            <div class="mb-3">
                                <label for="pelajaranSDTidakDisukai_pelajaran_0" class="form-label">Pelajaran 1</label>
                                <input type="text" class="form-control" id="pelajaranSDTidakDisukai_pelajaran_0" name="keadaanPendidikan[pelajaranSDTidakDisukai][0][pelajaran]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="pelajaranSDTidakDisukai_alasan_0" class="form-label">Alasan 1</label>
                                <input type="text" class="form-control" id="pelajaranSDTidakDisukai_alasan_0" name="keadaanPendidikan[pelajaranSDTidakDisukai][0][alasan]" value="">
                            </div>
                        </div>
                    @endif
                </div>

                <h4>Prestasi SD</h4>
                <div id="prestasiSDContainer">
                    @if (isset($fields['keadaanPendidikan']['mapValue']['fields']['prestasiSD']['arrayValue']['values']))
                        @foreach ($fields['keadaanPendidikan']['mapValue']['fields']['prestasiSD']['arrayValue']['values'] as $index => $prestasi)
                            <div class="border p-3 mb-2">
                                <h5>Prestasi {{ $index + 1 }}</h5>
                                <div class="mb-3">
                                    <label for="prestasiSD_namaKejuaraan_{{ $index }}" class="form-label">Nama Kejuaraan {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="prestasiSD_namaKejuaraan_{{ $index }}" name="keadaanPendidikan[prestasiSD][{{ $index }}][namaKejuaraan]" value="{{ $prestasi['mapValue']['fields']['namaKejuaraan']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="prestasiSD_tingkat_{{ $index }}" class="form-label">Tingkat {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="prestasiSD_tingkat_{{ $index }}" name="keadaanPendidikan[prestasiSD][{{ $index }}][tingkat]" value="{{ $prestasi['mapValue']['fields']['tingkat']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="prestasiSD_raihanPrestasi_{{ $index }}" class="form-label">Raihan Prestasi {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="prestasiSD_raihanPrestasi_{{ $index }}" name="keadaanPendidikan[prestasiSD][{{ $index }}][raihanPrestasi]" value="{{ $prestasi['mapValue']['fields']['raihanPrestasi']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="prestasiSD_tahunKelas_{{ $index }}" class="form-label">Tahun / Kelas {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="prestasiSD_tahunKelas_{{ $index }}" name="keadaanPendidikan[prestasiSD][{{ $index }}][tahunKelas]" value="{{ $prestasi['mapValue']['fields']['tahunKelas']['stringValue'] ?? '' }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="border p-3 mb-2">
                            <h5>Prestasi 1</h5>
                            <div class="mb-3">
                                <label for="prestasiSD_namaKejuaraan_0" class="form-label">Nama Kejuaraan 1</label>
                                <input type="text" class="form-control" id="prestasiSD_namaKejuaraan_0" name="keadaanPendidikan[prestasiSD][0][namaKejuaraan]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="prestasiSD_tingkat_0" class="form-label">Tingkat 1</label>
                                <input type="text" class="form-control" id="prestasiSD_tingkat_0" name="keadaanPendidikan[prestasiSD][0][tingkat]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="prestasiSD_raihanPrestasi_0" class="form-label">Raihan Prestasi 1</label>
                                <input type="text" class="form-control" id="prestasiSD_raihanPrestasi_0" name="keadaanPendidikan[prestasiSD][0][raihanPrestasi]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="prestasiSD_tahunKelas_0" class="form-label">Tahun / Kelas 1</label>
                                <input type="text" class="form-control" id="prestasiSD_tahunKelas_0" name="keadaanPendidikan[prestasiSD][0][tahunKelas]" value="">
                            </div>
                        </div>
                    @endif
                </div>

                <h4>Masalah Belajar</h4>
                <div class="mb-3">
                    <label for="kegiatanBelajar" class="form-label">Kegiatan Belajar</label>
                    <input type="text" class="form-control" id="kegiatanBelajar" name="masalahBelajar[kegiatanBelajar]" value="{{ $fields['masalahBelajar']['mapValue']['fields']['kegiatanBelajar']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="dilaksanakanSetiap" class="form-label">Dilaksanakan Setiap</label>
                    <select class="form-select" id="dilaksanakanSetiap" name="masalahBelajar[dilaksanakanSetiap][]" multiple>
                        @php
                            $selectedDilaksanakanSetiap = array_column($fields['masalahBelajar']['mapValue']['fields']['dilaksanakanSetiap']['arrayValue']['values'] ?? [], 'stringValue');
                        @endphp
                        <option value="Hari" {{ in_array('Hari', $selectedDilaksanakanSetiap) ? 'selected' : '' }}>Hari</option>
                        <option value="Minggu" {{ in_array('Minggu', $selectedDilaksanakanSetiap) ? 'selected' : '' }}>Minggu</option>
                        <option value="Bulan" {{ in_array('Bulan', $selectedDilaksanakanSetiap) ? 'selected' : '' }}>Bulan</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa opsi jika ada.</small>
                </div>
                <div class="mb-3">
                    <label for="kesulitanBelajar" class="form-label">Kesulitan Belajar</label>
                    <input type="text" class="form-control" id="kesulitanBelajar" name="masalahBelajar[kesulitanBelajar]" value="{{ $fields['masalahBelajar']['mapValue']['fields']['kesulitanBelajar']['stringValue'] ?? '' }}">
                </div>
                <div class="mb-3">
                    <label for="hambatanBelajar" class="form-label">Hambatan Belajar</label>
                    <input type="text" class="form-control" id="hambatanBelajar" name="masalahBelajar[hambatanBelajar]" value="{{ $fields['masalahBelajar']['mapValue']['fields']['hambatanBelajar']['stringValue'] ?? '' }}">
                </div>

                <h4>Prestasi SMP</h4>
                <div id="prestasiSMPContainer">
                    @if (isset($fields['keadaanPendidikan']['mapValue']['fields']['prestasiSMP']['arrayValue']['values']))
                        @foreach ($fields['keadaanPendidikan']['mapValue']['fields']['prestasiSMP']['arrayValue']['values'] as $index => $prestasi)
                            <div class="border p-3 mb-2">
                                <h5>Prestasi {{ $index + 1 }}</h5>
                                <div class="mb-3">
                                    <label for="prestasiSMP_namaKejuaraan_{{ $index }}" class="form-label">Nama Kejuaraan {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="prestasiSMP_namaKejuaraan_{{ $index }}" name="keadaanPendidikan[prestasiSMP][{{ $index }}][namaKejuaraan]" value="{{ $prestasi['mapValue']['fields']['namaKejuaraan']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="prestasiSMP_tingkat_{{ $index }}" class="form-label">Tingkat {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="prestasiSMP_tingkat_{{ $index }}" name="keadaanPendidikan[prestasiSMP][{{ $index }}][tingkat]" value="{{ $prestasi['mapValue']['fields']['tingkat']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="prestasiSMP_raihanPrestasi_{{ $index }}" class="form-label">Raihan Prestasi {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="prestasiSMP_raihanPrestasi_{{ $index }}" name="keadaanPendidikan[prestasiSMP][{{ $index }}][raihanPrestasi]" value="{{ $prestasi['mapValue']['fields']['raihanPrestasi']['stringValue'] ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="prestasiSMP_tahunKelas_{{ $index }}" class="form-label">Tahun / Kelas {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="prestasiSMP_tahunKelas_{{ $index }}" name="keadaanPendidikan[prestasiSMP][{{ $index }}][tahunKelas]" value="{{ $prestasi['mapValue']['fields']['tahunKelas']['stringValue'] ?? '' }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="border p-3 mb-2">
                            <h5>Prestasi 1</h5>
                            <div class="mb-3">
                                <label for="prestasiSMP_namaKejuaraan_0" class="form-label">Nama Kejuaraan 1</label>
                                <input type="text" class="form-control" id="prestasiSMP_namaKejuaraan_0" name="keadaanPendidikan[prestasiSMP][0][namaKejuaraan]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="prestasiSMP_tingkat_0" class="form-label">Tingkat 1</label>
                                <input type="text" class="form-control" id="prestasiSMP_tingkat_0" name="keadaanPendidikan[prestasiSMP][0][tingkat]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="prestasiSMP_raihanPrestasi_0" class="form-label">Raihan Prestasi 1</label>
                                <input type="text" class="form-control" id="prestasiSMP_raihanPrestasi_0" name="keadaanPendidikan[prestasiSMP][0][raihanPrestasi]" value="">
                            </div>
                            <div class="mb-3">
                                <label for="prestasiSMP_tahunKelas_0" class="form-label">Tahun / Kelas 1</label>
                                <input type="text" class="form-control" id="prestasiSMP_tahunKelas_0" name="keadaanPendidikan[prestasiSMP][0][tahunKelas]" value="">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Data Bimbingan</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="aktivitasSetelahSekolah" class="form-label">Aktivitas Setelah Sekolah</label>
                    <select class="form-select" id="aktivitasSetelahSekolah" name="dataBimbingan[aktivitasSetelahSekolah][]" multiple>
                        @php
                            $selectedAktivitasSetelahSekolah = array_column($fields['dataBimbingan']['mapValue']['fields']['aktivitasSetelahSekolah']['arrayValue']['values'] ?? [], 'stringValue');
                        @endphp
                        <option value="Les Tambahan" {{ in_array('Les Tambahan', $selectedAktivitasSetelahSekolah) ? 'selected' : '' }}>Les Tambahan</option>
                        <option value="Kursus" {{ in_array('Kursus', $selectedAktivitasSetelahSekolah) ? 'selected' : '' }}>Kursus</option>
                        <option value="Olahraga" {{ in_array('Olahraga', $selectedAktivitasSetelahSekolah) ? 'selected' : '' }}>Olahraga</option>
                        <option value="Kerja Paruh Waktu" {{ in_array('Kerja Paruh Waktu', $selectedAktivitasSetelahSekolah) ? 'selected' : '' }}>Kerja Paruh Waktu</option>
                    </select>
                    <small class="form-text text-muted">Pilih beberapa aktivitas jika ada.</small>
                </div>
                <div class="mb-3">
                    <label for="citaCitaBimbingan" class="form-label">Cita-Cita (Bimbingan)</label>
                    <input type="text" class="form-control" id="citaCitaBimbingan" name="dataBimbingan[citaCita]" value="{{ $fields['dataBimbingan']['mapValue']['fields']['citaCita']['stringValue'] ?? '' }}">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Update Pengguna</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection