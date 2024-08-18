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

        if ($this->session->userdata['level'] != 'ustadz') {
            $this->session->set_flashdata('message', 'Anda Belum Login!');
            redirect('login');
        }
    }

    public function index()
    {
        $data = $this->User_model->get_detail_guru($this->session->userdata['id_user'], $this->session->userdata['level']);
        $jilid = $this->jilid_model->get_like_walikelas($data['nama']);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'     => $data['level'],
            'tahun'     => $this->Tahun_model->get_data_groupname(),
            'jilid'     => $jilid,
            'menu'      => 'laporan_santri',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'walikelas'
                ],
                1 => (object)[
                    'name' => 'Laporan Daftar santri',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_guruwali/sidebar', $data);
        $this->load->view('guru_wali/laporan_santri', $data);
        $this->load->view('templates/footer');
    }

    public function data_all_santri()
    {
        $tahun      = $this->input->post('tahun', TRUE);
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
                            <h2 class="text-center">TPQ BUSTANUL ULUM</h2>
                            <h3 class="text-center">Tahun 2024</h3>
                            <h4 class="text-center">jilid ' . $jilid['jilid'] . '</h4>
                        </div>
                        <a href="' . base_url('walikelas/laporansantri/excel_laporan?tahun=' . $tahun . '&id_jilid=' . $id_jilid) . '" class="btn btn-success mb-2"><i class="fas fa-file-excel" aria-hidden="true" ></i> Print Excel</a>
                        <table class="table table-responsive-xl table-bordered table-striped table-sm w-100 d-block d-md-table" id="laporansantri">
                            <thead>
                                <tr class="text-center">
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">ID santri</th>
                                    <th rowspan="2">Tanggal Masuk TPQ</th>
                                    <th rowspan="2">Nama</th>
                                    <th rowspan="2">JK</th>
                                    <th rowspan="2">Tanggal Lahir</th>
                                    <th rowspan="2">asal_sekolah</th>
                                    <th colspan="7">Orang Tua</th>
                                    <th colspan="4">Alamat</th>
                                </tr>
                                <tr>
                                    <th>Nama Ayah</th>
                                    <th>Pendidkan Ayah</th>
                                    <th>Pekerjaan Ayah</th>
                                    <th>Nama Ibu</th>
                                    <th>Pendidikan Ibu</th>
                                    <th>Pekerjaan Ibu</th>
                                    <th>No Hp</th>
                                    <th>Dusun</th>
                                    <th>Desa</th>
                                    <th>Kecamatan</th>
                                    <th>Kabupaten</th>
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
                    <td>' . $value->pendidikan_ayah . '</td>
                    <td>' . $value->pekerjaan_ayah . '</td>
                    <td>' . $value->nama_ibu . '</td>
                    <td>' . $value->pendidikan_ibu . '</td>
                    <td>' . $value->pekerjaan_ibu . '</td>
                    <td>' . $value->no_hp . '</td>
                    <td>' . $value->dusun . '</td>
                    <td>' . $value->desa . '</td>
                    <td>' . $value->kecamatan . '</td>
                    <td>' . $value->kabupaten . '</td>
                    </tr>';
            }

            $html = $html . '
                            </tbody>
                        </table>
                    </div>
                </div>';

            // $html = $html . "
            //     <script>
            //         $(document).ready(function() {
            //             var table = $('#laporansantri').DataTable({
            //                 dom: 'Bfrtip',
            //                 lengthChange: false,
            //                 'pageLength': 10,
            //                 'lengthMenu': [[10, 20, 25, 50, -1], [10, 20, 25, 50, 'All']],
            //                 buttons: [
            //                     {
            //                         extend: 'excel',
            //                         text: 'Print Excel',
            //                         titleAttr: 'Excel',
            //                         className: 'btn-success'
            //                     },
            //                 ],
            //                 columnDefs: [
            //                     {
            //                         targets: [ -1,-2,-3,-4 ],
            //                         visible: false,
            //                         searchable: false
            //                     }
            //                 ]
            //             });


            //             table.buttons().container()
            //                 .appendTo( '#laporansantri_wrapper .col-md-6:eq(0)' );
            //         });
            //     </script>
            // ";
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
        $id_tahun = $this->input->post('id_tahun', TRUE);
        $id_jilid = $this->input->post('id_jilid', TRUE);

        $list = $this->Laporan_model->get_datatables_santri($id_tahun, $id_jilid);
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
            $row[] = $item->kecamatan;
            $row[] = $item->kabupaten;
            $row[] = anchor('walikelas/laporansantri/detail/' . $item->id_santri, '<div class="btn btn-sm btn-success mr-1 ml-1 mb-1 mt=1"><i class="fa fa-eye"></i></div>') .
                '<a href="' . base_url('walikelas/laporansantri/pdf_laporan?q=detaildata&id=' . $item->id_santri) . '" class="btn btn-sm btn-info mr-1 ml-1 mb-1 mt=1"><i class="fa fa-print"></i></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => @$_POST['draw'],
            "recordsTotal" => $this->Laporan_model->count_all_santri($id_tahun, $id_jilid),
            "recordsFiltered" => $this->Laporan_model->count_filtered_santri($id_tahun, $id_jilid),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function detail()
    {
        $id           = $this->uri->segment(4);
        if (!$id) {
            redirect('walikelas/laporansantri');
        }

        $data = $this->User_model->get_detail_guru($this->session->userdata['id_user'], $this->session->userdata['level']);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'santri'     => $this->santri_model->get_detail_data($id),
            'data'      => $this->Laporan_model->get_detail_lap_guru($id),
            'id_santri'  => $id,
            'level'     => $data['level'],
            'tahun'     => $this->Tahun_model->get_data(),
            'menu'      => 'laporan_santri',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'walikelas'
                ],
                1 => (object)[
                    'name' => 'Laporan Daftar santri',
                    'link' => 'walikelas/laporansantri'
                ],
                2 => (object)[
                    'name' => 'Detail',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_guruwali/sidebar', $data);
        $this->load->view('guru_wali/laporan_santridetail', $data);
        $this->load->view('templates/footer');
    }

    public function pdf_laporan()
    {
        $query      = $this->input->get('q');
        $tahun      = $this->input->get('tahun');
        $jilid      = $this->input->get('jilid');
        $id_santri   = $this->input->get('id');

        if ($query == 'alldata') {
            $data['data'] = $this->Laporan_model->get_all_lap_santri($tahun, $jilid);
            $data['jilid'] = $this->jilid_model->get_detail_data($jilid);
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
        $this->myexcel->generate_santri('walikelas', $tahun, $jilid, $data);
    }
}
