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
        <span style="font-size: 20px">LAPORAN DATA GURU</span>
        <br>
        <b>TAHUN AJARAN <?= $tahun['nama'] ?></b>
    </p>
    <table class="table table-bordered" id="table-laporanguru">
        <thead>
            <tr>
                <th width="15px" class="text-center">No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th width="20px" class="text-center">L/P</th>
                <th>Tanggal Lahir</th>
                <th>Jabatan</th>
                <th>jilid Mengajar</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $key => $value) :  ?>
                <?php
                $map_jilid = explode(',', $value->jilid);
                $uniqe_jilid = array_unique($map_jilid);
                sort($uniqe_jilid);
                $new_jilid = implode(', ', $uniqe_jilid);
                ?>
                <tr>
                    <td widtd="20px"><?= ++$key ?></td>
                    <td><?= $value->nama ?></td>
                    <td><?= $value->nip ?></td>
                    <td><?= $value->jenis_kelamin == 'Perempuan' ? 'P' : 'L' ?></td>
                    <td><?= $value->tanggal_lahir ?></td>
                    <td><?= $value->jabatan ?></td>
                    <td><?= $new_jilid ?></td>
                    <td><?= $value->alamat ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</body>

</html>