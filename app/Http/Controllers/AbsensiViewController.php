<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbsensiViewController extends Controller
{
     public function showMyAttendancePage()
    {
        return view('absensi.mahasiswa_info_hari_ini');
    }

    public function showGuruDailyReportPage()
    {
        return view('absensi.guru_harian');
    }

    public function showGuruMonthlyReportPage()
    {
        return view('absensi.guru_bulanan');
    }
}
