<?php
namespace Controllers;

use \Core\Controller;
use \Models\Users;
use \Models\Photos;

class PhotosController extends Controller {

    public function index(){}

    public function random(){
        $array = array('error' => '', 'logged'=>false);

        $method = $this->getMethod();
        $data = $this->getRequestData();

        $users = new Users();
        $p = new Photos();

        if (!empty($data['jwt']) && $users->validateJwt($data['jwt'])) {
            $array['logged'] = true;

            if ($method == 'GET') {
                $offset = 0;
                if (!empty($data['offset'])) {
                    $offset = inval($data['offset']);
                }

                $per_page = 10;
                if (!empty($data['per_page'])) {
                    $per_page = inval($data['per_page']);
                }

                $excludes = array();
                if(!empty($data['excludes'])){
                    $excludes = explode(',', $data['excludes']);
                
                }

                $array['data'] = $p->getRandomPhotos($per_page, $excludes);
            } else {
                $data['error'] = 'Método '.$method.' não disponível.';
            }
        } else {
            $array['error'] = 'Acesso negado';
        }

        $this->returnJson($array);
    }
}
