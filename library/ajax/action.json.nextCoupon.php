<?php require_once("_ajax_boot.php");

// Ajax handler for action SEND MAIL

	$data = array('status'=>'failed', 'message'=>'Error', 'errorID'=>'0', 'newCid'=>0);

	// GET POST DATA
	
	$_email 	= $_POST['email'];
	$_cid 		= $_POST['cid'];
	$_com_id 	= $_POST['com_id'];
	
	$query = "SELECT * FROM [pre]settings WHERE `code`='contact_email' LIMIT 1";
	$tmp = $db->exec_query($query,1,0);
	$fromEmail = $tmp["value"];
	
	if(!$_com_id)
	{
		header('HTTP/1.0 403 Forbidden');
		exit();
	}
	
	// VALIDATE POST DATA
	if(!filter_var($_email,FILTER_VALIDATE_EMAIL))
	{
		$data['errorID'] = 1;
	}else{
	
	$coupon = $companyObj->getRandomCoupon($_com_id,$_cid);
		
			if($coupon)
			{
				$data['status'] = 'success';
				$data['newCid'] = $coupon['id'];
			}else{
				$data['status'] = 'warning';
				$data['message'] = 'Wrong coupon ID';
				}
	}
	
	if($data['status']=='success')
	{
				$companyObj->updateCouponQuantityById( ($coupon['quantity']-1), $coupon['id'] );
			
				$letterMessage = "<p style='direction:rtl; text-align:right;'>ההטבה שלכם הגיעה, מהרו לממש!</p>";
				$letterMessage .= "<p style='direction:rtl; text-align:right;'>מייל זה נועד לשליחה בלבד, נא לא לשלוח תגובות. תודה</p>";
				$letterSubject = "ההטבה שלכם הגיעה, מהרו לממש!";
				
				$attachments = "";
				$att_path= "";
				
				$att_path = "../uploads/coupons/";
				
				$send_letter_vs_att = $exLib->sendLetterVsAttachment(array(
				 		'to'=>$_email,
				 		'from'=>$fromEmail,
				 		'subject'=>$letterSubject,
				 		'message'=>$letterMessage,
				 		'attachment'=>$coupon['image'],
				 		'att_path'=>$att_path
				 	));
			
			$data['message'] = 'Success';
	}


$db->db_destroy();

echo json_encode($data);