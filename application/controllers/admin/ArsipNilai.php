<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ArsipNilai extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        // Set headers to prevent caching
        $this->output->set_header('Cache-Control: no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');

        // Check if the user is logged in and has 'admin' level access
        if (!$this->session->userdata('username') || $this->session->userdata('level') != 'admin') {
            $this->session->set_flashdata('message', 'Anda Belum Login!');
            redirect('login');
        }

        // Load the ArsipNilai_model
        $this->load->model('ArsipNilai_model');
    }

    // Function to display the arsip nilai data
    public function index() {
        $data['arsip_nilai'] = $this->ArsipNilai_model->get_all_arsip_nilai();
        $this->load->view('admin/arsip_nilai_view', $data);
    }
}
