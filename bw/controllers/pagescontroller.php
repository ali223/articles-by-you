<?phpnamespace BW\controllers;class PagesController extends BaseController {        public function error(){        //echo "<br /> an error occurred.";        header("HTTP/1.0 404 Not Found");        $errormsg = "Oops! The page you are looking for does not exist.";        if($this->isLoggedIn()){            include("views/userheader.php");                   } else {        include("views/header.php");        }        include("views/pages/error.php");        include("views/footer.php");            }        public function contact(){        $this->view->setContentFile("views/pages/contact.php");        $this->view->renderView();    }}     ?>