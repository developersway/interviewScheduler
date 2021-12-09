<?php
session_start();
$edit_id = isset($_SESSION['id'])?$_SESSION['id']:0;

$conn = mysqli_connect('localhost','root','','scaler');
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
      }

$form = isset($_GET['form'])? $_GET['form'] : "OO";
echo $form;
switch($form)
{
    
    case 'insert':
        $interviewerName = isset($_GET['interviewerName'])? $_GET['interviewerName'] : "";
        $interviewerEmail = isset($_GET['interviewerEmail'])? $_GET['interviewerEmail'] : "";
        $interviewees = isset($_GET['interviewees'])? $_GET['interviewees'] : array();
        $date = isset($_GET['date'])? $_GET['date'] : "";
        $startTime = isset($_GET['startTime'])? $_GET['startTime'] : "";
        $endTime = isset($_GET['endTime'])? $_GET['endTime'] : "";
        

        sscanf($startTime, "%d:%d:%d", $hours, $minutes, $seconds);
        $startTimeSeconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

        sscanf($endTime, "%d:%d:%d", $hours, $minutes, $seconds);
        $endTimeSeconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
        
        if($startTimeSeconds-$endTimeSeconds>0)
        {
            ?><script>
                alert('Invalid Start and End Time');
                window.location.href='./index.php';
            </script><?php
            return;
        }
        
        
        if(count($interviewees)<2 and $_SESSION['id']==0)
        {
            ?><script>
                alert('"Please Select more than 1 interviewee"');
                window.location.href='./index.php';
            </script><?php
            return;
        }

        foreach ($interviewees as $val){
            $select = "SELECT * FROM scheduledInterviews";
            $row = $conn->query($select);
            while($data = $row->fetch_assoc()){
                if($data['intervieweeName']==$val and $data['date']==$date and $data['id'] != $_SESSION['id'] and $data['status']==1)
                {
                    if(!($data['startTimeSeconds']-$endTimeSeconds>0 or $startTimeSeconds-$data['endTimeSeconds']>0))
                    {
                        ?><script>
                        alert('Schedule Clash! Please check the scheduled Interviews');
                        window.location.href='./index.php';
                        </script><?php
                        return;
                    }
                }
            }
        }

        foreach ($interviewees as $val){

            $insert = "Insert INTO `scheduledInterviews`(`interviewerName`,`interviewerEmail`,`intervieweeName`,`date`,`startTimeSeconds`,`endTimeSeconds`,`startTime`,`endTime`,`status`)
            VALUES('$interviewerName','$interviewerEmail','$val','$date','$startTimeSeconds','$endTimeSeconds','$startTime','$endTime',1)";

            $conn->query($insert);
        }

        $update = "UPDATE `scheduledInterviews` SET status = 0 WHERE `id`=$edit_id";
        $conn->query($update);

        $_SESSION['id'] = 0;
        ?><script>
                alert('Interview Scheduled');
                window.location.href='./index.php';
        </script><?php
        return;
    
    
    case 'delete':
        $id = isset($_GET['id'])?$_GET['id']:0;
        $update = "UPDATE `scheduledInterviews` SET status = 0 WHERE `id`=$id";

        $conn->query($update);
        ?><script>
                alert('Successfully Deleted');
                window.location.href='./index.php#admin';
        </script><?php
        return;
    
    case 'edit':
        $_SESSION['id'] = $_GET['id'];
        header('location:./index.php#scheduleForm');
        break;
    
    case 'cancelEdit':
        $_SESSION['id'] = 0;
        header('location:./index.php#admin');
    break;
}


?>