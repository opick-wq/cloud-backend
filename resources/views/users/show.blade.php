@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
    <h1>Detail Pengguna: {{ $user['id'] }}</h1>

    <div class="card mb-3">
        <div class="card-header">Informasi Identitas</div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $user['id'] }}</p>
            <p><strong>Email:</strong> {{ $user['fields']['email']['stringValue'] ?? '-' }}</p>
            <p><strong>Username:</strong> {{ $user['fields']['username']['stringValue'] ?? '-' }}</p>
            <p><strong>Identitas:</strong> {{ json_encode($user['fields']['identitas']['mapValue']['fields'] ?? []) }}</p>
            <p><strong>Jenis Kelamin:</strong> {{ $user['fields']['jenisKelamin']['stringValue'] ?? '-' }}</p>
            <p><strong>Tempat Lahir:</strong> {{ $user['fields']['tempatLahir']['stringValue'] ?? '-' }}</p>
            <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($user['fields']['tanggalLahir']['timestampValue'] ?? '')->format('d M Y') ?? '-' }}</p>
            <p><strong>Agama:</strong> {{ $user['fields']['agama']['stringValue'] ?? '-' }}</p>
            <p><strong>Suku Bangsa:</strong> {{ $user['fields']['sukuBangsa']['stringValue'] ?? '-' }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Informasi Sekolah</div>
        <div class="card-body">
            <p><strong>Tanggal Masuk Sekolah:</strong> {{ \Carbon\Carbon::parse($user['fields']['masukSekolahTanggal']['timestampValue'] ?? '')->format('d M Y') ?? '-' }}</p>
            <p><strong>Asal Sekolah:</strong> {{ $user['fields']['asalSekolah']['stringValue'] ?? '-' }}</p>
            <p><strong>Status Siswa:</strong> {{ $user['fields']['statusSiswa']['stringValue'] ?? '-' }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Informasi Tempat Tinggal</div>
        <div class="card-body">
            <p><strong>Tempat Tinggal:</strong> {{ json_encode($user['fields']['tempatTinggal']['mapValue']['fields'] ?? []) }}</p>
            <p><strong>Alamat Asal:</strong> {{ $user['fields']['alamatAsal']['stringValue'] ?? '-' }}</p>
            <p><strong>Nomor Telepon Asal:</strong> {{ $user['fields']['nomorTelpAsal']['stringValue'] ?? '-' }}</p>
            <p><strong>Daerah Asal:</strong> {{ implode(', ', array_column($user['fields']['daerahAsal']['arrayValue']['values'] ?? [], 'stringValue')) }}</p>
            <p><strong>Alamat Sekarang:</strong> {{ $user['fields']['alamatSekarang']['stringValue'] ?? '-' }}</p>
            <p><strong>Nomor Telepon Sekarang:</strong> {{ $user['fields']['nomorTelpSekarang']['stringValue'] ?? '-' }}</p>
            <p><strong>Daerah Sekarang:</strong> {{ implode(', ', array_column($user['fields']['daerahSekarang']['arrayValue']['values'] ?? [], 'stringValue')) }}</p>
            <p><strong>Jarak Rumah ke Sekolah:</strong> {{ $user['fields']['jarakRumahSekolah']['integerValue'] ?? '-' }} km</p>
            <p><strong>Sarana ke Sekolah:</strong> {{ implode(', ', array_column($user['fields']['saranaKeSekolah']['arrayValue']['values'] ?? [], 'stringValue')) }}</p>
            <p><strong>Tempat Tinggal Saat Ini:</strong> {{ $user['fields']['tempatTinggalDi']['stringValue'] ?? '-' }}</p>
            <p><strong>Tinggal Bersama:</strong> {{ implode(', ', array_column($user['fields']['tinggalBersama']['arrayValue']['values'] ?? [], 'stringValue')) }}</p>
            <p><strong>Rumah Terbuat Dari:</strong> {{ $user['fields']['rumahTerbuatDari']['stringValue'] ?? '-' }}</p>
            <p><strong>Fasilitas Rumah:</strong> {{ implode(', ', array_column($user['fields']['fasilitasRumah']['arrayValue']['values'] ?? [], 'stringValue')) }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Data Keluarga</div>
        <div class="card-body">
            <h4>Orang Tua / Wali</h4>
            <div class="row">
                <div class="col-md-4">
                    <h5>Ayah</h5>
                    <p><strong>Nama:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['nama']['stringValue'] ?? '-' }}</p>
                    <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['tanggalLahir']['timestampValue'] ?? '')->format('d M Y') ?? '-' }}</p>
                    <p><strong>Agama:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['agama']['stringValue'] ?? '-' }}</p>
                    <p><strong>Pendidikan:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['pendidikan']['stringValue'] ?? '-' }}</p>
                    <p><strong>Pekerjaan:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['pekerjaan']['stringValue'] ?? '-' }}</p>
                    <p><strong>Suku Bangsa:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['sukuBangsa']['stringValue'] ?? '-' }}</p>
                    <p><strong>Alamat:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['alamat']['stringValue'] ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <h5>Ibu</h5>
                    <p><strong>Nama:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['nama']['stringValue'] ?? '-' }}</p>
                    <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['tanggalLahir']['timestampValue'] ?? '')->format('d M Y') ?? '-' }}</p>
                    <p><strong>Agama:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['agama']['stringValue'] ?? '-' }}</p>
                    <p><strong>Pendidikan:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['pendidikan']['stringValue'] ?? '-' }}</p>
                    <p><strong>Pekerjaan:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['pekerjaan']['stringValue'] ?? '-' }}</p>
                    <p><strong>Suku Bangsa:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['sukuBangsa']['stringValue'] ?? '-' }}</p>
                    <p><strong>Alamat:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['alamat']['stringValue'] ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <h5>Wali</h5>
                    <p><strong>Nama:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['nama']['stringValue'] ?? '-' }}</p>
                    <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['tanggalLahir']['timestampValue'] ?? '')->format('d M Y') ?? '-' }}</p>
                    <p><strong>Agama:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['agama']['stringValue'] ?? '-' }}</p>
                    <p><strong>Pendidikan:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['pendidikan']['stringValue'] ?? '-' }}</p>
                    <p><strong>Pekerjaan:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['pekerjaan']['stringValue'] ?? '-' }}</p>
                    <p><strong>Suku Bangsa:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['sukuBangsa']['stringValue'] ?? '-' }}</p>
                    <p><strong>Alamat:</strong> {{ $user['fields']['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['wali']['mapValue']['fields']['alamat']['stringValue'] ?? '-' }}</p>
                </div>
            </div>

            <h4>Saudara Kandung</h4>
            @if (isset($user['fields']['catatanLain']['mapValue']['fields']['saudaraKandung']['arrayValue']['values']))
                @foreach ($user['fields']['catatanLain']['mapValue']['fields']['saudaraKandung']['arrayValue']['values'] as $index => $saudara)
                    <div class="border p-3 mb-2">
                        <h5>Saudara {{ $index + 1 }}</h5>
                        <p><strong>Nama:</strong> {{ $saudara['mapValue']['fields']['nama']['stringValue'] ?? '-' }}</p>
                        <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($saudara['mapValue']['fields']['tanggalLahir']['timestampValue'] ?? '')->format('d M Y') ?? '-' }}</p>
                        <p><strong>Jenis Kelamin:</strong> {{ $saudara['mapValue']['fields']['jenisKelamin']['stringValue'] ?? '-' }}</p>
                        <p><strong>Kandung / Tiri:</strong> {{ $saudara['mapValue']['fields']['kandungSiri']['stringValue'] ?? '-' }}</p>
                        <p><strong>Pekerjaan / Sekolah:</strong> {{ $saudara['mapValue']['fields']['pekerjaanSekolah']['stringValue'] ?? '-' }}</p>
                        <p><strong>Tingkat:</strong> {{ $saudara['mapValue']['fields']['tingkat']['stringValue'] ?? '-' }}</p>
                        <p><strong>Status Pernikahan:</strong> {{ $saudara['mapValue']['fields']['kawinBelum']['stringValue'] ?? '-' }}</p>
                    </div>
                @endforeach
            @else
                <p>Tidak ada data saudara kandung.</p>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Keadaan Jasmani</div>
        <div class="card-body">
            <p><strong>Tinggi Badan:</strong> {{ $user['fields']['keadaanJasmani']['mapValue']['fields']['tinggiBadan']['integerValue'] ?? '-' }} cm</p>
            <p><strong>Berat Badan:</strong> {{ $user['fields']['keadaanJasmani']['mapValue']['fields']['beratBadan']['integerValue'] ?? '-' }} kg</p>
            <p><strong>Golongan Darah:</strong> {{ $user['fields']['keadaanJasmani']['mapValue']['fields']['golonganDarah']['stringValue'] ?? '-' }}</p>
            <p><strong>Bentuk Mata:</strong> {{ $user['fields']['keadaanJasmani']['mapValue']['fields']['bentukMata']['stringValue'] ?? '-' }}</p>
            <p><strong>Bentuk Muka:</strong> {{ $user['fields']['keadaanJasmani']['mapValue']['fields']['bentukMuka']['stringValue'] ?? '-' }}</p>
            <p><strong>Rambut:</strong> {{ $user['fields']['keadaanJasmani']['mapValue']['fields']['rambut']['stringValue'] ?? '-' }}</p>
            <p><strong>Warna Kulit:</strong> {{ $user['fields']['keadaanJasmani']['mapValue']['fields']['warnaKulit']['stringValue'] ?? '-' }}</p>

            <h4>Cacat Tubuh</h4>
            <p><strong>Ada Cacat Tubuh?:</strong> {{ ($user['fields']['cacatTubuh']['mapValue']['fields']['yaTidak']['booleanValue'] ?? false) ? 'Ya' : 'Tidak' }}</p>
            <p><strong>Keterangan:</strong> {{ $user['fields']['cacatTubuh']['mapValue']['fields']['keterangan']['stringValue'] ?? '-' }}</p>

            <h4>Kacamata</h4>
            <p><strong>Menggunakan Kacamata?:</strong> {{ ($user['fields']['kacamata']['mapValue']['fields']['yaTidak']['booleanValue'] ?? false) ? 'Ya' : 'Tidak' }}</p>

            <h4>Kelainan Mata</h4>
            <p><strong>Minus:</strong> {{ $user['fields']['kelainan']['mapValue']['fields']['minus']['integerValue'] ?? '-' }}</p>
            <p><strong>Plus:</strong> {{ $user['fields']['kelainan']['mapValue']['fields']['plus']['integerValue'] ?? '-' }}</p>
            <p><strong>Silinder:</strong> {{ $user['fields']['kelainan']['mapValue']['fields']['silinder']['integerValue'] ?? '-' }}</p>

            <p><strong>Sakit yang Sering Diderita:</strong> {{ $user['fields']['sakitSering']['stringValue'] ?? '-' }}</p>

            <h4>Sakit Keras (Riwayat)</h4>
            @if (isset($user['fields']['sakitKeras']['arrayValue']['values']))
                @foreach ($user['fields']['sakitKeras']['arrayValue']['values'] as $index => $sakit)
                    <div class="border p-3 mb-2">
                        <h5>Sakit {{ $index + 1 }}</h5>
                        <p><strong>Jenis Penyakit:</strong> {{ $sakit['mapValue']['fields']['jenisPenyakit']['stringValue'] ?? '-' }}</p>
                        <p><strong>Usia Saat Sakit:</strong> {{ $sakit['mapValue']['fields']['usia']['integerValue'] ?? '-' }}</p>
                        <p><strong>Pernah Opname?:</strong> {{ ($sakit['mapValue']['fields']['opname']['booleanValue'] ?? false) ? 'Ya' : 'Tidak' }}</p>
                        <p><strong>Nama RS (Jika Opname):</strong> {{ $sakit['mapValue']['fields']['opnameRS']['stringValue'] ?? '-' }}</p>
                    </div>
                @endforeach
            @else
                <p>Tidak ada riwayat sakit keras.</p>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Penguasaan Bahasa</div>
        <div class="card-body">
            <p><strong>Bahasa Indonesia:</strong> {{ $user['fields']['penguasaanBahasa']['mapValue']['fields']['bahasaIndonesia']['stringValue'] ?? '-' }}</p>
            <p><strong>Bahasa di Rumah:</strong> {{ $user['fields']['penguasaanBahasa']['mapValue']['fields']['bahasaRumah']['stringValue'] ?? '-' }}</p>
            <p><strong>Bahasa Daerah:</strong> {{ implode(', ', array_column($user['fields']['penguasaanBahasa']['mapValue']['fields']['bahasaDaerah']['arrayValue']['values'] ?? [], 'stringValue')) }}</p>
            <p><strong>Bahasa Asing:</strong> {{ implode(', ', array_column($user['fields']['penguasaanBahasa']['mapValue']['fields']['bahasaAsing']['arrayValue']['values'] ?? [], 'stringValue')) }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Hobi, Kegemaran, Cita-Cita</div>
        <div class="card-body">
            <p><strong>Hobi:</strong> {{ $user['fields']['hobiKegemaranCitaCita']['mapValue']['fields']['hobi']['stringValue'] ?? '-' }}</p>
            <p><strong>Cita-Cita:</strong> {{ $user['fields']['hobiKegemaranCitaCita']['mapValue']['fields']['citaCita']['stringValue'] ?? '-' }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Keadaan Pendidikan</div>
        <div class="card-body">
            <h4>Pelajaran SD yang Disukai</h4>
            @if (isset($user['fields']['keadaanPendidikan']['mapValue']['fields']['pelajaranSDDisukai']['arrayValue']['values']))
                @foreach ($user['fields']['keadaanPendidikan']['mapValue']['fields']['pelajaranSDDisukai']['arrayValue']['values'] as $index => $pelajaran)
                    <div class="border p-3 mb-2">
                        <h5>Pelajaran {{ $index + 1 }}</h5>
                        <p><strong>Pelajaran:</strong> {{ $pelajaran['mapValue']['fields']['pelajaran']['stringValue'] ?? '-' }}</p>
                        <p><strong>Alasan:</strong> {{ $pelajaran['mapValue']['fields']['alasan']['stringValue'] ?? '-' }}</p>
                    </div>
                @endforeach
            @else
                <p>Tidak ada data pelajaran SD yang disukai.</p>
            @endif

            <h4>Pelajaran SD yang Tidak Disukai</h4>
            @if (isset($user['fields']['keadaanPendidikan']['mapValue']['fields']['pelajaranSDTidakDisukai']['arrayValue']['values']))
                @foreach ($user['fields']['keadaanPendidikan']['mapValue']['fields']['pelajaranSDTidakDisukai']['arrayValue']['values'] as $index => $pelajaran)
                    <div class="border p-3 mb-2">
                        <h5>Pelajaran {{ $index + 1 }}</h5>
                        <p><strong>Pelajaran:</strong> {{ $pelajaran['mapValue']['fields']['pelajaran']['stringValue'] ?? '-' }}</p>
                        <p><strong>Alasan:</strong> {{ $pelajaran['mapValue']['fields']['alasan']['stringValue'] ?? '-' }}</p>
                    </div>
                @endforeach
            @else
                <p>Tidak ada data pelajaran SD yang tidak disukai.</p>
            @endif

            <h4>Prestasi SD</h4>
            @if (isset($user['fields']['keadaanPendidikan']['mapValue']['fields']['prestasiSD']['arrayValue']['values']))
                @foreach ($user['fields']['keadaanPendidikan']['mapValue']['fields']['prestasiSD']['arrayValue']['values'] as $index => $prestasi)
                    <div class="border p-3 mb-2">
                        <h5>Prestasi {{ $index + 1 }}</h5>
                        <p><strong>Nama Kejuaraan:</strong> {{ $prestasi['mapValue']['fields']['namaKejuaraan']['stringValue'] ?? '-' }}</p>
                        <p><strong>Tingkat:</strong> {{ $prestasi['mapValue']['fields']['tingkat']['stringValue'] ?? '-' }}</p>
                        <p><strong>Raihan Prestasi:</strong> {{ $prestasi['mapValue']['fields']['raihanPrestasi']['stringValue'] ?? '-' }}</p>
                        <p><strong>Tahun / Kelas:</strong> {{ $prestasi['mapValue']['fields']['tahunKelas']['stringValue'] ?? '-' }}</p>
                    </div>
                @endforeach
            @else
                <p>Tidak ada data prestasi SD.</p>
            @endif

            <h4>Masalah Belajar</h4>
            <p><strong>Kegiatan Belajar:</strong> {{ $user['fields']['masalahBelajar']['mapValue']['fields']['kegiatanBelajar']['stringValue'] ?? '-' }}</p>
            <p><strong>Dilaksanakan Setiap:</strong> {{ implode(', ', array_column($user['fields']['masalahBelajar']['mapValue']['fields']['dilaksanakanSetiap']['arrayValue']['values'] ?? [], 'stringValue')) }}</p>
            <p><strong>Kesulitan Belajar:</strong> {{ $user['fields']['masalahBelajar']['mapValue']['fields']['kesulitanBelajar']['stringValue'] ?? '-' }}</p>
            <p><strong>Hambatan Belajar:</strong> {{ $user['fields']['masalahBelajar']['mapValue']['fields']['hambatanBelajar']['stringValue'] ?? '-' }}</p>

            <h4>Prestasi SMP</h4>
            @if (isset($user['fields']['keadaanPendidikan']['mapValue']['fields']['prestasiSMP']['arrayValue']['values']))
                @foreach ($user['fields']['keadaanPendidikan']['mapValue']['fields']['prestasiSMP']['arrayValue']['values'] as $index => $prestasi)
                    <div class="border p-3 mb-2">
                        <h5>Prestasi {{ $index + 1 }}</h5>
                        <p><strong>Nama Kejuaraan:</strong> {{ $prestasi['mapValue']['fields']['namaKejuaraan']['stringValue'] ?? '-' }}</p>
                        <p><strong>Tingkat:</strong> {{ $prestasi['mapValue']['fields']['tingkat']['stringValue'] ?? '-' }}</p>
                        <p><strong>Raihan Prestasi:</strong> {{ $prestasi['mapValue']['fields']['raihanPrestasi']['stringValue'] ?? '-' }}</p>
                        <p><strong>Tahun / Kelas:</strong> {{ $prestasi['mapValue']['fields']['tahunKelas']['stringValue'] ?? '-' }}</p>
                    </div>
                @endforeach
            @else
                <p>Tidak ada data prestasi SMP.</p>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Data Bimbingan</div>
        <div class="card-body">
            <p><strong>Aktivitas Setelah Sekolah:</strong> {{ implode(', ', array_column($user['fields']['dataBimbingan']['mapValue']['fields']['aktivitasSetelahSekolah']['arrayValue']['values'] ?? [], 'stringValue')) }}</p>
            <p><strong>Cita-Cita:</strong> {{ $user['fields']['dataBimbingan']['mapValue']['fields']['citaCita']['stringValue'] ?? '-' }}</p>
        </div>
    </div>

    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
    <a href="{{ route('users.edit', $user['id']) }}" class="btn btn-warning">Edit Pengguna</a>
@endsection