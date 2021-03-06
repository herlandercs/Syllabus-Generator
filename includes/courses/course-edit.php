<?php $id = $_GET['editcourse']; ?>

<h2 class="mainheader">Edit Course</h2>
<form action="courses.php" method="post">
	<div class="frame">
        <h3> Basic Course Information</h3>
        <div>
            <input type="hidden" name="id" value="<?php echo $id; ?>"
            
            <p><label for="courseno">Course Number</label>
            <input type="text" id="courseno" name="courseno" value="<?php echo course_item('number', $id);?>" /></p>
            
            <p><label for="coursename">Course Name</label>
            <input type="text" id="coursename" name="coursename" value="<?php echo course_item('name', $id);?>" /></p>
            
            <p><label for="totalhrs">Total Hours</label>
            <input type="text" id="totalhrs" name="totalhrs" value="<?php echo course_item('totalhrs', $id);?>" /></p>
            
            <p><label for="lecthrs">Lecture Hours</label>
            <input type="text" id="lecthrs" name="lecthrs" value="<?php echo course_item('lecthrs', $id);?>" /></p>
            
            <p><label for="labhrs">Lab Hours</label>
            <input type="text" id="labhrs" name="labhrs" value="<?php echo course_item('labhrs', $id);?>" /></p>
            
            <p><label for="credits">Credits</label>
            <input type="text" id="credits" name="credits" value="<?php echo course_item('credit', $id);?>" /></p>
            
            <select name="depts">
                <option value='0'>Choose a Department</option>
                <?php $dept = course_item('dept', $id);?>
                <?php output_dept_list($dept); ?>
            </select>
            </div>
    </div>
            
            <div class="frame">
            <h3><label for="coursedesc">Course Description</label></h3>
            <textarea name="coursedesc" cols="45" rows="10"><?php echo course_item('desc', $id);?></textarea>
            </div>
            
            <div class="frame">
       		<h3>Course Competencies</h3>
            <?php edit_competencies(); ?>
            </div>
            
            <div class="frame">
       		<h3>Course Prerequisites</h3>
            <?php edit_prereqs(); ?>
            </div>
            
            <p><input type="submit" name="editcourse" value="edit course" />
    
</form>
