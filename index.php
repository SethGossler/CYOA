<?php
require 'sqlSetup.php';
require 'Slim/Slim.php';

function syncBook($properBook, $op)
{
    global $conn;

    //For book
    $title = $properBook->title;
    $pages = $properBook->pages;
    $author = $properBook->author;

    //For Pages
    $choiceDialog ="PH";
    $parentID = 1;
    $pagetitle = "PH";
    $content = "PH";
    $booksID = 1;
    $jsonID = 1;

    if($op == "post") {
        $bookQuery = "INSERT INTO books(authorID, title)
        VALUES (?,?)";
        $stmt = $conn->prepare($bookQuery);
        $stmt->bind_param("is", $author, $title);
        $stmt->execute();
        $booksID = $stmt->insert_id;
    }
    elseif($op == "put"){
        $booksID = $properBook->id;
        $bookQuery = "UPDATE books 
        set title=?
        WHERE ID =?";
        $stmt = $conn->prepare($bookQuery);
        $stmt->bind_param("si", $title, $booksID);
        $stmt->execute();
    }

    $pagesQuery = "INSERT INTO pages(choiceDialog, parentID, title, content, booksID, jsonID)
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY
    UPDATE choiceDialog = ?, title= ?, content= ?";
    $stmt = $conn->prepare($pagesQuery);
    $stmt->bind_param("sissiisss", $choiceDialog, $parentID, $pagetitle, $content, $booksID, $jsonID, $choiceDialog, $pagetitle, $content);

    foreach ($pages as $key => $page) { // $page was a JSON object
        $choiceDialog = $page->choiceDialog;
        $parentID = $page->parentPage;
        $pagetitle = $page->title;
        $content = $page->content;
        $jsonID = $page->id;
        $stmt->execute();
    }

    return array("result"=>"success", "bookID"=>$booksID);
}


function getBookWithID($bookID)
{
    global $conn;

    // get pages
    $getPageQuery = "SELECT *
    FROM  pages 
    WHERE booksID = $bookID
    ";
    $result = $conn->query($getPageQuery);
    $pages = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pages[] = $row;
    }

    // get book
    $getBookQuery = "SELECT *
    FROM books
    WHERE id = $bookID
    ";
    $resultTwo = $conn->query($getBookQuery);
    $book = $resultTwo->fetch_array(MYSQLI_ASSOC);

    $properBook = array('book' => $book, 'id' => $bookID, 'pages' => $pages);

    return $properBook;
}

function loadBookViewer($properBook)
{
    require('viewer/viewer.php');
}

function getRecentStories($rangeX, $rangeY){
    global $conn;

    $getBookQuery = "SELECT *
    FROM  books
    ";

    $result = $conn->query($getBookQuery);

    $books = array();

    while($row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $books[] = $row;
    }


    return $books;
}


function createUser($actualName, $emailAcct, $username, $password)
{
    global $conn;

    $password = sha1($password);

    $query = "INSERT INTO users(actualName, email, username, password)
    VALUES ('$actualName', '$emailAcct', '$username', '$password')";
    $result = $conn->query($query);

    return $result;
}


function getUser($username, $password){
    global $conn;

    $password = sha1($password);

    $query = "SELECT * FROM users 
    WHERE username = '$username' AND password = '$password'";

    $result = $conn->query($query);

    if($result->num_rows == 1)
    {
        return true;
    }
    else
    {
        return false;
    }
}

\Slim\Slim::registerAutoloader();

$user = NULL;
class userCheck extends \Slim\Middleware
{
    public function call()
    {
        // Get reference to application
        $app = $this->app;

        $this->checkLogin($app);

        //run
        $this->next->call();
    }

    public function checkLogin($app)
    {
        global $user;
        $userLoggedIn = $app->getCookie('user');
        if(!$userLoggedIn)
        {
            $user = false;
        }
        else
        {
            $user = true;
        }

    }
}


$app = new \Slim\Slim();

$app->add(new userCheck());

$app->get('/', function(){
    include 'frontpage/frontpage.php';
});

$app->post('/sync/indexer/', function() use($app){
    $bookToAdd = $app->request();
    $bookToAdd = json_decode($bookToAdd->getBody()); 
    $result = syncBook($bookToAdd, "post");
    if($result["result"]=="success") {
        $bookID = $result["bookID"];
        echo "{\"result\":\"success\", \"id\":\"$bookID\"}";
    }
    else {
        echo "error";
        var_dump($result);
    }
});
$app->put('/sync/indexer/', function() use($app){
    $bookToAdd = $app->request();
    $bookToAdd = json_decode($bookToAdd->getBody()); 
    $result = syncBook($bookToAdd, "put");
    if($result["result"]=="success") {
        $bookID = $result["bookID"];
        echo "{\"result\":\"success\", \"id\":\"$bookID\"}";
    }
    else {
        echo "error";
        var_dump($result);
    }
});

$app->get('/readID/:bookID', function($bookID){
    global $conn;
    //echo $bookID;
    $properBook = getBookWithID($bookID);
    loadBookViewer($properBook);
    /*Load the book given the ID*/
});

$app->get('/create/', function() use($app) {
    include 'createbook/create.php';
});
/*

*GET: indexer:name
*This should "load" books. 
*/
$app->get('/sync/indexer/', function() use($app){
    $content = $app->request();
    $content = $content->get();
    var_dump($content);
});

$app->get('/user/', function() use($app){
    $userLoggedIn = $app->getCookie('user');
    if(!$userLoggedIn)
    {
        include 'management/user/login/login.php';     
    }
    else
    {
       $app->redirect('/home');
    }
});

$app->get('/user/new', function() use($app){
    include 'management/user/create/create.php';     

});

$app->post('/user/login/', function() use($app){
    $req = $app->request();
    $username = $req->post('name');
    $password = $req->post("password");
    $userInfo = array('username' => $username);

    $user = getUser($username, $password);
    if($user != false)
    {
        echo "logged in!";
        date_default_timezone_set('America/New_York');
        $app->setCookie('user', $username, '2 days');
        $app->redirect('/home');
    }
    else
    {
        echo "please try logging in again";
    }
});

$app->post('/user/create/', function() use($app){
    $req = $app->request();

    $actualName = $req->post('actual');
    $emailAcct = $req->post('email');
    $newUsername = $req->post('name');
    $newPassword = $req->post('password');

    $didUserCreate = createUser($actualName, $emailAcct, $newUsername, $newPassword);
    if($didUserCreate){
        include 'management/user/login/login.php';     
    }
    else
    {
        echo "user creation fail!";
    }
});

$app->get('/home', function() use($app){
    global $user;
    $publicBooks = getRecentStories(0, 30);
    include 'management/user/home/home.php';
});

$app->run();