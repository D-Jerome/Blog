<?php

namespace Framework;

use Framework\Route;
use PhpParser\Node\Expr\Cast\Bool_;
use App\Model\Manager\{PostManager, CommentManager, UserManager};
use Framework\Helpers\Text;

class Router
{

    protected array $routes;


    public function __construct()
    {
        $routes = json_decode(file_get_contents(__DIR__ . '/../config/routes.json'), true);
        foreach ($routes as $route) {
            $this->routes[] = new Route($route['path'], $route['method'], $route['controller'], $route['action'], $route['authorize']);
        }
    }

    public function findRoute(Request $request): ?Route 
    {

        foreach ($this->routes as $route) {
            if ($route->getMethod() === $request->getMethod()) {
                preg_match_all('/\{(\w*)\}/', $route->getPath(), $paramNames);
                $routeMatcher = preg_replace('/\{(\w*)\}/', '(\S*)', $route->getPath());         
                $routeMatcher = str_replace('/', '\/', $routeMatcher);
                if (preg_match_all("~^$routeMatcher$~", $request->getUri(), $params, PREG_UNMATCHED_AS_NULL)) {
                    $paramsValues = [];
                    foreach ($paramNames[1] as $key => $names) {
                        $paramsValues[$names] = $params[$key + 1][0];
                    }
                   // if (preg_match($pattern, $request->getUri(), $matches, PREG_UNMATCHED_AS_NULL)) {

                        $paramsValues = array_merge([
                            'slug' => '',
                            'postId' => '',
                            'commentId' => '',
                            'username' => '',
                            'userId'  => ''
                        ], $paramsValues);

                        if ($this->validateRoute($paramsValues)) {
                            $route->setParams($request->getParams());
                            return $route;

                        }
                } elseif ($route->getPath() === $request->getUri()) {
                    $route->setParams($request->getParams());
                    return $route;
                }
                
            }
        }
        return null;
    }

    private function validateRoute(array $matches): bool
    {

        $valid = false;
       
        if ('' !== ($matches['slug']) && '' !== ($matches['postId']) && is_numeric($matches['postId'])) {
            $posts = new PostManager(Application::getDatasource());
            if ($posts->verifyCoupleIdSlug($matches['postId'], $matches['slug']) === 1) {
                $valid = true;
            }
        }
        
        if ('' !== ($matches['commentId']) && '' !== ($matches['postId']) && is_numeric($matches['commentId'])) {
            $comments = new CommentManager(Application::getDatasource());
            if ($comments->verifyCoupleCommentIdPostId($matches['postId'], $matches['commentId']) === 1) {
                $valid = true;
            }
        }
        if ('' !== ($matches['username']) && '' !== ($matches['userId'])  && is_numeric($matches['userId'])) {
            $users = new UserManager(Application::getDatasource());
            if ($users->verifyCoupleUsernameUserId($matches['userId'], $matches['username']) === 1) {

                $valid = true;
            }
        }
        if ('' === ($matches['postId']) && '' === ($matches['commentId']) && '' === ($matches['userId'])) {
            $valid = true;
        }

        return $valid;
    }
}
