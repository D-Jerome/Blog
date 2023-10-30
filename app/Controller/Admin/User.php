<?php

namespace App\Controller\Admin;

use App\Model\Manager\{UserManager, BaseManager, RoleManager};
use Framework\Application;
use Framework\BaseController;
use Framework\Request;
use Framework\Session;

class User extends BaseController
{
    function userList()
    {
        $users = (new UserManager(Application::getDatasource()));
        $statementUsers = $users->getAll();
        // foreach ($statementUsers as $statementUser){
        //     $statementUser->username = current($posts->getPostUsername($statementPost->getUserId())) ;
        // }
        $user = [
            'name' => Session::getSessionByKey('authName'),
            'id' => Session::getSessionByKey('auth')
        ];
        $this->view('admin.users.html.twig', ['registredUsers' => $statementUsers, 'authUser' => $user]);
    }

    public function modifyUser($id)
    {
        $users = new UserManager(Application::getDatasource());
        $statementUser = $users->getById($id);

        $roles = new RoleManager(Application::getDatasource());
        $statementRoles = $roles->getAll();

        $user = [
            'name' => Session::getSessionByKey('authName'),
            'id' => Session::getSessionByKey('auth'),
            'role' => Session::getSessionByKey('roleName')
        ];

        $this->view('modify.user.html.twig', ['user' => $statementUser, 'roles' => $statementRoles, 'authUser' => $user]);
    }

    public function modifiedUser($id)
    {
        $users = new UserManager(Application::getDatasource());
        $request = new Request("/blog-project/");

        $roles = new RoleManager(Application::getDatasource());
        $statementRoles = $roles->getAll();

        $return = $users->updateUser($request->getParams());

        $param = $request->getParams();


        $statement = $users->getById($id);
        $user = [
            'name' => Session::getSessionByKey('authName'),
            'id' => Session::getSessionByKey('auth'),
            'role' => Session::getSessionByKey('roleName')
        ];

        $this->view('modify.user.html.twig', ['users' => $statement, 'roles' => $statementRoles, 'authUser' => $user]);
    }

    public function disableUser(int $id)
    {

        (new UserManager(Application::getDatasource()))->disable($id);
        header('Location: /blog-project/admin/users');
    }

    public function enableUser(int $id)
    {

        (new UserManager(Application::getDatasource()))->enable($id);
        header('Location: /blog-project/admin/users');
    }

    public function addUser()
    {
        $roles = new RoleManager(Application::getDatasource());
        $statementRoles = $roles->getAll();
        $user = [
            'name' => Session::getSessionByKey('authName'),
            'id' => Session::getSessionByKey('auth'),
            'role' => Session::getSessionByKey('roleName')
        ];

        $this->view('add.user.html.twig', ['roles' => $statementRoles, 'authUser' => $user]);
    }

    public function addedUser()
    {
        $user = new UserManager(Application::getDatasource());
        $request = new Request("/blog-project/");

        $return = $user->insertNewUser($request->getParams());
        //verif si pas erreur
        $user = [
            'name' => Session::getSessionByKey('authName'),
            'id' => Session::getSessionByKey('auth')
        ];
        $users = new UserManager(Application::getDatasource());
        $statementUser = $users->getById($return);

        $this->view('modify.user.html.twig', ['users' => $statementUser, 'authUser' => $user]);
    }
}
