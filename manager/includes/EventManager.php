<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Events' );
@define( 'ENTITY', 'Event' );

class EventManager {
	function __construct( $db ) {
		$this->tableName = 'events';
		$this->idField = 'id';
		$this->db = $db;
		$this->error = '';
	}

	function getByID ( $id ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( $id ) { // make sure a valid ID was passed in
			$sql = "SELECT * FROM $this->tableName WHERE $this->idField = ? LIMIT 1";
			$stmt = $this->db->query( $sql, array( $id ) );

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

    function add( $title, $description, $event_date, $image, $category_id, $featured, $active ) {
        // clean up incoming variables for security reasons

        if ( $description == '' ) $this->error = 'Please enter a valid Description.';
        if ( $event_date == '' ) $this->error = 'Please enter a valid Event Date.';
        if ( $this->error != "" ) return ERROR;

        $title = $this->db->prepString( $title, 65 );
        $description = $this->db->prepString( $description, 0, false );
        $category_id = (int)$category_id;
        $featured = ( (int)$featured ) ? "1" : "0";
        $active = ( (int)$active ) ? "1" : "0";

        $image_name  =   NULL;
        if($_FILES[$image]['size']>0){
            $temp = explode(".", $_FILES[$image]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);

            $target_file   =    dirname(__FILE__,3) . "/assets/events/".$newfilename;
            if (move_uploaded_file($_FILES[$image]["tmp_name"], $target_file)) {
                $image_name   =   $newfilename;
            }
        }

        $sql = "INSERT INTO $this->tableName SET
			date_created = CURDATE(), title = ?, description = ?, event_date = ?, image = ?, category_id = ?, featured = ?, active = ?
			;";
        $new_id = $this->db->exec( $sql, array( $title, $description, $event_date, $image_name, $category_id, $featured, $active ), true );
        if ( $new_id ) {

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $event_sql = 'SELECT title FROM events where id=? LIMIT 1';
            $event_list = $this->db->query($event_sql, array($new_id));
            $event_row    =   $event_list->fetch();
            $event_title   =  $event_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 19, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $event_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );
            // return the id for the newly created entry
            return $new_id;
        } else {
            $this->error = $this->db->error();
            return ERROR;
        }
    }

    function update( $id, $title, $description, $event_date, $image, $category_id, $featured, $active ) {
        // clean up incoming variables for security reasons
        $id = (int)$id;

        if ( $description == '' ) $this->error = 'Please enter a valid Description.';
        if ( $event_date == '' ) $this->error = 'Please enter a valid Event Date.';
        if ( $this->error != "" ) return ERROR;

        if($_FILES[$image]['size']>0){
            $temp = explode(".", $_FILES[$image]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);

            $target_file   =    dirname(__FILE__,3) . "/assets/events/".$newfilename;
            if (move_uploaded_file($_FILES[$image]["tmp_name"], $target_file)) {
                $image_name   =   $newfilename;
            }
        }

        $title = $this->db->prepString( $title, 65 );
        $description = $this->db->prepString( $description, 0, false );
        $category_id = (int)$category_id;
        $featured = ( (int)$featured ) ? "1" : "0";
        $active = ( (int)$active ) ? "1" : "0";

        if ( $id ) {
            $sql = "UPDATE $this->tableName SET
				title = ?, description = ?, event_date = ?, category_id = ?, featured = ?, active = ?";

            if ( $image_name ) $sql .= ", image = ?";

            $sql .= " WHERE $this->idField = ? LIMIT 1;";

            $params = array( $title, $description, $event_date, $category_id, $featured, $active );
            if ( $image_name ) $params[] = $image_name;
            $params[] = $id;

            if ( $this->db->exec( $sql, $params ) ) {

                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $event_sql = 'SELECT title FROM events where id=? LIMIT 1';
                $event_list = $this->db->query($event_sql, array($id));
                $event_row    =   $event_list->fetch();
                $event_title   =  $event_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 20, reference = ?, ip_address = ?";
                $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $event_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


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

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $event_sql = 'SELECT title FROM events where id=? LIMIT 1';
            $event_list = $this->db->query($event_sql, array($id));
            $event_row    =   $event_list->fetch();
            $event_title   =  $event_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 21, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $event_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


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

	function getEventsCount() {
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

	function getEvents( $offset, $rowsPerPage ) {
		$sql = "SELECT a.* , b.category FROM $this->tableName a , event_categories b
			WHERE a.id > 0 AND a.category_id = b.id ORDER BY a.event_date DESC, a.title LIMIT $offset, $rowsPerPage;";

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

		if ( $id && $this->deleteImage( $id ) ) {
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
					if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/events/" . $row['image'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/events/" . $row['image'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/events/) directory.';
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

	function getEvent_categories_CategoryDropdown( $checked = false ) {
		$sql = "SELECT id, category FROM event_categories ORDER BY category;";
		$stmt = $this->db->prepare( $sql );
		if ( $stmt->execute() ) {
			return $this->db->buildDropdown( $stmt, $checked );
		} else {
			return ERROR;
		}
	}

}
?>