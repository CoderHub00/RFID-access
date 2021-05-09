<!DOCTYPE html>
<html>

<?php
include ('Aconfig.php');
         
         $Device_ID=$_POST['C_ID'];   // unique to determine the class location
		 $UID=$_POST['temp1'];  //uid  
		 
		 //$Device_ID = '1';
		 //$UID = '136e7c1f';
		 //$DATE = '20190213';
		 $Day = 'Monday';
		 
		 $CurrentTime = ('14:50:00');
		  
//**********************************************************************************************
		  //Get current date and time
           date_default_timezone_set('Asia/Kuala_Lumpur');
           $DATE = date("Y-m-d"); //date
           $t = date("H:i:s"); //time
		   $day = date("l"); //day
//**********************************************************************************************
		    //before class start
			//attendance will be taken before 10 minutes class starts
			$MinTimeArrive = new DateTime('0:15:00');
//**********************************************************************************************   
		    //after class start
			//attendance will be taken after 30 minutes class starts
			//$MaxTimeArrive = new DateTime('0:30:00 ');
			$MaxTimeArrive = new DateTime('00:30:00');
//**********************************************************************************************   
			$Tleft = mktime(02,30,00);
			$Timeleft = date("H:i:sa", $Tleft);
//**********************************************************************************************



/*******************************************************ALMOST DONE************************************************************************/

		//check whether he has register for that subject			
         $data3 = "SELECT *
				   FROM registeredsubject WHERE Arduino_Location = '$Device_ID' AND rfid_uid = '$UID' AND Subject_Day = '$Day'";
	               //AND Subject_StartTime - '00:15:00' <= '$CurrentTime' AND Subject_EndTime - '00:30:00' > '$CurrentTime'
		 $resultdata3 = mysqli_query($link,$data3);
		 //$rowdata3 = mysqli_fetch_array($resultdata3);
		 
		 
		 
		 if(!$resultdata3)
		 {
			 die('invalid query');
		 }
		 else{
			  
			 while($rowdata3=mysqli_fetch_array($resultdata3))
			 {
			           
						$u = $rowdata3['Subject_StartTime'];
						
		
							 //$startTime = new DateTime('22:00:00');
							 $startTime = new DateTime($u);
							 $Time = new DateTime('14:50:00');   //for demonstration purpose, a static time was declared as current time
							 $endTime = new DateTime('00:30:00');
							 $duration = $startTime->diff($Time);
							 $r = $duration->format("%H:%I:%S");
							 
			 
			                     //attendance will be taken 10minutes before and after class start
							     if($r <= '00:10:00')
								 {
								  echo $u;
			                     
							      $data4 = "SELECT * FROM registeredsubject 
											WHERE Arduino_Location = '$Device_ID' AND rfid_uid = '$UID' AND Subject_Day = '$Day' 
											AND Subject_StartTime = '$u'";
		                          $resultdata4 = mysqli_query($link,$data4);
								  $rowdata4 = mysqli_fetch_array($resultdata4);
								  
								  $data5 = "SELECT * FROM attendance 
								            WHERE Arduino_Location = '$Device_ID' AND rfid_uid = '$UID' 
											AND Subject_Day = '$Day' AND Subject_Date = '$DATE' AND Subject_StartTime = '$u'";
	                              $resultdata5 = mysqli_query($link,$data5);
 	                              
								       
									     if(mysqli_num_rows($resultdata4) > 0)
											 {		
												  
											    $rowdata5 = mysqli_fetch_array($resultdata5);
													 
													//check whether the timein AND the date is empty or not
                                                    //if not empty update the timeout of the student													
												 if(!empty($rowdata5['Subject_StartTime']) && !empty($rowdata5['Subject_Date']))
												 {
									                  echo 'print';
													  
													      $Arduino_Location  = $rowdata4['Arduino_Location'];
														  $Subject_Day       = $rowdata4['Subject_Day'];
														  $Subject_StartTime = $rowdata4['Subject_StartTime'];
														  $rfid_uid          = $rowdata4['rfid_uid'];
													 //present(timeout) and mark as present
													 $query2="UPDATE attendance SET attendance = 'Present', Student_TimeOut = '$CurrentTime'
															  WHERE Arduino_Location = $Arduino_Location AND rfid_uid = '$rfid_uid' 
											                  AND Subject_Day = '$Subject_Day' AND Subject_Date = '$DATE' AND Subject_StartTime = '$Subject_StartTime'";
												     
													 $insertResult=mysqli_query($link, $query2);
												 }
												 else{
													 //present(timein) and current date
													      $Arduino_Location  = $rowdata4['Arduino_Location'];
														  $Subject_ID        = $rowdata4['Subject_ID'];
														  $Subject_Code      = $rowdata4['Subject_Code'];
														  $Subject_Name      = $rowdata4['Subject_Name'];
														  $Subject_Section   = $rowdata4['Subject_Section'];
														  $Subject_Day       = $rowdata4['Subject_Day'];
														  $Subject_StartTime = $rowdata4['Subject_StartTime'];
														  $Subject_EndTime   = $rowdata4['Subject_EndTime'];
														  $Student_ID        = $rowdata4['Student_ID'];
														  $Student_Name      = $rowdata4['Student_Name'];
														  $rfid_uid          = $rowdata4['rfid_uid'];
								
														 echo 'hi';
														 echo $Arduino_Location;
														 echo $Subject_Code;
														 echo $Subject_Name;
														  
										
														$query3="INSERT INTO attendance(Arduino_Location, Subject_ID, Subject_Code, Subject_Name, Subject_Section,
       												             Subject_Day, Subject_Date, Subject_StartTime, Subject_EndTime, Student_ID, Student_Name, rfid_uid, Student_TimeIn) 
																 VALUES ($Arduino_Location, '$Subject_ID', '$Subject_Code', '$Subject_Name', '$Subject_Section',
                             									'$Subject_Day', '$DATE', '$Subject_StartTime', '$Subject_EndTime', '$Student_ID', '$Student_Name', '$rfid_uid', 
																'$CurrentTime')";
														  /*$query3="INSERT INTO attendance(Arduino_Location, Subject_Code, Subject_Name) VALUES ($Arduino_Location, '$Subject_Code', '$Subject_Name')";*/
                             									 
														$insertResult1=mysqli_query($link, $query3);
												 
												
												 }
												 
											 }else
											 {
												 echo 'you did not register for this subject';
											 }
													 
									 
									  
								 }else
								 {
									 echo 'you came late, attendance not taken';
								 }
						
			  BREAK;
		 }
		
		 }
			 
							 
						
		
		 
			 //check whether his time in attendance was taken or not
			 /*echo 'hai';
			 $data4 = "SELECT * 
      			       FROM attendance WHERE Arduino_Location = '$Device_ID' AND rfid_uid = '$UID' AND Subject_Day = '$Day' AND Subject_Date = '$DATE'
	                   AND Subject_StartTime - '00:15:00' <= '$CurrentTime' AND Subject_EndTime - '00:30:00' > '$CurrentTime'";
			 $resultdata4 = mysqli_query($link,$data4);
			 $rowdata4 = mysqli_fetch_array($resultdata4);
			          
					   if(!$rowdata4)
		                 {
							 //register student time in
						   echo 'you hvnt register'; 
							          
					        $data5 = "INSERT INTO attendance rfid_uid VALUES ('$UID')";
						    $resultdata5 = mysqli_query($link,$data5);
						 
						 }elseif($rowdata4)
						 {
							 echo 'bye';
							//student time out 
				 	         $data7 = "UPDATE attandence SET Subject_EndTime = '$CurrentTime' 
									   WHERE Arduino_Location = '$Device_ID' AND rfid_uid = '$UID' AND Subject_Day = '$Day' AND Subject_Date = '$DATE' AND Subject_StartTime = '$CurrentTime'
	                                   AND Subject_StartTime - '00:15:00' <= '$CurrentTime' AND Subject_EndTime - '00:30:00' > '$CurrentTime'";
							 $resultdata7 = mysqli_query($link,$data7);
						 }

		 }*/
	     
?>



</html>