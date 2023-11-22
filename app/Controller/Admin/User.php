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
        $userSession = $this->session->getUser();

        $user = $userSession ? $userSession->getAllUserInfo() : null;

        $filter = new FilterBuilder(Application::getFilter(), 'admin.' . substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));

        $httpParams = $this->groupFilterDataUser();
        $sqlParams = [];
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase($httpParams['sortBy']);
        $users = (new UserManager(Application::getDatasource()));

        $count = count($users->getAll());

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if ($httpParams['listSortSelect'] === null) {
            $statementUsers = $users->getAllOrderLimit($sortBySQL, $httpParams['sortDir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementUsers = $users->getAllOrderLimitCat($sortBySQL, $httpParams['sortDir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, $httpParams['listSortSelect']);
        }

        foreach ($statementUsers as $statementUser){
            $statementUser->setRoleName($users->getRoleById($statementUser->getRoleId()));
        }

        $this->view(
            'backoffice/admin.users.html.twig',
            [
                'baseUrl' => Application::getBaseUrl(),
                'registredUsers' => $statementUsers,
                'sort' => $filter->getSort(),
                'dir' => $filter->getDir(),
                'sortDir' => $httpParams['sortDir'],
                'sortBy' => $httpParams['sortBy'],
                'listSort' => $httpParams['listSort'],
                'list' => $filter->getList() ,
                'idListSelect' => $httpParams['listSortSelect'],
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

        $this->view('backoffice/modify.user.html.twig', ['baseUrl' => Application::getBaseUrl(), 'user' => $statementUser, 'roles' => $statementRoles, 'authUser' => $user]);
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
        $request = new Request(Application::getBaseUrl() .'/');

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

        header('Location: '. Application::getBaseUrl() .'/admin');
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
        header('Location: '. Application::getBaseUrl() .'/admin/users#'.$id);
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
        header('Location: '. Application::getBaseUrl() .'/admin/users#'.$id);
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

        $this->view('backoffice/add.user.html.twig', ['baseUrl' => Application::getBaseUrl(), 'roles' => $statementRoles, 'authUser' => $user]);
    }


    /**
     * addedUser: action after validate form => insert new user
     *
     * @return void
     */
    public function addedUser(): void
    {
        $user = new UserManager(Application::getDatasource());
        $request = new Request(Application::$baseUrl .'/');

        $return = $user->insertNewUser($request->getParams());
        //verif si pas erreur
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $users = new UserManager(Application::getDatasource());
        $statementUser = $users->getById($return);

        $this->view('backoffice/modify.user.html.twig', ['baseUrl' => Application::getBaseUrl(), 'users' => $statementUser, 'authUser' => $user]);
    }


}
