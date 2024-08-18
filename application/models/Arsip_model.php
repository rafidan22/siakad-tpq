<?php 
class Arsip_model extends CI_Model
{
    public function input_nilai($nilai)
    {
        foreach ($nilai as $key => $value) {
            $data = array(
                'id_datasantri' => $value->id_datasantri,
                'id_materi'     => $value->id_materi,
                'jenis'         => $value->jenis,
                'nilai'         => $value->nilai,
            );
            $this->db->insert('tb_arsipnilai', $data);
        }
    }

    public function cancel_nilai($nilai, $jenis)
    {
        foreach ($nilai as $key => $value) {
            $data = array(
                'id_datasantri' => $value->id_datasantri,
                'id_materi'     => $value->id_materi,
                'jenis'         => $jenis,
                'nilai'         => $value->nilai,
            );
            $this->db->insert('tb_nilai', $data);
        }
    }

    public function delete_nilai($id_jilid, $id_materi, $jenis, $tahun)
    {
        $this->db->query("DELETE tn FROM tb_arsipnilai tn 
            INNER JOIN tb_datasantri td ON tn.id_datasantri = td.id_datasantri
            WHERE tn.id_materi = $id_materi
            AND td.id_jilid = $id_jilid
            AND td.tahun_ajaran = '$tahun'
            AND tn.jenis = '$jenis'");
    }

    public function get_jenis_nilai_in_perkd($id_jilid = null, $id_mapel = null, $id_materi = null, $tahun = null)
    {
        $query = $this->_get_jenis_nilai_inperkd($id_jilid, $id_mapel, $id_materi, $tahun);
        return $query->result();
    }

    public function get_jenis_nilai_in_perkd_array($id_jilid = null, $id_mapel = null, $id_materi = null, $tahun = null)
    {
        $query = $this->_get_jenis_nilai_inperkd($id_jilid, $id_mapel, $id_materi, $tahun);
        return $query->result_array();
    }

    private function _get_jenis_nilai_inperkd($id_jilid = null, $id_mapel = null, $id_materi = null, $tahun = null)
    {
        $jilid = $id_jilid != null ? $id_jilid : 'null';
        $mapel = $id_mapel != null ? $id_mapel : 'null';
        $tahun = $tahun != null ? $tahun : 'null';
        $kd = $id_materi != null ? $id_materi : 'null';
        $query = $this->db->query("SELECT tn.jenis FROM tb_arsipnilai tn  
            LEFT JOIN tb_kd tk ON tn.id_materi = tk.id_materi 
            LEFT JOIN tb_pembelajaran tm ON tk.id_mapel = tm.id_mapel
            LEFT JOIN tb_datasantri td ON tn.id_datasantri = td.id_datasantri
            LEFT JOIN tb_santri ts ON td.id_santri = ts.id_santri
            LEFT JOIN tb_pengajar tp ON td.id_jilid = tp.id_jilid
            LEFT JOIN tb_tahun tt ON tp.id_tahun = tt.id_tahun  
            WHERE tt.status = '1' 
            AND tm.id_mapel = $mapel
            AND tk.id_materi = $kd
            AND td.id_jilid = $jilid
            AND td.tahun_ajaran = '$tahun'
            GROUP BY tn.jenis");
        return $query;
    }

    public function get_nilai_perkd($id_jilid, $id_mapel, $id_materi, $tahun)
    {
        $jilid = $id_jilid != null ? $id_jilid : 'null';
        $mapel = $id_mapel != null ? $id_mapel : 'null';
        $kd = $id_materi != null ? $id_materi : 'null';
        $tahun = $tahun != null ? $tahun : 'null';
        $jenis_nilai = $this->get_jenis_nilai_in_perkd($id_jilid, $id_mapel, $id_materi, $tahun);
        $query_select = "";

        foreach ($jenis_nilai as $jn => $value) {
            $query_select .= "SUM(IF(nilai.jenis = '$value->jenis', nilai.nilai, NULL)) AS '$value->jenis', ";
        }

        $query_select = rtrim($query_select, ", ");

        if ($query_select != null) {
            $query = $this->db->query("SELECT ts.nis, ts.tgl_masuk, ts.nama, $query_select, jm.jumlah, jm.rerata 
                FROM tb_santri ts
                INNER JOIN (
                    SELECT td.id_santri, tn.nilai, tn.jenis 
                    FROM tb_arsipnilai tn  
                    LEFT JOIN tb_kd tk ON tn.id_materi = tk.id_materi 
                    LEFT JOIN tb_pembelajaran tm ON tk.id_mapel = tm.id_mapel
                    LEFT JOIN tb_pengajar tp ON tm.id_mapel = tp.id_mapel
                    LEFT JOIN tb_jilid tk2 ON tp.id_jilid = tk2.id_jilid
                    LEFT JOIN tb_datasantri td ON tn.id_datasantri = td.id_datasantri
                    LEFT JOIN tb_tahun tt ON tp.id_tahun = tt.id_tahun 
                    WHERE tt.status = '1'
                    AND tm.id_mapel = $mapel
                    AND tk.id_materi = $kd
                    AND tk2.id_jilid = $jilid
                    AND td.tahun_ajaran = '$tahun') nilai ON nilai.id_santri = ts.id_santri    
                INNER JOIN (
                    SELECT td.id_santri, SUM(tn.nilai) AS jumlah, ROUND(AVG(tn.nilai)) AS rerata 
                    FROM tb_arsipnilai tn 
                    INNER JOIN tb_datasantri td ON tn.id_datasantri = td.id_datasantri 
                    INNER JOIN tb_kd tk ON tn.id_materi = tk.id_materi
                    INNER JOIN tb_pembelajaran tm ON tk.id_mapel = tm.id_mapel
                    WHERE tm.id_mapel = $mapel
                    AND tk.id_materi = $kd
                    AND td.id_jilid = $jilid
                    GROUP BY td.id_santri) jm ON jm.id_santri = ts.id_santri
                GROUP BY ts.nis");
            return $query->result_array();
        } else {
            return null;
        }
    }

    public function detail_nilai_perkd($id_jilid, $id_mapel, $id_materi, $jenis, $tahun)
    {
        $query = $this->db->query("SELECT td.id_datasantri, ts.id_santri, ts.nis, ts.nama, tn.nilai, tn.jenis, tk.id_materi 
            FROM tb_arsipnilai tn  
            LEFT JOIN tb_kd tk ON tn.id_materi = tk.id_materi 
            LEFT JOIN tb_pembelajaran tm ON tk.id_mapel = tm.id_mapel
            LEFT JOIN tb_datasantri td ON tn.id_datasantri = td.id_datasantri 
            LEFT JOIN tb_santri ts ON td.id_santri = ts.id_santri
            WHERE tm.id_mapel = $id_mapel
            AND tk.id_materi = $id_materi
            AND td.id_jilid = $id_jilid
            AND tn.jenis = '$jenis'
            AND td.tahun_ajaran = '$tahun'");
        return $query->result();
    }

    public function get_all_archived_values()
    {
        // Adjust the query based on your database structure
        $this->db->select('*');
        $this->db->from('tb_arsipnilai'); // Replace with your actual table name
        $query = $this->db->get();
        return $query->result_array();
    }
}
?>
