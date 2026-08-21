<?php
include_once('connect.php');
function getExtension($str)
{
    $i = strrpos($str, ".");
    if (!$i) {
        return "";
    }
    $l = strlen($str) - $i;
    $ext = substr($str, $i + 1, $l);
    return $ext;
}


if (isset($_POST["v_id"])) {
    $v_id = $_POST['v_id'];
    $id = mysqli_fetch_row(mysqli_query($con, "select max(id) from variant_pic"));

    if (isset($_FILES['pic']['name'])) {
        $count = count($_FILES['pic']['name']);
        $pic = $_FILES['pic']['name'];
        $picpath = $_FILES['pic']['tmp_name'];

        for ($i = 0; $i < $count; $i++) {
            $id[0]++;
            $image = $pic[$i];
            $uploadedfile = $picpath[$i];

            if ($image) {
                $filename = stripslashes($pic[$i]);
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                // ✅ Load image resource
                if ($extension == 'jpg' || $extension == 'jpeg') {
                    $src = imagecreatefromjpeg($uploadedfile);

                    // ✅ Fix EXIF orientation before resizing
                    if (function_exists('exif_read_data')) {
                        $exif = @exif_read_data($uploadedfile);
                        if (!empty($exif['Orientation'])) {
                            switch ($exif['Orientation']) {
                                case 3:
                                    $src = imagerotate($src, 180, 0);
                                    break;
                                case 6:
                                    $src = imagerotate($src, -90, 0);
                                    break;
                                case 8:
                                    $src = imagerotate($src, 90, 0);
                                    break;
                            }
                        }
                    }
                } elseif ($extension == 'png') {
                    $src = imagecreatefrompng($uploadedfile);
                } else {
                    continue;
                }

                // ⚡ Get size after rotation
                $width = imagesx($src);
                $height = imagesy($src);

                // 📏 Final desired size
                $newwidth = 600;
                $newheight = 700;

                // 🖼️ Create blank canvas
                $tmp = imagecreatetruecolor($newwidth, $newheight);

                // Preserve PNG transparency
                if ($extension == "png") {
                    imagealphablending($tmp, false);
                    imagesavealpha($tmp, true);
                }

                // 🚀 Stretch image to fill 300x400 (no black border)
                imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                // 📝 Save file
                $filename = "img/product/$id[0]." . $extension;
                if ($extension == "png") {
                    imagepng($tmp, $filename);
                } else {
                    imagejpeg($tmp, $filename, 100);
                }

                imagedestroy($src);
                imagedestroy($tmp);

                // 💾 Save path in DB
                $path = "img/product/$id[0]." . $extension;
                mysqli_query($con, "INSERT INTO variant_pic SET id='$id[0]', v_id='$v_id', pic='$path'");
            }
        }
    }

    // Remove images if requested
    if (isset($_POST['remove'])) {
        $str = explode(";", $_POST['remove']);
        $c = count($str);
        for ($i = 0; $i < $c - 1; $i++) {
            $pic = $str[$i];
            $rem = mysqli_fetch_row(mysqli_query($con, "select pic from variant_pic where id=$pic"));
            mysqli_query($con, "delete from variant_pic where id=$pic and v_id='$v_id'");
            if (file_exists($rem[0]))
                unlink($rem[0]);
        }
    }

    $error="";
    $success = "Executed";
    $output = array(
        'success' => $success,
        'error' => $error
    );
    echo json_encode($output);

}
?>