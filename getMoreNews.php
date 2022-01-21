<?php
session_start();
require( 'manager/includes/pdo.php' );
$row = $_POST['row'];
$rowperpage = 6;

// selecting posts
$html = '';
$sql = 'SELECT * FROM news WHERE period_id = ? AND active = 1 ORDER BY date_created DESC LIMIT '.$row.','.$rowperpage;
$news = $conn->query( $sql, array( $_SESSION['user_period_id'] ) );
if ( $conn->num_rows() > 0 ) {
    while ($new = $conn->fetch($news)) {
        $image = $new['image'] ? '/timThumb.php?src=/assets/news/' . $new['image'] . '&w=300&h=178&zc=1' : 'no_photo.png';

        $title = stripslashes( $new['title'] );
        $pos=strpos($title, ' ', 30);
        if($pos == '') {
            $trim_title = $title;
        } else {
            $trim_title = substr($title, 0, $pos ).'...';
        }

        $content = stripslashes( $new['description'] );
        $pos=strpos($content, ' ', 100);
        if($pos == '') {
            $trim_cnt = $content;
        } else {
            $trim_cnt = substr($content, 0, $pos ).'...';
        }
	$html.='<div class="avo_indus_news_cnt" id="post_'.$new['id'].'">
                    <div class="avo_indus_news_img"><a href="' . stripslashes( $new['url'] ) . '" target="main"><img src="' . $image . '" width="300" height="178"></a></div>
                    <div class="avo_indus_title_cnt-sec">
                        <div class="avo_indus_news-title"><h4><a href="' . stripslashes( $new['url'] ) .'">' . $trim_title . '</a></h4></div>
                        <p>' . $trim_cnt . '</p>
                    </div>
                    <div class="cal_action"><a href="' . stripslashes( $new['url'] ) .'" target="main">READ MORE</a></div>
                </div>';

    }//end of while
}
echo $html;

?>