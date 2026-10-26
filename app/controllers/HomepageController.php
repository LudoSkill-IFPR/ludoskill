<?php

namespace app\controllers;

use app\core\Controller;

class HomepageController extends Controller{
    public function homepage(){
        $this->view('entrada/homepage');
    }
    
    public function sobreNos(){
        $this->view('entrada/sobreNos');
    }

    public function login(){
        $sucesso = $_SESSION['mensagem_sucesso'] ?? null;
        unset($_SESSION['mensagem_sucesso']);
        $this->view('entrada/login', ['sucesso' => $sucesso]);
    }
}
