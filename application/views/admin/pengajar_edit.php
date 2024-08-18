<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <i class="fas fa-chalkboard-teacher mr-3"></i>Form Update Data Pengajar
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group">
                    <label for="guru">Nama Ustadz/Ustadzah</label>
                    <select class="form-control" id="guru" name="guru">
                        <?php foreach ($guru as $gr) : ?>
                            <?php if ($gr->id_ustadz == $pengajar['id_ustadz']) : ?>
                                <option value="<?php echo $gr->id_ustadz ?>" selected><?php echo $gr->nama ?></option>
                            <?php else : ?>
                                <option value="<?php echo $gr->id_ustadz ?>"><?php echo $gr->nama ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('guru', '<div class="text-danger small ml-3">', '</div>') ?>
                </div>
                <div class="form-group">
                    <label for="mapel">Pembelajaran/Jilid</label>
                    <select class="form-control" id="mapel" name="mapel">
                        <?php foreach ($mapel as $mp) : ?>
                            <?php if ($mp->id_mapel == $pengajar['id_mapel']) : ?>
                                <option value="<?php echo $mp->id_mapel ?>" selected><?= $mp->nama_mapel ?> / <?= $mp->level ?></option>
                            <?php else : ?>
                                <option value="<?php echo $mp->id_mapel ?>"><?= $mp->nama_mapel ?> / <?= $mp->level ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('mapel', '<div class="text-danger small ml-3">', '</div>') ?>
                </div>
                <div class="form-group">
                    <label for="jilid">Jilid</label>
                    <select class="form-control" id="jilid" name="jilid">
                        <?php foreach ($jilid as $kl) : ?>
                            <?php if ($kl->id_jilid == $pengajar['id_jilid']) : ?>
                                <option value="<?php echo $kl->id_jilid ?>" selected><?= $kl->jilid ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('jilid', '<div class="text-danger small ml-3">', '</div>') ?>
                </div>
                <div class="form-group">
                    <label for="jabatan">Jabatan</label>
                    <select class="form-control" id="jabatan" name="jabatan">
                        <?php foreach ($jabatan as $jb) : ?>
                            <?php if ($jb == $pengajar['jabatan']) : ?>
                                <option value="<?php echo $jb ?>" selected><?= $jb ?></option>
                            <?php else : ?>
                                <option value="<?php echo $jb ?>"><?= $jb ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('jabatan', '<div class="text-danger small ml-3">', '</div>') ?>
                </div>
                <div class="form-group">
                    <label for="tahun"></label>
                    <input type="Hidden" name="tahun" id="tahun" placeholder="Masukan Tahun Ajaran" class="form-control" value="<?= $tahun['nama'] . ' - ' . $tahun['toshi'] ?>" disabled>
                    <?php echo form_error('tahun', '<div class="text-danger small ml-3">', '</div>') ?>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="reset" class="btn btn-secondary ml-1">Reset</button>
            </form>
        </div>
    </div>
</div>

</main>

<script>
    $(document).ready(function() {
        $('#mapel').change(function() {
            const mapel = $(this).val();
            $.ajax({
                type: 'POST',
                url: '<?= base_url('admin/pengajar/get_jilid') ?>',
                data: {
                    id_mapel: mapel
                },
                success: function(response) {
                    $('#jilid').html(response);
                }
            });
        })
    });
</script>