<?php 

    $conn = mysqli_connect('localhost','root','','scaler');
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
      }
    
    session_start();
    $edit_id = isset($_SESSION['id'])?$_SESSION['id']:0;

    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href='./css/index.css' >
    
    <title>Scaler</title>
</head>
<body>
    

<!-- Nav Bar------------------>
    <div class='navBar'>
        <div class='leftNav'>
            <div><a href='#scheduleForm'>Schedule an Interview</a></div>
            <div><a href='#lists'>Scheduled Interviews</a></div>
        </div>

        <div class='rightNav'>
            <a href='#admin'>Admin</a>
        </div>
    </div>

<!--  ------------------------------------------  -->

<!--  Schedule Interview  -->

<?php     
    

    $interviewerName = "";
    $interviewerEmail = "";
    $interviewees = "";
    $interviewDate = "";
    $interviewStartTime = "";
    $interviewEndTime = ""; 

    if($edit_id!=0)
    {
        $select = "SELECT * FROM `scheduledInterviews` WHERE `id`= $edit_id";
        $row = $conn->query($select);
        while($data = $row->fetch_assoc()){
            $interviewerName = $data['interviewerName'];
            $interviewerEmail = $data['interviewerEmail'];
            $interviewees = $data['intervieweeName'];
            $interviewDate = $data['date'];
            $interviewStartTime = $data['startTime'];
            $interviewEndTime = $data['endTime']; 
        }
    }
    

    

?>


    
        <div id = 'scheduleForm' class='scheduleInterview'>
            <form action='./store.php'>
                <input type='hidden' name='form' value='insert'>
                <div class='outerContainer'>

                    <div class='outerContainerButton'>
                        <div><b>Schedule an Interview</b></div>
                    </div>
                    <br>
                    <div class='innerContainer'>
                        <div>Interviewer's Name :</div>
                        <input type='text' name = 'interviewerName' value = "<?=$interviewerName?>"required>
                    </div>

                    <div class='innerContainer'>
                        <div>Interviewer's Email :</div>
                        <input type='email' name = 'interviewerEmail' value = "<?=$interviewerEmail?>" required>
                    </div>

                    <div class='innerContainer'>
                        <div>Interviewees: (Use CTRL+click to select multiple)</div>
                        <select name = 'interviewees[]' multiple>
                            <?php
                                $select = "SELECT * FROM `interviewees` ";
                                $row = $conn->query($select);
                                while($data = $row->fetch_assoc()){
                                    if($data['intervieweeName']==$interviewees){
                            ?>
                                <option selected value='<?=$data['intervieweeName']?>'><?=$data['intervieweeName']?></option>
                                <?php }else{?>
                                <option value='<?=$data['intervieweeName']?>'><?=$data['intervieweeName']?></option>
                            <?php }}?>
                        </select>
                    </div>

                    <div class='innerContainer'>
                        <div>Interview Date:</div>
                        <input type='date' min="<?php echo date("Y-m-d"); ?>" name = 'date' value = "<?=$interviewDate?>" required>
                    </div>

                    <div class='innerContainer'>
                        <div>Start Time:</div>
                        <input type='time' name = 'startTime' value = "<?=$interviewStartTime?>" required>
                    </div>

                    <div class='innerContainer'>
                        <div>End Time:</div>
                        <input type='time' name = 'endTime' value = "<?=$interviewEndTime?>" required>
                    </div>

                    <div class='outerContainerButton'>
                        <div><button type='submit'>Submit</button></div>
                    </div>
                
                    <?php if($_SESSION['id']!=0){ ?>
                    <div class='outerContainerButton'>
                        <div><button type='button' onclick="cancelChanges();">Cancel Edit</button></div>
                    </div>   
                    <?php } ?>              
                    
                </div>
            </form>
        </div>
    


<!--  -----------------------------------------   -->

<!--  -----------------Scheduled Interview -------------------------------- -->
    

        <h1 id='lists'>Scheduled Interviews</h1>

       <div >
       <table id="customers">
            <tr>
                <th>Interviewer</th>
                <th>Interviewer's Email</th>
                <th>Interviewee</th>
                <th>Date</th>
                <th>Start time</th>
                <th>End time</th>
            </tr>
        
        <?php
                $select = "SELECT * FROM `scheduledInterviews` ";
                $row = $conn->query($select);
                while($data = $row->fetch_assoc()){
                    if($data['status']==1){
        ?>
        
            <tr>
                <td><?=$data['interviewerName']?></td>
                <td><?=$data['interviewerEmail']?></td>
                <td><?=$data['intervieweeName']?></td>
                <td><?=$data['date']?></td>
                <td><?=$data['startTime']?></td>
                <td><?=$data['endTime']?></td>
            </tr>
        <?php }}?>
        </table>
       </div>







<!--   ----------------------------------Admin Only ------------------>

<!--  -----------------Scheduled Interview -------------------------------- -->
    

        <h1 id='admin'>Admin</h1>

        <div>
        <table id="customers">
            <tr>
                <th>Interviewer</th>
                <th>Interviewer's Email</th>
                <th>Interviewee</th>
                <th>Date</th>
                <th>Start time</th>
                <th>End time</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        
        <?php
                $select = "SELECT * FROM `scheduledInterviews` ";
                $row = $conn->query($select);
                while($data = $row->fetch_assoc()){
                    if($data['status']==1){
        ?>
        
            <tr>
                <td><?=$data['interviewerName']?></td>
                <td><?=$data['interviewerEmail']?></td>
                <td><?=$data['intervieweeName']?></td>
                <td><?=$data['date']?></td>
                <td><?=$data['startTime']?></td>
                <td><?=$data['endTime']?></td>
                <td>
                    <form action='./store.php'>
                        <button type='submit'>Edit</button>
                        <input type='hidden' name = 'id' value='<?=$data['id']?>'>
                        <input type='hidden' name = 'form' value='edit'>
                    </form>
                </td>
                <td>
                    <form action='./store.php'>
                        <button type='submit'>Delete</button>
                        <input type='hidden' name ='id' value='<?=$data['id']?>'>
                        <input type='hidden' name = 'form' value='delete'>
                    </form>
                </td>
            </tr>
        <?php }}?>
        </table>
        </div>





<script>
    function cancelChanges()
    {
        window.location = './store.php?form=cancelEdit';
    }
</script>

</body>
</html>
