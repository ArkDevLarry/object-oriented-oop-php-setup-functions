<?php
function show($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}


function echor($data)
{
    echo $data;
}

function asset($data)
{
    $path = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
    $path = str_replace('index.php', '', $path);
    $full_path = $path.$data;
    return $full_path;
}

function check_input($name){
    $data =trim($name);
    $data =stripslashes($name);
    $data =htmlspecialchars($name);
    return $data;
}

function check_message($key) {
    if (isset($_SESSION[$key]) && $_SESSION[$key] !== "") {
        echo $_SESSION[$key];
        unset($_SESSION[$key]);
    }
}

function compare($str1, $str2) {
    similar_text($str1, $str2, $per);
    return $per;
}

function rand_str($length) {
    $array = array(0,1,2,3,4,5,6,7,8,9,
                'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
             );
    $text = "";
    if ($length > 15) {
        $length = rand(15,$length);
    }else{
        $length = 20;
    }
    for ($i=0; $i < $length; $i++) { 
        $random = rand(0,61);
        $text .= $array[$random];
    }
    return $text;
}

function slugify($text, string $divider = '-')
{
  // replace non letter or digits by divider
  $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, $divider);

  // remove duplicate divider
  $text = preg_replace('~-+~', $divider, $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return rand_str(30).'n-a';
  }

  return $text;
}

function diffForHumans($originalDate){
    $startDate = date("Y-m-d H:i:s", strtotime($originalDate));
    $endDate = date('Y-m-d H:i:s');
    $diff = (new DateTime($startDate))->diff(new DateTime($endDate));

    $lookup = [
        'y' => 'Year',
        'm' => 'Month',
        'd' => 'Day',
        'h' => 'Hour',
        'i' => 'Minute',
        's' => 'Second',
    ];

    $elements = [];
    foreach ($lookup as $property => $word) {
        if ($diff->$property) {
            $elements[] = "{$diff->$property} $word" . ($diff->$property !== 1 ? 's' : '');
        }
    }

   echo isset($elements[0]) ? $elements[0] . " Ago" : '1 second Ago' ;
}

function cookie_check($e)
{
    if(isset($_COOKIE[$e])) {
        echo $_COOKIE[$e];
    }else {
        '';
    }
}

function convertToWebP($rawN, $ext, $dir) {
    $image = imagecreatefromstring(file_get_contents($dir.$rawN.'.'.$ext));
    ob_start();

    if ($ext=="png") {
        imagepng($image,NULL,9);
    }elseif ($ext=="jpeg") {
        imagejpeg($image,NULL,100);
    }elseif ($ext=="jpg") {
        imagejpg($image,NULL,100);
    }elseif ($ext=="gif") {
        imagegif($image,NULL,100);
    }else {
        return false;
    }

    $cont = ob_get_contents();
    ob_end_clean();
    imagedestroy($image);
    $content = imagecreatefromstring($cont);
    $saveTo = $dir.$rawN.'.webp';
    imagewebp($content,$saveTo);
    imagedestroy($content);
    
}

function resize_image($pathtofile, $max_resolution) {
    if (file_exists($pathtofile)) {
        $original_image = imagecreatefromwebp($pathtofile);

        //resolution
        $original_width = imagesx($original_image);
        $original_height = imagesy($original_image);

        $ratio = $max_resolution / $original_width;
        $new_width = $max_resolution;
        $new_height = $original_height*$ratio;

        if ($new_height > $max_resolution) {
            $ratio = $max_resolution /$original_height;
            $new_height = $max_resolution;
            $new_width = $original_width * $ratio;
        }

        if ($original_image) {
            $new_image = imagecreatetruecolor($new_width,$new_height);
            imagecopyresampled($new_image,$original_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
            imagewebp($new_image,$pathtofile,80);
        }
    }
}

function crop_image($file, $ext, $max_resolution)
    {
        if (file_exists($file)) {
            if ($ext=="png") {
                $original_image = @imagecreatefrompng($file);
            }elseif ($ext=="jpeg" || $ext=="jpg") {
                $original_image = imagecreatefromjpeg($file);
                if ($meta_data = exif_read_data($file)) {
                    if (isset($meta_data['Orientation'])) {
                        $number = $meta_data['Orientation'];
                        if ($number==3) {
                            $orientation = 180;
                        }elseif ($number==5) {
                            $orientation = -90;
                        }elseif ($number==6) {
                            $orientation = -90;
                        }elseif ($number==7) {
                            $orientation = -90;
                        }elseif ($number==8) {
                            $orientation = 90;
                        }
                    }
                }
            }elseif ($ext=="gif") {
                $original_image = imagecreatefromgif($file);
            }elseif ($ext=="webp"){
                $original_image = imagecreatefromwebp($file);
            }else {
                $ext = 'jpeg';
                $original_image = imagecreatefromjpeg($file);
                if ($meta_data = exif_read_data($file)) {
                    if (isset($meta_data['Orientation'])) {
                        $number = $meta_data['Orientation'];
                        if ($number==3) {
                            $orientation = 180;
                        }elseif ($number==5) {
                            $orientation = -90;
                        }elseif ($number==6) {
                            $orientation = -90;
                        }elseif ($number==7) {
                            $orientation = -90;
                        }elseif ($number==8) {
                            $orientation = 90;
                        }
                    }
                }
            }

            //Get orientation information if file is jpg
            $orientation = 0;
            

            //resolution
            $original_width = imagesx($original_image);
            $original_height = imagesy($original_image);

            if ($original_height >$original_width) {
                $ratio = $max_resolution / $original_width;
                $new_width = $max_resolution;
                $new_height = $original_height * $ratio;

                $diff = ($new_height - $new_width)/2;
                $x = 0;
                $y = round($diff);
            }else {
                $ratio = $max_resolution /$original_height;
                $new_height = $max_resolution;
                $new_width = $original_width * $ratio;

                $diff = ($new_width - $new_height)/2;
                $x = round($diff);
                $y = 0;
            }

            if ($original_image) {
                $new_image = imagecreatetruecolor($new_width,$new_height);
                imagecopyresampled($new_image,$original_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);

                $new_crop_image = imagecreatetruecolor($max_resolution,$max_resolution);
                imagecopyresampled($new_crop_image,$new_image,0,0,$x,$y,$max_resolution,$max_resolution,$max_resolution,$max_resolution);

                //rotate image if necessary
                if ($orientation!=0) {
                    $new_crop_image = imagerotate($new_crop_image, $orientation, 0);
                }
                
                if ($ext=="png") {
                    imagepng($new_crop_image,$file);
                }elseif ($ext=="jpeg" || $ext=="jpg") {
                    imagejpeg($new_crop_image,$file);
                }elseif ($ext=="gif") {
                    imagegif($new_crop_image,$file);
                }elseif($ext=="webp"){
                    imagewebp($new_crop_image,$file);
                }else {
                    imagejpeg($new_crop_image,$file);
                }
                imagedestroy($new_crop_image);
                imagedestroy($original_image);
            }
        }
    }