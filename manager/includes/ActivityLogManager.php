<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Activity Log' );

class ActivityLogManager {
	function __construct( $db ) {
		$this->tableName = 'activity_log';
		$this->idField = 'id';
		$this->db = $db;
		$this->error = '';
	}

	function getActivityCount( $filters = '' ) {
		$sql = "SELECT COUNT(*) AS cnt FROM $this->tableName a WHERE $this->idField > 0 $filters;";
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

	function getActivity( $offset, $rowsPerPage, $filters = '' ) {
		$sql = "SELECT a.*, CONCAT( b.first_name, ' ', b.last_name ) AS full_name, c.activity_type FROM $this->tableName a , users b, activity_types c
			WHERE a.id > 0 AND a.user_id = b.id AND a.activity_type_id = c.id $filters ORDER BY a.date_created DESC LIMIT $offset, $rowsPerPage;";
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
    function getActivitylog( $offset, $rowsPerPage, $filters = '' ) {
        $sql = "SELECT a.*, CONCAT( b.first_name, ' ', b.last_name ) AS full_name,b.email, c.activity_type FROM $this->tableName a , users b, activity_types c
			WHERE a.id > 0 AND a.user_id = b.id AND a.activity_type_id = c.id $filters ORDER BY a.date_created DESC LIMIT $offset, $rowsPerPage;";
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