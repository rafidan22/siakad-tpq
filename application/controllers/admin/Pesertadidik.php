<?php
class PesertaDidik extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->output->set_header('Cache-Control: no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');

        if (!isset($this->session->userdata['username']) && $this->session->userdata['level'] != 'admin') {
            $this->session->set_flashdata('message', 'Anda Belum Login!');
            redirect('login');
        }

        if ($this->session->userdata['level'] != 'admin') {
            $this->session->set_flashdata('message', 'Anda Belum Login!');
            redirect('login');
        }
    }

    public function index()
    {
        $data = $this->User_model->get_detail_admin($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'     => $data['level'],
            'tahun'     => $this->Tahun_model->get_active_stats(),
            'jilid'     => $this->jilid_model->get_data(),
            'menu'      => 'peserta',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'Jilid/Naik',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_admin/sidebar', $data);
        $this->load->view('admin/peserta', $data);
        $this->load->view('templates/footer');
    }

    public function input()
    {

        $gettahun= $this->Tahun_model->get_active_stats();
        $data = $this->User_model->get_detail_admin($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'     => $data['level'],
            'tahun'     => $gettahun,
            'jilid'     => $this->jilid_model->get_data(),
            'santri'     => $this->santri_model->get_data_tahun($gettahun['nama']),
            'menu'      => 'peserta',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'Jilid/Naik',
                    'link' => 'admin/pesertadidik'
                ],
                2 => (object)[
                    'name' => 'Input',
                    'link' => NULL
                ]
            ]
        );

        $this->_rules_input();

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header');
            $this->load->view('templates_admin/sidebar', $data);
            $this->load->view('admin/peserta_input', $data);
            $this->load->view('templates/footer');
        } else {
            $tahun = $data['tahun']['nama'];
            $this->Peserta_model->input_data($tahun);
            $this->session->set_flashdata('message', 'Data Santri Berhasil Ditambahkan!');
            redirect('admin/pesertadidik');
        }
    }

    public function data_peserta()
    {
        $id_jilid       = $this->input->post('id_jilid', TRUE);
        $tahun          = $this->input->post('tahun', TRUE);
        $data_peserta   = $this->Peserta_model->get_data_jilid($id_jilid, $tahun);
        $html       = "";
        if ($data_peserta) {
            $html = $html . '
            <div class="card">
                <div class="card-body">
                    <table class="table table-responsive-sm table-bordered table-striped table-sm w-100 d-block d-md-table" id="table-peserta">
                        <thead>
                            <tr>
                                <th class="text-center" style="vertical-align : middle;text-align:center;" width="20px">No</th>
                                <th style="vertical-align : middle;text-align:center;">ID Santri</th>
                                <th style="vertical-align : middle;text-align:center;">Tanggal Masuk TPQ</th>
                                <th style="vertical-align : middle;text-align:center;">Nama</th>
                                <th style="vertical-align : middle;text-align:center;">Jenis Kelamin</th>
                                <th style="vertical-align : middle;text-align:center;">Asal Sekolah</th>
                                <th class="text-center" width="80px" style="vertical-align : middle;text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($data_peserta as $key => $value) {
                $html = $html . '<tr>
                                <td class="text-center" style="vertical-align : middle;text-align:center;" widtd="20px">' . ++$key . '</td>
                                <td style="vertical-align : middle;text-align:center;">' . $value->nis . '</td>
                                <td style="vertical-align : middle;text-align:center;">' . $value->tgl_masuk . '</td>
                                <td style="vertical-align : middle;text-align:left;">' . $value->nama . '</td>
                                <td style="vertical-align : middle;text-align:center;">' . $value->jenis_kelamin . '</td>
                                <td style="vertical-align : middle;text-align:center;">' . $value->asal_sekolah . '</td>
                                <td class="text-center" width="80px" style="vertical-align : middle;text-align:center;"><a href="javascript:;" id="data-santri" data-idsantri="' . $value->id_datasantri . '" class="btn btn-sm btn-danger btn-delete-guru btn-xs mr-1 ml-1 mb-1"><i class="fa fa-trash"></i></a></td>
                            </tr>';
            }

            $html = $html . '                    
                        </tbody>
                    </table>
                </div>
            </div>';
        } else {
            $html = $html . '<div class="card">
                                <div class="card-body">
                                    <h6 class="text-center">Data Santri Belum Tersedia</h6>
                                </div>
                            </div>';
        }

        echo $html;
    }

    public function updatejilid()
    {
        $data = $this->User_model->get_detail_admin($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'     => $data['level'],
            'tahun'     => $this->Tahun_model->get_active_stats()['nama'],
            'nama_tahun' => $this->Tahun_model->get_name_data(),
            'jilid'     => $this->jilid_model->get_data(),
            'santri'    => $this->santri_model->get_data(),
            'menu'      => 'peserta',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'Jilid/Naik',
                    'link' => 'admin/pesertadidik'
                ],
                2 => (object)[
                    'name' => 'Update/Kenaikan Jilid',
                    'link' => NULL
                ]
            ]
        );

        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header');
            $this->load->view('templates_admin/sidebar', $data);
            $this->load->view('admin/peserta_update', $data);
            $this->load->view('templates/footer');
        } else {
            $this->Peserta_model->update_jilid();
            $this->session->set_flashdata('message', 'Data Santri Berhasil Naik Jilid!');
            redirect('admin/pesertadidik');
        }
    }

    
    public function previewold()
    {
        $id_jilid       = $this->input->post('id_jilid', TRUE);
        $tahun          = $this->input->post('tahun', TRUE);
        $data_peserta   = $this->Peserta_model->get_data_jilid($id_jilid, $tahun);
        $jilid          = $this->jilid_model->get_detail_data($id_jilid);
        $html = "";
        $html = $html . '<label class="col-form-label" for="old-daftar">Daftar Santri Lama Jilid ' . $jilid['jilid'] . '</label>
                        <div class="">
                            <select class="form-control" id="old-daftar" name="old-daftar" size="20" multiple="">
                                <option value="">ID santri - Tanggal Masuk TPQ - Nama santri</option>';
        if ($data_peserta) {
            foreach ($data_peserta as $dp) {
                $html = $html . '<option value="">' . $dp->nis . '-' . $dp->tgl_masuk . '-' . $dp->nama . '</option>';
            }
        }

        $html = $html . '</select></div>';
        echo $html;
    }

    public function previewnew()
    {
        $id_jilid       = $this->input->post('id_jilid', TRUE);
        $tahun          = $this->input->post('tahun', TRUE);
        $data_peserta   = $this->Peserta_model->get_data_jilid($id_jilid, $tahun);
        $jilid          = $this->jilid_model->get_detail_data($id_jilid);
        $html = "";
        $html = $html . '<label class="col-form-label" for="new-daftar">Daftar Santri Baru Jilid ' . $jilid['jilid'] . '</label>
                        <div class="">
                            <select class="form-control" id="new-daftar" name="new-daftar" size="20" multiple="">
                                <option value="">ID santri - Tanggal Masuk TPQ - Nama santri</option>';
        if ($data_peserta) {
            foreach ($data_peserta as $dp) {
                $html = $html . '<option value="">' . $dp->nis . '-' . $dp->tgl_masuk . '-' . $dp->nama . '</option>';
            }
        }

        $html = $html . '</select></div>';
        echo $html;
    }

    public function delete($id)
    {
        $this->Peserta_model->delete_data($id);
        $this->session->set_flashdata('message', 'Data santri Berhasil Dihapus!');
        redirect('admin/pesertadidik');
    }

    private function _rules()
{
    $this->form_validation->set_rules('oldtahun', 'Tahun Ajaran Lama', 'required');
    $this->form_validation->set_rules('newtahun', 'Tahun Ajaran Baru', 'required');
    $this->form_validation->set_rules('oldjilid', 'jilid Lama', 'required');
    $this->form_validation->set_rules('newjilid', 'jilid Baru', 'required');
}

    private function _rules_input()
    {
        $this->form_validation->set_rules('jilid', 'jilid', 'required');
        $this->form_validation->set_rules('santri[]', 'santri', 'required');
    }
}
