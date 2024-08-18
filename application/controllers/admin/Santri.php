<?php
class santri extends CI_Controller
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
            'menu'      => 'santri',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'santri',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_admin/sidebar', $data);
        $this->load->view('admin/santri', $data);
        $this->load->view('templates/footer');
    }

    function get_result_santri()
    {
        $list = $this->santri_model->get_datatables();
        $data = array();
        $no = @$_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $item->nis;
            $row[] = $item->tgl_masuk;
            $row[] = $item->nama;
            $row[] = $item->tanggal_lahir;
            $row[] = $item->asal_sekolah;
            $row[] = $item->jenis_kelamin;
            $row[] = '<div id="set_detailModal" class="btn btn-sm btn-success mr-1 ml-1 mb-1" data-toggle="modal" data-target="#detailModal" data-idsantri="' . $item->id_santri . '" data-santri="' . $item->nama . '" data-namaibu="' . $item->nama_ibu . '" data-pendidikanibu="' . $item->pendidikan_ibu . '" data-perkejaanibu="' . $item->pekerjaan_ibu . '" data-namaayah="' . $item->nama_ayah . '" data-pendidikanayah="' . $item->pendidikan_ayah . '" data-pekerjaanayah="' . $item->pekerjaan_ayah . '" data-nohp="' . $item->no_hp . '" data-dusun="' . $item->dusun . '" data-desa="' . $item->desa . '" data-kecamatan="' . $item->kecamatan . '" data-kabupaten="' . $item->kabupaten . '" data-photo="' . $item->photo . '"><i class="fa fa-eye"></i></div>'
                . anchor('admin/santri/edit/' . $item->id_santri, '<div class="btn btn-sm btn-primary mr-1 ml-1 mb-1"><i class="fa fa-edit"></i></div>')
                . '<a href="javascript:;" onclick="confirmDelete(' . $item->id_santri . ')" class="btn btn-sm btn-danger btn-delete-santri mr-1 ml-1 mb-1"><i class="fa fa-trash"></i></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => @$_POST['draw'],
            "recordsTotal" => $this->santri_model->count_all(),
            "recordsFiltered" => $this->santri_model->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function edit()
    {
        $id           = $this->uri->segment(4);
        if (!$id) {
            redirect('admin/santri');
        }

        $santri = $this->santri_model->get_detail_data($id);
        if (!isset($santri)) {
            redirect('error_404');
        }

        $data = $this->User_model->get_detail_admin($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'       => $data['id_user'],
            'nama'          => $data['nama'],
            'level'         => $data['level'],
            'photo'         => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'santri'         => $santri,
            'jilid'         => $this->jilid_model->get_data(),
            'jenis_kelamin' => ['Laki-laki', 'Perempuan'],
            'menu'          => 'santri',
            'breadcrumb'    => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'santri',
                    'link' => 'admin/santri'
                ],
                2 => (object)[
                    'name' => 'Edit',
                    'link' => NULL
                ]
            ]
        );

        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header');
            $this->load->view('templates_admin/sidebar', $data);
            $this->load->view('admin/santri_edit', $data);
            $this->load->view('templates/footer');
        } else {
            $config['upload_path']          = './assets/photos/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg';
            $config['max_size']             = 5000;
            $config['file_name']            = 'photo-santri-' . $this->input->post('tanggal_lahir', TRUE) . '-' . substr(md5(rand()), 0, 10);
            $this->upload->initialize($config);

            if (@$_FILES['photo']['name'] != null) {

                if ($this->upload->do_upload('photo')) {
                    $item = $this->santri_model->get_detail_data($id);
                    if ($item['photo'] != null) {
                        $target_delete = './assets/photos/' . $item['photo'];
                        unlink($target_delete);
                    }

                    $gbr = $this->upload->data();
                    //Compress Image
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './assets/photos/' . $gbr['file_name'];
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['width'] = 400;
                    $config['height'] = 600;
                    $config['new_image'] = './assets/photos/' . $gbr['file_name'];
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();

                    $photo = $gbr['file_name'];
                    $this->santri_model->edit_data($id, $photo);
                    $this->session->set_flashdata('message', 'Data santri Berhasil Diupdate!');
                    redirect('admin/santri');
                } else {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message_error', $error);
                    redirect('admin/santri/input');
                }
            } else {
                $photo = NULL;
                $this->santri_model->edit_data($id, $photo);
                $this->session->set_flashdata('message', 'Data santri Berhasil Diupdate!');
                redirect('admin/santri');
            }
        }
    }

    public function input()
    {
        $data = $this->User_model->get_detail_admin($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'       => $data['id_user'],
            'nama'          => $data['nama'],
            'photo'         => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'         => $data['level'],
            'jilid'         => $this->jilid_model->get_data(),
            'menu'          => 'santri',
            'breadcrumb'    => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'santri',
                    'link' => 'admin/santri'
                ],
                2 => (object)[
                    'name' => 'Input',
                    'link' => NULL
                ]
            ]
        );

        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header');
            $this->load->view('templates_admin/sidebar', $data);
            $this->load->view('admin/santri_input', $data);
            $this->load->view('templates/footer');
        } else {

            $config['upload_path']          = './assets/photos/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg';
            $config['max_size']             = 5000;
            $config['file_name']            = 'photo-santri-' . $this->input->post('tanggal_lahir', TRUE) . '-' . substr(md5(rand()), 0, 10);
            $this->upload->initialize($config);

            if (@$_FILES['photo']['name'] != null) {

                if ($this->upload->do_upload('photo')) {
                    $gbr = $this->upload->data();
                    //Compress Image
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './assets/photos/' . $gbr['file_name'];
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['width'] = 400;
                    $config['height'] = 600;
                    $config['new_image'] = './assets/photos/' . $gbr['file_name'];
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();

                    $photo = $gbr['file_name'];
                    $this->santri_model->input_data_santri($photo);
                    $this->session->set_flashdata('message', 'Data santri Berhasil Ditambahkan!');
                    redirect('admin/santri');
                } else {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('message_error', $error);
                    redirect('admin/santri/input');
                }
            } else {
                $photo = NULL;
                $this->santri_model->input_data_santri($photo);
                $this->session->set_flashdata('message', 'Data santri Berhasil Ditambahkan!');
                redirect('admin/santri');
            }
        }
    }

    public function delete($id)
    {
        $item = $this->santri_model->get_detail_data($id);
        $id_address = $this->santri_model->get_id_address($item['id_orangtua']);
        if ($item['photo'] != null) {
            $target_delete = './assets/photos/' . $item['photo'];
            unlink($target_delete);
        }

        $this->santri_model->delete_data($id_address);
        $this->User_model->delete_data($item['id_user']);
        $this->session->set_flashdata('message', 'Data santri Berhasil Dihapus!');
        redirect('admin/santri');
    }

    private function _rules()
    {
        // rules data diri
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|max_length[10]');
        $this->form_validation->set_rules('tgl_masuk', 'Tanggal Masuk TPQ', 'required');
        $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[100]');
        $this->form_validation->set_rules('tanggal_lahir', 'Tanggal lahir', 'required');
        $this->form_validation->set_rules('asal_sekolah', 'asal_sekolah', 'required|max_length[25]');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required');

        // rules data orang tua
        $this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'required|max_length[100]');
        $this->form_validation->set_rules('pendidikan_ibu', 'Pendidikan Ibu', 'required|max_length[50]');
        $this->form_validation->set_rules('pekerjaan_ibu', 'Pekerjaan Ibu', 'required|max_length[50]');
        $this->form_validation->set_rules('nama_ayah', 'Nama Ayah', 'required|max_length[100]');
        $this->form_validation->set_rules('pendidikan_ayah', 'Pendidikan Ayah', 'required|max_length[50]');
        $this->form_validation->set_rules('pekerjaan_ayah', 'pekerjaan_ayah', 'required|max_length[50]');
        $this->form_validation->set_rules('no_hp', 'No Handphone', 'required|numeric|min_length[10]|max_length[15]');

        // rules data alamat
        $this->form_validation->set_rules('dusun', 'Dusun', 'required|max_length[50]');
        $this->form_validation->set_rules('desa', 'Desa', 'required|max_length[50]');
        $this->form_validation->set_rules('kecamatan', 'Kecamatan', 'required|max_length[50]');
        $this->form_validation->set_rules('kabupaten', 'Kabupaten', 'required|max_length[50]');
    }
}
