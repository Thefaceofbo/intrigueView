<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Adam Carnaffan">
  <link rel="icon" href="https://getpocket.com/a/i/pocketlogo.svg">

  <title>Intrigue View 1.0</title>

  <!-- Bootstrap core CSS -->
  <link href="bootstrap.min.css" rel="stylesheet">
  <link href="bootstrap-grid.css" rel="stylesheet">
  <!-- Custom styles -->
  <link href="custom-styles.css" rel="stylesheet">
</head>
<body class="hide-overflow">
  <!-- Fixed navbar -->
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
  <a class="navbar-brand" href="#">IntrigueView</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarCollapse">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="getPocket.php">New Pocket Fetch</a></a>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#">Disabled</a>
      </li>
    </ul>
    <ul class="navbar-nav">
      <button class="btn btn-outline-success-blue my-2 my-sm-0" onclick="openLogin()">Login</button>
    </ul>
  </div>
</nav>

<!-- Login Dialog trigged by openLogin() -> Registering is not supported, closed membership for management-->
<div id="loginDialog" class="login-box">
  <form method="post" action="adminConsole.php">
    <input type="text" name="username" placeholder="Username" class="login-form"/>
    <input type="password" name="password" placeholder="Password" />
    <input type="submit" name="submit" value="Login" class="btn btn-otuline-success-blue my-2 my-sm-0"/>
  </form>
</div>

<!-- Left Side Sort Bar -->
<div class="col-4 col-md-3 sidebar-offcanvas fix-sidebar" id="sidebar">
  <div class="list-group round-bars">
    <h4>Sort By (WIP)</h4>
    <a href="#" id="sortDef1" class="list-group-item first-list-option" onclick="selectSort(this.id)">Most Recent</a>
    <a href="#" id="sortDef2" class="list-group-item" onclick="selectSort(this.id)">Oldest</a>
    <a href="#" id="sortDef3" class="list-group-item" onclick="selectSort(this.id)">A-Z Alphabetical</a>
    <a href="#" id="sortDef4" class="list-group-item" onclick="selectSort(this.id)">Z-A Alphabetical</a>
  </div>
</div>

<!-- Main album view -->
<div class="container">
  <div class="col-12 col-md-12">
    <!-- PLACEHOLDER -> SMALL CIRCLE TO CALL THE SORT ORDERS ON MOBILE-->
    <!-- Feature Entry-->
    <div class="row">
      <div class="col-12 col-lg-6 tile-wrapper">
        <div class="feed-tile">
          <!-- LINK TO ARTICLE HERE -->
          <a href="http://google.ca" class="hover-detect"><span class="entry-url"></span></a>
          <h5 class="entry-heading">Article Heading</h5>
          <div class="image"><img src="https://hbr.org/resources/images/article_assets/2017/09/sept17-14-668999778.jpg"/></div>
          <div class="entry-stats">
            <p class="site-info">
              <img src="http://jsfiddle.net/favicon.png" class="site-icon"/><!-- SITE ICON -->
              <a class="site-info-url" href="https://getpocket.com/a/queue/">google.ca</a>
            </p>
          </div>
        </div><!--/span-->
      </div>
      <div class="col-6 col-lg-3 tile-wrapper">
        <div class="feed-tile">
          <!-- LINK TO ARTICLE HERE -->
          <a href="http://google.ca" class="hover-detect"><span class="entry-url"></span></a>
          <h4 class="entry-heading">Article Heading</h4>
          <div class="image"><img src="https://hbr.org/resources/images/article_assets/2017/09/sept17-14-668999778.jpg"/></div>
          <div class="entry-stats">
            <p class="site-info">
              <img src="http://jsfiddle.net/favicon.png" class="site-icon"/><!-- SITE ICON -->
              <a class="site-info-url" href="https://getpocket.com/a/queue/">google.ca</a>
            </p>
          </div>
        </div><!--/span-->
      </div>
      <div class="col-6 col-lg-3 tile-wrapper">
        <div class="feed-tile">
          <!-- LINK TO ARTICLE HERE -->
          <a href="http://google.ca" class="hover-detect"><span class="entry-url"></span></a>
          <h4 class="entry-heading">Article Heading</h4>
          <div class="image"><img src="https://hbr.org/resources/images/article_assets/2017/09/sept17-14-668999778.jpg"/></div>
          <div class="entry-stats">
            <p class="site-info">
              <img src="http://jsfiddle.net/favicon.png" class="site-icon"/><!-- SITE ICON -->
              <a class="site-info-url" href="https://getpocket.com/a/queue/">google.ca</a>
            </p>
          </div>
        </div><!--/span-->
      </div>
      <div class="col-6 col-lg-3 tile-wrapper">
        <div class="feed-tile">
          <!-- LINK TO ARTICLE HERE -->
          <a href="http://google.ca" class="hover-detect"><span class="entry-url"></span></a>
          <h4 class="entry-heading">Article Heading</h4>
          <div class="image"><img src="https://hbr.org/resources/images/article_assets/2017/09/sept17-14-668999778.jpg"/></div>
          <div class="entry-stats">
            <p class="site-info">
              <img src="http://jsfiddle.net/favicon.png" class="site-icon"/><!-- SITE ICON -->
              <a class="site-info-url" href="https://getpocket.com/a/queue/">google.ca</a>
            </p>
          </div>
        </div><!--/span-->
      </div>
      <div class="col-6 col-lg-3 tile-wrapper">
        <div class="feed-tile">
          <!-- LINK TO ARTICLE HERE -->
          <a href="http://google.ca" class="hover-detect"><span class="entry-url"></span></a>
          <h5 class="entry-heading">Article Heading which is longer than before, therefore hopefully shrinking image</h5>
          <div class="image"><img src="https://hbr.org/resources/images/article_assets/2017/09/sept17-14-668999778.jpg"/></div>
          <div class="entry-stats">
            <p class="site-info">
              <img src="http://jsfiddle.net/favicon.png" class="site-icon"/><!-- SITE ICON -->
              <a class="site-info-url" href="https://getpocket.com/a/queue/">google.ca</a>
            </p>
          </div>
        </div><!--/span-->
      </div>
      <div class="col-6 col-lg-3 tile-wrapper">
        <div class="feed-tile">
          <!-- LINK TO ARTICLE HERE -->
          <a href="http://google.ca" class="hover-detect"><span class="entry-url"></span></a>
          <h4 class="entry-heading">Article Heading</h4>
          <div class="image"><img src="https://hbr.org/resources/images/article_assets/2017/09/sept17-14-668999778.jpg"/></div>
          <div class="entry-stats">
            <p class="site-info">
              <img src="http://jsfiddle.net/favicon.png" class="site-icon"/><!-- SITE ICON -->
              <a class="site-info-url" href="https://getpocket.com/a/queue/">google.ca</a>
            </p>
          </div>
        </div><!--/span-->
      </div>
    </div><!--/row-->
  </div><!--/span-->
</div>

</body>
<!-- Scripting -->
<script src="displayManager.js"></script>
</html>

<!--
<div class="container">
  <div class="jumbotron">
    <h1>Navbar example</h1>
    <p class="lead">This example is a quick exercise to illustrate how the top-aligned navbar works. As you scroll, this navbar remains in its original position and moves with the rest of the page.</p>
  </div>
</div>

<form class="form-inline mt-2 mt-md-0">
  <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
  <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
</form>
-->
