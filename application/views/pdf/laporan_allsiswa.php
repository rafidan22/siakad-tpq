<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="google" content="notranslate">

    <!-- Main styles for this application-->
    <link href="<?= base_url() ?>assets/css/style.css" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link href="<?= base_url() ?>assets/vendors/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url() ?>assets/vendors/jquery/jquery.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url() ?>assets/vendors/jquery-easing/jquery.easing.min.js"></script>

    <title>Laporan Daftar Guru</title>
</head>

<body style="background-color: white; color: black;">
    <img src="<?= base_url('assets/img/logo.jpg') ?>" style="position: absolute; width: 110px; height: auto;">
    <table style="width: 100%;">
        <tr>
            <td align="center">
                <span style="line-height: 1.6; font-weight: bold; font-size: 28px; color: black;">SD MUHAMMDIYAH TRINI</span>
                <br>TRINI 005/016, TRIHANGGO, GAMPING, SLEMAN
                <br>Telepon : (0274) 292 00 66
                <br>E-mail : sdmuh_trini@yahoo.com Website : www.sdmuhtrini.webs.com
            </td>
        </tr>
    </table>

    <hr class="line-title">
    <p align="center">
        <span style="font-size: 20px">LAPORAN DATA santri</span>
        <br>
        <b>TAHUN AJARAN <?= $tahun['nama'] ?></b>
        <br>
        <?= 'jilid ' . $jilid['jilid'] ?>
    </p>
    <table class="table table-bordered" id="table-laporansantri">
        <thead>
            <tr class="text-center">
                <th width="10px" rowspan="2" style="vertical-align : middle;text-align:center;">No</th>
                <th width="10px" rowspan="2" style="vertical-align : middle;text-align:center;">NIS</th>
                <th rowspan="2" style="vertical-align : middle;text-align:center;">tgl_masuk</th>
                <th rowspan="2" style="vertical-align : middle;text-align:center;">Nama</th>
                <th width="20px" rowspan="2" style="vertical-align : middle;text-align:center;">L/P</th>
                <th rowspan="2" style="vertical-align : middle;text-align:center;">Tanggal Lahir</th>
                <th rowspan="2" style="vertical-align : middle;text-align:center;">asal_sekolah</th>
                <th colspan="4">Alamat</th>
            </tr>
            <tr class="text-center">
                <th>Dusun</th>
                <th>Desa</th>
                <th>Kecamatan</th>
                <th>Kabupaten</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $key => $value) :  ?>
                <tr>
                    <td widtd="10px"><?= ++$key ?></td>
                    <td widtd="10px"><?= $value->nis ?></td>
                    <td><?= $value->tgl_masuk ?></td>
                    <td><?= $value->nama ?></td>
                    <td width="20px"><?= $value->jenis_kelamin == 'Perempuan' ? 'P' : 'L' ?></td>
                    <td><?= $value->tanggal_lahir ?></td>
                    <td><?= $value->asal_sekolah ?></td>
                    <td><?= $value->dusun ?></td>
                    <td><?= $value->desa ?></td>
                    <td><?= $value->kecamatan ?></td>
                    <td><?= $value->kabupaten ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</body>

</html>