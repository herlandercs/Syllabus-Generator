<?php require_once('includes/session.php'); ?>
<?php require_once('includes/connection.php'); ?>
<?php require_once('includes/functions.php'); ?>
<?php require_once('includes/functions-courses.php'); ?>
<?php require_once('includes/authcheck.php'); ?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->

<head>
<meta charset="utf-8">

<meta name="description" content="Art Institute Syllabus Generator">
<meta name="author" content="William Mead">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<title>AI Syllabus Generator Course Page</title>

<link rel="stylesheet" href="stylesheets/base.css" type="text/css">
<link rel="stylesheet" href="stylesheets/skeleton.css" type="text/css">
<link rel="stylesheet" href="stylesheets/layout.css" type="text/css">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->


<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/script-course.js"></script>
</head>

<body>

<div id="logo"><a href="index.php"><img src="images/logo.png" alt="AI Syllabus Generator" /></a></div>

<?php
		if(!isset($_SESSION['auth09328']) || $_SESSION['auth09328'] != $val)
		{
			include('includes/users/loginform.php');
			print "</div></body></html>";
		}
		
		else
		{
?>


<div id="page" class="container">

<?php add_course(); ?>
<?php edit_course(); ?>

	<div class="three columns frame nav">
	<?php include('includes/navigation.php'); ?>
    </div>

	<div class="nine columns">
    
	<?php
	
		if(isset($_GET['editcourse']))
		{
			include('includes/courses/course-edit.php');
		}
		elseif(isset($_GET['courseid']))
		{
			include('includes/courses/course-desc.php');
		}
		else
		{
			include('includes/courses/course-add.php');
		}
	
	?>
    </div>
    
    <div class="three columns">
    	<h2 class="mainheader">List of Courses</h2>
    	<div class="courselist">
        <p><?php collapsed_course_list(); ?></p>
    	</div>
    </div>

</div>

</body>
</html>

<?php } ?>

<?php require_once('includes/footer.php'); ?>