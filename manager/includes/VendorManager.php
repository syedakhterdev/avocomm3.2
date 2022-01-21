<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Vendors' );
@define( 'ENTITY', 'Vendor' );

class VendorManager {
	function __construct( $db ) {
		$this->tableName = 'vendors';
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

	function add( $title, $logo, $tier_id, $sort, $active ) {
		// clean up incoming variables for security reasons

		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $this->error != "" ) return ERROR;

		$title = $this->db->prepString( $title, 65 );
		$tier_id = (int)$tier_id;
		$sort = (int)$sort;
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			title = ?, logo = ?, tier_id = ?, sort = ?, active = ?
			;";
		$new_id = $this->db->exec( $sql, array( $title, $logo, $tier_id, $sort, $active ), true );
		if ( $new_id ) {

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $vendor_sql = 'SELECT title FROM vendors where id=? LIMIT 1';
            $vendor_list = $this->db->query($vendor_sql, array($new_id));
            $vendor_row    =   $vendor_list->fetch();
            $vendor_title   =  $vendor_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 4, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $vendor_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


            // return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $title, $logo, $tier_id, $sort, $active ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( $title == '' ) $this->error = 'Please enter a valid Title.';
		if ( $this->error != "" ) return ERROR;

		$title = $this->db->prepString( $title, 65 );
		$tier_id = (int)$tier_id;
		$sort = (int)$sort;
		$active = ( (int)$active ) ? "1" : "0";

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				title = ?, tier_id = ?, sort = ?, active = ?";

			if ( $logo ) $sql .= ", logo = ?";

			$sql .= " WHERE $this->idField = ? LIMIT 1;";

			$params = array( $title, $tier_id, $sort, $active );
			if ( $logo ) $params[] = $logo;
			$params[] = $id;

			if ( $this->db->exec( $sql, $params ) ) {
                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $vendor_sql = 'SELECT title FROM vendors where id=? LIMIT 1';
                $vendor_list = $this->db->query($vendor_sql, array($id));
                $vendor_row    =   $vendor_list->fetch();
                $vendor_title   =  $vendor_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 5, reference = ?, ip_address = ?";
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
            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $vendor_sql = 'SELECT title FROM vendors where id=? LIMIT 1';
            $vendor_list = $this->db->query($vendor_sql, array($id));
            $vendor_row    =   $vendor_list->fetch();
            $vendor_title   =  $vendor_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 6, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $vendor_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


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

	function deleteUpdate ( $period_id, $vendor_id ) {
		// convert the $id to make sure a number is being passed in and not a string
		$vendor_id = (int)$vendor_id;
		$period_id = (int)$period_id;

		// if the $id was passed in then go ahead and delete the item
		if ( $vendor_id && $period_id ) {
			// delete all updates for this program in this period
			$sql = "DELETE FROM vendor_updates WHERE vendor_id = ? AND period_id = ?;";
			$this->db->exec( $sql, array( $vendor_id, $period_id ) );

			// delete all related links for this program in this period
			$sql = "DELETE FROM vendor_related_links WHERE vendor_id = ? AND period_id = ?;";
			$this->db->exec( $sql, array( $vendor_id, $period_id ) );

			// delete all documentation for this program in this period
			$sql = "DELETE FROM vendor_documentation WHERE vendor_id = ? AND period_id = ?;";
			$this->db->exec( $sql, array( $vendor_id, $period_id ) );

			if ( true ) {
				// return true if the item was deleted successfully
                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $vendor_sql = 'SELECT title FROM vendors where id=? LIMIT 1';
                $vendor_list = $this->db->query($vendor_sql, array($vendor_id));
                $vendor_row    =   $vendor_list->fetch();
                $vendor_title   =  $vendor_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 3, reference = ?, ip_address = ?";
                $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $vendor_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );

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

	function getVendorsCount() {
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

	function getVendors( $offset, $rowsPerPage ) {
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

			if ( $stmt = $this->db->query( $sql, array( $id ) ) ) {
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

	function removeDocument( $id, $period_id ) {
		$id = (int)$id;
		$period_id = (int)$period_id;

		if ( $id && $period_id ) {
			$sql = "DELETE FROM vendor_documentation WHERE $this->idField = ? AND period_id = ?";

			if ( $stmt = $this->db->exec( $sql, array( $id, $period_id ) ) ) {
				return SUCCESS;
			} else {
				return ERROR;
			}
		}

		return true;
	}

	function deleteDocument( $id, $period_id ) {
		$id = (int)$id;
		$period_id = (int)$period_id;

		if ( $id && $period_id ) {
			// delete the document
			$sql = "SELECT document FROM vendor_documentation WHERE $this->idField = ? AND period_id = ?";
			if ( $docs = $this->db->query( $sql, array( $id, $period_id ) ) ) {
				if ( $row = $this->db->fetch( $docs ) ) {
					if ( $row['document'] != '' ) {
						if ( $row['document'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_docs/" . $row['document'] ) ) {
							if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_docs/" . $row['document'] ) ) {
								return SUCCESS;
							} else {
								$this->error = 'Could not delete file from (/assets/documentation_docs/) directory.';
								return ERROR;
							}
						}
					} else {
						return SUCCESS;
					}
				}
			}

			// delete the image of the document
			$sql = "SELECT image FROM vendor_documentation WHERE $this->idField = ? AND period_id = ?";

			if ( $docs = $this->db->query( $sql, array( $id, $period_id ) ) ) {
				if ( $row =  $this->db->fetch( $docs ) ) {
					if ( $row['image'] != '' ) {
						if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_images/" . $row['image'] ) ) {
							if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_images/" . $row['image'] ) ) {
								return SUCCESS;
							} else {
								$this->error = 'Could not delete file from (/assets/documentation_images/) directory.';
								return ERROR;
							}
						}
					} else {
						return SUCCESS;
					}
				}
			}

		} else {
			$this->error = 'Please provide a valid ID';
			return ERROR;
		}
	}

	function removeRelated( $id, $period_id ) {
		$id = (int)$id;
		$period_id = (int)$period_id;
		if ( $id && $period_id && $this->deleteRelated( $id, $period_id ) ) {
			$sql = "DELETE FROM vendor_related_links WHERE $this->idField = ? AND period_id = ?";

			if ( $stmt = $this->db->exec( $sql, array( $id, $period_id ) ) ) {
				return SUCCESS;
			} else {
				return ERROR;
			}
		}

		return true;
	}

	function deleteRelated( $id, $period_id ) {
		$id = (int)$id;
		$period_id = (int)$period_id;

		if ( $id && $period_id ) {
			// delete the document
			$sql = "SELECT image FROM vendor_related_links WHERE $this->idField = ? AND period_id = ?";
			if ( $docs = $this->db->query( $sql, array( $id, $period_id ) ) ) {
				if ( $row = $this->db->fetch( $docs ) ) {
					if ( $row['image'] != '' ) {
						if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/vendor_related_links/" . $row['image'] ) ) {
							if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/vendor_related_links/" . $row['image'] ) ) {
								return SUCCESS;
							} else {
								$this->error = 'Could not delete file from (/assets/vendor_related_links/) directory.';
								return ERROR;
							}
						}
					} else {
						return SUCCESS;
					}
				}
			}

			// delete the image of the document
			$sql = "SELECT image FROM vendor_documentation WHERE $this->idField = ? AND period_id = ?";

			if ( $docs = $this->db->query( $sql, array( $id, $period_id ) ) ) {
				if ( $row =  $this->db->fetch( $docs ) ) {
					if ( $row['image'] != '' ) {
						if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_images/" . $row['image'] ) ) {
							if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/documentation_images/" . $row['image'] ) ) {
								return SUCCESS;
							} else {
								$this->error = 'Could not delete file from (/assets/documentation_images/) directory.';
								return ERROR;
							}
						}
					} else {
						return SUCCESS;
					}
				}
			}

		} else {
			$this->error = 'Please provide a valid ID';
			return ERROR;
		}
	}

	function getVendor_tiers_TierDropdown( $checked = false ) {
		$sql = "SELECT id, tier FROM vendor_tiers ORDER BY tier;";
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

	function getVendorDropdown( $period_id, $checked = false ) {
		$period_id = (int)$period_id;
		$sql = "SELECT id, title FROM vendors WHERE id NOT IN ( SELECT vendor_id FROM vendor_updates WHERE period_id = ? ) ORDER BY title;";
		$programs = $this->db->query( $sql, array( $period_id ) );
		if ( $this->db->num_rows() > 0 ) {
			return $this->db->buildDropdown( $programs, $checked );
		} else {
			return ERROR;
		}
	}

	function getEntriesCountCurrent( $period_id ) {
		$period_id = (int)$period_id;
		$sql = "SELECT COUNT(*) AS cnt FROM $this->tableName WHERE id IN ( SELECT vendor_id FROM vendor_updates WHERE period_id = ? );";
		if ( $stmt = $this->db->query( $sql, array( $period_id ) ) ) {
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

	function getEntriesCurrent( $period_id, $offset, $rowsPerPage ) {
		$period_id = (int)$period_id;
		$sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id IN ( SELECT vendor_id FROM vendor_updates WHERE period_id = ? ) ORDER BY a.title LIMIT $offset, $rowsPerPage;";

		// pull records from the database
		if ( $result = $this->db->query( $sql, array( $period_id ) ) ) {
			// return the resulting records
			return $result;
		} else {
			// return false if there was an error and the item was not deleted
			$this->error = $this->db->error();
			return ERROR;
		}
	}

}
?>