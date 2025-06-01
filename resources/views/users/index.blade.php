@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content')
    <h1>Daftar Pengguna</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Tambah Pengguna</a>

    @if (isset($users['documents']) && count($users['documents']) > 0)
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Nama Ayah</th>
                    <th>Nama Ibu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users['documents'] as $user)
                    @php
                        $id = basename($user['name']);
                        $fields = $user['fields'];
                    @endphp
                    <tr>
                        <td>{{ $id }}</td>
                        <td>{{ $fields['email']['stringValue'] ?? '-' }}</td>
                        <td>{{ $fields['username']['stringValue'] ?? '-' }}</td>
                        <td>{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ayah']['mapValue']['fields']['nama']['stringValue'] ?? '-' }}</td>
                        <td>{{ $fields['dataKeluarga']['mapValue']['fields']['orangTuaWali']['mapValue']['fields']['ibu']['mapValue']['fields']['nama']['stringValue'] ?? '-' }}</td>
                        <td>
                            <a href="{{ route('users.show', $id) }}" class="btn btn-sm btn-info">Detail</a>
                            <a href="{{ route('users.edit', $id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <a href="{{ route('api.siswa.edit', $id) }}" class="btn btn-sm btn-secondary">Lihat Detail Siswa</a>
                            <form action="{{ route('users.destroy', $id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada pengguna.</p>
    @endif
@endsection