<?php
class Peserta_model extends CI_Model
{
    public function input_data($tahun)
    {
        $peserta = $this->input->post('santri', TRUE);
        if (!empty($peserta)) {
            foreach ($peserta as $ps) {
                $data = array(
                    'id_santri'      => $ps,
                    'id_jilid'      => $this->input->post('jilid', TRUE),
                    'tahun_ajaran'  => $tahun
                );

                $this->db->insert('tb_datasantri', $data);
            }
        }
    }

    public function get_data_jilid($id_jilid, $tahun)
    {
        $this->db->select('td.id_datasantri, ts.id_santri, ts.nis, ts.tgl_masuk, ts.nama, ts.jenis_kelamin, ts.asal_sekolah, tk.jilid');
        $this->db->from('tb_datasantri td');
        $this->db->join('tb_santri ts', 'ts.id_santri = td.id_santri', 'inner');
        $this->db->join('tb_jilid tk', 'tk.id_jilid = td.id_jilid', 'inner');
        $this->db->where('td.id_jilid', $id_jilid);
        $this->db->where('td.tahun_ajaran', $tahun);
        $this->db->order_by('ts.nis', 'asc');

        return $this->db->get()->result();
    }

    public function update_jilid()
    {
        $tahun       = $this->input->post('oldtahun', TRUE);
        $newtahun    = $this->input->post('newtahun', TRUE);
        $oldjilid    = $this->input->post('oldjilid', TRUE);
        $get_oldjilid = $this->get_data_jilid($oldjilid, $tahun);

        if ($get_oldjilid) {
            foreach ($get_oldjilid as $go) {
                $data = array(
                    'id_santri'     => $go->id_santri,
                    'id_jilid'      => $this->input->post('newjilid', TRUE),
                    'tahun_ajaran'  => $newtahun
                );

                // Instead of insert, update the existing record if it's the same year
                if ($tahun == $newtahun) {
                    $this->db->where('id_datasantri', $go->id_datasantri);
                    $this->db->update('tb_datasantri', $data);
                } else {
                    $this->db->insert('tb_datasantri', $data);
                }
            }
        }
    }
    
    

    public function get_jilid($id_santri, $tahun)
    {
        return $this->db->get_where('tb_datasantri', ['id_santri' => $id_santri, 'tahun_ajaran' => $tahun])->row_array();
    }

    public function delete_data($id_datasantri)
    {
        $this->db->delete('tb_datasantri', ['id_datasantri' => $id_datasantri]);
    }
}
