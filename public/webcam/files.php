<?
header("Content-type: text/json");
$dir = "uploads";
$files = array();
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
             $files[] = $file;
        }
        closedir($dh);
    }
}
for($i=0;$i<=count($files);$i++){
 if($files[$i]=="." || $files[$i]==".."){
  unset($files[$i]);
 }
}
echo json_encode($files);