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

                $pattern = $this->decodePattern('~^' . $route->getPath() . '$~');
                
                if (preg_match($pattern, $request->getUri(), $matches, PREG_UNMATCHED_AS_NULL)) {

                    $matches = array_merge([
                        'slug' => '',
                        'postId' => '',
                        'commentId' => '',
                        'username' => '',
                        'userId' => ''
                    ], $matches);

                    if ($this->validateRoute($matches)) {
                     
                        return $route;

                    }
                } elseif ($route->getPath() === $request->getUri()) {
                    return $route;
                }
            }
        }
       
        return null;
    }

    private function decodePattern(string $pattern): string
    {
        $routePattern = [
            '~{:slug}~',
            '~{:postId}~',
            '~{:commentId}~',
            '~{:username}~',
            '~{:userId}~'
        ];
        $routeReplacement = [
            '(?<slug>([\D]{1,}-){1,})',
            '(?<postId>[\d]{1,})',
            '(?<commentId>[\d]{1,}-)',
            '(?<username>[\w]{1,}-)',
            '(?<userId>[\d]{1,})'
        ];

        return preg_replace($routePattern, $routeReplacement, $pattern);
    }

    private function validateRoute(array $matches): bool
    {
        $sanitizeMatches = [];
        $valid = false;
        //verifier en base si slug correspond au postId
        //verifier en base si CommentId correspond au PostId
        foreach ($matches as $k => $match) {
            $sanitizeMatches[$k] = Text::sanitizeMatches($match);
        }

        if ('' !== ($sanitizeMatches['slug']) && '' !== ($sanitizeMatches['postId'])) {
            $posts = new PostManager(Application::getDatasource());
            if ($posts->verifyCoupleIdSlug($sanitizeMatches['postId'], $sanitizeMatches['slug']) === 1) {
                $valid = true;
            }
        }
        if ('' !== ($sanitizeMatches['commentId']) && '' !== ($sanitizeMatches['postId'])) {
            $comments = new CommentManager(Application::getDatasource());
            if ($comments->verifyCoupleCommentIdPostId($sanitizeMatches['postId'], $sanitizeMatches['commentId']) === 1) {
                $valid = true;
            }
        }
        if ('' !== ($sanitizeMatches['username']) && '' !== ($sanitizeMatches['userId'])) {
            $users = new UserManager(Application::getDatasource());
            if ($users->verifyCoupleUsernameUserId($sanitizeMatches['userId'], $sanitizeMatches['username']) === 1) {

                $valid = true;
            }
        }
        if ('' === ($sanitizeMatches['postId']) && '' === ($sanitizeMatches['commentId']) && '' === ($sanitizeMatches['userId'])) {
            $valid = true;
        }

        return $valid;
    }
}
