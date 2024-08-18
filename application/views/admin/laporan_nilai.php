<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> <i class="fas fa-sticky-note"></i> Laporan Daftar Nilai</h1>
    </div>
    <div class="card">
        <div class="card-header bg-behance">
            <h6 class="text-white">Masukkan Data Yang Diperlukan</h6>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="thn_ajaran">Tahun</label>
                <select class="form-control" id="thn_ajaran" name="thn_ajaran">
                    <option value="">--Pilih Tahun--</option>
                    <?php foreach ($tahun as $th) : ?>
                        <option value="<?php echo $th->id_tahun ?>"><?= $th->nama ?> </option>
                    <?php endforeach; ?>
                </select>
                <?php echo form_error('thn_ajaran', '<div class="text-danger small ml-3">', '</div>') ?>
            </div>
            <div class="form-group">
                <label for="jilid">Jilid</label>
                <select class="form-control" id="jilid" name="jilid">
                    <option value="">--Pilih Jilid--</option>
                </select>
                <?php echo form_error('jilid', '<div class="text-danger small ml-3">', '</div>') ?>
            </div>
            <div class="form-group">
                <label for="penilaian">Penilaian</label>
                <select class="form-control" id="penilaian" name="penilaian">
                    <option value="">--Pilih Penilaian--</option>
                    <option value="Individu">Penilaian Individu</option>
                    <option value="Tambahan">Penilaian Tambahan</option>
                </select>
                <?php echo form_error('penilaian', '<div class="text-danger small ml-3">', '</div>') ?>
            </div>
            <button onclick="lihatNilai()" class="btn btn-primary"><i class="fas fa-search"></i> Lihat</button>
        </div>
    </div>
    <div id="data-all-nilai"></div>
</div>
</main>

<script>
    $(document).ready(function() {
        $('#thn_ajaran').change(function() {
            const tahun = $(this).val();
            $.ajax({
                type: 'POST',
                url: '<?= base_url('admin/laporannilai/get_jilid') ?>',
                data: {
                    id_tahun: tahun,
                },
                success: function(response) {
                    $('#jilid').html(response);
                }
            });
        })
    });

    function lihatNilai() {
        const thnAjaran = $('#thn_ajaran').val();
        const jilid = $('#jilid').val();
        const jetgl_masukilai = $('#penilaian').val();

        $.ajax({
            type: 'POST',
            url: '<?= base_url('admin/laporannilai/data_all_nilai') ?>',
            data: {
                id_tahun: thnAjaran,
                id_jilid: jilid,
                nilai: jetgl_masukilai
            },
            success: function(response) {
                $('#data-all-nilai').html(response);
            },
            error: function(response) {
                $('#data-all-nilai').html(response);
            }
        });
    }
</script>