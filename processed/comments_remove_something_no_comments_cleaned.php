<?php include './include/header_student.php';
include('../encryptDecrypt.php');
$sql = "SELECT student_register.*,course_name.course_display_name, course_stream.course_stream FROM student_register JOIN course_name ON student_register.student_course = course_name.course_id JOIN course_stream ON student_register.student_stream = course_stream.id  WHERE student_uid = '" .  mysqli_real_escape_string($conn,$_SESSION['student_id']) . "'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$skills = explode(', ', $row['student_skills']);
$langu = explode(', ', $row['student_languages']);
$street_perm = explode(', ', $row['street_perm']);
$str = $row['govt_id_number'];
$updated_adhar="xxxx xxxx ".substr(decryptString($str), -4);
?>
<div id="Printablearea">
  <form action="./src/main.php" method="POST">
    <section class="innerSecStrp newsecAppNone newappnewonein">
      <div class="container">
        <div class="col-md-3 col-xs-12 prflPic">
          <div class="prflPicIn">
			<iframe src="<?php echo $file_authorization_url.'path='.base64_encode($_SESSION['student_id'].'(DMAN_I)'.$row['student_profile_photo']);?>" frameBorder="0" scrolling="auto" height="300px" width="100%"></iframe>
          </div>
        </div>
        <div class="col-md-9 col-xs-12 prflDetl">
          <div class="prflDetlIn">
            <h4><?php echo strtoupper($row['name']); ?></h4>
            <p><?php echo strtoupper($row['street_perm'] . ' ' . $row['pincode_perm']); ?></p>
            <ul class="chkLst">
            </ul>
          </div>
        </div>
      </div>
    </section>
    <section class="stdntPrflSec newsecAppNone">
      <div class="container">
        <div class="dashbrdCntMin">
          <h2 class="frmHdng">Fill all the field to check Eligibility</h2>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Name of Student *</label>
            <input type="text" name="name" id="name" value="<?php echo htmlentities($row['name']); ?>" class="form-control" disabled style="border: none;" style="border: none;">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Date of Birth *</label>
            <input type="text" name="date_of_birth" disabled id="date_of_birth" value="<?php echo date('d M Y', strtotime($row['date_of_birth'])); ?>" placeholder="DOB" class="form-control" required style="border: none" style="border: none;">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Gender *</label>
            <?php if ($row['gender'] == 'M') {
              $gender = 'Male';
            }
            if ($row['gender'] == 'F') {
              $gender = 'Female';
            }
            if ($row['gender'] == 'T') {
              $gender = 'Transgender';
            } ?>
            <input type="text" name="gender" id="gender" value="<?php echo htmlentities($gender); ?>" placeholder="Gender" class="form-control" disabled style="border: none;">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Category *</label>
            <?php $community_sql = "SELECT community_name FROM community_master WHERE community_id = '" . $row['community_id'] . "'";
            $community_res = mysqli_query($conn, $community_sql);
            $community_row = mysqli_fetch_assoc($community_res);
            ?>
            <input type="text" name="" value="<?php echo htmlentities($community_row['community_name']); ?>" class="form-control" disabled style="border: none;">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Mothers Name *</label>
            <input type="text" name="mother_name" disabled id="mother_name" value="<?php echo htmlentities($row['mother_name']); ?>" placeholder="Mothers Name" class="form-control" required style="border: none" style="border: none;">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Father Name *</label>
            <input type="text" name="father_name" disabled id="father_name" value="<?php echo htmlentities($row['father_name']); ?>" placeholder="Father Name" class="form-control" required style="border: none" style="border: none;">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Minority *</label>
            <select name="minority" id="minority" class="form-control" required style="border: none" style="border: none; -webkit-appearance: none;" disabled>
              <option value="">Select</option>
              <?php
              $minorities = array('Yes' => 'Yes', 'No' => 'No');
              foreach ($minorities as $key => $value) { ?>
                <option <?php if ($key == $row['minority']) {
                          echo "selected";
                        }  ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
              <?php }
              ?>
            </select>
          </div>
          <div class="col-md-6 col-xs-12 form-group" id="community_minority" style="display: <?php if ($row['minority'] == 'No') {
                                                                                                echo 'none';
                                                                                              } else {
                                                                                                echo 'block';
                                                                                              } ?> ">
            <label>Minority Community *</label>
            <select name="minority_community_id" id="minority_community_id" class="form-control" style="border: none; -webkit-appearance: none;" disabled="">
              <?php if ($row['minority_community_id'] != 0) {
                $minority_sql = "SELECT minority_community_id, minority_community_name FROM minority_community_master";
                $minority_result = mysqli_query($conn, $minority_sql);
                while ($minority_row = mysqli_fetch_assoc($minority_result)) { ?>
                  <option <?php if ($minority_row['minority_community_id'] == $row['minority_community_id']) {
                            echo "selected";
                          } ?> value="<?php echo $minority_row['minority_community_id']; ?>"><?php echo $minority_row['minority_community_name']; ?></option>
              <?php }
              } ?>
            </select>
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Person with Disability *</label>
            <select name="student_differently_abled" id="student_differently_abled" class="form-control" required style="border: none; -webkit-appearance: none;" disabled="">
              <option value="">Select</option>
              <?php
              $disabilites = array('Y' => 'Yes', 'N' => 'No');
              foreach ($disabilites as $key => $value) { ?>
                <option <?php if ($key == $row['student_differently_abled']) {
                          echo "selected";
                        }  ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
              <?php }
              ?>
            </select>
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Email *</label>
            <input type="email" name="email" id="email" value="<?php echo htmlentities($row['student_email']); ?>" class="form-control" required disabled>
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Mobile *</label>
            <input type="text" name="mobile_no" id="mobile_no" value="<?php echo htmlentities($row['mobile_no']); ?>" class="form-control" required disabled>
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Aadhaar Number *</label>
            <input type="text" name="govt_id_number" value="<?php echo htmlentities($updated_adhar); ?>" class="form-control" disabled style="border: none;" style="border: none;">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Student Profile Photo *</label>
            <?php if ($row['student_profile_photo'] != '') { ?>
			  <a href="<?php echo $file_authorization_url.'path='.base64_encode($_SESSION['student_id'].$file_auth_code.$row['student_profile_photo']);?>" target="_blank">View </a>
            <?php } else { ?>
              <input type="file" name="student_profile_photo" id="student_profile_photo" class="form-control" required>
            <?php } ?>
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Aadhaar </label>
            <a href="<?php echo $file_authorization_url.'path='.base64_encode($_SESSION['student_id'].$file_auth_code.$row['govt_id_proof']);?>" target="_blank">View Aadhaar</a>
          </div>
        </div>
        <div class="acdmcQlf newacdnonone">
          <div class="card-body">
            <div class="acdmcQlfTbl">
           <?php  if($row['is_sandwich_student']){ ?>
            <div class="dashbrdCntMin">
            <h2 class="frmHdng">EDUCATIONAL DETAILS</h2>
            <div class="col-md-6 col-xs-12 form-group">
                <label>State to Which Institute Belongs *</label>
                <select class="form-control" name="state" id="state" readonly>
                  <?php
                  $state_query = "SELECT state_id, state_name FROM state_master ORDER BY state_name ASC";
                  $state_result = mysqli_query($conn, $state_query);
                  if (mysqli_num_rows($state_result) > 0) {
                    while ($state_row = mysqli_fetch_assoc($state_result)) { ?>
                      <?php
                        if ($row['state_id'] == $state_row['state_id']) { ?>
                                <option value="<?php echo $state_row['state_id']; ?>"><?php echo $state_row['state_name']; ?></option>
                                <?php         }
                   }
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-6 col-xs-12 form-group">
                <label> District to Which Institute Belongs *</label>
                <select class="form-control" name="district" id="district" readonly>
                  <?php $sql = "Select * from district_master where state_id = '" . $row['state_id'] . "'";
                  $res = mysqli_query($conn, $sql);
                  if (mysqli_num_rows($res) > 0) {
                    while ($row_stream = mysqli_fetch_assoc($res)) { ?>
                      <?php
                          if ($row['district_id'] == $row_stream['district_id']) { ?>
                            <option value="<?php echo $row_stream['district_id'] ?>"><?php echo $row_stream['district_name'] ?></option>
                                      <?php    }
                                    }
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-12 col-xs-12 form-group">
              <label>Institute Name (If institute name not apprearing in drop down , please contact natssupport@aicte-india.org)*</label>
                <select class="form-control" id="institute" name="institute" readonly>
                  <?php
                   $institute_sql = "SELECT pid, Institute_name_LEN FROM institution_master where district_id = '" . $row['district_id'] . "' ORDER BY Institute_name_LEN ASC ";
                  $res1 = mysqli_query($conn, $institute_sql);
                  if ($res1) {
                    while ($row_institute = mysqli_fetch_assoc($res1)) {
                  ?>
                     <?php
                            if ($row['college_name'] == $row_institute['pid']) { ?>
                              <option value="<?php echo $row_institute['pid'] ?>"><?php echo $row_institute['Institute_name_LEN']." ".($row_institute['pid']) ?></option>
                               <?php   }
                            ?>
                  <?php } } ?>
                </select>
              </div>
            <div class="col-md-6 col-xs-12 form-group">
                <label>Education Type *</label>
                <select name="last_passed_out" id="last_passed_out" class="form-control" readonly>
                  <?php if ($row['is_sandwich_student'] == 1) { ?>
                    <?php
                    $passout_sql = "SELECT id,course_type FROM course_type  where id  in (4,5,6,10,11,12) order by course_type ASC";
                    $passout_result = mysqli_query($conn, $passout_sql);
                    if (mysqli_num_rows($passout_result) > 0) {
                      while ($passout_row = mysqli_fetch_assoc($passout_result)) {
                       if ($row['passed_out'] == $passout_row['id']) { ?>
                            <option value="<?php echo $passout_row['id']; ?>" selected ><?php echo $passout_row['course_type']; ?></option>
                             <?php   } ?>
                    <?php }
                    } } ?>
                </select>
              </div>
              <div class="col-md-6 col-xs-12 form-group">
                <label>Course Name* </label>
                <select class="form-control" name="student_course" id="student_course" readonly>
                  <?php $sql = "Select * from course_name where course_type='" . $row['passed_out'] . "'";
                  $res = mysqli_query($conn, $sql);
                  if ($res) {
                    while ($row_new = mysqli_fetch_assoc($res)) {
                  ?>
                      <?php if ($row_new['course_id'] == $row['student_course']) { ?>
                           <option value="<?php echo $row_new['course_id'] ?>" ><?php echo $row_new['course_name'] ?></option>
                            <?php   } ?>
                  <?php }
                  } ?>
                </select>
              </div>
              <div class="col-md-6 col-xs-12 form-group">
                <label>Specialization *</label>
                <select class="form-control" name="student_stream" id="student_stream" readonly>
                  <?php $sql = "Select * from course_stream where course_id='" . $row['student_course'] . "'";
                  $res = mysqli_query($conn, $sql);
                  if ($res) {
                    while ($row_stream = mysqli_fetch_assoc($res)) {
                  ?>
                     <?php if ($row_stream['id'] == $row['student_stream']) { ?>
                                       <option value="<?php echo $row_stream['id'] ?>"><?php echo $row_stream['course_stream'] ?></option>
                                <?php    } ?>
                  <?php }
                  } ?>
                </select>
              </div>
              <div class="col-md-6 col-xs-12 form-group">
                <label> Student ID (Unique Registration/Roll Number) *</label>
                <input type="text" name="student_id" minlength="3" maxlength="20" onkeypress="return ((event.charCode >= 65 && event.charCode <= 91) || (event.charCode >= 96 && event.charCode <= 121) || (event.charCode >= 48 && event.charCode <= 57) || (event.charCode == 47) || (event.charCode == 45))" onchange="validatespecialstrings(this);" value="<?php echo htmlentities($row['student_id']); ?>" class="form-control" readonly>
              </div>
              <div class="col-md-6 col-xs-12 form-group">
                <label>Year of Joining *</label>
                <select class="form-control" name="year_of_passing" readonly>
                  <?php $y = array('2018' => '2018', '2019' => '2019', '2020' => '2020', '2021' => '2021', '2022' => '2022','2023' => '2023');
                  foreach ($y as $key => $value) { ?>
                    <?php if ($row['year_of_passing'] == $key) { ?>
                             <option value="<?php echo $value; ?>"><?php echo $value; ?></option>;
                          <?php  }  ?>
                  <?php } ?>
                </select>
              </div>
              <div class="col-md-6 col-xs-12 form-group">
                <label>Month of Joining *</label>
                <select class="form-control" readonly name="month_of_passing">
                  <?php $month = array();
                  for ($i = 1; $i <= 12; $i++) {
                    $monthNum = $i;
                    $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
                    $month[$i] = $monthName;
                  }
                  foreach ($month as $key => $value) { ?>
                   <?php if (intval($row['month_of_passing']) == $key || ($row['month_of_passing']) == $value) {
                            ?>
                              <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                         <?php   } ?>
                  <?php }
                  ?>
                </select>
              </div>
              <div class="col-md-6 col-xs-12 form-group">
                <label>Pursuing Semester *</label>
                <input type="text" value="<?php echo htmlentities($row['sandwich_student_pursuing_sem']); ?>" class="form-control" readonly/>
              </div>
            <div class="col-md-6 col-xs-12 form-group loc">
                <label>No. of Spells(No. of Apprenticeship Training Permitted in Course) *</label>
                <input type="text" value="<?php echo htmlentities($row['spell_number']); ?>" class="form-control" readonly/>
             </div>
              <div class="col-md-6 col-xs-12 form-group">
                <label>Percentage (For CGPA, convert into Percentage according to University formula) *</label>
                <input type="text" maxlength="5" onkeypress="return ((event.charCode >= 46 && event.charCode <= 57) || (event.charCode == 46))" onchange="check(this)" name="percentage" value="<?php echo htmlentities($row['percentage']); ?>" class="form-control" readonly>
              </div>
              <div class="col-md-6 col-xs-12 form-group">
                  <label>View Provisional / Passed Certificate*</label>
                  <a href="<?php echo $file_authorization_url.'path='.base64_encode($_SESSION['student_id'].$file_auth_code.$row['provision_passed_certificate']);?>" target="_blank">View Certificate </a>
                </div>
            </div>
                <?php }  else { ?>
              <div class="dashbrdCntMin">
                <h2 class="frmHdng">EDUCATIONAL DETAILS</h2>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>State to Which Institute Belongs *</label>
                  <select name="state_id" id="" class="form-control" style="border: none" style="border: none" disabled>
                    <option value="">Select</option>
                    <?php
                    $state_query = "SELECT state_id, state_name FROM state_master ORDER BY state_name ASC";
                    $state_result = mysqli_query($conn, $state_query);
                    if (mysqli_num_rows($state_result) > 0) {
                      while ($state_row = mysqli_fetch_assoc($state_result)) { ?>
                        <option <?php if ($state_row['state_id'] == $row['state_id']) {
                                  echo 'selected';
                                } ?> value="<?php echo $state_row['state_id']; ?>"><?php echo $state_row['state_name']; ?></option>
                    <?php }
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>District to Which Institute Belongs *</label>
                  <select name="district_id" id="" class="form-control" style="border: none" style="border: none" disabled>
                    <option value="">Select</option>
                    <?php
                    $district_sql = "SELECT district_id, district_name FROM district_master WHERE state_id = '" . $row['state_id'] . "' ";
                    $district_result = mysqli_query($conn, $district_sql);
                    while ($district_row = mysqli_fetch_assoc($district_result)) { ?>
                      <option <?php if ($district_row['district_id'] == $row['district_id']) {
                                echo 'selected';
                              } ?> value="<?php echo $district_row['district_id']; ?>"><?php echo $district_row['district_name']; ?></option>
                    <?php }
                    ?>
                  </select>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>Institute Name *</label>
                  <?php
               if ($row['college_name'] =='others') {
                ?>
               <input type="hidden" name="institute" value="others">
               <?php
                $others_inst_sql = "SELECT institute_name FROM others_institute_lists WHERE student_uid = '" . mysqli_real_escape_string($conn, $_SESSION['student_id']) . "' ";
               $others_inst_result = mysqli_query($conn, $others_inst_sql);
               $other_institute_name = "";
               if(mysqli_num_rows($others_inst_result)> 0){
                $row_others_institute = mysqli_fetch_assoc($others_inst_result);
                $other_institute_name = $row_others_institute['institute_name'];
               }
                ?>
               <input type="text" class="form-control" name="otherss" value="<?php echo $row['college_name']." - ".$other_institute_name ; ?>" disabled>
        <?php } else { ?>
                  <select class="form-control" id="institute" name="institute" required disabled>
                    <?php
                    echo $institute_sql = "SELECT pid, Institute_name_LEN FROM institution_master where district_id = '" . $row['district_id'] . "' ORDER BY Institute_name_LEN ASC ";
                    $res1 = mysqli_query($conn, $institute_sql);
                    if ($res1) {
                      while ($row_institute = mysqli_fetch_assoc($res1)) {
                        print_r($row_institute);
                    ?>
                        <option value="<?php echo $row_institute['pid'] ?>" <?php
                                                                            if ($row['college_name'] == $row_institute['pid']) {
                                                                              echo 'selected';
                                                                            }
                                                                            ?>><?php echo $row_institute['Institute_name_LEN'] . ($row_institute['pid']) ?></option>
                    <?php }
                    } ?>
                  </select>
                <?php } ?>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>Education Type (Pass Out)* </label>
                  <select class="form-control" name="last_passed_course" style="border: none" style="border: none" disabled>
                    <option>Select Last Passed Course</option>
                    <?php
                    $passout_sql = "SELECT course_type.id,course_type.course_type FROM course_type INNER JOIN student_register ON student_register.passed_out = course_type.id WHERE course_type.id = '" . $row['passed_out'] . "' AND student_register.student_uid = '" . mysqli_real_escape_string($conn, $_SESSION['student_id']) . "' ";
                    $passout_result = mysqli_query($conn, $passout_sql);
                    if (mysqli_num_rows($passout_result) > 0) {
                      while ($passout_row = mysqli_fetch_assoc($passout_result)) {    ?>
                        <option <?php if ($passout_row['id'] == $row['passed_out']) {
                                  echo 'selected';
                                } ?> value="<?php echo $passout_row['id']; ?>"><?php echo $passout_row['course_type']; ?></option>
                    <?php }
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>Course Name*</label>
                  <select class="form-control" name="student_course" style="border: none" style="border: none" disabled>
                    <?php
                    echo $course_sql = "SELECT course_id, course_name FROM course_name WHERE course_id = '" . $row['student_course'] . "' ";
                    $course_result = mysqli_query($conn, $course_sql);
                    if (mysqli_num_rows($course_result) > 0) {
                      while ($course_row = mysqli_fetch_assoc($course_result)) { ?>
                        <option value="<?php echo $course_row['course_id']; ?>"><?php echo $course_row['course_name']; ?></option>
                    <?php }
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>Specialization *</label>
                  <select class="form-control" name="student_course" style="border: none" style="border: none" disabled>
                    <?php
                    echo $stream_sql = "SELECT id,course_id, course_stream FROM course_stream WHERE id = '" . $row['student_stream'] . "' ORDER BY course_id ASC ";
                    $stream_result = mysqli_query($conn, $stream_sql);
                    if (mysqli_num_rows($stream_result) > 0) {
                      while ($stream_row = mysqli_fetch_assoc($stream_result)) { ?>
                        <option value="<?php echo $stream_row['id']; ?>"><?php echo $stream_row['course_stream']; ?></option>
                    <?php }
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label> Student ID (Enrollment Number) *</label>
                  <input type="text" name="student_id" value="<?php echo htmlentities($row['student_id']); ?>" class="form-control" disabled style="border: none" disabled>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>Year of Passing</label>
                  <select class="form-control" name="last_passed_course" style="border: none" style="border: none" disabled>
                    <option>Select Last Passed Course</option>
                    <option value="">Select</option>
                    <?php $y = array('2017' => '2017', '2018' => '2018', '2019' => '2019', '2020' => '2020', '2021' => '2021', '2022' => '2022','2023' => '2023');
                    foreach ($y as $key => $value) { ?>
                      <option <?php if ($row['year_of_passing'] == $value) {
                                echo 'selected';
                              }  ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>;
                    <?php } ?>
                  </select>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>Month of Passing</label>
                  <select class="form-control" name="last_passed_course" style="border: none" style="border: none" disabled>
                    <option value="">Select Last Passed Course</option>
                    <?php
                    $month = array();
                    for ($i = 1; $i <= 12; $i++) {
                      $monthNum = $i;
                      $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
                      $month[$i] = $monthName;
                    }
                    foreach ($month as $key => $value) { ?>
                      <option <?php if ($row['month_of_passing'] == $key || ($row['month_of_passing']) == $value) {
                                echo 'Selected';
                              } ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php }
                    ?>
                  </select>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>Percentage * (For CGPA, convert into Percentage according to University formula)</label>
                  <input type="text" name="percentage" value="<?php echo htmlentities($row['percentage']); ?>" class="form-control" style="border: none" maxlength="5" style="border: none;" disabled onchange="validatenumber(this);">
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>View Provisional / Passed Certificate*</label>
                  <a href="<?php echo $file_authorization_url.'path='.base64_encode($_SESSION['student_id'].$file_auth_code.$row['provision_passed_certificate']);?>" target="_blank">View Certificate </a>
                </div>
                <div class="col-md-12">
                  <input type="radio" id="higher_sec" disabled name="course_in" value="Higher Secondary (10+2)" required <?php if ($row['course_in'] == 'Higher Secondary (10+2)') { echo 'checked';} ?>> Higher Secondary (10+2)
                  <input type="radio" id="diploma" disabled name="course_in" value="Diploma" <?php if ($row['course_in'] == 'Diploma') { echo 'checked'; } ?>> Diploma
                  <input type="radio" id="high_sch" disabled name="course_in" value="High School(10th)" required <?php if ($row['course_in'] == 'High School(10th)') { echo 'checked'; } ?>> High School(10th)
                  <input type="radio" id="iti" disabled name="course_in" value="ITI" <?php if ($row['course_in'] == 'ITI') { echo 'checked'; } ?>> ITI
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>School/Institute State*</label>
                  <select class="form-control" name="school_state" id="school_state" disabled style="border: none" disabled>
                    <option value="">Select State</option>
                    <?php
                    $state_query = "SELECT state_id, state_name FROM state_master ORDER BY state_name ASC";
                    $state_result = mysqli_query($conn, $state_query);
                    if (mysqli_num_rows($state_result) > 0) {
                      while ($state_row = mysqli_fetch_assoc($state_result)) { ?>
                        <option <?php if ($state_row['state_id'] == $row['school_state']) {
                                  echo 'selected';
                                } ?> value="<?php echo $state_row['state_id']; ?>"><?php echo $state_row['state_name']; ?></option>
                    <?php }
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>School/Institute District*</label>
                  <select class="form-control" name="school_district" id="school_district" disabled style="border: none" disabled>
                    <?php if (!empty($row['school_state'])) {
                      $district_query = "SELECT district_id, district_name FROM district_master WHERE state_id = '" . $row['school_state'] . "' ORDER BY district_name ASC";
                      $district_result = mysqli_query($conn, $district_query);
                      while ($district_row = mysqli_fetch_assoc($district_result)) { ?>
                        <option <?php if ($district_row['district_id'] == $row['school_district']) {
                                  echo 'selected';
                                } ?> value="<?php echo $district_row['district_id']; ?>"><?php echo $district_row['district_name']; ?></option>
                    <?php }
                    } ?>
                    <option value="">Select School District</option>
                  </select>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>School/Institute Name*</label>
                  <input type="text" name="school_name" value="<?php echo htmlentities($row['school_name']); ?>" class="form-control" disabled style="border: none" disabled>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>School/Institute Board Name*</label>
                  <input type="text" name="school_board" value="<?php echo htmlentities($row['school_board']); ?>" class="form-control" disabled style="border: none" disabled>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>Student ID (Enrollment Number)</label>
                  <input type="text" name="student_school_id" value="<?php echo htmlentities($row['student_school_id']); ?>" class="form-control" disabled style="border: none" disabled>
                </div>
                <div class="col-md-6 col-xs-12 form-group">
                  <label>Percentage * (For CGPA, convert into Percentage according to University formula)</label>
                  <input type="text" name="school_percentage" value="<?php echo htmlentities($row['school_percentage']); ?>" class="form-control" maxlength="5" disabled style="border: none" disabled>
                </div>
                <div class="col-md-12 col-xs-12 form-group">
                  <label>About your Self *</label>
                  <textarea name="student_description" class="form-control" disabled style="border: none "><?php echo $row['student_description']; ?> </textarea>
                </div>
                <div class="col-md-12 col-xs-12 form-group" style="height: auto;">
                  <label>General Skills (Add comma separated values)</label>
                  <textarea class="form-control" name="student_skills" id="student_skills" required style="border: none" disabled=""><?php if (!empty($row['student_skills'])) {
                                                                                                                                        echo htmlentities($row['student_skills']);
                                                                                                                                      } ?> </textarea>
                </div>
                <div class="col-md-12 col-xs-12 form-group" style="height: auto;">
                  <label>Languages Known (Add comma separated values)</label>
                  <textarea class="form-control" name="student_languages" id="student_languages" required style="border: none" disabled=""><?php if (!empty($row['student_languages'])) {
                                                                                                                                              echo htmlentities($row['student_languages']);
                                                                                                                                            } ?> </textarea>
                </div>
                <?php
                $additional_sql = "SELECT * FROM additional_qualifications WHERE student_uid = '" . mysqli_real_escape_string($conn, $_SESSION['student_id']) . "' ";
                $additional_res = mysqli_query($conn, $additional_sql);
                if (mysqli_num_rows($additional_res) > 0) {
                  while ($additional_row = mysqli_fetch_assoc($additional_res)) {
                ?>
                    <h2 class="frmHdng">Additional Qualification</h2>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Institute State*</label>
                      <select class="form-control" name="additional_inst_state" disabled="">
                        <option value="">Select State</option>
                        <?php
                        $state_query = "SELECT state_id, state_name FROM state_master WHERE state_id = '" . $additional_row['additional_inst_state'] . "' ORDER BY state_name ASC";
                        $state_result = mysqli_query($conn, $state_query);
                        if (mysqli_num_rows($state_result) > 0) {
                          while ($state_row = mysqli_fetch_assoc($state_result)) { ?>
                            <option <?php if ($state_row['state_id'] == $additional_row['additional_inst_state']) {
                                      echo 'selected';
                                    } ?> value="<?php echo $state_row['state_id']; ?>"><?php echo $state_row['state_name']; ?></option>
                        <?php }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Institute District*</label>
                      <select class="form-control" name="additional_inst_district" disabled="">
                        <option value="">Select District</option>
                        <?php
                        $district_sql = "SELECT district_id, district_name FROM district_master WHERE district_id = '" . $additional_row['additional_inst_district'] . "' ";
                        $district_result = mysqli_query($conn, $district_sql);
                        while ($district_row = mysqli_fetch_assoc($district_result)) { ?>
                          <option <?php if ($district_row['district_id'] == $additional_row['additional_inst_district']) {
                                    echo 'selected';
                                  } ?> value="<?php echo $district_row['district_id']; ?>"><?php echo $district_row['district_name']; ?></option>
                        <?php }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-12 col-xs-12 form-group">
                      <label>Institute Name (If institute name not apprearing in drop down , please contact abc@xyz.com)*</label>
                      <select class="form-control" name="additional_institute_pid" required disabled="">
                        <option value="">Select Institute</option>
                        <?php
                        $institute_sql = "SELECT pid, name FROM institute_details1 WHERE pid = '" . $additional_row['additional_institute_pid'] . "' ORDER BY name ASC ";
                        $institute_result = mysqli_query($conn, $institute_sql);
                        while ($institute_row = mysqli_fetch_assoc($institute_result)) { ?>
                          <option <?php if ($institute_row['pid'] == $additional_row['additional_institute_pid']) {
                                    echo 'selected';
                                  } ?> value="<?php echo $additional_row['pid']; ?>"><?php echo $institute_row['name']; ?></option>
                        <?php }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Institute Type*</label>
                      <select class="form-control" name="additional_institute_type" required disabled="">
                        <option value="<?php echo $additional_row['additional_institute_type']; ?>"><?php echo $additional_row['additional_institute_type']; ?></option>
                      </select>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Education Type *</label>
                      <select name="additional_last_passed_out" class="form-control" disabled="">
                        <option value="">Select Last Passed Out</option>
                        <?php
                        $passout_sql = "SELECT id,course_type FROM course_type WHERE id = '" . $additional_row['additional_last_passed_out'] . "' ";
                        $passout_result = mysqli_query($conn, $passout_sql);
                        if (mysqli_num_rows($passout_result) > 0) {
                          while ($passout_row = mysqli_fetch_assoc($passout_result)) { ?>
                            <option <?php if ($passout_row['id'] == $additional_row['additional_last_passed_out']) {
                                      echo 'selected';
                                    } ?> value="<?php echo $passout_row['id']; ?>"><?php echo $passout_row['course_type']; ?></option>
                        <?php }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Course Name*</label>
                      <select class="form-control" name="additional_student_course" disabled="">
                        <option value="">Select Course</option>
                        <?php
                        $course_sql = "SELECT course_id, course_name FROM course_name WHERE course_id = '" . $additional_row['additional_student_course'] . "' ";
                        $course_result = mysqli_query($conn, $course_sql);
                        if (mysqli_num_rows($course_result) > 0) {
                          while ($course_row = mysqli_fetch_assoc($course_result)) { ?>
                            <option <?php if ($course_row['course_id'] == $additional_row['additional_student_course']) {
                                      echo 'selected';
                                    } ?> value="<?php echo $course_row['course_id']; ?>"><?php echo $course_row['course_name']; ?></option>
                        <?php }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Specialization *</label>
                      <select class="form-control" name="additional_student_stream" disabled="">
                        <option value="">Select Stream</option>
                        <?php
                        $stream_sql = "SELECT id,course_id, course_stream FROM course_stream WHERE id = '" . $additional_row['additional_student_stream'] . "' ORDER BY course_id ASC ";
                        $stream_result = mysqli_query($conn, $stream_sql);
                        if (mysqli_num_rows($stream_result) > 0) {
                          while ($stream_row = mysqli_fetch_assoc($stream_result)) { ?>
                            <option <?php if ($stream_row['id'] == $additional_row['additional_student_stream']) {
                                      echo 'selected';
                                    } ?> value="<?php echo $stream_row['id']; ?>"><?php echo $stream_row['course_stream']; ?></option>
                        <?php }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Student ID (Last studied College ID)*</label>
                      <input type="text" name="additional_student_id" value="<?php echo $additional_row['additional_student_id']; ?>" onchange="validatespecialstrings(this);" class="form-control" maxlength="10" disabled>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Year of Passing *</label>
                      <select name="additional_year_of_passing" class="form-control" required disabled="">
                        <option value="">Select Year of passing</option>
                        <?php $y = array('2017' => '2017', '2018' => '2018', '2019' => '2019', '2020' => '2020', '2021' => '2021', '2022' => '2022');
                        foreach ($y as $key => $value) { ?>
                          <option <?php if ($value == $additional_row['additional_year_of_passing']) {
                                    echo 'selected';
                                  } ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>;
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Months of Passing *</label>
                      <select name="additional_month_of_passing" class="form-control" required disabled>
                        <option value="<?php echo $additional_row['additional_month_of_passing']; ?>"><?php echo $additional_row['additional_month_of_passing']; ?></option>
                      </select>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                      <label>Percentage * (For CGPA, convert into Percentage according to University formula)</label>
                      <input type="text" name="additional_academic_per" onchange="validateper(this);" class="form-control" value="<?php echo $additional_row['additional_academic_per']; ?>" maxlength="5" disabled>
                    </div>
                <?php }
                }
                ?>
              </div>
             <?php }  ?>
            </div>
          </div>
        </div>
        <div class="dashbrdCntMin" style="margin-bottom: 50px;">
          <h2 class="frmHdng">Permanent Address</h2>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Pin Code *</label>
            <input type="text" <?php if ($_SESSION['student_status'] == 5 && $_SESSION['edit_profile'] = 1) {
                                  echo "disabled";
                                } ?> name="pincode_perm" value="<?php echo $row['pincode_perm']; ?>" class="form-control" maxlength="6" style="border: none" onchange="validatenumber(this);">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Post Office *</label>
            <input <?php if ($_SESSION['student_status'] == 5 && $_SESSION['edit_profile'] = 1) {
                      echo "disabled";
                    } ?> type="text" name="post_office" value="<?php echo @$street_perm[0]; ?>" class="form-control" style="border: none" onchange="validatestringsandnumber(this);">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>District*</label>
            <select <?php if ($_SESSION['student_status'] == 5 && $_SESSION['edit_profile'] = 1) {
                      echo "disabled";
                    } ?> class="form-control" name="district_perm" id="district_perm" style="border: none">
              <option value="">Select District</option>
              <?php if ($row['district_perm'] > 0) {
                $district_sql = "SELECT district_id, district_name FROM district_master WHERE state_id = '" . $row['state_perm'] . "' ";
                $district_result = mysqli_query($conn, $district_sql);
                while ($district_row = mysqli_fetch_assoc($district_result)) { ?>
                  <option <?php if ($district_row['district_id'] == $row['district_perm']) {
                            echo 'selected';
                          } ?> value="<?php echo $district_row['district_id']; ?>"><?php echo $district_row['district_name']; ?></option>
              <?php }
              } ?>
            </select>
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>State/UT*</label>
            <select <?php if ($_SESSION['student_status'] == 5 && $_SESSION['edit_profile'] = 1) {
                      echo "disabled";
                    } ?> class="form-control" name="state_perm" id="state_perm" style="border: none">
              <option value="">Select</option>
              <?php
              $state_query = "SELECT state_id, state_name FROM state_master ORDER BY state_name ASC";
              $state_result = mysqli_query($conn, $state_query);
              if (mysqli_num_rows($state_result) > 0) {
                while ($state_row = mysqli_fetch_assoc($state_result)) { ?>
                  <option <?php if ($state_row['state_id'] == $row['state_perm']) {
                            echo 'selected';
                          } ?> value="<?php echo $state_row['state_id']; ?>"><?php echo $state_row['state_name']; ?></option>
              <?php }
              }
              ?>
            </select>
          </div>
          <div class="col-md-12 col-xs-12 form-group">
            <label>Address *</label>
            <input <?php if ($_SESSION['student_status'] == 5 && $_SESSION['edit_profile'] = 1) {
                      echo "disabled";
                    } ?> type="text" name="address" value="<?php echo htmlentities(@$street_perm[1]); ?>" class="form-control" maxlength="255" style="border: none" onchange="validatestringsandnumber(this);">
          </div>
          <h2 class="frmHdng">Present Address</h2>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Pin Code *</label>
            <input type="text" disabled name="present_pin_code" id="present_pin_code" value="<?php echo htmlentities($row['present_pin_code']); ?>" class="form-control" style="border: none" onchange="validatenumber(this);">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Post Office *</label>
            <input type="text" disabled name="present_post_office" id="present_post_office" value="<?php echo htmlentities($row['present_post_office']); ?>" class="form-control" style="border: none" onchange="validatestringsandnumber(this);">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>District*</label>
            <select class="form-control" id="present_district" name="present_district" style="border: none" disabled>
              <option value="">Select</option>
              <?php if ($row['present_district'] > 0) {
                $district_sql1 = "SELECT district_id, district_name FROM district_master WHERE state_id = '" . $row['present_state'] . "' ";
                $district_result1 = mysqli_query($conn, $district_sql1);
                while ($district_row1 = mysqli_fetch_assoc($district_result1)) { ?>
                  <option disabled <?php if ($district_row1['district_id'] == $row['present_district']) {
                                      echo 'selected';
                                    } ?> value="<?php echo $district_row1['district_id']; ?>"><?php echo $district_row1['district_name']; ?></option>
              <?php }
              } ?>
            </select>
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>State/UT*</label>
            <select class="form-control" name="present_state" disabled id="present_state" style="border: none">
              <option value="">Select</option>
              <?php
              $state_query1 = "SELECT state_id, state_name FROM state_master ORDER BY state_name ASC";
              $state_result1 = mysqli_query($conn, $state_query1);
              if (mysqli_num_rows($state_result1) > 0) {
                while ($state_row1 = mysqli_fetch_assoc($state_result1)) { ?>
                  <option <?php if ($state_row1['state_id'] == $row['present_state']) {
                            echo 'selected';
                          } ?> value="<?php echo $state_row1['state_id']; ?>"><?php echo $state_row1['state_name']; ?></option>
              <?php }
              }
              ?>
            </select>
          </div>
          <div class="col-md-12 col-xs-12 form-group">
            <label>Address *</label>
            <input type="text" disabled name="present_address" id="present_address" disabled value="<?php echo htmlentities($row['present_address']); ?>" class="form-control" style="border: none" onchange="validatestringsandnumber(this);">
          </div>
        </div>
		<?php  if($_SESSION['is_sandwich_student']==1){  $required=""; } else { $required="required";}
if(empty($_SESSION['is_sandwich_student'])){
		?>
        <div class="dashbrdCntMin" style="margin-bottom: 50px;">
          <h2 class="frmHdng">Training Preferences</h2>
          <input type="hidden" value="<?php echo htmlentities($_SESSION['csrf_token'])?>" name="csrf_token">
            <div class="col-md-6 col-xs-12 form-group">
              <label>Preferred State - 1 </label>
              <select name="student_first_location" id="student_first_location" class="form-control" style="border: none; -webkit-appearance: none;" disabled="" <?php echo $required;?>>
                <option value="">Select</option>
                <?php
                $state_query = "SELECT state_id, state_name FROM state_master ORDER BY state_name ASC";
                $state_result = mysqli_query($conn, $state_query);
                if (mysqli_num_rows($state_result) > 0) {
                  while ($state_row = mysqli_fetch_assoc($state_result)) { ?>
                    <option <?php if ($state_row['state_id'] == $row['student_first_location']) {
                              echo 'selected';
                            } ?> value="<?php echo $state_row['state_id']; ?>"><?php echo $state_row['state_name']; ?></option>
                <?php }
                }
                ?>
              </select>
            </div>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Preferred District - 1 </label>
              <select name="student_first_location_dist1" id="student_first_location_dist1" class="form-control" style="border: none; -webkit-appearance: none;" disabled="" <?php echo $required;?>>
                <option value="">Select District</option>
                <?php if (!empty($row['student_first_location'])) {
                  $district_query = "SELECT district_id, district_name FROM district_master WHERE state_id = '" . $row['student_first_location'] . "' ORDER BY district_name ASC";
                  $district_result = mysqli_query($conn, $district_query);
                  while ($district_row = mysqli_fetch_assoc($district_result)) { ?>
                    <option <?php if ($district_row['district_id'] == $row['student_first_location_dist1']) {
                              echo 'selected';
                            } ?> value="<?php echo $district_row['district_id']; ?>"><?php echo $district_row['district_name']; ?></option>
                <?php }
                } ?>
              </select>
            </div>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Preferred State - 2 </label>
              <select name="student_second_location" id="student_second_location" class="form-control" style="border: none; -webkit-appearance: none;" disabled="" <?php echo $required;?> >
                <option value="">Select</option>
                <?php
                $state_query = "SELECT state_id, state_name FROM state_master ORDER BY state_name ASC";
                $state_result = mysqli_query($conn, $state_query);
                if (mysqli_num_rows($state_result) > 0) {
                  while ($state_row = mysqli_fetch_assoc($state_result)) { ?>
                    <option <?php if ($state_row['state_id'] == $row['student_second_location']) {
                              echo 'selected';
                            } ?> value="<?php echo $state_row['state_id']; ?>"><?php echo $state_row['state_name']; ?></option>
                <?php }
                }
                ?>
              </select>
            </div>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Preferred District - 2</label>
              <select name="student_second_location_dist2" id="student_second_location_dist2" class="form-control" style="border: none; -webkit-appearance: none;" disabled="" <?php echo $required;?>>
                <option value="">Select</option>
                <?php if (!empty($row['student_second_location'])) {
                  $district_query = "SELECT district_id, district_name FROM district_master WHERE state_id = '" . $row['student_second_location'] . "' ORDER BY district_name ASC";
                  $district_result = mysqli_query($conn, $district_query);
                  while ($district_row = mysqli_fetch_assoc($district_result)) { ?>
                    <option <?php if ($district_row['district_id'] == $row['student_second_location_dist2']) {
                              echo 'selected';
                            } ?> value="<?php echo $district_row['district_id']; ?>"><?php echo $district_row['district_name']; ?></option>
                <?php }
                } ?>
              </select>
            </div>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Preferred State - 3</label>
              <select name="student_third_location" id="student_third_location" class="form-control" style="border: none; -webkit-appearance: none;" disabled="" <?php echo $required;?>>
                <option value="">Select</option>
                <?php
                $state_query = "SELECT state_id, state_name FROM state_master ORDER BY state_name ASC";
                $state_result = mysqli_query($conn, $state_query);
                if (mysqli_num_rows($state_result) > 0) {
                  while ($state_row = mysqli_fetch_assoc($state_result)) { ?>
                    <option <?php if ($state_row['state_id'] == $row['student_third_location']) {
                              echo 'selected';
                            } ?> value="<?php echo $state_row['state_id']; ?>"><?php echo $state_row['state_name']; ?></option>
                <?php }
                }
                ?>
              </select>
            </div>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Preferred District - 3</label>
              <select name="student_third_location_dist3" id="student_third_location_dist3" class="form-control" style="border: none; -webkit-appearance: none;" disabled="" <?php echo $required;?>>
                <option value="">Select</option>
                <?php if (!empty($row['student_third_location'])) {
                  $district_query = "SELECT district_id, district_name FROM district_master WHERE state_id = '" . $row['student_third_location'] . "' ORDER BY district_name ASC";
                  $district_result = mysqli_query($conn, $district_query);
                  while ($district_row = mysqli_fetch_assoc($district_result)) { ?>
                    <option <?php if ($district_row['district_id'] == $row['student_third_location_dist3']) {
                              echo 'selected';
                            } ?> value="<?php echo $district_row['district_id']; ?>"><?php echo $district_row['district_name']; ?></option>
                <?php }
                } ?>
              </select>
            </div>
            <h2 class="frmHdng">Field Preference</h2>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Preferred Field -1</label>
              <select name="preferred_field1" id="preferred_field1" class="form-control" style="border: none; -webkit-appearance: none;" disabled="" <?php echo $required;?>>
                <option value="">Select Preferred Field 1</option>
                <?php
                $nature_query = "SELECT id, name FROM nature_of_establishments ORDER BY name ASC ";
                $nature_result = mysqli_query($conn, $nature_query);
                while ($nature_row = mysqli_fetch_assoc($nature_result)) { ?>
                  <option <?php if ($nature_row['id'] == $row['preferred_field1']) {
                            echo 'selected';
                          } ?> value="<?php echo $nature_row['id']; ?>"><?php echo $nature_row['name']; ?></option>
                <?php }
                ?>
              </select>
            </div>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Preferred Field -2</label>
              <select name="preferred_field2" id="preferred_field2" class="form-control" style="border: none; -webkit-appearance: none;" disabled="" <?php echo $required;?>>
                <option value="">Select Preferred Field 2</option>
                <?php if (!empty($row['preferred_field2'])) {
                  $nature_query = "SELECT id, name FROM nature_of_establishments ORDER BY name ASC ";
                  $nature_result = mysqli_query($conn, $nature_query);
                  while ($nature_row = mysqli_fetch_assoc($nature_result)) { ?>
                    <option <?php if ($nature_row['id'] == $row['preferred_field2']) {
                              echo 'selected';
                            } ?> value="<?php echo $nature_row['id']; ?>"><?php echo $nature_row['name']; ?></option>
                <?php }
                } ?>
              </select>
            </div>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Preferred Field -3</label>
              <select name="preferred_field3" id="preferred_field3" class="form-control" style="border: none; -webkit-appearance: none;" disabled="" <?php echo $required;?>>
                <option value="">Select Preferred Field 3</option>
                <?php if (!empty($row['preferred_field3'])) {
                  $nature_query = "SELECT id, name FROM nature_of_establishments ORDER BY name ASC ";
                  $nature_result = mysqli_query($conn, $nature_query);
                  while ($nature_row = mysqli_fetch_assoc($nature_result)) { ?>
                    <option <?php if ($nature_row['id'] == $row['preferred_field3']) {
                              echo 'selected';
                            } ?> value="<?php echo $nature_row['id']; ?>"><?php echo $nature_row['name']; ?></option>
                <?php }
                } ?>
              </select>
            </div>
        </div>
		<?php  }?>
        <div class="dashbrdCntMin">
          <h2>Bank Details</h2>
          <div class="col-md-6 col-xs-12 form-group">
            <label>IFSC Code *</label>
            <input type="text" name="bank_ifsc_code" disabled value="<?php echo htmlentities($row['bank_ifsc_code']); ?>" class="form-control" style="border: none" onchange="validatestringsandnumber(this);">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Bank Branch Name *</label>
            <input type="text" name="bank_branch_name" disabled value="<?php echo htmlentities($row['bank_branch_name']); ?>" class="form-control" style="border: none" onchange="validatestrings(this);">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Bank Name *</label>
            <select class="form-control" name="bank_name" disabled style="border: none">
              <option value="">Select</option>
              <?php
              $bank_sql = "SELECT Bank_id, Bank_name FROM bank_master ORDER BY Bank_name ASC ";
              $bank_result = mysqli_query($conn, $bank_sql);
              while ($bank_row = mysqli_fetch_assoc($bank_result)) { ?>
                <option <?php if ($row['bank_name'] == $bank_row['Bank_id']) {
                          echo 'selected';
                        } ?> value="<?php echo $bank_row['Bank_id']; ?>"><?php echo $bank_row['Bank_name']; ?></option>
              <?php }
              ?>
            </select>
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Name of the Account Holder *</label>
            <input type="text" name="account_holder_name" disabled value="<?php echo htmlentities($row['account_holder_name']); ?>" class="form-control" style="border: none" onchange="validatestrings(this);">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Bank Account Number *</label>
            <input type="text" name="bank_account_number" disabled value="<?php echo htmlentities($row['bank_account_number']); ?>" class="form-control" style="border: none" onchange="validatenumber(this);">
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <label>Upload Bank Passbook *</label>
            <?php if (!empty($row['bank_proof_path'])) { ?>
              <a href="<?php echo $file_authorization_url.'path='.base64_encode($_SESSION['student_id'].$file_auth_code.$row['bank_proof_path']); ?>" target="_blanck">View</a>
            <?php } else { ?>
              <input type="file" name="certificate" class="form-control">
            <?php } ?>
          </div>
        </div>
        <p>I <?php echo $row['name']; ?> son / daughter of <?php echo $row['mother_name']; ?> hereby declare that the above statements are true and correct to the best of my knowledge.</p> <p>I also declare that I am an Indian Citizen, and will sincerely abide by the rules and regulations of the Apprentices ACT monitored by BOATs/BOPT</p>
              <div class="col-xs-12 chkbx">
                <input type="checkbox" name="hello" id="some"> * By clicking this box I agree to the above Terms and Conditions
              </div>
        <center>
          <div class="col-md-1 form-group">
            <a href="student-dashboard-eligbility-check.php?edit_profile=True" id="first_button" class="btn btn-primary btnHdrGet">Edit</a>
          </div>
          <div class="col-md-1 form-group">
            <button type="button" onclick="printDiv('Printablearea')" id="print_btn" class="btn btn-primary btnHdrGet"> Print</button>
          </div>
          <div class="col-md-1 form-group">
          <form action="./src/main.php" method="post">
          <input type="hidden" value="<?php echo htmlentities($_SESSION['csrf_token'])?>" name="csrf_token">
              <button type="submit" name="final_submit_student" class="btn btn-primary btnHdrGet" id="post_skip" disabled>Final Submit</button>
            </form>
          </div>
        </center>
      </div>
    </section>
  </form>
</div>
<?php include('./include/footer.inc.php') ?>
<script>
  function printDiv(divName) {
    var aTags = document.getElementsByTagName('a');
     var atl = aTags.length;
     var i;
     for (i = 0; i < atl; i++) {
        aTags[i].removeAttribute("href");
        aTags[i].text='';
     }
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
  }
  $('
        $('
    })
</script>
</div>
</body>
</html>