<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Vendor Documentations' );
@define( 'ENTITY', 'Vendor Documentation' );

class VendorDocumentationManager {
	function __construct( $db ) {
		$this->tableName = 'vendor_documentation';
		$this->idField = 'id';
		$this->db = $db;
		$this->error = '';
	}

	function getByID ( $id, $period_id ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;
		$period_id = (int)$period_id;

		if ( $id && $period_id ) {
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

	/*function add( $vendor_id, $period_id, $title, $description, $image, $document, $documen_type_id, $active ) {
		// clean up incoming variables for security reasons

		if ( (int)$vendor_id < 0 || $vendor_id == '' ) $this->error = 'Please enter a valid Vendor.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $description == '' ) $this->error = 'Please enter a valid Description.';
		if ( $this->error != "" ) return ERROR;

		$vendor_id = (int)$vendor_id;
		$period_id = (int)$period_id;
		$title = $this->db->prepString( $title, 65 );
		$description = $this->db->prepString( $description, 255 );
		$documen_type_id = (int)$documen_type_id;
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			date_created = CURDATE(), vendor_id = ?, period_id = ?, title = ?, description = ?, image = ?, document = ?, documen_type_id = ?, active = ?
			;";
		$new_id = $this->db->exec( $sql, array( $vendor_id, $period_id, $title, $description, $image, $document, $documen_type_id, $active ), true );
		if ( $new_id ) {
			// return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}*/

    function add( $vendor_id, $period_id, $title, $description, $document, $documen_type_id, $active ) {

        if ( (int)$vendor_id < 0 || $vendor_id == '' ) $this->error = 'Please enter a valid Vendor.';
        if ( $title == '' ) $this->error = 'Please enter a valid Title.';
        if ( $description == '' ) $this->error = 'Please enter a valid Description.';
        if ( $this->error != "" ) return ERROR;

        $vendor_id = (int)$vendor_id;
        $period_id = (int)$period_id;
        $title = $this->db->prepString( $title, 65 );
        $description = $this->db->prepString( $description, 255 );
        $documen_type_id = (int)$documen_type_id;
        $active = ( (int)$active ) ? "1" : "0";
        $document_name   =   '';
        $thumb_image  =   '';

        $temp = explode(".", $_FILES[$document]["name"]);
        $newfilename = round(microtime(true)) . '.' . end($temp);

        $ext = pathinfo($newfilename, PATHINFO_EXTENSION);
        $name = pathinfo($newfilename, PATHINFO_FILENAME);

        $target_file   =    dirname(__FILE__,3) . "/assets/documentation_docs/".$newfilename;
        if (move_uploaded_file($_FILES[$document]["tmp_name"], $target_file)) {
            $document_name   =   $newfilename;
            if ( $documen_type_id == 1 ) {

                $this->db->genImageThumbnail(dirname(__FILE__,3) . "/assets/documentation_docs/".$document_name,dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.'.$ext);
                $thumb_image   =   "thumb_".$name.'.'.$ext;

            } else if ( $documen_type_id == 2 ) {

                if($ext=='pdf'){
                    $this->db->genPdfThumbnail(dirname(__FILE__,3) . "/assets/documentation_docs/".$document_name,dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.jpg');
                    $thumb_image   =   "thumb_".$name.'.jpg';
                }else{
                    $this->db->genImageThumbnail(dirname(__FILE__,3) . "/assets/documentation_images/doc_default.jpg",dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.jpg');
                    $thumb_image   =  "thumb_".$name.'.jpg';
                }
            }else if ( $documen_type_id == 3 ) {
                $this->db->genImageThumbnail(dirname(__FILE__,3) . "/assets/documentation_images/video_default.png",dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.jpg');
                $thumb_image   =  "thumb_".$name.'.jpg';
            }else if ( $documen_type_id == 4 ) {
                $this->db->genImageThumbnail(dirname(__FILE__,3) . "/assets/documentation_images/audio_default.jpg",dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.jpg');
                $thumb_image   =  "thumb_".$name.'.jpg';
            }

        }

        $sql = "INSERT INTO $this->tableName SET
			date_created = CURDATE(), vendor_id = ?, period_id = ?, title = ?, description = ?, image = ?, document = ?, documen_type_id = ?, active = ?
			;";
        $new_id = $this->db->exec( $sql, array( $vendor_id, $period_id, $title, $description, $thumb_image, $document_name, $documen_type_id, $active ), true );
        if ( $new_id ) {
            // return the id for the newly created entry
            return $new_id;
        } else {
            $this->error = $this->db->error();
            return ERROR;
        }
    }

	/*function update( $id, $vendor_id, $period_id, $title, $description, $image, $document, $documen_type_id, $active ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;
		$period_id = (int)$period_id;

		if ( (int)$vendor_id < 0 || $vendor_id == '' ) $this->error = 'Please enter a valid Vendor.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $description == '' ) $this->error = 'Please enter a valid Description.';
		if ( $this->error != "" ) return ERROR;

		$vendor_id = (int)$vendor_id;
		$title = $this->db->prepString( $title, 65 );
		$description = $this->db->prepString( $description, 255 );
		$documen_type_id = (int)$documen_type_id;
		$active = ( (int)$active ) ? "1" : "0";

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				vendor_id = ?, title = ?, description = ?, documen_type_id = ?, active = ?";

			if ( $image ) $sql .= ", image = ?";if ( $document ) $sql .= ", document = ?";

			$sql .= " WHERE $this->idField = ? AND period_id = ? LIMIT 1;";

			$params = array( $vendor_id, $title, $description, $documen_type_id, $active );
			if ( $image ) $params[] = $image;if ( $document ) $params[] = $document;
			$params[] = $id;
			$params[] = $period_id;

			if ( $this->db->exec( $sql, $params ) ) {
				// return true if the item was updated successfully
				return SUCCESS;
			} else {
				$this->error = $this->db->error();
				return ERROR;
			}
		} else {
			return false;
		}
	}*/

    function update( $id, $vendor_id, $period_id, $title, $description, $document, $documen_type_id, $active ) {
        // clean up incoming variables for security reasons
        $id = (int)$id;
        $period_id = (int)$period_id;
        if ( (int)$vendor_id < 0 || $vendor_id == '' ) $this->error = 'Please enter a valid Vendor.';
        if ( $title == '' ) $this->error = 'Please enter a valid Title.';
        if ( $description == '' ) $this->error = 'Please enter a valid Description.';
        if ( $this->error != "" ) return ERROR;

        $vendor_id = (int)$vendor_id;
        $title = $this->db->prepString( $title, 65 );
        $description = $this->db->prepString( $description, 255 );
        $documen_type_id = (int)$documen_type_id;
        $active = ( (int)$active ) ? "1" : "0";

        if($_FILES[$document]['size']>0){
            $document_name   =   '';
            $thumb_image  =   '';

            $temp = explode(".", $_FILES[$document]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);

            $ext = pathinfo($newfilename, PATHINFO_EXTENSION);
            $name = pathinfo($newfilename, PATHINFO_FILENAME);


            $target_file   =    dirname(__FILE__,3) . "/assets/documentation_docs/".$newfilename;
            if (move_uploaded_file($_FILES[$document]["tmp_name"], $target_file)) {
                $document_name   =   $newfilename;
                if ( $documen_type_id == 1 ) {

                    $this->db->genImageThumbnail(dirname(__FILE__,3) . "/assets/documentation_docs/".$document_name,dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.'.$ext);
                    $thumb_image   =   "thumb_".$name.'.'.$ext;

                } else if ( $documen_type_id == 2 ) {

                    if($ext=='pdf'){
                        $this->db->genPdfThumbnail(dirname(__FILE__,3) . "/assets/documentation_docs/".$document_name,dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.jpg');
                        $thumb_image   =   "thumb_".$name.'.jpg';
                    }else{
                        $this->db->genImageThumbnail(dirname(__FILE__,3) . "/assets/documentation_images/doc_default.jpg",dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.jpg');
                        $thumb_image   =  "thumb_".$name.'.jpg';
                    }
                }else if ( $documen_type_id == 3 ) {
                    $this->db->genImageThumbnail(dirname(__FILE__,3) . "/assets/documentation_images/video_default.png",dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.jpg');
                    $thumb_image   =  "thumb_".$name.'.jpg';
                }else if ( (int)$_POST['documen_type_id'] == 4 ) {
                    $this->db->genImageThumbnail(dirname(__FILE__,3) . "/assets/documentation_images/audio_default.jpg",dirname(__FILE__,3) . "/assets/documentation_images/thumb_".$name.'.jpg');
                    $thumb_image   =  "thumb_".$name.'.jpg';
                }

            }

        }

        if ( $id ) {
            $sql = "UPDATE $this->tableName SET
				vendor_id = ?, title = ?, description = ?, documen_type_id = ?, active = ?";

            if ( $thumb_image ) $sql .= ", image = ?";if ( $document_name ) $sql .= ", document = ?";

            $sql .= " WHERE $this->idField = ? AND period_id = ? LIMIT 1;";

            $params = array( $vendor_id, $title, $description, $documen_type_id, $active );
            if ( $thumb_image ) $params[] = $thumb_image;if ( $document_name ) $params[] = $document_name;
            $params[] = $id;
            $params[] = $period_id;

            if ( $this->db->exec( $sql, $params ) ) {
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

	function delete ( $id ) {
		// convert the $id to make sure a number is being passed in and not a string
		$id = (int)$id;

		// if the $id was passed in then go ahead and delete the item
		if ( $id ) {
			$sql = "DELETE FROM $this->tableName WHERE $this->idField = ? LIMIT 1;";

			if ( $this->db->exec( $sql, array( $id ) ) ) {
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

	function getVendor_DocumentationsCount() {
		$sql = "SELECT COUNT(*) AS cnt FROM $this->tableName WHERE $this->idField > 0;";
		if ( $stmt = $this->db->query( $sql, array() ) ) {
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

	function getVendor_Documentations( $offset, $rowsPerPage ) {
		$sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id > 0  ORDER BY a.title LIMIT $offset, $rowsPerPage;";

		// pull records from the database
		if ( $result = $this->db->query( $sql, array() ) ) {
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

			if ( $stmt = $this->db->query( $sql, array( $id ) ) ) {
				if ( $row = $stmt->fetch() ) {
					if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_images/" . $row['image'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_images/" . $row['image'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/documentation_images/) directory.';
							return ERROR;
						}
				} else {
					$this->error = 'Could not fetch record to delete Image';
					return ERROR;
				}
			} else {
				$this->error = 'Could not select the specified record to delete Image';
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
			$sql = "UPDATE $this->tableName SET document = NULL WHERE $this->idField = ?";

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
			$sql = "SELECT document FROM $this->tableName WHERE $this->idField = ?";

			if ( $stmt = $this->db->query( $sql, array( $id ) ) ) {
				if ( $row = $stmt->fetch() ) {
					if ( $row['document'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_docs/" . $row['document'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_docs/" . $row['document'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/documentation_docs/) directory.';
							return ERROR;
						}
				} else {
					$this->error = 'Could not fetch record to delete Document';
					return ERROR;
				}
			} else {
				$this->error = 'Could not select the specified record to delete Document';
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

	function getDocument_types_Document_typeDropdown( $checked = false ) {
		$sql = "SELECT id, document_type FROM document_types ORDER BY document_type;";
		$stmt = $this->db->prepare( $sql );
		if ( $stmt->execute() ) {
			return $this->db->buildDropdown( $stmt, $checked );
		} else {
			return ERROR;
		}
	}

}
?>