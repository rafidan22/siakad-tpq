<div class="container-fluid">
    <!-- Page Heading -->
    <?php $button = ($tahun) ? 'enabled' : 'disabled'; ?>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-sort-numeric-down"></i> Data Nilai <?= $jilid['jilid'] ?></h1>
    </div>

    <div class="row">

        <div class="col-sm-3">
            <div class="card">
                <div class="card-header bg-behance">
                    <h6 class="text-white">Masukkan Data Yang Diperlukan</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="mapel">Pembelajaran Jilid</label>
                        <select class="form-control" id="mapel" name="mapel">
                            <?php if ($mapel->num_rows() > 0) {
                                echo '<option value="">--Pilih Pembelajaran--</option>';
                                foreach ($mapel->result() as $mp) {
                                    echo "<option value=$mp->id_mapel>$mp->nama_mapel</option>";
                                }
                            } else {
                                echo '<option value="">--Tidak Tersedia--</option>';
                            } ?>
                        </select>
                        <?php echo form_error('mapel', '<div class="text-danger small ml-3">', '</div>') ?>
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
                    <button onclick="searchNilai()" class="btn btn-primary" <?= $button ?>><i class="fas fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        <div class="col-sm-9" id="table-result">

        </div>
    </div>
</div>
</main>

<script>
    function searchNilai() {
        const idjilid = <?= $jilid['id_jilid'] ?>;
        const idMapel = $('#mapel').val();
        const jetgl_masukilai = $('#penilaian').val();

        // if (idjilid !== '' && idMapel !== '') {
        $.ajax({
            type: 'POST',
            url: '<?= base_url('walikelas/nilai/data_nilai_permapel') ?>',
            data: {
                id_jilid: idjilid,
                id_mapel: idMapel,
                nilai: jetgl_masukilai
            },
            success: function(response) {
                $('#table-result').html(response);
            },
            error: function(response) {
                $('#table-result').html(response);
            }
        });
        // }
    }
</script>