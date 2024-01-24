<?php


namespace App\Controllers;

use App\Plugins\Di\Injectable;
use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;

class BaseController extends Injectable {


    /**
     * Template for creating a new row in the database
     */
    public function create($content){
        // Respond with a 501 code as the BaseController should not create any database rows, also the default for any inherited class that has not defined the function.
        (new Status\MethodNotAllowed(['message' => 'This endpoint has not (yet) been created']))->send();
    }

    public function getById($id){
        // Respond with a 501 code as the BaseController should not create any database rows, also the default for any inherited class that has not defined the function.
        (new Status\MethodNotAllowed(['message' => 'This endpoint has not (yet) been created']))->send();
    }

    public function getAll(){
        // Respond with a 501 code as the BaseController should not create any database rows, also the default for any inherited class that has not defined the function.
        (new Status\MethodNotAllowed(['message' => 'This endpoint has not (yet) been created']))->send();    
    }

    public function update($id, $content){
        // Respond with a 501 code as the BaseController should not create any database rows, also the default for any inherited class that has not defined the function.
        (new Status\MethodNotAllowed(['message' => 'This endpoint has not (yet) been created']))->send();
    }

    public function delete($id){
        // Respond with a 501 code as the BaseController should not create any database rows, also the default for any inherited class that has not defined the function.
        (new Status\MethodNotAllowed(['message' => 'This endpoint has not (yet) been created']))->send();    
    }


}
