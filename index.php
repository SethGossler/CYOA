<?php
require 'sqlSetup.php';
require 'Slim/Slim.php';


/*Helper functions*/
function addPages($book, $booksID)//bookID isn't in the book -- the server decides the bookID (how do we keep track of this? Please respond ...)
{
    global $conn;

    /*Some pseudo code
    *  Insert a new page, unless...   
    *    if the books ID and page# are the same...
    *       then update the row with the data
    *           Data would be the choiceDialog, title, content, and choices.
    */    

    $query = "INSERT INTO pages(choiceDialog, parentID, title, content, choices, booksID, pageNumber) VALUES (?,?,?,?,?,?,?)";

    if (!($stmt = $conn->prepare($query))) 
    {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }
    
    $choiceDialog ='a';
    $parentID = 1;
    $title = 'a';
    $content = 'a';
    $choices = 'a';
    $pageNumber = 1;

    $stmt->bind_param('sisssii', $choiceDialog, $parentID, $title, $content, $choices, $booksID, $pageNumber);

    foreach ($book as $page) 
    {
        $choiceDialog = $page->choiceDialog;
        $parentID = $page->parentID;
        $title = $page->title;
        $content = $page->content;
        $choices = json_encode($page->choices);
        //var_dump($page->choices); 
        //$booksID = $bookID;
        $pageNumber = $page->id;
        $stmt->execute();
    }
    $stmt->close();

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
    $bookHashKey = 'a1b2'; //title

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


function updateBook($properBook)
{

    /*This is updating the pages, but we need to insert pages that don't exist yet in the database*/
    global $conn;

    $thisBookID = $properBook->id;
    $pages = $properBook->pages;

    $query = "INSERT INTO pages(choiceDialog, parentID, title, content, choices, booksID, pageNumber)
    VALUES (?,?,?,?,?,?,?)
    ON DUPLICATE KEY
    UPDATE choiceDialog = ?, title= ?, content= ?, choices= ?";
    //$query = "UPDATE pages SET choiceDialog = ?, title= ?, content= ?, choices= ? WHERE booksID = $thisBookID AND pageNumber = ?";

    if (!($stmt = $conn->prepare($query))) 
    {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }
    
    $choiceDialog ='a';
    $title = 'a';
    $content = 'a';
    $choices = 'a';
    $pageNumber = 1;
    $parentID = 1;
    $booksID = $thisBookID;

    $stmt->bind_param('sisssiissss', $choiceDialog, $parentID, $title, $content, $choices, $booksID, $pageNumber, $choiceDialog, $title, $content, $choices);
    //$stmt->bind_param('ssssi', $choiceDialog, $title, $content, $choices, $pageNumber);

    foreach ($pages as $page) 
    {

        $choiceDialog = $page->choiceDialog;
        $parentID = $page->parentID;
        $title = $page->title;
        $content = $page->content;
        $choices = json_encode($page->choices);
        $pageNumber = $page->id;
        $stmt->execute();
    }
    $stmt->close();
    return true;
    /*
    * UPDATE where books.id = $properBook->id; -- this one not quite yet.
    * UPDATE where pages.bookID = $properBook->id && pages.ID = currentPage.ID
    */
    
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
    //echo $bookHash;
    $properBook = getBookWithHashTitle($bookHash);
    //$properBook = getBookWithID($bookID);
    
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

/*
*Post: indexer
*-This should save an entire "book".
*-Books don't make their own ID's, so in this stage,
*books should be saved, and then a reply should be given
*with the books new ID.
*-It's ID could be the 
*/
$app->post('/sync/indexer/', function() use($app){
    $bookToAdd = $app->request();
    $bookToAdd = json_decode($bookToAdd->getBody());
    //var_dump($bookToAdd);
    $bookPages = $bookToAdd->pages;
    $bookDetails = addBook($bookPages);

    $bookID = $bookDetails['ID'];
    addPages($bookPages,$bookID); 

    echo "{\"id\": $bookID, \"action\":\"save\"}";//whatever I echo back here is picked up as JSOn on syncToServer's success callback
});

$app->put('/sync/indexer/', function() use($app){
    $bookToUpdate = $app->request();
    $bookToUpdate = json_decode($bookToUpdate->getBody());
    $thisID = $bookToUpdate->id;


    if(updateBook($bookToUpdate))
    {
       echo "{\"id\": $thisID,\"action\":\"update\"}";
    }
    else
    {
       echo "{\"id\": \"err\",\"action\":\"update error\"}";
    }
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