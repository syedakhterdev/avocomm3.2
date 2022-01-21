<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Vendor Updates' );
@define( 'ENTITY', 'Vendor Update' );

class VendorUpdateManager {
	function __construct( $db ) {
		$this->tableName = 'vendor_updates';
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
					$row['current_marketing_activities'] = $this->db->parseOutputString( $row['current_marketing_activities'], false );
					$row['upcoming_marketing_activities'] = $this->db->parseOutputString( $row['upcoming_marketing_activities'], false );
					$row['current_shopper_marketing_activities'] = $this->db->parseOutputString( $row['current_shopper_marketing_activities'], false );
					$row['upcoming_shopper_marketing_activiites'] = $this->db->parseOutputString( $row['upcoming_shopper_marketing_activiites'], false );
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

	function add( $vendor_id, $period_id, $current_marketing_activities, $upcoming_marketing_activities, $current_shopper_marketing_activities, $upcoming_shopper_marketing_activiites ) {
		// clean up incoming variables for security reasons

		if ( (int)$vendor_id < 0 || $vendor_id == '' ) $this->error = 'Please enter a valid Vendor.';
		if ( (int)$period_id < 0 || $period_id == '' ) $this->error = 'Please enter a valid Period.';
		if ( $this->error != "" ) return ERROR;

		$vendor_id = (int)$vendor_id;
		$period_id = (int)$period_id;
		$current_marketing_activities = $this->db->prepString( $current_marketing_activities, 0, false );
		$upcoming_marketing_activities = $this->db->prepString( $upcoming_marketing_activities, 0, false );
		$current_shopper_marketing_activities = $this->db->prepString( $current_shopper_marketing_activities, 0, false );
		$upcoming_shopper_marketing_activiites = $this->db->prepString( $upcoming_shopper_marketing_activiites, 0, false );

		$sql = "INSERT INTO $this->tableName SET
			date_created = CURDATE(), vendor_id = ?, period_id = ?, current_marketing_activities = ?, upcoming_marketing_activities = ?, current_shopper_marketing_activities = ?, upcoming_shopper_marketing_activiites = ?
			;";
		$new_id = $this->db->exec( $sql, array( $vendor_id, $period_id, $current_marketing_activities, $upcoming_marketing_activities, $current_shopper_marketing_activities, $upcoming_shopper_marketing_activiites ), true );
		if ( $new_id ) {

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $vendor_sql = 'SELECT title FROM vendors where id=? LIMIT 1';
            $vendor_list = $this->db->query($vendor_sql, array($vendor_id));
            $vendor_row    =   $vendor_list->fetch();
            $vendor_title   =  $vendor_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 1, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $vendor_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


            // return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $vendor_id, $period_id, $current_marketing_activities, $upcoming_marketing_activities, $current_shopper_marketing_activities, $upcoming_shopper_marketing_activiites ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( (int)$vendor_id < 0 || $vendor_id == '' ) $this->error = 'Please enter a valid Vendor.';
		if ( (int)$period_id < 0 || $period_id == '' ) $this->error = 'Please enter a valid Period.';
		if ( $this->error != "" ) return ERROR;

		$vendor_id = (int)$vendor_id;
		$period_id = (int)$period_id;
		$current_marketing_activities = $this->db->prepString( $current_marketing_activities, 0, false );
		$upcoming_marketing_activities = $this->db->prepString( $upcoming_marketing_activities, 0, false );
		$current_shopper_marketing_activities = $this->db->prepString( $current_shopper_marketing_activities, 0, false );
		$upcoming_shopper_marketing_activiites = $this->db->prepString( $upcoming_shopper_marketing_activiites, 0, false );

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				vendor_id = ?, period_id = ?, current_marketing_activities = ?, upcoming_marketing_activities = ?, current_shopper_marketing_activities = ?, upcoming_shopper_marketing_activiites = ?";

			

			$sql .= " WHERE $this->idField = ? LIMIT 1;";

			$params = array( $vendor_id, $period_id, $current_marketing_activities, $upcoming_marketing_activities, $current_shopper_marketing_activities, $upcoming_shopper_marketing_activiites );
			
			$params[] = $id;

			if ( $this->db->exec( $sql, $params ) ) {

                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $vendor_sql = 'SELECT title FROM vendors where id=? LIMIT 1';
                $vendor_list = $this->db->query($vendor_sql, array($vendor_id));
                $vendor_row    =   $vendor_list->fetch();
                $vendor_title   =  $vendor_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 2, reference = ?, ip_address = ?";
                $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $vendor_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );

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

	function getVendor_UpdatesCount() {
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

	function getVendor_Updates( $offset, $rowsPerPage ) {
		$sql = "SELECT a.*  FROM $this->tableName a 
			WHERE a.id > 0  ORDER BY a.vendor_id LIMIT $offset, $rowsPerPage;";

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