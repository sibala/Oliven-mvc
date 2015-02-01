<?php
require __DIR__.'/config_with_app.php'; 

$di->set('CommentController', function() use ($di) {
    $controller = new Phpmvc\Comment\CommentController();
    $controller->setDI($di);
    return $controller;
});
$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/database_mysql.php');
    $db->connect();
    return $db;
});

$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('ThreadsController', function() use ($di) {
    $controller = new \Anax\Threads\ThreadsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('TagsController', function() use ($di) {
    $controller = new \Anax\Tags\TagsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('FormController', function () use ($di) {
    $controller = new \Anax\HTMLForm\FormController();
    $controller->setDI($di);
    return $controller;
});

$app->theme->configure(ANAX_APP_PATH . 'config/theme.php');
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar.php');
$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);



$app->router->add('', function() use ($app) {
	$app->theme->setTitle("Hem");	

	$app->dispatcher->forward([
        'controller' => 'threads',
        'action'     => 'latestQuestions',
    ]);
	
	$app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'popularTags',
    ]);
	
	$app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'popular',
    ]);

});
 
$app->router->add('threads', function() use ($app) {
	$app->theme->setTitle("Frågor");
	
	$app->dispatcher->forward([
        'controller' => 'threads',
        'action'     => 'listQuestions',
    ]);
});
$app->router->add('tags', function() use ($app) {
	$app->theme->setTitle("Taggar");
	
	$app->dispatcher->forward([
        'controller' => 'tags',
        'action'     => 'list',
    ]);
});
$app->router->add('users', function() use ($app) {

	$app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'list',
    ]);
});

$app->router->add('ask', function() use ($app) {
	$app->theme->setTitle("Ställ en fråga");
	
	$app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'loginCheck',
    ]);
});
$app->router->add('about', function() use ($app) {
 	$content = $app->fileContent->get('me.md');
	$content = $app->textFilter->doFilter($content, 'shortcode, markdown');
	
	$byline = $app->fileContent->get('byline.md');
    $byline = $app->textFilter->doFilter($byline, 'shortcode, markdown');
 
    $app->views->add('me/me', [
        'content' => $content,
        'byline' => $byline,
    ]);
});
/*
$app->router->add('source', function() use ($app) {
 
    $app->theme->addStylesheet('css/source.css');
    $app->theme->setTitle("Redovisning");
 
    $source = new \Mos\Source\CSource([
        'secure_dir' => '..', 
        'base_dir' => '..', 
        'add_ignore' => ['.htaccess'],
    ]);
 
    $app->views->add('me/source', [
        'content' => $source->View(),
    ]);
 
});
*/
$app->router->handle();
$app->theme->render();