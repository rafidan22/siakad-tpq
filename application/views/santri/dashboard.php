<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
    </div>

    <!-- Welcome Alert -->
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Selamat Datang!</h4>
        <p>Selamat Datang Orang Tua dari <strong><?php echo $nama; ?></strong> di Sistem Informasi Perkembangan Santri TPQ Bustanul Ulum</p>
        <hr>
    </div>

    <div class="row">
        <!-- jilid Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">jilid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $jilid ? $jilid['jilid'] : 'Belum Ditentukan'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ustadz Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ustadz/Ustadzah</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $jilid ? $jilid['ustadz_ustadzah'] : 'Belum Ditentukan'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</main>
