<?php
session_start();
require( 'includes/pdo.php' );
require( 'includes/check_login.php' );
$url = isset( $_GET['url'] ) ? $_GET['url'] : '';
header('Content-Type: application/json');

if ( $url ) {

  $str = curl_function($url,'metatag');

  if ( $str ) {
    $dir = $_GET['dir'];
    libxml_use_internal_errors(true); // Yeah if you are so worried about using @ with warnings
    $doc = new DomDocument();
    $doc->loadHTML( $str );
    $xpath = new DOMXPath($doc);
    $query = '//*/meta[starts-with(@property, \'og:\')]';
    $metas = $xpath->query($query);
    $rmetas = array();
    foreach ( $metas as $meta ) {
      $property = str_replace( ':', '_', $meta->getAttribute( 'property' ) );
      $content = $meta->getAttribute( 'content' );

     if ( $property == 'og_image' ) {

            $img = file_get_contents($content);  // get image data from $url

            $url_info = pathinfo($content);
            if($url_info['extension']){
              $img_name   =   'news_'.time().'.'.$url_info['extension'];
              copy( $content, '../assets/' . $dir . '/' . $img_name );
              $content = $img_name;

            }else{

              if(curl_function($content.'.jpg','image')){
                $img_name   =   'news_'.time().'.jpg';
                 copy( $content.'.jpg', '../assets/' . $dir . '/' . $img_name );
                 $content = $img_name;

              }

            }
      }
      $rmetas[$property] = $content;
    }
    echo json_encode( $rmetas );
  }
} else {
  echo json_encode( array( 'err' => 'Please provide a valid URL' ) );
}

function curl_function($url,$param) {

    $record =   false;
    $curl = curl_init($url);
    if($param=='metatag'){
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_REFERER, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $record = curl_exec($curl);
    }else{
         curl_setopt($curl, CURLOPT_NOBODY, true);
         curl_exec($curl);
         $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         if($code==200){
            $record = true;
         }else{
            $record = false;
         }
    }
    curl_close($curl);
    return $record;
}
?>