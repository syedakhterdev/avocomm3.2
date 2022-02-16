<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Shopper Program Updates' );
@define( 'ENTITY', 'Shopper Program Update' );

class ShopperProgramUpdateManager {
	function __construct( $db ) {
		$this->tableName = 'shopper_program_updates';
		$this->idField = 'id';
		$this->db = $db;
		$this->error = '';
	}

	function getByID ( $shopper_program_id, $period_id, $id ) {
		// clean up incoming variables for security reasons
		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$id = (int)$id;

		if ( $id ) { // make sure a valid ID was passed in
			$sql = "SELECT * FROM $this->tableName WHERE $this->idField = ? AND shopper_program_id = ? AND period_id = ? LIMIT 1";
			$stmt = $this->db->query( $sql, array( $id, $shopper_program_id, $period_id ) );

			if ( $stmt ) {
				// pull 1 record from the database
				if ( !( $row = $stmt->fetch() ) ) {
					return ERROR;
				} else {
					// clean up the output for the page calling this, no slashes will be included
					// Remove all slashes from outgoing text
					$row['description'] = $this->db->parseOutputString( $row['description'], false );
					$row['updates'] = $this->db->parseOutputString( $row['updates'], false );
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

	function add( $shopper_program_id, $period_id, $description, $updates ) {
		// clean up incoming variables for security reasons

		if ( $updates == '' ) $this->error = 'Please enter a valid Updates.';
		if ( $this->error != "" ) return ERROR;

		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$description = $this->db->prepString( $description, 0, false );
		$updates = $this->db->prepString( $updates, 0, false );

		$sql = "INSERT INTO $this->tableName SET
			shopper_program_id = ?, period_id = ?, description = ?, updates = ?
			;";
		$new_id = $this->db->exec( $sql, array( $shopper_program_id, $period_id, $description, $updates ), true );
		if ( $new_id ) {

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $shopper_sql = 'SELECT title FROM shopper_programs where id=? LIMIT 1';
            $shopper_list = $this->db->query($shopper_sql, array($shopper_program_id));
            $shopper_row    =   $shopper_list->fetch();
            $shopper_title   =  $shopper_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 7, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $shopper_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


            // return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $shopper_program_id, $period_id, $description, $updates ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( $this->error != "" ) return ERROR;

		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$description = $this->db->prepString( $description, 0, false );
		$updates = $this->db->prepString( $updates, 0, false );

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET description = ?, updates = ?";

			$sql .= " WHERE $this->idField = ? AND shopper_program_id = ? AND period_id = ? LIMIT 1;";

			$params = array( $description, $updates );

			$params[] = $id;
			$params[] = $shopper_program_id;
			$params[] = $period_id;

			if ( $this->db->exec( $sql, $params ) ) {
                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $shopper_sql = 'SELECT title FROM shopper_programs where id=? LIMIT 1';
                $shopper_list = $this->db->query($shopper_sql, array($shopper_program_id));
                $shopper_row    =   $shopper_list->fetch();
                $shopper_title   =  $shopper_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 8, reference = ?, ip_address = ?";
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

	function delete ( $shopper_program_id, $period_id, $id ) {
		// convert the $id to make sure a number is being passed in and not a string
		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$id = (int)$id;

		// if the $id was passed in then go ahead and delete the item
		if ( $shopper_program_id && $period_id && $id ) {
			$sql = "DELETE FROM $this->tableName WHERE $this->idField = ? AND shopper_program_id = ? AND period_id = ? LIMIT 1;";

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

	function getShopper_Program_UpdatesCount() {
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

	function getShopper_Program_Updates( $offset, $rowsPerPage ) {
		$sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id > 0 LIMIT $offset, $rowsPerPage;";

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


}
?>