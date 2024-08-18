<?php
class Laporansantri extends CI_Controller
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
            'jilid'     => $this->jilid_model->get_data(),
            'tahun'     => $this->Tahun_model->get_data_groupname(),
            'menu'      => 'laporan_santri',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'Laporan Daftar santri',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_admin/sidebar', $data);
        $this->load->view('admin/laporan_santri', $data);
        $this->load->view('templates/footer');
    }

    public function data_all_santri()
    {
        $tahun = $this->input->post('tahun', TRUE) ?? $this->Tahun_model->get_current_tahun();
        $id_jilid   = $this->input->post('id_jilid', TRUE);
        // $tahun      = $this->Tahun_model->get_detail_data($id_tahun);
        $jilid      = $this->jilid_model->get_detail_data($id_jilid);
        $html       = '';

        $cek_data   = $this->Laporan_model->get_numrow_santri($tahun, $id_jilid);
        $data = $this->Laporan_model->get_all_lap_santri($tahun, $id_jilid);
        if ($cek_data > 0) {
            $html       = $html . '
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h1 class="h1 text-center">LAPORAN DAFTAR SANTRI</h1>
                            <h2 class="text-center">TPQ Bustanul Ulum</h2>
                            <h3 class="text-center">Tahun 2024 </h3>
                            <h4 class="text-center">jilid ' . $jilid['jilid'] . '</h4>
                        </div>
                        <a href="' . base_url('admin/laporansantri/excel_laporan?tahun=' . $tahun . '&id_jilid=' . $id_jilid) . '" class="btn btn-success mb-2"><i class="fas fa-file-excel" aria-hidden="true" ></i> Print Excel</a>
                        <table class="table table-responsive-xl table-bordered table-striped table-sm w-100 d-block d-md-table" id="laporansantri">
                            <thead>
                                <tr class="text-center">
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">ID Santri</th>
                                    <th rowspan="2">Tanggal Masuk TPQ</th>
                                    <th rowspan="2">Nama</th>
                                    <th rowspan="2">JK</th>
                                    <th rowspan="2">Tanggal Lahir</th>
                                    <th rowspan="2">Asal Sekolah</th>
                                    <th colspan="3">Orang Tua</th>
                                    <th colspan="4">Alamat</th>
                                </tr>
                                <tr>
                                    <th>Nama Ayah</th>
                                    
                                    <th>Nama Ibu</th>
                                    
                                    <th>No Hp</th>
                                    <th>Dusun</th>
                                    <th>Desa</th>
                                    
                                </tr>
                            </thead>
                            <tbody>';
            foreach ($data as $key => $value) {
                $jk = ($value->jenis_kelamin == 'Perempuan') ? 'P' : 'L';
                $html = $html . '<tr>
                    <td>' . ++$key . '</td>
                    <td>' . $value->nis . '</td>
                    <td>' . $value->tgl_masuk . '</td>
                    <td>' . $value->nama . '</td>
                    <td>' . $jk . '</td>
                    <td>' . $value->tanggal_lahir . '</td>
                    <td>' . $value->asal_sekolah . '</td>
                    <td>' . $value->nama_ayah . '</td>
                    <td>' . $value->nama_ibu . '</td>
                    
                    <td>' . $value->no_hp . '</td>
                    <td>' . $value->dusun . '</td>
                    <td>' . $value->desa . '</td>
                   
                    </tr>';
            }
            $html = $html . '
                            </tbody>
                        </table>
                    </div>
                </div>';
            $html = $html . "
                <script>
                </script>
            ";
        } else {
            $html = $html . '<div class="card">
                                <div class="card-body">
                                    <h6 class="text-center">Laporan Daftar santri Tidak Tersedia, Silahkan Masukan Data Yang Diperlukan</h6>
                                </div>
                            </div>';
        }

        echo $html;
    }

    function get_result_santri()
    {
        $tahun = $this->input->post('tahun', TRUE);
        $id_jilid = $this->input->post('id_jilid', TRUE);

        $list = $this->Laporan_model->get_datatables_santri($tahun, $id_jilid);
        $data = array();
        $no = @$_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $item->nis;
            $row[] = $item->tgl_masuk;
            $row[] = $item->nama;
            $row[] = $item->jenis_kelamin;
            $row[] = $item->tanggal_lahir;
            $row[] = $item->asal_sekolah;
            $row[] = $item->dusun;
            $row[] = $item->desa;
           
            $row[] = $item->nama_ayah;
           
            $row[] = $item->nama_ibu;
            
            $row[] = $item->no_hp;
            $data[] = $row;
        }

        $output = array(
            "draw" => @$_POST['draw'],
            "recordsTotal" => $this->Laporan_model->count_all_santri($tahun, $id_jilid),
            "recordsFiltered" => $this->Laporan_model->count_filtered_santri($tahun, $id_jilid),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function detail()
    {
        $id           = $this->uri->segment(4);
        if (!$id) {
            redirect('admin/laporansantri');
        }

        $data = $this->User_model->get_detail_admin($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'santri'     => $this->santri_model->get_detail_data($id),
            'data'      => $this->Laporan_model->get_detail_lap_guru($id),
            'id_santri'   => $id,
            'level'     => $data['level'],
            'tahun'     => $this->Tahun_model->get_data(),
            'menu'      => 'laporan_santri',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'Laporan Daftar santri',
                    'link' => 'admin/laporansantri'
                ],
                2 => (object)[
                    'name' => 'Detail',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_admin/sidebar', $data);
        $this->load->view('admin/laporan_santridetail', $data);
        $this->load->view('templates/footer');
    }
    
    public function pdf_laporan()
    {
        $query      = $this->input->get('q');
        $tahun      = $this->input->get('tahun');
        $jilid      = $this->input->get('jilid');
        $id_santri   = $this->input->get('id');

        if ($query == 'alldata') {
            $data['data']   = $this->Laporan_model->get_all_lap_santri($tahun, $jilid);
            $data['jilid']  = $this->jilid_model->get_detail_data($jilid);
            $data['tahun']  = $this->Tahun_model->get_detail_data($tahun);

            $this->mypdf->generate('pdf/laporan_allsantri', $data, 'Laporan Data santri', 'A4', 'landscape');
        } elseif ($query == 'detaildata') {
            $data['santri'] = $this->santri_model->get_detail_data($id_santri);
            $this->mypdf->generate('pdf/laporan_detailsantri', $data, 'Laporan Data santri', 'A4', 'portrait');
        }
    }

    public function excel_laporan()
    {
        $tahun      = $this->input->get('tahun', TRUE);
        $id_jilid   = $this->input->get('id_jilid', TRUE);
        $jilid      = $this->jilid_model->get_detail_data($id_jilid);
        $data       = $this->Laporan_model->get_all_lap_santri($tahun, $id_jilid);
        $this->myexcel->generate_santri('Admin', $tahun, $jilid, $data);
    }
}
