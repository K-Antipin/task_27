<?php

namespace App\controllers;

use App\core\Controller;
use App\data\DB;

class Home extends Controller
{
    public function index()
    {
        $payload = DB::getAll('SELECT images.*, users.name FROM `images` INNER JOIN users ON (images.user_id = users.id)');
        $this->view->render('home.phtml', 'template.phtml', $payload);
    }
    public function about()
    {
        $this->view->render('about.phtml', 'template.phtml');
    }
    public function help()
    {
        $this->view->render('help.phtml', 'template.phtml');
    }
}
