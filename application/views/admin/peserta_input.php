<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-users mr-3"></i>Form Tambah Data Santri ke Jilid
            </div>
        <div class="card-body">
    <form action="" method="post">
        <div class="form-group">
                    <label for="jilid">Jilid</label>
                    <select class="form-control" id="jilid" name="jilid">
                        <option value="">--Pilih Jilid--</option>
                        <?php foreach ($jilid as $kl) : ?>
                            <option value="<?php echo $kl->id_jilid ?>"><?= $kl->jilid ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('jilid', '<div class="text-danger small ml-3">', '</div>') ?>
                </div>
                <div class="form-group">
                    <label class="col-form-label" for="santri">Santri</label>
                    <div class="">
                        <select class="form-control" id="santri" name="santri[]" size="20" multiple="">
                            <option value="">ID Santri - NIS - Nama Santri</option>
                            <?php foreach ($santri as $sw) : ?>
                                <option value="<?php echo $sw->id_santri ?>"><?= "$sw->nis - $sw->tgl_masuk - $sw->nama" ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php echo form_error('santri[]', '<div class="text-danger small ml-3">', '</div>') ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="reset" class="btn btn-secondary ml-1">Reset</button>
            </form>
        </div>
    </div>
</div>
</main>