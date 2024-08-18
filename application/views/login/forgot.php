<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/style-login.css" />
<title>Login</title>
</head>

<body>
    <div class="container-login">
        <div class="forms-container">
            <div class="signin">
                <form method="post" action="" class="sign-in-form">
                    <img src="<?php echo base_url() ?>assets/img/logo_dikdasmen.svg" alt="" class="image-logo">
                    <span class="mb-2 text-center">Masukan Email Jika Sebagai Admin, Guru atau ustadz,<br> Masukan No. Hp Jika Sebagai Orang Tua santri</span>
                    <?php if ($this->session->flashdata('message')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $this->session->flashdata('message'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="datadiri">Data</label>
                                <input class="form-control" id="datadiri" type="text" placeholder="Email / No. Hp">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="ccmonth">Sebagai</label>
                            <select class="form-control" id="ccmonth">
                                <option>Admin</option>
                                <option>ustadz</option>
                                <option>Guru</option>
                                <option>Orang Tua</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn solid">Reset Password</button>
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h1>TPQ Bustanul Ulum</h1>
                    <p>
                        Sistem Informasi Perkembangan santri
                    </p>
                </div>
                <img src="<?php echo base_url() ?>assets/img/school.svg" class="image" alt="" />
            </div>
        </div>
    </div>