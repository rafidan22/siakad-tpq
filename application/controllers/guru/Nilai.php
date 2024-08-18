<?php
class Nilai extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata['username'])) {
            $this->session->set_flashdata('message', 'Anda Belum Login!');
            redirect('login');
        }

        if ($this->session->userdata['level'] != 'guru') {
            $this->session->set_flashdata('message', 'Anda Belum Login!');
            redirect('login');
        }
    }

    public function index()
    {
        $data       = $this->User_model->get_detail_guru($this->session->userdata['id_user'], $this->session->userdata['level']);
        $guru       = $this->Guru_model->get_detail_data(NULL, $data['id_user']);
        $data = array(
            'id_user'   => $data['id_user'],
            'id_ustadz'   => $guru['id_ustadz'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'     => $data['level'],
            'tahun'     => $this->Tahun_model->get_active_stats(),
            'pengampu'  => $this->Pengajar_model->get_mapel_pengampu($guru['id_ustadz']),
            'menu'      => 'nilai',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'guru'
                ],
                1 => (object)[
                    'name' => 'Nilai',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_guru/sidebar', $data);
        $this->load->view('guru/nilai', $data);
        $this->load->view('templates/footer');
    }

    public function get_mapel()
    {
        $id_jilid   = $this->input->post('id_jilid', TRUE);
        $id_ustadz    = $this->input->post('id_ustadz', TRUE);
        $data       = $this->Pengajar_model->get_mapel_pengampu($id_ustadz, $id_jilid);
        if ($data) {
            echo '<option value="">--Pilih Mata Pelajaran--</option>';
            foreach ($data as $mp) {
                echo "<option value=$mp->id_mapel>$mp->nama_mapel</option>";
            }
        } else {
            echo '<option value="">--Tidak Tersedia--</option>';
        }
    }

    public function kd()
    {
        $id_jilid = $this->input->get('id_jilid', TRUE);
        $id_mapel = $this->input->get('id_mapel', TRUE);
        $nilai    = $this->input->get('nilai', TRUE);


        if (!isset($id_jilid) || !isset($id_mapel) || !isset($nilai)) {
            redirect('error_404');
        }

        $data = $this->User_model->get_detail_guru($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'     => $data['level'],
            'id_jilid'  => $id_jilid,
            'id_mapel'  => $id_mapel,
            'jenis_nilai'   => $nilai,
            'jilid'     => $this->jilid_model->get_detail_data($id_jilid),
            'mapel'     => $this->Mapel_model->get_detail_data($id_mapel),
            'komp_dasar'    => $this->Mapel_model->get_mapel_with_kd_nilai($id_mapel, $id_jilid, $nilai),
            'tahun'         => $this->Tahun_model->get_active_stats(),
            'menu'      => 'nilai',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'guru'
                ],
                1 => (object)[
                    'name' => 'Nilai',
                    'link' => 'guru/nilai'
                ],
                2 => (object)[
                    'name' => 'Detail',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_guru/sidebar', $data);
        $this->load->view('guru/nilai_perkd', $data);
        $this->load->view('templates/footer');
    }

    public function data_nilai_permapel()
    {
        $id_jilid       = $this->input->post('id_jilid', TRUE);
        $id_mapel       = $this->input->post('id_mapel', TRUE);
        $nilai          = $this->input->post('nilai', TRUE);
        $jilid          = $this->jilid_model->get_detail_data($id_jilid);
        $mapel          = $this->Mapel_model->get_detail_data($id_mapel);
        $data_default   = $this->Nilai_model->get_nilai_permapel($id_mapel, $id_jilid, 'default', NULL, $nilai);
        $data_min       = $this->Nilai_model->get_nilai_permapel($id_mapel, $id_jilid, 'min', NULL, $nilai);
        $data_max       = $this->Nilai_model->get_nilai_permapel($id_mapel, $id_jilid, 'max', NULL, $nilai);
        $data_jumlah    = $this->Nilai_model->get_nilai_permapel($id_mapel, $id_jilid, 'jumlah', NULL, $nilai);
        $data_rerata    = $this->Nilai_model->get_nilai_permapel($id_mapel, $id_jilid, 'rerata', NULL, $nilai);
        $daftar_kd      = $this->Nilai_model->get_kd_permapel_result($id_mapel, $id_jilid, $nilai);
        $html           = '';

        if ($id_mapel == null || $id_jilid == null) {
            //id not found
            $html = $html . '<div class="card">
                                <div class="card-body">
                                    <h6 class="text-center">Data Nilai Tidak Tersedia, Silahkan Masukkan Data Yang Diperlukan</h6>
                                </div>
                            </div>';
        } else if ($data_default != null) {
            //awal table
            $html = $html . '<div class="card">
                    <div class="card-header bg-behance">
                        <h6 class="text-white"> ' . $mapel['nama_mapel'] . ' / jilid ' . $jilid['jilid'] . '</h6>
                    </div>
                    <div class="card-body">
                        <a href="' . base_url('guru/nilai/kd?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&nilai=' . $nilai) . '" class="btn btn-primary mb-3"><i class="fas fa-info-circle"></i> Cek Selengkapnya</i></a>
                        <table class="table table-responsive-sm table-bordered table-striped table-sm w-100 d-block d-md-table">
                            <thead>
                                <tr>
                                    <th style="vertical-align : middle;text-align:center;">No</th>
                                    <th style="vertical-align : middle;text-align:center;">Nama</th>';

            //heading table
            foreach ($daftar_kd as $key => $value) {
                $html = $html . '<th style="vertical-align : middle;text-align:center;">' . $value->nama_materi . '</th>';
            }

            //jumlah dan rata-rata
            $html = $html . '<th style="vertical-align : middle;text-align:center;">Jumlah</th>
                            <th style="vertical-align : middle;text-align:center;">Rata-rata</th>
                            </tr></thead><tbody>';

            //body table default
            foreach ($data_default as $dt => $value_dt) {
                $html = $html . '<tr>
                    <td width="20px">' . ++$dt . '</td>
                    <td>' . $value_dt['nama'] . '</td>';
                foreach ($daftar_kd as $kd => $value_kd) {
                    $html = $html . '<td>' . $value_dt[$value_kd->nama_materi] . '</td>';
                }

                $html = $html . "<td>{$value_dt['jumlah']}</td><td>{$value_dt['rerata']}</td></tr>";
            }

            //body table min
            foreach ($data_min as $dt => $value_dt) {
                $html = $html . '<tr><td colspan="100%"></td></tr>';

                $html = $html . '<tr>
                    <td width="20px"></td>
                    <td>MIN</td>';
                foreach ($daftar_kd as $kd => $value_kd) {
                    $html = $html . '<td>' . $value_dt[$value_kd->nama_materi] . '</td>';
                }

                $html = $html . "<td>{$value_dt['jumlah']}</td><td>{$value_dt['rerata']}</td></tr>";
            }

            //body table max
            foreach ($data_max as $dt => $value_dt) {
                $html = $html . '<tr>
                    <td width="20px"></td>
                    <td>MAX</td>';
                foreach ($daftar_kd as $kd => $value_kd) {
                    $html = $html . '<td>' . $value_dt[$value_kd->nama_materi] . '</td>';
                }

                $html = $html . "<td>{$value_dt['jumlah']}</td><td>{$value_dt['rerata']}</td></tr>";
            }

            //body table jumlah
            foreach ($data_jumlah as $dt => $value_dt) {
                $html = $html . '<tr>
                    <td width="20px"></td>
                    <td>JUMLAH</td>';
                foreach ($daftar_kd as $kd => $value_kd) {
                    $html = $html . '<td>' . $value_dt[$value_kd->nama_materi] . '</td>';
                }

                $html = $html . "<td>{$value_dt['jumlah']}</td><td>{$value_dt['rerata']}</td></tr>";
            }

            //body table rerata
            foreach ($data_rerata as $dt => $value_dt) {
                $html = $html . '<tr>
                    <td width="20px"></td>
                    <td>RATA-RATA</td>';
                foreach ($daftar_kd as $kd => $value_kd) {
                    $html = $html . '<td>' . $value_dt[$value_kd->nama_materi] . '</td>';
                }

                $html = $html . "<td>{$value_dt['jumlah']}</td><td>{$value_dt['rerata']}</td></tr>";
            }

            //akhir table
            $html = $html . '</tbody></table></div></div>';
        } else {
            $html = $html . '
                <div class="card">
                    <div class="card-header bg-behance">
                        <h6 class="text-white"> ' . $mapel['nama_mapel'] . ' / jilid ' . $jilid['jilid'] . '</h6>
                    </div>
                    <div class="card-body">
                        <a href="' . base_url('guru/nilai/kd?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&nilai=' . $nilai) . '" class="btn btn-primary mb-3"><i class="fas fa-info-circle"></i> Cek Selengkapnya</i></a>
                        <h6 class="text-center">Data nilai belum lengkap, silahkan cek selengkapnya</h6>
                    </div>
                </div>
            
            ';
        }

        echo ($html);
    }

    public function data_nilai_perkd()
    {
        $id_jilid   = $this->input->post('id_jilid', TRUE);
        $id_mapel   = $this->input->post('id_mapel', TRUE);
        $id_materi      = $this->input->post('id_materi', TRUE);
        $tahun      = $this->input->post('tahun', TRUE);
        $nilai      = $this->input->post('nilai', TRUE);
        $data       = $this->Nilai_model->get_nilai_perkd($id_jilid, $id_mapel, $id_materi, $tahun);
        $jenis      = $this->Nilai_model->get_jenis_nilai_in_perkd($id_jilid, $id_mapel, $id_materi, $tahun);
        $kd         = $this->Mapel_model->get_kd_detail($id_materi);
        $html       = '';
        if ($data != null || $jenis != null) {
            //awal table
            $html = $html . '<div class="card">
                    <div class="card-body">
                        ' . anchor('guru/nilai/input?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&id_materi=' . $id_materi . '&nilai=' . $nilai, '<button class="btn btn-sm btn-primary mb-3 mr-2"><i class="fas fa-plus fa-sm"></i> Tambah Nilai</button>') . '
                        ' . anchor('guru/nilai/archivedata?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&id_materi=' . $id_materi . '&tahun=' . $tahun . '&nilai=' . $nilai, '<button class="btn btn-sm btn-dark mb-3 mr-2"><i class="fas fa-archive fa-sm"></i> Arsip Nilai</button>') . '
                        <h5>' . $kd['nama_materi'] . '</h5>
                        <table class="table table-responsive-sm table-bordered table-striped table-sm w-100 d-block d-md-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;" >No</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;" >Nama</th>';

            //heading button table
            foreach ($jenis as $jn => $value) {
                $html = $html . '<th class="text-center">' .
                    anchor('guru/nilai/edit?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&id_materi=' . $id_materi . '&jenis=' . $value->jenis . '&nilai=' . $nilai, '<div class="btn btn-sm btn-primary mr-1 ml-1 mb-1"><i class="fa fa-edit fa-sm"></i></div>') .
                    '<a href="' . base_url('guru/nilai/archive?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&id_materi=' . $id_materi . '&jenis=' . $value->jenis . '&nilai=' . $nilai) . '" class="btn btn-sm btn-dark mr-1 ml-1 mb-1" onclick="return deleteNilai(event)"><i class="fa fa-archive fa-sm"></i></a>' .
                    '</th>';
            }

            $html = $html . '<th rowspan="2" style="vertical-align : middle;text-align:center;">Jumlah</th>
                            <th rowspan="2" style="vertical-align : middle;text-align:center;">Rata-rata</th>
                            </tr><tr>';

            //heading table
            foreach ($jenis as $jn => $value) {
                $html = $html . '<th>' . $value->jenis . '</th>';
            }

            $html = $html . '</tr></thead><tbody>';

            //body table
            foreach ($data as $dt => $value_dt) {
                $html = $html . '<tr>
                    <td width="20px">' . ++$dt . '</td>
                    <td>' . $value_dt['nama'] . '</td>';
                foreach ($jenis as $jn => $value_jn) {
                    $html = $html . '<td>' . $value_dt[$value_jn->jenis] . '</td>';
                }

                $html = $html . "<td>{$value_dt['jumlah']}</td><td>{$value_dt['rerata']}</td></tr>";
            }

            //akhir table
            $html = $html . '</tbody></table></div></div>';
        } else if ($id_mapel == null || $id_jilid == null || $id_materi == null) {
            //id not found
            $html = $html . '<div class="card">
                                <div class="card-body">
                                    <h6 class="text-center">Data Nilai Tidak Dapat Ditampilkan, Silahkan Pilih Materi</h6>
                                </div>
                            </div>';
        } else {
            //data not found
            $html = $html . '<div class="card">
                                <div class="card-body">
                                    ' . anchor('guru/nilai/input?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&id_materi=' . $id_materi . '&nilai=' . $nilai, '<button class="btn btn-sm btn-primary mb-3 mr-2"><i class="fas fa-plus fa-sm"></i> Tambah Nilai</button>') . '
                                    <h5>' . $kd['nama_materi'] . '</h5>
                                    <h5 class="text-center">Data nilai ' . $kd['nama_materi'] . ' belum tersedia, silahkan klik tambah nilai untuk menambahkan nilai santri</h5>
                                </div>
                            </div>';
        }
        echo ($html);
    }

    // input nilai
    public function input()
    {
        $id_jilid   = $this->input->get('id_jilid', TRUE);
        $id_mapel   = $this->input->get('id_mapel', TRUE);
        $id_materi      = $this->input->get('id_materi', TRUE);
        $nilai      = $this->input->get('nilai', TRUE);
        $tahun      = $this->Tahun_model->get_active_stats();

        if (!isset($id_jilid) || !isset($id_mapel) || !isset($id_materi)) {
            redirect('error_404');
        }

        $result_jenis = array_column($this->Nilai_model->get_jenis_nilai_in_perkd_array($id_jilid, $id_mapel, $id_materi, $tahun['nama']), 'jenis');
        $object_jenis = ['Hafalan', 'Kelancaran', 'Adab'];
        $data = $this->User_model->get_detail_guru($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'     => $data['level'],
            'id_jilid'  => $id_jilid,
            'id_mapel'  => $id_mapel,
            'jenis_penilaian'   => $nilai,
            'tahun'             => $tahun,
            'jilid'     => $this->jilid_model->get_detail_data($id_jilid),
            'mapel'     => $this->Mapel_model->get_detail_data($id_mapel),
            'komp_dasar' => $this->Mapel_model->get_kd_detail($id_materi),
            'pengajar'  => $this->Pengajar_model->get_detail_data_with_jilid_and_mapel($id_jilid, $id_mapel),
            'santri'             => $this->santri_model->get_data_perjilid($id_jilid, $tahun),
            'jenis_nilai' => array_diff($object_jenis, $result_jenis),
            'menu'      => 'nilai',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'guru'
                ],
                1 => (object)[
                    'name' => 'Nilai',
                    'link' => 'guru/nilai'
                ],
                2 => (object)[
                    'name' => 'Detail',
                    'link' => 'guru/nilai/kd?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&nilai=' . $nilai
                ],
                3 => (object)[
                    'name' => 'Input Nilai',
                    'link' => NULL
                ]
            ]
        );

        $this->_rules_persantri($data['santri']);

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header');
            $this->load->view('templates_guru/sidebar', $data);
            $this->load->view('guru/nilai_input', $data);
            $this->load->view('templates/footer');
        } else {
            $this->Nilai_model->input_nilai($data['santri'], $id_materi);
            $this->session->set_flashdata('message', 'Nilai santri Berhasil Ditambahkan!');
            redirect('guru/nilai/kd?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&nilai=' . $nilai);
        }
    }

    //edit nilai
    public function edit()
    {
        $id_jilid   = $this->input->get('id_jilid', TRUE);
        $id_mapel   = $this->input->get('id_mapel', TRUE);
        $id_materi      = $this->input->get('id_materi', TRUE);
        $jenis      = $this->input->get('jenis', TRUE);
        $nilai      = $this->input->get('nilai', TRUE);
        $tahun      = $this->Tahun_model->get_active_stats();

        if (!isset($id_jilid) || !isset($id_mapel) || !isset($id_materi) || !isset($jenis)) {
            redirect('error_404');
        }

        $data = $this->User_model->get_detail_guru($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'       => $data['id_user'],
            'nama'          => $data['nama'],
            'photo'         => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'         => $data['level'],
            'jilid'         => $this->jilid_model->get_detail_data($id_jilid),
            'mapel'         => $this->Mapel_model->get_detail_data($id_mapel),
            'komp_dasar'    => $this->Mapel_model->get_kd_detail($id_materi),
            'pengajar'      => $this->Pengajar_model->get_detail_data_with_jilid_and_mapel($id_jilid, $id_mapel),
            'santri'         => $this->santri_model->get_data_perjilid($id_jilid, $tahun),
            'nilai'         => $this->Nilai_model->detail_nilai_perkd($id_jilid, $id_mapel, $id_materi, $jenis, $tahun['nama']),
            'jenis_nilai'   => $jenis,
            'jenis_penilaian'   => $nilai,
            'tahun'         => $tahun,
            'menu'          => 'nilai',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'guru'
                ],
                1 => (object)[
                    'name' => 'Nilai',
                    'link' => 'guru/nilai'
                ],
                2 => (object)[
                    'name' => 'Detail',
                    'link' => 'guru/nilai/kd?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&nilai=' . $nilai
                ],
                3 => (object)[
                    'name' => 'Edit Nilai',
                    'link' => NULL
                ]
            ]
        );

        $this->_rules_persantri($data['nilai']);

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header');
            $this->load->view('templates_admin/sidebar', $data);
            $this->load->view('guru/nilai_edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->Nilai_model->update_nilai($data['santri'], $id_materi, $jenis);
            $this->session->set_flashdata('message', 'Nilai santri Berhasil Diupdate!');
            redirect('guru/nilai/kd?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&nilai=' . $nilai);
        }
    }

    public function archivedata()
    {
        $id_jilid   = $this->input->get('id_jilid', TRUE);
        $id_mapel   = $this->input->get('id_mapel', TRUE);
        $id_materi      = $this->input->get('id_materi', TRUE);
        $tahun      = $this->input->get('tahun', TRUE);
        $nilai      = $this->input->get('nilai', TRUE);

        if (!isset($id_jilid) || !isset($id_mapel) || !isset($nilai) || !isset($id_materi) || !isset($tahun)) {
            redirect('error_404');
        }

        $data_nilai     = $this->Arsip_model->get_nilai_perkd($id_jilid, $id_mapel, $id_materi, $tahun);
        $jenis          = $this->Arsip_model->get_jenis_nilai_in_perkd($id_jilid, $id_mapel, $id_materi, $tahun);
        $kd             = $this->Mapel_model->get_kd_detail($id_materi);
        $result_jenis   = array_column($this->Nilai_model->get_jenis_nilai_in_perkd_array($id_jilid, $id_mapel, $id_materi, $tahun), 'jenis');
        $object_jenis   = ['Hafalan', 'Kelancaran', 'Adab'];

        $data       = $this->User_model->get_detail_guru($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'       => $data['id_user'],
            'nama'          => $data['nama'],
            'photo'         => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'         => $data['level'],
            'id_jilid'      => $id_jilid,
            'id_mapel'      => $id_mapel,
            'jenis_nilai'   => $nilai,
            'jilid'         => $this->jilid_model->get_detail_data($id_jilid),
            'mapel'         => $this->Mapel_model->get_detail_data($id_mapel),
            'kd'            => $kd,
            'tahun'         => $this->Tahun_model->get_active_stats(),
            'jenis'         => $jenis,
            'data'          => $data_nilai,
            'jenis_penilai' => array_diff($object_jenis, $result_jenis),
            'menu'          => 'nilai',
            'breadcrumb'    => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'guru'
                ],
                1 => (object)[
                    'name' => 'Nilai',
                    'link' => 'guru/nilai'
                ],
                2 => (object)[
                    'name' => 'Detail',
                    'link' => 'guru/nilai/kd?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&nilai=' . $nilai
                ],
                3 => (object)[
                    'name' => 'Arsip',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_guru/sidebar', $data);
        $this->load->view('guru/nilai_arsip', $data);
        $this->load->view('templates/footer');
    }

    public function archive()
    {
        $id_jilid   = $this->input->get('id_jilid', TRUE);
        $id_mapel   = $this->input->get('id_mapel', TRUE);
        $id_materi      = $this->input->get('id_materi', TRUE);
        $jenis      = $this->input->get('jenis', TRUE);
        $nilai      = $this->input->get('nilai', TRUE);
        $tahun      = $this->Tahun_model->get_active_stats();

        $getnilai   = $this->Nilai_model->detail_nilai_perkd($id_jilid, $id_mapel, $id_materi, $jenis, $tahun['nama']);
        $this->Arsip_model->input_nilai($getnilai);
        $this->_delete($id_jilid, $id_materi, $jenis, $id_mapel, $nilai, $tahun);
    }

    public function archive_cancel()
    {
        $id_jilid   = $this->input->get('id_jilid', TRUE);
        $id_mapel   = $this->input->get('id_mapel', TRUE);
        $id_materi      = $this->input->get('id_materi', TRUE);
        $old_jenis  = $this->input->get('oldjenis', TRUE);
        $new_jenis  = $this->input->get('newjenis', TRUE);
        $nilai      = $this->input->get('nilai', TRUE);
        $tahun      = $this->Tahun_model->get_active_stats();

        $getnilai   = $this->Arsip_model->detail_nilai_perkd($id_jilid, $id_mapel, $id_materi, $old_jenis, $tahun['nama']);
        $this->Arsip_model->cancel_nilai($getnilai, $new_jenis);
        $this->_delete_archive($id_jilid, $id_materi, $old_jenis, $id_mapel, $nilai, $tahun);
    }

    private function _delete($id_jilid, $id_materi, $jenis, $id_mapel, $nilai, $tahun)
    {
        if (!isset($id_jilid) || !isset($id_materi) || !isset($jenis) || !isset($id_mapel)) {
            redirect('error_404');
        }

        $this->Nilai_model->delete_nilai($id_jilid, $id_materi, $jenis, $tahun['nama']);
        $this->session->set_flashdata('message', 'Data Nilai Berhasil Diarsipkan!');
        redirect('guru/nilai/kd?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&nilai=' . $nilai);
    }

    private function _delete_archive($id_jilid, $id_materi, $jenis, $id_mapel, $nilai, $tahun)
    {
        if (!isset($id_jilid) || !isset($id_materi) || !isset($jenis) || !isset($id_mapel)) {
            redirect('error_404');
        }

        $this->Arsip_model->delete_nilai($id_jilid, $id_materi, $jenis, $tahun['nama']);
        $this->session->set_flashdata('message', 'Data Nilai Berhasil Dipindah!');
        redirect('guru/nilai/kd?id_jilid=' . $id_jilid . '&id_mapel=' . $id_mapel . '&nilai=' . $nilai);
    }

    private function _rules_persantri($data_santri)
    {
        foreach ($data_santri as $key => $value) {
            $this->form_validation->set_rules('nilai' . $key, 'Nilai', 'required|numeric');
        }
        $this->form_validation->set_rules('jenis', 'Jenis Nilai', 'required');
    }
}
