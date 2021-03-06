<h2 class="mainheader">Add a Course</h2>
<form action="courses.php" method="post">
	<div class="frame">
        <h3> Basic Course Information</h3>
        <div>
            <p><label for="courseno">Course Number</label>
            <input type="text" id="courseno" name="courseno" /></p>
            
            <p><label for="coursename">Course Name</label>
            <input type="text" id="coursename" name="coursename" /></p>
            
            <p><label for="totalhrs">Total Hours</label>
            <input type="text" id="totalhrs" name="totalhrs" /></p>
            
            <p><label for="lecthrs">Lecture Hours</label>
            <input type="text" id="lecthrs" name="lecthrs" /></p>
            
            <p><label for="labhrs">Lab Hours</label>
            <input type="text" id="labhrs" name="labhrs" /></p>
            
            <p><label for="credits">Credits</label>
            <input type="text" id="credits" name="credits" /></p>
            
            <select name="depts">
                <option value='0'>Choose a Department</option>
                <?php output_dept_list(); ?>
            </select>
        </div>
    </div>
    
    <div class="frame">
    <h3><label for="coursedesc">Course Description</label></h3>
    <textarea name="coursedesc" cols="45" rows="10"></textarea>
    </div>
    
    <div class="frame">
        <h3>Course Competencies</h3>
        <p id="input1" class="clonedInput">
        <label for="comp1">Competency</label> <input id="comp1" name="comp1" type="text" class="txtfield" />
        <a href="#" id="indent1" class="indentbtn">Indent</a>
        <input type="hidden" id="level1" name="level1" value="0">
        </p>
        
        <input type="button" id="addComp" value="add another competency" />
    </div>
    
    <div class="frame">
        <h3>Course Prerequisites</h3>
        <p id="prereq-input1" class="clonedPrereq">
        <label for="prereq1">Prerequisite</label> <input id="prereq1" name="prereq1" type="text" />
        </p>
        
        <input type="button" id="addPrereq" value="add another prerequisite" />
    </div>
    
    <p><input type="submit" name="addcourse" value="add course" /></p>
    
</form>