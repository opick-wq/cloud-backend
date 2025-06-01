<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Aplikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand">Dashboard</span>
        <button class="btn btn-danger btn-sm" id="logoutBtn">Logout</button>
    </div>
</nav>

<div class="container mt-5">
    <div id="alert" class="alert d-none"></div>

    <!-- Profile Info -->
    <div class="card p-4 shadow mb-4">
        <div class="d-flex align-items-center">
            <img id="avatarUrl" src="" alt="Avatar" width="80" class="rounded-circle me-3" style="object-fit: cover; height: 80px;">
            <div>
                <h5 id="username">-</h5>
                <p id="email">-</p>
                <span class="badge bg-info" id="role">-</span>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow">
  <div class="card-header d-flex justify-content-between align-items-center">
   <h5>Data Users</h5>
   <div>
    <input type="text" id="searchInput" class="form-control form-control-sm mr-2" placeholder="Cari...">
    <button class="btn btn-success btn-sm" id="btnAdd">Tambah User</button>
   </div>
  </div>
  <div class="card-body">
   <div class="table-responsive">
    <table class="table table-bordered table-hover" id="userTable">
     <thead>
      <tr>
       <th>No</th>
       <th>Password</th>
       <th>Username</th>
       <th>Email</th>
       <th>Role</th>
       <th>Aksi</th>
      </tr>
     </thead>
     <tbody></tbody>
    </table>
   </div>
  </div>
 </div>

 <script>
  document.addEventListener('DOMContentLoaded', function () {
   const searchInput = document.getElementById('searchInput');
   const userTable = document.getElementById('userTable').getElementsByTagName('tbody')[0];
   const tableRows = userTable.getElementsByTagName('tr');

   searchInput.addEventListener('keyup', function () {
    const searchTerm = searchInput.value.toLowerCase();

    for (let i = 0; i < tableRows.length; i++) {
     const rowData = tableRows[i].textContent.toLowerCase();
     if (rowData.includes(searchTerm)) {
      tableRows[i].style.display = '';
     } else {
      tableRows[i].style.display = 'none';
     }
    }
   });
  });
 </script>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="userForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="userId">
                <div class="mb-2">
                    <label>Username</label>
                    <input type="text" name="username" id="usernameInput" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>name</label>
                    <input type="text" name="name" id="nameInput" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Email</label>
                    <input type="email" name="email" id="emailInput" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label for="roleInput" class="form-label">Role</label>
                    <select name="role" id="roleInput" class="form-control" required>
                        <option value="" disabled selected>Pilih role</option>
                        <option value="guru_bk">Guru BK</option>
                        <option value="siswa">Siswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div class="mb-2">
                    <label>Password</label>
                    <input type="password" name="password" id="passwordInput" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
const token = localStorage.getItem('token');
const modal = new bootstrap.Modal(document.getElementById('userModal'));

if (!token) window.location.href = "/login";

// Load profile
$.ajax({
    url: '/api/me',
    headers: { Authorization: 'Bearer ' + token },
    success: function (res) {
        $('#username').text(res.user.username);
        $('#email').text(res.user.email);
        $('#role').text(res.user.role);
        $('#avatarUrl').attr('src', res.user.avatarUrl );
    },
    error: () => {
        $('#alert').removeClass('d-none').addClass('alert-danger').text("Token tidak valid. Silakan login ulang.");
        setTimeout(() => window.location.href = "/login", 2000);
    }
});

// Load user table
function loadUsers() {
            $.ajax({
                url: '/api/users',
                headers: { Authorization: 'Bearer ' + token },
                success: function (res) {
                    let rows = '';
                    res.forEach((user, index) => {
                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${user.password}</td>
                                <td>${user.username}</td>
                                <td>${user.email}</td>
                                <td>${user.role}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btnEdit" data-id="${user.id}">Edit</button>
                                    <button class="btn btn-sm btn-danger btnDelete" data-id="${user.id}">Hapus</button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#userTable tbody').html(rows);
                }
            });
        }
        loadUsers();

        // Tambah user
        $('#btnAdd').click(() => {
            $('#userForm')[0].reset();
            $('#userId').val('');
            modal.show();
        });

        // Simpan user
        $('#userForm').submit(function (e) {
            e.preventDefault();
            const id = $('#userId').val();
            const data = {
                username: $('#usernameInput').val(),
                name: $('#nameInput').val(),
                email: $('#emailInput').val(),
                role: $('#roleInput').val(),
                password: $('#passwordInput').val()
            };

            const url = id ? `/api/users/${id}` : '/api/admin/register';
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                headers: { Authorization: 'Bearer ' + token },
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: () => {
                    modal.hide();
                    loadUsers();
                }
            });
        });

        // Edit user
        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');
            $.ajax({
                url: `/api/users/${id}`,
                headers: { Authorization: 'Bearer ' + token },
                success: (res) => {
                    $('#userId').val(id);
                    $('#usernameInput').val(res.username);
                    $('#nameInput').val(res.name);
                    $('#emailInput').val(res.email);
                    $('#roleInput').val(res.role);
                    $('#passwordInput').val(res.password);
                    modal.show();
                }
            });
        });

        // Hapus user
        $(document).on('click', '.btnDelete', function () {
            if (!confirm('Yakin ingin menghapus user ini?')) return;
            const id = $(this).data('id');
            $.ajax({
                url: `/api/users/${id}`,
                method: 'DELETE',
                headers: { Authorization: 'Bearer ' + token },
                success: loadUsers
            });
        });

        // Logout
        $('#logoutBtn').click(function () {
            localStorage.removeItem('token');
            window.location.href = "/login";
        });

        // Fitur Pencarian dengan Nomor Urut
        $(document).ready(function() {
            $("#searchInput").on("keyup", function() {
                const value = $(this).val().toLowerCase();
                $("#userTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
                // Perbarui nomor urut setelah pencarian
                $('#userTable tbody tr:visible').each(function(i) {
                    $(this).find('td:first').text(i + 1);
                });
            });
        });
</script>
</body>
</html>
