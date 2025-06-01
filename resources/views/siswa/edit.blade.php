{{-- Assuming you have a layout file, e.g., resources/views/layouts/app.blade.php --}}
@extends('layouts.app') {{-- Or your actual layout file --}}

@section('content')
<div class="container">
    <h1>Edit Data Siswa: {{ $siswa['name'] ?? 'N/A' }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Make sure your route is defined in web.php or api.php --}}
    {{-- For web: Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update'); --}}
    <form action="{{ route('api.siswa.update', $siswa['id']) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- A. IDENTITAS PRIBADI --}}
        <fieldset class="mb-4 p-3 border">
            <legend class="w-auto px-2 h5">A. Identitas Pribadi</legend>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap (sesuai ijazah)</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $siswa['name'] ?? '') }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Nama Panggilan/Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $siswa['username'] ?? '') }}">
                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror" type="radio" name="jenis_kelamin" id="jk_laki" value="Laki-laki" {{ old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jk_laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror" type="radio" name="jenis_kelamin" id="jk_perempuan" value="Perempuan" {{ old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') == 'Perempuan' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jk_perempuan">Perempuan</label>
                        </div>
                    </div>
                    @error('jenis_kelamin') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa['tempat_lahir'] ?? '') }}">
                    @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa['tanggal_lahir'] ?? '') }}">
                    @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                 <div class="col-md-4 mb-3">
                    <label for="agama" class="form-label">Agama</label>
                    <input type="text" class="form-control @error('agama') is-invalid @enderror" id="agama" name="agama" value="{{ old('agama', $siswa['agama'] ?? '') }}">
                    @error('agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="suku_bangsa" class="form-label">Suku Bangsa</label>
                    <input type="text" class="form-control @error('suku_bangsa') is-invalid @enderror" id="suku_bangsa" name="suku_bangsa" value="{{ old('suku_bangsa', $siswa['suku_bangsa'] ?? '') }}">
                    @error('suku_bangsa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="col-md-4 mb-3">
                    <label for="anak_ke" class="form-label">Anak ke-</label>
                    <input type="number" class="form-control @error('anak_ke') is-invalid @enderror" id="anak_ke" name="anak_ke" value="{{ old('anak_ke', $siswa['anak_ke'] ?? '') }}">
                    @error('anak_ke') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="tanggal_masuk" class="form-label">Tanggal Masuk Sekolah Ini</label>
                    <input type="date" class="form-control @error('tanggal_masuk') is-invalid @enderror" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', $siswa['tanggal_masuk'] ?? '') }}">
                    @error('tanggal_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="asal_sekolah" class="form-label">Asal Sekolah (SD/SMP)</label>
                    <input type="text" class="form-control @error('asal_sekolah') is-invalid @enderror" id="asal_sekolah" name="asal_sekolah" value="{{ old('asal_sekolah', $siswa['asal_sekolah'] ?? '') }}">
                    @error('asal_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status Sebagai</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('status_sebagai') is-invalid @enderror" type="radio" name="status_sebagai" id="status_baru" value="Siswa Baru" {{ old('status_sebagai', $siswa['status_sebagai'] ?? '') == 'Siswa Baru' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_baru">Siswa Baru</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('status_sebagai') is-invalid @enderror" type="radio" name="status_sebagai" id="status_pindahan" value="Pindahan" {{ old('status_sebagai', $siswa['status_sebagai'] ?? '') == 'Pindahan' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_pindahan">Pindahan</label>
                        </div>
                    </div>
                    @error('status_sebagai') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
        </fieldset>

        {{-- B. KETERANGAN TEMPAT TINGGAL --}}
        <fieldset class="mb-4 p-3 border">
            <legend class="w-auto px-2 h5">B. Keterangan Tempat Tinggal</legend>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="alamat_asal" class="form-label">Alamat Asal (Sesuai KK)</label>
                    <textarea class="form-control @error('alamat_asal') is-invalid @enderror" id="alamat_asal" name="alamat_asal">{{ old('alamat_asal', $siswa['alamat_asal'] ?? '') }}</textarea>
                    @error('alamat_asal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nomor_telp_hp" class="form-label">No. Telp/HP (Rumah Asal)</label>
                    <input type="text" class="form-control @error('nomor_telp_hp') is-invalid @enderror" id="nomor_telp_hp" name="nomor_telp_hp" value="{{ old('nomor_telp_hp', $siswa['nomor_telp_hp'] ?? '') }}">
                    @error('nomor_telp_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Termasuk Daerah Asal</label>
                @php $termasukDaerahAsalOptions = ['Dalam kota', 'Pinggir kota', 'Luar kota', 'Pinggir sungai', 'Daerah pegunungan']; @endphp
                @foreach($termasukDaerahAsalOptions as $option)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_asal[]" value="{{ $option }}" id="tda_{{ Str::slug($option) }}"
                           {{ in_array($option, old('termasuk_daerah_asal', $siswa['termasuk_daerah_asal'] ?? [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="tda_{{ Str::slug($option) }}">{{ $option }}</label>
                </div>
                @endforeach
                @error('termasuk_daerah_asal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                @error('termasuk_daerah_asal.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
             <hr>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="alamat_sekarang" class="form-label">Alamat Sekarang (Jika Berbeda)</label>
                    <textarea class="form-control @error('alamat_sekarang') is-invalid @enderror" id="alamat_sekarang" name="alamat_sekarang">{{ old('alamat_sekarang', $siswa['alamat_sekarang'] ?? '') }}</textarea>
                    @error('alamat_sekarang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nomor_telp_hp_sekarang" class="form-label">No. Telp/HP (Sekarang)</label>
                    <input type="text" class="form-control @error('nomor_telp_hp_sekarang') is-invalid @enderror" id="nomor_telp_hp_sekarang" name="nomor_telp_hp_sekarang" value="{{ old('nomor_telp_hp_sekarang', $siswa['nomor_telp_hp_sekarang'] ?? '') }}">
                    @error('nomor_telp_hp_sekarang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
             <div class="mb-3">
                <label class="form-label">Termasuk Daerah Sekarang</label>
                @php $termasukDaerahSekarangOptions = ['Dalam kota', 'Pinggir kota', 'Luar kota', 'Pinggir sungai', 'Daerah pegunungan']; @endphp
                @foreach($termasukDaerahSekarangOptions as $option)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="termasuk_daerah_sekarang[]" value="{{ $option }}" id="tds_{{ Str::slug($option) }}"
                           {{ in_array($option, old('termasuk_daerah_sekarang', $siswa['termasuk_daerah_sekarang'] ?? [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="tds_{{ Str::slug($option) }}">{{ $option }}</label>
                </div>
                @endforeach
                @error('termasuk_daerah_sekarang') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                @error('termasuk_daerah_sekarang.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jarak_rumah_sekolah" class="form-label">Jarak Rumah ke Sekolah (meter)</label>
                    <input type="number" class="form-control @error('jarak_rumah_sekolah') is-invalid @enderror" id="jarak_rumah_sekolah" name="jarak_rumah_sekolah" value="{{ old('jarak_rumah_sekolah', $siswa['jarak_rumah_sekolah'] ?? '') }}">
                    @error('jarak_rumah_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alat/Sarana yang Digunakan ke Sekolah</label>
                @php $alatSaranaOptions = ['Jalan kaki', 'Naik sepeda', 'Naik sepeda motor', 'Diantar orang tua', 'Naik taksi/ojek', 'Naik mobil pribadi', 'Lainnya (teks)']; @endphp
                @foreach($alatSaranaOptions as $option)
                <div class="form-check">
                    <input class="form-check-input alat-sarana-checkbox" type="checkbox" name="alat_sarana_ke_sekolah[]" value="{{ $option }}" id="as_{{ Str::slug($option) }}"
                           {{ in_array($option, old('alat_sarana_ke_sekolah', $siswa['alat_sarana_ke_sekolah'] ?? [])) ? 'checked' : '' }}
                           data-lainnya-target="{{ $option === 'Lainnya (teks)' ? '#alat_sarana_ke_sekolah_lainnya_text_wrapper' : '' }}">
                    <label class="form-check-label" for="as_{{ Str::slug($option) }}">{{ $option }}</label>
                </div>
                @endforeach
                <div id="alat_sarana_ke_sekolah_lainnya_text_wrapper" class="mt-2" style="{{ in_array('Lainnya (teks)', old('alat_sarana_ke_sekolah', $siswa['alat_sarana_ke_sekolah'] ?? [])) ? '' : 'display: none;' }}">
                    <input type="text" name="alat_sarana_ke_sekolah_lainnya_text" class="form-control @error('alat_sarana_ke_sekolah_lainnya_text') is-invalid @enderror" placeholder="Sebutkan lainnya" value="{{ old('alat_sarana_ke_sekolah_lainnya_text', $siswa['alat_sarana_ke_sekolah_lainnya_text'] ?? '') }}">
                    @error('alat_sarana_ke_sekolah_lainnya_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @error('alat_sarana_ke_sekolah') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                @error('alat_sarana_ke_sekolah.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Tempat Tinggal</label>
                @php $tempatTinggalOptions = ['Rumah sendiri', 'Rumah dinas', 'Rumah kontrakan', 'Rumah nenek/kakek', 'Kamar kost', 'Lainnya (teks)']; @endphp
                @foreach($tempatTinggalOptions as $option)
                <div class="form-check">
                    <input class="form-check-input tempat-tinggal-radio" type="radio" name="tempat_tinggal" value="{{ $option }}" id="tt_{{ Str::slug($option) }}"
                           {{ old('tempat_tinggal', $siswa['tempat_tinggal'] ?? '') == $option ? 'checked' : '' }}
                           data-lainnya-target="{{ $option === 'Lainnya (teks)' ? '#tempat_tinggal_lainnya_text_wrapper' : '' }}">
                    <label class="form-check-label" for="tt_{{ Str::slug($option) }}">{{ $option }}</label>
                </div>
                @endforeach
                <div id="tempat_tinggal_lainnya_text_wrapper" class="mt-2" style="{{ old('tempat_tinggal', $siswa['tempat_tinggal'] ?? '') === 'Lainnya (teks)' ? '' : 'display: none;' }}">
                    <input type="text" name="tempat_tinggal_lainnya_text" class="form-control @error('tempat_tinggal_lainnya_text') is-invalid @enderror" placeholder="Sebutkan lainnya" value="{{ old('tempat_tinggal_lainnya_text', $siswa['tempat_tinggal_lainnya_text'] ?? '') }}">
                     @error('tempat_tinggal_lainnya_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @error('tempat_tinggal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Tinggal Bersama</label>
                @php $tinggalBersamaOptions = ['Ayah dan ibu kandung', 'Ayah kandung dan ibu tiri', 'Ayah tiri dan ibu kandung', 'Ayah kandung saja', 'Ibu kandung saja', 'Nenek/Kakek', 'Saudara kandung', 'Sendiri', 'Wali (teks)', 'Lainnya (teks)']; @endphp
                @foreach($tinggalBersamaOptions as $option)
                <div class="form-check">
                    <input class="form-check-input tinggal-bersama-checkbox" type="checkbox" name="tinggal_bersama[]" value="{{ $option }}" id="tb_{{ Str::slug($option) }}"
                           {{ in_array($option, old('tinggal_bersama', $siswa['tinggal_bersama'] ?? [])) ? 'checked' : '' }}
                           data-lainnya-target="{{ $option === 'Wali (teks)' ? '#tinggal_bersama_wali_text_wrapper' : ($option === 'Lainnya (teks)' ? '#tinggal_bersama_lainnya_text_wrapper' : '') }}">
                    <label class="form-check-label" for="tb_{{ Str::slug($option) }}">{{ $option }}</label>
                </div>
                @endforeach
                 <div id="tinggal_bersama_wali_text_wrapper" class="mt-2" style="{{ in_array('Wali (teks)', old('tinggal_bersama', $siswa['tinggal_bersama'] ?? [])) ? '' : 'display: none;' }}">
                    <input type="text" name="tinggal_bersama_wali_text" class="form-control @error('tinggal_bersama_wali_text') is-invalid @enderror" placeholder="Sebutkan Wali" value="{{ old('tinggal_bersama_wali_text', $siswa['tinggal_bersama_wali_text'] ?? '') }}">
                    @error('tinggal_bersama_wali_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div id="tinggal_bersama_lainnya_text_wrapper" class="mt-2" style="{{ in_array('Lainnya (teks)', old('tinggal_bersama', $siswa['tinggal_bersama'] ?? [])) ? '' : 'display: none;' }}">
                    <input type="text" name="tinggal_bersama_lainnya_text" class="form-control @error('tinggal_bersama_lainnya_text') is-invalid @enderror" placeholder="Sebutkan lainnya" value="{{ old('tinggal_bersama_lainnya_text', $siswa['tinggal_bersama_lainnya_text'] ?? '') }}">
                    @error('tinggal_bersama_lainnya_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @error('tinggal_bersama') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                @error('tinggal_bersama.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Rumah Terbuat Dari</label>
                @php $rumahTerbuatOptions = ['Tembok beton', 'Setengah kayu', 'Kayu', 'Bambu', 'Lainnya (teks)']; @endphp
                @foreach($rumahTerbuatOptions as $option)
                <div class="form-check">
                    <input class="form-check-input rumah-terbuat-radio" type="radio" name="rumah_terbuat_dari" value="{{ $option }}" id="rtd_{{ Str::slug($option) }}"
                           {{ old('rumah_terbuat_dari', $siswa['rumah_terbuat_dari'] ?? '') == $option ? 'checked' : '' }}
                           data-lainnya-target="{{ $option === 'Lainnya (teks)' ? '#rumah_terbuat_dari_lainnya_text_wrapper' : '' }}">
                    <label class="form-check-label" for="rtd_{{ Str::slug($option) }}">{{ $option }}</label>
                </div>
                @endforeach
                <div id="rumah_terbuat_dari_lainnya_text_wrapper" class="mt-2" style="{{ old('rumah_terbuat_dari', $siswa['rumah_terbuat_dari'] ?? '') === 'Lainnya (teks)' ? '' : 'display: none;' }}">
                    <input type="text" name="rumah_terbuat_dari_lainnya_text" class="form-control @error('rumah_terbuat_dari_lainnya_text') is-invalid @enderror" placeholder="Sebutkan lainnya" value="{{ old('rumah_terbuat_dari_lainnya_text', $siswa['rumah_terbuat_dari_lainnya_text'] ?? '') }}">
                    @error('rumah_terbuat_dari_lainnya_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @error('rumah_terbuat_dari') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Alat Fasilitas yang Dimiliki di Rumah</label>
                @php $alatFasilitasOptions = ['Kamar sendiri', 'Ruang belajar sendiri', 'Perpustakaan keluarga', 'Radio/TV/parabola', 'Ruang tamu', 'Almari pribadi', 'Gitar/piano alat musik', 'Komputer/laptop/LCD', 'Kompor/kompor gas', 'Ruang makan sendiri', 'Almari es', 'Sepeda', 'Sepeda motor', 'Mobil', 'Berlangganan surat kabar/majalah (teks)']; @endphp
                 @foreach($alatFasilitasOptions as $option)
                <div class="form-check">
                    <input class="form-check-input alat-fasilitas-checkbox" type="checkbox" name="alat_fasilitas_dimiliki[]" value="{{ $option }}" id="afd_{{ Str::slug($option) }}"
                           {{ in_array($option, old('alat_fasilitas_dimiliki', $siswa['alat_fasilitas_dimiliki'] ?? [])) ? 'checked' : '' }}
                            data-lainnya-target="{{ $option === 'Berlangganan surat kabar/majalah (teks)' ? '#alat_fasilitas_dimiliki_surat_kabar_text_wrapper' : '' }}">
                    <label class="form-check-label" for="afd_{{ Str::slug($option) }}">{{ $option }}</label>
                </div>
                @endforeach
                <div id="alat_fasilitas_dimiliki_surat_kabar_text_wrapper" class="mt-2" style="{{ in_array('Berlangganan surat kabar/majalah (teks)', old('alat_fasilitas_dimiliki', $siswa['alat_fasilitas_dimiliki'] ?? [])) ? '' : 'display: none;' }}">
                    <input type="text" name="alat_fasilitas_dimiliki_surat_kabar_text" class="form-control @error('alat_fasilitas_dimiliki_surat_kabar_text') is-invalid @enderror" placeholder="Sebutkan surat kabar/majalah" value="{{ old('alat_fasilitas_dimiliki_surat_kabar_text', $siswa['alat_fasilitas_dimiliki_surat_kabar_text'] ?? '') }}">
                    @error('alat_fasilitas_dimiliki_surat_kabar_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @error('alat_fasilitas_dimiliki') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                @error('alat_fasilitas_dimiliki.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </fieldset>

        {{-- C. DATA KELUARGA --}}
        <fieldset class="mb-4 p-3 border">
            <legend class="w-auto px-2 h5">C. Data Keluarga</legend>
            @foreach(['ayah', 'ibu', 'wali'] as $type)
            <div class="mb-3 p-2 border-start border-primary border-3">
                <h6 class="text-capitalize">{{ $type }}</h6>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="data_keluarga_{{ $type }}_nama" class="form-label">Nama {{ $type }}</label>
                        <input type="text" class="form-control @error("data_keluarga.{$type}.nama") is-invalid @enderror" id="data_keluarga_{{ $type }}_nama" name="data_keluarga[{{ $type }}][nama]" value="{{ old("data_keluarga.{$type}.nama", $siswa['data_keluarga'][$type]['nama'] ?? '') }}">
                        @error("data_keluarga.{$type}.nama") <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="data_keluarga_{{ $type }}_tanggal_lahir" class="form-label">Tanggal Lahir {{ $type }}</label>
                        <input type="date" class="form-control @error("data_keluarga.{$type}.tanggal_lahir") is-invalid @enderror" id="data_keluarga_{{ $type }}_tanggal_lahir" name="data_keluarga[{{ $type }}][tanggal_lahir]" value="{{ old("data_keluarga.{$type}.tanggal_lahir", $siswa['data_keluarga'][$type]['tanggal_lahir'] ?? '') }}">
                        @error("data_keluarga.{$type}.tanggal_lahir") <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label for="data_keluarga_{{ $type }}_agama" class="form-label">Agama {{ $type }}</label>
                        <input type="text" class="form-control @error("data_keluarga.{$type}.agama") is-invalid @enderror" id="data_keluarga_{{ $type }}_agama" name="data_keluarga[{{ $type }}][agama]" value="{{ old("data_keluarga.{$type}.agama", $siswa['data_keluarga'][$type]['agama'] ?? '') }}">
                        @error("data_keluarga.{$type}.agama") <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="data_keluarga_{{ $type }}_pendidikan" class="form-label">Pendidikan {{ $type }}</label>
                        <input type="text" class="form-control @error("data_keluarga.{$type}.pendidikan") is-invalid @enderror" id="data_keluarga_{{ $type }}_pendidikan" name="data_keluarga[{{ $type }}][pendidikan]" value="{{ old("data_keluarga.{$type}.pendidikan", $siswa['data_keluarga'][$type]['pendidikan'] ?? '') }}">
                        @error("data_keluarga.{$type}.pendidikan") <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="data_keluarga_{{ $type }}_pekerjaan" class="form-label">Pekerjaan {{ $type }}</label>
                        <input type="text" class="form-control @error("data_keluarga.{$type}.pekerjaan") is-invalid @enderror" id="data_keluarga_{{ $type }}_pekerjaan" name="data_keluarga[{{ $type }}][pekerjaan]" value="{{ old("data_keluarga.{$type}.pekerjaan", $siswa['data_keluarga'][$type]['pekerjaan'] ?? '') }}">
                        @error("data_keluarga.{$type}.pekerjaan") <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-3 mb-2">
                        <label for="data_keluarga_{{ $type }}_suku_bangsa" class="form-label">Suku Bangsa {{ $type }}</label>
                        <input type="text" class="form-control @error("data_keluarga.{$type}.suku_bangsa") is-invalid @enderror" id="data_keluarga_{{ $type }}_suku_bangsa" name="data_keluarga[{{ $type }}][suku_bangsa]" value="{{ old("data_keluarga.{$type}.suku_bangsa", $siswa['data_keluarga'][$type]['suku_bangsa'] ?? '') }}">
                        @error("data_keluarga.{$type}.suku_bangsa") <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-2">
                    <label for="data_keluarga_{{ $type }}_alamat" class="form-label">Alamat {{ $type }}</label>
                    <textarea class="form-control @error("data_keluarga.{$type}.alamat") is-invalid @enderror" id="data_keluarga_{{ $type }}_alamat" name="data_keluarga[{{ $type }}][alamat]">{{ old("data_keluarga.{$type}.alamat", $siswa['data_keluarga'][$type]['alamat'] ?? '') }}</textarea>
                    @error("data_keluarga.{$type}.alamat") <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            @endforeach

            {{-- Saudara Kandung --}}
            <div id="saudara-kandung-wrapper" class="mb-3">
                <h6>Saudara Kandung</h6>
                @php
                    $oldSaudara = old('saudara_kandung', $siswa['saudara_kandung'] ?? []);
                    if (empty($oldSaudara)) $oldSaudara = [['nama' => '', 'tanggal_lahir' => '', 'jenis_kelamin' => '', 'status_hubungan' => '', 'pekerjaan_sekolah' => '', 'tingkat' => '', 'status_perkawinan' => '']]; // Add one empty if none
                @endphp

                @foreach($oldSaudara as $index => $saudara)
                <div class="saudara-kandung-item border p-2 mb-2">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Nama Saudara</label>
                            <input type="text" name="saudara_kandung[{{ $index }}][nama]" class="form-control @error("saudara_kandung.{$index}.nama") is-invalid @enderror" value="{{ $saudara['nama'] ?? '' }}">
                            @error("saudara_kandung.{$index}.nama") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Tgl Lahir Saudara</label>
                            <input type="date" name="saudara_kandung[{{ $index }}][tanggal_lahir]" class="form-control @error("saudara_kandung.{$index}.tanggal_lahir") is-invalid @enderror" value="{{ $saudara['tanggal_lahir'] ?? '' }}">
                             @error("saudara_kandung.{$index}.tanggal_lahir") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Jenis Kelamin Saudara</label>
                            <select name="saudara_kandung[{{ $index }}][jenis_kelamin]" class="form-select @error("saudara_kandung.{$index}.jenis_kelamin") is-invalid @enderror">
                                <option value="">Pilih...</option>
                                <option value="Laki-laki" {{ ($saudara['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ ($saudara['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error("saudara_kandung.{$index}.jenis_kelamin") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                             <label class="form-label">Status Hub.</label>
                            <select name="saudara_kandung[{{ $index }}][status_hubungan]" class="form-select @error("saudara_kandung.{$index}.status_hubungan") is-invalid @enderror">
                                <option value="">Pilih...</option>
                                <option value="Kandung" {{ ($saudara['status_hubungan'] ?? '') == 'Kandung' ? 'selected' : '' }}>Kandung</option>
                                <option value="Siri" {{ ($saudara['status_hubungan'] ?? '') == 'Siri' ? 'selected' : '' }}>Siri</option>
                            </select>
                            @error("saudara_kandung.{$index}.status_hubungan") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="col-md-3 mb-2">
                            <label class="form-label">Pekerjaan/Sekolah</label>
                            <input type="text" name="saudara_kandung[{{ $index }}][pekerjaan_sekolah]" class="form-control @error("saudara_kandung.{$index}.pekerjaan_sekolah") is-invalid @enderror" value="{{ $saudara['pekerjaan_sekolah'] ?? '' }}">
                            @error("saudara_kandung.{$index}.pekerjaan_sekolah") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Tingkat</label>
                            <input type="text" name="saudara_kandung[{{ $index }}][tingkat]" class="form-control @error("saudara_kandung.{$index}.tingkat") is-invalid @enderror" value="{{ $saudara['tingkat'] ?? '' }}">
                            @error("saudara_kandung.{$index}.tingkat") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="col-md-3 mb-2">
                            <label class="form-label">Status Perkawinan</label>
                            <select name="saudara_kandung[{{ $index }}][status_perkawinan]" class="form-select @error("saudara_kandung.{$index}.status_perkawinan") is-invalid @enderror">
                                <option value="">Pilih...</option>
                                <option value="Kawin" {{ ($saudara['status_perkawinan'] ?? '') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                <option value="Belum" {{ ($saudara['status_perkawinan'] ?? '') == 'Belum' ? 'selected' : '' }}>Belum</option>
                            </select>
                            @error("saudara_kandung.{$index}.status_perkawinan") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    @if($index > 0 || count($oldSaudara) > 1)
                    <button type="button" class="btn btn-danger btn-sm remove-saudara-item">Hapus Saudara</button>
                    @endif
                </div>
                @endforeach
            </div>
            <button type="button" id="add-saudara-button" class="btn btn-success btn-sm">Tambah Saudara</button>
        </fieldset>

        {{-- D. KEADAAN JASMANI --}}
        <fieldset class="mb-4 p-3 border">
            <legend class="w-auto px-2 h5">D. Keadaan Jasmani</legend>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="tinggi_badan" class="form-label">Tinggi Badan (cm)</label>
                    <input type="number" class="form-control @error('tinggi_badan') is-invalid @enderror" id="tinggi_badan" name="tinggi_badan" value="{{ old('tinggi_badan', $siswa['tinggi_badan'] ?? '') }}">
                    @error('tinggi_badan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
                    <input type="number" step="0.1" class="form-control @error('berat_badan') is-invalid @enderror" id="berat_badan" name="berat_badan" value="{{ old('berat_badan', $siswa['berat_badan'] ?? '') }}">
                    @error('berat_badan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="golongan_darah" class="form-label">Golongan Darah</label>
                    <select name="golongan_darah" id="golongan_darah" class="form-select @error('golongan_darah') is-invalid @enderror">
                        <option value="">Pilih...</option>
                        @foreach(['A', 'B', 'AB', 'O'] as $gol)
                        <option value="{{ $gol }}" {{ old('golongan_darah', $siswa['golongan_darah'] ?? '') == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                        @endforeach
                    </select>
                    @error('golongan_darah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="col-md-3 mb-3">
                    <label for="rambut" class="form-label">Rambut</label>
                    <select name="rambut" id="rambut" class="form-select @error('rambut') is-invalid @enderror">
                        <option value="">Pilih...</option>
                        @foreach(['Lurus', 'Keriting', 'Bergelombang'] as $item)
                        <option value="{{ $item }}" {{ old('rambut', $siswa['rambut'] ?? '') == $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('rambut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
             <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="bentuk_mata" class="form-label">Bentuk Mata</label>
                    <input type="text" class="form-control @error('bentuk_mata') is-invalid @enderror" id="bentuk_mata" name="bentuk_mata" value="{{ old('bentuk_mata', $siswa['bentuk_mata'] ?? '') }}">
                    @error('bentuk_mata') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="bentuk_muka" class="form-label">Bentuk Muka</label>
                    <input type="text" class="form-control @error('bentuk_muka') is-invalid @enderror" id="bentuk_muka" name="bentuk_muka" value="{{ old('bentuk_muka', $siswa['bentuk_muka'] ?? '') }}">
                    @error('bentuk_muka') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="col-md-4 mb-3">
                    <label for="warna_kulit" class="form-label">Warna Kulit</label>
                    <input type="text" class="form-control @error('warna_kulit') is-invalid @enderror" id="warna_kulit" name="warna_kulit" value="{{ old('warna_kulit', $siswa['warna_kulit'] ?? '') }}">
                    @error('warna_kulit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Memiliki Cacat Tubuh?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input conditional-radio" type="radio" name="memiliki_cacat_tubuh" id="cacat_ya" value="Ya" {{ old('memiliki_cacat_tubuh', $siswa['memiliki_cacat_tubuh'] ?? '') == 'Ya' ? 'checked' : '' }} data-target="#cacat_tubuh_penjelasan_wrapper">
                            <label class="form-check-label" for="cacat_ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input conditional-radio" type="radio" name="memiliki_cacat_tubuh" id="cacat_tidak" value="Tidak" {{ old('memiliki_cacat_tubuh', $siswa['memiliki_cacat_tubuh'] ?? '') == 'Tidak' ? 'checked' : '' }} data-target="#cacat_tubuh_penjelasan_wrapper" data-hide="true">
                            <label class="form-check-label" for="cacat_tidak">Tidak</label>
                        </div>
                    </div>
                    <div id="cacat_tubuh_penjelasan_wrapper" class="mt-2" style="{{ old('memiliki_cacat_tubuh', $siswa['memiliki_cacat_tubuh'] ?? '') == 'Ya' ? '' : 'display:none;' }}">
                        <textarea name="cacat_tubuh_penjelasan" class="form-control @error('cacat_tubuh_penjelasan') is-invalid @enderror" placeholder="Jelaskan cacat tubuh">{{ old('cacat_tubuh_penjelasan', $siswa['cacat_tubuh_penjelasan'] ?? '') }}</textarea>
                        @error('cacat_tubuh_penjelasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @error('memiliki_cacat_tubuh') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                 <div class="col-md-6 mb-3">
                    <label class="form-label">Memakai Kacamata?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input conditional-radio" type="radio" name="memakai_kacamata" id="kacamata_ya" value="Ya" {{ old('memakai_kacamata', $siswa['memakai_kacamata'] ?? '') == 'Ya' ? 'checked' : '' }} data-target="#kacamata_kelainan_wrapper">
                            <label class="form-check-label" for="kacamata_ya">Ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input conditional-radio" type="radio" name="memakai_kacamata" id="kacamata_tidak" value="Tidak" {{ old('memakai_kacamata', $siswa['memakai_kacamata'] ?? '') == 'Tidak' ? 'checked' : '' }} data-target="#kacamata_kelainan_wrapper" data-hide="true">
                            <label class="form-check-label" for="kacamata_tidak">Tidak</label>
                        </div>
                    </div>
                    <div id="kacamata_kelainan_wrapper" class="mt-2" style="{{ old('memakai_kacamata', $siswa['memakai_kacamata'] ?? '') == 'Ya' ? '' : 'display:none;' }}">
                        <input type="text" name="kacamata_kelainan" class="form-control @error('kacamata_kelainan') is-invalid @enderror" placeholder="Minus/Plus/Silinder" value="{{ old('kacamata_kelainan', $siswa['kacamata_kelainan'] ?? '') }}">
                        @error('kacamata_kelainan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     @error('memakai_kacamata') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
             <div class="mb-3">
                <label for="sakit_sering_diderita" class="form-label">Sakit yang Sering Diderita</label>
                <input type="text" class="form-control @error('sakit_sering_diderita') is-invalid @enderror" id="sakit_sering_diderita" name="sakit_sering_diderita" value="{{ old('sakit_sering_diderita', $siswa['sakit_sering_diderita'] ?? '') }}">
                @error('sakit_sering_diderita') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Sakit Keras --}}
            <div id="sakit-keras-wrapper" class="mb-3">
                <h6>Riwayat Sakit Keras (jika ada)</h6>
                @php
                    $oldSakitKeras = old('sakit_keras', $siswa['sakit_keras'] ?? []);
                     if (empty($oldSakitKeras)) $oldSakitKeras = [['jenis_penyakit' => '', 'usia_saat_sakit' => '', 'opname' => '', 'opname_di_rs' => '']]; // Add one empty if none
                @endphp
                @foreach($oldSakitKeras as $index => $sakit)
                <div class="sakit-keras-item border p-2 mb-2">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Jenis Penyakit</label>
                            <input type="text" name="sakit_keras[{{ $index }}][jenis_penyakit]" class="form-control @error("sakit_keras.{$index}.jenis_penyakit") is-invalid @enderror" value="{{ $sakit['jenis_penyakit'] ?? '' }}">
                            @error("sakit_keras.{$index}.jenis_penyakit") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Usia Saat Sakit</label>
                            <input type="number" name="sakit_keras[{{ $index }}][usia_saat_sakit]" class="form-control @error("sakit_keras.{$index}.usia_saat_sakit") is-invalid @enderror" value="{{ $sakit['usia_saat_sakit'] ?? '' }}">
                            @error("sakit_keras.{$index}.usia_saat_sakit") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="col-md-5 mb-2">
                            <label class="form-label">Opname?</label>
                            <select name="sakit_keras[{{ $index }}][opname]" class="form-select conditional-select @error("sakit_keras.{$index}.opname") is-invalid @enderror" data-target="#sakit_keras_{{ $index }}_opname_di_rs_wrapper">
                                <option value="">Pilih...</option>
                                <option value="Ya" {{ ($sakit['opname'] ?? '') == 'Ya' ? 'selected' : '' }}>Ya</option>
                                <option value="Tidak" {{ ($sakit['opname'] ?? '') == 'Tidak' ? 'selected' : '' }} data-hide="true">Tidak</option>
                            </select>
                             @error("sakit_keras.{$index}.opname") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-5 mb-2">
                            <div id="sakit_keras_{{ $index }}_opname_di_rs_wrapper" style="{{ ($sakit['opname'] ?? '') == 'Ya' ? '' : 'display:none;' }}">
                                <label class="form-label">Opname di RS</label>
                                <input type="text" name="sakit_keras[{{ $index }}][opname_di_rs]" class="form-control @error("sakit_keras.{$index}.opname_di_rs") is-invalid @enderror" value="{{ $sakit['opname_di_rs'] ?? '' }}">
                                @error("sakit_keras.{$index}.opname_di_rs") <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="col-md-2 mb-2">
                            @if($index > 0 || count($oldSakitKeras) > 1)
                            <button type="button" class="btn btn-danger btn-sm remove-sakit-keras-item w-100">Hapus</button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" id="add-sakit-keras-button" class="btn btn-success btn-sm">Tambah Riwayat Sakit Keras</button>
        </fieldset>

        {{-- E. PENGUASAAN BAHASA --}}
        <fieldset class="mb-4 p-3 border">
            <legend class="w-auto px-2 h5">E. Penguasaan Bahasa</legend>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kemampuan_bahasa_indonesia" class="form-label">Kemampuan Bahasa Indonesia</label>
                    <select name="kemampuan_bahasa_indonesia" id="kemampuan_bahasa_indonesia" class="form-select @error('kemampuan_bahasa_indonesia') is-invalid @enderror">
                        <option value="">Pilih...</option>
                        @foreach(['Menguasai', 'Cukup Menguasai', 'Kurang Menguasai', 'Tidak Menguasai'] as $level)
                        <option value="{{ $level }}" {{ old('kemampuan_bahasa_indonesia', $siswa['kemampuan_bahasa_indonesia'] ?? '') == $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                    @error('kemampuan_bahasa_indonesia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="bahasa_sehari_hari_dirumah" class="form-label">Bahasa Sehari-hari di Rumah</label>
                    <input type="text" class="form-control @error('bahasa_sehari_hari_dirumah') is-invalid @enderror" id="bahasa_sehari_hari_dirumah" name="bahasa_sehari_hari_dirumah" value="{{ old('bahasa_sehari_hari_dirumah', $siswa['bahasa_sehari_hari_dirumah'] ?? '') }}">
                    @error('bahasa_sehari_hari_dirumah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Bahasa Daerah yang Dikuasai</label>
                @php $bahasaDaerahOptions = ['Bahasa Banjar', 'Bahasa Dayak', 'Bahasa Jawa', 'Bahasa Ambon', 'Lainnya']; @endphp
                @foreach($bahasaDaerahOptions as $option)
                <div class="form-check">
                    <input class="form-check-input bahasa-daerah-checkbox" type="checkbox" name="bahasa_daerah_dikuasai[]" value="{{ $option }}" id="bd_{{ Str::slug($option) }}"
                           {{ in_array($option, old('bahasa_daerah_dikuasai', $siswa['bahasa_daerah_dikuasai'] ?? [])) ? 'checked' : '' }}
                           data-lainnya-target="{{ $option === 'Lainnya' ? '#bahasa_daerah_lainnya_text_wrapper' : '' }}">
                    <label class="form-check-label" for="bd_{{ Str::slug($option) }}">{{ $option }}</label>
                </div>
                @endforeach
                <div id="bahasa_daerah_lainnya_text_wrapper" class="mt-2" style="{{ in_array('Lainnya', old('bahasa_daerah_dikuasai', $siswa['bahasa_daerah_dikuasai'] ?? [])) ? '' : 'display: none;' }}">
                    <input type="text" name="bahasa_daerah_lainnya_text" class="form-control @error('bahasa_daerah_lainnya_text') is-invalid @enderror" placeholder="Sebutkan bahasa daerah lainnya" value="{{ old('bahasa_daerah_lainnya_text', $siswa['bahasa_daerah_lainnya_text'] ?? '') }}">
                    @error('bahasa_daerah_lainnya_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @error('bahasa_daerah_dikuasai') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                 @error('bahasa_daerah_dikuasai.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
             <div class="mb-3">
                <label class="form-label">Bahasa Asing yang Dikuasai</label>
                @php $bahasaAsingOptions = ['Bahasa Inggris', 'Bahasa Arab', 'Bahasa Mandarin', 'Bahasa Jerman', 'Lainnya']; @endphp
                @foreach($bahasaAsingOptions as $option)
                <div class="form-check">
                    <input class="form-check-input bahasa-asing-checkbox" type="checkbox" name="bahasa_asing_dikuasai[]" value="{{ $option }}" id="ba_{{ Str::slug($option) }}"
                           {{ in_array($option, old('bahasa_asing_dikuasai', $siswa['bahasa_asing_dikuasai'] ?? [])) ? 'checked' : '' }}
                           data-lainnya-target="{{ $option === 'Lainnya' ? '#bahasa_asing_lainnya_text_wrapper' : '' }}">
                    <label class="form-check-label" for="ba_{{ Str::slug($option) }}">{{ $option }}</label>
                </div>
                @endforeach
                <div id="bahasa_asing_lainnya_text_wrapper" class="mt-2" style="{{ in_array('Lainnya', old('bahasa_asing_dikuasai', $siswa['bahasa_asing_dikuasai'] ?? [])) ? '' : 'display: none;' }}">
                    <input type="text" name="bahasa_asing_lainnya_text" class="form-control @error('bahasa_asing_lainnya_text') is-invalid @enderror" placeholder="Sebutkan bahasa asing lainnya" value="{{ old('bahasa_asing_lainnya_text', $siswa['bahasa_asing_lainnya_text'] ?? '') }}">
                    @error('bahasa_asing_lainnya_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @error('bahasa_asing_dikuasai') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                @error('bahasa_asing_dikuasai.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </fieldset>

        {{-- F. HOBBY, KEGEMARAN, DAN CITA-CITA --}}
        <fieldset class="mb-4 p-3 border">
            <legend class="w-auto px-2 h5">F. Hobby, Kegemaran, dan Cita-Cita</legend>
            <div class="mb-3">
                <label for="hobby" class="form-label">Hobby/Kegemaran</label>
                <textarea class="form-control @error('hobby') is-invalid @enderror" id="hobby" name="hobby">{{ old('hobby', $siswa['hobby'] ?? '') }}</textarea>
                @error('hobby') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="cita_cita" class="form-label">Cita-cita</label>
                <textarea class="form-control @error('cita_cita') is-invalid @enderror" id="cita_cita" name="cita_cita">{{ old('cita_cita', $siswa['cita_cita'] ?? '') }}</textarea>
                @error('cita_cita') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </fieldset>

        {{-- G. KEADAAN PENDIDIKAN --}}
        <fieldset class="mb-4 p-3 border">
            <legend class="w-auto px-2 h5">G. Keadaan Pendidikan</legend>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pelajaran_disukai_sd" class="form-label">Pelajaran yang Disukai (SD)</label>
                    <input type="text" class="form-control @error('pelajaran_disukai_sd') is-invalid @enderror" id="pelajaran_disukai_sd" name="pelajaran_disukai_sd" value="{{ old('pelajaran_disukai_sd', $siswa['pelajaran_disukai_sd'] ?? '') }}">
                    @error('pelajaran_disukai_sd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="alasan_pelajaran_disukai_sd" class="form-label">Alasan Disukai (SD)</label>
                    <input type="text" class="form-control @error('alasan_pelajaran_disukai_sd') is-invalid @enderror" id="alasan_pelajaran_disukai_sd" name="alasan_pelajaran_disukai_sd" value="{{ old('alasan_pelajaran_disukai_sd', $siswa['alasan_pelajaran_disukai_sd'] ?? '') }}">
                    @error('alasan_pelajaran_disukai_sd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
             <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pelajaran_tidak_disukai_sd" class="form-label">Pelajaran yang Tidak Disukai (SD)</label>
                    <input type="text" class="form-control @error('pelajaran_tidak_disukai_sd') is-invalid @enderror" id="pelajaran_tidak_disukai_sd" name="pelajaran_tidak_disukai_sd" value="{{ old('pelajaran_tidak_disukai_sd', $siswa['pelajaran_tidak_disukai_sd'] ?? '') }}">
                    @error('pelajaran_tidak_disukai_sd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="alasan_pelajaran_tidak_disukai_sd" class="form-label">Alasan Tidak Disukai (SD)</label>
                    <input type="text" class="form-control @error('alasan_pelajaran_tidak_disukai_sd') is-invalid @enderror" id="alasan_pelajaran_tidak_disukai_sd" name="alasan_pelajaran_tidak_disukai_sd" value="{{ old('alasan_pelajaran_tidak_disukai_sd', $siswa['alasan_pelajaran_tidak_disukai_sd'] ?? '') }}">
                    @error('alasan_pelajaran_tidak_disukai_sd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Prestasi SD --}}
            <div id="prestasi-sd-wrapper" class="mb-3">
                <h6>Prestasi yang Pernah Diraih (SD)</h6>
                 @php
                    $oldPrestasiSD = old('prestasi_sd', $siswa['prestasi_sd'] ?? []);
                     if (empty($oldPrestasiSD)) $oldPrestasiSD = [['nama_kejuaraan' => '', 'tingkat' => '', 'raihan_prestasi' => '', 'tahun_kelas' => '']];
                @endphp
                @foreach($oldPrestasiSD as $index => $prestasi)
                <div class="prestasi-sd-item border p-2 mb-2">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Nama Kejuaraan</label>
                            <input type="text" name="prestasi_sd[{{ $index }}][nama_kejuaraan]" class="form-control @error("prestasi_sd.{$index}.nama_kejuaraan") is-invalid @enderror" value="{{ $prestasi['nama_kejuaraan'] ?? '' }}">
                             @error("prestasi_sd.{$index}.nama_kejuaraan") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Tingkat</label>
                            <input type="text" name="prestasi_sd[{{ $index }}][tingkat]" class="form-control @error("prestasi_sd.{$index}.tingkat") is-invalid @enderror" value="{{ $prestasi['tingkat'] ?? '' }}">
                            @error("prestasi_sd.{$index}.tingkat") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="col-md-5 mb-2">
                            <label class="form-label">Raihan Prestasi</label>
                            <input type="text" name="prestasi_sd[{{ $index }}][raihan_prestasi]" class="form-control @error("prestasi_sd.{$index}.raihan_prestasi") is-invalid @enderror" value="{{ $prestasi['raihan_prestasi'] ?? '' }}">
                            @error("prestasi_sd.{$index}.raihan_prestasi") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-5 mb-2">
                            <label class="form-label">Tahun/Kelas</label>
                            <input type="text" name="prestasi_sd[{{ $index }}][tahun_kelas]" class="form-control @error("prestasi_sd.{$index}.tahun_kelas") is-invalid @enderror" value="{{ $prestasi['tahun_kelas'] ?? '' }}">
                             @error("prestasi_sd.{$index}.tahun_kelas") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2 mb-2">
                             @if($index > 0 || count($oldPrestasiSD) > 1)
                            <button type="button" class="btn btn-danger btn-sm remove-prestasi-sd-item w-100">Hapus</button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" id="add-prestasi-sd-button" class="btn btn-success btn-sm">Tambah Prestasi SD</button>
            <hr class="my-3">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kegiatan Belajar di Rumah</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('kegiatan_belajar_dirumah') is-invalid @enderror" type="radio" name="kegiatan_belajar_dirumah" id="belajar_rutin" value="Rutin" {{ old('kegiatan_belajar_dirumah', $siswa['kegiatan_belajar_dirumah'] ?? '') == 'Rutin' ? 'checked' : '' }}>
                            <label class="form-check-label" for="belajar_rutin">Rutin</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('kegiatan_belajar_dirumah') is-invalid @enderror" type="radio" name="kegiatan_belajar_dirumah" id="belajar_tidak" value="Tidak" {{ old('kegiatan_belajar_dirumah', $siswa['kegiatan_belajar_dirumah'] ?? '') == 'Tidak' ? 'checked' : '' }}>
                            <label class="form-check-label" for="belajar_tidak">Tidak</label>
                        </div>
                    </div>
                     @error('kegiatan_belajar_dirumah') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Dilaksanakan Setiap Belajar</label>
                    @php $waktuBelajarOptions = ['Sore', 'Malam', 'Pagi']; @endphp
                    @foreach($waktuBelajarOptions as $option)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="dilaksanakan_setiap_belajar[]" value="{{ $option }}" id="dsb_{{ Str::slug($option) }}"
                               {{ in_array($option, old('dilaksanakan_setiap_belajar', $siswa['dilaksanakan_setiap_belajar'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="dsb_{{ Str::slug($option) }}">{{ $option }}</label>
                    </div>
                    @endforeach
                    @error('dilaksanakan_setiap_belajar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @error('dilaksanakan_setiap_belajar.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="kesulitan_belajar" class="form-label">Kesulitan Belajar</label>
                <textarea class="form-control @error('kesulitan_belajar') is-invalid @enderror" id="kesulitan_belajar" name="kesulitan_belajar">{{ old('kesulitan_belajar', $siswa['kesulitan_belajar'] ?? '') }}</textarea>
                @error('kesulitan_belajar') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="hambatan_belajar" class="form-label">Hambatan Belajar</label>
                <textarea class="form-control @error('hambatan_belajar') is-invalid @enderror" id="hambatan_belajar" name="hambatan_belajar">{{ old('hambatan_belajar', $siswa['hambatan_belajar'] ?? '') }}</textarea>
                @error('hambatan_belajar') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Prestasi SMP --}}
             <div id="prestasi-smp-wrapper" class="mb-3">
                <h6>Prestasi yang Pernah Diraih (SMP)</h6>
                 @php
                    $oldPrestasiSMP = old('prestasi_smp', $siswa['prestasi_smp'] ?? []);
                     if (empty($oldPrestasiSMP)) $oldPrestasiSMP = [['nama_kejuaraan' => '', 'tingkat' => '', 'raihan_prestasi' => '', 'tahun_kelas' => '']];
                @endphp
                @foreach($oldPrestasiSMP as $index => $prestasi)
                <div class="prestasi-smp-item border p-2 mb-2">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Nama Kejuaraan</label>
                            <input type="text" name="prestasi_smp[{{ $index }}][nama_kejuaraan]" class="form-control @error("prestasi_smp.{$index}.nama_kejuaraan") is-invalid @enderror" value="{{ $prestasi['nama_kejuaraan'] ?? '' }}">
                            @error("prestasi_smp.{$index}.nama_kejuaraan") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Tingkat</label>
                            <input type="text" name="prestasi_smp[{{ $index }}][tingkat]" class="form-control @error("prestasi_smp.{$index}.tingkat") is-invalid @enderror" value="{{ $prestasi['tingkat'] ?? '' }}">
                             @error("prestasi_smp.{$index}.tingkat") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                     <div class="row align-items-end">
                        <div class="col-md-5 mb-2">
                            <label class="form-label">Raihan Prestasi</label>
                            <input type="text" name="prestasi_smp[{{ $index }}][raihan_prestasi]" class="form-control @error("prestasi_smp.{$index}.raihan_prestasi") is-invalid @enderror" value="{{ $prestasi['raihan_prestasi'] ?? '' }}">
                            @error("prestasi_smp.{$index}.raihan_prestasi") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-5 mb-2">
                            <label class="form-label">Tahun/Kelas</label>
                            <input type="text" name="prestasi_smp[{{ $index }}][tahun_kelas]" class="form-control @error("prestasi_smp.{$index}.tahun_kelas") is-invalid @enderror" value="{{ $prestasi['tahun_kelas'] ?? '' }}">
                            @error("prestasi_smp.{$index}.tahun_kelas") <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2 mb-2">
                            @if($index > 0 || count($oldPrestasiSMP) > 1)
                            <button type="button" class="btn btn-danger btn-sm remove-prestasi-smp-item w-100">Hapus</button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" id="add-prestasi-smp-button" class="btn btn-success btn-sm">Tambah Prestasi SMP</button>

        </fieldset>

        <div class="mt-4 mb-5">
            <button type="submit" class="btn btn-primary">Update Data Siswa</button>
            <a href="{{ route('api.siswa.index') }}" class="btn btn-secondary">Kembali</a> {{-- Adjust route as needed --}}
        </div>
    </form>
</div>

{{-- Template for Saudara Kandung --}}
<template id="saudara-kandung-template">
    <div class="saudara-kandung-item border p-2 mb-2">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label">Nama Saudara</label>
                <input type="text" name="saudara_kandung[__INDEX__][nama]" class="form-control" value="">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Tgl Lahir Saudara</label>
                <input type="date" name="saudara_kandung[__INDEX__][tanggal_lahir]" class="form-control" value="">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Jenis Kelamin Saudara</label>
                <select name="saudara_kandung[__INDEX__][jenis_kelamin]" class="form-select">
                    <option value="">Pilih...</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-2">
                 <label class="form-label">Status Hub.</label>
                <select name="saudara_kandung[__INDEX__][status_hubungan]" class="form-select">
                    <option value="">Pilih...</option>
                    <option value="Kandung">Kandung</option>
                    <option value="Siri">Siri</option>
                </select>
            </div>
             <div class="col-md-3 mb-2">
                <label class="form-label">Pekerjaan/Sekolah</label>
                <input type="text" name="saudara_kandung[__INDEX__][pekerjaan_sekolah]" class="form-control" value="">
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Tingkat</label>
                <input type="text" name="saudara_kandung[__INDEX__][tingkat]" class="form-control" value="">
            </div>
             <div class="col-md-3 mb-2">
                <label class="form-label">Status Perkawinan</label>
                <select name="saudara_kandung[__INDEX__][status_perkawinan]" class="form-select">
                    <option value="">Pilih...</option>
                    <option value="Kawin">Kawin</option>
                    <option value="Belum">Belum</option>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-danger btn-sm remove-saudara-item">Hapus Saudara</button>
    </div>
</template>

{{-- Template for Sakit Keras --}}
<template id="sakit-keras-template">
    <div class="sakit-keras-item border p-2 mb-2">
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Jenis Penyakit</label>
                <input type="text" name="sakit_keras[__INDEX__][jenis_penyakit]" class="form-control" value="">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Usia Saat Sakit</label>
                <input type="number" name="sakit_keras[__INDEX__][usia_saat_sakit]" class="form-control" value="">
            </div>
        </div>
        <div class="row align-items-end">
            <div class="col-md-5 mb-2">
                <label class="form-label">Opname?</label>
                <select name="sakit_keras[__INDEX__][opname]" class="form-select conditional-select" data-target="#sakit_keras___INDEX___opname_di_rs_wrapper">
                    <option value="">Pilih...</option>
                    <option value="Ya">Ya</option>
                    <option value="Tidak" data-hide="true">Tidak</option>
                </select>
            </div>
            <div class="col-md-5 mb-2">
                 <div id="sakit_keras___INDEX___opname_di_rs_wrapper" style="display:none;">
                    <label class="form-label">Opname di RS</label>
                    <input type="text" name="sakit_keras[__INDEX__][opname_di_rs]" class="form-control" value="">
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <button type="button" class="btn btn-danger btn-sm remove-sakit-keras-item w-100">Hapus</button>
            </div>
        </div>
    </div>
</template>

{{-- Template for Prestasi (SD/SMP) --}}
<template id="prestasi-template">
    <div class="prestasi-item border p-2 mb-2"> {{-- Generic class, specific class will be added by JS --}}
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Nama Kejuaraan</label>
                <input type="text" name="__NAME_PREFIX__[__INDEX__][nama_kejuaraan]" class="form-control" value="">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Tingkat</label>
                <input type="text" name="__NAME_PREFIX__[__INDEX__][tingkat]" class="form-control" value="">
            </div>
        </div>
        <div class="row align-items-end">
            <div class="col-md-5 mb-2">
                <label class="form-label">Raihan Prestasi</label>
                <input type="text" name="__NAME_PREFIX__[__INDEX__][raihan_prestasi]" class="form-control" value="">
            </div>
            <div class="col-md-5 mb-2">
                <label class="form-label">Tahun/Kelas</label>
                <input type="text" name="__NAME_PREFIX__[__INDEX__][tahun_kelas]" class="form-control" value="">
            </div>
            <div class="col-md-2 mb-2">
                <button type="button" class="btn btn-danger btn-sm remove-prestasi-item w-100">Hapus</button> {{-- Generic class --}}
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts') {{-- Or use @section('scripts') if your layout supports it --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Generic Repeater Function ---
    function initializeRepeater(options) {
        const addButton = document.getElementById(options.addButtonId);
        const wrapper = document.getElementById(options.wrapperId);
        const template = document.getElementById(options.templateId).innerHTML;
        let itemIndex = wrapper.querySelectorAll(options.itemClass).length;

        if (itemIndex === 0 && options.addDefaultIfEmpty) { // Add one if empty on load
            const defaultItemHtml = template.replace(/__INDEX__/g, 0).replace(/__NAME_PREFIX__/g, options.namePrefix);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = defaultItemHtml;
            // Remove "remove" button for the first item if it's the only one
            const firstRemoveButton = tempDiv.querySelector(options.removeButtonClass);
            if (firstRemoveButton) firstRemoveButton.remove();
            
            wrapper.insertAdjacentHTML('beforeend', tempDiv.innerHTML);
            itemIndex++;
            bindConditionalLogic(wrapper.lastElementChild); // Bind logic to newly added default item
        }


        addButton.addEventListener('click', function () {
            const newItemHtml = template.replace(/__INDEX__/g, itemIndex).replace(/__NAME_PREFIX__/g, options.namePrefix);
            wrapper.insertAdjacentHTML('beforeend', newItemHtml);
            bindConditionalLogic(wrapper.lastElementChild); // Bind logic to newly added item
            itemIndex++;
        });

        wrapper.addEventListener('click', function (e) {
            if (e.target && e.target.matches(options.removeButtonClass)) {
                e.target.closest(options.itemClass).remove();
                // Optional: re-index items if needed, but usually backend handles sparse arrays
            }
        });
    }

    initializeRepeater({
        addButtonId: 'add-saudara-button',
        wrapperId: 'saudara-kandung-wrapper',
        templateId: 'saudara-kandung-template',
        itemClass: '.saudara-kandung-item',
        removeButtonClass: '.remove-saudara-item',
        addDefaultIfEmpty: {{ count(old('saudara_kandung', $siswa['saudara_kandung'] ?? [])) == 0 ? 'true' : 'false' }} // Add if empty
    });

    initializeRepeater({
        addButtonId: 'add-sakit-keras-button',
        wrapperId: 'sakit-keras-wrapper',
        templateId: 'sakit-keras-template',
        itemClass: '.sakit-keras-item',
        removeButtonClass: '.remove-sakit-keras-item',
        addDefaultIfEmpty: {{ count(old('sakit_keras', $siswa['sakit_keras'] ?? [])) == 0 ? 'true' : 'false' }}
    });

    initializeRepeater({
        addButtonId: 'add-prestasi-sd-button',
        wrapperId: 'prestasi-sd-wrapper',
        templateId: 'prestasi-template', // Generic template
        itemClass: '.prestasi-sd-item', // Specific class for this instance
        removeButtonClass: '.remove-prestasi-item', // Generic remove class
        namePrefix: 'prestasi_sd', // For replacing __NAME_PREFIX__
        addDefaultIfEmpty: {{ count(old('prestasi_sd', $siswa['prestasi_sd'] ?? [])) == 0 ? 'true' : 'false' }}
    });
     // Add specific class to template items for Prestasi SD
    document.querySelectorAll('#prestasi-sd-wrapper .prestasi-item').forEach(el => el.classList.add('prestasi-sd-item'));


    initializeRepeater({
        addButtonId: 'add-prestasi-smp-button',
        wrapperId: 'prestasi-smp-wrapper',
        templateId: 'prestasi-template', // Generic template
        itemClass: '.prestasi-smp-item', // Specific class for this instance
        removeButtonClass: '.remove-prestasi-item', // Generic remove class
        namePrefix: 'prestasi_smp', // For replacing __NAME_PREFIX__
        addDefaultIfEmpty: {{ count(old('prestasi_smp', $siswa['prestasi_smp'] ?? [])) == 0 ? 'true' : 'false' }}
    });
    // Add specific class to template items for Prestasi SMP
    document.querySelectorAll('#prestasi-smp-wrapper .prestasi-item').forEach(el => el.classList.add('prestasi-smp-item'));


    // --- Conditional Logic for "Lainnya" and dependent fields ---
    function bindConditionalLogic(parentElement) {
        // For checkboxes triggering a text field
        parentElement.querySelectorAll('.alat-sarana-checkbox, .tinggal-bersama-checkbox, .alat-fasilitas-checkbox, .bahasa-daerah-checkbox, .bahasa-asing-checkbox').forEach(checkbox => {
            const targetSelector = checkbox.dataset.lainnyaTarget;
            if (targetSelector) {
                const targetWrapper = document.querySelector(targetSelector); // Search in whole document for initial load
                if (targetWrapper) {
                    checkbox.addEventListener('change', function() {
                        targetWrapper.style.display = this.checked ? '' : 'none';
                        if (!this.checked) {
                            const input = targetWrapper.querySelector('input[type="text"]');
                            if (input) input.value = ''; // Clear text if unchecked
                        }
                    });
                    // Initial check
                    // checkbox.dispatchEvent(new Event('change')); // Already handled by Blade rendering style
                }
            }
        });

        // For radio buttons triggering a text field or other elements
        parentElement.querySelectorAll('.tempat-tinggal-radio, .rumah-terbuat-radio, .conditional-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const groupName = this.name;
                document.querySelectorAll(`input[name="${groupName}"]`).forEach(rb => {
                    const targetSelector = rb.dataset.target || rb.dataset.lainnyaTarget;
                    if (targetSelector) {
                         // Query selector might need to be relative to the form or a specific container if IDs are not unique enough
                        const targetWrapper = document.querySelector(targetSelector);
                        if(targetWrapper){
                            if (rb.checked) {
                                targetWrapper.style.display = rb.dataset.hide === 'true' ? 'none' : '';
                                if (rb.dataset.hide === 'true') {
                                     const input = targetWrapper.querySelector('input[type="text"], textarea');
                                     if (input) input.value = '';
                                }
                            } else if (!this.checked && targetWrapper.style.display !== 'none' && rb.dataset.hide !== 'true') {
                                // If another radio in the same group is selected, and this one was showing its target, hide it
                                // (This logic is a bit tricky if multiple radios control same element, ensure unique targets or adjust)
                                // For current setup, each radio group controls distinct "lainnya" or explanation fields.
                            }
                        }
                    }
                });
            });
             // Initial check for radios that are already checked on load
            // if(radio.checked) radio.dispatchEvent(new Event('change')); // Already handled by Blade
        });

        // For select dropdowns triggering other elements (e.g., Sakit Keras Opname)
        parentElement.querySelectorAll('.conditional-select').forEach(select => {
            const targetSelector = select.dataset.target;
            if (targetSelector) {
                const targetWrapper = parentElement.querySelector(targetSelector.replace('__INDEX__', select.name.match(/\[(\d+)\]/)[1])); // Make target unique for repeaters
                if (targetWrapper) {
                     select.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        targetWrapper.style.display = selectedOption.dataset.hide === 'true' ? 'none' : '';
                        if (selectedOption.dataset.hide === 'true') {
                            const input = targetWrapper.querySelector('input[type="text"]');
                            if (input) input.value = '';
                        }
                    });
                    // Initial check
                    // select.dispatchEvent(new Event('change')); // Already handled by Blade
                }
            }
        });
    }

    bindConditionalLogic(document); // Bind to all existing elements on page load

});
</script>
@endpush