<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
    	$this->view->users = Users::find();
    }

}
