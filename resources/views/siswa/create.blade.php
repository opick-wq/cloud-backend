@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Data Siswa</h2>

    <form action="{{ route('siswa.store') }}" method="POST">
        @csrf

        @include('siswa.form')

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
