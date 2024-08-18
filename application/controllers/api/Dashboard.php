<?php

use chriskacerguis\RestServer\RestController;

class Dashboard extends RestController
{

    public function index_get()
    {
        $id_user = $this->get('id_user');
        $level = $this->input->get('level');

        if ($level == 'admin' && isset($id_user)){
            $tahun = $this->Tahun_model->get_active_stats();
            $santri = $this->santri_model->get_count_allsantri($tahun);
            $jilid = $this->jilid_model->get_count();
            $guru = $this->Guru_model->get_count($tahun);

            $data = [
                'school_year'   => $tahun['nama'],
                'toshi'      => $tahun['toshi'],
                'students'      => $santri,
                'class'         => $jilid,
                'teachers'      => $guru
            ];


            $this->response(['status' => 200, 'messages' => 'success', 'dashboard' => $data], RestController::HTTP_OK);
        }
    }
}
