<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Shopper Documentations' );
@define( 'ENTITY', 'Shopper Documentation' );

class ShopperDocumentationManager {
	function __construct( $db ) {
		$this->tableName = 'shopper_documentation';
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

	function add( $shopper_program_id, $period_id, $title, $description, $image, $document, $document_type_id, $active ) {
		// clean up incoming variables for security reasons

		if ( (int)$shopper_program_id < 0 || $shopper_program_id == '' ) $this->error = 'Please enter a valid Shopper Program.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $description == '' ) $this->error = 'Please enter a valid Description.';
		if ( $this->error != "" ) return ERROR;

		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$title = $this->db->prepString( $title, 65 );
		$description = $this->db->prepString( $description, 255 );
		$document_type_id = (int)$document_type_id;
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			date_created = CURDATE(), shopper_program_id = ?, period_id = ?, title = ?, description = ?, image = ?, document = ?, document_type_id = ?, active = ?
			;";
		$new_id = $this->db->exec( $sql, array( $shopper_program_id, $period_id, $title, $description, $image, $document, $document_type_id, $active ), true );
		if ( $new_id ) {
			// return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $shopper_program_id, $period_id, $title, $description, $image, $document, $document_type_id, $active ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( (int)$shopper_program_id < 0 || $shopper_program_id == '' ) $this->error = 'Please enter a valid Shopper Program.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $description == '' ) $this->error = 'Please enter a valid Description.';
		if ( $this->error != "" ) return ERROR;

		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$title = $this->db->prepString( $title, 65 );
		$description = $this->db->prepString( $description, 255 );
		$document_type_id = (int)$document_type_id;
		$active = ( (int)$active ) ? "1" : "0";

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				title = ?, description = ?, document_type_id = ?, active = ?";

			if ( $image ) $sql .= ", image = ?";
			if ( $document ) $sql .= ", document = ?";

			$sql .= " WHERE $this->idField = ? AND shopper_program_id = ? AND period_id = ? LIMIT 1;";

			$params = array( $title, $description, $document_type_id, $active );
			if ( $image ) $params[] = $image;
			if ( $document ) $params[] = $document;
			$params[] = $id;
			$params[] = $shopper_program_id;
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

	function delete ( $id, $shopper_program_id, $period_id ) {
		// convert the $id to make sure a number is being passed in and not a string
		$id = (int)$id;
		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;

		// if the $id was passed in then go ahead and delete the item
		if ( $id && $shopper_program_id && $period_id ) {
			$sql = "DELETE FROM $this->tableName WHERE $this->idField = ? AND shopper_program_id AND period_id = ? LIMIT 1;";

			if ( $this->db->exec( $sql, array( $id, $shopper_program_id, $period_id ) ) ) {
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

	function getShopper_DocumentationsCount() {
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

	function getShopper_Documentations( $offset, $rowsPerPage ) {
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
					if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation/" . $row['image'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation/" . $row['image'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/shopper_documentation/) directory.';
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
					if ( $row['document'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation_docs/" . $row['document'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation_docs/" . $row['document'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/shopper_documentation_docs/) directory.';
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