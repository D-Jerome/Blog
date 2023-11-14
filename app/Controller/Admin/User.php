<?php

namespace App\Controller\Admin;

use App\Model\Manager\{UserManager, BaseManager, RoleManager};
use Framework\Application;
use Framework\BaseController;
use Framework\Request;
use Framework\Session;

class User extends BaseController
{

    /**
     * userList: show list of user
     *
     * @return void
     */
    function userList(): void
    {
        $users = (new UserManager(Application::getDatasource()));
        $statementUsers = $users->getAll();
        // foreach ($statementUsers as $statementUser){
        //     $statementUser->username = current($posts->getPostUsername($statementPost->getUserId())) ;
        // }
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];

        $this->view('backoffice/admin.users.html.twig', ['registredUsers' => $statementUsers, 'authUser' => $user]);
    }


    /**
     * modifyUser
     *
     * @param  int $id
     * @return void
     */
    public function modifyUser(int $id): void
    {
        $users = new UserManager(Application::getDatasource());
        $statementUser = $users->getById($id);

        $roles = new RoleManager(Application::getDatasource());
        $statementRoles = $roles->getAll();

        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];

        $this->view('backoffice/modify.user.html.twig', ['user' => $statementUser, 'roles' => $statementRoles, 'authUser' => $user]);
    }


    /**
     * modifiedUser: action of user modification
     *
     * @param  int $id
     * @return void
     */
    public function modifiedUser(int $id): void
    {
        $users = new UserManager(Application::getDatasource());
        $request = new Request("/blog-project/");

        $roles = new RoleManager(Application::getDatasource());
        $statementRoles = $roles->getAll();
        $users->updateUser($request->getParams());

        $users->getById($id);
        $user = $this->session->getUser();
            $user = [
                        'name' => $user->getUsername(),
                        'id' => $user->getId(),
                        'roleName' => $user->getRoleName()
                    ];

            header('Location: /blog-project/admin');
    }


    /**
     * disableUser
     *
     * @param  int $id Id's user to disable
     * @return void
     */
    public function disableUser(int $id): void
    {

        (new UserManager(Application::getDatasource()))->disable($id);
        header('Location: /blog-project/admin/users');
    }


    /**
     * enableUser
     *
     * @param  int $id Id's user to enable
     * @return void
     */
    public function enableUser(int $id): void
    {
        (new UserManager(Application::getDatasource()))->enable($id);
        header('Location: /blog-project/admin/users');
    }


    /**
     * addUser: show page to add user
     *
     * @return void
     */
    public function addUser()
    {
        $roles = new RoleManager(Application::getDatasource());
        $statementRoles = $roles->getAll();
        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $this->view('backoffice/add.user.html.twig', ['roles' => $statementRoles, 'authUser' => $user]);
    }


    /**
     * addedUser: action after validate form => insert new user
     *
     * @return void
     */
    public function addedUser(): void
    {
        $user = new UserManager(Application::getDatasource());
        $request = new Request("/blog-project/");

        $return = $user->insertNewUser($request->getParams());
        //verif si pas erreur
        $user = $this->session->getUser();
        $user = [
                    'name' => $user->getUsername(),
                    'id' => $user->getId(),
                    'roleName' => $user->getRoleName()
                ];
        $users = new UserManager(Application::getDatasource());
        $statementUser = $users->getById($return);

        $this->view('backoffice/modify.user.html.twig', ['users' => $statementUser, 'authUser' => $user]);
    }


}
