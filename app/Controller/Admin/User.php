<?php

namespace App\Controller\Admin;

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
        $filter = new FilterBuilder(Application::getFilter(), substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $sortList = $filter->getSort();
        $dirList = $filter->getDir();
        $list = $filter->getList();
        $listNames = $filter->getListNames();


        $sortBy = isset(($this->getRoute()->getParams())['sort']) ? ($this->getRoute()->getParams())['sort'] : 'createdAt';
        $sortDir = ($this->getRoute()->getParams())['dir'] ?? 'DESC';
        $perPage = ($this->getRoute()->getParams())['perPage'] ?? 8;

        $currentPage = ($this->getRoute()->getParams())['page'] ?? 1;
        $currentPage = (int)$currentPage;
        $sqlParams=[];
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase($sortBy);
        $users = (new UserManager(Application::getDatasource()));
        $statementUsers = $users->getAllOrderLimit($sortBySQL, $sortDir, $perPage, $currentPage , $sqlParams);

       
            $count = count($users->getAll());
    

        $user = $this->session->getUser();
        $user = [
            'name' => $user->getUsername(),
            'id' => $user->getId(),
            'roleName' => $user->getRoleName()
        ];

        if ((int)(ceil(($count / $perPage))) === 1) {
            $pages['nextActive'] = false;
            $pages['previousActive'] = false;
        } elseif ($currentPage >= (ceil(($count / $perPage)))) {
            $pages['previousActive'] = true;
            $pages['nextActive'] = false;
        } elseif ($currentPage === 1) {
            $pages['previousActive'] = false;
            $pages['nextActive'] = true;
        } else {
            $pages['nextActive'] = true;
            $pages['previousActive'] = true;
        }//end if

        //pagination
        $temp = ($this->getRoute()->getParams());
        unset($temp['page']);
        $this->getRoute()->setParams($temp);
        $query = http_build_query($this->getRoute()->getParams());
        if (!empty($query)) {
            $query = "&$query";
        }
        $pages['previousUri'] = Application::getBaseUrl(). $this->getRoute()->getPath() . '?page=' . ($currentPage - 1) . $query;
        $pages['nextUri'] = Application::getBaseUrl(). $this->getRoute()->getPath() . '?page=' . ($currentPage + 1) . $query;


        $this->view('backoffice/admin.users.html.twig', [
                'registredUsers' => $statementUsers,
                'sort' => $sortList,
                'dir' => $dirList,
                'sortDir' => $sortDir,
                'sortBy' => $sortBy,
                'list' => $list ,
                'listNames' => $listNames,
                'pages' => $pages,
                'authUser' => $user
            ]);
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
