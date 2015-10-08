  <!--

      Brick Green
      05/20/15

   -->

   <?php

   include('header.php');
   include('dbconnect.php'); //connection to database

       //popup window for alerts
   function alert($string)
   {
     echo '<script type="text/javascript">alert("' . $string . '");</script>';
    }
       //Precondition: The parameter has a string input
       //Postcondition: The result of the sanitize and validate tests is returned
  function spamcheck($field) {
               // Sanitize e-mail address removes illegal characters from the input
     $new_field=filter_var($field, FILTER_SANITIZE_EMAIL);
               // Validate e-mail address ensures that the email has an @something.com
     if(filter_var($new_field, FILTER_VALIDATE_EMAIL)) {
      return TRUE;
   } else {
      return FALSE;
   }
}

//Delete
if(isset($_POST['delete'])) {
  try {
    $delete_mem = $_POST['checkbox']; //creates array of indexes of selected checkboxes
    if(empty($delete_mem)) {
     alert('Please choose a record(s) to delete.');
    }
    //else if checkboxes have been selected
    else {
    $select_query = 'call SELECT_TEAM'; //query to select all team member records
    $select_result = $conn->query($select_query); //execute query

    //create array for records.
    //It will be a 2D array when filled with records.
    $select_array = array();

    //Precondition: the SELECT_TEAM procedure has returned records
    //Postcondition: the records have been pushed to the array
    //                creating a 2D array.
    while($array = $select_result->fetch(PDO::FETCH_NUM)) { //fetch records individually
      array_push($select_array, $array); //input records to array
    }

    $select_result->closeCursor(); //close database connection process allowing new process to begin

    //traverse array of selected checkbox indexes
    foreach($delete_mem as $num) {
      $email = $select_array[$num][6]; //$num is choosen index of record to be deleted. 6 is the position of the email in individual record array.
      $del_query = 'call DELETE_MEMBER(:email)'; //stored procedure in mysql to delete a member based on the email given
      $del_prepare = $conn->prepare($del_query);

      $del_prepare->bindParam(':email', $email); //bind the parameter to the variable holding the selected email address
      $del_prepare->execute();
    }

    alert('Successfully deleted ' . count($delete_mem) . ' records');

    $del_prepare->closeCursor(); //close database connection process allowing new process to begin
   }
  }
  catch (PDOException $exception) {
 	  echo $exception->getMessage(); //show error messgae if an exception is thrown
  }
}


 //Insert
if(isset($_POST['saveInsert'])) {
   try {
     $email = spamcheck($_POST['email']); //run email through spam check function defined above

     if($email == TRUE) { //if the email passes spamcheck function

      //post data from the web form and save in variables
       $fname = $_POST['fname'];

       $lname = $_POST['lname'];

       $dept = $_POST['department'];

       $job_role = $_POST['job_role'];

       //these form inputs are not required form fields and thus must be validated
       $ophone = $_POST['ophone'];
       if ($ophone == '502-___-____') {
         $ophone = NULL;
      }

      $mphone = $_POST['mphone'];
      if ($mphone == '') {
         $mphone = NULL;
      }

      $email = $_POST['email'];

      $intercom = $_POST['intercom'];
      if ($intercom == '00') {
         $intercom = NULL;
      }

      $pager = $_POST['pager'];
      if ($pager == '502-___-____') {
         $pager = NULL;
      }     

      //save stored procedure in variable
      //parameters are placeholders waiting to be binded to the variables defined above
      $insert_query = 'call INSERT_MEMBER(:fname, :lname, :dept, :job_role, :ophone, :mphone, :email, :intercom, :pager)';

      //prepare the query to be binded
      $insert_prep = $conn->prepare($insert_query);

      //bind parameters to variables
      $insert_prep->bindParam(':fname', $fname);
      $insert_prep->bindParam(':lname', $lname);
      $insert_prep->bindParam(':dept', $dept);
      $insert_prep->bindParam(':job_role', $job_role);
      $insert_prep->bindParam(':ophone', $ophone);
      $insert_prep->bindParam(':mphone', $mphone);
      $insert_prep->bindParam(':email', $email);
      $insert_prep->bindParam(':intercom', $intercom);
      $insert_prep->bindParam(':pager', $pager);

      //execute query
      $insert_prep->execute();

      //close cursor to free resources for next query
      $insert_prep->closeCursor();
   }
}
   catch (PDOException $exception) { //show error messgae is an exception is thrown
    echo $exception->getMessage();
  }
}

//Update
if(isset($_POST['saveUpdate'])) {
   try {
     $email = spamcheck($_POST['up_email']); //run email through spam check function defined above

     if($email == TRUE) { //if the email passes function

      //post data from the web form and save in variables
       $fname = $_POST['up_fname']; //

       $lname = $_POST['up_lname'];

       $dept = $_POST['up_department'];

       $job_role = $_POST['up_job_role'];

       //these form inputs are not required form fields and thus must be validated
       $ophone = $_POST['up_ophone'];
       if ($ophone == '502-___-____') {
         $ophone = NULL;
      }

      $mphone = $_POST['up_mphone'];
      if ($mphone == '') {
         $mphone = NULL;
      }

      $email = $_POST['up_email'];

      $original_email = $_POST['chosen_email'];

      $intercom = $_POST['up_intercom'];
      if ($intercom == '00') {
         $intercom = NULL;
      }

      $pager = $_POST['up_pager'];
      if ($pager == '502-___-____') {
         $pager = NULL;
      }

      //prepare the query to be binded
      //parameters are placeholders waiting to be binded to the variables defined above
      $update_prep = $conn->prepare("UPDATE TEAM_MEMBER
      
                                    SET FIRST_NAME = :fname,
                                    LAST_NAME = :lname,
                                    DEPT_NAME = :dept,
                                    JOB_ROLE = :job_role,
                                    OFFICE_PHONE = :ophone,
                                    MOBILE_PHONE = :mphone,
                                    EMAIL = :email,
                                    INTERCOM_NUM = :intercom,
                                    PAGER_NUM = :pager

                                    WHERE EMAIL = :oemail;");

      //bind parameters to variables
      $update_prep->bindParam(':fname', $fname);
      $update_prep->bindParam(':lname', $lname);
      $update_prep->bindParam(':dept', $dept);
      $update_prep->bindParam(':job_role', $job_role);
      $update_prep->bindParam(':ophone', $ophone);
      $update_prep->bindParam(':mphone', $mphone);
      $update_prep->bindParam(':oemail', $original_email);
      $update_prep->bindParam(':email', $email);
      $update_prep->bindParam(':intercom', $intercom);
      $update_prep->bindParam(':pager', $pager);

      //execute query
      $update_prep->execute();

      //close cursor to free resources for next query
      $update_prep->closeCursor();

   }
}
   catch (PDOException $exception) { //show error messgae is an exception is thrown
    echo $exception->getMessage();
  }
}

?>
<script type='text/javascript'>
   $j=jQuery.noConflict();
   $j(document).ready(function () {

     // $j('#insert').click(function(){
     //    $j('.insert-form').show();
     // });

     $j('#insert').click(function(){
        // did-form has a css attribute that will hide the html form.
        // Toggling the the class will remove it from the form and the insert form will be displayed
        $j('#insert-form').toggleClass('did-form');
     });

     $j('#update').click(function(){
        // did-form has a css attribute that will hide the html form.
        // Toggling the the class will remove it from the form and the insert form will be displayed
        $j('#update-form').toggleClass('did-form');

        var checkedValue = $j('.checkbox:checked').val(); // get value from checkbox array of selected checkbox(s)

        var row = Array(); //create array for team member records

        $j("tr").each(function(i){
           index = i - 1; // start array count at 0 to parallel with checkbox array
           row[index] = Array(); //create array for team member data. This will result in a 2D array.
           $j(this).children('td').each(function(x){ //loop through the data of each team member record
           array_index = x - 1; //start array count at 0
           row[index][array_index] = $j(this).text(); //add record data to sub array
           });
        });

        //insert pulled data into update form
        $j('#up_fname').val($j.trim(row[checkedValue][0]));
        $j('#up_lname').val($j.trim(row[checkedValue][1]));
        $j('[name=up_department]').val($j.trim(row[checkedValue][2]));
        $j('[name=up_job_role]').val($j.trim(row[checkedValue][3]));
        $j('#up_ophone').val($j.trim(row[checkedValue][4]));
        $j('#up_mphone').val($j.trim(row[checkedValue][5]));
        $j('#up_email').val($j.trim(row[checkedValue][6]));
        $j('#up_pager').val($j.trim(row[checkedValue][7]));
        $j('#up_intercom').val($j.trim(row[checkedValue][8]));

        //insert original email into DOM 
        $j('#chosen_email').val($j.trim(row[checkedValue][6]));
     });

     $j(".formatphone").inputmask("999-999-9999");
     $j("#intercom").inputmask("99");
});
</script>

   <div class="container page-padding">
      <div class="row">
        <form method='post' role="form" style="padding-top: 10px">
          <div class="col-md-12">
            <div class="well well-sm">
              <h2 class="text-center">Division of Infectious Diseases Team</h2>
              <hr width="70%">

              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                     <th width="2%" text-align="center"></th>
                     <th width="4%" text-align="center">First</th>
                     <th width="4%" text-align="center">Last</th>
                     <th width="6%" text-align="center">Dept</th>
                     <th width="7%" text-align="center">Role</th>
                     <th width="6%" text-align="center">Office</th>
                     <th width="6%" text-align="center">Mobile</th>
                     <th width="7%" text-align="center">Email</th>
                     <th width="1.5%" text-align="center">Pager</th>
                     <th width="6%" text-align="center">Intercom</th>
                  </tr>
               </thead>
               <tbody id="body">

                  <?php
                  try {
      							//select all records form tblmember table
                   $query = 'call SELECT_TEAM';
                   $result = $conn->query($query);


                    $i = 0;
                    //create a row in the html table from a row in the query result
                    foreach($result as $row){
                       echo " <tr> ";
                       echo ' <td text-align="center" bgcolor="#FFFFFF">';
                       echo "<input  type='checkbox' name='checkbox[]' class='checkbox' value='$i'> ";
                       echo ' <td> ';
                       echo $row['FIRST_NAME'];
                       echo ' <td> ';
                       echo $row['LAST_NAME'];
                       echo ' <td> ';
                       echo $row['DEPT_NAME']; 
                       echo ' <td> ';
                       echo $row['JOB_ROLE'];
                       echo ' <td> ';
                       echo $row['OFFICE_PHONE'];
                       echo ' <td> ';
                       echo $row['MOBILE_PHONE'];
                       echo ' <td> ';
                       echo $row['EMAIL'];
                       echo ' <td> ';
                       echo $row['INTERCOM_NUM'];
                       echo ' <td> ';
                       echo $row['PAGER_NUM'];                 
                       $i++;
                    }
                  $result->closeCursor();
                  }
               catch (PDOException $exception) {
                echo $exception->getMessage();
             } 

             ?>                     
              </tbody>
          </table>
        </div>
    </div>
    </div>
<div style='padding: 0 15px 20px 15px'>            
  <button type='submit' name='delete' class="btn btn-danger">
   <i class="glyphicon glyphicon-remove"></i> Delete
</button>
<button type='button' name='insert' id="insert" class="btn btn-success">
   <i class="glyphicon glyphicon-ok"></i> Insert
</button>
<button type='button' name='update' id="update" class="btn btn-warning">
   <i class="glyphicon glyphicon-refresh"></i> Update
</button> 
</div>

</form>
<div id="insert-form" class='did-form col-md-6 well well-sm' style="margin: 0 15px">
  <form form='form' method="POST">
    <h4 class="text-center">Insert Team Member</h4>
    <div class='form-group'>
      <label>First Name:</label>
      <input name='fname' type='text' class="form-control" id="fname" placeholder="John" autofocus required>
   </div>
   <div class='form-group'>
      <label>Last Name:</label>
      <input name='lname' type='text' class="form-control" id="lname" placeholder="Snow" required>
   </div>
   <div class="form-group">
      <label>Department:</label>
      <div>
        <select id="dept_dropdown" multiple="1" name="department" class="form-control" required> <!--creates dropdown menu for departments-->
          <?php
          try {
            //select all records form tblmember table
            $dept_query = 'call SELECT_DEPARTMENT';
            $dept_result = $conn->query($dept_query);

            foreach($dept_result as $row){
              $dept = $row['DEPT_NAME'];
              echo "<option value='$dept'>$dept</option>";
           };
           $dept_result->closeCursor();
        }
        catch (PDOException $exception) {
           echo $exception->getMessage();
        }
        ?>
     </select>
      </div>
   </div>
   <div class="form-group">
   <label>Job Role:</label>
   <div class="form-group">
      <select id="role_dropdown" multiple="1" name="job_role" class="form-control" required> <!--creates dropdown menu for job roles-->
   <?php
      try {
         //select all records form tblmember table
         $role_query = 'call SELECT_ROLE';
         $role_result = $conn->query($role_query);

         foreach($role_result as $row){
            $role = $row['JOB_ROLE'];
            echo "<option value='$role'>$role</option>";
         };
         $role_result->closeCursor();
      }
      catch (PDOException $exception) {
         echo $exception->getMessage();
      }
   ?>
 </select>
</div>
</div>
<div class='form-group'>
  <label>Office Phone:</label>
  <input name="ophone" type='tel' class="form-control formatphone" value="502-">
</div>
<div class='form-group'>
  <label>Mobile Phone:</label>
  <input name="mphone" type='tel' class="form-control formatphone" id="phone">
</div>
<div class='form-group'>
  <label>Email:</label>
  <input name='email' type='email' class="form-control" placeholder="first.last@louisville.edu" required>
</div>
<div class='form-group'>
  <label>Intercom Number:</label>
  <input name="intercom" type='tel' class="form-control" id="intercom" value="00">
</div>
<div class='form-group'>
  <label>Pager Number:</label>
  <input name="pager" type='tel' class="form-control formatphone" value="502-">
</div>
<button type='submit' name='saveInsert' id='insertBtn' class="btn btn-success">
 <i class="glyphicon glyphicon-ok"></i> Save
</button>
</form>
</div>

<div id="update-form" class='did-form col-md-6 well well-sm' style="margin: 0 15px">
   <form form='form' method="POST">
      <h4 class="text-center">Update Team Member</h4>
      <div class='form-group'>
         <label>First Name:</label>
         <input name='up_fname' type='text' class="form-control" id="up_fname" autofocus required>
      </div>
      <div class='form-group'>
         <label>Last Name:</label>
         <input name='up_lname' type='text' class="form-control" id="up_lname" required>
      </div>
      <div class="form-group">
         <label>Department:</label>
         <div>
            <select multiple="1" name="up_department" class="form-control" required>  <!--creates dropdown menu for departments-->
            <?php
               try {
                              //select all records form tblmember table
               $dept_query = 'call SELECT_DEPARTMENT';
               $dept_result = $conn->query($dept_query);

               foreach($dept_result as $row){
                 $dept = $row['DEPT_NAME'];
                 echo "<option id='$dept' value='$dept'>$dept</option>";
               };
               $dept_result->closeCursor();
               }
               catch (PDOException $exception) {
                  echo $exception->getMessage();
               }
            ?>
            </select>
         </div>
      </div>
      <div class="form-group">
        <label>Job Role:</label>
        <div class="form-group">
          <select multiple="1" name="up_job_role" class="form-control" required> <!--creates dropdown menu for job roles-->
            <?php
            try {
               //select all records form tblmember table
               $role_query = 'call SELECT_ROLE';
               $role_result = $conn->query($role_query);

               foreach($role_result as $row){
                  $role = $row['JOB_ROLE'];
                  echo "<option id='$role' value='$role'>$role</option>";
               };
               $role_result->closeCursor();
            }
            catch (PDOException $exception) {
               echo $exception->getMessage();
            }
            ?>
         </select>
      </div>
      </div>
      <div class='form-group'>
        <label>Office Phone:</label>
        <input name="up_ophone" type='tel' class="form-control formatphone" id="up_ophone">
      </div>
      <div class='form-group'>
        <label>Mobile Phone:</label>
        <input name="up_mphone" type='tel' class="form-control" id="up_mphone">
      </div>
      <div class='form-group'>
        <label>Email:</label>
        <input name='up_email' type='email' class="form-control" id="up_email" required>
        <input name='chosen_email' id="chosen_email" style="display:none" readonly>
      </div>
      <div class='form-group'>
        <label>Intercom Number:</label>
        <input name="up_intercom" type='tel' class="form-control" id="up_intercom" value="00">
      </div>
      <div class='form-group'>
        <label>Pager Number:</label>
        <input name="up_pager" type='tel' class="form-control formatphone" id="up_pager">
      </div>
      <button type='submit' name='saveUpdate' id='insertBtn' class="btn btn-success">
       <i class="glyphicon glyphicon-ok"></i> Save
      </button>
   </form>
</div>

</div>
</div>
<?php include('../footer.php'); ?>
