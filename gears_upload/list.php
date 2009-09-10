<?php

require_once 'ImageResize.php';

foreach (glob("./tmp/*") as $source) {
    $file = pathinfo($source);
    if (!preg_match('#-100x100\.([a-z]+)$#i', $source)) {
        $destination = $file['dirname'] . '/' . $file['filename'] . '-100x100.' . $file['extension'];
        ImageResize::image_scale_cropped($source, $destination, 100, 100);
        printf('<a href="%s"><img src="%s" widht="100" height="100" /></a>', $source, $destination);
    }
}