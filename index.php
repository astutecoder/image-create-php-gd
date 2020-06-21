<?php

function generate_og_image($file_path) {
    $file = file_exists($file_path) ? $file_path : 'img/placeholder.jpg';
    $file_name = pathinfo($file, PATHINFO_FILENAME);
    $ext = pathinfo($file, PATHINFO_EXTENSION);

    $destination_uri = "img/{$file_name}-og-image.{$ext}";
    if(file_exists($destination_uri)) {
        echo "file exists <br>";
        echo "<img src='{$destination_uri}'/>";
        return;
    }

    // variable methods
    $vfn_imageCreateFrom = strtolower($ext) === 'jpg' ? 'imagecreatefromjpeg' : 'imagecreatefrom' . $ext;
    $vfn_imageExt = strtolower($ext) === 'jpg' ? 'imagejpeg' : 'image' . $ext;

    // create resource from provided image
    $original_img = $vfn_imageCreateFrom($file);

    list($original_w, $original_h) = getimagesize($file);

    // set background color of provided image
    $opaque_img = imagecreatetruecolor($original_w, $original_h);
    $grey = imagecolorallocate($opaque_img, 240, 240, 240);
    imagefill($opaque_img,0,0,$grey);

    imagecopy($opaque_img, $original_img,0,0,0,0, $original_w, $original_h);

    // variables for image dimension
    $dst_w = 180;
    $ratio = $dst_w / $original_w;
    $dst_h = $original_h * $ratio;

    $dst_x = 100 - $dst_w/2;
    $dst_y = 100 - $dst_h/2;

    // create image resource for 200x200
    $new_img = imagecreate(200, 200);
    imagecolorallocate($new_img, 240, 240, 240);
    imagecopyresampled($new_img, $opaque_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $original_w, $original_h);

    // create/write image file from newly created image resource
    $destination_file = fopen($destination_uri, 'wb');

    $vfn_imageExt($new_img, $destination_file);

    imagedestroy($original_img);
    imagedestroy($opaque_img);
    imagedestroy($new_img);

    echo "<img src='{$destination_uri}'/>";
}

generate_og_image('img/hp.png');
