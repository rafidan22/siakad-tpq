<?php
class Guru_model extends CI_Model
{
    public function get_data()
    {
        return $this->db->get('tb_ustadz')->result();
    }

    // public function get_count()
    // {
    //     return $this->db->get('tb_ustadz')->num_rows();
    // }
    public function get_count($tahun)
    {
        $tahun_ajaran = ($tahun) ? $tahun['nama'] : 'null';
        $this->db->from('tb_ustadz tg');
        $this->db->join('tb_pengajar tp', 'tp.id_ustadz = tg.id_ustadz', 'inner');
        $this->db->join('tb_tahun tt', 'tt.id_tahun = tp.id_tahun', 'inner');
        $this->db->where('tt.nama', $tahun_ajaran);
        $this->db->group_by('tg.id_ustadz');
        return $this->db->get()->num_rows();
    }

    public function get_data_only_name()
    {
        return $this->db->query("select tg.nama, tg.id_user 
            from
                tb_ustadz tg
            where
                tg.nama not in (
                select
                    tk.ustadz_ustadzah
                from
                    tb_jilid tk
                where
                    tk.ustadz_ustadzah = tg.nama)")->result();
    }

    public function get_detail_data($id, $id_user = NULL, $name = NULL)
    {
        if ($id_user) {
            return $this->db->get_where('tb_ustadz', ['id_user' => $id_user])->row_array();
        } elseif ($name) {
            return $this->db->get_where('tb_ustadz', ['nama' => $name])->row_array();
        } else {
            return $this->db->get_where('tb_ustadz', ['id_ustadz' => $id])->row_array();
        }
    }

    private function _input_user()
    {
        $date = date_create($this->input->post('tanggal_lahir', TRUE));
        $dateFormat = date_format($date, "mY");
        $data = array(
            'username'  => $this->input->post('email', TRUE),
            'password'  => MD5($dateFormat),
            'level'     => 'guru',
            'status'    => '1'
        );

        $this->db->insert('tb_user', $data);
        return $this->db->insert_id();
    }

    public function input_data($photo)
    {
        $id_user = $this->_input_user();
        $data = array(
            'nip'           => $this->input->post('nip', TRUE),
            'nama'          => $this->input->post('nama', TRUE),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', TRUE),
            'no_hp'         => $this->input->post('no_hp', TRUE),
            'email'         => $this->input->post('email', TRUE),
            'alamat'        => $this->input->post('alamat', TRUE),
            'photo'         => $photo,
            'id_user'       => $id_user
        );

        $this->db->insert('tb_ustadz', $data);
    }

    public function edit_data($id, $photo, $name_guru)
    {
        $dataDetail = $this->get_detail_data($id);
        $dataWali = $this->db->get_where('tb_jilid', ['ustadz_ustadzah' => $name_guru])->num_rows();

        $dataUser = array(
            'username'       => $this->input->post('email', TRUE),
        );
        $dataWali = array(
            'ustadz_ustadzah'     => $this->input->post('nama', TRUE),
        );
        $data = array(
            'nip'       => $this->input->post('nip', TRUE),
            'nama'      => $this->input->post('nama', TRUE),
            'jenis_kelamin' => $this->input->post('jenis_kelamin', TRUE),
            'tanggal_lahir' => $this->input->post('tanggal_lahir', TRUE),
            'no_hp'     => $this->input->post('no_hp', TRUE),
            'email'     => $this->input->post('email', TRUE),
            'alamat'    => $this->input->post('alamat', TRUE)
        );

        if ($photo != null) {
            $data['photo'] = $photo;
        }

        $this->db->where('id_ustadz', $id);
        $this->db->update('tb_ustadz', $data);

        $this->db->where('username', $dataDetail['email']);
        $this->db->update('tb_user', $dataUser);

        if ($dataWali > 0) {
            $this->db->where('ustadz_ustadzah', $name_guru);
            $this->db->update('tb_jilid', $dataWali);
        }
    }

    public function delete_data($id)
    {
        $this->db->delete('tb_ustadz', ['id_ustadz' => $id]);
    }

    var $column_order = array(null, 'nip', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'no_hp', 'email', 'alamat'); //Sesuaikan dengan field
    var $column_search = array('nama', 'no_hp', 'email', 'alamat'); //field yang diizin untuk pencarian 
    var $order = array('nama' => 'asc'); // default order 

    private function _get_datatables_query()
    {

        $this->db->from('tb_ustadz');

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
        $this->db->from('tb_ustadz');
        return $this->db->count_all_results();
    }
}
