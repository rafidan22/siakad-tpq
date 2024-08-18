<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <i class="fas fa-chalkboard-teacher mr-3"></i>Form Update Data Jilid
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group">
                    <label for="jilid">Jilid</label>
                    <input type="text" name="jilid" id="jilid" placeholder="Masukan Jilid" class="form-control" value="<?php echo $jilid['jilid']; ?>">
                    <?php echo form_error('jilid', '<div class="text-danger small ml-3">', '</div>') ?>
                </div>
                <div class="form-group">
                    <label for="ustadz_ustadzah">Ustadz</label>
                    <select class="form-control" id="ustadz_ustadzah" name="ustadz_ustadzah">
                        <option value="<?= $jilid['ustadz_ustadzah'] . '-' . $get_user_id['id_user'] ?>" selected><?= $jilid['ustadz_ustadzah'] ?></option>
                        <?php foreach ($guru as $gr) : ?>
                            <option value="<?= $gr->nama . '-' . $gr->id_user ?>"><?php echo $gr->nama ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('ustadz_ustadzah', '<div class="text-danger small ml-3">', '</div>') ?>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="reset" class="btn btn-secondary ml-1">Reset</button>
            </form>
        </div>
    </div>
</div>

</main>