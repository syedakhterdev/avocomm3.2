<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Period Vendors' );
@define( 'ENTITY', 'Period Vendor' );

class PeriodVendorManager {
	function PeriodVendorManager( $db ) {
		$this->tableName = 'period_vendors';
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

	function add( $period_id, $title, $logo, $tier_id, $current_marketing_activities, $upcoming_marketing_activities, $current_shopper_marketing_activities, $upcoming_shopper_marketing_activiites, $sort, $active ) {
		// clean up incoming variables for security reasons

		if ( (int)$period_id < 0 || $period_id == '' ) $this->error = 'Please enter a valid Period.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $this->error != "" ) return ERROR;

		$period_id = (int)$period_id;
		$title = $this->db->prepString( $title, 65 );
		$current_marketing_activities = $this->db->prepString( $current_marketing_activities, 0, false );
		$upcoming_marketing_activities = $this->db->prepString( $upcoming_marketing_activities, 0, false );
		$current_shopper_marketing_activities = $this->db->prepString( $current_shopper_marketing_activities, 0, false );
		$upcoming_shopper_marketing_activiites = $this->db->prepString( $upcoming_shopper_marketing_activiites, 0, false );
		$sort = (int)$sort;
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			period_id = ?, title = ?, logo = ?, tier_id = ?, current_marketing_activities = ?, upcoming_marketing_activities = ?, current_shopper_marketing_activities = ?, upcoming_shopper_marketing_activiites = ?, sort = ?, active = ?
			;";
		$new_id = $this->db->exec( $sql, array( $period_id, $title, $logo, $tier_id, $current_marketing_activities, $upcoming_marketing_activities, $current_shopper_marketing_activities, $upcoming_shopper_marketing_activiites, $sort, $active ), true );
		if ( $new_id ) {
			// return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $period_id, $title, $logo, $tier_id, $current_marketing_activities, $upcoming_marketing_activities, $current_shopper_marketing_activities, $upcoming_shopper_marketing_activiites, $sort, $active ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( (int)$period_id < 0 || $period_id == '' ) $this->error = 'Please enter a valid Period.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $this->error != "" ) return ERROR;

		$period_id = (int)$period_id;
		$title = $this->db->prepString( $title, 65 );
		$current_marketing_activities = $this->db->prepString( $current_marketing_activities, 0, false );
		$upcoming_marketing_activities = $this->db->prepString( $upcoming_marketing_activities, 0, false );
		$current_shopper_marketing_activities = $this->db->prepString( $current_shopper_marketing_activities, 0, false );
		$upcoming_shopper_marketing_activiites = $this->db->prepString( $upcoming_shopper_marketing_activiites, 0, false );
		$sort = (int)$sort;
		$active = ( (int)$active ) ? "1" : "0";

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				period_id = ?, title = ?, tier_id = ?, current_marketing_activities = ?, upcoming_marketing_activities = ?, current_shopper_marketing_activities = ?, upcoming_shopper_marketing_activiites = ?, sort = ?, active = ?";

			if ( $logo ) $sql .= ", logo = ?";

			$sql .= " WHERE $this->idField = ? LIMIT 1;";

			$params = array( $period_id, $title, $tier_id, $current_marketing_activities, $upcoming_marketing_activities, $current_shopper_marketing_activities, $upcoming_shopper_marketing_activiites, $sort, $active );
			if ( $logo ) $params[] = $logo;
			$params[] = $id;

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

	function getPeriod_VendorsCount() {
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

	function getPeriod_Vendors( $offset, $rowsPerPage ) {
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

			if ( $stmt = $this->db->exec( $sql, array( $id ) ) ) {
				if ( $row = $stmt->fetch() ) {
					if ( $row['logo'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/vendors/" . $row['logo'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/vendors/" . $row['logo'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/vendors/) directory.';
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

	function get_Dropdown( $checked = false ) {
		$sql = "SELECT ,  FROM  ORDER BY ;";
		$stmt = $this->db->prepare( $sql );
		if ( $stmt->execute() ) {
			return $this->db->buildDropdown( $stmt, $checked );
		} else {
			return ERROR;
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
	function removeDocumentation( $vendor_id, $id ) {
		$vendor_id = (int)$vendor_id;
		$id = (int)$id;

		if ( $vendor_id && $id ) {
			$sql = "DELETE FROM vendor_documentation WHERE vendor_id = ? AND id = ?";
      $stmt = $this->db->prepare( $sql );

			if ( $stmt->execute( array( $vendor_id, $id ) ) ) {
				return SUCCESS;
			} else {
				$this->error = $this->db->error();
				return ERROR;
			}
		} else {
			$this->error = 'Please provide all required arguments.';
			return ERROR;
		}
	}

}
?>