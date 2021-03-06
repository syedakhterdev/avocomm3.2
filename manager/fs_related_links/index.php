<?php
session_start();

require( '../includes/pdo.php' );
require( '../includes/check_login.php' );
require( '../includes/ShopperRelatedLinkManager.php' );
$ShopperRelatedLink = new ShopperRelatedLinkManager($conn);

$msg = '';
$error = '';
$criteria = '';
$page = ( empty($_GET['page']) ) ? 1 : (int) $_GET['page'];

$del_id = ( isset($_POST['del']) ) ? (int) $_POST['del'] : '';
$update = ( isset($_GET['update']) ) ? (int) $_GET['update'] : '';
$add = ( isset($_GET['add']) ) ? (int) $_GET['add'] : '';

if ($del_id) {
    // get the delete token that was set previously
    $token = $_SESSION['del_token'];
    unset($_SESSION['del_token']);

    if ($token != '' && $_POST['token'] == $token) {
        if (!$ShopperRelatedLink->delete($del_id)) {
            $msg = "Sorry, an error has occurred, please contact your administrator!<br>Error:" . $conn->error();
        } else {
            $msg = "The specified record was deleted successfully!";
        }
    }
} else if ($update) {
    $msg = "The specified record was updated successfully!";
} else if ($add) {
    $msg = "Your item was added successfully!";
}

// create a token for secure deletion (from this page only and not remote)
$_SESSION['del_token'] = md5(uniqid());
session_write_close();
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo SECTION_TITLE; ?></title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="/manager/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">

        <link href="/manager/css/imagine.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
        <style>

        </style>
    </head>

    <body>

        <?php include( '../includes/header.php' ); ?>

        <div class="container-fluid" id="main">
            <div class="row row-offcanvas row-offcanvas-left">

                <?php include( '../includes/nav.php' ); ?>

                <div class="col main pt-5 mt-3">
                    <div class="row mgr_heading">
                        <div class="col-lg-10">
                            <h3 class="float-left">Foodservice Related Links</h3>
                        </div>
                    </div>

                    <ol class="breadcrumb bc-3">
                        <li><a href="/manager/menu.php">Dashboard</a></li>
                        <li>&nbsp;/&nbsp;</li>
                        <li><strong>Foodservice Related Links</strong></li>
                    </ol>

                    <div class="row my-4 mgr_body foodservice programs related_links">
                        <div class="col-lg-10 col-md-8">

                            <?php if ($msg) echo '<div class="alert alert-success" role="alert">' . $msg . '</div>'; ?>
                            <?php if ($error) echo '<div class="alert alert-error" role="alert">' . $error . '</div>'; ?>

                            <?php
                            //$sql = "SELECT COUNT(*) FROM shopper_related_links WHERE id > 0;";
                            $rowsPerPage = 15;
                            $total_count = $ShopperRelatedLink->getShopper_Related_LinksCount();
                            $conn->getPaging($total_count, $page, $rowsPerPage);
                            ?>

                            <div class="table-responsive">

                                <div class="add_button">
                                    <a href="add.php" data-fancybox data-type="iframe"><button type="button" class="btn btn-primary btn-sm float-right">Add New</button></a>
                                </div>

                                <table class="table table-striped table-sm">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <!--<th style="width: 50px;">&nbsp;</th>-->
                                            <th style="width: 20%;text-align: center;">Shopper Program Id</th>
                                            <th>Title</th>
                                            <th class="text-center" style="width: 10%;">Sort</th>

                                            <th class="text-center" colspan="2" style="width: 20%;">Action</th>
                                            <!--<th style="width: 16px;"></th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result = $ShopperRelatedLink->getShopper_Related_Links($conn->offset, $rowsPerPage);

                                        if ($conn->num_rows() > 0) {
                                            while ($row = $conn->fetch($result)) {
                                                echo '
                                                        <tr>
                                                            <td align="center">
                                                                ' . $conn->parseOutputString($row['shopper_program_id']) . '
                                                            </td>
                                                            <td>
                                                                ' . ( ( $row['image'] ) ? "<img src=\"/manager/timThumb.php?src=/assets/shopper_related_links/" . $row['image'] . "&h=60&zc=1\" class=\"listing_image\">" : '' ) . '<a href="edit.php?id=' . $row['id'] . '">' . $conn->parseOutputString($row['title']) . '</a>
                                                            </td>
                                                            <td style="text-align: center;">' . $row['sort'] . '</td>
                                                            <td align="center" class="listing_icons">
                                                                <a href="edit.php?id=' . $row['id'] . '" title="Edit" class="action_btn edit">EDIT</a>
                                                            </td>
                                                            <td align="center" class="listing_icons">
                                                                <form action="index.php?page=' . $page . '&criteria=' . $criteria . '" method="POST" onSubmit="return confirm(' . "'Are you sure you want to delete this item?'" . ');">
                                                                    <input type="hidden" name="del" value="' . $row['id'] . '">
                                                                    <input type="hidden" name="token" value="' . $_SESSION['del_token'] . '">
                                                                    <input type="submit" class="action_btn delete" value="DELETE">
                                                                </form>
                                                            </td>
                                                        </tr>';
                                            }
                                        } else {
                                            echo "<td colspan=\"6\">No shopper related links found.</td>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php echo $conn->paging(); ?>

                        </div>
                    </div>
                    <!--/row-->

                    <footer class="container-fluid">
                        <p class="text-right small">??2019 All rights reserved.</p>
                    </footer>

                </div>
                <!--/main col-->

            </div>

        </div>
        <!--/.container-->
        <!-- Core Scripts - Include with every page -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script src="/manager/js/imagine.js"></script>
        <script>
            $(document).ready(function () {
                window.setTimeout(function () {
                    $(".alert").fadeTo(500, 0).slideUp(500, function () {
                        $(this).remove();
                    });
                }, 2000);
            });
        </script>
    </body>
</html>
<?php $conn->close(); ?>