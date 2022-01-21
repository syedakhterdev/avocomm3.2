<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Event Categories' );
@define( 'ENTITY', 'Event Category' );

class EventCategoryManager {
	function __construct( $db ) {
		$this->tableName = 'event_categories';
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
					$row['category'] = $this->db->parseOutputString( $row['category'], false );
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

	function add( $category ) {
		// clean up incoming variables for security reasons

		if ( $category == '' ) $this->error = 'Please enter a valid Category.';
		if ( $this->error != "" ) return ERROR;

		$category = $this->db->prepString( $category, 65 );

		$sql = "INSERT INTO $this->tableName SET
			category = ?
			;";
		$new_id = $this->db->exec( $sql, array( $category ), true );
		if ( $new_id ) {

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $event_sql = 'SELECT category FROM event_categories where id=? LIMIT 1';
            $event_list = $this->db->query($event_sql, array($new_id));
            $event_row    =   $event_list->fetch();
            $event_title   =  $event_row['category'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 25, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $event_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


            // return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $category ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( $category == '' ) $this->error = 'Please enter a valid Category.';
		if ( $this->error != "" ) return ERROR;

		$category = $this->db->prepString( $category, 65 );

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				category = ?";



			$sql .= " WHERE $this->idField = ? LIMIT 1;";

			$params = array( $category );

			$params[] = $id;

			if ( $this->db->exec( $sql, $params ) ) {

                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $event_sql = 'SELECT category FROM event_categories where id=? LIMIT 1';
                $event_list = $this->db->query($event_sql, array($id));
                $event_row    =   $event_list->fetch();
                $event_title   =  $event_row['category'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 26, reference = ?, ip_address = ?";
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

            $event_sql = 'SELECT category FROM event_categories where id=? LIMIT 1';
            $event_list = $this->db->query($event_sql, array($id));
            $event_row    =   $event_list->fetch();
            $event_title   =  $event_row['category'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 27, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $event_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


            $sql = "DELETE FROM $this->tableName WHERE $this->idField = ? LIMIT 1;";

			if ( $this->db->exec( $sql, array( $id ) ) ) {
				// return true if the item was deleted successfully
				return SUCCESS;
			} else {
				// return false if there was an error and the item was not deleted
				$this->error = $this->db->error();
				$this->error = 'Cannot delete the specified category because an event has been assigned to it.';
				return ERROR;
			}
		} else {
			return false;
		}
	}

	function getEvent_CategoriesCount() {
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

	function getEvent_Categories( $offset, $rowsPerPage ) {
		$sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id > 0  ORDER BY a.category LIMIT $offset, $rowsPerPage;";

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