<?php
class Nilai extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata['username']) && $this->session->userdata['level'] != 'santri') {
            $this->session->set_flashdata('message', 'Anda Belum Login!');
            redirect('login');
        }

        if ($this->session->userdata['level'] != 'santri') {
            $this->session->set_flashdata('message', 'Anda Belum Login!');
            redirect('login');
        }
    }

    public function index()
    {
        $data       = $this->User_model->get_detail_santri($this->session->userdata['id_user'], $this->session->userdata['level']);
        $tahun      = $this->Tahun_model->get_active_stats();
        $jilid      = $this->jilid_model->get_detail_santri($data['id_santri'], $tahun);
        $id_jilid   = $jilid['id_jilid'];
        $id_tahun   = $tahun['id_tahun'];
        $id_santri   = $data['id_santri'];
        $nilai      = $this->Nilai_model->nilai_persantri($id_santri, $id_jilid, $id_tahun);
        $total_nilai = $this->Nilai_model->total_nilai_persantri($id_santri, $id_jilid, $id_tahun);
        $alljilid   = $this->Pengajar_model->get_alljilid_peserta($id_santri);
        $data = array(
            'id_user'   => $data['id_user'],
            'nama'      => $data['nama'],
            'photo'     => $data['photo'] != null ? $data['photo'] : 'user-placeholder.jpg',
            'level'     => $data['level'],
            'jilid'     => $jilid,
            'alljilid'  => $alljilid,
            'menu'      => 'dashboard',
            'tahun'     => $this->Tahun_model->get_data(),
            'tahun_aktif' => $this->Tahun_model->get_active_stats(),
            'nilai'     => $nilai,
            'total'     => $total_nilai,
            'breadcrumb' => [
                0 => (object)[
                    'name' => 'Dashboard',
                    'link' => NULL
                ]
            ]
        );

        $this->load->view('templates/header');
        $this->load->view('templates_santri/sidebar', $data);
        $this->load->view('santri/nilai', $data);
        $this->load->view('templates/footer');
    }

    public function get_other_nilai()
    {
        $id_tahun   = $this->input->post('id_tahun', TRUE);
        $tahun      = $this->Tahun_model->get_detail_data($id_tahun);
        $data       = $this->User_model->get_detail_santri($this->session->userdata['id_user'], $this->session->userdata['level']);
        $peserta    = $this->Peserta_model->get_jilid($data['id_santri'], $tahun['nama']);
        $jilid      = $this->jilid_model->get_detail_data($peserta['id_jilid']);
        $nilai      = $this->Nilai_model->nilai_persantri($data['id_santri'], $peserta['id_jilid'], $id_tahun);
        $total_nilai = $this->Nilai_model->total_nilai_persantri($data['id_santri'], $peserta['id_jilid'], $id_tahun);
        $html = '';

        $html = $html . '
            <h5 class="text-right">jilid ' . $jilid['jilid'] . '</h5>
            <table class="table table-responsive-sm table-bordered table-striped table-sm w-100 d-block d-md-table" id="table-guru">
                <thead>
                    <tr>
                        <th class="text-center" width="30px">No</th>
                        <th>Materi Pembelajaran</th>
                            <th>Penilaian Individu</th>
                            <th>Penilaian Tambahan</th>
                            <th>Jumlah</th> 
                            <th>Rata-rata</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($nilai as $key => $value) {
            $html = $html . '
                <tr>
                    <td class="text-center">' . ++$key . '</td>
                    <td>' . $value->nama_mapel . '</td>
                    <td>' . $value->individu . '</td>
                    <td>' . $value->tambahan . '</td>
                    <td>' . $value->jumlah . '</td>
                    <td>' . $value->rerata . '</td>
                </tr>
            ';
        }
        $html = $html . '
                    <tr>
                        <td colspan="5">Jumlah Seluruh Nilai</td>
                        <td>' . $total_nilai['jumlah'] . '</td>
                    </tr>
                    <tr>
                        <td colspan="5">Rata-rata Seluruh Nilai</td>
                        <td>' . $total_nilai['rerata'] . '</td>
                    </tr>
                </tbody>
            </table>
        ';

        echo $html;
    }
}
