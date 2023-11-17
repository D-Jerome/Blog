<?php

namespace App\Controller\Admin;

use App\Controller\Pagination;
use App\Model\Manager\{UserManager, BaseManager, RoleManager};
use Framework\Application;
use Framework\BaseController;
use Framework\Helpers\FilterBuilder;
use Framework\Helpers\Text;
use Framework\Request;
use Framework\Session;

class User extends BaseController
{
    /**
     * userList: show list of user
     *
     * @return void
     */
    public function userList(): void
    {
        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $currentPage = null;
        $perPage = null;

        if (isset(($this->getRoute()->getParams())['page'])) {
            $currentPage = (int)($this->getRoute()->getParams())['page'];
        }

        if (isset(($this->getRoute()->getParams())['perPage'])) {
            $perPage = (int)($this->getRoute()->getParams())['perPage'];
        }

        $filter = new FilterBuilder(Application::getFilter(), 'admin.' . substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));

        $sortBy = isset(($this->getRoute()->getParams())['sort']) ? ($this->getRoute()->getParams())['sort'] : 'createdAt';
        $sortDir = ($this->getRoute()->getParams())['dir'] ?? 'DESC';

        $sqlParams = [];
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase($sortBy);
        $users = (new UserManager(Application::getDatasource()));

        $count = count($users->getAll());

        $pagination = new Pagination($this->getRoute(), $count, $currentPage, $perPage);
        $pages = $pagination->pagesInformations();

        $statementUsers = $users->getAllOrderLimit($sortBySQL, $sortDir, $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);

        $this->view(
            'backoffice/admin.users.html.twig',
            [
                'registredUsers' => $statementUsers,
                'sort' => $filter->getSort(),
                'dir' => $filter->getDir(),
                'sortDir' => $sortDir,
                'sortBy' => $sortBy,
                'list' => $filter->getList() ,
                'listSelect' => $filter->getListSelect(),
                'listNames' => $filter->getListNames(),
                'pages' => $pages,
                'authUser' => $user
            ]
        );
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
