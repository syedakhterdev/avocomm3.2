<?php
@define( 'ERROR', 0 );
@define( 'SUCCESS', 1 );
@define( 'SECTION_TITLE', 'Manage Shopper Programs' );
@define( 'ENTITY', 'Shopper Program' );

class ShopperProgramManager {
	function __construct( $db ) {
		$this->tableName = 'shopper_programs';
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
					$row['intro'] = $this->db->parseOutputString( $row['intro'], false );
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

	function add( $title, $image, $start_date, $end_date, $intro, $sort, $active ) {
		// clean up incoming variables for security reasons

		if ( !$intro ) $this->error = 'Please enter a valid intro.';
		if ( $this->error != "" ) return ERROR;

		$title = $this->db->prepString( $title, 85 );
		$sort = (int)$sort;
		$active = ( (int)$active ) ? "1" : "0";

		$sql = "INSERT INTO $this->tableName SET
			title = ?, image = ?, start_date = ?, end_date = ?, intro = ?, sort = ?, active = ?
			;";
		$new_id = $this->db->exec( $sql, array( $title, $image, $start_date, $end_date, $intro, $sort, $active ), true );
		if ( $new_id ) {
            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $shopper_sql = 'SELECT title FROM shopper_programs where id=? LIMIT 1';
            $shopper_list = $this->db->query($shopper_sql, array($new_id));
            $shopper_row    =   $shopper_list->fetch();
            $shopper_title   =  $shopper_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 13, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $shopper_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );

            // return the id for the newly created entry
			return $new_id;
		} else {
			$this->error = $this->db->error();
			return ERROR;
		}
	}

	function update( $id, $title, $image, $start_date, $end_date, $intro, $sort, $active ) {
		// clean up incoming variables for security reasons
		$id = (int)$id;

		if ( !$intro ) $this->error = 'Please enter a valid intro.';
		if ( $this->error != "" ) return ERROR;

		$title = $this->db->prepString( $title, 85 );
		$sort = (int)$sort;
		$active = ( (int)$active ) ? "1" : "0";

		if ( $id ) {
			$sql = "UPDATE $this->tableName SET
				title = ?, start_date = ?, end_date = ?, intro = ?, sort = ?, active = ?";

			if ( $image ) $sql .= ", image = ?";

			$sql .= " WHERE $this->idField = ? LIMIT 1;";

			$params = array( $title, $start_date, $end_date, $intro, $sort, $active );
			if ( $image ) $params[] = $image;
			$params[] = $id;

			if ( $this->db->exec( $sql, $params ) ) {
                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $shopper_sql = 'SELECT title FROM shopper_programs where id=? LIMIT 1';
                $shopper_list = $this->db->query($shopper_sql, array($id));
                $shopper_row    =   $shopper_list->fetch();
                $shopper_title   =  $shopper_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 14, reference = ?, ip_address = ?";
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

	function delete ( $id ) {
		// convert the $id to make sure a number is being passed in and not a string
		$id = (int)$id;

		// if the $id was passed in then go ahead and delete the item
		if ( $id ) {

            $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
            $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
            $row    =   $periods_list->fetch();
            $period_title   =  $row['title'];

            $shopper_sql = 'SELECT title FROM shopper_programs where id=? LIMIT 1';
            $shopper_list = $this->db->query($shopper_sql, array($id));
            $shopper_row    =   $shopper_list->fetch();
            $shopper_title   =  $shopper_row['title'];

            $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 15, reference = ?, ip_address = ?";
            $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $shopper_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


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

	function deleteUpdate( $period_id, $shopper_program_id ) {
		// convert the $id to make sure a number is being passed in and not a string
		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;

		// if the $id was passed in then go ahead and delete the item
		if ( $shopper_program_id && $period_id ) {
			// delete all updates for this program in this period
			$sql = "DELETE FROM shopper_program_updates WHERE shopper_program_id = ? AND period_id = ?;";
			$this->db->exec( $sql, array( $shopper_program_id, $period_id ) );

			// delete all bin allocations for this program in this period
			$sql = "DELETE FROM shopper_program_bin_allocations WHERE shopper_program_id = ? AND period_id = ?;";
			$this->db->exec( $sql, array( $shopper_program_id, $period_id ) );

			// delete all related links for this program in this period
			$sql = "DELETE FROM shopper_related_links WHERE shopper_program_id = ? AND period_id = ?;";
			$this->db->exec( $sql, array( $shopper_program_id, $period_id ) );

			// delete all documentation for this program in this period
			$sql = "DELETE FROM shopper_documentation WHERE shopper_program_id = ? AND period_id = ?;";
			$this->db->exec( $sql, array( $shopper_program_id, $period_id ) );

			if ( true ) {

                $period_sql = 'SELECT title FROM periods where id=? LIMIT 1';
                $periods_list = $this->db->query($period_sql, array($_SESSION['admin_period_id']));
                $row    =   $periods_list->fetch();
                $period_title   =  $row['title'];

                $shopper_sql = 'SELECT title FROM shopper_programs where id=? LIMIT 1';
                $shopper_list = $this->db->query($shopper_sql, array($shopper_program_id));
                $shopper_row    =   $shopper_list->fetch();
                $shopper_title   =  $shopper_row['title'];

                $activity_sql = "INSERT INTO admin_activity_log SET date_created = NOW(), user_id = ?, admin_activity_type_id = 9, reference = ?, ip_address = ?";
                $this->db->exec( $activity_sql, array( $_SESSION['admin_id'], $shopper_title . ' - ' . $period_title, $_SERVER['REMOTE_ADDR'] ) );


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

	function getShopper_ProgramsCount() {
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

	function getShopper_Programs( $offset, $rowsPerPage ) {
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

	function getShopper_ProgramsCountCurrent( $period_id ) {
		$period_id = (int)$period_id;
		$sql = "SELECT COUNT(*) AS cnt FROM $this->tableName WHERE id IN ( SELECT shopper_program_id FROM shopper_program_updates WHERE period_id = ? );";
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

	function getShopper_ProgramsCurrent( $period_id, $offset, $rowsPerPage ) {
		$period_id = (int)$period_id;
		$sql = "SELECT a.*  FROM $this->tableName a
			WHERE a.id IN ( SELECT shopper_program_id FROM shopper_program_updates WHERE period_id = ? ) ORDER BY a.title LIMIT $offset, $rowsPerPage;";

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
					if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_programs/" . $row['image'] ) )
						if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_programs/" . $row['image'] ) ) {
							return SUCCESS;
						} else {
							$this->error = 'Could not delete file from (/assets/shopper_programs/) directory.';
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

	function removePartners( $shopper_program_id, $shopper_partner_id ) {
		$shopper_program_id = (int)$shopper_program_id;
		$shopper_partner_id = (int)$shopper_partner_id;

		if ( $shopper_program_id && $shopper_partner_id ) {
			$sql = "DELETE FROM shopper_programs_and_partners WHERE shopper_program_id = ? AND shopper_partner_id = ?";
      $stmt = $this->db->prepare( $sql );

			if ( $stmt->execute( array( $shopper_program_id, $shopper_partner_id ) ) ) {
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

	function addToPartners( $shopper_program_id, $shopper_partner_id ) {
		$shopper_program_id = (int)$shopper_program_id;
		$shopper_partner_id = (int)$shopper_partner_id;

		if ( $shopper_program_id && $shopper_partner_id ) {
			$sql = "INSERT INTO shopper_programs_and_partners VALUES ( ?, ? );";
      $stmt = $this->db->prepare( $sql );

			if ( $stmt->execute( array( $shopper_program_id, $shopper_partner_id ) ) ) {
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

	function removeBinAllocation( $shopper_program_id, $period_id, $bin_id ) {
		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$bin_id = (int)$bin_id;

		if ( $shopper_program_id && $period_id && $bin_id ) {
			$sql = "DELETE FROM shopper_program_bin_allocations WHERE shopper_program_id = ? AND period_id = ? AND shopper_program_bin_id = ?";
      $stmt = $this->db->prepare( $sql );

			if ( $stmt->execute( array( $shopper_program_id, $period_id, $bin_id ) ) ) {
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

	function addBinAllocation( $shopper_program_id, $period_id, $bin_id, $qty ) {
		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$bin_id = (int)$bin_id;
		$qty = (int)$qty;

		if ( $shopper_program_id && $period_id && $bin_id ) {
			$sql = "INSERT INTO shopper_program_bin_allocations VALUES ( ?, ?, ?, ? );";
      $stmt = $this->db->prepare( $sql );

			if ( $stmt->execute( array( $shopper_program_id, $period_id, $bin_id, $qty ) ) ) {
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

	function getProgramDropdown( $period_id, $checked = false ) {
		$period_id = (int)$period_id;
		$sql = "SELECT id, title FROM shopper_programs WHERE id NOT IN ( SELECT shopper_program_id FROM shopper_program_updates WHERE period_id = ? ) ORDER BY title;";
		$programs = $this->db->query( $sql, array( $period_id ) );
		if ( $this->db->num_rows() > 0 ) {
			return $this->db->buildDropdown( $programs, $checked );
		} else {
			return ERROR;
		}
	}

	function removeDocumentation( $shopper_program_id, $id, $period_id ) {
		$shopper_program_id = (int)$shopper_program_id;
		$id = (int)$id;
		$period_id = (int)$period_id;

		if ( $shopper_program_id && $id && $this->deleteDocumentation( $id, $period_id ) ) {
			$sql = "DELETE FROM shopper_documentation WHERE shopper_program_id = ? AND id = ? AND period_id = ?";
      $stmt = $this->db->prepare( $sql );

			if ( $stmt->execute( array( $shopper_program_id, $id, $period_id ) ) ) {
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

	function deleteDocumentation( $id, $period_id ) {
		$id = (int)$id;
		$period_id = (int)$period_id;

		if ( $id && $period_id ) {
			// delete the document
			$sql = "SELECT document FROM shopper_documentation WHERE $this->idField = ? AND period_id = ?";
			if ( $docs = $this->db->query( $sql, array( $id, $period_id ) ) ) {
				if ( $row = $this->db->fetch( $docs ) ) {
					if ( $row['document'] != '' ) {
						if ( $row['document'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation_docs/" . $row['document'] ) ) {
							if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation_docs/" . $row['document'] ) ) {
								return SUCCESS;
							} else {
								$this->error = 'Could not delete file from (/assets/shopper_documentation_docs/) directory.';
								return ERROR;
							}
						}
					} else {
						return SUCCESS;
					}
				}
			}

			// delete the image of the document
			$sql = "SELECT image FROM shopper_documentation WHERE $this->idField = ? AND period_id = ?";

			if ( $docs = $this->db->query( $sql, array( $id, $period_id ) ) ) {
				if ( $row =  $this->db->fetch( $docs ) ) {
					if ( $row['image'] != '' ) {
						if ( $row['image'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation/" . $row['image'] ) ) {
							if ( unlink( $_SERVER['DOCUMENT_ROOT'] . "/assets/shopper_documentation/" . $row['image'] ) ) {
								return SUCCESS;
							} else {
								$this->error = 'Could not delete file from (/assets/shopper_documentation/) directory.';
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

	function removeRelatedLinks( $shopper_program_id, $period_id, $id ) {
		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$id = (int)$id;

		if ( $shopper_program_id && $period_id && $id ) {
			$sql = "DELETE FROM shopper_related_links WHERE shopper_program_id = ? AND period_id = ? AND id = ?";
      $stmt = $this->db->prepare( $sql );

			if ( $stmt->execute( array( $shopper_program_id, $period_id, $id ) ) ) {
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

	function removeUpdates( $shopper_program_id, $period_id, $id ) {
		$shopper_program_id = (int)$shopper_program_id;
		$period_id = (int)$period_id;
		$id = (int)$id;

		if ( $shopper_program_id && $period_id && $id ) {
			$sql = "DELETE FROM shopper_program_updates WHERE shopper_program_id = ? AND period_id = ? AND id = ?";
      $stmt = $this->db->prepare( $sql );

			if ( $stmt->execute( array( $shopper_program_id, $period_id, $id ) ) ) {
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

	function removeBins( $shopper_program_id, $id ) {
		$shopper_program_id = (int)$shopper_program_id;
		$id = (int)$id;

		if ( $shopper_program_id && $id ) {
			$sql = "DELETE FROM shopper_program_bins WHERE shopper_program_id = ? AND id = ?";
      $stmt = $this->db->prepare( $sql );

			if ( $stmt->execute( array( $shopper_program_id, $id ) ) ) {
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