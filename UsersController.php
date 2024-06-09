<?php
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Http\Response;
include_once 'Blowfish.php';

class UsersController extends Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $admin = AdminUsers::find();
        $admin = $this->session->get('auth');
        if ($admin == null) {
            $this->response->redirect("admin/index");
        }
/*
        $users = Users::find();
        $this->view->setVar("users", $users);
*/
        $numberPage = 1;
        if ($this->request->getQuery("page", "int")) {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = array();
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $users = Users::find($parameters);
        if (count($users) == 0) {
            $this->flash->notice("The search did not find any products");
            return $this->dispatcher->forward(
                [
                    "controller" => "users",
                    "action"     => "index",
                ]
            );
        }
        $paginator = new Paginator(array(
            "data"  => $users,
            "limit" => 20,
            "page"  => $numberPage
        ));
        $statuses = Statuses::find();
        $this->view->page = $paginator->getPaginate();
        $this->view->statuses = $statuses;
    }


    public function editAction($id)
    {
        $admin = $this->session->get('auth');
        if ($admin == null) {
            $this->response->redirect("admin/index");
        }
	$blowfish_key = 'ukbihoweb92n2WasA';
	$blowfish = new Crypt_Blowfish($blowfish_key);

        $user = Users::findFirst($id);
        if(!$user) {
            $this->flash->error('該当ユーザーが存在しません');
        }
        $plans = Plans::find();
        $statuses = Statuses::find();
	foreach($user as $key => $v) {
	    if($key == 'RST_PASSWD'){
                $password = $blowfish->decrypt(base64_decode($v));
                break;
            }
	}
        $this->view->user = $user;
        $this->view->password = $password;
        $this->view->plans = $plans;
        $this->view->statuses = $statuses;
    }


    public function registAction() 
    {
        $admin = $this->session->get('auth');
        if ($admin == null) {
            $this->response->redirect("admin/index");
        }

        $plans = Plans::find();
        $statuses = Statuses::find();
        $this->view->plans = $plans;
        $this->view->statuses = $statuses;
    }

    public function registerAction()
    {
        $admin = $this->session->get('auth');
        if ($admin == null) {
            $this->response->redirect("admin/index");
        }

	$blowfish_key = 'ukbihoweb92n2WasA';
	$blowfish = new Crypt_Blowfish($blowfish_key);
	$password = base64_encode($blowfish->encrypt($this->request->getPost('password')));
        // 新規登録
        if($this->request->getPost('id') == null){
            $user = new Users();
            $create_date = date('Y-m-d h:i:s');
            $update_date = $create_date;
            $status = $this->request->getPost('status');
            if(!$status) { $status = 0; }
        // 更新
        } else {
            $user = Users::findFirst($this->request->getPost('id'));
            $create_date = $this->request->getPost('create_date');
            $update_date = date('Y-m-d h:i:s');
            $status = $this->request->getPost('status');
            if(!$status) { $status = 0; }
        }
        $success = $user->save(array(
            'RST_NAME' => $this->request->getPost('name'), 
            'RST_PASSWD' => $password, 
            'RST_SERVICE_PLAN' => $this->request->getPost('plan'), 
            'RST_REGIST_DATE' => $create_date, 
            'RST_UPDATE_DATE' => $update_date,
            'RST_STATUS' => $status,
            )
        );
        if ($success) {
            $this->flash->success('登録・更新成功');
        } else {
            $this->flash->error("データの登録・更新に失敗しました");
            foreach ($user->getMessages() as $message) {
                echo $message->getMessage(), "<br/>";
            }
        }
        $this->response->redirect("users/index");
    }


}