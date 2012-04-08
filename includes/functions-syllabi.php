<?php

function output_term_list()
{
	$query = "select id, term, year from terms order by year desc, term desc limit 0, 4";
	$result = mysql_query($query);
	$termnames = array("", "Winter", "Spring", "Summer", "Fall");
	
	while ($row = mysql_fetch_row($result))
	{
		list($id, $term, $year) = $row;
		print "<option value='$id'>$termnames[$term] $year</option>\n";
	}
}

function output_course_select_list()
{
	$query = "select courses.id, coursenum, courses.name, abbrv from courses left join depts on courses.dept = depts.id order by abbrv asc, courses.name asc";
	$result = mysql_query($query);
	
	while ($row = mysql_fetch_row($result))
	{
		list($id, $coursenum, $name, $abbrv) = $row;
		print "<option value='$id'>$abbrv $name $coursenum</option>\n";
	}
}

function add_syllabus()
{
	if(isset($_POST['addsyll']))
	{
		if(!empty($_POST['term']) && !empty($_POST['course']))
		{
			$userid = $_POST['userid'];
			$termid = $_POST['term'];
			$courseid = $_POST['course'];
			$classtype = $_POST['classtype'];
			
			$query = "insert into classes values('', '$courseid', '$userid', '$termid', '$classtype', '0')";
			//print $query;
			mysql_query($query);
			
			$lastid = mysql_insert_id();
			
			switch ($classtype)
			{
				case "0": $meetings = 11;  break;
				case "1": $meetings = 11;  break;
				default: $meetings = 11;
			}
			
			for($i=0; $i<$meetings; $i++)
			{
				$counter = $i+1;
				$query = "insert into activities values('', '$lastid', '$counter', '' )";
				mysql_query($query);
			}
			
			print "<div class='feedback success'>Syllabus record successfully created.</div>";
		}
		else { print "<div class='feedback error'>ERROR: Term, Course, and Class type fields are required.</div>"; }
	}
}

function display_user_terms()
{
	$userid = $_SESSION['id'];
	$counter = 1;
	$section_init = 10000000;
	$termnames = array("", "Winter", "Spring", "Summer", "Fall");
	
	$query = "select classes.id, terms.year, terms.term, courses.name, courses.coursenum, classes.status 
				from classes left join terms on classes.term_id = terms.id 
				left join courses on classes.course_id = courses.id where user_id = '$userid'
				order by terms.year desc, terms.term desc, courses.coursenum asc";

	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	if($numrows > 0)
	{	
		while($row = mysql_fetch_row($result))
		{
			list($id, $year, $term, $coursename, $coursenum, $status) = $row;
			
			$section = $year.$term;
			
			if($section < $section_init && $section_init != 10000000)
			{
				print "</div>\n";
				print "</div>\n";
				
			}
			
			if($section < $section_init)
			{
				print "<div class='container frame'>\n";
				print "<h2>$termnames[$term] $year</h2>\n";
				print "<div ";
				if($counter < 3) { print "class='opened'>\n"; }
				else { print "class='closed'>\n"; }
				$counter++;
			}
			
			print "<p><a href='index.php?sylledit=$id'>$coursenum $coursename</a></p>\n";
			
			$section_init = $section;
		}
		print "</div>\n";
		print "</div>\n";
	}
}

function syll_info($item, $classid)
{
	$query = "select users.fname, users.lname, users.phone, users.email,courses.id, courses.name, 
	courses.coursenum, classes.status, terms.year, terms.term 
	from classes left join terms on classes.term_id = terms.id 
	left join courses on classes.course_id = courses.id left join users 
	on classes.user_id = users.id where classes.id = '$classid'";
	
	$result = mysql_query($query);
	
	$row = mysql_fetch_row($result);
	list($fname, $lname, $phone, $email, $courseid, $coursename, $coursenum, $statusnum, $year, $termnum) = $row;
	$termnames = array('', 'Winter', 'Spring', 'Summer', 'Fall');
	$statusnames = array('Draft', 'Approved', 'Published');
	$term = $termnames[$termnum];
	$status = $statusnames[$statusnum];
	
	$row = array($fname, $lname, $phone, $email, $courseid, $coursename, $coursenum, $status, $year, $term);
	
	$field_names = array("fname", "lname", "phone", "email", "courseid", "course", "coursenum", "status", "year", "term");
	$syllabus_info = array_combine($field_names, $row);
	$the_item = $syllabus_info[$item];
	return $the_item;
}

function edit_addtl_competencies($id, $type)
{
	$query ="select competency, ordr from competencies where class_id='$id' and type='$type' order by ordr";
	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	if($numrows == 0)
	{
		print "<p id='input1' class='clonedInput'>\n";
		print "<label for='comp1'>Competency</label><br />\n";
		print "<input id='comp1' name='comp1' type='text' />\n";
		print "</p>";
	}
	else
	{
		while($row = mysql_fetch_row($result))
		{
			list($competency, $order)=$row;
			print "<p id='input$order' class='clonedInput'>\n";
			print "<label for='comp$order'>Competency</label><br />\n";
			print "<input id='comp$order' name='comp$order' type='text' value='$competency' />\n";
			print "</p>\n";
		}
	}
	print "<p><input type='button' id='addComp' value='add another competency' /></p>\n";
}

function edit_addtn_grade_policies($id, $type)
{	
	$query = "select policy, ordr from gradingpolicies where class_id='$id' and type='$type' order by ordr";
	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	
	if($numrows > 0 )
	{
		while($row = mysql_fetch_row($result))
		{
			list($policy, $order)=$row;
			print "<p id='policyinput$order' class='clonedPolicy'>\n";
			print "<label for='policy$order'>Policy</label><br />\n";
			print "<input id='policy$order' name='policy$order' type='text' class='policyfield' value='$policy' />\n";
			print "</p>\n";
		}
		print "<p><input type='button' id='addPolicy' value='add another grade policy' /></p>\n";
	}
	
	else
	{
		print "<p id='policyinput1' class='clonedPolicy'>\n";
		print "<label for='policy1'>Policy</label><br />\n";
		print "<input id='policy1' name='policy1' type='text' class='policyfield' />\n";
		print "</p>\n";
		print "<p><input type='button' id='addPolicy' value='add another grade policy' /></p>\n";
	}
}

function edit_meeting_times($classid)
{
	$query = "select type from classes where id = '$classid'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$classtype = $row[0];
	
	switch($classtype)
	{
		case "0": include('syllabi/once-per-week.php');  break;
		case "1": include('syllabi/twice-per-week.php');  break;
		default: include('syllabi/once-per-week.php');
	}
	
}

function display_activity_form($classid)
{
	$query = "select type from classes where id = '$classid'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$classtype = $row[0];
	
	switch($classtype)
	{
		case "0": include('syllabi/activities-once-per-week.php');  break;
		case "1": include('syllabi/activities-twice-per-week.php');  break;
		default: include('syllabi/activities-once-per-week.php');
	}
}

function edit_eval_process($id)
{
	$query = "select descrip, percent, ordr from evalscales where class_id='$id' order by ordr";
	
	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	
	if($numrows > 0)
	{
		while($row = mysql_fetch_row($result))
		{
			list($description, $percent, $order) = $row;
			print "<p id='eval$order' class='clonedeval'>";
			print "<input type='text' value='$description' name='desc$order' id='desc$order' class='description' />";
			print "<input type='text' value='$percent' name='perc$order' id='perc$order' class='percent' />%";
			print "</p>\n";
		}
		print "<p><input type='button' id='addEval' value='add an evaluation item' /></p>\n";
	}
	else
	{
		print "<p id='eval1' class='clonedeval'>";
		print "<input type='text' name='desc1' id='desc1' class='description' />";
		print "<input type='text' name='perc1' id='perc1' class='percent' />%";
		print "</p>\n";
		print "<p><input type='button' id='addEval' value='add an evaluation item' /></p>\n";
	}
}

function edit_books($id)
{
	$query = "select * from books where class_id = '$id' order by ordr";
	
	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	$booktypes = array('Required', 'Recommended', 'Suggested');
	
	if($numrows > 0 )
	{
		while($row = mysql_fetch_row($result))
		{
			list($bookid, $classid, $title, $author, $date, $isbn, $link, $type, $order) = $row;
			print "<p id=book$order class='clonedbook frame'>\n";
			print "<select name='booktype$order' class='booktype'>\n";
			print "<option value='none'>---</option>\n";
			for ($i=0; $i<3; $i++)
			{
				if($i == $type)
				{
					print "<option value='$i' selected='selected'>$booktypes[$i]</option>\n";
				}
				else
				{
					print "<option value='$i'>$booktypes[$i]</option>\n";
				}
			}
			print "</select><br /><br />\n";
			print "<label for 'bookname$order' class='lableblock titlelabel'>Title</label>";
			print "<input id='bookname$order' name='bookname$order' type='text' class='long title' /><br /><br />\n";
			print "<label for 'author$order' class='lableblock authortitle'>Author</label>";
			print "<input id='author$order' name='author$order' type='text'  class='long author' /><br /><br />\n";
			print "<label for 'date$order' class='lableblock datetitle'>Date</label>";
			print "<input id='date$order' name='date$order' type='text' class='short date' /><br /><br />\n";
			print "<label for 'isbn$order' class='lableblock noclear isbntitle'>ISBN</label>";
			print "<input id='isbn$order' name='isbn$order' type='text' class='short isbn' /><br /><br />\n";
			print "<label for 'link$order' class='lableblock linktitle'>Link</label>";
			print "<input id='link$order' name='link$order' type='text' class='long link' /><br /><br />\n";
			print "</p>";
		}
	}
	
	else
	{
		print "<p id=book1 class='clonedbook frame'>\n";
		print "<select name='booktype1' class='booktype'>\n";
			print "<option value='none' selected='selected'>---</option>\n";
			for ($i=0; $i<3; $i++)
			{
				print "<option value='$i'>$booktypes[$i]</option>\n";
			}
			print "</select><br /><br />\n";
		print "<label for 'bookname1' class='lableblock titlelabel'>Title</label>";
		print "<input id='bookname1' name='bookname1' type='text' class='long title' /><br /><br />\n";
		print "<label for 'author1' class='lableblock authorlabel'>Author</label>";
		print "<input id='author1' name='author1' type='text' class='long author' /><br /><br />\n";
		print "<label for 'date1' class='lableblock datetitle'>Date</label>";
		print "<input id='date1' name='date1' type='text' class='short date' />";
		print "<label for 'isbn1' class='lableblock noclear isbntitle'>ISBN</label>";
		print "<input id='isbn1' name='isbn1' type='text' class='short isbn' /><br /><br />\n";
		print "<label for 'link1' class='lableblock linktitle'>Link</label>";
		print "<input id='link1' name='link1' type='text' class='long link' /><br /><br />\n";
		print "</p>";
	}
	print "<p><input type='button' id='addBook' value='add another book' /></p>\n";
}

function term_start_date($classid)
{
	$query ="select terms.startdate from terms left join classes on terms.id = classes.term_id where classes.id = '$classid' ";
	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	if($numrows == 1)
	{
		$row = mysql_fetch_row($result);
		$startdate = $row[0];
		return $startdate;
	}
	else { return NULL; }
}

function mid_term_start_date($classid)
{
	$query ="select terms.startdate from terms left join classes on terms.id = classes.term_id where classes.id = '$classid' ";
	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	if($numrows == 1)
	{
		$row = mysql_fetch_row($result);
		$startdate = $row[0];
		$startdate = strtotime ( '5 week' , strtotime( $startdate ) );
		$startdate = date( 'Y-m-j' , $startdate );
		return $startdate;
	}
	else { return NULL; }
}

function class_date($classid, $termstart, $week, $day="")
{
	$week_offset = $week -1;
	
	if(!empty($day))
	{	
		switch ($day)
		{
			case "Monday": $num = 0;  break;
			case "Tuesday": $num = 1;  break;
			case "Wednesday": $num = 2;  break;
			case "Thursday": $num = 3;  break;
			case "Friday": $num = 4;  break;
			case "Saturday": $num = 5;  break;
			case "Sunday": $num = 6;  break;
			default: $num = 0;
		}
		
		$startdate = strtotime ( $num . ' day' , strtotime( $termstart ) ) ;
		$startdate = date( 'Y-m-j' , $startdate );
		
		if($week_offset > 0)
		{
			$weekdate = strtotime ( $week_offset . ' week' , strtotime( $startdate ) ) ;
			$weekdate = date( 'M jS, Y' , $weekdate );
			print $weekdate;
		}
		
		else
		{
			$startdate = date( 'M jS, Y' , strtotime ($startdate) );
			print $startdate;
		}
	}
}


function return_day($classid)
{
	$query="select classday from class_days_times where class_id = '$classid'";
	//print $query;
	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	
	if($numrows == 1)
	{
		$row = mysql_fetch_row($result);
		$day = $row[0];
		return $day;
	}
	elseif($numrows == 2)
	{
		$twodays = array();
		while($row = mysql_fetch_row($result))
		{
			$day = $row[0];
			array_push($twodays, $day);
		}
		return $twodays;
	}
	else { return NULL; }
}

function return_full_date($classid, $termstart, $week, $day)
{
	$week_offset = $week -1;	
	switch ($day)
	{
		case "Monday": $num = 0;  break;
		case "Tuesday": $num = 1;  break;
		case "Wednesday": $num = 2;  break;
		case "Thursday": $num = 3;  break;
		case "Friday": $num = 4;  break;
		case "Saturday": $num = 5;  break;
		case "Sunday": $num = 6;  break;
		default: $num = 0;
	}
	
	$startdate = strtotime ( $num . ' day' , strtotime( $termstart ) ) ;
	$startdate = date( 'Y-m-j' , $startdate );
	
	if($week_offset > 0)
	{
		$weekdate = strtotime ( $week_offset . ' week' , strtotime( $startdate ) ) ;
		$weekdate = date( 'Y-m-j' , $weekdate );
		return $weekdate;
	}
	
	else
	{
		$startdate = date( 'Y-m-j' , strtotime ($startdate) );
		return $startdate;
	}
}

function print_holiday($classid, $termstart, $week, $day="")
{	
	if(!empty($day))
	{
		$date = return_full_date($classid, $termstart, $week, $day);
		$query = "select name from dates where date = '$date'";
		$result = mysql_query($query);
		$numrows = mysql_num_rows($result);
		
		if($numrows > 0)
		{
			$row = mysql_fetch_row($result);
			$name = $row[0];
			print "$name";
		}
	}
}

/***************** Process Syllabus Form functions *******************/

function process_class_times($num)
{
	$classid = $_POST['classid'];
	
	switch ($num)
	{
		case "1": $day = $_POST["day1"]; $start = $_POST["timestart1"]; $end = $_POST["timeend1"]; break;
		case "2": $day = $_POST["day2"]; $start = $_POST["timestart2"]; $end = $_POST["timeend2"]; break;
		default: $day = $_POST["day1"]; $start = $_POST["timestart1"]; $end = $_POST["timeend1"];
	}
	
	$query = "select classday, starttime, endtime from class_days_times where class_id = '$classid' and ordr = '$num'";
	//print $query . "<br /><br />";
	$results = mysql_query($query);
	$numrows = mysql_num_rows($results);
	if($numrows == 0)
	{
		$query = "insert into class_days_times values('', '$classid', '$day', '$start', '$end', '$num')";
		mysql_query($query);
	}
	else
	{
		if($numrows == 1)
		{
			$row = mysql_fetch_row($results);
			list($storedday, $storedstart, $storedend) = $row;
			
			if($day != $storedday || $start != $storedstart || $end != $storedend)
			{
				$query = "update class_days_times set classday='$day', starttime='$start', 
				endtime='$end' where class_id='$classid' and ordr='$num'";
				mysql_query($query);
			}
		}
	}
}

function process_class_details()
{
	$classid = $_POST['classid'];
	$hwhrs = mysql_prep($_POST['hwhrs']);
	$officehrs = mysql_prep($_POST['officehrs']);
	$materials = mysql_prep($_POST['materials']);
	$methods = mysql_prep($_POST['methods']);
	$tech = mysql_prep($_POST['tech']);
	$focus = mysql_prep($_POST['focus']);
	$additional = mysql_prep($_POST['additional']);
	
	$hwhrslength = strlen($hwhrs);
	$officehrslength = strlen($officehrs);
	
	if($hwhrslength > 254){ $hwhrs = cut_long_strings($hwhrs, 254);}
	if($officehrs > 254){ $officehrs = cut_long_strings($officehrs, 254);}
	
	
	$query = "select id from class_details where class_id = '$classid'";
	$results = mysql_query($query);
	$numrows = mysql_num_rows($results);
	if($numrows == 0)
	{
		$query = "insert into class_details values('', '$classid', 
		'$materials', '$methods', '$tech', '$hwhrs', '$officehrs', '$additional', '$focus')";
	}
	elseif($numrows == 1)
	{
		$query = "update class_details set materials='$materials', methods='$methods', 
		tech='$tech', hwhrs='$hwhrs', officehrs='$officehrs', add_req='$additional', 
		focus='$focus' where class_id='$classid'";
	}
	else
	{
		$del_query = "DELETE FROM class_details WHERE class_id='$classid'";
		mysql_query($del_query);
		
		$query = "insert into class_details values('', '$classid', 
		'$materials', '$methods', '$tech', '$hwhrs', '$officehrs', '$additional', '$focus')";
	}
	
	mysql_query($query);
}

function process_form($classid)
{
	if(isset($_POST['update']))
	{
		process_class_times('1');
		if(isset($_POST['day2'])) { process_class_times('2'); }
		process_class_details();
	}
	
}

/***************** Displaying Form Data Functions *******************/

function get_class_times($classid, $ordr)
{
	$query = "select classday, starttime, endtime from class_days_times where class_id = '$classid' and ordr = '$ordr'";
	$results = mysql_query($query);
	$numrows = mysql_num_rows($results);
	if($numrows == 1)
	{
		$row = mysql_fetch_row($results);
		return $row;
	}
	else { return NULL; }
}

function get_class_details($classid)
{
	$query = "select * from class_details where class_id = '$classid'";
	$results = mysql_query($query);
	$numrows = mysql_num_rows($results);
	if($numrows == 1)
	{
		$row = mysql_fetch_row($results);
		list($id, $classid, $materials, $methods, $tech, $hwhrs, $officehrs, $addreq, $focus) = $row;
		$data = array('materials'=>$materials, 'methods'=>$methods, 'tech'=>$tech, 
		'hwhrs'=>$hwhrs, 'officehrs'=>$officehrs, 'addreq'=>$addreq, 'focus'=>$focus);
		
		return $data;
	}
	else { return NULL; }
}

?>