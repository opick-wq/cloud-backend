<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FirebaseService;

class DetailSiswaController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
    $response = $this->firebase->getAll('Users');

    $detailSiswaList = [];

    if (isset($response['documents'])) {
        foreach ($response['documents'] as $doc) {
            $fields = $doc['fields'];
            $detailSiswaList[] = [
                'id' => basename($doc['name']),
                'alamat_asal' => $fields['alamat_asal']['stringValue'] ?? '',
                'nomor_telp_hp' => $fields['nomor_telp_hp']['stringValue'] ?? '',
                'termasuk_daerah_asal' => $this->formatCheckboxValue($fields['termasuk_daerah_asal']['arrayValue']['values'] ?? []),
                'alamat_sekarang' => $fields['alamat_sekarang']['stringValue'] ?? '',
                'nomor_telp_hp_sekarang' => $fields['nomor_telp_hp_sekarang']['stringValue'] ?? '',
                'termasuk_daerah_sekarang' => $this->formatCheckboxValue($fields['termasuk_daerah_sekarang']['arrayValue']['values'] ?? []),
                'jarak_rumah_sekolah' => $fields['jarak_rumah_sekolah']['integerValue'] ?? null,
                'alat_sarana_ke_sekolah' => $this->formatCheckboxValue($fields['alat_sarana_ke_sekolah']['arrayValue']['values'] ?? []),
                'tempat_tinggal' => $fields['tempat_tinggal']['stringValue'] ?? '',
                'tinggal_bersama' => $this->formatCheckboxValue($fields['tinggal_bersama']['arrayValue']['values'] ?? []),
                'rumah_terbuat_dari' => $fields['rumah_terbuat_dari']['stringValue'] ?? '',
                'alat_fasilitas_dimiliki' => $this->formatCheckboxValue($fields['alat_fasilitas_dimiliki']['arrayValue']['values'] ?? []),
            ];
        }
    }

    return response()->json($detailSiswaList);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'alamat_asal' => 'nullable|string',
            'nomor_telp_hp' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/',
            'termasuk_daerah_asal' => 'nullable|array',
            'termasuk_daerah_asal.*' => 'in:Dalam kota,Pinggir kota,Luar kota,Pinggir sungai,Daerah pegunungan',
            'alamat_sekarang' => 'nullable|string',
            'nomor_telp_hp_sekarang' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/',
            'termasuk_daerah_sekarang' => 'nullable|array',
            'termasuk_daerah_sekarang.*' => 'in:Dalam kota,Pinggir kota,Luar kota,Pinggir sungai,Daerah pegunungan',
            'jarak_rumah_sekolah' => 'nullable|integer',
            'alat_sarana_ke_sekolah' => 'nullable|array',
            'alat_sarana_ke_sekolah.*' => 'in:Jalan kaki,Naik sepeda,Naik sepeda motor,Diantar orang tua,Naik taksi/ojek,Naik mobil pribadi,Lainnya (teks)',
            'tempat_tinggal' => 'nullable|string|in:Rumah sendiri,Rumah dinas,Rumah kontrakan,Rumah nenek/kakek,Kamar kost,Lainnya (teks)',
            'tinggal_bersama' => 'nullable|array',
            'tinggal_bersama.*' => 'in:Ayah dan ibu kandung,Ayah kandung dan ibu tiri,Ayah tiri dan ibu kandung,Ayah kandung saja,Ibu kandung saja,Nenek/Kakek,Saudara kandung,Sendiri,Wali (teks),Lainnya (teks)',
            'rumah_terbuat_dari' => 'nullable|string|in:Tembok beton,Setengah kayu,Kayu,Bambu,Lainnya (teks)',
            'alat_fasilitas_dimiliki' => 'nullable|array',
            'alat_fasilitas_dimiliki.*' => 'in:Kamar sendiri,Ruang belajar sendiri,Perpustakaan keluarga,Radio/TV/parabola,Ruang tamu,Almari pribadi,Gitar/piano alat musik,Komputer/laptop/LCD,Kompor/kompor gas,Ruang makan sendiri,Almari es,Sepeda,Sepeda motor,Mobil,Berlangganan surat kabar/majalah (teks)',
        ]);

        $id = (string) Str::uuid();

        $this->firebase->create(array_merge(['id' => $id], $data), 'Users');

        return response()->json(['message' => 'Detail data siswa berhasil ditambahkan'], 201);
    }

    public function show($id)
    {
        $doc = $this->firebase->get($id, 'DetailSiswa');

        if (!isset($doc['fields'])) {
            return response()->json(['message' => 'Detail siswa tidak ditemukan'], 404);
        }

        $fields = $doc['fields'];

        return response()->json([
            'id' => $id,
            'alamat_asal' => $fields['alamat_asal']['stringValue'] ?? '',
            'nomor_telp_hp' => $fields['nomor_telp_hp']['stringValue'] ?? '',
            'termasuk_daerah_asal' => $this->formatCheckboxValue($fields['termasuk_daerah_asal']['arrayValue']['values'] ?? []),
            'alamat_sekarang' => $fields['alamat_sekarang']['stringValue'] ?? '',
            'nomor_telp_hp_sekarang' => $fields['nomor_telp_hp_sekarang']['stringValue'] ?? '',
            'termasuk_daerah_sekarang' => $this->formatCheckboxValue($fields['termasuk_daerah_sekarang']['arrayValue']['values'] ?? []),
            'jarak_rumah_sekolah' => $fields['jarak_rumah_sekolah']['integerValue'] ?? null,
            'alat_sarana_ke_sekolah' => $this->formatCheckboxValue($fields['alat_sarana_ke_sekolah']['arrayValue']['values'] ?? []),
            'tempat_tinggal' => $fields['tempat_tinggal']['stringValue'] ?? '',
            'tinggal_bersama' => $this->formatCheckboxValue($fields['tinggal_bersama']['arrayValue']['values'] ?? []),
            'rumah_terbuat_dari' => $fields['rumah_terbuat_dari']['stringValue'] ?? '',
            'alat_fasilitas_dimiliki' => $this->formatCheckboxValue($fields['alat_fasilitas_dimiliki']['arrayValue']['values'] ?? []),
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'alamat_asal' => 'nullable|string',
            'nomor_telp_hp' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/',
            'termasuk_daerah_asal' => 'nullable|array',
            'termasuk_daerah_asal.*' => 'in:Dalam kota,Pinggir kota,Luar kota,Pinggir sungai,Daerah pegunungan',
            'alamat_sekarang' => 'nullable|string',
            'nomor_telp_hp_sekarang' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/',
            'termasuk_daerah_sekarang' => 'nullable|array',
            'termasuk_daerah_sekarang.*' => 'in:Dalam kota,Pinggir kota,Luar kota,Pinggir sungai,Daerah pegunungan',
            'jarak_rumah_sekolah' => 'nullable|integer',
            'alat_sarana_ke_sekolah' => 'nullable|array',
            'alat_sarana_ke_sekolah.*' => 'in:Jalan kaki,Naik sepeda,Naik sepeda motor,Diantar orang tua,Naik taksi/ojek,Naik mobil pribadi,Lainnya (teks)',
            'tempat_tinggal' => 'nullable|string|in:Rumah sendiri,Rumah dinas,Rumah kontrakan,Rumah nenek/kakek,Kamar kost,Lainnya (teks)',
            'tinggal_bersama' => 'nullable|array',
            'tinggal_bersama.*' => 'in:Ayah dan ibu kandung,Ayah kandung dan ibu tiri,Ayah tiri dan ibu kandung,Ayah kandung saja,Ibu kandung saja,Nenek/Kakek,Saudara kandung,Sendiri,Wali (teks),Lainnya (teks)',
            'rumah_terbuat_dari' => 'nullable|string|in:Tembok beton,Setengah kayu,Kayu,Bambu,Lainnya (teks)',
            'alat_fasilitas_dimiliki' => 'nullable|array',
            'alat_fasilitas_dimiliki.*' => 'in:Kamar sendiri,Ruang belajar sendiri,Perpustakaan keluarga,Radio/TV/parabola,Ruang tamu,Almari pribadi,Gitar/piano alat musik,Komputer/laptop/LCD,Kompor/kompor gas,Ruang makan sendiri,Almari es,Sepeda,Sepeda motor,Mobil,Berlangganan surat kabar/majalah (teks)',
        ]);

         $this->firebase->update($id, $data, 'Users');

        return response()->json(['message' => 'Detail data siswa berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $this->firebase->delete($id, 'DetailSiswa');

        return response()->json(['message' => 'Detail data siswa berhasil dihapus']);
    }

    protected function formatCheckboxValue(array $values): array
    {
        $formattedValues = [];
        foreach ($values as $value) {
            $formattedValues[] = $value['stringValue'] ?? null;
        }
        return $formattedValues;
    }

    protected function prepareFirebaseData(array $data): array
    {
        $firebaseData = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $stringValues = [];
                foreach ($value as $item) {
                    $stringValues[] = ['stringValue' => $item];
                }
                $firebaseData[$key] = ['arrayValue' => ['values' => $stringValues]];
            } elseif (is_int($value)) {
                $firebaseData[$key] = ['integerValue' => (string) $value];
            } else {
                $firebaseData[$key] = ['stringValue' => $value];
            }
        }
        return $firebaseData;
    }
}