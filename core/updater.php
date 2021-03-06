<?php
// Access the version file for the local machine
require_once('buildConfig.php');
require_once('dbConnect.php');

mkdir("tempDir");

// Define a shutdown function
register_shutdown_function(function() {
  // Remove the temporary directory
  removeDirectory("tempDir");
});

// The root of the hosted project
$gitRoot = 'https://raw.githubusercontent.com/Thefaceofbo/intrigueView/master/';

// Download version data
if (!download($gitRoot . "currentVersion.json")) {
  removeDirectory("tempDir");
  exit;
}

// Get the version data
$gitVersion = json_decode(file_get_contents("tempDir\currentVersion.json"));

// Stop the run if the version number is the current
if ($cfg->trackingVersion == $gitVersion->sourceVersion) {
  echo "The site is currently up to date";
  removeDirectory("tempDir");
  return;
}

// UPDATE DB

// Pull the database scripts
// if ($conn->query("SELECT dbVersion FROM versionTracker ORDER BY dateApplied LIMIT 1") != $gitVersion->databaseVersion) {
//   // Check the number of database versions from the original offset
//   // Pull the most recent script and run
// }

// UPDATE FILES

// Set the git API file tree URL
$gitTreeAPIPath = 'https://api.github.com/repos/thefaceofbo/intrigueview/contents/core?ref=master';
// Get all required files for update
$info = getDirectoryData($gitTreeAPIPath);
$fileDownloadLinks = getFiles($info, $cfg, true);

// Loop and download each file
foreach ($fileDownloadLinks as $fileLink) {
  download($fileLink);
}

// Target the downloaded update in the folder
$updatePath = "{$cfg->coreDirectory}tempDir\\";

// Generate an array of absolute paths to the files
$fileList = getUpdatedFiles($updatePath);

// Change the paths to relative for copy()
foreach ($fileList as &$file) {
  $file = convertToRelativePath($file, $cfg);
}
unset($file); // required to avoid php defect

// Copy each file into the core
foreach ($fileList as $target) {
  $location = str_replace("tempDir\core\\", "", $target);
  // Get the folders containing the file
  $folders = explode("\\", $location);
  array_pop($folders);
  // Begin folder check
  $current = ""; // set current directory
  foreach ($folders as $dir) {
    $current .= "\\$dir";
    if (!is_dir($current)) {
      mkdir($current);
    }
  }
  copy($target, $location);
}

// Remove the files at the end
removeDirectory("tempDir");

// Remove a second time as it tends to ghost
while (is_dir("tempDir")) {
  removeDirectory("tempDir");
}

// Update the version number tracking in the json

// FUNCTIONS

function removeDirectory($directory) {
  // Pause to prevent file lockup
  sleep(5);
  // Begin Removal
  if (!is_dir($directory)) {
    return;
  }
  $files = glob($directory . '*', GLOB_MARK);
  foreach ($files as $file) {
    if (is_dir($file)) {
      removeDirectory($file);
    } else {
      unlink($file);
    }
  }
  @rmdir($directory);
}

function download($gitFilePath) {
  $data = getFileData($gitFilePath);

  $fileName = explode("/master/", $gitFilePath)[1];

  if (count(explode("/", $fileName)) > 1) {
    $subFolders = explode("/", $fileName);
    array_pop($subFolders);
    $currentDir = "tempDir/";
    foreach ($subFolders as $dir) {
      $targetDirectory = "$currentDir/$dir";
      if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory);
      }
      $currentDir .= "$dir/";
    }
  }

  $newFile = fopen("tempDir/$fileName", "w");

  fwrite($newFile, $data);

  return $newFile;
}

function getFileData($gitLink) {
  $curlConn = curl_init();
  curl_setopt($curlConn, CURLOPT_URL, $gitLink);
  curl_setopt($curlConn, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curlConn, CURLOPT_RETURNTRANSFER, 1);
  $data = curl_exec($curlConn);
  curl_close($curlConn);

  if ($data == "404 Error: Not Found") {
    return false;
  }

  return $data;
}

function getDirectoryData($path) {
  $curlConn = curl_init();
  curl_setopt($curlConn, CURLOPT_URL, $path);
  curl_setopt($curlConn, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curlConn, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curlConn, CURLOPT_USERAGENT, "IntrigueView App");
  $data = curl_exec($curlConn);
  curl_close($curlConn);

  return json_decode($data);
}

function getFiles($directoryArray, $config, $isRoot = false) {
  $paths = [];
  $blacklistLink = "https://raw.githubusercontent.com/Thefaceofbo/intrigueView/master/fileBlacklist.txt";
  $fileBlacklist = ($isRoot) ? explode(",", getFileData($blacklistLink)) : [];

  foreach ($directoryArray as $file) {
    if ($file->download_url !== null) {
      if (!in_array($file->name, $fileBlacklist)) {
        $paths[] = $file->download_url;
      }
    } else {
      if ($isRoot && ommitFile($file->name, $config)) {
        // Skip the ommited directory
        continue;
      }
      $directoryArray = getFiles(getDirectoryData($file->url), $config);
      $paths = array_merge($paths, $directoryArray);
    }
  }

  return $paths;
}

function ommitFile($fileName, $config) {
  // Underscores are used in version tracking for specific directories
  // This allows the name of the directory to be easily isolated
  foreach ($config->fileVersions as $tracked) {
    if ($fileName == $tracked->name) { // Check version too
      return true;
    }
  }
  return false;
}

function getUpdatedFiles($directory) {
  $files = [];
  $directories = glob($directory . '*', GLOB_MARK);
  foreach ($directories as $path) {
    if (is_dir($path)) {
      $tempFilesArray = getUpdatedFiles($path);
      $files = array_merge($files, $tempFilesArray);
    } else {
      $files[] = $path;
    }
  }
  return $files;
}

function convertToRelativePath($path, $cfg) {
  return str_replace($cfg->coreDirectory, "", $path);
}

/*

3) Check for DB scripts
4) If DB scripts exist, backup DB and run them
5) If DB scripts fails, stop and restore old db

*/

//
// foreach ($changedFiles as $name) {
//   if (str_pos('db_install/', $name) !== false) {
//     $dbChanges[] = $name;
//   }
// }
//
// if (count($dbChanges) > 0) {
//   // Backup the DB
//
//   // Run scripts
//   foreach ($dbChanges as $name) {
//     if (explode("/", $name)[1] != "db_finals" && explode(".", $name)[1] == "sql") {
//       $script = $fetchToTemp($name);
//       if (!$conn->multi_query($script)) {
//
//         throw new Exception("The following error occured while updating the database: '{$conn->error}'");
//       }
//     }
//   }
// }
//
// $fetchToTemp = function ($fileName) use ($gitRoot) {
//
//   return "Open File in fopen format";
// };

?>
