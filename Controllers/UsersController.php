<?php
namespace Controllers;

use \Core\Controller;
use \Models\users;

class UsersController extends Controller {

    public function index(){}

    public function login()
    {
        $array = array('error'=>'');

        $method = $this->getMethod();
        $data = $this->getRequestData();

        if($method == 'POST'){
            if(!empty($data['email']) && !empty($data['pass'])){
                $user = new Users;
                if ($user->checkCredentials($data['email'], $data['pass'])){
                    $array['jwt'] = $user->createJwt();
                } else {
                    $array['error'] = 'Acesso negado!';
                }
                
            }
            
        } else {
            $array['error'] = 'Método requisição incompátivel';
        }

        $this->returnJson($array);
    }

}