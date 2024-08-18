<div class="container-fluid">
    <?php if ($this->session->flashdata('message')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('message'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-users mr-3"></i>Form Update/Kenaikan Jilid Peserta Didik
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/pesertadidik/updatejilid') ?>" method="post">
                <div class="row">
                    <input type="hidden" id="oldtahun" name="oldtahun" value="<?= $tahun ?>">
                    <input type="hidden" id="newtahun" name="newtahun" value="<?= $tahun ?>">
                    <div class="form-group col-sm-3">
                        <label for="oldjilid">Jilid Lama</label>
                        <div class="input-group control-group after-add-jilid">
                            <select class="form-control" id="oldjilid" name="oldjilid">
                                <option value="">--Pilih Jilid--</option>
                                <?php foreach ($jilid as $kl) : ?>
                                    <option value="<?php echo $kl->id_jilid ?>"><?= $kl->jilid ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-btn">
                                <button class="btn btn-primary add-jilid ml-1" onclick="previewOldPeserta()" type="button"><i class="fas fa-search"></i> Preview</i></button>
                            </div>
                        </div>
                        <?php echo form_error('oldjilid', '<div class="text-danger small ml-3">', '</div>') ?>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="newjilid">Jilid Baru</label>
                        <div class="input-group control-group after-add-jilid">
                            <select class="form-control" id="newjilid" name="newjilid">
                                <option value="">--Pilih Jilid--</option>
                                <?php foreach ($jilid as $kl) : ?>
                                    <option value="<?php echo $kl->id_jilid ?>"><?= $kl->jilid ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-btn">
                                <button class="btn btn-primary add-jilid ml-1" onclick="previewNewPeserta()" type="button"><i class="fas fa-search"></i> Preview</i></button>
                            </div>
                        </div>
                        <?php echo form_error('newjilid', '<div class="text-danger small ml-3">', '</div>') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6" id="data-old">
                    </div>
                    <div class="form-group col-sm-6" id="data-new">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="reset" class="btn btn-secondary ml-1">Reset</button>
            </form>
        </div>
    </div>
</div>
</main>

<script>
    function previewOldPeserta() {
        const nameTahun = $('#oldtahun').val();
        const idjilid = $('#oldjilid').val();

        console.log(nameTahun + '=' + idjilid);

        if (nameTahun != "" && idjilid != "") {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('admin/pesertadidik/previewold') ?>',
                data: {
                    id_jilid: idjilid,
                    tahun: nameTahun
                },
                success: function(response) {
                    $('#data-old').html(response);
                },
                error: function(response) {
                    $('#data-old').html(response);
                }
            });
        }
    }

    function previewNewPeserta() {
        const nameTahun = $('#newtahun').val();
        const idjilid = $('#newjilid').val();

        console.log(nameTahun + '=' + idjilid);

        if (nameTahun != "" && idjilid != "") {
            $.ajax({
                type: 'POST',
                url: '<?= base_url('admin/pesertadidik/previewnew') ?>',
                data: {
                    id_jilid: idjilid,
                    tahun: nameTahun
                },
                success: function(response) {
                    $('#data-new').html(response);
                },
                error: function(response) {
                    $('#data-new').html(response);
                }
            });
        }
    }
</script>
