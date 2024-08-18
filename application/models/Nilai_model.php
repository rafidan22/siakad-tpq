<?php
class Nilai_model extends CI_Model
{
    public function get_nilai_perkd($id_jilid, $id_mapel, $id_materi, $tahun)
    {
        $jilid = $id_jilid != null ? $id_jilid : 'null';
        $mapel = $id_mapel != null ? $id_mapel : 'null';
        $kd = $id_materi != null ? $id_materi : 'null';
        $tahun = $tahun != null ? $tahun : 'null';
        $jenis_nilai = $this->get_jenis_nilai_in_perkd($id_jilid, $id_mapel, $id_materi, $tahun);
        $query_select = "";

        foreach ($jenis_nilai as $jn => $value) {
            $query_select = $query_select . "sum( if ( nilai.jenis = '$value->jenis', nilai.nilai, null)) as '$value->jenis', ";
        }

        $query_select = substr($query_select, 0, -2);

        if ($query_select != null) {
            $query = $this->db->query("select ts.nis,ts.nama, $query_select, jm.jumlah, jm.rerata from tb_santri ts
                inner join (
                    select td.id_santri, tn.nilai, tn.jenis from tb_nilai tn  
                        left join tb_kd tk 
                            on tn.id_materi = tk.id_materi 
                        left join tb_pembelajaran tm 
                            on tk.id_mapel = tm.id_mapel
                        left join tb_pengajar tp 
                            on tm.id_mapel = tp.id_mapel
                        left join tb_jilid tk2 
                            on tp.id_jilid =tk2.id_jilid
                        left join tb_datasantri td 
                            on tn.id_datasantri = td.id_datasantri
                        left join tb_tahun tt 
                            on tp.id_tahun = tt.id_tahun 
                    where tt.status = '1'
                        and tm.id_mapel = $mapel
                        and tk.id_materi = $kd
                        and tk2.id_jilid = $jilid
                        and td.tahun_ajaran = '$tahun') nilai on nilai.id_santri = ts.id_santri    
                inner join (
                    select td.id_santri, sum(tn.nilai) as jumlah, round(avg(tn.nilai)) as rerata 
                    from tb_nilai tn 
                        inner join tb_datasantri td 
                            on tn.id_datasantri = td.id_datasantri 
                        inner join tb_kd tk 
                            on tn.id_materi = tk.id_materi
                        inner join tb_pembelajaran tm 
                            on tk.id_mapel = tm.id_mapel
                    where 
                        tm.id_mapel = $mapel
                        and tk.id_materi = $kd
                        and td.id_jilid = $jilid
                    group by td.id_santri) jm on jm.id_santri = ts.id_santri
                group by ts.nis");
            return $query->result_array();
        } else {
            return null;
        }
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
        $query = $this->db->query("select tn.jenis from tb_nilai tn  
            left join tb_kd tk 
                on tn.id_materi = tk.id_materi 
            left join tb_pembelajaran tm 
                on tk.id_mapel = tm.id_mapel
            left join tb_datasantri td 
                on tn.id_datasantri = td.id_datasantri
            left join tb_santri ts 
                on td.id_santri = ts.id_santri
            left join tb_pengajar tp 
                on td.id_jilid = tp.id_jilid
            left join tb_tahun tt 
                on tp.id_tahun = tt.id_tahun  
            where
                tt.status = '1' 
                and tm.id_mapel = $mapel
                and tk.id_materi = $kd
                and td.id_jilid = $jilid
                and td.tahun_ajaran = '$tahun'
            group by tn.jenis");
        return $query;
    }

    public function get_nilai_permapel($id_mapel, $id_jilid, $view, $id_tahun = NULL, $jenis_nilai)
    {
        $jilid          = $id_jilid != null ? $id_jilid : 'null';
        $mapel          = $id_mapel != null ? $id_mapel : 'null';
        $jenis_nilai    = $jenis_nilai != null ? $jenis_nilai : 'null';
        $kd             = $this->get_kd_permapel_result($mapel, $jilid, $jenis_nilai);
        $kd_row         = $this->get_kd_permapel_numrow($mapel, $jilid, $jenis_nilai);
        $query_select   = "";
        $query_join     = "";

        foreach ($kd as $key => $value) {
            switch ($view) {
                case 'min':
                    $query_select = $query_select . "min(round(kd$key.rerata)) as '$value->nama_materi', ";
                    break;
                case 'max':
                    $query_select = $query_select . "max(round(kd$key.rerata)) as '$value->nama_materi', ";
                    break;
                case 'jumlah':
                    $query_select = $query_select . "sum(round(kd$key.rerata)) as '$value->nama_materi', ";
                    break;
                case 'rerata':
                    $query_select = $query_select . "round(avg(kd$key.rerata)) as '$value->nama_materi', ";
                    break;
                default:
                    $query_select = $query_select . "round(kd$key.rerata) as '$value->nama_materi', ";
                    break;
            }

            $query_join = $query_join . "
                inner join (
                    select ts.id_santri, ts.nis, ts.nama, sum(tn.nilai) as jumlah, avg(tn.nilai) as rerata 
                    from tb_nilai tn
                    inner join tb_datasantri td 
                        on tn.id_datasantri = td.id_datasantri 
                    inner join tb_santri ts 
                        on td.id_santri = ts.id_santri 
                    inner join tb_kd tk 
                        on tn.id_materi = tk.id_materi
                    inner join tb_pembelajaran tm 
                        on tk.id_mapel = tm.id_mapel
                    inner join tb_pengajar tp 
                        on tm.id_mapel = tp.id_mapel
                    inner join tb_tahun tt 
                        on tp.id_tahun = tt.id_tahun
                    where tk.jenis_penilaian = '$jenis_nilai' and";
            if ($id_tahun) {
                $query_join = $query_join . " tt.id_tahun = $id_tahun";
            } else {
                $query_join = $query_join . " tt.status = '1'";
            }

            $query_join = $query_join . "
                        and tm.id_mapel = $mapel
                        and tk.id_materi = {$value->id_materi}
                        and td.id_jilid = $jilid
                    group by ts.id_santri ) kd$key on ts.id_santri = kd$key.id_santri";
        }

        switch ($view) {
            case 'min':
                $query_select = $query_select . "min(round(nm.jumlah)) as jumlah, min(nm.rerata) as rerata";
                break;
            case 'max':
                $query_select = $query_select . "max(round(nm.jumlah)) as jumlah, max(nm.rerata) as rerata";
                break;
            case 'jumlah':
                $query_select = $query_select . "sum(round(nm.jumlah)) as jumlah, sum(nm.rerata) as rerata";
                break;
            case 'rerata':
                $query_select = $query_select . "round(avg(nm.jumlah)) as jumlah, round(avg(nm.rerata)) as rerata";
                break;
            default:
                $query_select = $query_select . "nm.jumlah as jumlah, nm.rerata as rerata";
                break;
        }

        if ($query_select != null || $query_join != null) {
            $query_join = $query_join . "
                inner join(
                        select ts.id_santri, ts.nis, ts.nama, round(sum(tn.nilai)/$kd_row) as jumlah, round(avg(tn.nilai)) as rerata 
                        from tb_nilai tn
                        inner join tb_datasantri td 
                            on tn.id_datasantri = td.id_datasantri 
                        inner join tb_santri ts 
                            on td.id_santri = ts.id_santri 
                        inner join tb_kd tk 
                            on tn.id_materi = tk.id_materi
                        inner join tb_pembelajaran tm 
                            on tk.id_mapel = tm.id_mapel
                        where 
                            tm.id_mapel = $mapel
                            and td.id_jilid = $jilid
                            and tk.jenis_penilaian = '$jenis_nilai'
                        group by ts.id_santri) nm on ts.id_santri = nm.id_santri";

            $query = $this->db->query("select ts.id_santri, ts.nis, ts.nama, $query_select from tb_santri ts $query_join order by ts.nis asc");
            return $query->result_array();
        } else {
            return null;
        }
    }

    public function get_kd_permapel_numrow($id_mapel = null, $id_jilid = null, $jenis_nilai = null)
    {
        return $this->_get_kd_permapel($id_mapel, $id_jilid, $jenis_nilai)->num_rows();
    }

    public function get_kd_permapel_result($id_mapel = null, $id_jilid = null, $jenis_nilai = null)
    {
        return $this->_get_kd_permapel($id_mapel, $id_jilid, $jenis_nilai)->result();
    }

    public function get_kd_permapel_array($id_mapel = null, $id_jilid = null, $jenis_nilai = null)
    {
        return $this->_get_kd_permapel($id_mapel, $id_jilid, $jenis_nilai)->result_array();;
    }

    private function _get_kd_permapel($id_mapel = null, $id_jilid = null, $jenis_nilai = null)
    {
        $this->db->select('tk.*');
        $this->db->from('tb_kd tk');
        $this->db->join('tb_nilai tn', 'tk.id_materi = tn.id_materi', 'inner');
        $this->db->join('tb_datasantri td', 'tn.id_datasantri = td.id_datasantri', 'inner');
        $this->db->join('tb_santri ts', 'td.id_santri = ts.id_santri', 'inner');
        $this->db->where('tk.id_mapel', $id_mapel);
        $this->db->where('tk.jenis_penilaian', $jenis_nilai);
        $this->db->where('td.id_jilid', $id_jilid);
        $this->db->group_by('tk.id_materi');
        $this->db->order_by('tk.nama_materi', 'asc');
        return $this->db->get();
    }

    public function input_nilai($data_murid, $id_materi)
    {
        foreach ($data_murid as $key => $value) {
            $data = array(
                'id_datasantri'      => $value->id_datasantri,
                'id_materi'         => $id_materi,
                'jenis'         => $this->input->post('jenis', TRUE),
                'nilai'         => $this->input->post('nilai' . $key, TRUE),
            );
            $this->db->insert('tb_nilai', $data);
        }
    }

    public function update_nilai($data_murid, $id_materi, $jenis)
    {
        foreach ($data_murid as $key => $value) {
            $data = array(
                'nilai'         => $this->input->post('nilai' . $key, TRUE),
            );
            $this->db->where('id_datasantri', $value->id_datasantri);
            $this->db->where('id_materi', $id_materi);
            $this->db->where('jenis', $jenis);
            $this->db->update('tb_nilai', $data);
        }
    }

    public function delete_nilai($id_jilid, $id_materi, $jenis, $tahun)
    {
        $this->db->query("delete tn from tb_nilai tn 
            inner join tb_datasantri td on tn.id_datasantri = td.id_datasantri
            where tn.id_materi = $id_materi
            and td.id_jilid = $id_jilid
            and td.tahun_ajaran = '$tahun'
            and tn.jenis = '$jenis'");
    }

    public function detail_nilai_perkd($id_jilid, $id_mapel, $id_materi, $jenis, $tahun)
    {
        $query = $this->db->query("select td.id_datasantri, ts.id_santri, ts.nis, ts.nama, tn.nilai, tn.jenis, tk.id_materi 
            from tb_nilai tn  
                left join tb_kd tk 
                    on tn.id_materi = tk.id_materi 
                left join tb_pembelajaran tm 
                    on tk.id_mapel = tm.id_mapel
                left join tb_datasantri td
                    on tn.id_datasantri = td.id_datasantri 
                left join tb_santri ts 
                    on td.id_santri = ts.id_santri
            where 
                tm.id_mapel = $id_mapel
                and tk.id_materi = $id_materi
                and td.id_jilid = $id_jilid
                and tn.jenis = '$jenis'
                and td.tahun_ajaran = '$tahun'");

        return $query->result();
    }

    public function nilai_persantri($id_santri, $id_jilid, $id_tahun)
    {
        $query = $this->db->query("
            select
                tm.nama_mapel,
                coalesce(individu.nilai, 0) as individu,
                coalesce(tambahan.nilai, 0) as tambahan,
                coalesce(individu.nilai, 0) + coalesce(tambahan.nilai, 0) as 'jumlah',
                round((coalesce(individu.nilai, 0) + coalesce(tambahan.nilai, 0))/2) as 'rerata'
            from
                tb_pembelajaran tm
            left join (
                select
                    ts.id_santri,
                    ts.nis,
                    ts.nama,
                    round(avg(tn.nilai)) as nilai,
                    tm.id_mapel,
                    tm.nama_mapel
                from
                    tb_nilai tn
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
                inner join tb_tahun tt on
                    tp.id_tahun = tt.id_tahun
                where
                    td.id_jilid = $id_jilid
                    and tk.jenis_penilaian = 'individu'
                    and tt.shared = '1'
                    and tt.id_tahun = $id_tahun
                    and td.id_santri = $id_santri
                group by
                    tm.id_mapel) individu on
                individu.id_mapel = tm.id_mapel
            left join (
                select
                    ts.id_santri,
                    ts.nis,
                    ts.nama,
                    round(avg(tn.nilai)) as nilai,
                    tm.id_mapel,
                    tm.nama_mapel
                from
                    tb_nilai tn
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
                inner join tb_tahun tt on
                    tp.id_tahun = tt.id_tahun
                where
                    td.id_jilid = $id_jilid
                    and tk.jenis_penilaian = 'tambahan'
                    and tt.shared = '1'
                    and tt.id_tahun = $id_tahun
                    and td.id_santri = $id_santri
                group by
                    tm.id_mapel) tambahan on
                tambahan.id_mapel = tm.id_mapel
            left join tb_pengajar tp2 on
                tp2.id_mapel = tm.id_mapel
            where
                tp2.id_jilid = $id_jilid
            group by
                tm.id_mapel
        ");

        return $query->result();
    }

    public function total_nilai_persantri($id_santri, $id_jilid, $id_tahun)
    {
        $query = $this->db->query("
            select
                sum(round((coalesce(individu.nilai, 0) + coalesce(tambahan.nilai, 0))/2)) as 'jumlah',
                round(avg(round((coalesce(individu.nilai, 0) + coalesce(tambahan.nilai, 0))/2))) as 'rerata'
            from
                tb_pembelajaran tm
            left join (
                select
                    ts.id_santri,
                    ts.nis,
                    ts.nama,
                    round(avg(tn.nilai)) as nilai,
                    tm.id_mapel,
                    tm.nama_mapel
                from
                    tb_nilai tn
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
                inner join tb_tahun tt on
                    tp.id_tahun = tt.id_tahun
                where
                    td.id_jilid = $id_jilid
                    and tk.jenis_penilaian = 'individu'
                    and tt.shared = '1'
                    and tt.id_tahun = $id_tahun
                    and td.id_santri = $id_santri
                group by
                    tm.id_mapel) individu on
                individu.id_mapel = tm.id_mapel
            left join (
                select
                    ts.id_santri,
                    ts.nis,
                    ts.nama,
                    round(avg(tn.nilai)) as nilai,
                    tm.id_mapel,
                    tm.nama_mapel
                from
                    tb_nilai tn
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
                inner join tb_tahun tt on
                    tp.id_tahun = tt.id_tahun
                where
                    td.id_jilid = $id_jilid
                    and tk.jenis_penilaian = 'tambahan'
                    and tt.shared = '1'
                    and tt.id_tahun = $id_tahun
                    and td.id_santri = $id_santri
                group by
                    tm.id_mapel) tambahan on
                tambahan.id_mapel = tm.id_mapel
            left join tb_pengajar tp2 on
                tp2.id_mapel = tm.id_mapel
            where
                tp2.id_jilid = $id_jilid
        ");

        return $query->row_array();
    }
}
