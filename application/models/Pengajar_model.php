<?php
class Pengajar_model extends CI_Model
{
    public function input_data($id_tahun)
    {
        $mapel = $this->input->post('mapel', TRUE);
        if (!empty($mapel)) {
            foreach ($mapel as $key => $value) {
                $data = array(
                    'id_ustadz'   => $this->input->post('guru', TRUE),
                    'id_mapel'  => $value,
                    'id_jilid'  => $this->input->post('jilid', TRUE),
                    'id_tahun'  => $id_tahun,
                    'jabatan'   => $this->input->post('jabatan', TRUE)
                );

                $this->db->insert('tb_pengajar', $data);
            }
        }
    }

    public function get_alljilid_peserta($id_santri)
    {
        $this->db->select('td.tahun_ajaran');
        $this->db->from('tb_datasantri td');
        $this->db->where('td.id_santri', $id_santri);
        return $this->db->get()->result();
    }

    public function get_count_perpengajar($id)
    {
        return $this->db->get_where('tb_pengajar', ['id_ustadz' => $id])->num_rows();
    }

    public function edit_data($id)
    {
        $data = array(
            'id_ustadz'   => $this->input->post('guru', TRUE),
            'id_mapel'  => $this->input->post('mapel', TRUE),
            'id_jilid'  => $this->input->post('jilid', TRUE),
            'jabatan'   => $this->input->post('jabatan', TRUE)
        );

        $this->db->where('id_pengajar', $id);
        $this->db->update('tb_pengajar', $data);
    }

    public function get_data()
    {
        //mysql
        // $this->db->select('tp.id_pengajar, tp.jabatan, tg.nama as guru, CONCAT_WS(" / ", tm.nama_mapel, tm.level) as mapel, tk.jilid, tt.nama as tahun');

        //sqlite
        $this->db->select("tp.id_pengajar, tp.jabatan, tg.nama as guru, tm.nama_mapel ||' /'|| tm.level as mapel, tk.jilid, tt.nama as tahun");

        $this->db->from('tb_pengajar tp');
        $this->db->join('tb_ustadz tg', 'tp.id_ustadz = tg.id_ustadz', 'left');
        $this->db->join('tb_pembelajaran tm', 'tp.id_mapel = tm.id_mapel', 'left');
        $this->db->join('tb_jilid tk', 'tp.id_jilid  = tk.id_jilid', 'left');
        $this->db->join('tb_tahun tt', 'tp.id_tahun = tt.id_tahun', 'left');

        return $this->db->get()->result();
    }

    public function get_data_with_tahun($id_tahun)
    {
        $this->db->select('tk.*, tt.nama as tahun');
        $this->db->from('tb_pengajar tp');
        $this->db->join('tb_ustadz tg', 'tp.id_ustadz = tg.id_ustadz', 'left');
        $this->db->join('tb_pembelajaran tm', 'tp.id_mapel = tm.id_mapel', 'left');
        $this->db->join('tb_jilid tk', 'tp.id_jilid  = tk.id_jilid', 'left');
        $this->db->join('tb_tahun tt', 'tp.id_tahun = tt.id_tahun', 'left');
        $this->db->where('tt.id_tahun', $id_tahun);
        $this->db->group_by('tk.id_jilid');
        $this->db->order_by('tk.jilid', 'asc');


        return $this->db->get();
    }

    public function get_detail_data($id)
    {
        return $this->db->get_where('tb_pengajar', ['id_pengajar' => $id])->row_array();
    }

    public function get_detail_data_with_jilid_and_mapel($id_jilid, $id_mapel)
    {
        $this->db->select('tp.id_pengajar, tg.id_ustadz, tg.nama');
        $this->db->from('tb_ustadz tg');
        $this->db->join('tb_pengajar tp', 'tp.id_ustadz = tg.id_ustadz', 'inner');
        $this->db->join('tb_jilid tk', 'tk.id_jilid = tp.id_jilid', 'inner');
        $this->db->join('tb_pembelajaran tm', 'tm.id_mapel = tp.id_mapel', 'inner');
        $this->db->where('tm.id_mapel', $id_mapel);
        $this->db->where('tk.id_jilid', $id_jilid);
        return $this->db->get()->row_array();
    }

    public function get_count_pengampu($id_ustadz, $tahun)
    {
        $id_tahun = ($tahun) ? $tahun['id_tahun'] : 'null';
        $query = $this->db->query("
            select
                tm.jabatan ,count(tm.jumlah_jilid) as 'jumlah_jilid'
            from
                (
                select
                    tp.jabatan,
                    count(tp.id_jilid) as 'jumlah_jilid'
                from
                    tb_pengajar tp
                where
                    tp.id_ustadz = $id_ustadz
                    and tp.id_tahun = $id_tahun
                group by
                    tp.id_jilid) tm");
        return $query->row_array();
    }

    public function get_count_santri($id_ustadz, $tahun)
    {
        $tahun_ajaran = ($tahun) ? $tahun['nama'] : 'null';
        $query = $this->db->query("
        select 
            count(tm.jumlah_santri) as 'jumlah_santri'
        from (
            select 
                count(td.id_santri) as 'jumlah_santri' 
            from tb_pengajar tp
            inner join tb_jilid tk 
                on tk.id_jilid = tp.id_jilid 
            inner join tb_datasantri td 
                on td.id_jilid = tk.id_jilid 
            where tp.id_ustadz = $id_ustadz
                and td.tahun_ajaran = '$tahun_ajaran'
            group by td.id_santri) tm");
        return $query->row_array();
    }

    public function get_mapel_pengampu($id_ustadz, $id_jilid = NULL, $id_mapel = NULL, $id_tahun = NULL)
    {
        $this->db->select('*');
        $this->db->from('tb_pengajar tp');
        $this->db->join('tb_jilid tk', 'tk.id_jilid = tp.id_jilid', 'left');
        $this->db->join('tb_pembelajaran tm', 'tm.id_mapel = tp.id_mapel', 'left');
        $this->db->join('tb_tahun tt', 'tt.id_tahun = tp.id_tahun', 'left');
        $this->db->where('tp.id_ustadz', $id_ustadz);
        $this->db->group_by('tk.id_jilid');
        $this->db->order_by('tk.jilid', 'asc');

        if ($id_tahun) {
            $this->db->where('tt.id_tahun', $id_tahun);
        } else {
            $this->db->where('tt.status', '1');
        }

        if ($id_jilid) {
            $this->db->where('tp.id_jilid', $id_jilid);
        }

        if ($id_mapel) {
            $this->db->where('tp.id_mapel', $id_mapel);
        }

        return $this->db->get()->result();
    }

    public function delete_data($id)
    {
        $this->db->delete('tb_pengajar', ['id_pengajar' => $id]);
    }

    var $column_order = array(null, 'tg.nama', 'tp.jabatan', 'tm.nama_mapel', 'tm.level', 'tk.jilid', 'tahun', 'tt.toshi'); //Sesuaikan dengan field
    var $column_search = array('tg.nama'); //field yang diizin untuk pencarian 
    var $order = array('jilid' => 'asc'); // default order 

    private function _get_datatables_query()
    {
        $this->db->select('tp.id_pengajar, tg.nama, tp.jabatan, tm.nama_mapel, tm.level, tk.jilid, tt.nama as tahun, tt.toshi');
        $this->db->from('tb_pengajar tp');
        $this->db->join('tb_ustadz tg', 'tp.id_ustadz = tg.id_ustadz', 'left');
        $this->db->join('tb_pembelajaran tm', 'tp.id_mapel = tm.id_mapel', 'left');
        $this->db->join('tb_jilid tk', 'tp.id_jilid  = tk.id_jilid', 'left');
        $this->db->join('tb_tahun tt', 'tp.id_tahun = tt.id_tahun', 'left');
        // $this->db->order_by('tk.jilid', 'asc');
        // $this->db->order_by('tp.jabatan', 'asc');

        $i = 0;

        foreach ($this->column_search as $item) // looping awal
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

                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from('tb_pengajar');
        return $this->db->count_all_results();
    }
}
