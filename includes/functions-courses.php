<?php

// courses functions

function add_course()
{
	if(isset($_POST['addcourse']))
	{
		if(!empty($_POST['courseno']) && 
		!empty($_POST['coursename']) && 
		is_numeric($_POST['totalhrs']) && 
		is_numeric($_POST['lecthrs']) && 
		is_numeric($_POST['labhrs']) && 
		is_numeric($_POST['credits']) && 
		!empty($_POST['coursedesc']) && $_POST['depts'] != 0)
		{
			$courseno = strtoupper(mysql_prep($_POST['courseno']));
			$query = "select * from courses where coursenum = '$courseno'";
			$result = mysql_query($query);
			$num_of_rows = mysql_num_rows($result);
			
			if($num_of_rows == 0)
			{
				$coursename = ucwords(strtolower(mysql_prep($_POST['coursename'])));
				$totalhrs = mysql_prep($_POST['totalhrs']);
				$lecthrs = mysql_prep($_POST['lecthrs']);
				$labhrs = mysql_prep($_POST['labhrs']);
				$credits = mysql_prep($_POST['credits']);
				$coursedesc = clean_up_ms(mysql_prep($_POST['coursedesc']));
				$dept = mysql_prep($_POST['depts']);
				
				$query = "insert into courses values('', '$courseno', '$coursename', '$coursedesc', '$totalhrs', '$lecthrs', '$labhrs', '$credits', '$dept')";
				mysql_query($query);
				$lastid = mysql_insert_id();
				add_competencies($_POST, $lastid);
				add_prerequisites($_POST, $lastid);
				
				print "<div class='feedback success'>record added</div>";
			}
			
			else { print "<div class='feedback error'>This course already exists.</div>"; }
		}
		else { print "<div class='feedback error'>Please fill all the fields</div>"; }
	}
}

function add_competencies($data, $id)
{
	$counter = 1;
	foreach($data as $key => $value)
	{
		$test = substr($key, 0, 4);
		if($test == 'comp')
		{
			if(!empty($value))
			{
				$value = clean_up_ms(mysql_prep($value));
				$query = "insert into competencies values('', '$id', '', '$value', '0', '$counter')";
				mysql_query($query);
				$counter++;
			}
		}
	}
}

function add_prerequisites($data, $id)
{
	$counter = 1;
	foreach($data as $key => $value)
	{
		$test = substr($key, 0, 6);
		if($test == 'prereq')
		{
			if(!empty($value))
			{
				$value = clean_up_ms(mysql_prep($value));
				$query = "insert into prereqs values('', '$id', '$value', '$counter')";
				mysql_query($query);
				$counter++;
			}
		}
	}
}

function edit_course()
{
	if(isset($_POST['editcourse']))
	{
		if(!empty($_POST['courseno']) && 
		!empty($_POST['coursename']) && 
		is_numeric($_POST['totalhrs']) && 
		is_numeric($_POST['lecthrs']) && 
		is_numeric($_POST['labhrs']) && 
		is_numeric($_POST['credits']) && 
		!empty($_POST['coursedesc']) && $_POST['depts'] != 0)
		{
			$id = $_POST['id'];
			$courseno = strtoupper(mysql_prep($_POST['courseno']));
			$query = "select * from courses where coursenum = '$courseno' and id != '$id'";
			$result = mysql_query($query);
			$num_of_rows = mysql_num_rows($result);
			
			if($num_of_rows == 0)
			{
				$coursename = ucwords(strtolower(mysql_prep($_POST['coursename'])));
				$totalhrs = mysql_prep($_POST['totalhrs']);
				$lecthrs = mysql_prep($_POST['lecthrs']);
				$labhrs = mysql_prep($_POST['labhrs']);
				$credits = mysql_prep($_POST['credits']);
				$coursedesc = clean_up_ms(mysql_prep($_POST['coursedesc']));
				$dept = mysql_prep($_POST['depts']);
				
				$query = "update courses set coursenum = '$courseno', name = '$coursename', description = '$coursedesc', totalhrs = '$totalhrs', lecthrs = '$lecthrs', labhrs = '$labhrs', credit = '$credits', dept = '$dept' where id='$id'";
				mysql_query($query);
				
				$del_query = "DELETE FROM competencies WHERE course_id='$id' AND type='0'";
				mysql_query($del_query);
				add_competencies($_POST, $id);
				
				$del_query = "DELETE FROM prereqs WHERE course_id='$id'";
				mysql_query($del_query);
				add_prerequisites($_POST, $id);			
				
				print "<div class='feedback success'>record edited</div>";
			}
			
			else { print "<div class='feedback error'>This course already exists.</div>"; }
		}
		else { print "<div class='feedback error'>Please fill all the fields</div>"; }
	}
}

function edit_competencies()
{
	if(isset($_GET['editcourse']))
	{
		$courseid = $_GET['editcourse'];
		$query ="select competency, ordr from competencies where course_id='$courseid' and type='0' order by ordr";
		$result = mysql_query($query);
		$numrows = mysql_num_rows($result);
		if($numrows == 0)
		{
			print "<p id='input1' class='clonedInput'>\n";
			print "Competency: <input id='comp1' name='comp1' type='text' />\n";
			print "</p>";
		}
		else
		{
			while($row = mysql_fetch_row($result))
			{
				list($competency, $order)=$row;
				print "<p id='input$order' class='clonedInput'>\n";
				print "<label for='comp1'>Competency</label>  <input id='comp$order' name='comp$order' type='text' value='$competency' />\n";
				print "</p>\n";
			}
		}
		print "<p><input type='button' id='addComp' value='add another competency' /></p>\n";
	}	
}

function edit_prereqs()
{
	if(isset($_GET['editcourse']))
	{
		$courseid = $_GET['editcourse'];
		$query ="select prereq, ordr from prereqs where course_id='$courseid' order by ordr";
		$result = mysql_query($query);
		$numrows = mysql_num_rows($result);
		if($numrows == 0)
		{
			print "<p id='prereq-input1' class='clonedPrereq'>\n";
			print "<label for='prereq1'>Prerequisite</label> <input id='prereq1' name='prereq1' type='text' />\n";
			print "</p>";
		}
		else
		{
			while($row = mysql_fetch_row($result))
			{
				list($prereq, $order)=$row;
				print "<p id='prereq-input$order' class='clonedPrereq'>\n";
				print "<label for='prereq$order'>Prerequisite</label> <input id='prereq$order' name='prereq$order' type='text' value='$prereq' />\n";
				print "</p>\n";
			}
		}
		print "<p><input type='button' id='addPrereq' value='add another prerequiste' /></p>\n";
	}	
}

function display_course_list()
{
	$query = "select id, coursenum, name from courses order by coursenum";
	$result = mysql_query($query);
	print "<ul>";
	while($row = mysql_fetch_row($result))
	{
		list($id, $courseno, $name) = $row;
		print "<li><a href='courses.php?courseid=$id'>$courseno $name</a></li>";
	}
	print "</ul>";
}

function course_item($item)
{
	if(isset($_GET['courseid']))
	{
		$id = $_GET['courseid'];
		
		$query = "select * from courses where id = '$id'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		
		$field_names = array("id", "number", "name", "desc", "totalhrs", "lecthrs", "labhrs", "credit", "dept");
		$profile_info = array_combine($field_names, $row);
		$the_item = $profile_info[$item];
		return $the_item;
	}
	
	elseif(isset($_GET['editcourse']))
	{
		$id = $_GET['editcourse'];
		
		$query = "select * from courses where id = '$id'";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		
		$field_names = array("id", "number", "name", "desc", "totalhrs", "lecthrs", "labhrs", "credit", "dept");
		$profile_info = array_combine($field_names, $row);
		$the_item = $profile_info[$item];
		return $the_item;
	}
}

function output_core_competencies()
{
	if(isset($_GET['courseid']))
	{
		$id = $_GET['courseid'];
		
		$query = "select competency from competencies where course_id='$id' and type='0' order by ordr";
		$result = mysql_query($query);
		
		$num_rows = mysql_num_rows($result);
		if($num_rows > 0)
		{
			print "<h3>Course Competencies</h3>";
			print "<ul>";
			
			while($row = mysql_fetch_row($result))
			{
				list($competency) = $row;
				print "<li>$competency</li>";
			}
			
			print "</ul>";
		}
	}
}

function output_prerequisites()
{
	if(isset($_GET['courseid']))
	{
		$id = $_GET['courseid'];
		
		$query = "select prereq from prereqs where course_id='$id' order by ordr";
		$result = mysql_query($query);
		
		$num_rows = mysql_num_rows($result);
		if($num_rows > 0)
		{
			print "<h3>Course Prerequistes</h3>";
			print "<ul>";
			
			while($row = mysql_fetch_row($result))
			{
				list($prereq) = $row;
				print "<li>$prereq</li>";
			}
			
			print "</ul>";
		}
	}
}

function display_course_info()
{
	if(isset($_GET['courseid']))
	{
		include('includes/courses/course-desc.php');
	}
}

?>