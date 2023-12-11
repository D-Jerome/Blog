<?php

namespace App\Controller\Admin;

use App\Controller\Pagination;
use App\Model\Manager\{UserManager, BaseManager, RoleManager};
use Framework\{Application,Config};
use Framework\BaseController;
use Framework\Helpers\FilterBuilder;
use Framework\Helpers\Text;
use Framework\{Request, HttpParams};
use Framework\Session;
use Webmozart\Assert\Assert;

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
        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;
        $this->session->generateToken();
        $user['token'] = $this->session->getToken();

        $filter = new FilterBuilder('admin.' . substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));

        $httpParams = $this->groupFilterDataUser();
        $sqlParams = [];
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase((string)$httpParams['sort']);
        $users = UserManager::getUserInstance(Config::getDatasource());
        if ($httpParams['list'] === null) {
            $count = count($users->getAllByParams([]));
        } else {
            $count = count($users->getAllByParams([$httpParams['list'] . '_id' => $httpParams['listSelect']]));
        }

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if ($httpParams['listSelect'] === null) {
            $statementUsers = $users->getAllOrderLimit($sortBySQL, (string)$httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementUsers = $users->getAllOrderLimitCat($sortBySQL, (string)$httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, (int)$httpParams['listSelect']);
        }

        foreach ($statementUsers as $statementUser) {
            $statementUser->setRoleName($users->getRoleById($statementUser->getRoleId()));
        }

        $dataView = [
            'baseUrl' => Config::getBaseUrl(),
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

        if (isset($httpParams['user']) && $httpParams['user'] == 'modified') {
            $dataView['message'] = '<strong>Modification réussie</strong><br>
                La modification de l\'utilisateur a été éffectué.';
            $dataView['error'] = false;
        }

        $this->view('backoffice/admin.users.html.twig', $dataView);
    }


    /**
     * modifyUser
     *
     * @return void
     */
    public function modifyUser(int $id): void
    {
        $users = UserManager::getUserInstance(Config::getDatasource());
        $statementUser = $users->getById($id);

        $roles = RoleManager::getRoleInstance(Config::getDatasource());
        $statementRoles = $roles->getAllByParams([]);

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token'] = $this->session->getToken();
        $this->view('backoffice/modify.user.html.twig', ['baseUrl' => Config::getBaseUrl(), 'user' => $statementUser, 'roles' => $statementRoles, 'authUser' => $user]);
    }


    /**
     * modifiedUser: action of user modification
     *
     * @return void
     */
    public function modifiedUser(int $id): void
    {
        $users = UserManager::getUserInstance(Config::getDatasource());
        $userData = (new HttpParams())->getParamsPost();
        $dataUser = [];
        Assert::isArray($userData);
        foreach ($userData as $key => $data) {
            Assert::notEmpty($data);
            Assert::string($key);
            Assert::notNull($data);
            if (is_string($data)) {
                $dataUser[$key] = htmlentities($data);
            } elseif (is_integer($data)) {
                $dataUser[$key] = $data;
            }
        }

        $users->updateUser($dataUser);

        $users->getAllByParams(['id' => $id]);
        $userSession = $this->session->getUser();

        header('Location: ' . Config::getBaseUrl() . '/admin?user=modified');
    }


    /**
     * disableUser
     *
     * @param  int $id Id's user to disable
     * @return void
     */
    public function disableUser(int $id): void
    {
        $filterParams = ((new HttpParams())->getParamsReferer());
        $filterParams = isset($filterParams) ? '?' . $filterParams : null;
        (UserManager::getUserInstance(Config::getDatasource()))->disable($id);
        header('Location: ' . Config::getBaseUrl() . '/admin/users' . $filterParams . '#' . $id);
    }


    /**
     * enableUser
     *
     * @param  int $id Id's user to enable
     * @return void
     */
    public function enableUser(int $id): void
    {
        $filterParams = ((new HttpParams())->getParamsReferer());
        $filterParams = isset($filterParams) ? '?' . $filterParams : null;
        (UserManager::getUserInstance(Config::getDatasource()))->enable($id);
        header('Location: ' . Config::getBaseUrl() . '/admin/users' . $filterParams . '#' . $id);
    }


    /**
     * addUser: show page to add user
     *
     * @return void
     */
    public function addUser()
    {
        $roles = RoleManager::getRoleInstance(Config::getDatasource());
        $statementRoles = $roles->getAllByParams([]);
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $this->view('backoffice/add.user.html.twig', ['baseUrl' => Config::getBaseUrl(), 'roles' => $statementRoles, 'authUser' => $user]);
    }


    /**
     * addedUser: action after validate form => insert new user
     *
     * @return void
     */
    public function addedUser(): void
    {
        $user = UserManager::getUserInstance(Config::getDatasource());

        $userData = (new HttpParams())->getParamsPost();
        $dataUser = [];
        Assert::isArray($userData);
        foreach ($userData as $key => $data) {
            Assert::notEmpty($data);
            Assert::string($key);
            Assert::notNull($data);
            Assert::string($data);
            $dataUser[$key] = htmlentities($data);
        }


        $validation = $user->insertNewUser($dataUser);
        //verif si pas erreur
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $users = UserManager::getUserInstance(Config::getDatasource());
        $statementUser = $users->getAllByParams(['id' => $validation]);

        $this->view('backoffice/modify.user.html.twig', ['baseUrl' => Config::getBaseUrl(), 'users' => $statementUser, 'authUser' => $user]);
    }
}
