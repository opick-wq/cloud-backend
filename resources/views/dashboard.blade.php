<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aplikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Lighter gray for a softer look */
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            border: none; /* Remove default card border for a cleaner look */
            border-radius: 0.5rem; /* Softer corners */
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .table th {
            font-weight: 600; /* Slightly bolder table headers */
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef; /* Softer hover color */
        }
        .btn-action {
            margin-right: 5px;
        }
        #paginationContainer .page-link {
            color: var(--bs-primary);
        }
        #paginationContainer .page-item.active .page-link {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            color: #fff;
        }
         /* Style for search input with icon */
        .search-container {
            position: relative;
        }
        .search-container .form-control {
            padding-left: 2.5rem; /* Space for icon */
        }
        .search-container .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d; /* Icon color */
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Aplikasi Dashboard</span>
        <button class="btn btn-danger btn-sm d-flex align-items-center" id="logoutBtn">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
    </div>
</nav>

<div class="container mt-4">
    <div id="alert" class="alert d-none" role="alert"></div>

    <div class="card p-3 p-md-4 shadow-sm mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <img id="avatarUrl" src="https://via.placeholder.com/100" alt="Avatar" class="rounded-circle profile-avatar">
            </div>
            <div class="col">
                <h4 id="username" class="mb-1">-</h4>
                <p id="nameDisplay" class="text-muted mb-1">-</p>
                <p id="email" class="text-muted mb-1">-</p>
                <span class="badge bg-info fs-6" id="role">-</span>
            </div>
            </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h5 class="card-title mb-2 mb-md-0">Manajemen Pengguna</h5>
                <div class="d-flex">
                    <div class="search-container me-2">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari pengguna...">
                    </div>
                    <button class="btn btn-success btn-sm d-flex align-items-center" id="btnAdd">
                        <i class="bi bi-plus-circle me-1"></i> Tambah User
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="userTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-3" id="paginationContainer">
                    </ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="userForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Form User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="userId">
                <div class="mb-3">
                    <label for="usernameInput" class="form-label">Username</label>
                    <input type="text" name="username" id="usernameInput" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="nameInput" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="nameInput" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="emailInput" class="form-label">Email</label>
                    <input type="email" name="email" id="emailInput" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="roleInput" class="form-label">Role</label>
                    <select name="role" id="roleInput" class="form-select" required>
                        <option value="" disabled selected>Pilih role</option>
                        <option value="guru_bk">Guru BK</option>
                        <option value="siswa">Siswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="passwordInput" class="form-label">Password</label>
                    <input type="password" name="password" id="passwordInput" class="form-control">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {
    const token = localStorage.getItem('token');
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    const $alert = $('#alert');

    let allUsersData = []; // To store all users for client-side pagination & search
    let currentPage = 1;
    const usersPerPage = 5; // Adjust as needed

    if (!token) {
        window.location.href = "/login"; // Make sure this path is correct
    }

    function showAlert(message, type = 'danger') {
        $alert.removeClass('d-none alert-danger alert-success alert-warning').addClass(`alert-${type}`).text(message);
        setTimeout(() => $alert.addClass('d-none'), 5000);
    }

    // Load profile
    $.ajax({
        url: '/api/me', // Make sure this path is correct
        headers: { Authorization: 'Bearer ' + token },
        success: function (res) {
            if (res.user) {
                $('#username').text(res.user.username || '-');
                $('#nameDisplay').text(res.user.name || 'Nama tidak tersedia'); // Display name
                $('#email').text(res.user.email || '-');
                $('#role').text(res.user.role || '-').addClass(`bg-${getRoleColor(res.user.role)}`);
                $('#avatarUrl').attr('src', res.user.avatarUrl || 'https://via.placeholder.com/100');
            } else {
                 showAlert("Gagal memuat data profil.");
            }
        },
        error: () => {
            showAlert("Sesi Anda tidak valid. Silakan login ulang.");
            setTimeout(() => window.location.href = "/login", 2000); // Make sure this path is correct
        }
    });

    function getRoleColor(role) {
        switch (role) {
            case 'admin': return 'danger';
            case 'guru_bk': return 'warning';
            case 'siswa': return 'info';
            default: return 'secondary';
        }
    }

    function displayTablePage(usersToDisplay, page) {
        const $userTableBody = $('#userTable tbody');
        $userTableBody.empty();
        currentPage = page;

        const startIndex = (page - 1) * usersPerPage;
        const endIndex = startIndex + usersPerPage;
        const paginatedUsers = usersToDisplay.slice(startIndex, endIndex);

        if (paginatedUsers.length === 0 && page === 1) {
             $userTableBody.html('<tr><td colspan="6" class="text-center">Tidak ada data pengguna.</td></tr>');
             setupPagination([], 0); // Clear pagination if no users
             return;
        }
         if (paginatedUsers.length === 0 && page > 1) { // if current page has no users (e.g. after a delete on last page)
            currentPage = Math.max(1, page - 1);
            displayTablePage(usersToDisplay, currentPage);
            return;
        }


        paginatedUsers.forEach((user, index) => {
            const rowNum = startIndex + index + 1;
            // CRITICAL: Do NOT display user.password. It's a major security risk.
            // The password field in edit form is for *changing* it.
            const row = `
                <tr>
                    <td>${rowNum}</td>
                    <td>${user.username || '-'}</td>
                    <td>${user.name || '-'}</td>
                    <td>${user.email || '-'}</td>
                    <td><span class="badge bg-${getRoleColor(user.role)}">${user.role || '-'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-info btn-action btnEdit" data-id="${user.id}" title="Edit">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger btn-action btnDelete" data-id="${user.id}" title="Hapus">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                        ${user.role === 'siswa' ? `<a href="/siswa/${user.id}/edit" class="btn btn-sm btn-secondary btn-action" title="Lihat Detail Siswa"><i class="bi bi-person-lines-fill"></i> Detail</a>` : ''}
                    </td>
                </tr>`;
            $userTableBody.append(row);
        });
        setupPagination(usersToDisplay, usersToDisplay.length);
    }

    function setupPagination(items, totalItems) {
        const $paginationContainer = $('#paginationContainer');
        $paginationContainer.empty();
        const totalPages = Math.ceil(totalItems / usersPerPage);

        if (totalPages <= 1) return; // No pagination needed for 1 or 0 pages

        // Previous button
        $paginationContainer.append(
            `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>`
        );

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            $paginationContainer.append(
                `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`
            );
        }

        // Next button
        $paginationContainer.append(
            `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>`
        );
    }

    $('#paginationContainer').on('click', 'a.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage) {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const filteredUsers = allUsersData.filter(user =>
                (user.username && user.username.toLowerCase().includes(searchTerm)) ||
                (user.name && user.name.toLowerCase().includes(searchTerm)) ||
                (user.email && user.email.toLowerCase().includes(searchTerm)) ||
                (user.role && user.role.toLowerCase().includes(searchTerm))
            );
            displayTablePage(filteredUsers, page);
        }
    });


    function loadUsers() {
        $.ajax({
            url: '/api/users', // Make sure this path is correct
            headers: { Authorization: 'Bearer ' + token },
            success: function (res) {
                allUsersData = res || [];
                $('#searchInput').val(''); // Clear search on load
                displayTablePage(allUsersData, 1);
            },
            error: function() {
                showAlert("Gagal memuat data pengguna.");
                $('#userTable tbody').html('<tr><td colspan="6" class="text-center">Gagal memuat data. Coba lagi nanti.</td></tr>');
            }
        });
    }

    loadUsers();

    // Search functionality
    $('#searchInput').on('keyup', function () {
        const searchTerm = $(this).val().toLowerCase();
        const filteredUsers = allUsersData.filter(user =>
            (user.username && user.username.toLowerCase().includes(searchTerm)) ||
            (user.name && user.name.toLowerCase().includes(searchTerm)) || // Search by name
            (user.email && user.email.toLowerCase().includes(searchTerm)) ||
            (user.role && user.role.toLowerCase().includes(searchTerm))
        );
        displayTablePage(filteredUsers, 1); // Reset to page 1 for new search
    });

    // Tambah user - Open modal
    $('#btnAdd').click(() => {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#passwordInput').attr('placeholder', 'Masukkan password baru').prop('required', true);
        $('#userModalLabel').text('Tambah User Baru');
        userModal.show();
    });

    // Simpan user (Tambah or Edit)
    $('#userForm').submit(function (e) {
        e.preventDefault();
        const id = $('#userId').val();
        let password = $('#passwordInput').val();

        const data = {
            username: $('#usernameInput').val(),
            name: $('#nameInput').val(),
            email: $('#emailInput').val(),
            role: $('#roleInput').val(),
        };

        // Only include password if it's being set or changed
        if (password) {
            data.password = password;
        } else if (!id) { // If it's a new user, password is required
            showAlert('Password wajib diisi untuk user baru.', 'warning');
            return;
        }


        const url = id ? `/api/users/${id}` : '/api/admin/register'; // Make sure these paths are correct
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            headers: { Authorization: 'Bearer ' + token },
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: () => {
                userModal.hide();
                loadUsers(); // Reload users to reflect changes
                showAlert(`User berhasil ${id ? 'diperbarui' : 'ditambahkan'}.`, 'success');
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : `Gagal ${id ? 'memperbarui' : 'menambahkan'} user.`;
                showAlert(errorMsg);
            }
        });
    });

    // Edit user - Populate modal
    $(document).on('click', '.btnEdit', function () {
        const id = $(this).data('id');
        $.ajax({
            url: `/api/users/${id}`, // Make sure this path is correct
            headers: { Authorization: 'Bearer ' + token },
            success: (res) => {
                $('#userId').val(id);
                $('#usernameInput').val(res.username);
                $('#nameInput').val(res.name);
                $('#emailInput').val(res.email);
                $('#roleInput').val(res.role);
                $('#passwordInput').val('').attr('placeholder', 'Kosongkan jika tidak ingin mengubah').prop('required', false); // Clear password and make it optional for edit
                $('#userModalLabel').text('Edit User');
                userModal.show();
            },
            error: function() {
                showAlert("Gagal mengambil data user untuk diedit.");
            }
        });
    });

    // Hapus user
    $(document).on('click', '.btnDelete', function () {
        if (!confirm('Yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.')) return;
        const id = $(this).data('id');
        $.ajax({
            url: `/api/users/${id}`, // Make sure this path is correct
            method: 'DELETE',
            headers: { Authorization: 'Bearer ' + token },
            success: () => {
                loadUsers(); // Reload users
                showAlert("User berhasil dihapus.", "success");
            },
            error: function() {
                showAlert("Gagal menghapus user.");
            }
        });
    });

    // Logout
    $('#logoutBtn').click(function () {
        localStorage.removeItem('token');
        window.location.href = "/login"; // Make sure this path is correct
    });
});
</script>
</body>
</html>