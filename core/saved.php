<?php
require_once('manageUser.php');
require_once('buildConfig.php');
?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Adam Carnaffan">
  <link rel="icon" href="assets/icon.png">

  <title>Intrigue View <?php echo $cfg->displayVersion ?></title>

  <!-- Bootstrap core CSS -->
  <link href="styling/bootstrap.min.css" rel="stylesheet">
  <link href="styling/bootstrap-grid.css" rel="stylesheet">
  <!-- Custom styles -->
  <link href="styling/custom-styles.css" rel="stylesheet">
  <!-- Javascript -->
  <script src='js/jquery-3.2.1.min.js'></script>
  <script src='js/displayManager.js'></script>
  <script src='js/loginManager.js'></script>
  <script src='js/popper.js'></script>
  <script src='js/bootstrap.js'></script>
</head>
<body class="hide-overflow dark-back" onresize='resizeCanvas'>
  <!-- Fixed navbar -->
  <nav class="navbar navbar-expand-md navbar-dark bg-dark dropdown-ontop">
    <a class="icon-brand icon-sprite-white" href="index.php"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mr-auto nav-navigation fix-ul">
        <li class="nav-item active nav-hoverable">
          <a class="nav-link nav-underline" title="See the Most Popular Articles From the Last Few Days" href="index.php">Featured<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active nav-hoverable">
          <a class="nav-link nav-underline" title="Scroll Through a Continuous Feed of Recommended Articles" href="recommended.php">Recommended<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active nav-hoverable">
          <a class="nav-link nav-underline" title="Browse a Compilation of All Public Feeds" href="browse.php">Browse<span class="sr-only">(current)</span></a>
        </li>
        <?php
        if (!$user->isTemp) {
          echo '<li class="nav-item active nav-selected">
            <a class="nav-link" title="See Your Saved Tiles" href="saved.php">Saved<span class="sr-only">(current)</span></a>
          </li>';
        }
        ?>
        <li class="nav-item active">
          <a class="nav-link nav-underline nav-activate" href="#" title="Export the Current Feed as RSS" onclick="return openInNewTab('feed.php?size=10&selection=' + feedSelection.join('+'))">Export RSS<span class="sr-only">(current)</span></a>
        </li>
      </ul>
      <ul class="navbar-nav mr-auto fix-ul">
      </ul>
      <ul class="navbar-nav dropdown-ontop">
        <?php
          // Change the User display based on a logged in user
          if (!$user->isTemp) {
            echo "<div class='dropdown'>";
            echo '<a class="nav-item active nav-link nav-underline hover-highlight dropdown-toggle" href="#" data-toggle="dropdown">Welcome back, ' . $user->name . '</a>';
            echo '<ul class="dropdown-menu dropdown-menu-right dropdown-align">';
            echo '<li display="block"><a class="move-right dropdown-link" href="profile.php">My Profile</a></li>';
            echo '<li class="divider"></li>';
            echo '<li display="block"><a class="move-right dropdown-link" href="builder.php">Feed Builder</a></li>';
            echo '<li class="divider"></li>';
            echo '<li display="block"><a class="move-right dropdown-link" href="settings.php">Settings</a></li>';
            if ($user->isAdmin) {
              echo '<li class="divider"></li>';
              echo '<li display="block"><a class="move-right dropdown-link" href="admin/splash.php">Administration</a></li>';
            }
            echo '<li class="divider"></li>';
            echo '<li display="block"><a class="move-right dropdown-link" href="#" onclick="return logout()">Logout</a></li>';
            echo "</ul>";
            echo "</div>";
          } else {
            echo '<button class="btn btn-outline-success-blue my-2 my-sm-0 separate" onclick="location.href=\'register.php\';">Register</button>';
            echo '<button class="btn btn-outline-success-blue my-2 my-sm-0" onclick="location.href=\'login.php\';">Login</button>';
          }
         ?>
      </ul>
    </div>
  </nav>
   <!-- Sticky Bar for feed selection -->
   <!-- <div id='feed-selection-bar' class='sticky-top'>
     <a id='default-active-feed' class="feed-selector" href='#' onclick="return setActiveFeed('Comps', this)">Compilations</a>
     <a class="feed-selector" href='#' onclick="return setActiveFeed('Saved', this)">Saved</a>
     <a class="feed-selector" href='#' onclick="return setActiveFeed('Sub', this)">Subscriptions</a>
     <a class="feed-selector" href='#' onclick="return setActiveFeed('Faves', this)">Favourites</a>
     <a class="feed-selector" href='#' onclick="return setActiveFeed('Cat', this)">Categories</a>
   </div> -->

   <!-- Main album view -->
   <div id='tag-display' class='container tag-scroller'>
     <div class='tags-title'>Popular Tags:</div>
     <div id='tag-collection' class='tag-block'></div>
   </div>
   <!-- ALL ARTICLES GO HERE -->
   <div class="container no-top-offset" id='feed-content'>
     <div class="col-md-12">
       <div class="row" id="feed-view">
       </div><!--/row-->
     </div><!--/span-->
   </div>

<!-- Bottom Bar -->
<footer class="navbar-dark btm-info bg-dark">
  <a class="fix-link-color nav-link nav-underline" href="https://github.com/Thefaceofbo">By Adam Carnaffan<span class="sr-only">(current)</span></a>
</footer>
</body>
<!-- Scripting -->
<script>
// Define Variable display buttons
var ReturnButton = "<div class='button-holder' id='return-button'><a class='return-button front' href='#' onclick='returnToTop()'><img class='return-button front' src='assets/returnToTop.png'></a></div>";
var loadingCanvas = "<div id='loading'><canvas id='loading-dots' width='900' height='600'>Loading...</canvas></div>";
// Instantiate necessary global variables
var returnButtonIsDisplayed = false;
var cooldown = 0.8;
var entriesDisplayed = 0;
var search = "";
var queryTags = [];
var display = true;
var currentTagMode = 1; // Defined in a global scope to use in multiple functions
// Define different feed selections
var feedSelection = [<?php echo $user->feed; ?>]; // Default feed selection is the user's feed
var selectionOptions = {
  comps: 4,
  saved: <?php echo $user->feed ?>,
  subs: 4, // This is all of the sub-feeds connected to the compilations
  favs: 4, // This is the current feed, though only where isFavourite = 1
  cats: 4 // This is a list of categories for the sub-feeds. All related sub-feeds are displayed
};
// Toggle the AND selection
$('#and-tag').toggleClass('toggle-button-class');
// Make initial display
setActiveFeed('All', $('#default-active-feed'));
queryEntries(51, feedSelection, true);
getTags();

$(document).ready( function () {
  // Reset scroll before watching for scroll changes
  $(this).scrollTop(0);
  // Begin waiting for the scroll
  $(window).scroll(function() {
    // Load more entries
    if (($(document).scrollTop() / ($(document).height() - $(window).height())) > cooldown && entriesDisplayed < 150 && display == true) {
      queryEntries(26, feedSelection, true);
    }
    // Display Settings for the Return to Top button
    if ($(document).scrollTop() > 600 && returnButtonIsDisplayed == false) {
      returnButtonIsDisplayed = true;
      $(document.body).append(ReturnButton);
    } else if ($(document).scrollTop() < 200) {
      $('#return-button').remove();
      returnButtonIsDisplayed = false;
    }
  });
});
// Allow the Search to begin on enter keypress
$('#search-input').keypress(function(event) {
  if (event.keyCode == 13) {
    beginSearch();
  }
});
</script>

</html>
