<div class="container-fluid">
    <!-- Page Heading -->
    <?php $button = ($tahun) ? 'enabled' : 'disabled'; ?>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> <i class="fas fa-users"></i> Data Santri & Kenaikan Jilid </h1>
    </div>
    <?php if ($this->session->flashdata('message')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('message'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php echo anchor('admin/pesertadidik/input', '<button class="btn btn-sm btn-primary mb-3 mr-2" ' . $button . '><i class="fas fa-plus fa-sm"></i> Tambah Santri ke Jilid</button>') ?>
    <?php echo anchor('admin/pesertadidik/updatejilid', '<button class="btn btn-sm btn-primary mb-3 mr-2"><i class="fas fa-pen fa-sm"></i> Update/Kenaikan Jilid</button>') ?>
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="jilid">Jilid</label>
                <select class="form-control" id="jilid" name="jilid">
                    <option value="">--Pilih Jilid--</option>
                    <?php foreach ($jilid as $kl) : ?>
                        <option value="<?php echo $kl->id_jilid ?>"><?= $kl->jilid ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button onclick="lihatPeserta()" class="btn btn-primary" <?= $button ?>><i class="fas fa-search"></i> Lihat</button>
        </div>
    </div>

    <div id="data-peserta">
    </div>
</div>
</main>

<script>
    function lihatPeserta() {
        const idjilid = $('#jilid').val();
        const nameTahun = '<?= $tahun['nama'] ?>';

        $.ajax({
            type: 'POST',
            url: '<?= base_url('admin/pesertadidik/data_peserta') ?>',
            data: {
                id_jilid: idjilid,
                tahun: nameTahun
            },
            success: function(response) {
                $('#data-peserta').html(response);
            },
            error: function(response) {
                $('#data-peserta').html(response);
            }
        });
    }
    $(document).ready(function() {
        $(document).on('click', '#data-santri', function() {
            const idsantri = $(this).data('idsantri');
            const href = '<?= site_url('admin/pesertadidik/delete/') ?>' + idsantri;

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
        });
    });
</script>