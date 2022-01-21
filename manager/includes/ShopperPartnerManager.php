<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Shopper Partners' );
@define( 'ENTITY', 'Shopper Partner' );

class ShopperPartnerManager {
	function ShopperPartnerManager( $db ) {
		$this->tableName = 'shopper_partners';
		$this->idField = 'id';
		$this->db = $db;
		$this->error = '';
	}

	function getByID ( $id ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( $id ) {
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

	function add( $title, $logo, $active ) {
		// clean up incoming variables for security reasons

		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $this->error != "" ) return ERROR;

		$title = $this->db->prepString( $title, 65 );
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			title = ?, logo = ?, active = ?
			;";
		$new_id = $this->db->exec( $sql, array( $title, $logo, $active ), true );
		if ( $new_id ) {

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $shopper_sql = 'SELECT title FROM shopper_partners where id=? LIMIT 1';
            $shopper_list = $this->db->query($shopper_sql, array($new_id));
            $shopper_row    =   $shopper_list->fetch();
            $shopper_title   =  $shopper_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 10, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $shopper_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );

            // return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $title, $logo, $active ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $this->error != "" ) return ERROR;

		$title = $this->db->prepString( $title, 65 );
		$active = ( (int)$active ) ? "1" : "0";

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				title = ?, active = ?";

			if ( $logo ) $sql .= ", logo = ?";

			$sql .= " WHERE $this->idField = ? LIMIT 1;";

			$params = array( $title, $active );
			if ( $logo ) $params[] = $logo;
			$params[] = $id;

			if ( $this->db->exec( $sql, $params ) ) {
                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $shopper_sql = 'SELECT title FROM shopper_partners where id=? LIMIT 1';
                $shopper_list = $this->db->query($shopper_sql, array($id));
                $shopper_row    =   $shopper_list->fetch();
                $shopper_title   =  $shopper_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 11, reference = ?, ip_address = ?";
                $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $shopper_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );

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

            $shopper_sql = 'SELECT title FROM shopper_partners where id=? LIMIT 1';
            $shopper_list = $this->db->query($shopper_sql, array($id));
            $shopper_row    =   $shopper_list->fetch();
            $shopper_title   =  $shopper_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 12, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $shopper_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


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

	function getShopper_PartnersCount() {
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

	function getShopper_Partners( $offset, $rowsPerPage ) {
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


	function removeLogo( $id ) {
		$id = (int)$id;

		if ( $id && $this->deleteLogo( $id ) ) {
			$sql = "UPDATE $this->tableName SET logo = NULL WHERE $this->idField = ?";

			if ( $stmt = $this->db->exec( $sql, array( $id ) ) ) {
				return SUCCESS;
			} else {
				return ERROR;
			}
		}

		return true;
	}

	function deleteLogo( $id ) {
		$id = (int)$id;

		if ( $id ) {
			$sql = "SELECT logo FROM $this->tableName WHERE $this->idField = ?";

			if ( $partners = $this->db->query( $sql, array( $id ) ) ) {
				if ( $row = $this->db->fetch( $partners ) ) {
					if ( $row['logo'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_partners/" . $row['logo'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_partners/" . $row['logo'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/shopper_partners/) directory.';
							return ERROR;
						}
				} else {
					$this->error = 'Could not fetch record to delete Logo';
					return ERROR;
				}
			} else {
				$this->error = 'Could not select the specified record to delete Logo';
				return ERROR;
			}
		} else {
			$this->error = 'Please provide a valid ID';
			return ERROR;
		}
	}


}
?>