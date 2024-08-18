<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ArsipNilai_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Fungsi untuk mendapatkan semua data arsip nilai
    public function get_all_arsip_nilai() {
        $query = $this->db->get('tb_arsipnilai');
        return $query->result_array();
    }
}
