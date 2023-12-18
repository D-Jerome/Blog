<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Pagination;
use App\Model\Manager\RoleManager;
use App\Model\Manager\UserManager;
use Framework\BaseController;
use Framework\Config;
use Framework\Helpers\FilterBuilder;
use Framework\HttpParams;
use Framework\ParamsGetFilter;
use Framework\Security\AuthUser;
use Webmozart\Assert\Assert;

class User extends BaseController
{
    /**
     * userList: show list of user
     */
    public function userList(): void
    {
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());

        $filter = new FilterBuilder('admin.'.substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), '\\') + 1));

        $httpParams = new ParamsGetFilter();
        $sqlParams = [];
        $pages = [];

        $users = UserManager::getUserInstance(Config::getDatasource());
        $count = 1;
        if (null === $httpParams->getList()) {
            if (false !== $users->getAllByParams([])) {
                $count = \count($users->getAllByParams([]));
            }
        } else {
            Assert::notNull($httpParams->getListSelect());
            if (false !== $users->getAllByParams([$httpParams->getList().'_id' => $httpParams->getListSelect()])) {
                $count = \count($users->getAllByParams([$httpParams->getList().'_id' => $httpParams->getListSelect()]));
            }
        }

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        $statementUsers = $users->getAllOrderLimitCat($httpParams, $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);

        foreach ($statementUsers as $statementUser) {
            $statementUser->setRoleName($users->getRoleById($statementUser->getRoleId()));
        }

        $dataView = [
            'baseUrl'        => Config::getBaseUrl(),
            'registredUsers' => $statementUsers,
            'filter'         => $filter,
            'httpFilter'     => $httpParams,
            'pages'          => $pages,
            'authUser'       => $user,
        ];

        if (null !== $httpParams->getUserInfo() && 'modified' === $httpParams->getUserInfo()) {
            $dataView['message'] = '<strong>Modification réussie</strong><br>
                La modification de l\'utilisateur a été éffectué.';
            $dataView['error'] = false;
        }

        $this->view('backoffice/admin.users.html.twig', $dataView);
    }

    /**
     * modifyUser
     */
    public function modifyUser(int $id): void
    {
        $users = UserManager::getUserInstance(Config::getDatasource());
        $statementUser = $users->getById($id);

        $roles = RoleManager::getRoleInstance(Config::getDatasource());
        $statementRoles = $roles->getAllByParams([]);

        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());
        $this->view('backoffice/modify.user.html.twig', ['baseUrl' => Config::getBaseUrl(), 'user' => $statementUser, 'roles' => $statementRoles, 'authUser' => $user]);
    }

    /**
     * modifiedUser: action of user modification
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
            if (\is_string($data)) {
                $dataUser[$key] = htmlentities($data);
            } elseif (\is_int($data)) {
                $dataUser[$key] = $data;
            }
        }

        $users->updateUser($dataUser);

        $users->getAllByParams(['id' => $id]);
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);

        header('Location: '.Config::getBaseUrl().'/admin?user=modified');
    }

    /**
     * disableUser
     *
     * @param int $id Id's user to disable
     */
    public function disableUser(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?'.$filterParams : null;
        UserManager::getUserInstance(Config::getDatasource())->disable($id);
        header('Location: '.Config::getBaseUrl().'/admin/users'.$filterParams.'#'.$id);
    }

    /**
     * enableUser
     *
     * @param int $id Id's user to enable
     */
    public function enableUser(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?'.$filterParams : null;
        UserManager::getUserInstance(Config::getDatasource())->enable($id);
        header('Location: '.Config::getBaseUrl().'/admin/users'.$filterParams.'#'.$id);
    }

    /**
     * addUser: show page to add user
     */
    public function addUser(): void
    {
        $roles = RoleManager::getRoleInstance(Config::getDatasource());
        $statementRoles = $roles->getAllByParams([]);
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);

        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());

        $this->view('backoffice/add.user.html.twig', ['baseUrl' => Config::getBaseUrl(), 'roles' => $statementRoles, 'authUser' => $user]);
    }

    /**
     * addedUser: action after validate form => insert new user
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
        // verif si pas erreur
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);

        $users = UserManager::getUserInstance(Config::getDatasource());
        $statementUser = $users->getAllByParams(['id' => $validation]);

        $this->view('backoffice/modify.user.html.twig', ['baseUrl' => Config::getBaseUrl(), 'users' => $statementUser, 'authUser' => $user]);
    }
}
