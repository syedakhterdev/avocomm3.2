<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage News' );
@define( 'ENTITY', 'News' );

class NewManager {
	function NewManager( $db ) {
		$this->tableName = 'news';
		$this->idField = 'id';
		$this->db = $db;
		$this->error = '';
	}

	function getByID ( $period_id, $id ) {
		// clean up incoming variables for security reasons
		$period_id = (int)$period_id;
		$id = (int)$id;

		if ( $id ) {
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
					$row['url'] = $this->db->parseOutputString( $row['url'], false );
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

	function add( $period_id, $title, $description, $image, $url, $active ) {
		// clean up incoming variables for security reasons

		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $description == '' ) $this->error = 'Please enter a valid Description.';
		if ( $url == '' ) $this->error = 'Please enter a valid URL.';
		if ( $this->error != "" ) return ERROR;

		$period_id = (int)$period_id;
		$title = $this->db->prepString( $title, 65 );
		$description = $this->db->prepString( $description, 255 );
		$url = $this->db->prepString( $url, 255 );
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			date_created = NOW(), period_id = ?, title = ?, description = ?, image = ?, url = ?, active = ?
			;";
		$new_id = $this->db->exec( $sql, array( $period_id, $title, $description, $image, $url, $active ), true );
		if ( $new_id ) {
            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $news_sql = 'SELECT title FROM news where id=? LIMIT 1';
            $news_list = $this->db->query($news_sql, array($new_id));
            $news_row    =   $news_list->fetch();
            $news_title   =  $news_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 16, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $news_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );

            // return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $period_id, $title, $description, $image, $url, $active, $created_date ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $description == '' ) $this->error = 'Please enter a valid Description.';
		if ( $this->error != "" ) return ERROR;

		$period_id = (int)$period_id;
		$title = $this->db->prepString( $title, 65 );
		$description = $this->db->prepString( $description, 255 );
		$url = $this->db->prepString( $url, 255 );
		$active = ( (int)$active ) ? "1" : "0";
        $created_date = date('Y-m-d',strtotime($created_date));

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				title = ?, description = ?, url = ?, active = ?, date_created = ?";

			if ( $image ) $sql .= ", image = ?";

			$sql .= " WHERE $this->idField = ? AND period_id = ? LIMIT 1;";

			$params = array( $title, $description, $url, $active, $created_date  );
			if ( $image ) $params[] = $image;
			$params[] = $id;
			$params[] = $period_id;

			if ( $this->db->exec( $sql, $params ) ) {

                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $news_sql = 'SELECT title FROM news where id=? LIMIT 1';
                $news_list = $this->db->query($news_sql, array($id));
                $news_row    =   $news_list->fetch();
                $news_title   =  $news_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 17, reference = ?, ip_address = ?";
                $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $news_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );

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
		if ( $id && $period_id ) {

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $news_sql = 'SELECT title FROM news where id=? LIMIT 1';
            $news_list = $this->db->query($news_sql, array($id));
            $news_row    =   $news_list->fetch();
            $news_title   =  $news_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 18, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $news_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


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

	function getNewsCount( $period_id, $mo, $yr ) {
		$period_id = (int)$period_id;
		$mo = (int)$mo;
		$yr = (int)$yr;

		$sql = "SELECT COUNT(*) AS cnt FROM $this->tableName WHERE $this->idField > 0 AND period_id = ?";
		if ( $mo ) $sql .= ' AND MONTH ( date_created ) = ?';
		if ( $yr ) $sql .= ' AND YEAR ( date_created ) = ?';
		$params = array( $period_id );
		if ( $mo ) $params[] = $mo;
		if ( $yr ) $params[] = $yr;

		if ( $stmt = $this->db->query( $sql, $params ) ) {
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

	function getNews( $period_id, $sort, $offset, $rowsPerPage, $mo, $yr ) {
		$period_id = (int)$period_id;
		$mo = (int)$mo;
		$yr = (int)$yr;

		$sql = "SELECT a.*  FROM $this->tableName a WHERE a.id > 0 AND a.period_id = ?";
		if ( $mo ) $sql .= ' AND MONTH ( date_created ) = ?';
		if ( $yr ) $sql .= ' AND YEAR ( date_created ) = ?';
		$params = array( $period_id );
		if ( $mo ) $params[] = $mo;
		if ( $yr ) $params[] = $yr;
		if ( $sort == 'date_created' ) $sort = 'date_created DESC';

		if ( $sort )
			$sql .= " ORDER BY a.$sort";
		else
			$sql .= " ORDER BY a.date_created DESC";

		$sql .= " LIMIT $offset, $rowsPerPage;";

		// pull records from the database
		if ( $result = $this->db->query( $sql, $params ) ) {
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

	function getPeriods_TitleDropdown( $checked = false ) {
		$sql = "SELECT id, title FROM periods ORDER BY title;";
		$stmt = $this->db->prepare( $sql );
		if ( $stmt->execute() ) {
			return $this->db->buildDropdown( $stmt, $checked );
		} else {
			return ERROR;
		}
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
			if ( $news = $this->db->query( $sql, array( $id ) ) ) {
				if ( $row = $this->db->fetch( $news ) ) {
					if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/news/" . $row['image'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/news/" . $row['image'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/news/) directory.';
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


}
?>