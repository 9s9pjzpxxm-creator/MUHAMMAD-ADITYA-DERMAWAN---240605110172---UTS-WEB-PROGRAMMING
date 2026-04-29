<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS - Muhammad Aditya Dermawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .header { background: #1a2a3a; color: white; padding: 1rem 2rem; border-bottom: 3px solid #f39c12; }
        .sidebar { background: white; min-height: 100vh; border-right: 1px solid #e0e0e0; padding: 2rem 1rem; }
        .nav-link { color: #555; padding: 0.8rem 1rem; border-radius: 8px; margin-bottom: 0.5rem; cursor: pointer; transition: 0.3s; }
        .nav-link:hover { background: #f8f9fa; color: #1a2a3a; }
        .nav-link.active { background: #fff4e5; color: #d35400; font-weight: 600; border-right: 4px solid #d35400; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .img-table { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; }
    </style>
</head>
<body>

<div class="header d-flex align-items-center justify-content-between shadow-sm">
    <div>
        <h5 class="mb-0 fw-bold"><i class="bi bi-water me-2"></i>SISTEM MANAJEMEN BLOG</h5>
        <small class="text-warning fw-semibold">Muhammad Aditya Dermawan - 240605110172</small>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <nav class="nav flex-column">
                <div class="nav-link active" onclick="loadMenu('penulis', this)"><i class="bi bi-person-badge me-2"></i>Kelola Penulis</div>
                <div class="nav-link" onclick="loadMenu('artikel', this)"><i class="bi bi-file-earmark-post me-2"></i>Kelola Artikel</div>
                <div class="nav-link" onclick="loadMenu('kategori', this)"><i class="bi bi-tags me-2"></i>Kelola Kategori</div>
            </nav>
        </div>
        <div class="col-md-10 p-5">
            <div id="dynamic-content">
                <div class="text-center p-5"><div class="spinner-border text-warning"></div></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function loadMenu(menu, el) {
        if(el) {
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            el.classList.add('active');
        }
        fetch(`ambil_${menu}.php`).then(res => res.text()).then(html => {
            document.getElementById('dynamic-content').innerHTML = html;
        });
    }

    window.onload = () => loadMenu('penulis');

    function submitForm(endpoint, formId, menuToRefresh) {
        const form = document.getElementById(formId);
        const formData = new FormData(form);

        fetch(endpoint, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if(data.status === 'success') {
                    const modalEl = document.querySelector('.modal.show');
                    if(modalEl) bootstrap.Modal.getInstance(modalEl).hide();
                    loadMenu(menuToRefresh);
                }
            }).catch(err => {
                console.error(err);
                alert("Gagal memproses ke server.");
            });
    }

    function hapusData(endpoint, id, menuToRefresh) {
        if(confirm('Yakin ingin menghapus data ini?')) {
            const formData = new FormData();
            formData.append('id', id);
            fetch(endpoint, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if(data.status === 'success') loadMenu(menuToRefresh);
                });
        }
    }

    // === FUNGSI EDIT DIPINDAHKAN KE SINI AGAR BISA TERBACA ===
    function bukaEditPenulis(id) {
        fetch(`ambil_satu_penulis.php?id=${id}`).then(res => res.json()).then(data => {
            document.getElementById('edit_id_pen').value = data.id;
            document.getElementById('edit_nama_depan').value = data.nama_depan;
            document.getElementById('edit_nama_belakang').value = data.nama_belakang;
            document.getElementById('edit_user_name').value = data.user_name;
            new bootstrap.Modal(document.getElementById('modalEditPenulis')).show();
        });
    }

    function bukaEditArtikel(id) {
        fetch(`ambil_satu_artikel.php?id=${id}`).then(res => res.json()).then(data => {
            document.getElementById('edit_id_artikel').value = data.id;
            document.getElementById('edit_judul').value = data.judul;
            document.getElementById('edit_id_penulis').value = data.id_penulis;
            document.getElementById('edit_id_kategori').value = data.id_kategori;
            document.getElementById('edit_isi').value = data.isi;
            new bootstrap.Modal(document.getElementById('modalEditArtikel')).show();
        });
    }

    function bukaEditKategori(id) {
        fetch(`ambil_satu_kategori.php?id=${id}`).then(res => res.json()).then(data => {
            document.getElementById('edit_id_kat').value = data.id;
            document.getElementById('edit_nama_kat').value = data.nama_kategori;
            document.getElementById('edit_ket_kat').value = data.keterangan;
            new bootstrap.Modal(document.getElementById('modalEditKategori')).show();
        });
    }
</script>
</body>
</html>