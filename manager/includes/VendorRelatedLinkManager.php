<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Vendor Related Links' );
@define( 'ENTITY', 'Vendor Related Link' );

class VendorRelatedLinkManager {
	function __construct( $db ) {
		$this->tableName = 'vendor_related_links';
		$this->idField = 'id';
		$this->db = $db;
		$this->error = '';
	}

	function getByID ( $vendor_id, $period_id, $id ) {
		// clean up incoming variables for security reasons
		$vendor_id = (int)$vendor_id;
		$period_id = (int)$period_id;
		$id = (int)$id;

		if ( $id && $vendor_id && $period_id ) { // make sure a valid ID was passed in
			$sql = "SELECT * FROM $this->tableName WHERE $this->idField = ? AND vendor_id = ? AND period_id = ? LIMIT 1";
			$stmt = $this->db->query( $sql, array( $id, $vendor_id, $period_id ) );

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

	function add( $period_id, $vendor_id, $title, $description, $image, $url, $sort ) {
		// clean up incoming variables for security reasons

		if ( (int)$vendor_id < 0 || $vendor_id == '' ) $this->error = 'Please enter a valid Vendor.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $description == '' ) $this->error = 'Please enter a valid Description.';
		if ( $this->error != "" ) return ERROR;

		$period_id = (int)$period_id;
		$vendor_id = (int)$vendor_id;
		$title = $this->db->prepString( $title, 85 );
		$description = $this->db->prepString( $description, 255 );
		$url = $this->db->prepString( $url, 250 );
		$sort = (int)$sort;

		$sql = "INSERT INTO $this->tableName SET
			period_id = ?, vendor_id = ?, title = ?, description = ?, image = ?, url = ?, sort = ?
			;";
		$new_id = $this->db->exec( $sql, array( $period_id, $vendor_id, $title, $description, $image, $url, $sort ), true );
		if ( $new_id ) {
			// return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $period_id, $vendor_id, $title, $description, $image, $url, $sort ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( (int)$vendor_id < 0 || $vendor_id == '' ) $this->error = 'Please enter a valid Vendor.';
		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $description == '' ) $this->error = 'Please enter a valid Description.';
		if ( $this->error != "" ) return ERROR;

		$period_id = (int)$period_id;
		$vendor_id = (int)$vendor_id;
		$title = $this->db->prepString( $title, 85 );
		$description = $this->db->prepString( $description, 255 );
		$url = $this->db->prepString( $url, 250 );
		$sort = (int)$sort;

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				title = ?, description = ?, url = ?, sort = ?";

			if ( $image ) $sql .= ", image = ?";

			$sql .= " WHERE $this->idField = ? AND period_id = ? AND vendor_id = ? LIMIT 1;";

			$params = array( $title, $description, $url, $sort );
			if ( $image ) $params[] = $image;
			$params[] = $id;
			$params[] = $period_id;
			$params[] = $vendor_id;

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

	function delete ( $period_id, $vendor_id, $id ) {
		// convert the $id to make sure a number is being passed in and not a string
		$period_id = (int)$period_id;
		$vendor_id = (int)$vendor_id;
		$id = (int)$id;

		// if the $id was passed in then go ahead and delete the item
		if ( $period_id && $vendor_id && $id ) {
			$sql = "DELETE FROM $this->tableName WHERE $this->idField = ? AND vendor_id = ? AND period_id = ? LIMIT 1;";

			if ( $this->db->exec( $sql, array( $id, $vendor_id, $period_id ) ) ) {
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

	function getvendor_related_LinksCount() {
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

	function getvendor_related_Links( $offset, $rowsPerPage ) {
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

			if ( $stmt = $this->db->query( $sql, array( $id ) ) ) {
				if ( $row = $stmt->fetch() ) {
					if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/vendor_related_links/" . $row['image'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/vendor_related_links/" . $row['image'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/vendor_related_links/) directory.';
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