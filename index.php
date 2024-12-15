<?
header("Content-type:pplication/json;charset=UTF-8");

require __DIR__ . '/src/controllers/PostController.php';
require __DIR__ . '/src/controllers/UserController.php';
require __DIR__ . '/src/controllers/CommentController.php';
require __DIR__ . '/src/controllers/PostImageController.php';
require __DIR__ . '/src/Router.php';
require __DIR__ . '/src/utils/Errorhandler.php';
require __DIR__ . '/src/utils/ServiceContainer.php';
require __DIR__ . "/src/models/PostModel.php";
require __DIR__ . "/src/models/UserModel.php";
require __DIR__ . "/src/models/CommentModel.php";
require __DIR__ . "/src/models/PostImageModel.php";
require __DIR__ . "/src/config/Database.php";
require __DIR__ . "/src/middlewares/AuthMiddleware.php";
require __DIR__ . "/src/middlewares/VerifyAdminMiddleware.php";


//set exception handler
set_exception_handler("Errorhandler::handleException");

// Initialize Service Container
$container = new ServiceContainer();

//connect db
$database = new Database();
$db = $database->connect();

//register modelds
$container->register(PostModel::class, fn() => new PostModel($db));

$container->register(UserModel::class, fn() => new UserModel($db));

$container->register(CommentModel::class, fn() => new CommentModel($db));

$container->register(PostImageModel::class, fn() => new PostImageModel($db));


$container->register(
    Jwt::class,
    fn() =>
    new Jwt(getenv(
        "SECRET_KEY"
    ))
);


$container->register(AuthMiddleware::class, function () use ($container) {
    return new AuthMiddleware($container->get(Jwt::class),$container->get(UserModel::class));
});

$container->register(VerifyAdminMiddleware::class, function () use ($container) {
    return new VerifyAdminMiddleware($container->get(Jwt::class),$container->get(UserModel::class));
});



// Register Controllers
$container->register(PostController::class, fn() => new PostController($container->get(PostModel::class)));
$container->register(UserController::class, fn() => new UserController($container->get(UserModel::class)));
$container->register(CommentController::class, fn() => new CommentController($container->get(CommentModel::class),$container->get(UserModel::class),$container->get(PostModel::class)));
$container->register(PostImageController::class, fn() => new PostImageController($container->get(PostImageModel::class),$container->get(PostModel::class)));

// Initialize Router
$router = new Router($container);

// posts
$router->add('GET', '/api/posts', [PostController::class, 'getAllPosts'], [AuthMiddleware::class]);
$router->add('GET', '/api/posts/{id}', [PostController::class, 'getOnePost'], [AuthMiddleware::class]);
$router->add('POST', '/api/posts', [PostController::class, 'createPost'], [AuthMiddleware::class]);
$router->add('PATCH', '/api/posts/{id}', [PostController::class, 'updatePost'], [VerifyAdminMiddleware::class]);
$router->add('DELETE', 'api/posts/{id}', [PostController::class, 'deletePost'], [VerifyAdminMiddleware::class]);

// users
$router->add('POST', '/api/users/login', [UserController::class, 'login']);
$router->add('POST', '/api/users/register', [UserController::class, 'register']);
$router->add('PATCH', '/api/users/profile', [UserController::class, 'updateUser'],[AuthMiddleware::class]);

$router->add('GET', '/api/users/profile', [UserController::class, 'getUser'],[AuthMiddleware::class]);
$router->add('GET', '/api/users/', [UserController::class, 'getAllUsers'],);


// comments
$router->add('POST', 'api/posts/{id}/comments', [CommentController::class, 'createComment'], [AuthMiddleware::class]);

$router->add('GET', 'api/posts/{id}/comments', [CommentController::class, 'getAllCommentsOfPost'], [AuthMiddleware::class]);

// images
$router->add('POST', 'api/posts/{id}/images', [PostImageController::class, 'createPostImage'], [AuthMiddleware::class]);

$router->add('DELETE', 'api/posts/{postId}/images/{fileId}', [PostImageController::class, 'deletePostImage'], [AuthMiddleware::class]);
$router->add('GET', 'api/posts/{postId}/images/', [PostImageController::class, 'getAllImages'], [AuthMiddleware::class]);


$router->dispatch($_SERVER['REQUEST_URI']);
