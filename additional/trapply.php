<?php
date_default_timezone_set('Asia/Kolkata');
$today = date("y-m-d");
include('../connection/db.php');
$flag = false;
if(isset($_GET['id']) && isset($_SESSION['email'])){
    $id = $_GET['id'];
    $query = "SELECT *,`dept`.`dept_id` AS 'dept_id' FROM `training`,`dept` WHERE `training`.`department` = `dept`.`dept_id` AND `training`.`tid`= '$id' AND `training`.`last_apply_date` >= '$today' ";
    $result = mysqli_query($conn, $query);
    $rows = mysqli_fetch_array($result);
    $dept = $rows['dept_id'];
    
    $email = $_SESSION['email'];
    $query = "SELECT * FROM `signup` WHERE `email`= '$email' AND `dept` = '$dept' AND `status` = 0 ";
    $result1 = mysqli_query($conn, $query);
    if(mysqli_num_rows($result1) == 1){
        $flag = true;
    }
    $rows1 = mysqli_fetch_array($result1);
    if(!empty($rows1['sem_marks'])){
        $marks = explode("/", $rows1['sem_marks']);
        $lenm = count($marks);
    }
    $t_id = $rows['tid'];
    $s_id = $rows1['id'];
    $std = makautStdDatails($rows1['rollno']);
    $std_sem  = sem($std['year']);
    $index = $std_sem - 2;
    $sem_marks = $marks[$index];
    $tr_sem = $rows['sem'];
    $tester = "SELECT * FROM `apply_training` WHERE `std_id`= '$s_id' AND `tr_id` = '$t_id' ";
    $ex = mysqli_query($conn, $tester);
    if(mysqli_num_rows($result) == 1){
        if(!empty($sem_marks)){
            if($flag && $std_sem == $tr_sem){
                if(mysqli_num_rows($ex) == 0){
                    $query2 = "INSERT INTO `apply_training`(`std_id`, `tr_id`) VALUES ($s_id,$t_id)";
                    $result = mysqli_query($conn, $query2);
                    if($result){
                        $_SESSION['message'] = 'Successfully Applied';
                        $_SESSION['message_type'] = 'success';
                        header("Location: training_show.php?id=$id");
                        //echo "submit";
                    }else{
                        $_SESSION['message'] = 'Ohhh Not Applied';
                        $_SESSION['message_type'] = 'danger';
                        header("Location: training_show.php?id=$id");
                        //echo "not submit";
                    }
                }else{
                    $_SESSION['message'] = 'Ohhh Already Applied';
                    $_SESSION['message_type'] = 'warning';
                    header("Location: training_show.php?id=$id");
                    //echo "Already applied";
                }
            }else{
                $_SESSION['message'] = 'You Are Not Applicable';
                $_SESSION['message_type'] = 'warning';
                header("Location: training_show.php?id=$id");
                //echo "not applicable";
            }
        }else{
            $_SESSION['message'] = 'Submit Semester marks first';
            $_SESSION['message_type'] = 'danger';
            header("Location: placement_show.php?id=$id");
        }
    }
    else{
        $_SESSION['message'] = 'Not Allowed To Access This Page ';
        $_SESSION['message_type'] = 'danger';
        header("Location: training_show.php?id=$id");
        //echo "error1";
    }
}else{
    $_SESSION['message'] = 'Signup First';
    $_SESSION['message_type'] = 'danger';
    header("Location: signup.php?");
    //echo "error2";
}
function sem($year){
    $sem = null;
    $year = date("y")-$year;
    $month = date("m")-6;
    if($year == 0){
        $sem = 1;
    }
    elseif($year == 1){
        if($month<= 0 && $month>=-5){
            $sem = 2;
        }elseif($month>=1 && $month<=6){
            $sem = 3; 
        }
    }
    elseif($year == 2){
        if($month<= 0 && $month>=-5){
            $sem = 4; 
        }elseif($month>=1 && $month<=6){
            $sem = 5; 
        }
    }
    elseif($year == 3){
        if($month<= 0 && $month>=-5){
            $sem = 6; 
        }elseif($month>=1 && $month<=6){
            $sem = 7; 
        }
    }
    elseif($year == 4){
        if($month<= 0 && $month>=-5){
            $sem = 8; 
        }elseif($month>=1 && $month<=6){
            $sem = 9; 
        }
    }elseif($year == 5){
        if($month<= 0 && $month>=-5){
            $sem = 10; 
        }elseif($month>=1 && $month<=6){
            $sem = 11; 
        }
    }
   
   return $sem;
}
function makautStdDatails ($roll){
    $clg = substr($roll,0,3);
    $dept = substr($roll,3,3);
    $year = substr($roll,6,2);
    $sn = substr($roll,8,3);
    $student = array("clg" =>$clg,"dept" =>$dept,"year" =>$year,"no" =>$sn);
    return $student;
  }
?>
