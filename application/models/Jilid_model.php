<?php
class jilid_model extends CI_Model
{
    // public function get_data()
    // {
    //     $this->db->from('tb_jilid tk');
    //     $this->db->join('tb_pengajar tp', 'tp.id_jilid = tk.id_jilid', 'inner');
    //     $this->db->join('tb_tahun tt', 'tt.id_tahun = tp.id_tahun', 'inner');
    //     $this->db->where('tt.status', '1');
    //     $this->db->order_by('tk.jilid', 'asc');
    //     $this->db->group_by('tk.id_jilid');
    //     return $this->db->get()->result();
    // }

    public function get_data()
    {
        $this->db->from('tb_jilid');
        $this->db->order_by('jilid', 'asc');
        return $this->db->get()->result();
    }

    public function get_like_data($query)
    {
        $this->db->like('jilid', $query, 'both');
        return $this->db->get('tb_jilid');
    }

    public function get_like_walikelas($query)
    {
        $this->db->like('ustadz_ustadzah', $query, 'both');
        return $this->db->get('tb_jilid')->row_array();
    }

    public function get_jilid_with_name($name)
    {
        return $this->db->get_where('tb_jilid', ['ustadz_ustadzah' => $name])->result();
    }

    public function get_count()
    {
        return $this->db->get('tb_jilid')->num_rows();
    }

    public function get_detail_data($id)
    {
        return $this->db->get_where('tb_jilid', ['id_jilid' => $id])->row_array();
    }

    public function get_detail_santri($id, $tahun)
    {
        $tahun = ($tahun) ? $tahun['nama'] : 'null';
        $this->db->from('tb_datasantri td');
        $this->db->join('tb_jilid tk', 'tk.id_jilid = td.id_jilid', 'inner');
        $this->db->where('td.id_santri', $id);
        $this->db->where('td.tahun_ajaran', $tahun);
        return $this->db->get()->row_array();
    }

    public function get_id_jilid()
    {
        $jilid = $this->input->post('jilid', TRUE);
        $this->db->select('id_jilid');
        $this->db->where('jilid', $jilid);
        $row = $this->db->get('tb_jilid')->row();
        return $row->id_jilid;
    }

    public function input_data()
    {
        $guru = explode("-", $this->input->post('ustadz_ustadzah', TRUE));
        $ustadz_ustadzah = $guru[0];
        $id_user = $guru[1];

        $data = array(
            'jilid'         => $this->input->post('jilid', TRUE),
            'ustadz_ustadzah'    => $ustadz_ustadzah,
        );
        $dataUser = array(
            'level'     => 'ustadz'
        );

        $this->db->insert('tb_jilid', $data);

        $this->db->where('id_user', $id_user);
        $this->db->update('tb_user', $dataUser);
    }

    public function edit_data($id, $id_old_user)
    {
        $guru = explode("-", $this->input->post('ustadz_ustadzah', TRUE));
        $ustadz_ustadzah = $guru[0];
        $id_user = $guru[1];

        $data = array(
            'jilid'         => $this->input->post('jilid', TRUE),
            'ustadz_ustadzah'    => $ustadz_ustadzah,
        );

        $dataUser = array(
            'level'     => 'ustadz'
        );

        $dataOldUser = array(
            'level'     => 'guru'
        );

        $this->db->where('id_user', $id_old_user);
        $this->db->update('tb_user', $dataOldUser);

        $this->db->where('id_jilid', $id);
        $this->db->update('tb_jilid', $data);

        $this->db->where('id_user', $id_user);
        $this->db->update('tb_user', $dataUser);
    }

    public function delete_data($id)
    {
        $detail     = $this->get_detail_data($id);
        $guru       = $this->db->get_where('tb_ustadz', ['nama' => $detail['ustadz_ustadzah']])->row_array();
        $dataUser   = array('level' => 'guru');

        $this->db->where('id_user', $guru['id_user']);
        $this->db->update('tb_user', $dataUser);

        $this->db->delete('tb_jilid', ['id_jilid' => $id]);
    }

    public function delete_walikelas($nama)
    {
        $data = array(
            'ustadz_ustadzah'    => NULL,
        );

        $this->db->where('ustadz_ustadzah', $nama);
        $this->db->update('tb_jilid', $data);
    }

    var $column_order = array(null, 'jilid', 'ustadz_ustadzah'); //Sesuaikan dengan field
    var $column_search = array('jilid', 'ustadz_ustadzah'); //field yang diizin untuk pencarian 
    var $order = array('jilid' => 'asc'); // default order 

    private function _get_datatables_query()
    {

        $this->db->from('tb_jilid');
        $this->db->order_by('jilid', 'asc');

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
        $this->db->from('tb_jilid');
        return $this->db->count_all_results();
    }
}
