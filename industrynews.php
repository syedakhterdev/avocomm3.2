<?php
session_start();
include_once 'header.php';
// log the activity
$sql = "INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 9, ip_address = ?";
$conn->exec( $sql, array( $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ) );

?>

<div class="pg_banner">
    <div class="container">
        <div class="avo_comm">
            <img src="/images/avo_comm_img.png" alt="" />
        </div>
        <h2>Avocado Industry News</h2>
        <p>Explore the latest Avocado Industry news here!</p>
    </div>
</div>
<div class="clear"></div>
<div class="avo_indus_news_sec">
    <div class="container">
        <div class="avo_indus_news_cnts">

            <?php
            $rowperpage = 6;

            $allcount_query = "SELECT count(*) as allcount FROM news WHERE period_id = ? AND active = 1";
            $allcount_result = $conn->query( $allcount_query, array( $_SESSION['user_period_id'] ) );
            $allcount_fetch = $conn->fetch($allcount_result);
            $allcount = $allcount_fetch['allcount'];



            // select first 5 posts
            //$query = "select * from posts order by id asc limit 0,$rowperpage ";

            $sql = "SELECT * FROM news WHERE period_id = ? AND active = 1 ORDER BY date_created DESC LIMIT 0,$rowperpage";
            $news = $conn->query( $sql, array( $_SESSION['user_period_id'] ) );
            if ( $conn->num_rows() > 0 ) {
              while ( $new = $conn->fetch( $news ) ) {
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

                echo '
                <div class="avo_indus_news_cnt" id="post_'.$new['id'].'">
                    <div class="avo_indus_news_img"><a href="' . stripslashes( $new['url'] ) . '" target="main"><img src="' . $image . '" width="300" height="178"></a></div>
                    <div class="avo_indus_title_cnt-sec">
                        <div class="avo_indus_news-title"><h4><a href="' . stripslashes( $new['url'] ) .'">' . $trim_title . '</a></h4></div>
                        <p>' . $trim_cnt . '</p>
                    </div>
                    <div class="cal_action"><a href="' . stripslashes( $new['url'] ) .'" target="main">READ MORE</a></div>
                </div>
                ';
              }
            } else {
              echo '<div class="alert alert-primary">No news have been created.</div>';
            }
            ?>
            <!--div class="avo_indus_news_cnt">
                <div class="avo_indus_news_img"><a href="javascript:void(0)"><img src='/images/industry_img2.jpg'></a></div>
                <div class="avo_indus_title_cnt-sec">
                    <div class="avo_indus_news-title"><h4><a href="javascript:void(0)">AVO INDUSTRY NEWS HEADLINE GOES HERE</a></h4></div>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                </div>
                <div class="cal_action"><a href="javascript:void(0)">Call to Action</a></div>
            </div>
            <div class="avo_indus_news_cnt">
                <div class="avo_indus_news_img"><a href="javascript:void(0)"><img src='/images/industry_img2.jpg'></a></div>
                <div class="avo_indus_title_cnt-sec">
                    <div class="avo_indus_news-title"><h4><a href="javascript:void(0)">AVO INDUSTRY NEWS HEADLINE GOES HERE</a></h4></div>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                </div>
                <div class="cal_action"><a href="javascript:void(0)">Call to Action</a></div>
            </div>
            <div class="avo_indus_news_cnt">
                <div class="avo_indus_news_img"><a href="javascript:void(0)"><img src='/images/industry_img2.jpg'></a></div>
                <div class="avo_indus_title_cnt-sec">
                    <div class="avo_indus_news-title"><h4><a href="javascript:void(0)">AVO INDUSTRY NEWS HEADLINE GOES HERE</a></h4></div>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                </div>
                <div class="cal_action"><a href="javascript:void(0)">Call to Action</a></div>
            </div>
            <div class="avo_indus_news_cnt">
                <div class="avo_indus_news_img"><a href="javascript:void(0)"><img src='/images/industry_img2.jpg'></a></div>
                <div class="avo_indus_title_cnt-sec">
                    <div class="avo_indus_news-title"><h4><a href="javascript:void(0)">AVO INDUSTRY NEWS HEADLINE GOES HERE</a></h4></div>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                </div>
                <div class="cal_action"><a href="javascript:void(0)">Call to Action</a></div>
            </div>
            <div class="avo_indus_news_cnt">
                <div class="avo_indus_news_img"><a href="javascript:void(0)"><img src='/images/industry_img2.jpg'></a></div>
                <div class="avo_indus_title_cnt-sec">
                    <div class="avo_indus_news-title"><h4><a href="javascript:void(0)">AVO INDUSTRY NEWS HEADLINE GOES HERE</a></h4></div>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                </div>
                <div class="cal_action"><a href="javascript:void(0)">Call to Action</a></div>
            </div>
            <div class="avo_indus_news_cnt">
                <div class="avo_indus_news_img"><a href="javascript:void(0)"><img src='/images/industry_img2.jpg'></a></div>
                <div class="avo_indus_title_cnt-sec">
                    <div class="avo_indus_news-title"><h4><a href="javascript:void(0)">AVO INDUSTRY NEWS HEADLINE GOES HERE</a></h4></div>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                </div>
                <div class="cal_action"><a href="javascript:void(0)">Call to Action</a></div>
            </div>
            <div class="avo_indus_news_cnt">
                <div class="avo_indus_news_img"><a href="javascript:void(0)"><img src='/images/industry_img2.jpg'></a></div>
                <div class="avo_indus_title_cnt-sec">
                    <div class="avo_indus_news-title"><h4><a href="javascript:void(0)">AVO INDUSTRY NEWS HEADLINE GOES HERE</a></h4></div>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                </div>
                <div class="cal_action"><a href="javascript:void(0)">Call to Action</a></div>
            </div>
            <div class="avo_indus_news_cnt">
                <div class="avo_indus_news_img"><a href="javascript:void(0)"><img src='/images/industry_img2.jpg'></a></div>
                <div class="avo_indus_title_cnt-sec">
                    <div class="avo_indus_news-title"><h4><a href="javascript:void(0)">AVO INDUSTRY NEWS HEADLINE GOES HERE</a></h4></div>
                    <p>Lorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                </div>
                <div class="cal_action"><a href="javascript:void(0)">Call to Action</a></div>
            </div-->
            <div class="clear"></div>
        </div>
        <div class="view_more">
            <a href="javascript:void(0)" id="more_button">view more</a>
            <img src="/images/loader.gif" id="loader_image" style="display: none; height: 50px;">
        </div>

        <input type="hidden" id="row" value="0">
        <input type="hidden" id="all" value="<?php echo $allcount; ?>">
    </div>
</div>

<div class="clear"></div>
<?php
include_once 'footer.php';
?>
<script>
    $(document).ready(function(){

        // Load more data
        $('.view_more').click(function(){

            var row = Number($('#row').val());
            var allcount = Number($('#all').val());
            var rowperpage = 6;
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
                            $(".avo_indus_news_cnt:last").after(response).show().fadeIn("slow");

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
                    $('.avo_indus_news_cnt:nth-child(6)').nextAll('.avo_indus_news_cnt').remove();

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
