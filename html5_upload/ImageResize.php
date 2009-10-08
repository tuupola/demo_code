<?php

/** Taken from http://github.com/naehrstoff/image_resize/ */

/**
 *  Helper functions, many from Drupal
 *  http://drupal.org
 */

class ImageResize {

  public function image_scale($source, $destination, $width, $height)
  {
      $info = ImageResize::image_get_info($source);

      // don't scale up
      if ($width > $info['width'] && $height > $info['height']) {
          return false;
      }

      $aspect = $info['height'] / $info['width'];
      if (!$height || ($width && $aspect < $height / $width)) {
          $width = (int)min($width, $info['width']);
          $height = (int)round($width * $aspect);
      } else {
          $height = (int)min($height, $info['height']);
          $width = (int)round($height / $aspect);
      }

      return ImageResize::image_gd_resize($source, $destination, $width, $height);
  }
  
  public function image_scale_cropped($source, $destination, $width, $height)
  {
      $info = ImageResize::image_get_info($source);

      // don't scale up
      if ($width > $info['width'] && $height > $info['height']) {
          return false;
      }
            
      /* If we are square. */
      if (!$height || !$width || $height == $width) {
          if ($info['width'] > $info['height']) {
              $source_width = $source_height = $info['height'];
              $source_y = 0;
              $source_x = round(($info['width'] - $info['height']) / 2);
          } else {
              $source_width = $source_height = $info['width'];
              $source_x = 0;
              $source_y = round(($info['height'] - $info['width']) / 2);
          }
          if ($width) {
              $height = $width;
          } else {
              $width = $height;
          }
      /* We are not square. */
      } else {

          $x_ratio = $width / $info['width'];
          $y_ratio = $height / $info['height'];

          if (($x_ratio * $info['width']) >= $width  && ($x_ratio * $info['height']) >= $height) {
              $aspect  = $width / $height;
              $x_ratio * $info['width'];
              $source_width  = $info['width'];
              $source_height = round($source_width / $aspect);
              $source_x = 0;
              $source_y = round(($info['height'] - $source_height) / 2);
          } else {
              $aspect  = $height / $width;
              $source_height = $info['height'];
              $source_width  = round($source_height / $aspect);
              $source_x = round(($info['width'] - $source_width) / 2);
              $source_y = 0;

          }
      }


      return ImageResize::image_gd_resize($source, $destination, $width, $height,  $source_x, $source_y, $source_width, $source_height);
  }
  
  
  /**
   * GD2 has to be available on the system
   *
   * @return boolean
   */
  public function gd_available() {
    if ($check = get_extension_funcs('gd')) {
      if (in_array('imagegd2', $check)) {
        // GD2 support is available.
        return true;
      }
    }
    return false;
  }
  
  
  /**
   * Get details about an image.
   *
   * @return array containing information about the image
   *      'width': image's width in pixels
   *      'height': image's height in pixels
   *      'extension': commonly used extension for the image
   *      'mime_type': image's MIME type ('image/jpeg', 'image/gif', etc.)
   */
  function image_get_info($file) {
    if (!is_file($file)) {
      return false;
    }

    $details = false;
    $data = @getimagesize($file);

    if (is_array($data)) {
      $extensions = array('1' => 'gif', '2' => 'jpg', '3' => 'png');
      $extension = array_key_exists($data[2], $extensions) ?  $extensions[$data[2]] : '';
      $details = array('width'     => $data[0],
                       'height'    => $data[1],
                       'extension' => $extension,
                       'mime_type' => $data['mime']);
    }

    return $details;
  }
  
  
  /**
   * Scale an image to the specified size using GD.
   */
  function image_gd_resize($source, $destination, $width, $height, $source_x = 0, $source_y = 0, $source_width = null, $source_height = null) {
    if (!file_exists($source)) {
      return false;
    }

    $info = ImageResize::image_get_info($source);
    if (!$info) {
      return false;
    }

    $im = ImageResize::image_gd_open($source, $info['extension']);
    if (!$im) {
      return false;
    }
    
    /* Get source dimensions from GD info is not passed as parameters. */
    $source_width  = is_null($source_width)  ? $info['width']  : $source_width;
    $source_height = is_null($source_height) ? $info['height'] : $source_height;
    
    $res = imageCreateTrueColor($width, $height);
    imageCopyResampled($res, $im, 0, 0, $source_x, $source_y, $width, $height,  $source_width, $source_height);
    $result = ImageResize::image_gd_close($res, $destination, $info['extension']);

    imageDestroy($res);
    imageDestroy($im);

    return $result;
  }
  
  
  /**
   * GD helper function to create an image resource from a file.
   */
  function image_gd_open($file, $extension) {
    $extension = str_replace('jpg', 'jpeg', $extension);
    $open_func = 'imagecreatefrom'. $extension;
    if (!function_exists($open_func)) {
      return false;
    }
    return $open_func($file);
  }
  

  /**
   * GD helper to write an image resource to a destination file.
   */
  function image_gd_close($res, $destination, $extension) {
    $extension = str_replace('jpg', 'jpeg', $extension);
    $close_func = 'image'. $extension;
    if (!function_exists($close_func)) {
      return false;
    }
    return $close_func($res, $destination);
  }
  
  function is_image($extension) {
      $images = array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG');
      return in_array($extension, $images);
  }
}