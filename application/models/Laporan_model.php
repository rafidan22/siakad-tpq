<?php
class Laporan_model extends CI_Model
{
    public function get_all_tahun()
    {
        return $this->db->get('tahun_ajaran')->result();
    }

    public function get_current_tahun($tahun)
    {
        // Assuming there's a column `is_current` to mark the current year
        $current = $this->db->get_where('tahun_ajaran', ['is_current' => 1])->row();
        return $current ? $current->nama : null;
    }


    public function cek_datatahun_guru($tahun)
    {
        $this->db->select('*');
        $this->db->from('tb_pengajar tp');
        $this->db->join('tb_tahun tt', 'tp.id_tahun = tt.id_tahun', 'left');
        $this->db->where('tt.nama', $tahun);
        $this->db->group_by('tp.id_ustadz');
        return $this->db->get();
    }

    public function get_all_lap_guru($id_tahun)
    {
        $this->db->select('tg.*, tp.jabatan, group_concat(tk.jilid) as jilid');
        $this->db->from('tb_ustadz tg');
        $this->db->join('tb_pengajar tp', 'tg.id_ustadz = tp.id_ustadz', 'left');
        $this->db->join('tb_tahun tt', 'tp.id_tahun = tt.id_tahun', 'left');
        $this->db->join('tb_pembelajaran tm', 'tp.id_mapel = tm.id_mapel', 'left');
        $this->db->join('tb_jilid tk', 'tp.id_jilid = tk.id_jilid', 'left');
        $this->db->where('tt.id_tahun', $id_tahun);
        $this->db->group_by('tg.nama');
        $this->db->order_by('jilid', 'asc');
        return $this->db->get()->result();
    }

    public function get_detail_lap_guru($id_ustadz)
    {
        $this->db->select('tp.id_pengajar, tm.nama_mapel, tk.jilid, count(tk2.id_materi) as kd ,tt.nama as tahun');
        $this->db->from('tb_pengajar tp');
        $this->db->join('tb_ustadz tg', 'tp.id_ustadz = tg.id_ustadz', 'left');
        $this->db->join('tb_pembelajaran tm', 'tp.id_mapel = tm.id_mapel', 'left');
        $this->db->join('tb_jilid tk', 'tp.id_jilid = tk.id_jilid', 'left');
        $this->db->join('tb_kd tk2', 'tm.id_mapel = tk2.id_mapel', 'left');
        $this->db->join('tb_tahun tt', 'tp.id_tahun = tt.id_tahun', 'left');
        $this->db->where('tg.id_ustadz', $id_ustadz);
        $this->db->group_by('tp.id_pengajar');
        return $this->db->get_where('tahun_ajaran', ['id_tahun' => $id_tahun])->row_array();
    }

    public function _get_data_santri($tahun, $id_jilid)
    {
        $tahun = $tahun != null ? $tahun : 'null';
        $id_jilid = $id_jilid != null ? $id_jilid : 'null';

        $this->db->select('ts.*, ta.*, to.nama_ayah,to.pendidikan_ayah,to.pekerjaan_ayah,to.nama_ibu,to.pendidikan_ibu,to.pekerjaan_ibu, to.no_hp');
        $this->db->from('tb_santri ts');
        $this->db->join('tb_orangtua to', 'ts.id_orangtua = to.id_orangtua', 'left');
        $this->db->join('tb_alamat ta', 'to.id_alamat = ta.id_alamat', 'left');
        $this->db->join('tb_datasantri td', 'ts.id_santri = td.id_santri', 'inner');
        $this->db->where('td.tahun_ajaran', $tahun);
        $this->db->where('td.id_jilid', $id_jilid);
        $this->db->group_by('ts.nis');
    }

    public function get_numrow_santri($tahun, $id_jilid)
    {
        $this->_get_data_santri($tahun, $id_jilid);
        return $this->db->get()->num_rows();
    }

    public function get_all_lap_santri($tahun, $id_jilid)
    {
        $this->_get_data_santri($tahun, $id_jilid);
        return $this->db->get()->result();
    }

    var $column_order_santri = array(null, 'nis', 'tgl_masuk', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'asal_sekolah', 'dusun', 'desa', 'nama_ayah',  'nama_ibu', 'no_hp'); //Sesuaikan dengan field
    var $column_search_santri = array('nis', 'tgl_masuk', 'nama', 'dusun', 'kecamatan', 'kabupaten'); //field yang diizin untuk pencarian 
    var $order_santri = array('nis' => 'asc'); // default order 

    private function _get_datatables_query_santri($tahun, $id_jilid)
    {
        $this->_get_data_santri($tahun, $id_jilid);

        $i = 0;

        foreach ($this->column_search_santri as $item) // looping awal
        {
            if ($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {

                if ($i === 0) // looping awal
                {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search_santri) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order_santri[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order_santri = $this->order;
            $this->db->order_by(key($order_santri), $order_santri[key($order_santri)]);
        }
    }

    function get_datatables_santri($tahun, $id_jilid)
    {
        $this->_get_datatables_query_santri($tahun, $id_jilid);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_santri($tahun, $id_jilid)
    {
        $this->_get_datatables_query_santri($tahun, $id_jilid);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_santri($tahun, $id_jilid)
    {
        $this->_get_data_santri($tahun, $id_jilid);
        return $this->db->count_all_results();
    }

    var $column_order_guru = array(null, 'nama', 'nip', 'jenis_kelamin', 'tanggal_lahir', 'jabatan', 'jilid', 'alamat'); //Sesuaikan dengan field
    var $column_search_guru = array('nama', 'nip', 'jenis_kelamin', 'jabatan', 'jilid'); //field yang diizin untuk pencarian 
    var $order_guru = array('jilid' => 'asc'); // default order 

    private function _get_datatables_query_guru($id_tahun)
    {

        $this->db->select("tg.id_ustadz, tg.nama, tg.nip, tg.jenis_kelamin, tg.tanggal_lahir, tp.jabatan, group_concat(tk.jilid) as 'jilid', tg.alamat");
        $this->db->from('tb_ustadz tg');
        $this->db->join('tb_pengajar tp', 'tg.id_ustadz = tp.id_ustadz', 'left');
        $this->db->join('tb_tahun tt', 'tp.id_tahun = tt.id_tahun', 'left');
        $this->db->join('tb_pembelajaran tm', 'tp.id_mapel = tm.id_mapel', 'left');
        $this->db->join('tb_jilid tk', 'tp.id_jilid = tk.id_jilid', 'left');
        $this->db->where('tp.id_tahun', $id_tahun);
        $this->db->group_by('tg.nama');
        $this->db->order_by('tk.jilid', 'asc');

        $i = 0;

        foreach ($this->column_search_guru as $item) // looping awal
        {
            if ($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {

                if ($i === 0) // looping awal
                {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search_guru) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order_guru[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order_guru = $this->order;
            $this->db->order_by(key($order_guru), $order_guru[key($order_guru)]);
        }
    }

    function get_datatables_guru($id_tahun)
    {
        $this->_get_datatables_query_guru($id_tahun);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_guru($id_tahun)
    {
        $this->_get_datatables_query_guru($id_tahun);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_guru($id_tahun)
    {
        $this->db->select('tg.id_ustadz, tg.nama, tg.nip, tg.jenis_kelamin, tg.tanggal_lahir, tp.jabatan, group_concat(tk.jilid) as jilid, tg.alamat');
        $this->db->from('tb_ustadz tg');
        $this->db->join('tb_pengajar tp', 'tg.id_ustadz = tp.id_ustadz', 'left');
        $this->db->join('tb_tahun tt', 'tp.id_tahun = tt.id_tahun', 'left');
        $this->db->join('tb_pembelajaran tm', 'tp.id_mapel = tm.id_mapel', 'left');
        $this->db->join('tb_jilid tk', 'tp.id_jilid = tk.id_jilid', 'left');
        $this->db->where('tp.id_tahun', $id_tahun);
        $this->db->group_by('tg.nama');
        return $this->db->count_all_results();
    }

    public function get_data_nilai($id_tahun, $id_jilid, $view = 'default', $jenis, $id_ustadz = NULL)
    {
        $id_tahun               = $id_tahun != null ? $id_tahun : 'null';
        $id_jilid               = $id_jilid != null ? $id_jilid : 'null';
        $tahun                  = $this->_get_detail_tahun($id_tahun);
        $name_tahun             = $tahun['nama'];
        $get_mapel              = $this->get_mapel_pertahun($id_tahun, $id_jilid, $id_ustadz);
        $mapel                  = ($get_mapel->num_rows() > 0) ? $get_mapel->result() : null;
        $guru                   = $id_ustadz != null ? " and tp.id_ustadz = $id_ustadz " : '';
        $query_join             = "";
        $query_select           = "";
        $query_select_injoin    = "";

        if (!isset($mapel)) {
            return null;
        }

        foreach ($mapel as $key => $value) {
            $query_select_injoin = $query_select_injoin . "sum(if ( nilai.nama_mapel = '$value->nama_mapel', nilai.nilai, 0)) as nilai$key, ";

            switch ($view) {
                case 'min':
                    $query_select = $query_select . "min(hasil.nilai$key) as '$value->nama_mapel', ";
                    break;
                case 'max':
                    $query_select = $query_select . "max(hasil.nilai$key) as '$value->nama_mapel', ";
                    break;
                case 'jumlah':
                    $query_select = $query_select . "sum(hasil.nilai$key) as '$value->nama_mapel', ";
                    break;
                case 'rerata':
                    $query_select = $query_select . "round(avg(hasil.nilai$key)) as '$value->nama_mapel', ";
                    break;
                default:
                    $query_select = $query_select . "hasil.nilai$key as '$value->nama_mapel', ";
                    break;
            }
        }

        switch ($view) {
            case 'min':
                $query_select = $query_select . "min(hasil.jumlah) as 'jumlah', min(hasil.rerata) as 'rerata'";
                break;
            case 'max':
                $query_select = $query_select . "max(hasil.jumlah) as 'jumlah', max(hasil.rerata) as 'rerata'";
                break;
            case 'jumlah':
                $query_select = $query_select . "sum(hasil.jumlah) as 'jumlah', sum(hasil.rerata) as 'rerata'";
                break;
            case 'rerata':
                $query_select = $query_select . "round(avg(hasil.jumlah)) as 'jumlah', round(avg(hasil.rerata)) as 'rerata'";
                break;
            default:
                $query_select = $query_select . "hasil.jumlah as 'jumlah', hasil.rerata as 'rerata'";
                break;
        }

        $query_join = "select ts.nis, ts.tgl_masuk ,ts.nama, $query_select_injoin round(sum(nilai.nilai)) as 'jumlah', round(avg(nilai.nilai)) as 'rerata' from tb_santri ts 
                inner join (
                    select ts.id_santri, ts.nis, ts.nama, round(avg(tn.nilai)) as nilai, tm.id_mapel, tm.nama_mapel 
                    from tb_nilai tn 
                    inner join tb_datasantri td on 
                        tn.id_datasantri = td.id_datasantri 
                    inner join tb_santri ts on
                        td.id_santri = ts.id_santri
                    inner join tb_kd tk on
                        tn.id_materi = tk.id_materi
                    inner join tb_pembelajaran tm on
                        tk.id_mapel = tm.id_mapel
                    inner join tb_pengajar tp on
                        tp.id_mapel = tm.id_mapel 
                    where td.id_jilid = $id_jilid
                        and td.tahun_ajaran = '$name_tahun'
                        and tk.jenis_penilaian = '$jenis' $guru
                    group by ts.nis, tm.id_mapel) nilai on nilai.id_santri = ts.id_santri
                    group by ts.nis";

        if ($query_select != null || $query_join != null) {

            $query = "select ts.nis, ts.tgl_masuk, ts.nama, $query_select from
                        tb_santri ts
                    inner join($query_join) hasil on
                    hasil.nis = ts.nis";

            return $this->db->query($query)->result_array();
        } else {
            return null;
        }
    }

    private function _get_detail_tahun($id)
    {
        return $this->db->get_where('tb_tahun', ['id_tahun' => $id])->row_array();
    }

    public function get_mapel_pertahun($id_tahun, $id_jilid, $id_ustadz = NULL)
    {
        $this->db->select('tm.*');
        $this->db->from('tb_pembelajaran tm');
        $this->db->join('tb_pengajar tp', 'tm.id_mapel = tp.id_mapel', 'left');
        $this->db->where('tp.id_tahun', $id_tahun);
        $this->db->where('tp.id_jilid', $id_jilid);
        if ($id_ustadz != null) {
            $this->db->where('tp.id_ustadz', $id_ustadz);
        }
        return $this->db->get();
    }
}
