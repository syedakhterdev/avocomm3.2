<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Admins' );
@define( 'ENTITY', 'Admin' );

class AdminManager {
	function AdminManager( $db ) {
		$this->tableName = 'Admins';
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
					$row['username'] = $this->db->parseOutputString( $row['username'], false );
					$row['first_name'] = $this->db->parseOutputString( $row['first_name'], false );
					$row['last_name'] = $this->db->parseOutputString( $row['last_name'], false );
					$row['email'] = $this->db->parseOutputString( $row['email'], false );
					$row['password'] = $this->db->parseOutputString( $row['password'], false );
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

	function add( $username, $first_name, $last_name, $email, $password, $photo, $permission_news, $permission_events, $permission_reports, $permission_trade, $permission_marketing_activities, $permission_shopper_hub, $permission_fs_hub, $permission_periods, $permission_users, $sa, $active ) {
		// clean up incoming variables for security reasons

		if ( $username == '' ) $this->error = 'Please enter a valid Username.';
		if ( $email == '' ) $this->error = 'Please enter a valid Email.';
		if ( $this->error != "" ) return ERROR;

		$username = $this->db->prepString( $username, 25 );
		$first_name = $this->db->prepString( $first_name, 35 );
		$last_name = $this->db->prepString( $last_name, 35 );
		$email = $this->db->prepString( $email, 120 );
		$password = $this->db->prepString( $password, 40 );
		$permission_news = ( (int)$permission_news ) ? "1" : "0";
		$permission_events = ( (int)$permission_events ) ? "1" : "0";
		$permission_reports = ( (int)$permission_reports ) ? "1" : "0";
		$permission_trade = ( (int)$permission_trade ) ? "1" : "0";
		$permission_marketing_activities = ( (int)$permission_marketing_activities ) ? "1" : "0";
		$permission_shopper_hub = ( (int)$permission_shopper_hub ) ? "1" : "0";
		$permission_fs_hub = ( (int)$permission_fs_hub ) ? "1" : "0";
		$permission_periods = ( (int)$permission_periods ) ? "1" : "0";
		$permission_users = ( (int)$permission_users ) ? "1" : "0";
		$sa = ( (int)$sa ) ? "1" : "0";
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			username = ?, first_name = ?, last_name = ?, email = ?, password = md5( ? ), photo = ?, permission_news = ?, permission_events = ?, permission_reports = ?, permission_trade = ?, permission_marketing_activities = ?, permission_shopper_hub = ?, permission_fs_hub = ?, permission_periods = ?, permission_users = ?, sa = ?, active = ?
			;";
		$new_id = $this->db->exec( $sql, array( $username, $first_name, $last_name, $email, $password, $photo, $permission_news, $permission_events, $permission_reports, $permission_trade, $permission_marketing_activities, $permission_shopper_hub, $permission_fs_hub, $permission_periods, $permission_users, $sa, $active ), true );
		if ( $new_id ) {
			// return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $username, $first_name, $last_name, $email, $password, $photo, $permission_news, $permission_events, $permission_reports, $permission_trade, $permission_marketing_activities, $permission_shopper_hub, $permission_fs_hub, $permission_periods, $permission_users, $sa, $active ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( $username == '' ) $this->error = 'Please enter a valid Username.';
		if ( $email == '' ) $this->error = 'Please enter a valid Email.';
		if ( $this->error != "" ) return ERROR;

		$username = $this->db->prepString( $username, 25 );
		$first_name = $this->db->prepString( $first_name, 35 );
		$last_name = $this->db->prepString( $last_name, 35 );
		$email = $this->db->prepString( $email, 120 );
		$password = $this->db->prepString( $password, 40 );
		$permission_news = ( (int)$permission_news ) ? "1" : "0";
		$permission_events = ( (int)$permission_events ) ? "1" : "0";
		$permission_reports = ( (int)$permission_reports ) ? "1" : "0";
		$permission_trade = ( (int)$permission_trade ) ? "1" : "0";
		$permission_marketing_activities = ( (int)$permission_marketing_activities ) ? "1" : "0";
		$permission_shopper_hub = ( (int)$permission_shopper_hub ) ? "1" : "0";
		$permission_fs_hub = ( (int)$permission_fs_hub ) ? "1" : "0";
		$permission_periods = ( (int)$permission_periods ) ? "1" : "0";
		$permission_users = ( (int)$permission_users ) ? "1" : "0";
		$sa = ( (int)$sa ) ? "1" : "0";
		$active = ( (int)$active ) ? "1" : "0";

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				username = ?, first_name = ?, last_name = ?, email = ?, permission_news = ?, permission_events = ?, permission_reports = ?, permission_trade = ?, permission_marketing_activities = ?, permission_shopper_hub = ?, permission_fs_hub = ?, permission_periods = ?, permission_users = ?, sa = ?, active = ?";

			if ( $password ) $sql .= ", password = md5( '$password' )";
			if ( $photo ) $sql .= ", photo = ?";

			$sql .= " WHERE $this->idField = ? LIMIT 1;";

			$params = array( $username, $first_name, $last_name, $email, $permission_news, $permission_events, $permission_reports, $permission_trade, $permission_marketing_activities, $permission_shopper_hub, $permission_fs_hub, $permission_periods, $permission_users, $sa, $active );
			if ( $photo ) $params[] = $photo;
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

	function getAdminsCount() {
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

	function getAdmins( $offset, $rowsPerPage ) {
		$sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id > 0 ORDER BY a.last_name LIMIT $offset, $rowsPerPage;";

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


	function removePhoto( $id ) {
		$id = (int)$id;

		if ( $id && $this->deletePhoto( $id ) ) {
			$sql = "UPDATE $this->tableName SET photo = NULL WHERE $this->idField = ?";

			if ( $stmt = $this->db->exec( $sql, array( $id ) ) ) {
				return SUCCESS;
			} else {
				return ERROR;
			}
		}

		return true;
	}

	function deletePhoto( $id ) {
		$id = (int)$id;

		if ( $id ) {
			$sql = "SELECT photo FROM $this->tableName WHERE $this->idField = ?";

			if ( $stmt = $this->db->exec( $sql, array( $id ) ) ) {
				if ( $row = $stmt->fetch() ) {
					if ( $row['photo'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/admins/" . $row['photo'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/admins/" . $row['photo'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/admins/) directory.';
							return ERROR;
						}
				} else {
					$this->error = 'Could not fetch record to delete Photo';
					return ERROR;
				}
			} else {
				$this->error = 'Could not select the specified record to delete Photo';
				return ERROR;
			}
		} else {
			$this->error = 'Please provide a valid ID';
			return ERROR;
		}
	}


}
?>