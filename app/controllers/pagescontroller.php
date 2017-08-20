<?phpnamespace App\controllers;class PagesController {        protected $view;    protected $sessionUtility;    public function __construct(View $view, SessionUtility $sessionUtility) {        $this->view = $view;          $this->sessionUtility = $sessionUtility;         }        public function error(){               header("HTTP/1.0 404 Not Found");        $errormsg = "Oops! The page you are looking for does not exist.";        if($this->sessionUtility->isLoggedIn()){            $this->view->setHeaderFile("views/userheader.php");        }        $this->view->setData('errormsg', $errormsg);        $this->view->setContentFile("views/pages/error.php");        $this->view->renderView();            }        public function contact(){        $this->view->setContentFile("views/pages/contact.php");        $this->view->renderView();    }}     ?>