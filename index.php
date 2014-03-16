<?php
require 'sqlSetup.php';
require 'Slim/Slim.php';


/*Helper functions*/
function addPages($book, $booksID)//bookID isn't in the book -- the server decides the bookID (how do we keep track of this? Please respond ...)
{
    global $conn;


    $query = "INSERT INTO pages(choiceDialog, parentID, title, content, choices, booksID, pageNumber) VALUES (?,?,?,?,?,?,?)";

    //var_dump($book);
};

/*
*  addBook
*   -takes in the authordID, and the url 'hash' indentifier, and adds it to the DB
*
*/
function addBook($book)
{
    //var_dump($bookAssocArray);
    global $conn;

    $authorID = 1;
    $bookHashKey = $book->title; //title

    //Add the book to the database
    $query = "INSERT INTO books(authorID, titleHash) VALUES (?,?)";

    if (!($stmt = $conn->prepare($query))) 
    {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }

    $stmt->bind_param('is', $authorID, $bookHashKey);
    $stmt->execute();
    $insertID = $stmt->insert_id;
    $stmt->close();

    $bookDetails = array("ID"=>$insertID, "key"=>$bookHashKey);
    return $bookDetails;
};


function syncBook($properBook)
{
    global $conn;

    //For book
    $booksID = $properBook->id;
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



    $bookQuery = "INSERT INTO books(authorID, title)
    VALUES (?,?)";
    $stmt = $conn->prepare($bookQuery);
    $stmt->bind_param("is", $author, $title);
    $stmt->execute();
    $booksID = $stmt->insert_id;

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

    $getBookQuery = "SELECT *
    FROM  pages 
    WHERE booksID = $bookID
    ";

    $result = $conn->query($getBookQuery);
    $pages = array();

    while($row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pages[] = $row;
    }


    $properBook = array('id' => $bookID, 'pages' => $pages);

    return $properBook;
}

function getBookWithHashTitle($titleHash)
{
    //$book = "{\"title\":\"$titleHash\"}";

    $bookID = 1;

    global $conn;

    $getBookQuery = "SELECT *
    FROM  pages 
    WHERE booksID = (SELECT ID FROM books WHERE titleHash = \"$titleHash\") 
    ";

    $result = $conn->query($getBookQuery);
    $pages = array();

    while($row = $result->fetch_array(MYSQLI_ASSOC))
    {
        $pages[] = $row;
    }


    $properBook = array('id' => $bookID, 'pages' => $pages);

    return $properBook;
}

function loadBookViewer($properBook)
{
    require('viewer/viewer.php');
}

function getRecentStories(){
    global $conn;

    $query = "SELECT * 
    FROM books
    LIMIT 0, 30";

    $result = $conn->query($query);

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


function userLoginExists($username, $password){
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

$app = new \Slim\Slim();

$app->get('/', function(){
    include 'frontpage/frontpage.php';
});

/*
*Post: indexer
*-This should save an entire "book".
*-Retruns book ID
*/
$app->post('/sync/indexer/', function() use($app){
    $bookToAdd = $app->request();
    $bookToAdd = json_decode($bookToAdd->getBody()); 
    $result = syncBook($bookToAdd);
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
    $result = syncBook($bookToAdd);
    if($result["result"]=="success") {
        $bookID = $result["bookID"];
        echo "{\"result\":\"success\", \"id\":\"$bookID\"}";
    }
    else {
        echo "error";
        var_dump($result);
    }
});


//If the user is trying to read "nothing", sned them to index
$app->get('/read/', function() use($app) {
    $app->redirect('/');
});

$app->get('/readID/:bookID', function($bookID){
    global $conn;
    //echo $bookID;
    $properBook = getBookWithID($bookID);
    loadBookViewer($properBook);
    /*Load the book given the ID*/
});

$app->get('/read/:bookHash', function($bookHash){
    global $conn;
    $properBook = getBookWithHashTitle($bookHash);    
    loadBookViewer($properBook);
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
       echo "You're logged in!";
       $app->redirect('/home');
    }
});

$app->post('/user/login/', function() use($app){
    $req = $app->request();
    $username = $req->post('name');
    $password = $req->post("password");
    $userInfo = array('username' => $username);

    if(userLoginExists($username, $password))
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
        echo "user created!";
    }
    else
    {
        echo "user creation fail!";
    }
});

$app->get('/home/', function() use($app){
    //$stories = getRecentStories(0, 30);
    //$myStories = getMyStories(0, 30);
    //loadUserHomePage($stories, $myStories);
});

$app->run();