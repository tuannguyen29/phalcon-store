<?php

class AdminController extends ControllerBase
{

    public function adminAction()
    {
    	$this->view->users = Users::find();
    }

}
