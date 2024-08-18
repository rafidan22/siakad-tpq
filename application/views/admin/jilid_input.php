<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <i class="fas fa-chalkboard mr-3"></i>Form Tambah Data Jilid
         </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group">
                    <label for="jilid">Jilid</label>
                    <select class="form-control" id="jilid" name="jilid">
                        <option value="">--Pilih Level--</option>
                        <option value="Jilid 1">Jilid 1</option>
                        <option value="Jilid 2">Jilid 2</option>
                        <option value="Jilid 3">Jilid 3</option>
                        <option value="Jilid 4">Jilid 4</option>
                        <option value="Jilid 5">Jilid 5</option>
                        <option value="Jilid 6">Jilid 6</option>
                    </select>
                    <?php echo form_error('level', '<div class="text-danger small ml-3">', '</div>') ?>
                </div>
                <div class="form-group">
                    <label for="ustadz_ustadzah">Ustadz</label>
                    <select class="form-control" id="ustadz_ustadzah" name="ustadz_ustadzah">
                        <option value="">--Pilih Ustadz/Ustadzah--</option>
                        <?php foreach ($guru as $gr) : ?>
                            <option value="<?= $gr->nama . '-' . $gr->id_user ?>"><?= $gr->nama ?></option>
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