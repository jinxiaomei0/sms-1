<?php
	// Include the CRC Object class that needs to
	// extended by all classes. This is the super
	// class.
	include_once('crc_constants.mod.php');
	include_once('crc_object.cls.php');
	include_once('crc_mysql.cls.php');

	//******************************************
	// Name: crc_object
	//******************************************
	//
	// Desc: The Staff Object
	// Version: 1.0.0
	//
	//******************************************

	class crc_staff extends crc_object {

		var $m_sql;
		var $m_workexdata;
		var $m_startmonth;
		var $m_endmonth;
		var $m_year;
		var $m_position;
		var $m_desc;
		var $m_score;
		var $m_status;		
		var $m_uid;		
		var $m_dure;		

		var $m_data;
		var $m_courseid;
		var $m_coursename;
		var $m_coursedesc;
		var $m_startdate;
		var $m_enddate;
		var $m_daytime;
		var $m_roomid;
		var $m_roomname;
		var $m_roomdesc;
		var $m_active;
		var $m_venueid;
		var $m_coursefee;
		var $m_profileid;
		var $m_scheduleid;
		var $m_evaluation;
		
		var $m_firstname;
		var $m_lastname;
		var $m_gender;
		var $m_email;
		var $m_phone;
		
		var $m_courselist;
		var $m_teacherlist;
		var $m_studentlist;
		
		function crc_staff($debug) {
			//******************************************
			// Initialization by constructor
			//******************************************
			$this->classname = 'crc_staff';
			$this->classdescription = 'Handle staff actions.';
			$this->classversion = '1.0.0';
			$this->classdate = 'September 22th, 2010';
			$this->classdevelopername = 'james';
			$this->classdeveloperemail = 'gjz22cn@hotmail.com';
			$this->_DEBUG = $debug;
			
            $this->m_uid = 1;
			$this->m_workexdata['syear'] = '';
			$this->m_workexdata['smonth'] = '';
			$this->m_workexdata['eyear'] = '';
			$this->m_workexdata['emonth'] = '';
			$this->m_workexdata['year'] = '';
			$this->m_workexdata['position'] = '';
			$this->m_workexdata['desc'] = '';
			$this->m_workexdata['score'] = '';

			if ($this->_DEBUG) {
				echo "DEBUG {crc_staff::constructor}: The class \"crc_staff\" was successfuly created. <br>";
				echo "DEBUG {crc_staff::constructor}: Running in debug mode. <br>";
			}

		}

		function fn_setworkex($post) {
			//******************************************
			// Update the workex information
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_staff::fn_setworkex}: Setting workex information <br>";
			}

			$db = new crc_mysql($this->_DEBUG);
			$db->fn_connect();
			$result = true;
			if ($db->m_mysqlhandle != false) {
				echo "DEBUG {crc_staff::fn_setworkex}: Setting workex information 1<br>";
				if(isset($post['syear']) && isset($post['smonth'])) {
					$this->m_startmonth = $post['syear'] . '.' . $post['smonth'];
					$this->m_workexdata['syear'] = $post['syear'];
					$this->m_workexdata['smonth'] = $post['smonth'];
				} else {
					$this->m_startmonth = "";
					$this->m_workexdata['syear'] = "";
					$this->m_workexdata['smonth'] = "";
				}
				if(isset($post['eyear']) && isset($post['emonth'])) {
					$this->m_endmonth = $post['eyear'] . '.' . $post['emonth'];
					$this->m_workexdata['eyear'] = $post['eyear'];
					$this->m_workexdata['emonth'] = $post['emonth'];
				} else {
					$this->m_endmonth = "";
					$this->m_workexdata['eyear'] = "";
					$this->m_workexdata['emonth'] = "";
				}
				if(isset($post['year'])) {
					$this->m_year = $post['year'];
				} else {
					$this->m_year = "";
				}
				if(isset($post['position'])) {
					$this->m_position = $post['position'];
				} else {
					$this->m_position = "";
				}
				if(isset($post['desc'])) {
					$this->m_desc = $post['desc'];
				} else {
					$this->m_desc = "";
				}
				$this->m_status = 'In progress';
				if(isset($post['score'])) {
					$this->m_score = $post['score'];
				} else {
					$this->m_score = "";
				}
				
				//this data should be restored if something goes wrong
				echo "DEBUG {crc_staff::fn_setworkex}: Setting workex information 2<br>";
				
				if( ($this->m_year == "") || 
				    ($this->m_workexdata['syear'] == "") ||
				    ($this->m_workexdata['smonth'] == "") ||
				    ($this->m_workexdata['eyear'] == "") ||
				    ($this->m_workexdata['emonth'] == "") ||
				    ($this->m_position == "") ||
				    ($this->m_desc == "") ) {
					return false;
				}
				echo "DEBUG {crc_staff::fn_setworkex}: Setting workex information 3<br>";

                $this->m_dure = $this->m_startmonth . '~' . $this->m_endmonth;

				//set course information
                $this->m_sql = 'insert into ' . MYSQL_WORKEX_TBL . '(' .
                    'workex_uid, workex_dure, ' .
                    'workex_year, workex_posi, ' .
                    'workex_desc, workex_score) ' .
                    'values("' . $this->m_uid. '","' . $this->m_dure. '","' . $this->m_year.
                    '","' . $this->m_position. '","' . $this->m_desc. '","' . $this->m_score. '")';
				$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);			
				if (mysql_errno() != 0) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_staff::fn_setworkex}: Could not update/insert workex information. <br>';
					}
					$db->fn_freesql($resource);
					$db->fn_disconnect();
					$this->lasterrmsg = "Could not update/insert workec information";					
					return false;
				}
				
				$db->fn_freesql($resource);
				$db->fn_disconnect();
			} else {
				$db->fn_disconnect();
				$result = false;
				$this->lasterrmsg = "Cannot connect to MySQL database";
			}
			return $result;
		}

		function fn_getcourseid($db, $course) {
			//******************************************
			// Get course ID
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getcourseid}: Retreiving the course id for " . $course . " <br>";
			}

			$this->m_courseid = 0;
			if ($db->m_mysqlhandle != false) {

				$this->m_sql = 'select course_id from ' . MYSQL_COURSES_TBL .
												' where (course_name = "' . $course . '")';

				$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
				if (mysql_num_rows($resource) > 0) {
					$row = mysql_fetch_row($resource);
					$this->m_courseid = $row[0];
				} else {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getcourseid}: The sql command returned nothing. <br>';
					}
				}				
			} 
			return $this->m_courseid;
		}

		function fn_getroomid($db, $room) {
			//******************************************
			// Get room ID
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getroomid}: Retreiving the room id for " . $room . " <br>";
			}

			$this->m_roomid = 0;
			if ($db->m_mysqlhandle != false) {

				$this->m_sql = 'select room_id from ' . MYSQL_ROOMS_TBL .
												' where (room_name = "' . $room . '")';
				$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
				if (mysql_num_rows($resource) > 0) {
					$row = mysql_fetch_row($resource);
					$this->m_roomid = $row[0];
				} else {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getroomid}: The sql command returned nothing. <br>';
					}
				}
			}
			return $this->m_roomid;
		}
		
		function fn_getscheduleid($db, $courseid) {
			//******************************************
			// Get schedule ID
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getscheduleid}: Retreiving the schedule id for " . $courseid . " <br>";
			}

			if ($db->m_mysqlhandle != 0) {

				$this->m_sql = 'select schedule_id from ' . MYSQL_SCHEDULE_TBL .
							    ' where (schedule_course_id = "' . $courseid . '")';

				$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
				if (mysql_num_rows($resource) > 0) {
					$row = mysql_fetch_row($resource);
					$this->m_scheduleid = $row[0];
				} else {
					$this->m_scheduleid = 0;
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getscheduleid}: The sql command returned nothing. <br>';						
					}
				}
				return $this->m_scheduleid;
			} else {
				return 0;
			}
		}
		
		function fn_getnewworkex($db) {
			
			//******************************************
			// Get the active course list
			//******************************************
			
			if ($this->_DEBUG) {
				echo "DEBUG {crc_staff::fn_getnewworkex}: Getting new workex <br>";
			}

			$closedb = false;
			if($db == null) {
				$db = new crc_mysql($this->_DEBUG);
				$db->fn_connect();
				$closedb = true;
			}
			if ($db->m_mysqlhandle != 0) {
				$this->m_sql = 'select * ' .
								'from ' . MYSQL_COURSES_TBL . 
								' where (course_active = "0") ' .
								'order by course_name asc';				
				$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);											
				if (mysql_num_rows($resource) > 0) {					
					$index = 0;
					$this->m_courselist = '';					
					while ($row = mysql_fetch_array($resource)) {						
						if(strlen($row[2])>0) {
							$this->m_courselist[$index]['cnamedesc'] = $row[1] . ', ' . $row[2];
						} else {
							$this->m_courselist[$index]['cnamedesc'] = $row[1];
						}
						$this->m_courselist[$index]['courseid'] = $row[0];					
						$index++;
					}
				} else {
					$this->m_courselist = null;
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getcourselist}: The sql command returned nothing. <br>';
					}
				}
				if ($closedb == true) {
					$db->fn_freesql($resource);
					$db->fn_disconnect();
				}
				return $this->m_courselist;
			} else {
				if ($closedb == true) {
					$db->fn_disconnect();
				}
				return null;
			}
		}

		function fn_getteacherlist($db) {
			
			//******************************************
			// Get the teacher list
			//******************************************
			
			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getteacherlist}: Getting course list <br>";
			}

			$closedb = false;
			if($db == null) {
				$db = new crc_mysql($this->_DEBUG);
				$db->fn_connect();
				$closedb = true;
			}
			$this->m_teacherlist = null;
			if ($db->m_mysqlhandle != false) {
				$this->m_sql = 'select profile_id, profile_firstname, profile_lastname ' .
								'from ' . MYSQL_PROFILES_TBL . ' as p' . 
								' where (profile_active = "0") and ' .
								'(profile_role_id between 1 and 2) ' .
								'order by p.profile_lastname';
				$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);											
				if (mysql_num_rows($resource) > 0) {					
					$index = 0;
					$this->m_teacherlist = '';					
					while ($row = mysql_fetch_array($resource)) {						
						$this->m_teacherlist[$index]['lastfirstname'] = $row[2] . ', ' . $row[1];//lastname, firstname
						$this->m_teacherlist[$index]['profileid'] = $row[0];
						$index++;
					}
				} else {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getteacherlist}: The sql command returned nothing.<br>';
					}
				}
				if ($closedb == true) {
					$db->fn_freesql($resource);
					$db->fn_disconnect();
				}
			} else {
				if ($closedb == true) {
					$db->fn_disconnect();
				}
			}
			return $this->m_teacherlist;
		}		

		function fn_getstudentlist($db) {
			
			//******************************************
			// Get registered student list
			//******************************************
			
			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getstudentlist}: Getting registered student list<br>";
			}

			$closedb = false;
			if($db == null) {
				$db = new crc_mysql($this->_DEBUG);
				$db->fn_connect();
				$closedb = true;
			}
			if ($db->m_mysqlhandle != false) {
				$this->m_sql = 'select profile_uid, profile_firstname, profile_lastname ' .
								'from ' . MYSQL_PROFILES_TBL . ' as p' . 
								' where (profile_active = "0") and ' .
								'(profile_role_id = "3") ' .
								'order by p.profile_lastname';
				$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);											
				if (mysql_num_rows($resource) > 0) {					
					$index = 0;
					$this->m_studentlist = '';					
					while ($row = mysql_fetch_array($resource)) {						
						$this->m_studentlist[$index]['lastfirstname'] = $row[2] . ', ' . $row[1];//lastname, firstname
						$this->m_studentlist[$index]['profileuid'] = $row[0];
						$index++;
					}
				} else {
					$this->m_studentlist = null;
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getstudentlist}: The sql command returned nothing.<br>';
					}
				}
				if ($closedb == true) {
					$db->fn_freesql($resource);
					$db->fn_disconnect();
				}
				return $this->m_studentlist;
			} else {
				if ($closedb == true) {
					$db->fn_disconnect();
				}
				return null;
			}
		}		
		
		function fn_getprofileid($db, $firstname, $lastname) {
			//******************************************
			// Get profile ID
			//******************************************

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_getprofileid}: Retreiving the schedule id for " . $firstname . " " . $lastname . "<br>";
			}

			if ($db->m_mysqlhandle != false) {

				$this->m_sql = 'select profile_id from ' . MYSQL_PROFILES_TBL .
							    ' where (profile_firstname = "' . $firstname . 
							    '" and profile_lastname = "' . $lastname . '")';

				$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
				if (mysql_num_rows($resource) > 0) {
					$row = mysql_fetch_row($resource);
					$this->m_profileid = $row[0];
				} else {
					$this->m_profileid = 0;
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_getprofileid}: The sql command returned nothing. <br>';
					}
				}
				return $this->m_profileid;
			} else {
				return 0;
			}
		}
		
		function fn_setstudent($post) {

			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_setstudent}: Setting student information <br>";
			}
				
			$db = new crc_mysql($this->_DEBUG);
			$db->fn_connect();
			$result = true;
			if ($db->m_mysqlhandle != false) {
				if(isset($post['fname'])) {
					$this->m_firstname = $post['fname'];
				} else {
					$this->m_firstname = "";
				}
				if(isset($post['lname'])) {
					$this->m_lastname = $post['lname'];
				} else {
					$this->m_lastname = "";
				}
				if(isset($post['gender']))
				{
					$this->m_gender = strtoupper($post['gender'][0]);
				} else {
					$this->m_gender = "";
				}
				if(isset($post['email'])) {
					$this->m_email = $post['email'];
				} else {
					$this->m_email = "";
				}
				if(isset($post['lcode']) && isset($post['lprefix']) && isset($post['lpostfix'])) {
					$this->m_phone = $post['lcode'] . $post['lprefix'] . $post['lpostfix'];
					$this->m_data['lcode'] = $post['lcode'];
					$this->m_data['lprefix'] = $post['lprefix'];
					$this->m_data['lpostfix'] = $post['lpostfix'];
				} else {
					$this->m_phone = "";
					$this->m_data['lcode'] = "";
					$this->m_data['lprefix'] = "";
					$this->m_data['lpostfix'] = "";
				}

				//this data should be restored if something goes wrong
				$this->m_data['fname'] = $this->m_firstname;
				$this->m_data['lname'] = $this->m_lastname;
				$this->m_data['gender'] = $this->m_gender;
				$this->m_data['email'] = $this->m_email;

				if( ($this->m_firstname == "") || ($this->m_lastname == "") ) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setstudent}: First or last name is empty.<br>';
					}
					$db->fn_disconnect();
					$this->lasterrmsg = "First or last name is empty";
					return false;
				}
				
				//check if at least one course has been selected
				$this->fn_getcourselist($db);
				$courseselected = false;
				for($i = 0; $i < count($this->m_courselist); $i++) {
					if(isset($post['course' . $i]) && (strtolower($post['course' . $i]) == "on")) {
						$courseselected = true;
						break;
					}
				}
				if($courseselected == false) {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setstudent}: No course has been selected.<br>';
					}
					$db->fn_disconnect();
					$this->lasterrmsg = "No course has been selected";
					return false;
				}
					
				//check for user name
				$this->m_sql = 'select * ' .
							'from ' . MYSQL_PROFILES_TBL . 
							' where (profile_firstname = "' . $this->m_firstname .
							'" and profile_lastname = "' . $this->m_lastname . '")';
				$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
				if (mysql_num_rows($resource) == 0) {					
					//insert student
					$this->m_sql = 'insert into ' . MYSQL_PROFILES_TBL . '(' .
									'profile_uid, profile_firstname, profile_lastname, ' .
									'profile_email, profile_gender, profile_phone_land, ' .
									'profile_role_id, profile_rdn) ' .
									'values("' . $this->m_firstname . $this->m_lastname .
									'","' . $this->m_firstname .
									'","' . $this->m_lastname .
									'","' . $this->m_email .
									'","' . $this->m_gender .
									'","' . $this->m_phone . 
									'","3","ou=don mills,ou=toronto,ou=ontario,ou=canada,o=crc world")';
					$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
					if (mysql_affected_rows() <= 0) {
						if ($this->_DEBUG) {
							echo 'ERROR {crc_admin::fn_setstudent}: Could not insert student information. <br>';
						}
						$db->fn_freesql($resource);
						$db->fn_disconnect();
						$this->lasterrmsg = "Could not insert student information";
						return false;
					}

					//initialize m_profileid
					$this->fn_getprofileid($db, $this->m_firstname, $this->m_lastname);
					if ($this->m_profileid == 0) {
						if ($this->_DEBUG) {
							echo 'ERROR {crc_admin::fn_setstudent}: Could not get profile id. <br>';
						}
						$db->fn_freesql($resource);
						$db->fn_disconnect();
						$this->lasterrmsg = "Could not get profile id";
						return false;
					}

					//initialize m_scheduleid using selected course(s)
					if ($this->fn_setstudentschedule($db, $post, $this->m_profileid) == false) {
						if ($this->_DEBUG) {
							echo 'ERROR {crc_admin::fn_setstudent}: Cannot set student schedule. <br>';
						}
						$db->fn_freesql($resource);
						$db->fn_disconnect();
						$this->lasterrmsg = "Cannot set student schedule";
						return false;
					}
				} else {
					if ($this->_DEBUG) {
						echo 'ERROR {crc_admin::fn_setstudent}: User ' . $this->m_firstname . ' ' . $this->m_lastname . ' already exists in database.<br>';
					}
					$this->lasterrmsg = "User " . $this->m_firstname . " " . $this->m_lastname . " already exists in database.<br>Use \"Edit Student\" menu if you want to modify this user.";
					$result = false;
				}
				
				$db->fn_freesql($resource);
				$db->fn_disconnect();
			} else {
				$db->fn_disconnect();
			}
			return $result;
		}
		
		function fn_setstudentschedule($db, $post, $profileid) {
			
			//***************************************************
			// Helper function for inserting the student schedule
			//***************************************************
			
			if ($this->_DEBUG) {
				echo "DEBUG {crc_admin::fn_setstudentschedule}: Setting student schedule <br>";
			}
			
			$closedb = false;
			if($db == null) {
				$db = new crc_mysql($this->_DEBUG);
				$db->fn_connect();
				$closedb = true;
			}
			if ($db->m_mysqlhandle == false) {
				$db->fn_disconnect();
				return false;
			}
			
			$this->fn_getcourselist($db);
			$this->m_scheduleid = 0;
            $resource = 0;
			for($i = 0; $i < count($this->m_courselist); $i++) {
				if(isset($post['course' . $i]) && (strtolower($post['course' . $i]) == "on")) {
					$this->fn_getscheduleid($db, $this->m_courselist[$i]['courseid']);
					if ($this->m_scheduleid == 0) {
						if ($this->_DEBUG) {
							echo 'ERROR {crc_admin::fn_setstudentschedule}: Could not get schedule id. <br>';
						}
						if (is_resource($resource)) {
							$db->fn_freesql($resource);
						}
						$db->fn_disconnect();
						return false;
					}
					//check if the student has been already assigned to this course
					$this->m_sql = 'select * ' .
							'from ' . MYSQL_STUDENT_SCHEDULE_TBL . 
							' where (student_schedule_profile_id = "' . $profileid .
							'" and student_schedule_schedule_id = "' . $this->m_scheduleid . '")';
					$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
					if (mysql_num_rows($resource) == 0) {
						//set student schedule
						$this->m_sql = 'insert into ' . MYSQL_STUDENT_SCHEDULE_TBL . '(' .
									'student_schedule_profile_id, ' .
									'student_schedule_schedule_id, ' .
									'student_schedule_questions) ' .
									'values("' . $profileid .
									'","' . $this->m_scheduleid .
									'","1")';
						$resource = $db->fn_runsql(MYSQL_DB, $this->m_sql);
						if (mysql_affected_rows() <= 0) {
							if ($this->_DEBUG) {
								echo 'ERROR {crc_admin::fn_setstudentschedule}: Could not insert student schedule information. <br>';
							}
							$db->fn_freesql($resource);
							$db->fn_disconnect();
							return false;
						}
					}
				}
			}
			
			if ($closedb == true) {
				if (is_resource($resource)) {
					$db->fn_freesql($resource);
				}
				$db->fn_disconnect();
			}
			
			return true;
		}
	}
?>