<html>
<?php
require_once('../manageUser.php');
if (!$user->isAdmin) {
  header('location: ../index.php');
}
?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Adam Carnaffan">
  <link rel="icon" href="assets/icon.png">

  <title>Intrigue View <?php echo $cfg->displayVersion ?></title>

  <!-- Bootstrap core CSS -->
  <link href="../styling/bootstrap.min.css" rel="stylesheet">
  <link href="../styling/bootstrap-grid.css" rel="stylesheet">
  <!-- Custom styles -->
  <link href="../styling/custom-styles.css" rel="stylesheet">
  <!-- JavaScript -->
  <script src='../js/jquery-3.2.1.min.js'></script>
  <script src='administration.js'></script>
  <script src='../js/loginManager.js'></script>
</head>
<body class="hide-overflow">

  <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <a class="navbar-brand" href="../index.php">IntrigueView</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="../index.php">Home <span class="sr-only">(current)</span></a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <button class="btn btn-outline-success-blue my-2 my-sm-0" onclick="logout()">Logout</button>
      </ul>
    </div>
  </nav>

  <div id="sidebar"  class="fix-sidebar">
      <!-- sidebar menu start-->
      <ul class="sidebar-menu">
          <li class="active">
              <a class="" href="splash.php">
                  <span>Dashboard</span>
              </a>
          </li>
          <li class="active">
              <a class="" href="feeds.php">
                  <span>Feeds</span>
              </a>
          </li>
          <li class="active">
              <a class="" href="entries.php">
                  <span>Entries</span>
              </a>
          </li>
          <?php
            foreach ($user->permissions as $perm) {
              if ($perm->permissionID == 1) {
                echo '<li class="active">
                    <a class="" href="users.php">
                        <span>Users</span>
                    </a>
                </li>';
                break;
              }
            }
          ?>
      </ul>
      <!-- sidebar menu end-->
  </div>

  <!-- PAGE SPECIFIC CONTENT STARTS -->
  <div class="container">
    <?php
      // Determine which feeds the user can manage
      $showAllFeeds = false;
      $feedsList = [];
      $feeds = [];
      foreach ($user->permissions as $perm) {
        if ($perm->permissionID == 2) {
          if ($perm->feedID == null) {
            $showAllFeeds = true;
            break;
          } else {
            array_push($feeds, $perm->feedID);
          }
        }
      }
      
      $getAllFeedIds = "SELECT sourceID, isExternalFeed FROM feeds";
      if ($showAllFeeds) {
        $result = $conn->query($getAllFeedIds);
        while ($row = $result->fetch_array()) {
          if ($row[1] == 1) {
            array_push($feedsList, $row[0]);
          }
        }
      } else {
        $result = $conn->query("SELECT feedID FROM user_permissions WHERE userID = '$user->id'");
      }
      // Execute query and prepare results
      $feedInfos = [];
      foreach ($feedsList as $feedId) {
        array_push($feedInfos, new Feed($feedId, $conn, 1));
      }
    ?>
    <!-- EDIT FEEDS -->
    <h5>Manage External Linked Feeds:</h5>
    <table>
      <?php
        $feedManageCount = 0;
        foreach ($feedInfos as $feed) {
          // Only display a feed that can be edited
          if ($feed->source != null) {
            echo "<tr>";
            echo "<td>" . $feed->title . "</td>";
            echo "<td><input class='feed-source-input feed-url' id='feed_" . $feed->id . "' type='text' name='feed_" . $feed->id . "' placeholder='" . $feed->source . "...'/>";
            echo "<td><button class='feed-source-input' id='changeURLFeed_" . $feed->id . "' onclick='changeFeedURL(" . $feed->id . ")' >Change URL</button></td>";
            // Add a check button to check that the feed source is valid
            echo "<td><button class='feed-source-input' id='refreshFeed_" . $feed->id . "' onclick='refreshFeed(" . $feed->id . ", this)'>Refresh</button></td>";
            echo "<td><button class='feed-source-input' id='deleteFeed_" . $feed->id . "' onclick='deleteFeed(" . $feed->id . ")' >Delete</button></td>";
            echo "</tr>";
          }
          $feedManageCount++;
        }
        if ($feedManageCount == 0) {
          echo "<h7 class='vertical-center'>No Feeds to Display</h7>";
        }
       ?>
       <!-- SAVE CHANGES BUTTON -->
    </table>
  </br>
  </br>
  <!-- ADD FEEDS -->
    <!-- ERROR RETURN -->
    <?php
      // If the user is allowed to create new linked feeds, display this portion
      foreach ($user->permissions as $perm) {
        if ($perm->permissionID == 3) {
          echo '<h5>Add a Linked Feed:</h5>';
          echo "<table><tr><td><b>New Feed Name</b></td><td><b>Feed Source URL</b></td></tr><tr>";
          echo "<td><input class='feed-source-input' id='addedFeedName' type='text' name='newFeedName' placeholder='New Feed Name'/></td>
                <td><input class='feed-source-input feed-url' id='addedFeedURL' type='text' name='newURL' placeholder='Source URL'/></td></tr>";
          echo "<tr><td><b>Image Link</b></td></tr><tr>";
          echo "<td colspan=2><input class='feed-source-input feed-url' id='addedFeedImage' type='text' name='newImg' placeholder='Feed Image URL'></td></tr>";
          echo "<tr><td><b>Description</b></td></tr><tr>";
          echo "<td colspan=2><textarea id='addedFeedDesc' name='newDesc' cols='62' rows='6' placeholder='Feed Description'></textarea></td></tr>";
          echo "</table></br>";
          echo "<button class='feed-source-input' id='submitNewFeed' onclick='submitFeed()'>Submit</button>";
          break;
        }
      }

     ?>
   </br>
 </br>
    <!-- Export an RSS Feed -->
    <h5>Export an RSS Feed:</h5>
    <b>Feed Name: </b>
    <select id='feed-selector'>
      <option value='0'>All</option>
      <?php
        $feedsList = [];
        $result = $conn->query($getAllFeedIds);
        while ($row = $result->fetch_array()) {
          //echo $row[0];
          array_push($feedsList, new FeedInfo($row[0], $conn, $row[1]));
        }
        foreach ($feedsList as $feed) {
          echo "<option value='" . $feed->id .  "'>" . $feed->title . "</option>";
        }
       ?>
    </select>
  </br>
  <b>Export Size: </b>
  <select id='export-quantity'>
    <?php
      for ($x = 10; $x < 100; $x *= 2) {
        echo "<option value='" . $x .  "'>" . $x . "</option>";
      }
     ?>
     <option value='100'>100</option>
     <option value='*'>All</option>
  </select>
</br>
  <button class='feed-source-input' id='getRSSFeed' onclick='getRSS()'>Export</button>
       <!-- ADD THE ABILITY TO COMBINE FEEDS HERE -->
  </div>


</body>
</html>
