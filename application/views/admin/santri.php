<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-graduate"></i> Data Santri</h1>
    </div>
    <?php if ($this->session->flashdata('message')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('message'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php echo anchor('admin/santri/input', '<button class="btn btn-sm btn-primary mb-3"><i class="fas fa-plus fa-sm"></i> Tambah Data</button>') ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-responsive-sm table-bordered table-striped table-sm w-100 d-block d-md-table" id="table-santri">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Santri</th>
                        <th>Tgl Masuk TPQ</th>
                        <th>Nama</th>
                        <th>Tanggal Lahir</th>
                        <th>Asal Sekolah</th>
                        <th>Jenis Kelamin</th>
                        <th width="160px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <!-- <?= $this->pagination->create_links(); ?> -->
        </div>
    </div>
</div>

<!-- Detal Modal -->
<div class="modal fade detailModal" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detalModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark font-weight-bold">Santri : <span id="namasantri"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3" class="text-center">
                            <h6 class="text-dark font-weight-bold">Foto Santri</h6>
                            <div id="photo" class="mb-3"></div>
                        </div>
                        <div class="col-sm-5">
                            <h6 class="text-dark font-weight-bold">Orang Tua</h6>
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-borderless no-margin">
                                        <tr>
                                            <th>Nama Ibu</th>
                                            <td><span id="namaibu"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Pendidikan Ibu</th>
                                            <td><span id="pendibu"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Pekerjaan Ibu</th>
                                            <td><span id="pekibu"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Nama Ayah</th>
                                            <td><span id="namaayah"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Pendidikan Ayah</th>
                                            <td><span id="pendayah"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Pekerjaan Ayah</th>
                                            <td><span id="pekayah"></span></td>
                                        </tr>
                                        <tr>
                                            <th>No. Handphone</th>
                                            <td><span id="nohp"></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <h6 class="text-dark font-weight-bold">Alamat</h6>
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-borderless no-margin">
                                        <tr>
                                            <th>Dusun</th>
                                            <td><span id="dusun"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Desa</th>
                                            <td><span id="desa"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Kecamatan</th>
                                            <td><span id="kecamatan"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Kabupaten</th>
                                            <td><span id="kabupaten"></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary text-white" id="edit-santri">Edit</a>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</main>

<script>
    //onclick hapus data santri
    function confirmDelete(id) {
        const href = '<?= site_url('admin/santri/delete/') ?>' + id;

        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "data santri akan dihapus",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus Data',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = href;
            }
        });
    }

    // Detail modal santri
    $(document).ready(function() {
        $(document).on('click', '#set_detailModal', function() {
            const idsantri = $(this).data('idsantri');
            const namasantri = $(this).data('santri');
            const namaIbu = $(this).data('namaibu');
            const pendIbu = $(this).data('pendidikanibu');
            const pekIbu = $(this).data('perkejaanibu');
            const namaAyah = $(this).data('namaayah');
            const pendAyah = $(this).data('pendidikanayah');
            const pekAyah = $(this).data('pekerjaanayah');
            const noHp = $(this).data('nohp');
            const dusun = $(this).data('dusun');
            const desa = $(this).data('desa');
            const kecamatan = $(this).data('kecamatan');
            const kabupaten = $(this).data('kabupaten');
            const photo = $(this).data('photo');
            const isPhoto = photo !== '' ? photo : 'user-placeholder.jpg';
            const href = '<?php echo base_url('admin/santri/edit/') ?>' + idsantri;
            const url_photo = '<?= base_url('assets/photos/') ?>' + isPhoto;

            $('#namasantri').text(namasantri);
            $('#namaibu').text(namaIbu);
            $('#pendibu').text(pendIbu);
            $('#pekibu').text(pekIbu);
            $('#namaayah').text(namaAyah);
            $('#pendayah').text(pendAyah);
            $('#pekayah').text(pekAyah);
            $('#nohp').text(noHp);
            $('#dusun').text(dusun);
            $('#desa').text(desa);
            $('#kecamatan').text(kecamatan);
            $('#kabupaten').text(kabupaten);
            $('#photo').html(`<img src="${url_photo}" alt="photo santri" style="max-width:200px; max-height:300px; object-fit: scale-down; object-position: center; border-radius: 15px;">`)

            $(document).on('click', '#edit-santri', function() {
                document.location.href = href;
            });

        });
    });

    //datatables
    $(document).ready(function() {
        $('#table-santri').DataTable({
            "serverSide": true,
            "ajax": {
                "url": "<?= site_url('admin/santri/get_result_santri') ?>",
                "type": "POST"
            },
            "columnDefs": [{
                    "targets": [0, 7, -1],
                    "className": 'text-center'
                },
                {
                    "targets": [-1],
                    "orderable": false
                }
            ]
        });
    });
</script>