<?php

namespace Test\Controller;

use Test\lib\Controller;
use Test\lib\Model;

class Main extends Controller
{
    /**
     * @return string
     */
    public function indexAction()
    {
        /** @var Model $user */
        $model = $this->getUser();
        if ($user = $model->get()) {
            return $this->render('index.phtml', ['user' => $user]);
        }

        return $this->redirect('user.login');
    }
}