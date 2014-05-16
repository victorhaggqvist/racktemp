<?php
// echo shell_exec('pwd');
if (defined("AUTOLOAD_PATH"))
  loadDir(AUTOLOAD_PATH);
else
  loadDir('lib/classes');

/**
 * Reqursive class loader
 * @param  string $path path to load
 */
function loadDir($path) {
  $dir = scandir($path);
  // var_dump($dir);

  foreach ($dir as $d) {
    if($d != '.' && $d != '..' & (!preg_match("/[~]+/", $d))){
      $nextdir = $path.'/'.$d;
      if (is_dir($nextdir))
        loadDir($nextdir);
      else {
        // $file = substr($nextdir,3,strlen($nextdir));
        $file = $nextdir;
        require_once $file;
        // echo 'req1 '.$file."\n";
      }
    }
  }
}
?>
