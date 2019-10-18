<?php
session_start();
$prefix = "../";
$prefix1 = "../";
$choose_rem = $dublicat = array();
$fom_show = "";
include_once $prefix . 'db.php';
$user = $_SESSION['user'];
$name = $_SESSION['name'];
$location = $prefix . "index.php";
if (isset($_SESSION['user'])) {
    
} else {
    header("Location: $location");
    exit;
}
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}
if (isset($_GET['Tmd3ZFVwaCtxWmNsYU1UODJWaUYxUT09'])) {
    $encrypt_action = $_GET['Tmd3ZFVwaCtxWmNsYU1UODJWaUYxUT09'];
    $action = encrypt_decrypt('decrypt', $encrypt_action);
    $encrypt_id = $_GET['WnAyV3FOdHJ3dkNiMEgrMGxVcytZUT09'];
    $id = encrypt_decrypt('decrypt', $encrypt_id);
    $fom_show = encrypt_decrypt('decrypt', $encrypt_id);
    //echo $action.$id;exit;
    if ($action == 'edit') {
        $sql = "SELECT `id`, `district_id`, `taluk_id`, `name`, `address`, `cdate`, `cby`, `cip`, `mdate`, `mby`, `mip` FROM `centre_master` WHERE  id='$id' ";
        $result = mysqli_query($mysqli, $sql);
        while ($data = mysqli_fetch_assoc($result)) {
            $taluk_id = $data['taluk_id'];
            $centre_name = $data['name'];
            $address = $data['address'];
        }
        //       echo $title_tamil ; exit;
    } else if ($action == 'delete') {
        $sql = "DELETE FROM `centre_master` where id='$id' AND `district_id`='$district_details[id]' ";
        $result = mysqli_query($mysqli, $sql);
        header("Location: create_center.php?msg=4");
    }
}

if (isset($_POST['get_centers'])) {
    $id = mysqli_real_escape_string($mysqli, $_POST['get_centers']);
    ?>
    <option value="0">Select Centre</option>
    <?php
    $sql = "SELECT * FROM  `centre_master`WHERE `taluk_id`='$id' and `district_id`='$district_details[id]' ";
    $result = mysqli_query($mysqli, $sql);
    while ($data = mysqli_fetch_assoc($result)) {
        ?>
        <option value="<?php echo $data['id']; ?>" ><?php echo $data['name'] . ' ( ' . $data['address'] . ')'; ?></option>
        <?php
    }
    exit;
}
if (isset($_POST['job_select'])) {
    $id = mysqli_real_escape_string($mysqli, $_POST['job_select']);
    $en_id = $_POST['job_select'];
    $sql = "SELECT  * FROM `job_applied`  WHERE `ai_job_id`='$id' and`district_id`='$district_details[id]'  and `mark`!='0' and `allotment_no`!='0' order by `allotment_no`  asc";
    $result = mysqli_query($mysqli, $sql);
    $i = 1;
    if (mysqli_num_rows($result)) {
        ?>
        <table class="table"><thead>
            <th>#</th>
            <th>Application</th>
            <th>Name</th>
            <th>Mobile / Email</th>
            <th>Mark</th>
            <th>Turn no</th>
            <th>Allotment No</th>
        </thead>
        <tbody>
            <?php
            while ($data = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $data['application_no']; ?></td>
                    <td class="text-uppercase"><?php echo $data['applicant_initials'] . ' ' . $data['applicant_name']; ?></td>
                    <td><?php echo $data['applicant_mobile'] . '<br>' . $data['applicant_email_id']; ?></td>
                    <td><?php echo $data['mark']; ?></td>
                    <td><?php echo $data['turn_no']; ?></td>
                    <td><?php echo $data['allotment_no']; ?></td>
                </tr> 
                <?php
                $i++;
            }
            ?> </tbody>
        </table>
    <?php
    }
    exit;
}
include('../mpdf60/mpdf.php');
require '../phpmailer/PHPMailerAutoload.php';

function mpdf_gen($html, $application_no) {
    $mpdf = new mPDF();
    $mpdf->setFooter('{PAGENO}');
    $mpdf->WriteHTML($html);
    $mpdf->SetDisplayMode('fullpage');
    ob_clean();
    //$mpdf->Output(__DIR__ . '../pdf/' . $outputs['application_no'] . '.pdf', "F");
    $mpdf->Output('../pdf/' . $application_no . '.pdf', "F");
}

function bulk_messagesend($msg, $mobile,$shortcode) {
    ob_start();
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://api.msg91.com/api/v2/sendsms?campaign=&response=&afterminutes=&schtime=&unicode=&flash=&message=&encrypt=&authkey=&mobiles=&route=&sender=&country=91",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{ \"sender\": \"$shortcode\", \"route\": \"4\", \"country\": \"91\", \"sms\": [ { \"message\": \"$msg\", \"to\": [$mobile] } ] }",
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTPHEADER => array(
            "authkey: 221068AW6ROwfK5b2782c0",
            "content-type: application/json"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        //     echo "cURL Error #:" . $err;
    } else {
        //echo $response;
    }
}



$main_folder = str_replace('\\', '/', dirname(__FILE__));
$document_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$main_folder = str_replace($document_root, '', $main_folder);
if ($main_folder) {
    $current_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($main_folder, '/') . '/';
} else {
    $current_url = $_SERVER['REQUEST_SCHEME'] . '://' . rtrim($_SERVER['HTTP_HOST'], '/') . '/';
}
if (isset($_POST['generate_call_letters'])) {
    $job_selected = mysqli_real_escape_string($mysqli, $_POST['job_selected']);  // Job ID Candidate  -----------------
    $interview_date = mysqli_real_escape_string($mysqli, $_POST['interview_date']);
    $reporting_time = mysqli_real_escape_string($mysqli, $_POST['reporting_time']);
    $sign_by = mysqli_real_escape_string($mysqli, $_POST['sign_by']); // Sigin By  -----------------
    $venue = mysqli_real_escape_string($mysqli, $_POST['venue']); // Sigin By  -----------------
    $subject = mysqli_real_escape_string($mysqli, $_POST['subject']); // Sigin By  -----------------
    $instruction_data = mysqli_real_escape_string($mysqli, $_POST['instruction_data']); // Hall  Instraction -----------------
    if (!empty($_FILES)) {
        $sigin_photo = count($_FILES['sigin_photo']['name']);
        if ($sigin_photo != 0) {
            if (file_exists($_FILES["sigin_photo"]["tmp_name"])) {
                $img_array = explode('.', basename($_FILES["sigin_photo"]["name"]));
                $img_name = 'call_letter' . $img_array[0] . mt_rand(100000, 999999) . '_' . time() . '.' . end($img_array);
                $sigin_photo = $current_url . "../images/" . $img_name;
                $target_path = "../images/" . $img_name;
                @move_uploaded_file($_FILES['sigin_photo']['tmp_name'], $target_path);
            }
        }
    }
    if (empty($sigin_photo)) {
        $sigin_photo = $sign;
    }
    $sql = "SELECT  GROUP_CONCAT(`id`) as `idds` FROM `job_applied`  WHERE `ai_job_id`='$job_selected' and`district_id`='$district_details[id]'  and `mark`!='0' and `allotment_no`!='0' order by `allotment_no`  asc";
    $resul = mysqli_query($mysqli, $sql);
    while ($data = mysqli_fetch_assoc($resul)) {
        $applied_id = $data['idds'];
    }
    $sql = "INSERT INTO `call_letter` (`job_id`,`district_id`, `applied_id`, `instraction`,`sigin_img`,`sign_by`, `subject`, `cdate`,`cby`,`cip`,`last_update`,`interview_date`,`reporting_time`,`venue`) 
VALUES ('$job_selected','$district_details[id]', '$applied_id' ,'$instruction_data','$sigin_photo','$sign_by', '$subject' ,'$datetime','$user','$_SERVER[REMOTE_ADDR]' ,'$datetime','$interview_date','$reporting_time','$venue')
ON DUPLICATE KEY UPDATE `last_update` = '$datetime', `applied_id`='$applied_id' , `subject`='$subject' ,`instraction`='$instruction_data' ,`sigin_img`='$sigin_photo' , `interview_date`='$interview_date',`reporting_time`='$reporting_time', `venue`='$venue' ,`sign_by`='$sign_by',mdate=concat(`mdate`,'|','$datetime'),mby=concat(`mby`,'|','$user'),mip=concat(`mip`,'|','$_SERVER[REMOTE_ADDR]');  ";
    $result = mysqli_query($mysqli, $sql);
    $applied_id1 = explode(",", $applied_id);
    $ap_ai_id = implode("','", $applied_id1);
    $from_email_id = $district_details['email_id'];
    $from_email_password = $district_details['email_password'];
//    echo 'sddddddddddddddd'.$from_email_id.$from_email_password; exit;

    $sql = "SELECT `id`,  `address`, `email`, `contact` FROM `contact` WHERE `district_id`='$district_details[id]' ";
    $add_re = mysqli_query($mysqli, $sql);
    while ($row = mysqli_fetch_assoc($add_re)) {
        $address = strip_tags($row['address']);
    }
    $sql = "SELECT `ja`.`applicant_initials`, `applicant_mobile`, `ja`.`applicant_email_id`, `cl`.`subject`,  `ja`.`ai_job_id`,`ja`.`application_no`, `ja`.`per_addressline1`, `ja`.`per_addressline2`, `ja`.`per_addressline3`, `ja`.`per_pincode`, `jp`.`job_id`, `jp`.`post_name`, date_format(`cl`.`interview_date`,'%d-%m-%Y') interview_date , `cl`.`reporting_time`, DAYNAME(`cl`.`interview_date`) as `interview_day`, `ja`.`applicant_name`, `ja`.`father_initials`, `ja`.`applicant_father`, date_format(`ja`.`applicant_dob`,'%d-%m-%Y') as applicant_dob, `ja`.`register_no` ,`ja`.`applicant_gender`, `ja`.`profile_photos`,`ja`.`sigin_photo`, `cl`.`venue`, `cl`.`instraction`,`cl`.`sigin_img`,`cl`.`sign_by` FROM `job_applied` `ja` CROSS JOIN `call_letter` `cl` ON `cl`.`job_id`=`ja`.`ai_job_id` CROSS JOIN `job_post` `jp` ON `jp`.`id`=`ja`.`ai_job_id` where `ja`.`id` IN ('$ap_ai_id') order by `allotment_no`  asc";
    //echo $sql; exit;
    $result = mysqli_query($mysqli, $sql);
    if (mysqli_num_rows($result)) {
        while ($outputs = mysqli_fetch_assoc($result)) {
            $applicant_email_id = $outputs['applicant_email_id'];
            $applicant_mobile[] = $outputs['applicant_mobile'];
            // echo "sql get:".$sql.$applicant_email_id;       
            $html = '<!DOCTYPE html>
              <html>
                <head>
                    <style>
                      table.venue {
    border-collapse: collapse;
        text-transform: uppercase;
  }
  th.venue, td.venue {
    border: 1px solid black;
    padding: 10px;
    text-align: left;
  } 
  table {
          text-transform: uppercase;
  }
                    </style>
                </head>
              <body style=\"font-family:Helvetica,sans-serif; font-size:20px; color:#0b2975; border-style: solid\"><table ><thead><tr><td align="center" style="font-size:14px" colspan="5"><b>  DISTRICT RECRUITMENT BUREAU COOPERATIVE DEPARTMENT<br>' . strtoupper($district_details['english']) . ' DISTRICT</b><br>
                        <strong>' . $address . '</strong><br><strong> Website: <a href="http://' . $district_details['host'] . '" target="_blank" >' . $district_details['host'] . '</a></strong></td>
                </tr>
            </thead>
        </table>
        <table class="ttts"  style="font-family:freeserif; border-collapse: collapse;width:100%"  >
            <tbody class="ttts">
                 <tr>
                    <td class="ttts"  style="color:blue" align="center" colspan="4">INTERVIEW CALL LETTER</td>
                </tr>
                 <tr>
                    <td class="ttts1"  colspan="4">TO<br>Application No : ' . $outputs['application_no'] . ',<br>Name : ' . $outputs['applicant_name'] . ' ' . $outputs['applicant_father'] . '<br>Address : ' . $outputs['per_addressline1'] . ', ' . $outputs['per_addressline2'] . ', ' . $outputs['per_addressline3'] . ', ' . $outputs['per_pincode'] . '</td>
                </tr>
                 <tr>
                    <td class="ttts1"  colspan="4"><br>Subject : ' . $outputs['subject'] . '</td>
                </tr>
                 <tr>
                     <td class="ttts1" align="left"   colspan="4"><br>Dear Candidate, </td>
                </tr>
                 <tr>
                     <td class="ttts1" align="left"   colspan="4">
                        <br><center><table class="venue"><tr class="venue"><td class="venue">Interview Date</td><td class="venue">' . $outputs['interview_date'] . '<br>' . $outputs['interview_day'] . '</td><td class="venue">Reporting Time</td><td class="venue">' . $outputs['reporting_time'] . '</td></tr><tr class="venue"><td class="venue" >Venue</td><td colspan="3" class="venue" >' . nl2br($outputs['venue']) . '</td></tr></table></center>
                    </td>
                </tr>
             </tbody>
        </table><br>
        <div   style="font-family:freeserif; border-collapse: collapse;width:100%;text-align:justify" >' . nl2br($outputs['instraction']) . '</div>
            <br>
        <div style="text-align:right">Yours faithfully,<br><img width="120px"  src="' . $outputs['sigin_img'] . '"><br>' . $outputs['sign_by'] . '</div><br>
        <div style="position: fixed; bottom:10px; text-align:right">Generated Time ' . $newDate . ' ' . $_SERVER['REMOTE_ADDR'] . '</div>
                </body>
                </html>';
            mpdf_gen($html, $outputs['application_no']);

            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = $from_email_id;
            $mail->Password = $from_email_password;
            $mail->setFrom($from_email_id, $district_details['english'] . ' DRB');
            if (filter_var($applicant_email_id, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($applicant_email_id);
            }
            $mail->Subject = 'INTERVIEW CALL LETTER';
            $mail->isHTML(TRUE);
            $message = <<<EOT
                  <div id=":pf" class="a3s aXjCH ">
                      <table style="width:600px;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#191919;border:1px solid #f0f0f0" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="border-style:hidden;border:1px solid #c9e3e4;padding:5px">
                          <center><h4 style="text-transform: uppercase">DISTRICT RECRUITMENT BUREAU COOPERATIVE DEPARTMENT&nbsp;$district_details[english] DISTRICT</h4>
                        <strong>$address</strong><strong> Website: <a href="http://$district_details[host]" target="_blank" >$district_details[host]</a></strong></center>
   <table style="margin:0 1px 3px 12px;line-height:23px" width="96%" border="0" cellspacing="10" cellpadding="0">
       <tbody>
           <tr>
               <td colspan="5" align="left" valign="top">
                   <p><strong>Dear $outputs[applicant_name] $outputs[applicant_father], We have sent your call letter. Please check below attachment.</strong></p>
                           <p class="" style="margin:0px 0px 0px 1px;border-bottom:1px solid #3333ff;color:#3333ff"><strong>Interview Details:</strong></p></td></tr><tr><td style="width:100px"  align="left" valign="top"><strong>Interview Date</strong></td><td style="width:10px" align="left" valign="top">:</td><td align="left" valign="top"  >$outputs[interview_date] ($outputs[interview_day])</td></tr><tr><td style="width:100px" align="left" valign="top" ><strong>Reporting Time</strong></td><td style="width:10px" align="left" valign="top">:</td><td align="left" valign="top">$outputs[reporting_time]</td></tr><tr ><td style="width:100px" align="left" valign="top" ><strong>Venue</strong></td><td style="width:10px" align="left" valign="top">:</td><td align="left" valign="top" class="">$outputs[venue]</td></tr><tr><td colspan="5"><p class="" style="margin:0px 0px 0px 1px;border-bottom:1px solid #3333ff;color:#3333ff"></p></td></tr><tr><td style="padding:0 15px 15px 15px" colspan="5"><p >Regards,</p><p style="text-transform:uppercase;" ><strong>DISTRICT RECRUITMENT BUREAU COOPERATIVE DEPARTMENT&nbsp;$district_details[english] DISTRICT</strong></p></td></tr></tbody></table>
              </div>
EOT;
            $mail->Body = $message;
            $mail->addAttachment('../pdf/' . $outputs['application_no'] . '.pdf');
            if (!$mail->send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
                $msg = '1';
            }
        }
        $msg="Dear Candidate, Your call letter has been generated. Kindly check your Email.";
    $send_mobile= implode(",", $applicant_mobile);
    bulk_messagesend($msg,$send_mobile,"DRB" . $district_details['short']);
    }
    
    echo "completed@#@".$job_selected;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Generate Interview Call Letter - <?php echo $district_details['tamil']; ?> | DRB</title>
<?php include_once $prefix . 'include/css.php'; ?>
        <script src="../assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <link type="text/css" rel="stylesheet" href="<?php echo $prefix; ?>assets/css/theme-1/libs/summernote/summernote.css?1425218701" />
        <script src="<?php echo $prefix; ?>assets/js/libs/summernote/summernote.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>

        <link type="text/css" rel="stylesheet" href="<?php echo $prefix; ?>assets/css/date_picker.css" />
        <style>
            .border_img {
                border-style: inset;
                padding: 4px;
            }
            .scroll123{
                height:500px;
                overflow-y: scroll;
            }
            .height-1 {
                height:auto;
                overflow-y: auto;
            }
        </style>
    </head>
    <body class="menubar-hoverable header-fixed menubar-pin menubar-visible ">
        <!-- BEGIN HEADER-->
<?php include_once $prefix . 'include/header.php'; ?>
        <!-- END HEADER-->

        <!-- BEGIN BASE-->
        <div id="base">
            <!-- BEGIN OFFCANVAS LEFT -->
            <div id="content">
                <section>
                    <div class="section-body contain-lg">                       
                        <div class="row">                                                                        
                            <div class="col-md-12">
                                <div class="card center-block">
                                    <div class="card-head style-primary">
                                        <header>Generate Interview Call Letter </header>
                                        <div class="tools pull-right ">
<!--                                            சேர்க்க&nbsp;<i class="fa fa-hand-o-right"></i><a  id="btnsclick"   class="btn btn-floating-action btn-default-light" data-toggle="tooltip" data-placement="top" data-original-title="Add Centre Details" href="#"><i class="fa fa-plus"></i></a>-->
                                        </div>
                                    </div>
                                    <div class="card-body"  id="formid"  >
                                        <div id="loading" style="display:none"><center><img src="../assets/images/load.gif"/></center></div>
                                        <form class="form form-validate "  role="form" method="POST" enctype="multipart/form-data"  >
                                            <div class="col-md-12 form-group">
                                                <div class="form-group">
                                                    <select id="job_select" name="job_select" class=" form-control select2-list borderinput coma_filter" >
                                                        <option value="" >-- Select Job  --</option>
                                                        <?php
                                                        $sql = "SELECT Distinct `jp`.* FROM `job_post` `jp`  INNER  JOIN `job_applied` `ja` ON `jp`.`id`=`ja`.`ai_job_id`  where `jp`.`district_id`='$district_details[id]' ";
                                                        $result = mysqli_query($mysqli, $sql);
                                                        while ($data = mysqli_fetch_assoc($result)) {
                                                            ?>
                                                            <option value="<?php echo $data['id'] ?>"><?php echo $data['post_name'] ?></option>
<?php } ?>
                                                    </select>
                                                    <label for="select1">Select Job</label>
                                                    <h4 id="append_status" class="text-bold text-center"></h4>
                                                </div>
                                            </div>
                                            <div class="col-md-12" id="interview_call" ></div>
                                            <div id="having_pending" hidden="">

                                                <div class="form-group sd-container">
                                                    <input type="date" data-date="" data-date-format="DD-MM-YYYY" class="form-control sd" id="interview_date" name="interview_date" value="" placeholder="Notification Date" id="notification_date" required=""  >
                                                    <span class="open-button">
                                                        <button type="button">📅</button>
                                                    </span>
                                                    <label for="regular1">Interview Date<span style="color:red;">*</span></label>
                                                </div>
                                                <div class="col-md-8 " style="background-color: beige;">
                                                    <div class="form-group">
                                                        <input type="text" id="reporting_time" placeholder="Ex: Fore Noon 10.00 AM to 01.00 PM" class="form-control  ">
                                                        <label>Reporting Time </label>
                                                        <p class="help-block">Give Reporting Time</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 form-group">
                                                    <h5>Venue</h5>
                                                    <textarea rows="2" id="venue" placeholder="Enter Venue "  class="form-control " required></textarea>
                                                </div>
                                                <div class="col-sm-12 form-group">
                                                    <h5>Subject</h5>
                                                    <textarea rows="2" id="subject" placeholder="Enter Subject "  class="form-control " required></textarea>
                                                </div>
                                                <div class="col-sm-12 form-group">
                                                    <h5>Interview Instruction</h5>
                                                    <textarea rows="15" id="instruction_data"  class="form-control " required></textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 ">
                                                        <div class="form-group">
                                                            <h6>Exam Controller Sign ( 3.5x1.5 cm), (File Size : 10kb To 20kb)</h6>
                                                            <input type="file" accept="image/*"  name="sigin_photo"  id="sigin_photo" class="" >
                                                            <img id="sign"  width="120px" height="40px" src="<?php echo $row['sign']; ?>" alt="your image" <?php if (empty($row['sign'])) { ?>style="display:none;" <?php } ?>/>
                                                        </div>  
                                                    </div>
                                                    <div class="col-md-12 ">
                                                        <div class="form-group">
                                                            <input type="text" name="sign_by"  id="sign_by"  class="form-control">
                                                            <label for="regular1">Sign By Content<span style="color:red">*</span></label>
                                                        </div>  
                                                    </div>  
                                                </div>

                                                <div class="col-md-12 form-group">
                                                    <button tabindex="3" type="button" onclick="location.reload()   " name="cancel" class="btn btn-flat btn-default-light ink-reaction pull-left">Cancel</button>
                                                    <button tabindex="2" type="button" data-keyboard="false" onclick="open_modal()" name="previewbtn" class="btn ink-reaction btn-raised btn-primary pull-right testform " id="preview_id"><i class="md md-remove-red-eye"></i>&nbsp;Confirm</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div><!--end .card-body -->
                                </div><!--end .card -->                            
                            </div><!--end .col -->
                        </div><!--end .row -->  
                        <?php
                        $id_string = encrypt_decrypt("encrypt", "id");
                        $action_string = encrypt_decrypt("encrypt", "action");
                        $edit_string = encrypt_decrypt("encrypt", "edit");
                        $active_string = encrypt_decrypt("encrypt", "active");
                        $deactive_string = encrypt_decrypt("encrypt", "deactive");
                        $delete_string = encrypt_decrypt("encrypt", "delete");
                        ?>
                        <div class="modal fade" id="modal-publish">           
                            <div class="modal-dialog white-modal modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header height-1">
                                        <h4 class="modal-title">Call Letter Generation </h4>
                                    </div>
                                    <div id="Image_view"  >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div><!--end .section-body -->
        </section>
    </div><!--end .section-body -->
    <!-- END CONTENT -->

    <!-- BEGIN MENUBAR-->
    <?php include_once $prefix . 'include/menubar.php'; ?>
    <!-- END MENUBAR -->
    <!-- BEGIN JAVASCRIPT -->
<?php //include_once $prefix . 'include/jsfiles.php';                                        ?>
    <script src="<?php echo $prefix; ?>assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/autosize/jquery.autosize.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/DataTables/jquery.dataTables.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/DataTables/extensions/ColVis/js/dataTables.colVis.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/DataTables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/toastr/toastr.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/source/App.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/source/AppNavigation.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/source/AppOffcanvas.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/source/AppCard.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/source/AppForm.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/source/AppNavSearch.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/source/AppVendor.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/demo/Demo.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/demo/DemoFormComponents.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/core/demo/DemoTableDynamic.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/select2/select2.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/libs/inputmask/jquery.inputmask.bundle.min.js"></script>
    <script src="<?php echo $prefix; ?>assets/js/date_picker_change.js"></script>


    <!-- END JAVASCRIPT -->
    <script>
                                                        toastr.options = {
                                                            "allowHtml": true
                                                        }
<?php if ($msg == '2') { ?>
                                                            Command: toastr["success"]("Added Successfully", "Success")
<?php } elseif ($msg == '1') {
    ?>
                                                            Command: toastr["error"]("Same Name already exist", "Error")
<?php } elseif ($msg == '3') { ?>
                                                            Command: toastr["success"]("Updated Successfully", "Success")
<?php } elseif ($msg == '4') { ?>
                                                            Command: toastr["success"]("Deleted Successfully", "Success")
<?php } elseif ($msg == '5') { ?>
                                                            Command: toastr["error"]("Cannot delete<br>Product Exist in this Category<br>Please change product detail first", "Error")
<?php } ?>
    </script>
    <script>
<?php if ($fom_show) { ?>
            $("#formid").show();
<?php } ?>
        var _URL = window.URL || window.webkitURL;
        var getSizeInCM;
        getSizeInCM = function (sizeInPX) {
            return sizeInPX * 2.54 / (96 * window.devicePixelRatio)
        };
        function check_sign_size(id, sizea, widthcm, heightcm) {
            console.log("Size" + sizea + ":" + widthcm + "X" + heightcm)
            if (sizea > "20480")
            {
                alert("Choose Correct Photo ");
                $(id).val("");
                $("#sign").hide();
            }
        }
        function readURL1(input) {
            if (input.files && input.files[0]) {
                var img, widthcm, heightcm;
                img = new Image();
                var imgsize = input.files[0].size;
                img.onload = function () {
                    //     alert(this.width + " " + this.height);
                    widthcm = getSizeInCM(this.width);
                    heightcm = getSizeInCM(this.height);
                    check_sign_size("#sigin_photo", imgsize, widthcm.toFixed(1), heightcm.toFixed(1));
                };
                img.src = _URL.createObjectURL(input.files[0]);
                var reader1 = new FileReader();
                reader1.onload = function (e) {
                    $('#sign').attr('src', e.target.result);
                    $("#sign").show();
                }
                reader1.readAsDataURL(input.files[0]);
            }
        }
        $("#sigin_photo").change(function () {
            readURL1(this);
        });
        $('.summernote').summernote({
            height: 200,
            minHeight: null,
            maxHeight: null,
            focus: true
        });
        $(document).on("click", "#generate_hall_ticket", function () {
            var form_data = new FormData();
            var job_select = $.trim($("#job_select").val());
            form_data.append('job_selected', job_select);
            var venue = $.trim($("#venue").val());
            form_data.append('venue', venue);
            var subject = $.trim($("#subject").val());
            form_data.append('subject', subject);
            var interview_date = $.trim($("#interview_date").val());
            form_data.append('interview_date', interview_date);
            var reporting_time = $.trim($("#reporting_time").val());
            form_data.append('reporting_time', reporting_time);
            var sign_by = $.trim($("#sign_by").val());
            form_data.append('sign_by', sign_by);
            var instruction_data = $.trim($("#instruction_data").val());
            form_data.append('instruction_data', instruction_data);
            form_data.append("sigin_photo", document.getElementById('sigin_photo').files[0]);
            form_data.append("generate_call_letters", "save");
            $("#Image_view").html('<div class="modal-body"  ><div class="card-body"><center><img src="../assets/images/load.gif"/><h3 class="text-bold text-danger text-center">Please Wait...</h3></center></div></div>');
            $.ajax({
                url: 'call_for_interview.php', // point to server-side PHP script 
                dataType: 'text', // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (data) {
                    console.log(data);
                    var res=data.split("@#@");
                    if (res[0] == 'completed')
                    {
                        $("#Image_view").html('<div class="modal-body "  ><div class="card-body"><h2 class="text-center text-bold text-info">Call Letter Generation  Completed</h2></div></div><div class="modal-footer"><a href="view_call_letter.php?jooid='+res[1]+'" class="btn btn-info pull-lift"  >View Call Letter</button></div>');
                    }
                }
            });
        });

        $(document).on("click", "#add_more_click", function () {
            var vlas = parseInt(0);
            var no_ofc = Number($(".no_of_centreclass:last").val());
            var centre_name = Number($(".cenre_select_class:last").val());
            var taluka_id_class = Number($(".taluka_id_class:last").val());
            switch (vlas) {
                case taluka_id_class:
                    $(".taluka_id_class:last").select2('open');
                    break;
                case centre_name:
                    $(".cenre_select_class:last").select2('open');
                    break;
                case no_ofc:
                    $(".no_of_centreclass:last").focus();
                    break;
                default:
                    $('select').select2('destroy');
                    $(".clone_data:last").clone().appendTo(".append_editas");
                    $("select:last option[value='']").removeAttr("disabled", true);
                    $('select').select2();
                    $(".remove_centre").show();
                    $(".no_of_centreclass:last").val("");
                    $(".taluka_id_class:last").val("").change();

            }
        });
        $(document).on("click", ".remove_centre", function () {
            $(this).closest(".clone_data").remove();
            var rem_this = $(this);
            var length = $(".remove_centre").length;
            if (length == '1') {
                $(".remove_centre").hide();
            }
            $(".cenre_select_class").attr("disabled", false);
            $(".cenre_select_class option:selected").each(function () {
                rem_this.closest(".clone_data").find(".cenre_select_class  option[value='" + $(this).val() + "']").attr("disabled", true);
            });
            todal_calc();
        });
        function todal_calc(thiss)
        {
            var a = parseInt(0);
            $(".no_of_centreclass").each(function () {
                a += parseInt($(this).val());
            });
            var a = isNaN(parseInt(a)) ? 0 : parseInt(a);
            var toala = $("#total_application").val();
            var instruction_data = $.trim($(".note-editable").val());
            var sign_by = $.trim($("#sign_by").val());
            switch (parseInt(toala)) {
                case parseInt(a):
                    $(".total_conuts").html('<center><span class="style-success  text-center small-padding">Total Exam Hall Enterd : ' + parseInt(a) + '</span></center>');
                    $("#add_more_click").attr("disabled", true);
                    $("#preview_id").attr("disabled", false);
                    $(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
                    break;
                default:
                    if (parseInt(a) > parseInt(toala))
                    {
                        $(".modal-dialog").removeClass("modal-lg").addClass("modal-md");
                        $('#modal-publish').modal({backdrop: 'static', keyboard: false})
                        $("#modal-publish").modal('show');
                        $("#Image_view").html('<div class="modal-body"  ><div class="card-body"><center><h3 class="text-bold text-danger text-center">Please Enter Correct Count,<br> You Entered Greater Then Applied Candidate Count </h3></center></div></div><div class="modal-footer"><button type="button" class="btn pull-lift btn-info" data-dismiss="modal">OK</button></div>');
                        $(thiss).val("");
                    }
                    $(".total_conuts").html('<center><span class="style-danger  text-center small-padding"> Total Exam Hall Enterd : ' + parseInt(a) + '</span></center>');
                    $("#add_more_click").attr("disabled", false);
                    $("#preview_id").attr("disabled", true);


            }
        }
        $(document).on("keyup", ".no_of_centreclass", function () {
            todal_calc(this);
        });
        $(document).on("change", "#job_select", function () {
            var id = $(this).val();
            $.post('call_for_interview.php',
                    {
                        job_select: id,
                    },
                    function (data, status) {
                        if (data) {
                            $("#interview_call").html(data);
                            $("#having_pending").show();
                        } else {
                            $("#interview_call").html("No Data Found");
                            $("#having_pending").hide();
                        }
                    });
        })
        function  regenerate(job_id) {
            $.post('call_for_interview.php',
                    {
                        regenerate_job: job_id,
                    },
                    function (data, status) {
                        console.log(data);
                        var obj = $.parseJSON(data);
                        if (obj.exam_date != "regenerate" && obj.exam_date != "empty")
                        {
                            $("#having_pending").show();
                            $("#exam_date").val(obj.exam_date);
                            if (parseInt(obj.pending) != parseInt(0)) {

                                $("#having_pending").html('<h3 class="text-bold text-danger text-center">Selected Job Having ' + parseInt(obj.pending) + ' Pending Application Verify or Reject It</h3>');
                            } else if (parseInt(obj.tobeverify) != parseInt(0)) {
                                $("#having_pending").html('<h3 class="text-bold text-danger text-center">Selected Job Having ' + parseInt(obj.tobeverify) + ' To Be Verify Application Verify or Reject  It</h3>');
                            }
                            $("#total_application").val(parseInt(obj.verified));
                            $("#append_status").html('<span class="style-primary small-padding">Total Applied :' + obj.allcount + '</span><span class="style-warning small-padding">To Be Verify :' + obj.tobeverify + '</span><span class="style-success small-padding">Verified :' + obj.verified + '</span><span class="style-danger small-padding">Pending :' + obj.pending + '</span><span class="style-primary small-padding">Rejected :' + obj.rejected + '</span>');
                        } else if (obj.exam_date == "empty") {
                            var job = obj.job_id;
                            //  $("#having_pending").show();
                            $("#append_status").html('<h3 class="text-bold text-danger text-center">Application Not Found</h3>');
                        } else {
                            var job = obj.job_id;
                            //  $("#having_pending").show();
                            $("#append_status").html('<h3 class="text-bold text-danger text-center">call Letter Generation Completed For This Job: <span class="btn btn-info btn-md" onclick="regenerate(' + job + ');" >Regenerate Now</span></h3>');
                        }
                    });
        }

        $(document).on("change", ".taluka_id_class", function () {
            var id = $(this).val();
            var thiss = $(this)
            var seledcenters = "";
            $(this).closest(".clone_data").css("background-color", 'red');
            $.post('call_for_interview.php',
                    {
                        get_centers: id,
                    },
                    function (data, status) {
                        $('select').select2('destroy');
                        thiss.closest(".clone_data").find(".cenre_select_class").html(data);
                        $('select').select2();
                        $(".cenre_select_class option:selected").each(function () {
                            thiss.closest(".clone_data").find(".cenre_select_class  option[value='" + $(this).val() + "']").attr("disabled", true);
                        });
                    });
        })
        function open_modal()
        {
            var sign_by = $.trim($("#sign_by").val());
            var reporting_time = $.trim($("#reporting_time").val());
            var instruction_data = $.trim($("#instruction_data").val());
            var interview_date = $.trim($("#interview_date").val());
            var venue = $.trim($("#venue").val());
            var subject = $.trim($("#subject").val());

            var sigin_photo = $.trim($("#sigin_photo").val());
            var job_select = $.trim($("#job_select").val());
            if (instruction_data != "" && sign_by != "" && reporting_time != "" && interview_date != "" && venue != "" && subject != "") {
                $('#modal-publish').modal({backdrop: 'static', keyboard: false})
                $("#modal-publish").modal('show');
                var job_name = $("#job_select option:selected").text();
                var total_application = $("#total_application").val();
                var html = "";
                var aa = 1;
                var aD = 0;
                html = '<div class="modal-body "  ><div class="card-body"><h2>Are You want to Generate Call Letter ?</h2></div></div><div class="modal-footer"><button type="button" class="btn btn-default pull-lift" data-dismiss="modal">Close</button><button type="button" class="btn btn-success pull-right" id="generate_hall_ticket">Generate Call Letter</button></div>';
                $("#Image_view").html(html);
            } else {
                if (interview_date == "") {
                    $("#interview_date").focus();
                    alert("Fill Interview Date");
                } else if (reporting_time == "") {
                    $("#reporting_time").focus();
                    alert("Fill Reporting Time");
                } else if (venue == "") {
                    $("#venue").focus();
                    alert("Fill Call letter venue");
                } else if (subject == "") {
                    $("#subject").focus();
                    alert("Fill Call letter Subject");
                } else if (instruction_data == "") {
                    $(".note-editable").focus();
                    alert("Fill Call letter Instruction");
                }  else if (sigin_photo == "") {
                    alert("Choose Call letter Sign");
                } else if (sign_by == "") {
                    $("#sign_by").focus();
                    alert("Fill Call letter Sign By");
                } 
            }
        }
    </script>
    <script>
        $('#datatable3').DataTable({
            "dom": 'lCfrtip',
            "order": [],
            "colVis": {
                "buttonText": "Columns",
                "overlayFade": 0,
                "align": "right"
            },
        });
    </script>
</body>
</html>