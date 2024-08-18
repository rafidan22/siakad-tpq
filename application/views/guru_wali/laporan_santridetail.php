<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> <i class="fas fa-sticky-note"></i> Laporan Detail santri</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <a href="<?= base_url('walikelas/laporansantri/pdf_laporan?q=detaildata&id=' . $id_santri) ?> " class="btn btn-info mb-2"><i class="fas fa-print"></i> Print</a>
            <legend class="mt-3 text-center">
                <h2>Laporan Data santri</h2>
            </legend>

            <div class="row mr-5">
                <div class="col-sm-3" class="text-center">
                    <div id="photo" class="mb-3 mt-3 text-center">
                        <?php if ($santri['photo']) : ?>
                            <img src="<?= base_url('assets/photos/' . $santri['photo']) ?>" alt="photo <?= $santri['nama'] ?>" style="width: 200px; height: 300px; border-radius: 15px;">
                        <?php else : ?>
                            <img src="<?= base_url('assets/photos/user-placeholder.jpg') ?>" alt="photo <?= $santri['nama'] ?>" style="width: 200px; height: 300px; border-radius: 15px;">
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-sm-9">
                    <table class="table table-borderless no-margin table-striped">
                        <thead>
                            <tr>
                                <th colspan="2">DATA DIRI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="300px">NIS</td>
                                <td><?= $santri['nis'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">tgl_masuk</td>
                                <td><?= $santri['tgl_masuk'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">NAMA</td>
                                <td><?= $santri['nama'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">JENIS KELAMIN</td>
                                <td><?= $santri['jenis_kelamin'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">TANGGAL LAHIR</td>
                                <td><?= $santri['tanggal_lahir'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">asal_sekolah</td>
                                <td><?= $santri['asal_sekolah'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-borderless no-margin table-striped">
                        <thead>
                            <tr>
                                <th colspan="2">DATA ORANG TUA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="300px">NAMA AYAH</td>
                                <td><?= $santri['nama_ayah'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">PENDIDIKAN AYAH</td>
                                <td><?= $santri['pendidikan_ayah'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">PEKERJAAN AYAH</td>
                                <td><?= $santri['pekerjaan_ayah'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">NAMA IBU</td>
                                <td><?= $santri['nama_ibu'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">PENDIDIKAN IBU</td>
                                <td><?= $santri['pendidikan_ibu'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">PEKERJAAN IBU</td>
                                <td><?= $santri['pekerjaan_ibu'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">NO. HANDPHONE</td>
                                <td><?= $santri['no_hp'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-borderless no-margin table-striped">
                        <thead>
                            <tr>
                                <th colspan="2">ALAMAT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="300px">DUSUN</td>
                                <td><?= $santri['dusun'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">DESA</td>
                                <td><?= $santri['desa'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">KECAMATAN</td>
                                <td><?= $santri['kecamatan'] ?></td>
                            </tr>
                            <tr>
                                <td width="300px">KABUPATEN</td>
                                <td><?= $santri['kabupaten'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</main>