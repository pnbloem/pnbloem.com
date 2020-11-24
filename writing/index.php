<?php
require 'setup.php';
require 'config.php';
require 'datamodel/datamodel.php';
require 'dbproxy/dbproxy.php';
require 'dbproxy/medoo.php';

require 'Slim/Slim.php';
use Slim\Slim;
Slim::registerAutoloader();

require_once 'Slim/Extras/Views/Twig.php';
Twig_Autoloader::register();

$app = new \Slim\Slim();
$app->view = new \Slim\Views\Twig();
$app->view->setTemplatesDirectory("templates");

$app->notFound(function() use ($app){
	$app->render("404.html", array());
	http_response_code(404);
});
$app->get('/prevnext/:id', function($id) use ($config, $app){
	$proxy = new DBProxy();
    $auth = authorize($request->get('auth'));
	$data = $proxy->getPrevNext($config, $id, $auth);
	echo json_encode($data);
});
$app->get('/', function() use ($config, $app) {
    $request = $app->request();
    $loc = $request->get('loc');
    $auth = authorize($request->get('auth'));
	$proxy = new DBProxy();
	$data = $proxy->getPoemList($config, null, $auth);
	$poems = $data->data;
	if ($data->status){
		$app->render('home.html', array('poems' => $poems, 'loc' => $loc));
	} else {
		$app->notFound();
	}
});
$app->get('/poem/:id', function($id) use ($config, $app){
    $request = $app->request();
    $loc = $request->get('loc');
    $auth = authorize($request->get('auth'));
	$proxy = new DBProxy();
	$data = $proxy->getPoem($config, $id, $auth);
	if($data->status == true){
		if(!$auth && $data->data['poem']->public == 0){
			$app->redirect('/writing?loc=re-'.$id);
	    }
		$poem = $data->data['poem'];
		$prev = $data->data['prev'];
		$next = $data->data['next'];
		switch($poem->category){
			case 1:
				break;
			case 2:
				$app->render('freeverse.html', array(
					"poem" => $poem,
					"prev" => $prev,
					"next" => $next,
                    "loc"  => $loc
				));
				break;
			case 3:
				$app->render('haiku.html', array(
					"poem" => $poem,
					"prev" => $prev,
					"next" => $next,
                    "loc"  => $loc
				));
				break;
			case 4:
				$app->render('freeverse.html', array(
					"poem" => $poem,
					"prev" => $prev,
					"next" => $next,
                    "loc"  => $loc
				));
				break;
			case 5:
				$app->render('freeverse.html', array(
					"poem" => $poem,
					"prev" => $prev,
					"next" => $next,
					"loc"  => $loc
				));
		}
	} else {
		$app->notFound();
	}
});
$app->get('/post/:id', function($id) use ($config, $app){
    $request = $app->request();
    $loc = $request->get('loc');
    $auth = authorize($request->get('auth'));
	$proxy = new DBProxy();
	$data = $proxy->getPost($config, $id, $auth);
	if($data->status == true){
		$post = $data->data['poem'];
		
		$app->render('post.html', array(
			"post" => $post,
			"loc" => $loc,
			"prev" => $data->data['prev'],
			"next" => $data->data['next']
		));
				
	} else {
		$app->notFound();
	}
});
$app->get('/publish', function() use ($config, $app){
	$proxy = new DBProxy();
	$data = $proxy->getCategories($config);
	$date = date('Y-m-d');
	$app->render('publish.html', array(
		'categories' => $data->data,
		'date' => $date
	));
});
$app->post('/publish', function() use ($config, $app){
	$proxy = new DBProxy();
	$data = $app->request()->post();
	$lines = array();
	$count = 1;
	$lineArray = preg_split("/\r\n|\n|\r/", $data["poem"]);
	foreach ($lineArray as $l) {
		array_push($lines, new Line(array(
			"text" => $l,
			"line" => $count
		)));
		$count++;
	}
	$newPoem = new Poem($data);
	$newPoem->lines = $lines;
	$return = $proxy->postPoem($config, $newPoem);
	if($return->status == true){$app->redirect('/writing');} else {$app->notFound();}
});
$app->get('/api/poem/:id', function($id) use ($config, $app) {
	$proxy = new DBProxy();
	$return = $proxy->getPoem($config, $id);
	if($return->status == true){echo json_encode($return);} else {$app->notFound();}
});
$app->post('/api/poem', function() use ($config, $app){
	$request = $app->request();
	$body = json_decode($request->getBody(), true);
	$proxy = new DBProxy();
	$poem = new Poem($body);
	$return = $proxy->postPoem($config, $poem);
	if($return->status == true){echo json_encode($return);} else {$app->notFound();}
});
$app->get('/api/poems(/:category)', function($category = null) use ($config, $app){
	$proxy = new DBProxy();
	$data = $proxy->getPoemList($config, $category);
	if ($data->status){
		$app->render('home.html', array('poems' => $poems));
	} else {
		$app->notFound();
	}
});
$app->run();

function authorize($string) {
	if($string == "pnbloem"){
		return true;
	} else {
		return false;
	}
}
?>