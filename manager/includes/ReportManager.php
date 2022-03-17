<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Reports' );
@define( 'ENTITY', 'Report' );

class ReportManager {
	function __construct( $db ) {
		$this->tableName = 'reports';
		$this->idField = 'id';
		$this->db = $db;
		$this->error = '';
	}

	function getByID ( $period_id, $id ) {
		// clean up incoming variables for security reasons
		$period_id = (int)$period_id;
		$id = (int)$id;

		if ( $period_id && $id ) {
			$sql = "SELECT * FROM $this->tableName WHERE $this->idField = ? AND period_id = ? LIMIT 1";
			$stmt = $this->db->query( $sql, array( $id, $period_id ) );

			if ( $stmt ) {
				// pull 1 record from the database
				if ( !( $row = $stmt->fetch() ) ) {
					return ERROR;
				} else {
					// clean up the output for the page calling this, no slashes will be included
					// Remove all slashes from outgoing text
					$row['title'] = $this->db->parseOutputString( $row['title'], false );
					$row['description'] = $this->db->parseOutputString( $row['description'], false );
					return $row;
				}
			} else {
				$this->error = 'Could not retrieve the specified record.';
				return ERROR;
			}
		} else {
			return false; // no valid number id was passed in
		}
	}

    function add( $period_id, $title, $description, $doc, $sort, $active ) {
        // clean up incoming variables for security reasons
        $period_id = (int)$period_id;

        if ( !$title ) $this->error = 'Please enter a valid title.';
        if ( $this->error != "" ) return ERROR;

        $title = $this->db->prepString( $title, 85 );
        $description = $this->db->prepString( $description, 255 );
        $doc = $this->db->prepString( $doc, 65 );
        $sort = (int)$sort;
        $active = ( (int)$active ) ? "1" : "0";

        if($_FILES[$doc]['size']>0){

            $image_array =   array('gif','png','jpg','jpeg');
            $document_name   =   '';
            $thumb_image  =   '';
            $temp = explode(".", $_FILES[$doc]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);

            $ext = pathinfo($newfilename, PATHINFO_EXTENSION);
            $name = pathinfo($newfilename, PATHINFO_FILENAME);

            $target_file   =    $_SERVER['DOCUMENT_ROOT'] . "/avocomm3.2/assets/report_docs/".$newfilename;
            if (move_uploaded_file($_FILES[$doc]["tmp_name"], $target_file)) {
                $document_name  =   $newfilename;
                if(in_array($ext,$image_array)){

                    $this->db->genImageThumbnail($_SERVER['DOCUMENT_ROOT'] . "/avocomm3.2/assets/report_docs/".$document_name,$_SERVER['DOCUMENT_ROOT'] . "/avocomm3.2/assets/reports/thumb_".$name.'.'.$ext);
                    $thumb_image   =   "thumb_".$name.'.'.$ext;
                }else if($ext=='pdf'){

                    $this->db->genPdfThumbnail($_SERVER['DOCUMENT_ROOT'] . "/avocomm3.2/assets/report_docs/".$document_name,$_SERVER['DOCUMENT_ROOT'] . "/avocomm3.2/assets/reports/thumb_".$name.'.jpg');
                    $thumb_image   =   "thumb_".$name.'.jpg';
                }else{
                    $this->db->genImageThumbnail($_SERVER['DOCUMENT_ROOT'] . "/avocomm3.2/assets/reports/doc_default.jpg",$_SERVER['DOCUMENT_ROOT'] . "/avocomm3.2/assets/reports/thumb_".$name.'.jpg');
                    $thumb_image   =  "thumb_".$name.'.jpg';
                }

            }

        }


        $sql = "INSERT INTO $this->tableName SET
			date_created = NOW(), period_id = ?, title = ?, description = ?, image = ?, doc = ?, sort = ?, active = ?
			;";
        $new_id = $this->db->exec( $sql, array( $period_id, $title, $description, $thumb_image, $document_name, $sort, $active ), true );
        if ( $new_id ) {
            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $report_sql = 'SELECT title FROM reports where id=? LIMIT 1';
            $report_list = $this->db->query($report_sql, array($new_id));
            $report_row    =   $report_list->fetch();
            $report_title   =  $report_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 22, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $report_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


            // return the id for the newly created entry
            return $new_id;
        } else {
            $this->error = $this->db->error();
            return ERROR;
        }
    }

	function update( $id, $period_id, $title, $description, $image, $doc, $sort, $active ) {
		// clean up incoming variables for security reasons
		$period_id = (int)$period_id;
		$id = (int)$id;

		if ( !$title ) $this->error = 'Please enter a valid title.';
		if ( $this->error != "" ) return ERROR;

		$title = $this->db->prepString( $title, 85 );
		$description = $this->db->prepString( $description, 255 );
		$doc = $this->db->prepString( $doc, 65 );
		$sort = (int)$sort;
		$active = ( (int)$active ) ? "1" : "0";

		if ( $period_id && $id ) {
			$sql = "UPDATE $this->tableName SET
				title = ?, description = ?, doc = ?, sort = ?, active = ?";

			if ( $image ) $sql .= ", image = ?";

			$sql .= " WHERE $this->idField = ? AND period_id = ? LIMIT 1;";

			$params = array( $title, $description, $doc, $sort, $active );
			if ( $image ) $params[] = $image;
			$params[] = $id;
			$params[] = $period_id;

			if ( $this->db->exec( $sql, $params ) ) {

                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $report_sql = 'SELECT title FROM reports where id=? LIMIT 1';
                $report_list = $this->db->query($report_sql, array($id));
                $report_row    =   $report_list->fetch();
                $report_title   =  $report_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 23, reference = ?, ip_address = ?";
                $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $report_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );

                // return true if the item was updated successfully
				return SUCCESS;
			} else {
				$this->error = $this->db->error();
				return ERROR;
			}
		} else {
			return false;
		}
	}

	function delete ( $period_id, $id ) {
		// convert the $id to make sure a number is being passed in and not a string
		$period_id = (int)$period_id;
		$id = (int)$id;

		// if the $id was passed in then go ahead and delete the item
		if ( $period_id && $id ) {

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $report_sql = 'SELECT title FROM reports where id=? LIMIT 1';
            $report_list = $this->db->query($report_sql, array($id));
            $report_row    =   $report_list->fetch();
            $report_title   =  $report_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 24, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $report_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


            $sql = "DELETE FROM $this->tableName WHERE $this->idField = ? AND period_id = ? LIMIT 1;";

			if ( $this->db->exec( $sql, array( $id, $period_id ) ) ) {
				// return true if the item was deleted successfully
				return SUCCESS;
			} else {
				// return false if there was an error and the item was not deleted
				$this->error = $this->db->error();
				return ERROR;
			}
		} else {
			return false;
		}
	}

	function getReportCount( $period_id ) {
		$sql = "SELECT COUNT(*) AS cnt FROM $this->tableName WHERE $this->idField > 0 AND period_id = ?;";
		if ( $stmt = $this->db->query( $sql, array( $period_id ) ) ) {
			// pull 1 record from the database
			if ( !( $row = $stmt->fetch() ) ) {
				$this->error = 'An error occurred while getting the item count.';
				return ERROR;
			} else {
				return $row['cnt'];
			}
		} else {
			$this->error = 'Could not fetch a count from the database, please contact your administrator.';
			return ERROR;
		}
	}

	function getReports( $period_id, $offset, $rowsPerPage ) {
		$sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id > 0 AND period_id = ? ORDER BY a.title LIMIT $offset, $rowsPerPage;";

		// pull records from the database
		if ( $result = $this->db->query( $sql, array( $period_id ) ) ) {
			// return the resulting records
			return $result;
		} else {
			// return false if there was an error and the item was not deleted
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function error() {
		return $this->error;
	}

	function removeImage( $id ) {
		$id = (int)$id;

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET image = NULL WHERE $this->idField = ?";

			if ( $stmt = $this->db->exec( $sql, array( $id ) ) ) {
				return SUCCESS;
			} else {
				return ERROR;
			}
		}

		return true;
	}

	function deleteImage( $id ) {
		$id = (int)$id;

		if ( $id ) {
			$sql = "SELECT image FROM $this->tableName WHERE $this->idField = ?";

			if ( $stmt = $this->db->exec( $sql, array( $id ) ) ) {
				if ( $row = $stmt->fetch() ) {
					if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/reports/" . $row['image'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/reports/" . $row['image'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/reports/) directory.';
							return ERROR;
						}
				} else {
					$this->error = 'Could not fetch record to delete image';
					return ERROR;
				}
			} else {
				$this->error = 'Could not select the specified record to delete image';
				return ERROR;
			}
		} else {
			$this->error = 'Please provide a valid ID';
			return ERROR;
		}
	}

	function removeDocument( $id ) {
		$id = (int)$id;

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET doc = NULL WHERE $this->idField = ?";

			if ( $stmt = $this->db->exec( $sql, array( $id ) ) ) {
				return SUCCESS;
			} else {
				return ERROR;
			}
		}

		return true;
	}

	function deleteDocument( $id ) {
		$id = (int)$id;

		if ( $id ) {
			$sql = "SELECT doc FROM $this->tableName WHERE $this->idField = ?";

			if ( $stmt = $this->db->exec( $sql, array( $id ) ) ) {
				if ( $row = $stmt->fetch() ) {
					if ( $row['document'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/report_docs/" . $row['doc'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/report_docs/" . $row['doc'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/report_docs/) directory.';
							return ERROR;
						}
				} else {
					$this->error = 'Could not fetch record to delete document';
					return ERROR;
				}
			} else {
				$this->error = 'Could not select the specified record to delete document';
				return ERROR;
			}
		} else {
			$this->error = 'Please provide a valid ID';
			return ERROR;
		}
	}

	function makeThumbnail( $id, $updir, $img, $destdir ) {
		$id = (int)$id;
    $thumbnail_width = 250;
    $thumbnail_height = 250;
    $thumb_beforeword = "";
    $arr_image_details = getimagesize( "$updir" . "$img" ); // pass id to thumb name
    $original_width = $arr_image_details[0];
    $original_height = $arr_image_details[1];

    if ( $original_width > $original_height ) {
      $new_width = $thumbnail_width;
      $new_height = intval( $original_height * $new_width / $original_width );
    } else {
      $new_height = $thumbnail_height;
      $new_width = intval( $original_width * $new_height / $original_height );
    }

    $dest_x = intval( ( $thumbnail_width - $new_width ) / 2 );
    $dest_y = intval( ( $thumbnail_height - $new_height ) / 2 );

    if ( $arr_image_details[2] == IMAGETYPE_GIF ) {
      $imgt = "ImageGIF";
      $imgcreatefrom = "ImageCreateFromGIF";
    }

    if ( $arr_image_details[2] == IMAGETYPE_JPEG ) {
      $imgt = "ImageJPEG";
      $imgcreatefrom = "ImageCreateFromJPEG";
    }

    if ( $arr_image_details[2] == IMAGETYPE_PNG ) {
      $imgt = "ImagePNG";
      $imgcreatefrom = "ImageCreateFromPNG";
    }

    if ( $imgt ) {
      $old_image = $imgcreatefrom( "$updir" . "$img" );
      $new_image = imagecreatetruecolor( $thumbnail_width, $thumbnail_height );
      imagecopyresized( $new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height );
      $imgt( $new_image, "$destdir" . "$img" );
    }
	}

	function getRangeDropdown( $start, $end, $checked = 0 ) {
		$dropdown = '';
		for ( $i = $start; $i <= $end; $i++ ) {
			if ( $i == $checked )
				$dropdown .= "\t<option value=\"$i\" SELECTED>$i</option>";
			else
				$dropdown .= "\t<option value=\"$i\">$i</option>";
		}

		return $dropdown;
	}
}
?>