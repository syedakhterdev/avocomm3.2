<?php
session_start();
include_once 'header-new.php';
// log the activity
$sql = "INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 9, ip_address = ?";
$conn->exec( $sql, array( $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ) );

?>


<!-- banner sec start -->
<section class="report-banner banner">
    <div class="container">
        <h1>Avocado Industry News</h1>
        <p>Explore the latest Avocado Industry news here!</p>
        <a href="<?php echo SITE_URL?>/main.php"><img src="<?php echo SITE_URL?>/images/back-button.png" alt="back-btn"></a>
    </div>
</section>
<!-- banner sec end -->


<section class="report-wrap">
    <div class="container">
        <img class="avn-arrow-left" src="<?php echo SITE_URL?>/images/avn-arrow-left.png" alt="">
        <img class="avn-arrow-right" src="<?php echo SITE_URL?>/images/avn-arrow-right.png" alt="">
        <div class="report-inner">

            <?php
            $rowperpage = 15;

            $allcount_query = "SELECT count(*) as allcount FROM news WHERE period_id = ? AND active = 1";
            $allcount_result = $conn->query( $allcount_query, array( $_SESSION['user_period_id'] ) );
            $allcount_fetch = $conn->fetch($allcount_result);
            $allcount = $allcount_fetch['allcount'];

            $sql = "SELECT * FROM news WHERE period_id = ? AND active = 1 ORDER BY date_created DESC LIMIT 0,$rowperpage";
            $news = $conn->query( $sql, array( $_SESSION['user_period_id'] ) );

            if ( $conn->num_rows() > 0 ) {
                while ( $new = $conn->fetch( $news ) ) {
                    $image = $new['image'] ? SITE_URL.'/timThumb.php?src=/assets/news/' . $new['image'] . '&w=300&h=178&zc=1' : 'no_photo.png';

                    $title = stripslashes( $new['title'] );
                    $pos=strpos($title, ' ', 30);
                    if($pos == '') {
                        $trim_title = $title;
                    } else {
                        $trim_title = substr($title, 0, $pos ).'...';
                    }


                    ?>

                    <div class="report-card" id="post_<?php echo $new['id']?>">
                        <div class="thumbnail">
                            <img src="<?php echo $image?>" alt="<?php echo $trim_title?>">
                        </div>
                        <div class="report-card-detail">
                            <p><?php echo $trim_title?></p>
                            <a title="<?php echo $trim_title?>" href="<?php echo stripslashes( $new['url'] )?>" class="learn-more-btn learn_more">
                                <img src="<?php echo SITE_URL?>/images/learn-more-btn.png" onmouseover="this.src='<?php echo SITE_URL?>/images/learn-more-hvr-btn.png'" onmouseout="this.src='<?php echo SITE_URL?>/images/learn-more-btn.png'" alt="read-more-btn" />
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {?>
                <div class="report-card">
                    <p class="not-found">No News found.</p>
                </div>
            <?php }
            ?>
        </div>
        <input type="hidden" id="row" value="0">
        <input type="hidden" id="all" value="<?php echo $allcount; ?>">
        <?php if($allcount>$rowperpage){?>
        <a href="javascript:void(0)" class="load-more-btn page_expander">
            <img id="more_button" src="<?php echo SITE_URL?>/images/load-more-btn.png" onmouseover="this.src='<?php echo SITE_URL?>/images/load-more-hvr-btn.png'" onmouseout="this.src='<?php echo SITE_URL?>/images/load-more-btn.png'" alt="load-more-btn" />
            <img src="<?php echo SITE_URL?>/images/loader.gif" id="loader_image" style="display: none; height: 50px;">
        </a>
        <?php }?>
    </div>
</section>



<?php
include_once 'footer-new.php';
?>
<script>
    $(document).ready(function(){

        // Load more data
        $('.load-more-btn').click(function(){


            var row = Number($('#row').val());
            var allcount = Number($('#all').val());
            var rowperpage = 15;
            row = row + rowperpage;
            if(row <= allcount){
                $("#row").val(row);

                $.ajax({
                    url: 'getMoreNews.php',
                    type: 'post',
                    data: {row:row},
                    beforeSend:function(){
                        $('#loader_image').show();
                        $("#more_button").hide();
                    },
                    success: function(response){

                        // Setting little delay while displaying new content
                        setTimeout(function() {
                            // appending posts after last post with class="post"
                            $(".report-card:last").after(response).show().fadeIn("slow");

                            var rowno = row + rowperpage;

                            // checking row value is greater than allcount or not
                            if(rowno == allcount){

                                // Change the text and background
                                $('#loader_image').hide();
                                $('#more_button').hide();
                            }else{
                                $('#loader_image').hide();
                                $('#more_button').show();
                            }
                        }, 2000);

                    }
                });
            }else{

                $('#loader_image').show();
                $("#more_button").hide();
                // Setting little delay while removing contents
                setTimeout(function() {

                    // When row is greater than allcount then remove all class='post' element after 3 element
                    $('.report-card:nth-child(6)').nextAll('.report-card').remove();

                    // Reset the value of row
                    $("#row").val(0);

                    // Change the text and background
                    $('#loader_image').hide();
                    $('#more_button').show();

                }, 2000);


            }
        });

    });
</script>
<script src="<?php echo SITE_URL?>/js/report.js"></script>
