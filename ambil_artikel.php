<?php 
require 'koneksi.php'; 

$query = "SELECT artikel.*, kategori_artikel.nama_kategori, penulis.nama_depan, penulis.nama_belakang 
          FROM artikel LEFT JOIN kategori_artikel ON artikel.id_kategori = kategori_artikel.id 
          LEFT JOIN penulis ON artikel.id_penulis = penulis.id ORDER BY artikel.id DESC";
$res = $conn->query($query);

$kategoris = []; 
$res_kat = $conn->query("SELECT id, nama_kategori FROM kategori_artikel");
if ($res_kat) { while($r = $res_kat->fetch_assoc()) { $kategoris[] = $r; } }

$penulises = []; 
$res_pen = $conn->query("SELECT id, nama_depan, nama_belakang FROM penulis");
if ($res_pen) { while($r = $res_pen->fetch_assoc()) { $penulises[] = $r; } }
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">Kelola Artikel</h4>
    <button class="btn btn-warning text-white fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahArtikel">
        <i class="bi bi-file-earmark-plus me-1"></i>Tambah Artikel
    </button>
</div>

<div class="card card-custom p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">GAMBAR</th>
                    <th>JUDUL</th>
                    <th>KATEGORI</th>
                    <th>PENULIS</th>
                    <th>WAKTU UPLOAD</th>
                    <th class="text-end pe-4">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($res && $res->num_rows > 0): while($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-4">
                            <img src="uploads_artikel/<?= htmlspecialchars($row['gambar'] ?? '') ?>" 
                                 class="img-table shadow-sm" style="width: 70px; height: 50px;"
                                 onerror="this.src='https://via.placeholder.com/70x50?text=No+Image';">
                        </td>
                        <td class="fw-bold"><?= htmlspecialchars($row['judul'] ?? '-') ?></td>
                        <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($row['nama_kategori'] ?? 'Umum') ?></span></td>
                        <td class="text-muted"><?= htmlspecialchars(($row['nama_depan'] ?? '') . ' ' . ($row['nama_belakang'] ?? '')) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($row['hari_tanggal'] ?? '-') ?></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="bukaEditArtikel(<?= $row['id'] ?>)">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="hapusData('hapus_artikel.php', <?= $row['id'] ?>, 'artikel')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada artikel.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambahArtikel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formTambahArtikel" enctype="multipart/form-data" class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="fw-bold">Tambah Artikel Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Judul Artikel</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Penulis</label>
                        <select name="id_penulis" class="form-select" required>
                            <option value="">Pilih Penulis...</option>
                            <?php foreach($penulises as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_depan'].' '.$p['nama_belakang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col">
                        <label class="form-label">Kategori</label>
                        <select name="id_kategori" class="form-select" required>
                            <option value="">Pilih Kategori...</option>
                            <?php foreach($kategoris as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Isi Artikel</label>
                    <textarea name="isi" class="form-control" rows="6" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gambar Thumbnail</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning text-white fw-bold w-100" onclick="submitForm('simpan_artikel.php', 'formTambahArtikel', 'artikel')">PUBLIKASIKAN</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditArtikel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="formEditArtikel" enctype="multipart/form-data" class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="fw-bold">Edit Artikel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_artikel" id="edit_id_artikel">
                <div class="mb-3">
                    <label class="form-label">Judul Artikel</label>
                    <input type="text" name="judul" id="edit_judul" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Penulis</label>
                        <select name="id_penulis" id="edit_id_penulis" class="form-select" required>
                            <?php foreach($penulises as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_depan'].' '.$p['nama_belakang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col">
                        <label class="form-label">Kategori</label>
                        <select name="id_kategori" id="edit_id_kategori" class="form-select" required>
                            <?php foreach($kategoris as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Isi Artikel</label>
                    <textarea name="isi" id="edit_isi" class="form-control" rows="6" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ganti Gambar <small class="text-muted">(Biarkan kosong jika tidak ingin mengubah)</small></label>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary fw-bold w-100" onclick="submitForm('update_artikel.php', 'formEditArtikel', 'artikel')">UPDATE PERUBAHAN</button>
            </div>
        </form>
    </div>
</div>