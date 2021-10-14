<?php
session_start();
if(!isset($_SESSION["email"])){
header("Location: login.php");
exit(); }
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('./db.php');
$postTitle = $postBody = "";
$postTitleErr = $postBodyErr = "";
$trn_date = date("Y-m-d H:i:s");

// var_dump($_SERVER["REQUEST_METHOD"]);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty(trim($_POST["postTitle"]))) {
    $postTitleErr = "Please enter post title.";
  } else {
    $postTitle = $con->real_escape_string(trim($_POST["postTitle"]));
  }
  if (empty(trim($_POST["postBody"]))) {
    $postBodyErr = "Please enter post body.";
  } else {
    $postTitle = $con->real_escape_string(trim($_POST["postBody"]));
  }
  $check_error = empty($postTitleErr) && empty($postBodyErr);
  
  if ($check_error) {
      $query = "INSERT INTO posts (user, title, body, postCreation) VALUES (?, ?, ?, ?)";
      if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ssss", $_SESSION["email"], $postTitle, $postBody, $trn_date);
        
        if ($stmt->execute()) {
          
        } else {
            var_dump($con->error);
          echo "<script>alert('Oops! Something went wrong. Please try again later.')</script>";
        }
        $stmt->close(); 
      }
  } else {
      if (!empty($postTitleErr)) {
        echo "<script>alert('".$postTitleErr."')</script>";
      }
      if (!empty($postBodyErr)) {
        echo "<script>alert('".$postBodyErr."')</script>";
      }
  }
}

$posts = [];
$query = "SELECT * FROM posts";
if ($stmt = $con->prepare($query)) {
  if ($stmt->execute()) {
    $stmt->store_result();
    $result = $stmt->get_result();
    if ($stmt->num_rows > 0) {
      $stmt->bind_result($postId, $postUser, $postTitle, $postBody, $postCreation);
      while ($stmt->fetch()) {
        $new_post = array (
          'id' => $postId,
          'user' => $postUser,
          'title' => $postTitle,
          'body' => $postBody,
          'postCreation' => $postCreation
        );
        array_push($posts, $new_post);
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>360 Travels</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" >
	<link rel="stylesheet" href="./css/style.css">
</head>
<body>

<!-- Image and text -->
<nav class="navbar navbar-expand-lg navbar-light bg-primary">
  <a class="navbar-brand" href="#">
    <img src="./icon.png" width="36" height="36" class="d-inline-block align-center" alt="">
    360 Travels
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item active">
        <a class="nav-link" href="./index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./myposts.php">My Posts</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="./logout.php">Logout</a>
      </li>
      
    </ul>
  </div>
</nav>

  <div class="container">
      <div class="row mt-5">
        <div class="col-12">
          Hello <span class="badge badge-pill badge-dark"><?= $_SESSION['email'] ?></span>
        </div>
      </div>
      <div class="row">
          <div class="col-sm-2 col-12 mt-2">  
            <button type="button" data-toggle="modal" data-target="#newPostModal" class="btn btn-primary">New post</button>
          </div>
          <div class="col-sm-10 col-12">
          <?php
          if (!empty($posts)) {
            foreach($posts as $key=>$post) {
              ?>
              <div class="col-12">
              <div class="card my-4" style="box-shadow: 0px 0px 4px rgba(0,0,0,.1)">
                <div class="card-body">
                  <h5 class="card-title text-center"><?= $post['title'] ?></h5>
                  <p class="card-text"><?= $post['body'] ?></p>
                </div>
                <div class="card-footer text-muted d-flex justify-content-between">
                  <div><?= $post['user'] ?></div>
                  <div><?= $post['postCreation'] ?></div>
                </div>
              </div>
              </div>
              <?php
            }
          }
        ?>
          </div>
      </div>
  </div>
<!-- Button trigger modal -->

<!-- Modal -->
<div class="modal fade" id="newPostModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Create a new post</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post">
        <div class="form-group">
          <label for="postTitle">Title</label>
          <input name="postTitle" type="text" class="form-control" id="postTitle" placeHolder="Enter a title for post">
        </div>
        <div class="form-group">
          <label for="postBody">Post body</label>
          <textarea name="postBody" class="form-control" id="postBody" rows="10" placeHolder="Add a post body"></textarea>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create post</button>
      </div>
      </form>
    </div>
  </div>
</div>
  <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>