<?php
/*** A stupid simple PHP-based file manager ***/

// Were to put the files. Assume the "fm" directory where this script
// Is located but you can change it to anything you want.
$storage_dir = implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), 'fm'));

// Set to the name of the file that is the header and footer of the HTML
$header = null;
$footer = null;


/* Everything below here you can ignore */
function fn($file, $check_security=true) {
  global $storage_dir;
  $path = implode(DIRECTORY_SEPARATOR, array($storage_dir, $file));
  if($check_security) {
    $path = realpath($path);
    if(strpos($path, $storage_dir) === false) die('Security error');
  }
  return $path;
}
function path($file) {
  global $storage_dir;
  return str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  if(array_key_exists('filename', $_POST)) {
    $filename = fn($_POST['filename']);
    if(file_exists($filename)) unlink($filename);
  } else {
    $tmp = $_FILES['new_file']['tmp_name'];
    if(is_uploaded_file($tmp)) {
      $filename = fn(basename($_FILES['new_file']['name']), false);
      move_uploaded_file($tmp, $filename);
    }
  }
  header("Location: $_SERVER[REQUEST_URI]");
  exit;
}
$files = glob(fn('*', false));
if(!is_null($header)) require $header; ?>
<style type="text/css">
  ul#file-list, ul#file-list li {
    margin: 0; padding: 0;
    list-style-type: none;
  }
  #delete-file {display: inline}
  #delete-file input {
    background-color: transparent;
    border-width: 0;
    text-decoration: underline;
    padding: 0; margin: 0 0.5em;
    cursor: pointer;
  }
</style>

<div id="file-manager">
  <h2>Uploaded Files</h2>
  <ul id="file-list">
    <li>
      <form method="POST" enctype="multipart/form-data" id="new-file">
        <input type="file" name="new_file">
        <input type="submit" value="Upload">
      </form>
    </li>
    <?php foreach($files as $file) { ?>
      <li>
        <a href="<?php echo path($file) ?>">
          <?php echo basename($file) ?>
        </a>
        <form method="POST" id="delete-file">
          <input type="hidden" name="filename"
            value="<?php echo basename($file) ?>">
          <input type="submit" value="Delete">
        </form>
      </li>
    <?php } ?>
  </ul>
</div>
<?php if(!is_null($footer)) require $footer ?>
