<?php

namespace App\Controllers;

use App\Plugins\Http\Response as Status;

class IndexController extends BaseController {
    /**
     * Controller function used to test whether the project was set up properly.
     * @return void
     */
    public function test() {
        // Respond with 200 (OK):
        (new Status\Ok(['message' => 'Hello world!']))->send();
    }

    /**
     * Controller function used to give a 404 response when a route is not defined in the routes.php file.
     * @return void
     */
    public function notFound() {
        (new Status\NotFound(['message' => 'The requested resource was not found']))->send();
    }
}