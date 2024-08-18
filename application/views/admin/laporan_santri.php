<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> <i class="fas fa-sticky-note"></i> Laporan Daftar Santri</h1>
    </div>
    <div class="card">
        <div class="card-header bg-behance">
            <h6 class="text-white">Masukkan Data Yang Diperlukan</h6>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="thn_ajaran">Tahun</label>
                <select class="form-control" id="thn_ajaran" name="thn_ajaran">
                    
                    <?php foreach ($tahun as $th) : ?>
                        <option value="<?= $th->nama ?>"><?= $th->nama ?></option>
                    <?php endforeach; ?>
                </select>
                <?php echo form_error('thn_ajaran', '<div class="text-danger small ml-3">', '</div>') ?>
            </div>
            <div class="form-group">
                <label for="jilid">jilid</label>
                <select class="form-control" id="jilid" name="jilid">
                    <option value="">--Pilih jilid--</option>
                    <?php foreach ($jilid as $kl) : ?>
                        <option value="<?= $kl->id_jilid ?>"><?= $kl->jilid ?></option>
                    <?php endforeach; ?>
                </select>
                <?php echo form_error('jilid', '<div class="text-danger small ml-3">', '</div>') ?>
            </div>
            <button onclick="lihatsantri()" class="btn btn-primary"><i class="fas fa-search"></i> Lihat</button>
        </div>
    </div>
    <div id="data-all-santri"></div>
</div>
</main>

<script>
    function lihatsantri() {
        const thnAjaran = $('#thn_ajaran').val();
        const jilid = $('#jilid').val();

        $.ajax({
            type: 'POST',
            url: '<?= base_url('admin/laporansantri/data_all_santri') ?>',
            data: {
                tahun: thnAjaran,
                id_jilid: jilid
            },
            success: function(response) {
                $('#data-all-santri').html(response);
            },
            error: function(response) {
                $('#data-all-santri').html(response);
            }
        });
    }
</script>