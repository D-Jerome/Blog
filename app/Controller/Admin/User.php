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
        $sortBySQL = Text::camelCaseToSnakeCase($httpParams['sort']);
        $users = UserManager::getUserInstance(Application::getDatasource());
        if ($httpParams['list'] === null) {
            $count = count($users->getByParams([]));
        }else{
            $count = count($users->getByParams([$httpParams['list'].'_id' => $httpParams['listSelect']]));
        }

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if ($httpParams['listSelect'] === null) {
            $statementUsers = $users->getAllOrderLimit($sortBySQL, $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementUsers = $users->getAllOrderLimitCat($sortBySQL, $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, $httpParams['listSelect']);
        }

        foreach ($statementUsers as $statementUser){
            $statementUser->setRoleName($users->getRoleById($statementUser->getRoleId()));
        }

        $dataView = [
            'baseUrl' => Application::getBaseUrl(),
            'registredUsers' => $statementUsers,
            'sort' => $filter->getSort(),
            'dir' => $filter->getDir(),
            'sortDir' => $httpParams['dir'],
            'sortBy' => $httpParams['sort'],
            'listSort' => $httpParams['list'],
            'list' => $filter->getList() ,
            'idListSelect' => $httpParams['listSelect'],
            'listSelect' => $filter->getListSelect(),
            'listNames' => $filter->getListNames(),
            'pages' => $pages,
            'authUser' => $user
        ];

        if (isset(($this->getRoute()->getParams())['user'])) {
            if (($this->getRoute()->getParams())['user'] == 'modified') {
                $dataView['message'] = '<strong>Modification réussie</strong><br>
                La modification de l\'utilisateur a été éffectué.';
                $dataView['error'] = false;
            }
        }

        $this->view('backoffice/admin.users.html.twig', $dataView);
    }


    /**
     * modifyUser
     *
     * @param  int $id
     * @return void
     */
    public function modifyUser(int $id): void
    {
        $users = UserManager::getUserInstance(Application::getDatasource());
        $statementUser = $users->getByParams(['id'=>$id]);

        $roles = RoleManager::getRoleInstance(Application::getDatasource());
        $statementRoles = $roles->getByParams([]);

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

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
        $users = UserManager::getUserInstance(Application::getDatasource());
        $request = new Request(Application::getBaseUrl() .'/');

        $roles = RoleManager::getRoleInstance(Application::getDatasource());
        $statementRoles = $roles->getByParams([]);
        $users->updateUser($request->getParams());

        $users->getByParams(['id'=>$id]);
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        header('Location: '. Application::getBaseUrl() .'/admin?user=modified');
    }


    /**
     * disableUser
     *
     * @param  int $id Id's user to disable
     * @return void
     */
    public function disableUser(int $id): void
    {
        $filterParams = ($this->getRoute()->getOldParams());
        (UserManager::getUserInstance(Application::getDatasource()))->disable($id);
        header('Location: '. Application::getBaseUrl() .'/admin/users'.$filterParams.'#'.$id);
    }


    /**
     * enableUser
     *
     * @param  int $id Id's user to enable
     * @return void
     */
    public function enableUser(int $id): void
    {
        $filterParams = ($this->getRoute()->getOldParams());
        (UserManager::getUserInstance(Application::getDatasource()))->enable($id);
        header('Location: '. Application::getBaseUrl() .'/admin/users'.$filterParams.'#'.$id);
        exit;
    }


    /**
     * addUser: show page to add user
     *
     * @return void
     */
    public function addUser()
    {
        $roles = RoleManager::getRoleInstance(Application::getDatasource());
        $statementRoles = $roles->getByParams([]);
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $this->view('backoffice/add.user.html.twig', ['baseUrl' => Application::getBaseUrl(), 'roles' => $statementRoles, 'authUser' => $user]);
    }


    /**
     * addedUser: action after validate form => insert new user
     *
     * @return void
     */
    public function addedUser(): void
    {
        $user = UserManager::getUserInstance(Application::getDatasource());
        $request = new Request(Application::getBaseUrl() .'/');

        $return = $user->insertNewUser($request->getParams());
        //verif si pas erreur
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $users = UserManager::getUserInstance(Application::getDatasource());
        $statementUser = $users->getByParams(['id'=>$return]);

        $this->view('backoffice/modify.user.html.twig', ['baseUrl' => Application::getBaseUrl(), 'users' => $statementUser, 'authUser' => $user]);
    }


}
