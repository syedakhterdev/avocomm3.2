<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Kit Options' );
@define( 'ENTITY', 'Shopper Kit Option' );

class ShopperProgramBinManager {
	function __construct( $db ) {
		$this->tableName = 'shopper_program_bins';
		$this->idField = 'id';
		$this->db = $db;
		$this->error = '';
	}

	function getByID ( $shopper_program_id, $id ) {
		// clean up incoming variables for security reasons
		$shopper_program_id = (int)$shopper_program_id;
		$id = (int)$id;

		if ( $shopper_program_id && $id ) { // make sure a valid ID was passed in
			$sql = "SELECT * FROM $this->tableName WHERE $this->idField = ? AND shopper_program_id = ? LIMIT 1";
			$stmt = $this->db->query( $sql, array( $id, $shopper_program_id ) );

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

	function add( $shopper_program_id, $title, $image, $sort, $active ) {
		// clean up incoming variables for security reasons

		if ( (int)$shopper_program_id < 0 || $shopper_program_id == '' ) $this->error = 'Please enter a valid Shopper Program.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $image == '' ) $this->error = 'Please enter a valid Image.';
		if ( $this->error != "" ) return ERROR;

		$shopper_program_id = (int)$shopper_program_id;
		$title = $this->db->prepString( $title, 85 );
		$sort = (int)$sort;
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			shopper_program_id = ?, title = ?, image = ?, sort = ?, active = ?
			;";
		$new_id = $this->db->exec( $sql, array( $shopper_program_id, $title, $image, $sort, $active ), true );
		if ( $new_id ) {
			// return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $shopper_program_id, $title, $image, $sort, $active ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( (int)$shopper_program_id < 0 || $shopper_program_id == '' ) $this->error = 'Please enter a valid Shopper Program.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $this->error != "" ) return ERROR;

		$shopper_program_id = (int)$shopper_program_id;
		$title = $this->db->prepString( $title, 85 );
		$sort = (int)$sort;
		$active = ( (int)$active ) ? "1" : "0";

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				title = ?, sort = ?, active = ?";

			if ( $image ) $sql .= ", image = ?";

			$sql .= " WHERE $this->idField = ? AND shopper_program_id = ? LIMIT 1;";

			$params = array( $title, $sort, $active );
			if ( $image ) $params[] = $image;
			$params[] = $id;
			$params[] = $shopper_program_id;

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

	function getShopper_Program_BinsCount() {
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

	function getShopper_Program_Bins( $offset, $rowsPerPage ) {
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

			if ( $bins = $this->db->query( $sql, array( $id ) ) ) {
				if ( $row = $this->db->fetch( $bins ) ) {
					if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_program_bins/" . $row['image'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_program_bins/" . $row['image'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/shopper_program_bins/) directory.';
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

}
?>