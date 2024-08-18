<?php

class LaporanNilai extends CI_Controller
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
            'tahun'     => $this->Tahun_model->get_data(),
            'menu'      => 'laporan_nilai',
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => 'admin'
                ],
                1 => (object)[
                    'name' => 'Laporan Daftar Nilai',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_admin/sidebar', $data);
        $this->load->view('admin/laporan_nilai', $data);
        $this->load->view('templates/footer');
    }

    public function get_jilid()
    {
        $id_tahun   = $this->input->post('id_tahun', TRUE);
        $data       =  $this->Pengajar_model->get_data_with_tahun($id_tahun);
        if ($data->num_rows() > 0) {
            echo '<option value="">--Pilih jilid--</option>';
            foreach ($data->result() as $pe) {
                echo "<option value=$pe->id_jilid>$pe->jilid</option>";
            }
        } else {
            echo '<option value="">--Tidak Tersedia--</option>';
        }
    }

    public function data_all_nilai()
    {
        $id_tahun       = $this->input->post('id_tahun', TRUE);
        $id_jilid       = $this->input->post('id_jilid', TRUE);
        $nilai          = $this->input->post('nilai', TRUE);
        $html           = '';

        if ($id_tahun == null || $id_jilid == null) {
            //id not found
            $html = $html . '<div class="card">
                                <div class="card-body">
                                    <h6 class="text-center">Laporan Daftar Nilai Santri Tidak Tersedia, Silahkan Masukan Data Yang Diperlukan</h6>
                                </div>
                            </div>';
        } else {
            $tahun          = $this->Tahun_model->get_detail_data($id_tahun);
            $jilid          = $this->jilid_model->get_detail_data($id_jilid);
            $daftar_mapel   = $this->Laporan_model->get_mapel_pertahun($id_tahun, $id_jilid)->result();
            $result         = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'default', $nilai);
            $result_min     = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'min', $nilai);
            $result_max     = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'max', $nilai);
            $result_jumlah  = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'jumlah', $nilai);
            $result_rerata  = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'rerata', $nilai);

            if ($result) {
                $html = $html . '
                <div class="card">
                    <div class="card-body">
                        <div>
                            <h1 class="h1 text-center">LAPORAN DAFTAR NILAI SANTRI ' . $nilai . '</h1>
                            <h1 class="text-center">TPQ BUSTANUL ULUM</h1>
                            <h3 class="text-center">Tahun 2024</h3>
                            <h3 class="text-center">jilid ' . $jilid['jilid'] . '</h3>
                        </div>
                        <a href="' . base_url('admin/laporannilai/excel_laporan?id_tahun=' . $id_tahun . '&id_jilid=' . $id_jilid . '&nilai=' . $nilai) . '" class="btn btn-success mb-2"><i class="fas fa-file-excel" aria-hidden="true" ></i> Print Excel</a>
                        <table class="table table-responsive-sm table-bordered table-striped table-sm w-100 d-block d-md-table" id="table-laporanSantri">
                            <thead>
                                <tr class="text-center">
                                    <th width="10px">NO</th>
                                    <th width="10px">ID Santri</th>
                                    <th width="10px">Tanggal Masuk TPQ</th>
                                    <th>NAMA</th>';

                //heading mapel
                foreach ($daftar_mapel as $key => $value) {
                    $html = $html . "<th>$value->nama_mapel</th>";
                }

                //heading jumlah dan rata-rata
                $html = $html . '<th>Jumlah</th>
                            <th>Rata-rata</th>
                            </tr></thead><tbody>';

                // body table default
                foreach ($result as $key => $value) {
                    $html = $html . '
                    <tr>
                        <td class="text-center">' . ++$key . '</td>
                        <td>' . $value['nis'] . '</td>
                        <td>' . $value['tgl_masuk'] . '</td>
                        <td>' . $value['nama'] . '</td>';

                    foreach ($daftar_mapel as $kd => $mapel) {
                        $html = $html . '<td>' . $value[$mapel->nama_mapel] . '</td>';
                    }

                    $html = $html . '
                        <td>' . $value['jumlah'] . '</td>
                        <td>' . $value['rerata'] . '</td>
                    </tr>';
                }

                // body table min
                foreach ($result_min as $key => $value) {
                    $html = $html . '<tr><td colspan="100%"></td></tr>';

                    $html = $html . '<tr>
                    <td width="20px"></td>
                    <td colspan="3">MIN</td>';
                    foreach ($daftar_mapel as $kd => $mapel) {
                        $html = $html . '<td>' . $value[$mapel->nama_mapel] . '</td>';
                    }

                    $html = $html . "<td>{$value['jumlah']}</td><td>{$value['rerata']}</td></tr>";
                }

                // body table max
                foreach ($result_max as $key => $value) {
                    $html = $html . '<tr>
                    <td width="20px"></td>
                    <td colspan="3">MAX</td>';
                    foreach ($daftar_mapel as $kd => $mapel) {
                        $html = $html . '<td>' . $value[$mapel->nama_mapel] . '</td>';
                    }

                    $html = $html . "<td>{$value['jumlah']}</td><td>{$value['rerata']}</td></tr>";
                }

                // body table jumlah
                foreach ($result_jumlah as $key => $value) {
                    $html = $html . '<tr>
                    <td width="20px"></td>
                    <td colspan="3">Jumlah</td>';
                    foreach ($daftar_mapel as $kd => $mapel) {
                        $html = $html . '<td>' . $value[$mapel->nama_mapel] . '</td>';
                    }

                    $html = $html . "<td>{$value['jumlah']}</td><td>{$value['rerata']}</td></tr>";
                }

                // body table rerata
                foreach ($result_rerata as $key => $value) {
                    $html = $html . '<tr>
                    <td width="20px"></td>
                    <td colspan="3">Rata-Rata</td>';
                    foreach ($daftar_mapel as $kd => $mapel) {
                        $html = $html . '<td>' . $value[$mapel->nama_mapel] . '</td>';
                    }

                    $html = $html . "<td>{$value['jumlah']}</td><td>{$value['rerata']}</td></tr>";
                }

                $html = $html . '<tr></tr>';

                $html = $html . '
                            </tbody>
                        </table>
                    </div>
                </div>';
            } else {
                $html = $html . '<div class="card">
                                <div class="card-body">
                                    <h6 class="text-center">Laporan Daftar Nilai Santri Tidak Tersedia, Silahkan Masukan Data Yang Diperlukan</h6>
                                </div>
                            </div>';
            }
        }

        echo $html;
    }

    public function pdf_laporan()
    {
        $query      = $this->input->get('q');
        $id_tahun      = $this->input->get('tahun');
        $id_jilid      = $this->input->get('jilid');

        if ($query == 'alldata') {
            $data['tahun']   = $this->Tahun_model->get_detail_data($id_tahun);
            $data['jilid']   = $this->jilid_model->get_detail_data($id_jilid);
            $data['mapel']   = $this->Laporan_model->get_mapel_pertahun($id_tahun, $id_jilid)->result();
            $data['result']  = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'default');
            $data['min']     = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'min');
            $data['max']     = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'max');
            $data['jumlah']  = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'jumlah');
            $data['rerata']  = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'rerata');

            $this->mypdf->generate('pdf/laporan_allnilai', $data, 'Laporan Data Santri', 'A4', 'landscape');
            // $this->load->view('pdf/laporan_allnilai', $data);
        }
    }

    public function excel_laporan()
    {
        $id_tahun       = $this->input->get('id_tahun', TRUE);
        $id_jilid       = $this->input->get('id_jilid', TRUE);
        $nilai          = $this->input->get('nilai', TRUE);

        $tahun          = $this->Tahun_model->get_detail_data($id_tahun);
        $jilid          = $this->jilid_model->get_detail_data($id_jilid);

        $daftar_mapel   = $this->Laporan_model->get_mapel_pertahun($id_tahun, $id_jilid)->result();

        $result         = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'default', $nilai);
        $result_min     = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'min', $nilai);
        $result_max     = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'max', $nilai);
        $result_jumlah  = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'jumlah', $nilai);
        $result_rerata  = $this->Laporan_model->get_data_nilai($id_tahun, $id_jilid, 'rerata', $nilai);
        $this->myexcel->generate('Admin', $nilai, $tahun, $jilid, $daftar_mapel, $result, $result_min, $result_max, $result_jumlah, $result_rerata);
    }
}
