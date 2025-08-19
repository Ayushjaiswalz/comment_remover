<?php include './include/header_industry.php'; ?>
<?php
$_SESSION['is_ai'] = false;
$year = array();
$year1 = date("Y");
for ($i = 4; $i >= 0; $i--) {
    //$year[$i] = $year1 - $i;
    /* $year[($year1-0).','.($year1-1).','.($year1-2).','.($year1-3).','.($year1-4)] = ($year1-4).' and Later';
    $year[($year1-0).','.($year1-1).','.($year1-2).','.($year1-3)] = ($year1-3).' and Later';
    $year[($year1-0).','.($year1-1).','.($year1-2)] = ($year1-2).' and Later';
    $year[($year1-0).','.($year1-1)] = ($year1-1).' and Later';*/
    $year[$year1 - $i] = $year1 - $i;
}
$month = array();
for ($i = 1; $i <= 12; $i++) {
    $monthNum = $i;
    $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
    $month[$monthName] = $monthName;
}
$today_date = date('d-m-Y');

?>
<section class="lgnSectionSec registrSec">
    <div class="container-fluid">
        <div class="dashBrdRgt">
            <div class="dashBrdRgtIn">
                <div class="dashbrdCntMin">
                    <form action="./src/main.php" method="post" id="post_app" enctype="multipart/form-data">
                        <input type="hidden" value="<?php echo $_SESSION['csrf_token'] ?>" name="csrf_token">
                        <input type="hidden" id="max_interns" value="<?php echo $max = (int)((15 * $_SESSION['corporate_manpower']) / 100) ?>">
                        <input type="hidden" id="min_interns" value="<?php echo $min = (int)((2.5 * $_SESSION['corporate_manpower']) / 100) ?>">
                        <h2 class="frmHdng">Post Advertisement</h2>
                        <?php
                        //print_r($_SESSION);
                        ?>
                        <!-- <div class="col-md-4 col-xs-12 form-group">
              <label>Select State of Office *</label>
              <select class="form-control" name="state" id="state" required multiple="multiple">
                <?php
                $corporate_uid = $_SESSION['corporate_id'];
                $sql = "Select state,state_name from corporate_locations a inner join state_master d on state = state_id where a.corporate_uid = '$corporate_uid' and a.is_deleted=0";
                $res = mysqli_query($conn, $sql);
                if ($res) {
                    while ($row = mysqli_fetch_assoc($res)) { ?>
                    <option value="<?php echo $row['state']; ?>"><?php echo $row['state_name']; ?></option>
                  <?php
                    }
                }
                    ?>
              </select>
            </div>
            <div class="col-md-4 col-xs-12 form-group">
              <label>Select District of Office *</label>
              <select class="form-control" name="district" id="district" required>
                <option selected disabled value="">Select Districts</option>
                <option value="All Districts" selected>All District</option>-->
                        <!-- <?php
                                $corporate_uid = $_SESSION['corporate_id'];
                                $sql = "Select district_name,district_id from corporate_locations a inner join district_master d on district = district_id where a.corporate_uid = '$corporate_uid' and a.is_deleted=0";
                                $res = mysqli_query($conn, $sql);
                                if ($res) {
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        echo "<option value='" . $row['district_id'] . "'>" . $row['district_name'] . "</option>";
                                    }
                                }
                                ?>
              </select>
            </div>-->
                        <div class="col-md-6 col-xs-12 form-group">
                            <label>Select Location of the Office * <b style="color:red" id="loc"></b></label>
                            <select class="form-control" name="location[]" id="location" multiple="multiple" required="true">

                                <?php
                                $corporate_uid = $_SESSION['corporate_id'];
                                $sql = "Select address, district_name,district_id,state_name,pincode, a.id as corp_loc_id
                        from corporate_locations a 
                        inner join state_master s on state=state_id
                        inner join district_master d on district = district_id 
                        where a.corporate_uid = '$corporate_uid' and a.is_deleted=0
                        order by corp_loc_id";
                                $res = mysqli_query($conn, $sql);
                                if ($res) {
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        echo "<option value='" . $row['corp_loc_id'] . "'>" . $row['address'] . ', ' . $row['district_name'] . ', ' . $row['state_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6 form-group">
                            <label>&nbsp;</label>
                            <input type="checkbox" name="is_ai" id="is_ai"> <span style="color:red; font-size:16px; font-weight:500">Is this AI Apprenticeship Advertisement? </span>
                            <script>
                                $('#is_ai').on('change', function() {
                                    if ($(this).is(':checked')) {
                                        $('#subdomain').show()
                                        $('#domain').removeClass('col-md-12')
                                        $('#domain').removeClass('col-xs-12')
                                        $('#domain').addClass('col-md-6')
                                        $('#domain').addClass('col-xs-6')
                                        $('#ai_type').show()
                                        $('#add_ai_week').show()
                                        $('#ai_training_type').attr('required', 'required')
                                        $('#ai_week').show()
                                        $('#ai_weeks').attr('required', 'required')
                                        $('#ai_weeks').val('2')
                                        $('#industry_sub_sector').attr('required', 'required')
                                        $.post('../src/ajax/get_sector.php', {
                                            domain: "AI"
                                        }, function(data) {
                                            $('#industry_sector').html(data);

                                        })
                                        var sector = "all";
                                        $.post('../src/ajax/get_subsector.php', {
                                            sector: sector
                                        }, function(data) {

                                            $('#industry_sub_sector').html(data);

                                        })
                                    } else {
                                        // $('#add_ai_week').hide()
                                        $('#subdomain').hide()
                                        $('#domain').addClass('col-md-12')
                                        $('#domain').addClass('col-xs-12')
                                        $('#domain').removeClass('col-md-6')
                                        $('#domain').removeClass('col-xs-6')
                                        $('#ai_type').hide()
                                        $('#ai_week').hide()
                                        $('#ai_training_type').removeAttr('required')
                                        $('#ai_type11').hide()
                                        $('#ai_type22').hide()
                                        $('#ai_training_type1').removeAttr('required')
                                        $('#ai_training_type2').removeAttr('required')
                                        $('#ai_training_type1').val('0')
                                        $('#ai_training_type2').val('0')
                                        $('#ai_training_type').val('0')
                                        $('#add_ai_week').hide()
                                        $('#ai_weeks').removeAttr('required')
                                        $('#ai_weeks1').removeAttr('required')
                                        $('#ai_weeks2').removeAttr('required')

                                        $('#ai_weeks').val('0')
                                        $('#ai_weeks1').val('0')
                                        $('#ai_weeks2').val('0')
                                        $('#industry_sub_sector').removeAttr('required')
                                        $.post('../src/ajax/get_sector.php', {}, function(data) {
                                            $('#industry_sector').html(data);
                                            $('#industry_sub_sector').html('<option value="0">None</option>');
                                        })
                                    }
                                })
                                $(document).ready(function() {
                                    $('#ai_type').hide()
                                    $('#ai_week').hide()
                                    $('#ai_weeks').val('0')
                                    $('#add_ai_week_1').hide();
                                })
                            </script>
                        </div>
                        <!-- <div class="col-md-12 col-xs-12 form-group">
              <label>Advertisement Id *</label>
              <input type="text" name="adid" id="adid" placeholder="Advertisement Id" onchange="validatestringsandnumber(this);" class="form-control" value="<?php //echo "APREN" . time() . srand(time()); 
                                                                                                                                                                ?>" readonly>
            </div> -->

                        <div class="col-md-12 col-xs-12 form-group" id="domain">
                            <label>Sector of Apprenticeship *</label>
                            <?php
                            //php code to select sector from the db
                            $sql_sector = "select * from sectors_of_establishment where status=1  order by name";
                            $result_sector = mysqli_query($conn, $sql_sector);
                            //end of edited part 11-09-2023
                            echo '
                              <select name="industry_sector"  id="industry_sector" class="form-control form-field period1" required>
                              <option value="" disabled selected>Select Industry Sector</option>';
                            while ($row_sector = mysqli_fetch_assoc($result_sector)) {
                                echo '<option value="' . $row_sector['id'] . '">' . $row_sector['name'] . '</option>';
                            }
                            echo '
                                            </select>
                                            ';
                            ?>
                            <script>
                                $('#industry_sector').on('change', function() {

                                    //send ajax request to update the sub-sector list
                                    var sector = $('#industry_sector').val();
                                    $.post('../src/ajax/get_subsector.php', {
                                        sector: sector
                                    }, function(data) {

                                        $('#industry_sub_sector').html(data);
                                        $('#industry_sub_sector').multiselect("destroy").multiselect({
                                            includeSelectAllOption: true,
                                            enableFiltering: true,
                                            enableCaseInsensitiveFiltering: true,
                                            maxHeight: 150
                                        });

                                    })
                                    // alert(sector)
                                })
                            </script>

                        </div>
                        <div class="col-md-6 col-xs-6 form-group" id="subdomain" style="display:none">
                            <label>Sub Sector of AI domain *</label>

                            <select name="industry_sub_sector[]" id="industry_sub_sector" class="form-control form-field' . $i . ' period1" multiple="multiple">
                                <option value="" disabled selected>Select Industry Sub-Sector</option>

                            </select>
                        </div>



                        <div class="col-md-12 col-xs-12 form-group">
                            <label>Apprenticeship Title *</label>
                            <input type="text" name="title" id="title" placeholder="Apprenticeship Title" class="form-control" onchange="validatespecialstrings1(this);" maxlength="100" required>
                        </div>
                        <div class="col-md-12 col-xs-12 form-group">
                            <label>Apprenticeship Description(100 to 1000 words) *</label>
                            <textarea class="form-control" name="description" onchange="validatestrings(this);" required id="description" style="height: 60px" maxlength="255"></textarea>
                        </div>
                        <?php
                        $sql_type_training = "select * from type_of_training where status=1";
                        $result_type_training = mysqli_query($conn, $sql_type_training);
                        $types_training = array();
                        if (mysqli_num_rows($result_type_training) > 0) {
                            while ($row_training = mysqli_fetch_assoc($result_type_training)) {
                                $types_training[$row_training['id']] = $row_training['training_type'];
                            }
                        }

                        ?>

                        <div class="col-md-6 col-xs-12 form-group" id="ai_type">
                            <label>Select Type of Training *</label>
                            <select class="form-control form-field ai_training" name="ai_training_type" id="ai_training_type">
                                <option value="NA" disabled selected>Select Type of Training</option>
                                <?php
                                foreach ($types_training as $key => $value) {
                                ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-5 col-xs-12 form-group" id="ai_week">

                            <label>No of Weeks of Apprenticeship in AI-centered Training *</label>
                            <input type="number" class="form-control ai_weeks" id="ai_weeks" value="0" name="ai_weeks">
                        </div>
                        <div class="col-md-1 col-xs-2 form-group" id="add_ai_week" style="display:none">
                            <label>&nbsp;</label>
                            <button type="button" id="add_ai_week_1" title="Add new type of training for AI" class="btn btn-success"><i class="fa fa-plus"></i> </button>
                        </div>
                        <div class="col-md-12 col-xs-12 form-group" id="ai_type11" style="display:none">
                        </div>
                        <div class="col-md-12 col-xs-12 form-group" id="ai_type22" style="display:none">
                        </div>
                        <script>
                            $('#ai_weeks').on('blur', function() {
                                var weeks = $('#ai_weeks').val();
                                $('#ai_weeks_hide').val($('#ai_weeks').val())
                                if (weeks < 2) {
                                    alert('A minimum of 2 weeks of training is mandatory.')
                                    $('#ai_weeks').val(2)
                                }
                            })

                            $('#add_ai_week_1').on('click', function() {
                                var item_selected = $('#ai_training_type').val()
                                // var item_selected1=$('#ai_training_type1').val()
                                var item_selected2 = $('#ai_training_type2').val()
                                var html = '<div class="col-md-6 col-xs-12 form-group" id="ai_type1"> <label>Select Type of Training *</label> <select class="form-control form-field ai_training" name="ai_training_type1" id="ai_training_type1" onchange="changetype2();"> ';
                                // array_types.forEach((item)=>{
                                //   if(item_selected!==item){
                                //     html = html+ `<option value="${item}">${item}</option>`
                                //   }

                                // })
                                <?php
                                foreach ($types_training as $key => $value) {
                                ?>
                                    if (item_selected != <?= $key ?> && item_selected2 != <?= $key ?>) {
                                        html += '<option value="<?= $key ?>"><?= $value ?></option>';
                                    }
                                <?php
                                }
                                ?>

                                html = html + '</select></div> <div class="col-md-5 col-xs-10 form-group" id="ai_week1"> <label>No of Weeks of Apprenticeship in AI-centered Training *</label> <input type="number" onblur=check_week(this) class="form-control ai_weeks" id="ai_weeks1" value="2" name="ai_weeks1" > </div> <div class="col-md-1 col-xs-2 form-group" id="add_ai_week1"  style="display:none"> <button type="button" id="add_ai_week_2" onclick="additem3();" title="Add new type of training for AI" class="btn btn-success"><i class="fa fa-plus"></i> </button> <br> <button type="button" id="delete_ai_week_2" onclick="removetype1()" title="Add new type of training for AI" class="btn btn-danger"><i class="fa fa-minus"></i> </button> ';
                                $('#ai_type11').html(html);
                                $('#ai_type11').show();
                                $('#add_ai_week_1').hide();
                                $('#add_ai_week1').show();

                            });
                            $('#ai_training_type').on('change', function() {
                                // on change check the others are visible then remove the divs
                                $('#add_ai_week_1').show();
                                $('#ai_type1').remove()
                                $('#ai_week1').remove()
                                $('#add_ai_week_1').show()
                                $('#add_ai_week1').hide();
                                $('#ai_type2').remove()
                                $('#ai_week2').remove()
                                $('#add_ai_week_2').show()
                                $('#add_ai_week2').hide();
                                $('#ai_type11').hide();
                                $('#ai_type22').hide();
                            })

                            function changetype2() {
                                // on change check the others are visible then remove the divs

                                $('#ai_type2').remove()
                                $('#ai_week2').remove()
                                $('#add_ai_week_2').show()
                                $('#add_ai_week2').hide();

                                $('#ai_type22').hide();
                            }

                            function check_week(input) {
                                const value = parseInt(input.value, 10);

                                if (value < 2 || isNaN(value)) {
                                    alert("Value must be at least 2");
                                    input.value = 2;
                                    input.focus();
                                }
                            }

                            function removetype1() {
                                $('#ai_type1').remove()
                                $('#ai_week1').remove()
                                $('#add_ai_week_1').show()
                                $('#add_ai_week1').hide();
                                $('#ai_type11').hide();
                            }

                            function additem3() {
                                var item_selected = $('#ai_training_type').val()
                                var item_selected1 = $('#ai_training_type1').val()
                                var html = '<div class="col-md-6 col-xs-12 form-group" id="ai_type2"> <label>Select Type of Training *</label> <select class="form-control form-field ai_training" name="ai_training_type2" id="ai_training_type2"> ';


                                <?php
                                foreach ($types_training as $key => $value) {
                                ?>
                                    // alert(item_selected+'!= '+<?= $key ?> +' &&  '+ item_selected1 +'!= ' +<?= $key ?>)
                                    if (item_selected != <?= $key ?> && item_selected1 != <?= $key ?>) {
                                        html += '<option value="<?= $key ?>"><?= $value ?></option>';
                                    }
                                <?php
                                }
                                ?>
                                html = html + '</select></div> <div class="col-md-5 col-xs-10 form-group" id="ai_week2"> <label>No of Weeks of Apprenticeship in AI-centered Training *</label> <input type="number" onblur=check_week(this) class="form-control ai_weeks" id="ai_weeks2" value="2" name="ai_weeks2" > </div> <div class="col-md-1 col-xs-2 form-group" id="add_ai_week2"  style="display:none">  <button type="button" id="delete_ai_week_2" onclick="removetype2()" title="Add new type of training for AI" class="btn btn-danger"><i class="fa fa-minus"></i> </button> ';
                                $('#ai_type22').html(html);
                                $('#ai_type22').show();
                                $('#add_ai_week_2').hide();
                                $('#add_ai_week2').show();
                            }

                            function removetype2() {
                                $('#ai_type2').remove()
                                $('#ai_week2').remove()
                                $('#add_ai_week_2').show()
                                $('#add_ai_week2').hide();
                                $('#ai_type22').hide();
                            }
                            // $('#add_ai_week_1').on('click',function(){
                            //   var html='<div class="col-md-6 col-xs-12 form-group" id="ai_type1">               <label>Select Type of Training *</label>                <select class="form-control form-field ai_training" name="ai_training_type1" id="ai_training_type1">                  <option value="" disabled selected>Select Type of Training</option>                  <option value="Core Development-centered training in AI/Emerging Technology">Core Development-centered training in AI/Emerging Technology</option>                  <option value="Use of existing tools/applications for AI/Emerging Technology in training">Use of existing tools/applications for AI/Emerging Technology in training</option>                  <option value="Academic/Web-based Training imparted to apprentices">Academic/Web-based Training imparted to apprentices</option>                </select>            </div>            <div class="col-md-5 col-xs-10 form-group" id="ai_week1">              <label>No of Weeks of Apprenticeship in AI-centered Training *</label>              <input type="number" class="form-control ai_weeks" id="ai_weeks1" value="0" name="ai_weeks1" >            </div>            <div class="col-md-1 col-xs-2 form-group" id="add_ai_week1"  style="display:none">                        <button type="button" id="add_ai_week_2" title="Add new type of training for AI" class="btn btn-success"><i class="fa fa-plus"></i> </button> <button type="button" id="add_ai_week_2" title="Add new type of training for AI" class="btn btn-danger"><i class="fa fa-minus"></i> </button>      ';
                            //   $('#ai_type11').html(html);
                            //   $('#ai_type11').show();
                            //   $('#add_ai_week_1').hide();
                            //   $('#add_ai_week1').show();
                            // })
                            // $('#ai_weeks1').on('blur',function(){
                            //   var weeks=$('#ai_weeks1').val();
                            //   $('#ai_weeks_hide1').val($('#ai_weeks1').val())
                            //   if(weeks<2){
                            //     alert('A minimum of 2 weeks of training is mandatory.')
                            //   }
                            // })
                            // $('#add_ai_week_2').on('click',function(){
                            //   var html='<div class="col-md-6 col-xs-12 form-group" id="ai_type2">               <label>Select Type of Training *</label>                <select class="form-control form-field ai_training" name="ai_training_type[]" id="ai_training_type2">                  <option value="" disabled selected>Select Type of Training</option>                  <option value="Core Development-centered training in AI/Emerging Technology">Core Development-centered training in AI/Emerging Technology</option>                  <option value="Use of existing tools/applications for AI/Emerging Technology in training">Use of existing tools/applications for AI/Emerging Technology in training</option>                  <option value="Academic/Web-based Training imparted to apprentices">Academic/Web-based Training imparted to apprentices</option>                </select>            </div>            <div class="col-md-5 col-xs-10 form-group" id="ai_week1">              <label>No of Weeks of Apprenticeship in AI-centered Training *</label>              <input type="number" class="form-control ai_weeks" id="ai_weeks" value="0" name="ai_weeks" >            </div>            <div class="col-md-1 col-xs-2 form-group" id="add_ai_week"  style="display:none">            <label>&nbsp;</label>              <button type="button" id="add_ai_week_2" title="Add new type of training for AI" class="btn btn-success"><i class="fa fa-plus"></i> </button>      ';
                            //   $('#ai_type22').html(html);
                            //   $('#ai_type22').show();
                            // })
                        </script>

                        <input type="hidden" name="internship_type" value="Full Time">
                        <div class="col-md-4 col-xs-12 form-group" hidden>
                            <label>Reimbursement Type *</label>
                            <select class="form-control" name="reimbursement_type" id="reimbursement_type">
                                <option value="<?php echo $_SESSION['corporate_reimbursement'] ?>"><?php echo $_SESSION['corporate_reimbursement'] ?></option>
                            </select>
                        </div>

                        <div class="col-md col-xs-12 form-group">
                            <label>Keywords (Specify Skills)</label>
                            <input type="text" name="keywords" id="keywords0" placeholder="Keywords" onchange="validatespecialstrings(this);" class="form-control">
                        </div>

                        <div class="col-md-6 col-xs-12 form-group">
                            <?php
                            // calculate the total available seats
                            $fyear = "";
                            $start = "";
                            $end = "";
                            if (date("m") < 4) {
                                $start = (date("Y") - 1) . "-04-01";
                                $end = (date("Y")) . "-03-31";
                                $fyear = (date("Y") - 1) . "-" . (date("Y"));
                            } else {
                                $fyear = (date("Y")) . "-" . (date("Y") + 1);
                                $start = (date("Y")) . "-04-01";
                                $end = (date("Y") + 1) . "-03-31";
                            }
                            $sql_contract_engaged = "SELECT * FROM contract_creation WHERE est_id='" . $_SESSION['est_id'] . "' and (govt_status not like '%reject%' AND govt_status not like 'termination_approved_by_director') and govt_status_date between '$start' and '$end'";
                            $result_engaged = mysqli_query($conn, $sql_contract_engaged);
                            $total_contracts_live = mysqli_num_rows($result_engaged);

                            // //get total requirement
                            $sql_req = "SELECT sum(i_with_rem_by_director+i_without_rem_by_director) slots FROM corporate_requirement where corporate_uid='" . $_SESSION['corporate_id'] . "' and status=3 group by corporate_uid";
                            $result_req = mysqli_query($conn, $sql_req);
                            $total_slots = mysqli_fetch_assoc($result_req)['slots'];
                            $available_slots = $total_slots - $total_contracts_live;
                            if ($available_slots < 0) {
                                $available_slots = 0;
                            }
                            ?>
                            <label>Number of Apprentices *</label>
                            <input type="number" min="1" name="number_of_apprenticies" id="number_of_apprenticies" onchange="validatenumber(this);" required placeholder="Number of Apprentices" class="form-control" max="<?= $available_slots ?>">
                        </div>
                        <!--
            <?php
            if ($_SESSION['corporate_reimbursement'] == 'Non-Reimbursement') {
            ?>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Number of Apprentices *</label>
              <input type="text" name="number_of_apprenticies_without" id="number_of_apprenticies_without" onchange="validatenumber(this);" required placeholder="without Reimbursement" class="form-control">
            </div>
            <?php } else { ?>
            <div class="col-md-6 col-xs-12 form-group">
              <label>Number of Apprentices willing to engage with Reimbursement *</label>
              <input type="text" name="number_of_apprenticies_with" id="number_of_apprenticies_with" placeholder="with Reimbursement" onchange="validatenumber(this);" required class="form-control">
            </div>
            <?php } ?>
            -->
                        <div class="col-md-6 col-xs-12 form-group">
                            <label> Last Date to Apply * </label>
                            <input type="date" name="last_date" id="last_date" placeholder="Last Date to Apply" min="<?php echo date('Y-m-d', strtotime($today_date)) ?>" class="form-control" required>
                        </div>


                        <!-- <div class="col-md-6 col-xs-12 form-group">
                <label>Year of Passing *</label>
                <select name="year_of_passing" id="year_of_passing" class="form-control" required>
                  <option value="">Select Year of passing</option>
                  <?php
                    //foreach ($year as $key => $value) {
                    //echo '<option value="' . $value . '">' . $value . '</option>';
                    //}
                    ?>
                </select>
            </div> -->
                        <!-- <div class="col-md-6 col-xs-12 form-group">
              <label>Locations *</label>
              <input type="text" name="locations" id="locations" placeholder="Locations" class="form-control">
            </div> -->

                        <div class="col-xs-12 fullDiv" id="container1">
                            <div class="fullIn" id="fullPd">
                                <section id="mainSection">
                                    <h3>Who can apply?</h3>
                                    <div class="tableRespons">

                                        <table id="dynamic_course">
                                            <tr id="row0">
                                                <td>
                                                    <div class="col-md  col-xs-12 form-group course_type1">

                                                        <label for="qualification">Education Type<b style="color:red">*</b></label>
                                                        <div class="form-group">
                                                            <select class="form-control border-dark student_course_type" name="course_type0[]" id="course_type_new0" onchange="get_qualification(this,0)" required>
                                                                <option disabled selected value=""> --Select Your Education Type-- </option>
                                                                <?php $sql = "SELECT * from course_type where status=0 order by course_type asc";
                                                                $res = mysqli_query($conn, $sql);
                                                                while ($row = mysqli_fetch_assoc($res)) {
                                                                    echo '<option value="' . $row['id'] . '">' . $row['course_type'] . '</option>';
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-md col-xs-12 form-group qualification1">
                                                        <label for="qualification">Course Type<b style="color:red">*</b></label>
                                                        <div class="form-group">
                                                            <select class="form-control border-dark student_course" name="student_course0[]" id="student_course0" required onchange="get_spec(this,0)">
                                                                <option value="">- Select Your Course Type -</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-md  col-xs-12 form-group specialisation1">
                                                        <label for="student_stream">Specialization<b style="color:red">*</b></label>
                                                        <div class="form-group">
                                                            <select class="form-control border-dark item_sub_category" name="student_stream0[]" id="item_sub_category0" multiple="multiple">

                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-md  col-xs-12 form-group specialisation1">
                                                        <label for="student_stream">Minimum Percentage<b style="color:red">*</b></label>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="min_percenta0[]" onchange="intercheck(this)" onkeyup="validatenumber(this);" maxlength="2" max="99" id="min_percenta0" required>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="row0">
                                                <!--<td>
                        <div class="col-md col-xs-12 form-group interns1">
                          <label>No. of Apprenticeship *</label>
                          <input type="text" name="interns[]" max="<?php //echo $max = (int)((15 * $_SESSION['corporate_manpower']) / 100) 
                                                                    ?>" placeholder="No. of Apprenticeship Required" class="form-control" onkeyup="intercheck(this)" onchange="validatenumber(this);" required maxlength="3">
                        </div>
                            </td>-->
                                                <td>
                                                    <div class="col-md col-xs-12 form-group amount1">
                                                        <label>Amount of Stipend per Month *</label>
                                                        <input type="text" name="amount_aprenticeship0[]" id="amount_aprenticeship0" placeholder="Amount of Stipend per Month" onchange="check_stipend(this,0)" onkeyup="validatenumber(this);" required maxlength="7" class="form-control">
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="col-md col-xs-12 form-group">
                                                        <label>Duration in Month*</label>

                                                        <select name="duration0[]" id="duration0" class="form-control" required>

                                                        </select>
                                                    </div>
                                                </td>

                                                <td>

                                                    <div class="col-md col-xs-12 form-group">
                                                        <label>Gender * <b style="color:red" id="gen0"></b></label>
                                                        <select class="form-control" type="text" name="gender0[]" required id="gender0" multiple="multiple">
                                                            <option value="M">Male</option>
                                                            <option value="F">Female</option>
                                                            <option value="T">Transgender</option>
                                                        </select>
                                                    </div>

                                                </td>


                                                <td>

                                                    <div class="col-md col-xs-12 form-group">
                                                        <label>Year of Passing * <b style="color:red" id="yop0"></b></label>
                                                        <select name="year_of_passing0[]" id="year_of_passing0" class="form-control" required multiple="multiple" onchange="enable_addmore(0)">
                                                            <?php
                                                            foreach ($year as $key => $value) { ?>
                                                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>

                                                            <?php }
                                                            ?>

                                                        </select>

                                                    </div>

                                                </td>


                                            </tr>
                                            <tr id="row0">

                                                <td>
                                                    <div class="col-md col-xs-12 form-group">
                                                        <label>Select State</label>
                                                        <select class="form-control" name="student_state0[]" id="student_state0" onchange="enable_district(0)" multiple="multiple">
                                                            <?php
                                                            $state_sql = "SELECT * FROM state_master";
                                                            $state_res = mysqli_query($conn, $state_sql);
                                                            while ($state_res_row = mysqli_fetch_assoc($state_res)) {
                                                            ?>
                                                                <option value='<?php echo $state_res_row['state_id']; ?>'> <?php echo $state_res_row['state_name']; ?> </option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-md col-xs-12 form-group">
                                                        <label>Select District</label>
                                                        <select class="form-control" name="student_district0[]" id="student_district0" multiple="multiple">
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>

                                                <td>
                                                    <div class="col-md add-more1">
                                                        <label for="student_stream"></label>
                                                        <div class="form-group">
                                                            <input type="hidden" name="total_count" id="total_count" value="0" />
                                                            <button type="button" id="add_more_new" name="add_more" class="btn btn-danger btn_row" disabled>+</button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                </section>
                            </div>
                        </div>



                        <!-- <section id="mainSection">
                  <h3>Who Can Apply?</h3>-->

                        <!-- <div class="col-md-6 col-xs-12 form-group">
                          <label>Select State*</label>
                          <select class="form-control" name="student_state" id="student_state" required>
                            <option value="">Select State</option>
                            <?php
                            // $state_id = array();
                            //   $state_sql = "SELECT * FROM state_master";
                            //   $state_res = mysqli_query($conn, $state_sql);
                            //   while ($state_row = mysqli_fetch_assoc($state_res)) {
                            //     $state_id[] = $state_row['state_id'];
                            //   }
                            //   $state_code = implode(',', $state_id);
                            // echo "<pre>";
                            // print_r($state_code);
                            ?>
                            <option value="1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37">All India</option> 
                            <option value="<?php //echo $state_code; 
                                            ?>">All India</option>
                            <?php
                            // $corporate_uid = $_SESSION['corporate_id'];
                            // $sql = "Select state,state_name from corporate_locations a inner join state_master d on state = state_id where a.corporate_uid = '$corporate_uid'";
                            // $res = mysqli_query($conn, $sql);
                            // if ($res) {
                            //   while ($row = mysqli_fetch_assoc($res)) {
                            //     echo "<option value='" . $row['state'] . "'>" . $row['state_name'] . "</option>";
                            //   }
                            // }
                            ?>
                          </select>
                        </div> -->
                        <!-- <div class="col-md-6 col-xs-12 form-group">
                            <label>Select District*</label>
                            <select class="form-control" name="student_district" id="student_district" disabled="true" required>
                              <?php
                                // $district_id = array();
                                // $district_sql = "SELECT dm.district_id FROM corporate_register as cr  JOIN district_master as dm ON cr.corporate_state = dm.state_id WHERE cr.corporate_uid = '".$corporate_uid."'";
                                // $district_res = mysqli_query($conn, $district_sql);
                                // if(mysqli_num_rows($district_res) > 0) {
                                //   while ($district_row = mysqli_fetch_assoc($district_res)) {
                                //     $district_id[] = $district_row['district_id'];
                                //   }
                                // }
                                // $district_code = implode(',',$district_id);

                                ?>
                              <option value="" selected>Select District</option>
                              <option value="<?php //echo $district_code; 
                                                ?>">All District</option>
                              <?php
                                //$corporate_uid = $_SESSION['corporate_id'];
                                //$sql = "Select district_name,district_id from corporate_locations a inner join district_master d on district = district_id where a.corporate_uid = '$corporate_uid'";
                                //$res = mysqli_query($conn, $sql);
                                //if ($res) {
                                //while ($row = mysqli_fetch_assoc($res)) {
                                //echo "<option value='" . $row['district_id'] . "'>" . $row['district_name'] . "</option>";
                                //}
                                // }
                                ?>
                              
                            </select>
                        </div> -->
                        <!--</section>-->
                        <!-- <div class="col-md-4 col-xs-12 form-group">
              <label>Duration in Month*</label>
             <input type="number" name="duration" id="duration" class="form-control" onchange="validatenumber(this);" required maxlength="3" Placeholder="Enter Duration">
              <select name="duration" id="" class="form-control">
                <option disabled selected value="">Duration in Month</option>
                <option value="6">6 Months</option>
                <option value="12">12 Months</option>
                <option value="18">18 Months</option>
                <option value="24">24 Months</option>
                <option value="30">30 Months</option>
                <option value="36">36 Months</option>
              </select>
            </div> -->
                        <div class="col-md-4 col-xs-12 form-group">
                            <label> Mobile Number *</label>
                            <input type="text" name="mobile" id="mobile" onchange="phonenumber2(this);" required maxlength="10" placeholder="Mobile Number" class="form-control">
                        </div>
                        <div class="col-md-4 col-xs-12 form-group">
                            <label> Std Code </label>
                            <input type="text" name="std" id="std" onchange="validatenumber(this);" maxlength="4" placeholder="Std Code" class="form-control">
                        </div>
                        <div class="col-md-4 col-xs-12 form-group">
                            <label> Land Line Number </label>
                            <input type="text" name="phone" id="phone" onchange="validatenumber(this);" maxlength="7" placeholder="Land Line Number" class="form-control">
                        </div>
                        <div class="col-md-4 col-xs-12 form-group">
                            <label> Email *</label>
                            <input type="email" name="email" id="email" placeholder="Email" onchange="validateemail(this);" required maxlength="100" class="form-control">
                        </div>
                        <input type="hidden" name="perks">
                        <input type="hidden" name="terms_of_enagagement">


                        <div class="col-md-6 col-xs-12 form-group">
                            <label>Any supporting Document<span style="color: red;">* (only pdf) </span></label>
                            <input class="form-control" type="file" name="image_1" required id="image_1" onchange="return fileValidation()">
                        </div>
                        <div class="col-md-12 col-xs-12 form-group">
                            <input type="submit" class="btn btn-primary" name="post_aprenticeship_ai2" id="post_aprenticeship_new" value='Submit' />
                        </div>
                    </form>
</section>
</div>
</div>
</div>
</div>
</div>
</section>
<input type="hidden" id="box_id" value="" />

<?php include('../include/footer.inc.php') ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">

<script type="text/javascript">
    $(document).ready(function() {
        $('#course_type_new0').change(function() {

            var course_val = $('#course_type_new0').val();
            // alert(course_val);
            if (course_val == '3' || course_val == '9' || course_val == '7' || course_val == '1' || course_val == '2' || course_val == '8') {
                $('#duration0').html('<option disabled selected value="" required>Duration in Month</option> <option value="12">12 Months</option><option value="18">18 Months</option><option value="24">24 Months</option><option value="30">30 Months</option><option value="36">36 Months</option>');
            } else {
                $('#duration0').html('<option disabled selected value="" required>Duration in Month</option> <option value="12">12 Months</option><option value="18">18 Months</option>');

            }

        });
    });



    function check_stipend(id, row) {
        var stipend = id.value;
        if (row == 0) {
            var student_course = $('#course_type_new' + row).val();
        } else {
            var student_course = $('#course_type' + row).val();

        }
        $.ajax({
            url: "../src/ajax/course.php",
            method: "post",
            data: {
                c_type: student_course,
            },
            success: function(data) {
                debugger;
                if (parseInt(stipend) < parseInt(data)) {
                    alert("Stipend can't be less than " + data);
                    id.value = data
                } else {}
            }
        });
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {

        $('#post_aprenticeship_new').click(function() {
            // alert('test')
            var tot_count = $('#total_count').val();
            var col = $('#location').val();
            if (col == '') {
                alert('First Select Location of the Office');
                $("#loc").html("First Select Location of the Office");
                $("#loc").focus();
                $("#loc").show().delay(5000).fadeOut();
                $(window).scrollTop(0);


            } else {
                if (tot_count > 0) {
                    for (let i = 0; i <= tot_count; i++) {
                        var gen = $('#gender' + i).val();
                        var yop = $('#year_of_passing' + i).val();

                        if (gen == '') {
                            $("#gen" + i).focus();
                            $("#gen" + i).html("Please select gender");
                            $("#gen" + i).show().delay(5000).fadeOut();


                        } else if (yop == '') {
                            $("#yop" + i).focus();
                            $("#yop" + i).html("Select Year of Passing");
                            $("#yop" + i).show().delay(5000).fadeOut();

                        }
                    }

                } else if (tot_count == 0) {

                    var gen = $('#gender' + tot_count).val();
                    var yop = $('#year_of_passing' + tot_count).val();

                    if (gen == '') {
                        $("#gen" + tot_count).focus();
                        $("#gen" + tot_count).html("Please select gender");
                        $("#gen" + tot_count).show().delay(5000).fadeOut();


                    } else if (yop == '') {
                        $("#yop" + tot_count).focus();
                        $("#yop" + tot_count).html("Select Year of Passing");
                        $("#yop" + tot_count).show().delay(5000).fadeOut();

                    }

                }
            }


        });
    });
</script>

<script type="text/javascript">
    function get_qualification(id, row) {
        var student_course = id.value;
        $.ajax({
            url: "../src/ajax/course.php",
            method: "post",
            data: {
                course_type: student_course,
            },
            success: function(data) {
                var html = '';
                html += data;
                //$('#student_course'+row).html(html);
                $('#student_course' + row).empty().append(html);
                // $('#item_sub_category').multiselect('refresh'); 
            }
        });

        $.ajax({
            url: "../src/ajax/course.php",
            method: "post",
            data: {
                c_type: student_course,
            },
            success: function(data) {
                $('#amount_aprenticeship' + row).val(data);
            }
        });
    }

    function get_spec(id, row) {
        var student_course = id.value;
        // var student_stream = $(this).data('sub_category_id');
        $.ajax({
            url: "../src/ajax/stream.php",
            method: "post",
            data: {
                course_id: student_course,
            },
            success: function(data) {
                var html = '';
                html += data;
                $('#item_sub_category' + row).empty().html(html);
                //$("#item_sub_category" + row).html(data);
                $('#item_sub_category' + row).multiselect("destroy").multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    maxHeight: 150

                });
            }
        });
    }

    function enable_district(row) {
        //$('#student_district0').val()='';
        var state = $('#student_state' + row).val();

        $('#student_district' + row).val('');
        $.ajax({
            url: "../src/ajax/district.php",
            method: "post",
            data: {
                post_state: state,
            },
            success: function(data) {

                $("#student_district" + row).html(data);
                $('#student_district' + row).multiselect("destroy").multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    maxHeight: 150

                });
            }
        });
    }

    function enable_addmore(row) {
        if ($('#year_of_passing' + row).val() != null) {
            $('#add_more_new').prop('disabled', false);
        } else {
            $('#add_more_new').prop('disabled', true);
        }
    }
</script>

<script>
    $(document).ready(function() {
        var i = 0;

        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            //console.log("button id",button_id);
            var t_count = $('#total_count').val();
            $('#total_count').val(t_count - 1);

            for (var j = 0; j < 3; j++) {
                $('#row' + button_id + '').remove();
            }
        });

        $(document).on('change', '.student_course_type', function() {
            var student_course_type = $(this).val();
            var student_stream = $(this).data('sub_category_id_new');
            $.ajax({
                url: "../src/ajax/course.php",
                method: "post",
                data: {
                    course_type: student_course_type,
                },
                success: function(data) {
                    var html = '';
                    html += data;
                    $('#item_sub_category_new' + student_stream).empty().html(html);
                    /*    $('#item_sub_category' + student_stream).multiselect('refresh'); */
                }
            })
        });

        $(document).on('change', '.student_course', function() {
            var student_course = $(this).val();
            var student_stream = $(this).data('sub_category_id');
            $.ajax({
                url: "../src/ajax/stream.php",
                method: "post",
                data: {
                    course_id: student_course,
                },
                success: function(data) {
                    var html = '';
                    html += data;
                    $('#item_sub_category' + student_stream).empty().html(html);
                }
            })
        });

        $('#amount_aprenticeship').on('keyup', function() {
            if ($('#amount_aprenticeship').val() != null) {
                $('#add_more_new').prop('disabled', false);
            } else {
                $('#add_more_new').prop('disabled', true);
            }
        });
        //
        var i = 0;
        $('#add_more_new').click(function() {

            i++;
            $('#box_id').val(i);
            $('#total_count').val(i);
            $('#dynamic_course').append('<tr id="row' + i + '"><td><div style="padding:20px" class="col-md  col-xs-12 form-group type1" ><label for="qualification" style="float:left">Course Type<b style="color:red">*</b></label><div class="form-group"><select class="form-control border-dark student_course_type" name="course_type' + i + '[]" id="course_type' + i + '" data-sub_category_id_new="' + i + '" required onchange="get_qualification(this,' + i + ')"><option value="">- Select Your Course Type -</option> <?php $sql = "SELECT * from course_type order by course_type asc";
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    $res = mysqli_query($conn, $sql);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    while ($row = mysqli_fetch_assoc($res)) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        echo '<option value="' . $row['id'] . '">' . $row['course_type'] . '</option>';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    } ?> </select></div></div></td><td><div class="col-md  col-xs-12 form-group qualification1" style="padding:20px"><label for="qualification" style="float:left">Qualification<b style="color:red">*</b></label><div class="form-group"><select class="form-control border-dark student_course" name="student_course' + i + '[]" id="student_course' + i + '" data-sub_category_id="' + i + '" required onchange="get_spec(this,' + i + ')"><option value="">- Select Your Course -</option></select></div></div></td><td><div class="col-md  col-xs-12 form-group specialisation1" style="float:left;padding: 20px;" ><label for="student_stream" style="float:left">Specialisation<b style="color:red">*</b></label><div class="form-group"><select class="form-control border-dark item_sub_category" name="student_stream' + i + '[]" id="item_sub_category' + i + '" multiple="multiple" required><option value=" "disabled>--Select the specialisation of the course--</option></select></div></div></td><td><div class="col-md  col-xs-12 form-group specialisation1" style=" padding: 20px;"><label for="student_stream">Minimum Percentage<b style="color:red">*</b></label><div class="form-group"><input type="text" class="form-control" onchange="intercheck(this)" name="min_percenta' + i + '[]"  id="min_percenta' + i + '" onkeyup="validatenumber(this);" maxlength="3" max="100" required></div></div></td></tr><tr id="row' + i + '"><td><div class="col-md col-xs-12 form-group amount1"><label>Amount of Stipend per Month *</label><input type="number" id="amount_aprenticeship' + i + '" name="amount_aprenticeship' + i + '[]" placeholder="Amount of Salary per Month" class="form-control" id="stipend' + i + '" required onchange="check_stipend(this,' + i + ')" onkeyup="validatenumber(this);"></div></td><td><div class="col-md col-xs-12 form-group"><label>Duration in Month*</label><select name="duration' + i + '[]" id="duration' + i +
                '" class="form-control" required><option disabled selected value="">Duration in Month</option><option value="6">6 Months</option><option value="12">12 Months</option><option value="18">18 Months</option><option value="24">24 Months</option><option value="30">30 Months</option><option value="36">36 Months</option></select></div></td><td><div class="col-md col-xs-12 form-group"><label>Gender * <b style="color:red" id="gen' + i + '"></b></label><select class="form-control" type="text" name="gender' + i + '[]" required id="gender' + i + '" multiple="multiple"><option value="M">Male</option><option value="F">Female</option><option value="T">Transgender</option></select></div></td><td><div class="col-md col-xs-12 form-group"><label>Year of Passing * <b style="color:red" id="yop' + i + '"></b></label><select name="year_of_passing' + i + '[]" id="year_of_passing' + i + '" class="form-control" required multiple="multiple"><?php foreach ($year as $key => $value) { ?><option value="<?php echo $key; ?>"><?php echo $value; ?></option><?php } ?></select></div></td></tr><tr style="border-bottom: 2px solid #3171db;" id="row' + i + '"><td><div class="col-md col-xs-12 form-group"><label>Select State</label><select class="form-control" name="student_state' + i + '[]" id="student_state' + i + '"  onchange="enable_district(' + i + ')" multiple="multiple"><?php $state_sql = "SELECT * FROM state_master";
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            $state_res = mysqli_query($conn, $state_sql);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            while ($state_res_row = mysqli_fetch_assoc($state_res)) { ?><option value="<?php echo $state_res_row['state_id']; ?>"><?php echo $state_res_row['state_name']; ?></option><?php } ?></select></div></td><td><div class="col-md col-xs-12 form-group"><label>Select District</label><select class="form-control" name="student_district' + i + '[]" id="student_district' + i + '" multiple="multiple"></select></div></td><td><div class="col-md" style="float:left"><div class="form-group"><button id="' + i + '" name="remove" class="btn btn-danger btn_remove">X</button></div></div></td></tr>');

            if (i == '9') {
                $('#add_more_new').css('display', 'none');
            } else {
                $('#add_more_new').css('display', 'block');
            }
            var get_val = $('#box_id').val();

            for (let j = 0; j <= parseInt(get_val); j++) {
                // alert(j);

                $("#course_type" + j).change(function() {
                    var course_val = $("#course_type" + j).val();
                    if (course_val == "3" || course_val == "9" || course_val == "7" || course_val == "1" || course_val == "2" || course_val == "8") {
                        $("#duration" + j).html('<option disabled selected value="" required>Duration in Month</option> <option value="12">12 Months</option><option value="18">18 Months</option><option value="24">24 Months</option><option value="30">30 Months</option><option value="36">36 Months</option>');
                    } else {
                        $("#duration" + j).html('<option disabled selected value="" required>Duration in Month</option><option value="6">6 Months</option> <option value="12">12 Months</option>');
                    }
                });
            }

            $('#gender' + i).multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 150

            });
            $('#item_sub_category' + i).multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 150

            });

            $('#year_of_passing' + i).multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 150

            });
            $('#student_state' + i).multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 150

            });
            $('#student_district' + i).multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 150

            });



            $('#stipend' + i).on('change', function() {
                var student_course_type = $('#course_type' + i).val();
                console.log('course_type', student_course_type)
                if (student_course_type == 1 || student_course_type == 2 || student_course_type == 8 || student_course_type == 4 || student_course_type == 5 || student_course_type == 11) {
                    if ($('#stipend' + i).val() < 9000) {
                        console.log('stipend', $('#stipend' + i).val())
                        alert("Amount must be greater than or equal to 9000");
                        $('#stipend' + i).val('');
                    }

                } else if (student_course_type == 3 || student_course_type == 9 || student_course_type == 7 || student_course_type == 6 || student_course_type == 9 || student_course_type == 10 || student_course_type == 12) {
                    if ($('#stipend' + i).val() < 8000) {
                        alert("Amount must be greater than or equal to 8000");
                        $('#stipend' + i).val('');
                    }
                }
            });
        });
        $('#course_type_new').change(function() {
            var student_course = $(this).val();
            $.ajax({
                url: "../src/ajax/course.php",
                method: "post",
                data: {
                    course_type: student_course,
                },
                success: function(data) {
                    var html = '';
                    html += data;
                    $('#student_course').html(html);
                    $('#student_course').empty().append(html);
                    /*  $('#item_sub_category').multiselect('refresh'); */
                }
            })
        });



        $('#student_course').change(function() {
            var l = $('.item_sub_category').map((_, el) => el.value).get();
            console.log(l);
            var arr = l.filter(function(v) {
                return v !== ''
            });
            var arr = arr.filter(function(v) {
                return v !== ' '
            });

            var student_course = $(this).val();
            $.ajax({
                url: "../src/ajax/stream.php",
                method: "post",
                data: {
                    course_id: student_course,
                    arr: arr
                },
                success: function(data) {
                    var html = '';
                    html += data;
                    // $('#item_sub_category').html(html);
                    $('#item_sub_category').empty().append(html);
                    /*    $('#item_sub_category').multiselect('refresh'); */
                }
            })
        });



    });
</script>

<script type="text/javascript">
    $('#graduation_type').on('change', function() {
        var course_type = this.value;
        alert(course_type);
        $.ajax({
            url: "./../src/ajax/course.php",
            type: "POST",
            data: {
                course_type: course_type
            },
            cache: false,
            success: function(result) {
                $("#student_course").html(result);
            }
        });
    });
    $('#student_course').on('change', function() {
        var l = $('.item_sub_category').map((_, el) => el.value).get();
        var arr = l.filter(function(v) {
            return v !== ''
        });
        var arr = arr.filter(function(v) {
            return v !== ' '
        });
        var course_id = this.value;
        $.ajax({
            url: "./../src/ajax/stream.php",
            type: "POST",
            data: {
                course_id: course_id,
                arr: arr
            },
            cache: false,
            success: function(result) {
                $("#specialisation ").html(result);
            }
        });
    });
    $('#reimbursement_type').on('change', function() {
        var reimbursement_type = this.value;
        //alert(reimbursement_type);

        if (reimbursement_type == 'No Reimbursement') {
            Swal.fire(
                'No Reimbursement- ',
                'You dont receive any financial compensation for No reimbursement requirements'
            )
        } else {
            Swal.fire(
                'Full Reimbursement- ',
                'You will receive compensation of Rs.9000 for degree, Rs.6000 for dimploma students '
            )
        }
    });
    $('#interns').on('change', function() {
        var intern = parseInt(this.value);
        var max = parseInt($('#max_interns').val());
        //alert(max);
        //var min = $('#min_interns').val();
        if (intern > max) {
            Swal.fire(
                'Exceeded ',
                'You can`t fill more than ' + max
            )
            $('#interns').val('');
        }
    });

    $('#amount_aprenticeship').on('change', function() {
        let course_type_new = document.getElementById("course_type_new").value;
        if (course_type_new == 1 || course_type_new == 2 || course_type_new == 8 || course_type_new == 4 || course_type_new == 5 || course_type_new == 11) {
            if ($('#amount_aprenticeship').val() < 9000) {
                console.log('amoutn_apprenticeship', $('#amount_aprenticeship').val())
                alert("Amount must be greater than or equal to 9000");
                $('#amount_aprenticeship').val('');
            }
        } else if (course_type_new == 3 || course_type_new == 9 || course_type_new == 7 || course_type_new == 6 || course_type_new == 9 || course_type_new == 10 || course_type_new == 12) {
            if ($('#amount_aprenticeship').val() < 8000) {
                alert("Amount must be greater than or equal to 8000");
                $('#amount_aprenticeship').val('');
            }
        }
    });

    function intercheck(all) {
        if (all.value < 1) {
            all.focus();
            all.value = ""
            alert("Minimum Percentage Can't be 0");
        } else if (all.value < 35) {
            all.focus();
            all.value = ""
            alert("Minimum Percentage should be greater tha 35");
        }
    }
</script>
<script>
    // document.getElementById("newsectionbtn").onclick = function() {
    //   var container = document.getElementById("container1");
    //   var section = document.getElementById("mainSection");
    //   container.appendChild(section.cloneNode(true));
    // }
</script>

<script>
    function fileValidation() {
        var fileInput =
            document.getElementById('image_1');

        var filePath = fileInput.value;

        // Allowing file type
        var allowedExtensions =
            /(\.pdf)$/i;

        if (!allowedExtensions.exec(filePath)) {
            alert('Invalid file type');
            fileInput.value = '';
            return false;
        }
    }

    $('#student_state').on('change', function() {
        var state = $('#student_state option:selected').text();
        if (state == 'All India' || state == 'Select State') {
            $('#student_district').val('');
            jQuery('#student_district').attr("disabled", true);

        } else {
            jQuery('#student_district').attr("disabled", false);
            $('#student_district').val('');
        }
    });
</script>

<script>
    $(document).ready(function() {

        $('#location').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 150

        });
        $('#industry_sub_sector').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 150
        });
        $('#student_state0').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 150

        });

        $('#student_district0').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 150

        });
        $('#year_of_passing0').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 150

        });

        $('#gender0').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 150

        });
        $('#item_sub_category0').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 150
        });

    });
</script>

<style type="text/css">
    .add {
        border: 1px solid #ff865b !important;
    }

    #table_wrapper {
        border: 1px solid #ff865b !important;
        width: 107%;
    }

    .newfildterStnd table tr td {

        border-right: 1px solid #ccc;

        padding: 8px;

        border-bottom: #ccc 1px solid;

    }

    .filter {
        margin-right: 50%;
    }

    .multiselect-clear-filter {
        display: none;
    }

    .input-group .form-control {
        width: auto;
    }

    .multiselect-selected-text {
        width: auto;
        min-width: 280px;
    }


    .btn-default {
        height: 55%;
        border: 1px solid #ff865b !important;


    }

    .btn-group>.btn:first-child {
        width: auto;
        min-width: 280px;
    }



    .dropdown-menu>.active>a,
    .dropdown-menu>.active>a:focus,
    .dropdown-menu>.active>a:hover {
        color: #0e0d0d;
    }
</style>
</body>

</html>