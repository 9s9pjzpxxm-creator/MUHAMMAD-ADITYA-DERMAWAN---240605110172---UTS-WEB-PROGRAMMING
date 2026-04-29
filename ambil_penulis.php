<?php require 'koneksi.php'; $res = $conn->query("SELECT * FROM penulis ORDER BY id DESC"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">Kelola Penulis</h4>
    <button class="btn btn-warning fw-bold text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPenulis"><i class="bi bi-person-plus me-1"></i>Tambah Penulis</button>
</div>

<div class="card card-custom p-3">
    <table class="table table-hover">
        <thead class="table-light"><tr><th class="ps-4">FOTO</th><th>NAMA LENGKAP</th><th>USERNAME</th><th class="text-end pe-4">AKSI</th></tr></thead>
        <tbody>
            <?php while($row = $res->fetch_assoc()): ?>
            <tr>
                <td class="ps-4"><img src="uploads_penulis/<?= htmlspecialchars($row['foto']) ?>" class="img-table shadow-sm" onerror="this.src='https://via.placeholder.com/50';"></td>
                <td class="fw-bold"><?= htmlspecialchars($row['nama_depan'].' '.$row['nama_belakang']) ?></td>
                <td><code class="text-primary"><?= htmlspecialchars($row['user_name']) ?></code></td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="bukaEditPenulis(<?= $row['id'] ?>)"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="hapusData('hapus_penulis.php', <?= $row['id'] ?>, 'penulis')"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambahPenulis" tabindex="-1"><div class="modal-dialog"><div class="modal-content border-0 shadow">
    <div class="modal-header"><h5>Tambah Penulis</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><form id="formTambahPenulis" enctype="multipart/form-data">
        <div class="row mb-3"><div class="col"><label class="form-label">Nama Depan</label><input type="text" name="nama_depan" class="form-control" required></div><div class="col"><label class="form-label">Nama Belakang</label><input type="text" name="nama_belakang" class="form-control" required></div></div>
        <div class="mb-3"><label class="form-label">Username</label><input type="text" name="user_name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Foto Profil</label><input type="file" name="foto" class="form-control" accept="image/png, image/jpeg"></div>
    </form></div>
    <div class="modal-footer"><button type="button" class="btn btn-warning text-white fw-bold w-100" onclick="submitForm('simpan_penulis.php', 'formTambahPenulis', 'penulis')">SIMPAN</button></div>
</div></div></div>

<div class="modal fade" id="modalEditPenulis" tabindex="-1"><div class="modal-dialog"><div class="modal-content border-0 shadow">
    <div class="modal-header"><h5>Edit Penulis</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><form id="formEditPenulis" enctype="multipart/form-data">
        <input type="hidden" name="id_penulis" id="edit_id_pen">
        <div class="row mb-3"><div class="col"><label class="form-label">Nama Depan</label><input type="text" name="nama_depan" id="edit_nama_depan" class="form-control" required></div><div class="col"><label class="form-label">Nama Belakang</label><input type="text" name="nama_belakang" id="edit_nama_belakang" class="form-control" required></div></div>
        <div class="mb-3"><label class="form-label">Username</label><input type="text" name="user_name" id="edit_user_name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Password Baru <small class="text-muted">(kosongkan jika tetap)</small></label><input type="password" name="password" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Foto Baru <small class="text-muted">(kosongkan jika tetap)</small></label><input type="file" name="foto" class="form-control" accept="image/png, image/jpeg"></div>
    </form></div>
    <div class="modal-footer"><button type="button" class="btn btn-primary fw-bold w-100" onclick="submitForm('update_penulis.php', 'formEditPenulis', 'penulis')">UPDATE PERUBAHAN</button></div>
</div></div></div>

<script>
function bukaEditPenulis(id) {
    fetch(`ambil_satu_penulis.php?id=${id}`).then(res => res.json()).then(data => {
        document.getElementById('edit_id_pen').value = data.id;
        document.getElementById('edit_nama_depan').value = data.nama_depan;
        document.getElementById('edit_nama_belakang').value = data.nama_belakang;
        document.getElementById('edit_user_name').value = data.user_name;
        new bootstrap.Modal(document.getElementById('modalEditPenulis')).show();
    });
}
</script>