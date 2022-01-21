<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Users' );
@define( 'ENTITY', 'User' );

class UserManager {
	function __construct( $db ) {
		$this->tableName = 'users';
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
					$row['first_name'] = $this->db->parseOutputString( $row['first_name'], false );
					$row['last_name'] = $this->db->parseOutputString( $row['last_name'], false );
					$row['email'] = $this->db->parseOutputString( $row['email'], false );
					$row['password'] = $this->db->parseOutputString( $row['password'], false );
					$row['company'] = $this->db->parseOutputString( $row['company'], false );
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

	function add( $first_name, $last_name, $email, $company, $active ) {
		// clean up incoming variables for security reasons

		if ( $first_name == '' ) $this->error = 'Please enter a valid First Name.';
		if ( $last_name == '' ) $this->error = 'Please enter a valid Last Name.';
		if ( $email == '' ) $this->error = 'Please enter a valid Email.';
		if ( $company == '' ) $this->error = 'Please enter a valid Company.';
		if ( $this->error != "" ) return ERROR;

		$first_name = $this->db->prepString( $first_name, 40 );
		$last_name = $this->db->prepString( $last_name, 40 );
		$email = $this->db->prepString( $email, 120 );
		/*$password = $this->db->prepString( $password, 40 );*/
		$company = $this->db->prepString( $company, 80 );
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			date_created = CURDATE(), first_name = ?, last_name = ?, email = ?, company = ?, active = ?, last_updated = NOW()
			;";
		$new_id = $this->db->exec( $sql, array( $first_name, $last_name, $email, $company, $active ), true );
		if ( $new_id ) {
			// return the id for the newly created entry

			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $first_name, $last_name, $email, $password, $company, $active ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( $first_name == '' ) $this->error = 'Please enter a valid First Name.';
		if ( $last_name == '' ) $this->error = 'Please enter a valid Last Name.';
		if ( $email == '' ) $this->error = 'Please enter a valid Email.';
		if ( $company == '' ) $this->error = 'Please enter a valid Company.';
		if ( $this->error != "" ) return ERROR;

		$first_name = $this->db->prepString( $first_name, 40 );
		$last_name = $this->db->prepString( $last_name, 40 );
		$email = $this->db->prepString( $email, 120 );
		$password = $this->db->prepString( $password, 40 );
		$company = $this->db->prepString( $company, 80 );
		$active = ( (int)$active ) ? "1" : "0";

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				first_name = ?, last_name = ?, email = ?, company = ?, active = ?, last_updated = NOW()";

			if ( $password ) $sql .= ", password = md5( '$password' )";

			$sql .= " WHERE $this->idField = ? LIMIT 1;";

			$params = array( $first_name, $last_name, $email, $company, $active );

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

	function getUsersCount() {
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

    function getExportUsers( ) {
        $sql = "SELECT id,first_name,last_name,email,company,active,verify_code,agree_to_terms  FROM $this->tableName ORDER BY email ASC";
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

	function getUsers( $offset, $rowsPerPage ) {
		$sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id > 0  ORDER BY a.last_name LIMIT $offset, $rowsPerPage;";

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

    function getApprovedUsersCount() {
        $sql = "SELECT COUNT(*) AS cnt FROM $this->tableName WHERE $this->idField > 0 and agree_to_terms=1";
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

    function getApprovedUsers( $offset, $rowsPerPage ) {
        $sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id > 0 and a.agree_to_terms=1  ORDER BY a.last_name LIMIT $offset, $rowsPerPage;";

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

    function getNonApprovedUsersCount() {
        $sql = "SELECT COUNT(*) AS cnt FROM $this->tableName WHERE $this->idField > 0 and agree_to_terms=0";
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

    function getNonApprovedUsers( $offset, $rowsPerPage ) {
        $sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id > 0 and a.agree_to_terms=0  ORDER BY a.last_name LIMIT $offset, $rowsPerPage;";

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