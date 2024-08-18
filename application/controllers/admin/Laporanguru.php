<?php
class LaporanGuru extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata['username'])) {
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
            'tahun'     => $this->Tahun_model->get_data_groupname(),
            'menu'      => 'laporan_guru',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'Laporan Daftar Ustadz Ustadzah',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_admin/sidebar', $data);
        $this->load->view('admin/laporan_guru', $data);
        $this->load->view('templates/footer');
    }

    public function detail()
    {
        $id           = $this->uri->segment(4);
        if (!$id) {
            redirect('admin/laporanguru');
        }

        $data = $this->User_model->get_detail_admin($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'guru'      => $this->Guru_model->get_detail_data($id),
            'data'      => $this->Laporan_model->get_detail_lap_guru($id),
            'id_ustadz'   => $id,
            'level'     => $data['level'],
            'tahun'     => $this->Tahun_model->get_data(),
            'menu'      => 'laporan_guru',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'Laporan Daftar Ustadz Ustadzah',
                    'link' => 'admin/laporanguru'
                ],
                2 => (object)[
                    'name' => 'Detail',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_admin/sidebar', $data);
        $this->load->view('admin/laporan_gurudetail', $data);
        $this->load->view('templates/footer');
    }

    public function data_all_guru()
{
    // Get the year from the POST request, default to the current year if not provided
    $tahun = $this->input->post('tahun', TRUE) ?? $this->Tahun_model->get_current_tahun();
    
    // Check if data exists for the given year
    $cek_data = $this->Laporan_model->cek_datatahun_guru($tahun);
    $id_tahun = ($cek_data->row_array()) ? $cek_data->row_array()['id_tahun'] : 'null';

    // Get detailed data for the year
    $tahun_data = $this->Tahun_model->get_detail_data($id_tahun);
    $data_guru = $this->Laporan_model->get_all_lap_guru($id_tahun);
    $html = '';

    // Build the HTML output
    if ($cek_data->num_rows() > 0) {
        $html .= '
            <div class="card">
                <div class="card-body">
                    <div>
                        <h1 class="h1 text-center">LAPORAN DAFTAR USTADZ USTADZAH</h1>
                        <h2 class="text-center">TPQ BUSTANUL ULUM</h2>
                        <h3 class="text-center">Tahun 2024 </h3>
                    </div>
                    <a href="' . base_url('admin/laporanguru/excel_laporan?tahun=' . $tahun_data['nama']) . '" class="btn btn-success mb-2"><i class="fas fa-file-excel" aria-hidden="true"></i> Print Excel</a>
                    <table class="table table-responsive-sm table-bordered table-striped table-sm w-100 d-block d-md-table" id="table-laporanguru">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>Jenis Kelamin</th>
                                <th>Tanggal Lahir</th>
                                <th>Jabatan</th>
                                <th>Jilid Mengajar</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>';

        foreach ($data_guru as $key => $value) {
            $map_jilid = explode(',', $value->jilid);
            $unique_jilid = array_unique($map_jilid);
            sort($unique_jilid);
            $new_jilid = implode(', ', $unique_jilid);

            $html .= '<tr>
                <td>' . ++$key . '</td>
                <td>' . $value->nama . '</td>
                <td>' . $value->nip . '</td>
                <td>' . $value->jenis_kelamin . '</td>
                <td>' . $value->tanggal_lahir . '</td>
                <td>' . $value->jabatan . '</td>
                <td>' . $new_jilid . '</td>
                <td>' . $value->alamat . '</td>
                </tr>';
        }

        $html .= "
                        </tbody>
                    </table>
                </div>
            </div>";
    } else {
        $html .= '<div class="card">
                    <div class="card-body">
                        <h6 class="text-center">Laporan Daftar Ustadz Ustadzah Tidak Tersedia, Silahkan Pilih Tahun Ajaran</h6>
                    </div>
                </div>';
    }

    echo $html;
}

    function get_result_guru()
    {
        $id_tahun = $this->input->post('id_tahun', TRUE);
        $list = $this->Laporan_model->get_datatables_guru($id_tahun);
        $data = array();
        $no = @$_POST['start'];
        foreach ($list as $item) {
            $map_jilid = explode(',', $item->jilid);
            $uniqe_jilid = array_unique($map_jilid);
            sort($uniqe_jilid);
            $new_jilid = implode(', ', $uniqe_jilid);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $item->nama;
            $row[] = $item->nip;
            $row[] = $item->jenis_kelamin;
            $row[] = $item->tanggal_lahir;
            $row[] = $item->jabatan;
            $row[] = $new_jilid;
            $row[] = $item->alamat;

            // $row[] = anchor('admin/laporanguru/detail/' . $item->id_ustadz, '<div class="btn btn-sm btn-success mr-1 ml-1 mb-1 mt-1"><i class="fa fa-eye"></i></div>') .
            // '<a href="' . base_url('admin/laporanguru/pdf_laporan?q=detaildata&id=' . $item->id_ustadz) . '" class="btn btn-sm btn-info mr-1 ml-1 mb-1 mt-1"><i class="fa fa-print"></i></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => @$_POST['draw'],
            "recordsTotal" => $this->Laporan_model->count_all_guru($id_tahun),
            "recordsFiltered" => $this->Laporan_model->count_filtered_guru($id_tahun),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function pdf_laporan()
    {
        $query   = $this->input->get('q');
        $tahun   = $this->input->get('tahun');
        $id_ustadz = $this->input->get('id');

        if ($query == 'alldata') {
            $data['data']   = $this->Laporan_model->get_all_lap_guru($tahun);
            $data['tahun']  = $this->Tahun_model->get_detail_data($tahun);

            $this->mypdf->generate('pdf/laporan_allguru', $data, 'Laporan Data Guru', 'A4', 'landscape');
        } elseif ($query == 'detaildata') {
            $data = array(
                'guru'  => $this->Guru_model->get_detail_data($id_ustadz),
                'data'  => $this->Laporan_model->get_detail_lap_guru($id_ustadz)
            );

            $this->mypdf->generate('pdf/laporan_detailguru', $data, 'Laporan Data Guru', 'A4', 'portrait');
        }
    }

    public function excel_laporan()
    {
        $tahun      = $this->input->get('tahun');
        $cek_data   = $this->Laporan_model->cek_datatahun_guru($tahun);
        $id_tahun   = ($cek_data->row_array()) ? $cek_data->row_array()['id_tahun'] : 'null';
        $tahun      = $this->Tahun_model->get_detail_data($id_tahun);
        $data_guru  = $this->Laporan_model->get_all_lap_guru($id_tahun);
        $this->myexcel->generate_guru('Admin', $tahun, $data_guru);
    }
}
