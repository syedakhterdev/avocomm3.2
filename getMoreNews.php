<?php
include('config.php');
require( 'manager/includes/pdo.php' );
$row = $_POST['row'];
$rowperpage = 15;

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
	$html.='<div class="report-card" id="post_post_'.$new['id'].'">
                        <div class="thumbnail">
                            <img src="' . $image . '" alt="' . $trim_title . '">
                        </div>
                        <div class="report-card-detail">
                            <p>' . $trim_title . '</p>
                            <a title="' . $trim_title . '" href="' . stripslashes( $new['url'] ) . '" class="learn-more-btn">
                                <img src="'.SITE_URL.'/images/learn-more-btn.png" onmouseover="this.src=\''.SITE_URL.'/images/learn-more-hvr-btn.png\'" onmouseout="this.src=\''.SITE_URL.'/images/learn-more-btn.png\'" alt="read-more-btn" />
                            </a>
                        </div>
                </div>';

    }//end of while
}
echo $html;

?>