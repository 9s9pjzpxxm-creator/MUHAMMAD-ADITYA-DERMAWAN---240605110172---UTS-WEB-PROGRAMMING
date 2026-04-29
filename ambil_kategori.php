<?php require 'koneksi.php'; $res = $conn->query("SELECT * FROM kategori_artikel ORDER BY id DESC"); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">Kelola Kategori Artikel</h4>
    <button class="btn btn-warning fw-bold text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKategori"><i class="bi bi-plus-lg me-1"></i>Tambah Kategori</button>
</div>

<div class="card card-custom p-3">
    <table class="table table-hover">
        <thead class="table-light"><tr><th class="ps-4">KATEGORI</th><th>KETERANGAN</th><th class="text-end pe-4">AKSI</th></tr></thead>
        <tbody>
            <?php while($row = $res->fetch_assoc()): ?>
            <tr>
                <td class="ps-4"><span class="badge bg-warning text-dark px-3 py-2"><?= $row['nama_kategori'] ?></span></td>
                <td class="text-muted"><?= $row['keterangan'] ?></td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="bukaEditKategori(<?= $row['id'] ?>)"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="hapusData('hapus_kategori.php', <?= $row['id'] ?>, 'kategori')"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambahKategori" tabindex="-1"><div class="modal-dialog"><div class="modal-content border-0 shadow">
    <div class="modal-header"><h5>Tambah Kategori Baru</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><form id="formTambahKategori">
        <div class="mb-3"><label class="form-label">Nama Kategori</label><input type="text" name="nama_kategori" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" rows="3"></textarea></div>
    </form></div>
    <div class="modal-footer"><button class="btn btn-warning text-white fw-bold w-100" onclick="submitForm('simpan_kategori.php', 'formTambahKategori', 'kategori')">SIMPAN</button></div>
</div></div></div>

<div class="modal fade" id="modalEditKategori" tabindex="-1"><div class="modal-dialog"><div class="modal-content border-0 shadow">
    <div class="modal-header"><h5>Edit Kategori</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><form id="formEditKategori">
        <input type="hidden" name="id_kategori" id="edit_id_kat">
        <div class="mb-3"><label class="form-label">Nama Kategori</label><input type="text" name="nama_kategori" id="edit_nama_kat" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Keterangan</label><textarea name="keterangan" id="edit_ket_kat" class="form-control" rows="3"></textarea></div>
    </form></div>
    <div class="modal-footer"><button class="btn btn-primary fw-bold w-100" onclick="submitForm('update_kategori.php', 'formEditKategori', 'kategori')">UPDATE PERUBAHAN</button></div>
</div></div></div>

<script>
function bukaEditKategori(id) {
    fetch(`ambil_satu_kategori.php?id=${id}`).then(res => res.json()).then(data => {
        document.getElementById('edit_id_kat').value = data.id;
        document.getElementById('edit_nama_kat').value = data.nama_kategori;
        document.getElementById('edit_ket_kat').value = data.keterangan;
        new bootstrap.Modal(document.getElementById('modalEditKategori')).show();
    });
}
</script>