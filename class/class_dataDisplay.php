<?php

class Entry_Display extends Entry {

  public $entryDisplaySize;
  public $contextMenu;
  public $isFeatured;

  public function __construct($dataArray, $dataTags, $displayContext) {
    // Get all data from the Query. Indexes are based on position in the query
    $this->title = $dataArray[0];
    $this->url = $dataArray[1];
    $this->image = $dataArray[3];
    $this->synopsis = $dataArray[4];
    $this->isFeatured = ($dataArray[5] == 1) ? true : false; // Create a boolean based on the data table output. This boolean decides highlighting
    $this->siteURL = $dataArray[6];
    $this->siteIcon = $dataArray[7];
    $this->id = $dataArray[8];
    $this->views = $dataArray[11];
    $this->rating = 5; // We only put out the highest quality content xD
    // Build the tags array
    while ($row = $dataTags->fetch_array()) {
      $this->tags[$row[2]] = $row[1];
    }
    if ($displayContext == "Saved") {
      $this->contextMenu = "X FOR REMOVING";
    } else {
      $this->contextMenu = "<a href='#' class='context-display' onclick='return saveEntry(this, " . $this->id . ")'><span class='fa fa-plus fa-context-style'></span></a>";
    }
  }

  public function displayEntryTile($entryDisplay, $featuredTiles) {
    if (in_array($entryDisplay, $featuredTiles)) { // Decide if the article will be a feature or not
      $this->entryDisplaySize = 2;
    } else {
      $this->entryDisplaySize = 1;
    }
    // Begin building the entry tile
    if ($this->entryDisplaySize == 1) {
      $tile = '<div class="col-6 col-lg-3 tile-wrapper">';
    } else {
      $tile = '<div class="col-12 col-lg-6 tile-wrapper">';
    }
    // Add entry tile Class
    $tile .= '<div class="entry-tile';
    if ($this->isFeatured) {
      $tile .= ' featured-tile';
    }
    $tile .= '">';
    // Add Article URL
    $tile .= '<a href="' . $this->url . '" onclick="return openInNewTab(\'' . $this->url . '\', \'' . $this->id . '\')" class="hover-detect" id="lol-test"><span class="entry-url"></span></a>';
    // Add Article Heading
    $tile .= '<h5 class="entry-heading">' . $this->title . '</h5>';

    // Add Article Image Display
    if ($this->image != null) {
      $tile .= '<div class="image-container"><img class="image" src="' . $this->image . '"/>';
    } else {
      $tile .= '<div class="image-container"><img class="image fill-size" src="assets/tileFill.png"/>';
    }

    // Begin site details slider
    $tile .= '<div class="extra-info">';
    // Add Top Tags
    $tile .= '<div class="entry-stats tag-display extra-info-addon">Tags: ';
    // Initialize a counter
    $c = 1;
    foreach ($this->tags as $id=>$tag) {
      $tile .= '<a class="tag in-extra-info" href="#" onclick="return addTag(' . $id . ')">' . $tag . '</a> ';
      $c++;
    }
    $tile .= '</div>';
    // Display Article Synopsis
    $tile .= '<div class="extra-info-addon extra-info-synopsis">' . $this->synopsis . '</div>';
    // Display Entry Stats
    $tile .= '<div class="extra-info-addon extra-info-bottom-data"><div class="extra-info-views">Views: ' . $this->views . '</div><div class="extra-info-rating">Rating: ' . $this->rating . '/5</div></div>';
    $tile .= '</div></div>';

    // Add Site Stats
    $tile .= '<div class="entry-stats">';
    // Site Icon
    if ($this->siteIcon != null) { // Handle cases where site icons haven't fetched properly or don't exist
      $tile .= '<img src="' . $this->siteIcon . '" class="site-icon"/>';
    }
    // Site URL (hyperlink)
    $linkedURL = "http://" . $this->siteURL;
    $tile .= '<a class="site-info-url" href="' . $linkedURL . '">';
    // Site URL (visual)
    $tile .= $this->siteURL . '</a>';
    // Context Display
    $tile .= $this->contextMenu;
    // Close all required tags
    $tile .= '</div></div></div>';
    return $tile;
  }

}

class FeedDisplay {

  public $name;
  public $id;
  public $size;
  public $updateRate;
  public $description;
  public $imagePath;
  public $author;
  public $categories = [];

  public function __construct($dataPackage, $dbConn) {
    $this->id = $dataPackage[0];
    $this->author = $dataPackage[1];
    $this->name = $dataPackage[2];
    $this->imagePath = $dataPackage[3];
    $this->description = $dataPackage[4];
    $this->size = $dataPackage[5];
    $this->getCategories($dbConn);
  }

  public function getCategories($dbConn) {
    $getCatsQuery = "SELECT categories.categoryID, categories.label FROM feed_categories AS catConn
                      JOIN categories ON categories.categoryID = catConn.categoryID
                      WHERE catConn.feedID = '$this->id'";
    $categoriesReturned = $dbConn->query($getCatsQuery);
    while ($catSelected = $categoriesReturned->fetch_array()) {
      $this->categories[$row[0]] = $row[1];
    }
    if (count($this->categories) < 1) {
      array_push($this->categories, "Unsorted");
    }
  }

  public function generateTile() {
    $tile = "<div class='feed-tile'><div class='feed-tile-image-container'>";
    // Add the image
    if ($this->imagePath == null || $this->imagePath == "") {
      $this->imagePath = "assets/feedFiller.jpg";
    }
    $tile .= "<img class='feed-tile-image' src='" . $this->imagePath . "'></div>";
    // Begin feed info divider
    $tile .= "<div class='feed-tile-info'>";
    // Feed Reference
    $tile .= "<a href='viewFeed.php?feedID=" . $this->id . "' onclick='return selectFeed(this, " . $this->id . ")' class='hover-detect'><span class='entry-url'></span></a>";
    // Feed Title
    $tile .= "<h4 class='feed-tile-title'>" . $this->name . "</h4>";
    // Feed Description
    $tile .= "<p class='feed-tile-desc'>" . $this->description . "</p>";
    // Begin feed footer divider
    $tile .= "<div class='feed-tile-footer'>";
    // Generate Categories
    $tile .= "<b>Categories: </b>";
    foreach ($this->categories as $catID=>$category) {
      $tile .= "<a class='tag' href='#' onclick='return sortByCategory(" . $catID . ")'>" . $category . "</a>";
    }
    // Place the Subscription button
    $tile .= "<a class='context-display' href='#' onclick='return saveFeed(this, " . $this->id . ")'><span class='fa fa-plus fa-context-style'></span></a>";
    // Close all divs
    $tile .= "</div></div></div>";
    return $tile;
  }

}

class FeedInfo {

  public $title;
  public $source;
  public $id;
  public $busy;
  public $isExternal = false;

  public function __construct($feedId, $dbConn, $isExternal) {
    $this->id = $feedId;
    if ($isExternal) {
      $feedType = "external_feeds";
      $includedFields = "url, title, busy";
      $idColumn = "externalFeedID";
      $this->isExternal = true;
    } else {
      $feedType = "user_feeds";
      $includedFields = "title";
      $idColumn = "internalFeedID";
    }
    $sourceQuery = "SELECT $includedFields FROM $feedType WHERE $idColumn = '$this->id' AND active = 1";
    if ($result = $dbConn->query($sourceQuery)) {
      $sourceInfo = $result->fetch_array();
    } else {
      throw new exception($dbConn->error);
    }
    $this->source = (isset($sourceInfo['url'])) ? $sourceInfo['url'] : null;
    $this->title = $sourceInfo['title'];
    $this->busy = (isset($sourceInfo['busy'])) ? $sourceInfo['busy'] : 0;
  }

}

 ?>