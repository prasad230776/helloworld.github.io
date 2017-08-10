<?php
//
// Recommended way to include parent theme styles.
//  (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
// 

function find_grade($marks_per){
	$grade='';
	
switch ($marks_per) {
    case $marks_per >= 90:
       $grade='A+(Excellent)';
        break;
	case $marks_per >= 80 && $marks_per < 90:
       $grade='A(Very Good)';
        break;
	case $marks_per >= 70 && $marks_per < 80:
       $grade='B(Very)';
        break;
	case $marks_per >= 60 && $marks_per < 70:
       $grade='C(Very Fair)';
        break;
		
	case $marks_per >= 50 && $marks_per < 60:
       $grade='D(Fair)';
        break;
	case $marks_per < 50:
       $grade='E(Needs Improvement)';
        break;
    
case default:
       $grade='F(Pl.see that the student is regular)';
        break;
    
  }
		
	return $grade;
}


add_shortcode('sendsms_cw_tw_marks','fun_sendsms_cw_tw_marks');
function fun_sendsms_cw_tw_marks($atts,$content=null)
{
	$a=shortcode_atts(array('sendsms'=>'No','regdnum'=>'-1','entryid1'=>'','test'=>'','total_marks'=>'','percentage'=>''),$atts);
	$redgno_fieldid=294;

	global $wpdb;
	$regdnum=$a['regdnum'];
	$test=$a['test'];
	$total_marks=$a['total_marks'];
	$percentage=$a['percentage'];
	$sendsms=$a['sendsms'];
	

//echo "ok into sms function:sms value".$a['sendsms'].":regdnum:".$a['regdnum'].":test".$a['test'].":total_marks".$a['total_marks'].":percentage".$a['percentage']; 

	if($sendsms=='Yes'){
				$username="littleflowerhighschool";
				$password ="littleflowerhighscho";
				$number="9573811540";
				$sender="LFHSTS";
				$regd_fieldid=256;
				$regd_meta_value=$regdnum;
				$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."') group by m.item_id having count(*)=1 ";
				
				$itemid=$wpdb->get_col($qry);
				$total_numbers=count($itemid);
				if($total_numbers!=0){
				{
					$result= FrmProEntriesController::show_entry_shortcode(array('id' => $itemid[0], 'plain_text' => 1,'format'=>'array'));
					$stclass=$result['y1iew'];
					$stname=$result['ovwjo'];
					$father=$result['rm4fc'];
					$phone=$result['el997'];
					$gender=$result['okb06'];
					$grade=find_grade($percentage);
					$sms_msg='Student '.$stname.' studying class: '.$stclass.' got total marks:'.$total_marks.'('.$percentage.' percent) in Test:'.$test.'.  '.$stname.' grade is'. $grade;
					$sms_msg="Dear parent, greetings from LFHS. ".$sms_msg;
					//echo $sms_msg;
					$url="login.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($phone)."&sender=".urlencode($sender)."&message=".urlencode($sms_msg)."&type=".urlencode('3'); 
					$ch = curl_init($url);

					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					$curl_scraped_page = curl_exec($ch);

					curl_close($ch); 
				}
	}
	
}
return $content;
}



add_filter('frm_validate_field_entry', 'redirect_att_marks_normalview', 10, 3);
function redirect_att_marks_normalview($errors, $posted_field, $posted_value){
  if ( $posted_field->id == 790 ) { //change 25 to the ID of the field to change
    $regd_num=$_POST['item_meta'][$posted_field->id];
    $frmpro_settings = FrmProAppHelper::get_settings();
$from_date = FrmProAppHelper::convert_date($_POST['item_meta'][791], $frmpro_settings->date_format, 'Y-m-d');
	//$from_date=$_POST['item_meta'][389];
	//$to_date=$_POST['item_meta'][390];
$to_date= FrmProAppHelper::convert_date($_POST['item_meta'][792], $frmpro_settings->date_format, 'Y-m-d');	
	$regd_fieldid=256;
	$regd_meta_value=$regd_num;
	$student_details_formid=27;
	//$password=$_POST['item_meta'][764];
	global $wpdb;
	
/* $itemid = $wpdb->get_col($wpdb->prepare("SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE m.field_id=%d and m.meta_value=%s and i.form_id=%d", $regd_fieldid, $regd_meta_value, $student_details_formid));*/
$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."')  group by m.item_id having count(*)=1 ";
$itemid=$wpdb->get_col($qry);
if(count($itemid)==0){
$stclass=-1;
$errors[790]="Pl. Provide Valid Number";

}
else
{
  $stclass= FrmProEntriesController::get_field_value_shortcode(array('field_id' =>383 , 'entry' => $itemid[0]));
   header("Location:/sw-full-report/?attclass=".$stclass."&regdnum=".$regd_num."&fromdate=".$from_date."&todate=".$to_date);
  }
   

 
 
  }
  
  return $errors;
}


add_filter('frm_validate_field_entry', 'redirect_att_marks_view_mobile', 10, 3);
function redirect_att_marks_view_mobile($errors, $posted_field, $posted_value){
  if ( $posted_field->id == 783 ) { //change 25 to the ID of the field to change
    $regd_num=$_POST['item_meta'][$posted_field->id];
    $frmpro_settings = FrmProAppHelper::get_settings();
$from_date = FrmProAppHelper::convert_date($_POST['item_meta'][784], $frmpro_settings->date_format, 'Y-m-d');
	//$from_date=$_POST['item_meta'][389];
	//$to_date=$_POST['item_meta'][390];
$to_date= FrmProAppHelper::convert_date($_POST['item_meta'][785], $frmpro_settings->date_format, 'Y-m-d');	
	$regd_fieldid=256;
	$regd_meta_value=$regd_num;
	$student_details_formid=27;
	//$password=$_POST['item_meta'][764];
	global $wpdb;
	
/* $itemid = $wpdb->get_col($wpdb->prepare("SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE m.field_id=%d and m.meta_value=%s and i.form_id=%d", $regd_fieldid, $regd_meta_value, $student_details_formid));*/
$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."')  group by m.item_id having count(*)=1 ";
$itemid=$wpdb->get_col($qry);
if(count($itemid)==0){
$stclass=-1;
$errors[783]="Pl. Provide Valid Number";

}
else
{
  $stclass= FrmProEntriesController::get_field_value_shortcode(array('field_id' =>383 , 'entry' => $itemid[0]));
   header("Location:/sw-full-report-mobile/?attclass=".$stclass."&regdnum=".$regd_num."&fromdate=".$from_date."&todate=".$to_date);
  }
   

 
 
  }
  
  return $errors;
}
  
	
	
	
	add_filter('frm_validate_field_entry', 'redirect_ws_hw_mobile', 10, 3);
function redirect_ws_hw_mobile($errors, $posted_field, $posted_value){
  if ( $posted_field->id == 771 ) { //change 25 to the ID of the field to change
    $regd_num=$_POST['item_meta'][$posted_field->id];
    $frmpro_settings = FrmProAppHelper::get_settings();

	$regd_fieldid=256;
	$regd_meta_value=$regd_num;
	$student_details_formid=27;
	$password=$_POST['item_meta'][772];
	global $wpdb;

$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."') or( m.field_id=333 and m.meta_value='".$password."') group by m.item_id having count(*)=2 ";
$itemid=$wpdb->get_col($qry);
if(count($itemid)==0){
$stclass=-1;
$errors[771]="Invalid credentials";
$errors[772]="Invalid credentials";
}
else
{
	//echo "valid student";
  $stclass= FrmProEntriesController::get_field_value_shortcode(array('field_id' =>383 , 'entry' => $itemid[0]));
   header("Location:/student-ws-hw-download-mobile/?attclass=".$stclass);
  }
   

 
 
  }
  
  return $errors;
}


add_filter('frm_validate_field_entry', 'redirect_marks_view_mobile', 10, 3);
function redirect_marks_view_mobile($errors, $posted_field, $posted_value){
  if ( $posted_field->id == 763 ) { //change 25 to the ID of the field to change
    $regd_num=$_POST['item_meta'][$posted_field->id];
    $frmpro_settings = FrmProAppHelper::get_settings();
$from_date = FrmProAppHelper::convert_date($_POST['item_meta'][768], $frmpro_settings->date_format, 'Y-m-d');
	//$from_date=$_POST['item_meta'][389];
	//$to_date=$_POST['item_meta'][390];
$to_date= FrmProAppHelper::convert_date($_POST['item_meta'][769], $frmpro_settings->date_format, 'Y-m-d');	
	$regd_fieldid=256;
	$regd_meta_value=$regd_num;
	$student_details_formid=27;
	$password=$_POST['item_meta'][764];
	global $wpdb;
	
/* $itemid = $wpdb->get_col($wpdb->prepare("SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE m.field_id=%d and m.meta_value=%s and i.form_id=%d", $regd_fieldid, $regd_meta_value, $student_details_formid));*/
$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."') or( m.field_id=333 and m.meta_value='".$password."') group by m.item_id having count(*)=2 ";
$itemid=$wpdb->get_col($qry);
if(count($itemid)==0){
$stclass=-1;
$errors[763]="Invalid credentials";
$errors[764]="Invalid credentials";
}
else
{
  $stclass= FrmProEntriesController::get_field_value_shortcode(array('field_id' =>383 , 'entry' => $itemid[0]));
   header("Location:/student-academic-report-mobile/?attclass=".$stclass."&regdnum=".$regd_num."&fromdate=".$from_date."&todate=".$to_date);
  }
   

 
 
  }
  
  return $errors;
} 

add_filter('frm_validate_field_entry', 'two_fields_unique_att_form_mobile', 10, 2);
function two_fields_unique_att_form_mobile( $errors, $posted_field ) {
  $first_field_id = 704; // change 237to the id of the class
  global $wpdb;

  if ( $posted_field->id == $first_field_id ) {
  $class_meta_value2=FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][704] ) );
  $class_meta_value=$_POST['item_meta'][704];
  $second_field_id = 705; // change 239to the id of the attendance date
 $frmpro_settings = FrmProAppHelper::get_settings();
  $attdate = FrmProAppHelper::convert_date($_POST['item_meta'][ $second_field_id ], $frmpro_settings->date_format, 'Y-m-d');
  
  $qry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 71 )and (m.field_id=704  and  m.meta_value=". $_POST['item_meta'][704] .")or  (m.field_id=705 and  m.meta_value='". $attdate ."') group by m.item_id having count(*)=2";

//echo $qry;
$entries =$wpdb->get_col($qry);
$cnt1=count($entries);

  $qry2="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 32 )and (m.field_id=291  and  m.meta_value=". $_POST['item_meta'][704] .")or  (m.field_id=292 and  m.meta_value='". $attdate ."') group by m.item_id having count(*)=2";

//echo $qry;
$entries =$wpdb->get_col($qry2);
$cnt2=count($entries);
   
	if ( $cnt1>0||$cnt2>0 ) {
		
		$errors[ 'field'. $first_field_id ] = '  ';
		$errors[ 'field'. $second_field_id ] = '  ';
	
		
		header("Location: /add-student-attendance-mobile/?attclassid=". $_POST['item_meta'][704]."&attdate=".$attdate ."&attclass=".$class_meta_value2);
	}
	else{
	//add student attendance in that class
	 global $wpdb;
	 
 /*$student_details_formid=28;
 $class_fieldid=267; //class field in student fee details
 $redgno_fieldid=268; //regd no in studentdetails
 $attendance_formid=33;*/
 $class_meta_value=FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][704] ) );

  $student_details_formid=27;
 $class_fieldid=383; //class field in student fee details
 $redgno_fieldid=256; //regd no in studentdetails
 $attendance_formid=33;
 
 
 $item_ids = $wpdb->get_col($wpdb->prepare("SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE m.field_id=%d and m.meta_value=%s and i.form_id=%d", $class_fieldid, $class_meta_value, $student_details_formid));
 

foreach($item_ids as $item_id)
 {


 $st_name= FrmProEntriesController::get_field_value_shortcode(array('field_id' => 257, 'entry' => $item_id));
 $regd_num=FrmProEntriesController::get_field_value_shortcode(array('field_id' => 256, 'entry' => $item_id));
 $phone_num=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>247 , 'entry' => $item_id));
 $father=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>242 , 'entry' => $item_id));


	FrmEntry::create(array(
               'form_id' => $attendance_formid,
                               
               'item_meta' => array(
                 
				 293 => $class_meta_value,
				 294 => $regd_num,
				 296 => $attdate,
				 302=>'Present',
				 299=>$st_name,
				 305=> $phone_num,
				 312=>$father,
				 
               ),
             ));
	
 }
	}
	
  }
  return $errors;
}


add_action('add_horizontal_scroll','scroll_function');
function scroll_function(){
	echo do_shortcode( '[horizontal-scrolling group="GROUP2" scrollamount="6" ]' );
}

function register_custom_menus() {
register_nav_menus(
array(
'custom-menu-cust1' => __( 'Menu Mobile' ),
'custom-menu-cust2' => __( 'Menu SchoolAdmin' ),
'custom-menu-cust3' => __( 'Menu School Admin Mobile' )
)
);
}
add_action( 'init', 'register_custom_menus' );

add_filter('frm_validate_field_entry', 'copy_class_dynamic_field', 10, 3);
function copy_class_dynamic_field( $errors, $posted_field, $posted_value ) {
  if ( $posted_field->id == 634 ) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][344] ) );
  }
  return $errors;
}


add_filter('frm_validate_field_entry', 'send_SMS', 10, 3);
function send_SMS($errors, $posted_field, $posted_value){
	if ( $posted_field->id == 636 ) { //change 25 to the ID of the field to change
		$whomtosend=$_POST['item_meta'][$posted_field->id];
		global $wpdb;
		$total_numbers=0;
		$my_error=false;
		$qry='';
		switch ($whomtosend)
		{
			case 'All Students':
				$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27  group by m.item_id " ; 
				
				$itemid=$wpdb->get_col($qry);
				$total_numbers=count($itemid);
				//error_log("total nums: ".$total_numbers);
				if($total_numbers==0){
			
					$errors[636]="No students yet";
					$my_error=true;
				}
			break;
			
			case 'A Class':
			
				$class_filedid=383;
				$class_meta_value=FrmProEntriesController::get_field_value_shortcode( array( 'field_id' =>155 , 'entry' => $_POST['item_meta'][638] ) );
				$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$class_filedid." and m.meta_value='".$class_meta_value."') group by m.item_id having count(*)=1 ";
				$itemid=$wpdb->get_col($qry);
				$total_numbers=count($itemid);
				//error_log("total nums in class: ".$total_numbers);
				if($total_numbers==0){
			
					$errors[638]="No Such Class.pl. select existing class" ;
					$my_error=true;
				}
			break;
			
						
			case 'A Student':
				$regd_fieldid=256;
				$regd_meta_value=$_POST['item_meta'][639];
				$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."') group by m.item_id having count(*)=1 ";
				
				$itemid=$wpdb->get_col($qry);
				$total_numbers=count($itemid);
				if($total_numbers==0){
			
					$errors[639]="Wrong regd number. Pl. enter correct number and try again.";
					$my_error=true;
				}
			break;
			
		}
			
				$username="littleflowerhighschool";
				$password ="littleflowerhighscho";
				$number="9573811540";
				$sender="LFHSTS";
				$sms_msg=$_POST['item_meta'][640];
				if(!isset($sms_msg)) { $errors[640]="pl. write msg"; return $errors;}
			
			if(!$my_error)
			for($i=0; $i<$total_numbers ;$i++)
			{
				$result= FrmProEntriesController::show_entry_shortcode(array('id' => $itemid[$i], 'plain_text' => 1,'format'=>'array'));
				$stclass=$result['y1iew'];
				$stname=$result['ovwjo'];
				$father=$result['rm4fc'];
				$phone=$result['el997'];
				$gender=$result['okb06'];
				//$sms_msg="Dear parent ".$father." garu, greetings from LFHS. ".$sms_msg;
				
				
				
				$url="login.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($phone)."&sender=".urlencode($sender)."&message=".urlencode($sms_msg)."&type=".urlencode('3'); 
				$ch = curl_init($url);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$curl_scraped_page = curl_exec($ch);

				curl_close($ch); 
				
				
			}
	 
	}
	return $errors;
}


add_action('init','custom_login');

function custom_login(){
 global $pagenow;
 if( 'wp-login.php' == $pagenow ) {
  wp_redirect('http://yoursite.com/');
  exit();
 }
}

function get_user_role() {
    global $current_user;

    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);

    return $user_role;
}

add_action( 'init', 'register_more_stylesheets' ); // should I use wp_print_styles hook instead?
function register_more_stylesheets() {
    wp_register_style( 'admin_demo_stylesheet', '/wp-content/themes/Divi/css/admin_demo_style.css' );
}

add_action( 'wp_enqueue_scripts', 'load_admin_demo_style' );
function load_admin_demo_style() {
   $stylesheeturi = get_stylesheet_directory_uri() . '/admin_demo_style.css';
$user_role=get_user_role();
 if($user_role=='admin_demo'){
 //wp_enqueue_style('admin_demo_stylesheet', $stylesheeturi);
wp_enqueue_style('/wp-content/themes/Divi/css/admin_demo_style.css');
  }
}


add_filter('frm_validate_field_entry', 'two_fields_unique_general_marks', 10, 2);
function two_fields_unique_general_marks( $errors, $posted_field ) {
  $first_field_id = 415; // change 125 to the id of the first field
  $second_field_id = 416; // change 126 to the id of the second field
  if ( $posted_field->id == $first_field_id ) {
    $entry_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $values_used = FrmDb::get_col( 'frm_item_metas',
		array( 'item_id !' => $entry_id,
			array( 'or' => 1,
				array( 'field_id' => $first_field_id, 'meta_value' => $_POST['item_meta'][ $first_field_id ] ),
				array( 'field_id' => $second_field_id, 'meta_value' => $_POST['item_meta'][ $second_field_id ] ),
			)
		), 'item_id', array( 'group_by' => 'item_id', 'having' => 'COUNT(*) > 1' )
	);
	if ( ! empty( $values_used ) ) {
		$errors[ 'field'. $first_field_id ] = 'You have already selected that option';
		$errors[ 'field'. $second_field_id ] = 'You have already selected that option';
	}
  }
  return $errors;
}

add_action('frm_after_update_entry', 'link_fields', 10, 2);
function link_fields($entry_id, $form_id){
     if($form_id == 27){//Change 113 to the ID of the first form
         global $wpdb, $frmdb;
         $phone_number = $_POST['item_meta'][247]; //change 25 to the ID of the field in your first form
         $regd_number = $_POST['item_meta'][256];
	 $entry_ids = $wpdb->get_col("Select item_id from $frmdb->entry_metas where field_id=268 and meta_value='". $regd_number."'");//Change 112 to the ID of the second form
         foreach ($entry_ids as $e)
             $wpdb->update($frmdb->entry_metas, array('meta_value' => $phone_number), array('item_id' => $e, 'field_id' => '303'));//Change 6422 to the ID of the field to be updated automatically in your second form
  }
}

add_action('frm_after_update_field', 'frm_trigger_entry_update');
function frm_trigger_entry_update($atts){
  $entry = FrmEntry::getOne($atts['entry_id']);
  do_action('frm_after_update_entry', $entry->id, $entry->form_id);
}

add_action('frm_after_update_entry', 'send_sms_abt_absent', 10, 2);
function send_sms_abt_absent($entry_id, $form_id){
if ( $form_id ==33 ) {
$attstatus= FrmProEntriesController::get_field_value_shortcode(array('field_id' => 302, 'entry' => $entry_id));

if($attstatus=="Absent"){
$username="littleflowerhighschool";
$password ="littleflowerhighscho";
$number="9573811540";
 $sender="LFHSTS";
$att_date=FrmProEntriesController::get_field_value_shortcode(array('field_id' => 296, 'entry' => $entry_id));
$mydate=date('l jS \of F Y');

$regd_fieldid=256;
$regd_meta_value= FrmProEntriesController::get_field_value_shortcode(array('field_id' => 294, 'entry' => $entry_id));
global $wpdb;
$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."')";
$itemid=$wpdb->get_col($qry);
$result= FrmProEntriesController::show_entry_shortcode(array('id' => $itemid[0], 'plain_text' => 1,'format'=>'array'));
$stclass=$result['y1iew'];
$stname=$result['ovwjo'];
$father=$result['rm4fc'];
$phone=$result['el997'];
$gender=$result['okb06'];
if($gender=="Male")
$child='son';
if($gender=="Female")
$child='daughter';
$message='';
if($father!='' || $father!=NULL)
$message="Dear parent,".$father. " garu,";
else
$message="Dear parent, ";
$message.= "greetings from LFHS. This is to bring to your notice that student ".$stname ."  is absent to the school today,that is on ".$mydate;
//$message="absent";

//$phone=9573811540;
$url="login.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($phone)."&sender=".urlencode($sender)."&message=".urlencode($message)."&type=".urlencode('3'); 
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$curl_scraped_page = curl_exec($ch);

curl_close($ch); 
}   
}
}

add_filter('frm_validate_field_entry', 'redirect_marks_view_p', 10, 3);
function redirect_marks_view_p($errors, $posted_field, $posted_value){
  if ( $posted_field->id == 447 ) { //change 25 to the ID of the field to change
    $regd_num=$_POST['item_meta'][$posted_field->id];
    $frmpro_settings = FrmProAppHelper::get_settings();
$from_date = FrmProAppHelper::convert_date($_POST['item_meta'][452], $frmpro_settings->date_format, 'Y-m-d');
	//$from_date=$_POST['item_meta'][389];
	//$to_date=$_POST['item_meta'][390];
$to_date= FrmProAppHelper::convert_date($_POST['item_meta'][453], $frmpro_settings->date_format, 'Y-m-d');	
	$regd_fieldid=256;
	$regd_meta_value=$regd_num;
	$student_details_formid=27;
	$password=$_POST['item_meta'][448];
	global $wpdb;
	
/* $itemid = $wpdb->get_col($wpdb->prepare("SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE m.field_id=%d and m.meta_value=%s and i.form_id=%d", $regd_fieldid, $regd_meta_value, $student_details_formid));*/
$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."') or( m.field_id=333 and m.meta_value='".$password."') group by m.item_id having count(*)=2 ";
$itemid=$wpdb->get_col($qry);
if(count($itemid)==0){
$stclass=-1;
$errors[447]="Invalid credentials";
$errors[448]="Invalid credentials";
}
else
{
  $stclass= FrmProEntriesController::get_field_value_shortcode(array('field_id' =>383 , 'entry' => $itemid[0]));
   header("Location:/student-academic-report-p/?attclass=".$stclass."&regdnum=".$regd_num."&fromdate=".$from_date."&todate=".$to_date);
  }
   

 
 
  }
  
  return $errors;
}

/* function to check the valid reg number to display workshets and home works */

add_filter('frm_validate_field_entry', 'redirect_ws_hw', 10, 3);
function redirect_ws_hw($errors, $posted_field, $posted_value){
  if ( $posted_field->id == 455 ) { //change 25 to the ID of the field to change
    $regd_num=$_POST['item_meta'][$posted_field->id];
    $frmpro_settings = FrmProAppHelper::get_settings();

	$regd_fieldid=256;
	$regd_meta_value=$regd_num;
	$student_details_formid=27;
	$password=$_POST['item_meta'][456];
	global $wpdb;

$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."') or( m.field_id=333 and m.meta_value='".$password."') group by m.item_id having count(*)=2 ";
$itemid=$wpdb->get_col($qry);
if(count($itemid)==0){
$stclass=-1;
$errors[455]="Invalid credentials";
$errors[456]="Invalid credentials";
}
else
{
	//echo "valid student";
  $stclass= FrmProEntriesController::get_field_value_shortcode(array('field_id' =>383 , 'entry' => $itemid[0]));
   header("Location:/student-ws-hw-download/?attclass=".$stclass);
  }
   

 
 
  }
  
  return $errors;
}


add_filter('frm_validate_field_entry', 'redirect_sw_marks', 10, 3);
function redirect_sw_marks($errors, $posted_field, $posted_value){
  if ( $posted_field->id == 422 ) { //change 25 to the ID of the field to change
  if($_POST['item_meta'][$posted_field->id]==NULL) header("Location:/sw-marks-p/");
    $regd_num=$_POST['item_meta'][$posted_field->id];
    $frmpro_settings = FrmProAppHelper::get_settings();
$from_date = FrmProAppHelper::convert_date($_POST['item_meta'][423], $frmpro_settings->date_format, 'Y-m-d');
	
$to_date= FrmProAppHelper::convert_date($_POST['item_meta'][424], $frmpro_settings->date_format, 'Y-m-d');	
	$regd_fieldid=256;
	$regd_meta_value=$regd_num;
	$student_details_formid=27;
	//$password=$_POST['item_meta'][456];
	global $wpdb;

$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."') group by m.item_id having count(*)=1 ";
$itemid=$wpdb->get_col($qry);
if(count($itemid)==0){
$stclass=-1;
$errors[422]="Invalid Regd num. Pl. check.";
//$errors[456]="Invalid credentials";
}
else
{
	//echo "valid student";
  $stclass= FrmProEntriesController::get_field_value_shortcode(array('field_id' =>383 , 'entry' => $itemid[0]));
 // $sendsms=$_POST['item_meta'][797];
   header("Location:/sw-marks-p/?regdnum=".$regd_num."&stclass=".$stclass."&fromdate=".$from_date."&todate=".$to_date."&sendsms=".$sendsms);
  }
   

 
 
  }
  
  return $errors;
}

add_filter('frm_validate_field_entry', 'redirect_sw_marks_mobile', 10, 3);
function redirect_sw_marks_mobile($errors, $posted_field, $posted_value){
  if ( $posted_field->id == 689 ) { //change 25 to the ID of the field to change
  if($_POST['item_meta'][$posted_field->id]==NULL) header("Location:/sw-marks-report-p-mobile/");
    $regd_num=$_POST['item_meta'][$posted_field->id];
    $frmpro_settings = FrmProAppHelper::get_settings();
$from_date = FrmProAppHelper::convert_date($_POST['item_meta'][690], $frmpro_settings->date_format, 'Y-m-d');
	
$to_date= FrmProAppHelper::convert_date($_POST['item_meta'][691], $frmpro_settings->date_format, 'Y-m-d');	
	$regd_fieldid=256;
	$regd_meta_value=$regd_num;
	$student_details_formid=27;
	//$password=$_POST['item_meta'][456];
	global $wpdb;

$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."') group by m.item_id having count(*)=1 ";
$itemid=$wpdb->get_col($qry);
if(count($itemid)==0){
$stclass=-1;
$errors[689]="Invalid Regd num. Pl. check.";
//$errors[456]="Invalid credentials";
}
else
{
	//echo "valid student";
  $stclass= FrmProEntriesController::get_field_value_shortcode(array('field_id' =>383 , 'entry' => $itemid[0]));
  $sendsms=$_POST['item_meta'][798];
   header("Location:/sw-marks-report-p-mobile/?regdnum=".$regd_num."&stclass=".$stclass."&fromdate=".$from_date."&todate=".$to_date."&sendsms=".$sendsms);
  }
   

 
 
  }
  
  return $errors;
}



add_filter('frm_validate_field_entry', 'redirect_marks_view', 10, 3);
function redirect_marks_view($errors, $posted_field, $posted_value){
  if ( $posted_field->id == 395 ) { //change 25 to the ID of the field to change
    $regd_num=$_POST['item_meta'][$posted_field->id];
    $frmpro_settings = FrmProAppHelper::get_settings();
$from_date = FrmProAppHelper::convert_date($_POST['item_meta'][389], $frmpro_settings->date_format, 'Y-m-d');
	//$from_date=$_POST['item_meta'][389];
	//$to_date=$_POST['item_meta'][390];
$to_date= FrmProAppHelper::convert_date($_POST['item_meta'][390], $frmpro_settings->date_format, 'Y-m-d');	
	$regd_fieldid=256;
	$regd_meta_value=$regd_num;
	$student_details_formid=27;
	$password=$_POST['item_meta'][400];
	global $wpdb;
	

$qry="SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE i.form_id=27 and (m.field_id=".$regd_fieldid." and m.meta_value='".$regd_meta_value."') or( m.field_id=333 and m.meta_value='".$password."') group by m.item_id having count(*)=2 ";
$itemid=$wpdb->get_col($qry);
if(count($itemid)==0){
$stclass=-1;
$errors[395]="Invalid credentials";
$errors[400]="Invalid credentials";
}
else
{
  $stclass= FrmProEntriesController::get_field_value_shortcode(array('field_id' =>383 , 'entry' => $itemid[0]));
   header("Location:/sw-con-details/?attclass=".$stclass."&regdnum=".$regd_num."&fromdate=".$from_date."&todate=".$to_date);
  }
   

 
 
  }
  
  return $errors;
}
add_filter('frm_validate_field_entry', 'two_fields_unique_import_class1marks', 10, 2);
function two_fields_unique_import_class1marks( $errors, $posted_field ) {
  $first_field_id = 375; // change 125 to the id of the first field
  $second_field_id = 376; // change 126 to the id of the second field
  if ( $posted_field->id == $first_field_id ) {
    $entry_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $values_used = FrmDb::get_col( 'frm_item_metas',
		array( 'item_id !' => $entry_id,
			array( 'or' => 1,
				array( 'field_id' => $first_field_id, 'meta_value' => $_POST['item_meta'][ $first_field_id ] ),
				array( 'field_id' => $second_field_id, 'meta_value' => $_POST['item_meta'][ $second_field_id ] ),
			)
		), 'item_id', array( 'group_by' => 'item_id', 'having' => 'COUNT(*) > 1' )
	);
	if ( ! empty( $values_used ) ) {
		$errors[ 'field'. $first_field_id ] = 'You have already selected that option';
		$errors[ 'field'. $second_field_id ] = 'You have already selected that option';
	}
  }
  return $errors;
}

add_action('frm_after_create_entry', 'import_marks', 30, 2);
function import_marks( $entry_id, $form_id ) {
	if ( $form_id == 42 ) { //replace 5 with the id of the form	
	
	
	$info =sanitize_text_field( $_POST['item_meta'][377]);//id of uploding file
	
	$file=wp_get_attachment_url( $info );
	
	
$flag=true;
$stclass=FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][375] ) );
 //error_log( "selected class:".$stclass);
 //echo( "selected class:".$stclass);
switch ($stclass) {

	 case "1":
	case "2":
       		
		if (($handle = fopen($file, "r")) !== FALSE)
		{
			fgetcsv($handle);   
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
				{
					FrmEntry::create(array(
					'form_id' => 39,
                               
					'item_meta' => array(
                 
					347=>$data[0],
					
					359=>$data[1],
					350=>$data[2],
					351=>$data[3],
					
					354=>$data[4],
					356=>$data[5],
					378=>$data[6],
					458=>$data[7],
					
					355=>$data[8],
					357=>$data[9],
					358=>$data[10],
					459=>$data[11],
					360=>$data[12],
					363=>$data[13],
					475=>$data[14],
					476=>$data[15],	
					478=>$data[16],	
					
					382=>$data[4],
							 
					),
					));           
				}
			fclose($handle);
		}
        break;
   
    case "3":
    case "4":
    case "5":
    case "6":
    case "7":
      //  echo "Your favorite color is green!";
       if (($handle = fopen($file, "r")) !== FALSE)
		{
			fgetcsv($handle);  
			$i=0; 
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && isset($data[2]))
				{
				$i=$i+1;
				//error_log( "iteration num:". $i. "regdnum: ".$data[2]);
				
					FrmEntry::create(array(
					'form_id' => 56,
                               
					'item_meta' => array(
					498=>$data[0],
					499=>$data[1],
					500=>$data[2],
					501=>$data[3],
					502=>$data[4],
					503=>$data[5],
					504=>$data[6],
					505=>$data[7],
					506=>$data[8],
					507=>$data[9],
					508=>$data[10],
					509=>$data[11],
					517=>$data[12],
					512=>$data[13],
					513=>$data[14],
					510=>$data[15],
					511=>$data[16],
					515=>$data[17],
					514=>$data[4],			 
					),
					));           
				}
			fclose($handle);
		}

        break;
        
    case "8":
	case "9":
	case "10":
      //  echo "Your favorite color is green!";
       if (($handle = fopen($file, "r")) !== FALSE)
		{
			fgetcsv($handle);  
			$i=0; 
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && isset($data[2]))
				{
				$i=$i+1;
				//error_log( "iteration num:". $i. "regdnum: ".$data[2]);
				
					FrmEntry::create(array(
					'form_id' => 75,
                               
					'item_meta' => array(
					733=>$data[0],
					734=>$data[1],
					735=>$data[2],
					736=>$data[3],
					737=>$data[4],
					738=>$data[5],
					739=>$data[6],
					753=>$data[7],
					752=>$data[8],
					740=>$data[9],
					741=>$data[10],
					755=>$data[11],
					754=>$data[12],
					742=>$data[13],
					757=>$data[14],
					756=>$data[15],
					743=>$data[16],
					758=>$data[17],
					751=>$data[4],
					759=>$data[18],
					744=>$data[19],
					761=>$data[20],
					760=>$data[21],
					745=>$data[22],
					749=>$data[23],
					750=>$data[24],
					746=>$data[25],
					747=>$data[26],
					
					
					),
					));           
				}
			fclose($handle);
		}

        break;
        
	case "LKG" :
    case "UKG":
        //echo "Your favorite color is green!";
        if (($handle = fopen($file, "r")) !== FALSE)
		{
			fgetcsv($handle);   
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
				{
					FrmEntry::create(array(
					'form_id' => 61,
                               
					'item_meta' => array(
                 
					597=>$data[0],
					
					598=>$data[1],
					599=>$data[2],
					600=>$data[3],
					
					601=>$data[4],
					602=>$data[5],
					603=>$data[6],
					604=>$data[7],
					
					605=>$data[8],
					606=>$data[9],
					607=>$data[10],
					611=>$data[11],
					612=>$data[12],
					
					
					610=>$data[13],	
					608=>$data[14],
									
					613=>$data[4],
							 
					),
					));           
				}
			fclose($handle);
		}
        break;
    
    case "Nursery":
        //echo "Your favorite color is green!";
        // error_log( " in nursery case:".$stclass);
          //echo( " in nursery case:".$stclass);
        if (($handle = fopen($file, "r")) !== FALSE)
		{
			fgetcsv($handle);   
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
				{
					FrmEntry::create(array(
					'form_id' => 60,
                               
					'item_meta' => array(
                 
					578=>$data[0],
					
					579=>$data[1],
					580=>$data[2],
					581=>$data[3],
					
					582=>$data[4],
					583=>$data[5],
					584=>$data[6],
					587=>$data[7],
					
					588=>$data[8],
					589=>$data[9],
					592=>$data[10],
					593=>$data[11],
					595=>$data[12],
					
					
					590=>$data[13],	
									
					594=>$data[4],
							 
					),
					));           
				}
			fclose($handle);
		}
        break;
        
	
    default:
        //echo "Your favorite color is neither red, blue, nor green!";
}
		
}
		
}

add_action('frm_after_create_entry', 'import_marks_general', 30, 2);
function import_marks_general( $entry_id, $form_id ) {
	if ( $form_id == 47 ) { //replace 5 with the id of the form	
	
	
	$info =sanitize_text_field( $_POST['item_meta'][417] );
	
	$file=wp_get_attachment_url( $info );
	
	


if (($handle = fopen($file, "r")) !== FALSE) {
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
  // if($flag){$flag=false;continue;}

	FrmEntry::create(array(
               'form_id' => 46,
                               
               'item_meta' => array(
                 
				 
				 408=>$data[2],
				 409=>$data[1],
				 410=>$data[3],
				 411=>$data[4],
				 412=>$data[5],
				 413=>$data[6],
				 
				 
				 
               ),
             ));           
 }
    fclose($handle);
}

		
}
		
}


add_filter('frmreg_required_role', 'frm_allow_front_end_user_creation');
function frm_allow_front_end_user_creation(){
  return 'administrator'; // change this to any user role
}

add_filter('frm_send_new_user_notification', 'frm_stop_user_notification', 10, 4);
function frm_stop_user_notification($send, $form, $entry_id, $entry){
  return false;
}
add_filter('frm_get_default_value', 'reset_user_id', 10, 2);
function reset_user_id($new_value, $field){
  if ( in_array( $field->id, array( 343 ) ) && current_user_can('administrator') ) { //change 25 to the ID of the userID field
    $new_value = '0';
  }
  return $new_value;
}


add_filter('frm_setup_edit_fields_vars', 'viewfieldsnoedit', 20, 2);
function viewfieldsnoedit($values, $field){
if($field->id == 272 or $field->id == 273 or $field->id == 274){ //change 272 to the ID of your field
   $values['read_only'] = 1;
}
return $values;
}

add_filter('frm_validate_field_entry', 'copy_my_dynamic_field_studentclass_fromattenatten', 10, 3);
function copy_my_dynamic_field_studentclass_fromattenatten( $errors, $posted_field, $posted_value ) {
  if ( $posted_field->id == 300 ) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][291] ) );
  }
  if ( $posted_field->id == 310 ) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][307] ) );
  }
   if ( $posted_field->id == 318 ) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][314] ) );
  }
   if ( $posted_field->id == 382 ) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 353, 'entry' => $_POST['item_meta'][354] ) );
  }
  if ( $posted_field->id ==707  ) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][704] ) );
  }
  if ( $posted_field->id == 713 ) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][709] ) );
  }
   if ( $posted_field->id == 723 ) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][715] ) );
  }
  
   if ( $posted_field->id == 732 ) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][725] ) );
  }
  
  if ( $posted_field->id == 778) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][774] ) );
  }
  
   if ( $posted_field->id == 681) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][677] ) );
  }
  
   if ( $posted_field->id == 426) {
    $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][644] ) );
  }
  return $errors;
}






add_action('frm_after_create_entry', 'get_file_details', 30, 2);
function get_file_details( $entry_id, $form_id ) {
	if ( $form_id == 38 ) { //replace 5 with the id of the form	

	
	$info =sanitize_text_field( $_POST['item_meta'][345] );
	
	$file=wp_get_attachment_url( $info );
	
	
$flag=true;

if (($handle = fopen($file, "r")) !== FALSE) {
   fgetcsv($handle);   
   $flag=true;
   while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
   if($data[1]==''||$data[1]==NULL)continue;
 if($data[4]==''||$data[4]==NULL){
 $data[4]='1234567890';
 }
  /*if($data[6]==''||$data[6]==NULL){
 $data[6]=0;
 }*/
 

	/*FrmEntry::create(array(
               'form_id' => 27,
                               
               'item_meta' => array(
                 
				 237=>$data[0],
				 238=>$data[1],
				 239=>$data[2],
				 240=>$data[3],
				 241=>$data[4],
				 259=>$data[5],
				 242=>$data[6],
				 244=>$data[7],
				 247=>$data[8],
				 248=>$data[9],
				 333=>$data[10],
				 245=>$data[11],
				 251=>$data[12],
				 255=>$data[13],
				 256=>$data[14],
				 257=>$data[15],
				 346=>$data[16],
				 343=>$data[16],
				 383=>$data[12],
				 
               ),
             )); */  
             FrmEntry::create(array(
               'form_id' => 27,
                               
               'item_meta' => array(
                 
				 383=>$data[0],
				 256=>$data[1],
				 257=>$data[2],
				 242=>$data[3],
				 247=>$data[4],
				 333=>$data[4],
				 245=>$data[5],
				 
				 
               ),
             ));      
             
             /*FrmEntry::create(array(
               'form_id' => 28,
                'item_key' =>$data[16].rand(100,999),
               
               'item_meta' => array(
                 262 =>$data[16], //change 48262 to the ID of the field you want to populate (in Form B)
                 263 => $data[15],//Change 48269 to the ID of the userID field in Form B
				 267 => $data[12],
				 264 =>$data[6],
				 268 => $data[14],
				 269 => $data[13],
				 303=>$data[8],
				 275=>$data[16],
               ),
             ));*/
    
    FrmEntry::create(array(
               'form_id' => 28,
               
               
               'item_meta' => array(
                 263 => $data[2],
				 267 => $data[0],
				 264 =>$data[3],
				 268 => $data[1],
				 269 =>0,
				 303=>$data[4],
               ),
             ));
	

		

 }
    fclose($handle);
}

		
}
		
}

add_filter('frm_validate_field_entry', 'validate_number_of_files_csv_file_student_info', 10, 3);
function validate_number_of_files_csv_file_student_info($errors, $field, $value){
global $csv_file_student_info;
    if ( in_array($field->id, array(345)) && $field->type == 'file' && (isset($_FILES['file'.$field->id])) && !empty($_FILES['file'.$field->id]['name'])){
        $csv_file_student_info= $_FILES['file'. $field->id]['name'];
		

    }
   
    return $errors;
}

add_filter('frm_validate_field_entry', 'validate_number_of_files_csv_file_student_marks', 10, 3);
function validate_number_of_files_csv_file_student_marks($errors, $field, $value){
global $csv_file_student_marks;
    if ( in_array($field->id, array(377)) && $field->type == 'file' && (isset($_FILES['file'.$field->id])) && !empty($_FILES['file'.$field->id]['name'])){
        $csv_file_student_marks= $_FILES['file'. $field->id]['name'];
		

    }
   
    return $errors;
}

add_filter('frm_validate_field_entry', 'validate_number_of_files_csv_file_student_marks_general', 10, 3);
function validate_number_of_files_csv_file_student_marks_general($errors, $field, $value){
global $csv_file_student_marks_general;
    if ( in_array($field->id, array(417)) && $field->type == 'file' && (isset($_FILES['file'.$field->id])) && !empty($_FILES['file'.$field->id]['name'])){
        $csv_file_student_marks_general= $_FILES['file'. $field->id]['name'];
		

    }
   
    return $errors;
}



 


add_filter('frm_validate_field_entry', 'addingtwofields', 8, 3);
function addingtwofields( $errors, $posted_field, $posted_value ) {
  if($posted_field->id == 346){ //346 is the id of regd num
 $frmpro_settings = FrmProAppHelper::get_settings();
 $str1 = FrmProAppHelper::convert_date($_POST['item_meta'][239], $frmpro_settings->date_format, 'dmy');  //239 id of dob

    $_POST['item_meta'][346] = str_replace(" ","",$_POST['item_meta'][238]). $str1;// 237 id of surname
    //change each number (20, 21, 22, 23) to the ID of the field to insert
  }
if($posted_field->id == 257){ //change 257 to the ID of full name
 

    //$_POST['item_meta'][257] = $_POST['item_meta'][237] . ' ' . $_POST['item_meta'][238];
    //change each number (20, 21, 22, 23) to the ID of the field to insert
  }
  
  if($posted_field->id == 257){ //change 257 to the ID of full name
 

    $_POST['item_meta'][257] = $_POST['item_meta'][237] . ' ' . $_POST['item_meta'][238];
    //change each number (20, 21, 22, 23) to the ID of the field to insert
  }

  return $errors;
}




add_filter('frm_validate_field_entry', 'two_fields_unique_att_form', 10, 2);
function two_fields_unique_att_form( $errors, $posted_field ) {
  $first_field_id = 291; // change 237to the id of the class
   global $wpdb;

  if ( $posted_field->id == $first_field_id ) {
  $class_meta_value2=FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][291] ) );
  $class_meta_value=$_POST['item_meta'][291];
  $second_field_id = 292; // change 239to the id of the attendance date
 $frmpro_settings = FrmProAppHelper::get_settings();
  $selected_date11a = FrmProAppHelper::convert_date($_POST['item_meta'][ $second_field_id ], $frmpro_settings->date_format, 'Y-m-d');
    $entry_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $values_used = FrmDb::get_col( 'frm_item_metas',
		array( 'item_id !' => $entry_id,
			array('or'=>1,
				array( 'field_id' => $first_field_id, 'meta_value' => $class_meta_value),
array( 'field_id' => $second_field_id, 'meta_value' =>  $selected_date11a))
			
		), 'item_id', array( 'group_by' => 'item_id', 'having' => 'COUNT(*)>1' )
	);
	
$qry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 71 )and (m.field_id=704  and  m.meta_value=". $_POST['item_meta'][291] .")or  (m.field_id=705 and  m.meta_value='". $selected_date11a."') group by m.item_id having count(*)=2";

//echo $qry;
$entries =$wpdb->get_col($qry);
$cnt1=count($entries);
	
	if ( ! empty( $values_used ) ||  $cnt1>0) {
		//$errors[ 'field'. $first_field_id ] = 'You have already added this class';
		//$errors[ 'field'. $second_field_id ] = 'You have already added on this date';
		$errors[ 'field'. $first_field_id ] = '   ';
		$errors[ 'field'. $second_field_id ] = '  ';
	//$class_meta_value=FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][291] ) );
		
		header("Location: /student-attendance-entry/?attclassid=". $_POST['item_meta'][291]."&attdate=".$selected_date11a ."&attclass=".$class_meta_value2);
	}
	else{
	//add student attendance in that class
	
	 
 /*$student_details_formid=28;
 $class_fieldid=267; //class field in student fee details
 $redgno_fieldid=268; //regd no in studentdetails
 $attendance_formid=33;*/
 $class_meta_value=FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][291] ) );

  $student_details_formid=27;
 $class_fieldid=383; //class field in student fee details
 $redgno_fieldid=256; //regd no in studentdetails
 $attendance_formid=33;
 
 
 $item_ids = $wpdb->get_col($wpdb->prepare("SELECT m.item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) WHERE m.field_id=%d and m.meta_value=%s and i.form_id=%d", $class_fieldid, $class_meta_value, $student_details_formid));
 

foreach($item_ids as $item_id)
 {


 $st_name= FrmProEntriesController::get_field_value_shortcode(array('field_id' => 257, 'entry' => $item_id));
 $regd_num=FrmProEntriesController::get_field_value_shortcode(array('field_id' => 256, 'entry' => $item_id));
 $phone_num=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>247 , 'entry' => $item_id));
 $father=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>242 , 'entry' => $item_id));


	FrmEntry::create(array(
               'form_id' => $attendance_formid,
                               
               'item_meta' => array(
                 
				 293 => $class_meta_value,
				 294 => $regd_num,
				 296 => $selected_date11a,
				 302=>'Present',
				 299=>$st_name,
				 305=> $phone_num,
				 312=>$father,
				 
               ),
             ));
	
 }
	}
	
  }
  return $errors;
}



add_filter('frm_validate_field_entry', 'two_fields_unique_student_details', 10, 2);
function two_fields_unique_student_details( $errors, $posted_field ) {
  $first_field_id = 237; // change 125 to the id of the first field
  
  
  if ( $posted_field->id == $first_field_id ) {
  $second_field_id = 239; // change 126 to the id of the second field
  $third_field_id = 238; // change 126 to the id of the second field
   $frmpro_settings = FrmProAppHelper::get_settings();
   $stdob='';
  // echo $_POST['item_meta'][239];
  $stdob = FrmProAppHelper::convert_date($_POST['item_meta'][$second_field_id], $frmpro_settings->date_format, 'Y-m-d');
    $entry_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $values_used = FrmDb::get_col( 'frm_item_metas',
		array( 'item_id !' => $entry_id,
			array( 'or' => 1,
				//array( 'field_id' => $first_field_id, 'meta_value' => $_POST['item_meta'][ $first_field_id ] ),
				array( 'field_id' => $second_field_id, 'meta_value' => $stdob ),
				array( 'field_id' => $third_field_id, 'meta_value' => $_POST['item_meta'][ $third_field_id ] )
			)
		), 'item_id', array( 'group_by' => 'item_id', 'having' => 'COUNT(*) >1' )
	);
	if ( ! empty( $values_used ) ) {
		//$errors[ 'field'. $first_field_id ] = 'You have already selected that option';
		$errors[ 'field'. $second_field_id ] = 'You have already selected that option';
		$errors[ 'field'. $third_field_id ] = 'You have already selected that option';
	}else{
	

	
	}
  }
  return $errors;
}
add_action('frm_after_create_entry', 'copy_into_fee_table', 20, 2);
function copy_into_fee_table($entry_id, $form_id){
  if($form_id == 27 && isset($_POST['item_meta'][346])){ //change 4 to the form id of the form to copy
  $frmpro_settings = FrmProAppHelper::get_settings();
 $str2 = FrmProAppHelper::convert_date($_POST['item_meta'][239], $frmpro_settings->date_format, 'dmy'); 
	
	FrmEntry::create(array(
               'form_id' => 28,
                'item_key' => $_POST['item_meta'][346].rand(100,999),
               
               'item_meta' => array(
                 262 =>$_POST['item_meta'][238].$str2, //change 48262 to the ID of the field you want to populate (in Form B)
                 263 => $_POST['item_meta'][237].' '.$_POST['item_meta'][238],//Change 48269 to the ID of the userID field in Form B
				 267 => FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 149, 'entry' => $_POST['item_meta'][251] ) ),
				 264 => $_POST['item_meta'][242],
				 268 => $_POST['item_meta'][256],
				 269 => $_POST['item_meta'][255],
				 303=>$_POST['item_meta'][247],
				 275=>$_POST['item_meta'][238].$str2,
               ),
             ));
			 
   
  }
}

add_shortcode('checkclass1','checkclass1_function');
function checkclass1_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=1)
	$content='';
	return $content;
}
add_shortcode('checkclass5','checkclass5_function');
function checkclass5_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=5)
	$content='';
	return $content;
}
add_shortcode('checkclass4','checkclass4_function');
function checkclass4_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=4)
	$content='';
	return $content;
}

add_shortcode('checkclass3','checkclass3_function');
function checkclass3_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=3)
	$content='';
	return $content;
}
add_shortcode('checkclass2','checkclass2_function');
function checkclass2_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=2)
	$content='';
	return $content;
}
add_shortcode('checkclassLKG','checkclassLKG_function');
function checkclassLKG_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if(strcmp($a['stclass'],'LKG')!=0)
	$content='';
	return $content;
}
add_shortcode('checkclassUKG','checkclassUKG_function');
function checkclassUKG_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if(strcmp($a['stclass'],'UKG')!=0)
	$content='';
	return $content;
}
add_shortcode('checkclassNursery','checkclassNursery_function');
function checkclassNursery_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if(strcmp($a['stclass'],'Nursery')!=0)
	$content='';
	return $content;
}

add_shortcode('checkpresent','checkpresent_function');
function checkpresent_function($atts,$content=null)
{
	$a=shortcode_atts(array('status'=>'Present'),$atts);
	if(strcmp($a['status'],'Present')!=0)
	$content='';
	return $content;
}

add_shortcode('checkabsent','checkabsent_function');
function checkabsent_function($atts,$content=null)
{
	$a=shortcode_atts(array('status'=>'Absent'),$atts);
	if(strcmp($a['status'],"Absent")!=0)
	$content='';
	
	return $content;
}

add_shortcode('checkclass6','checkclass6_function');
function checkclass6_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=6)
	$content='';
	return $content;
}

add_shortcode('checkclass7','checkclass7_function');
function checkclass7_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=7)
	$content='';
	return $content;
}

add_shortcode('checkclass8','checkclass8_function');
function checkclass8_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=8)
	$content='';
	return $content;
}

add_shortcode('checkclass9','checkclass9_function');
function checkclass9_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=9)
	$content='';
	return $content;
}

add_shortcode('checkclass10','checkclass10_function');
function checkclass10_function($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>0),$atts);
	if($a['stclass']!=10)
	$content='';
	return $content;
}
add_filter('frm_before_display_content', 'dynamic_frm_stats_present_absent_cnt', 10, 4);
function dynamic_frm_stats_present_absent_cnt($content, $display, $show, $atts){
	if($display->ID == 860 || $display->ID == 2872){//Change 1066 to the ID of your View
		$entries = $atts['entry_ids'];
		//$total = 0;
		$present_count=0;
		$absent_count=0;
		$att_fieldid=302;
$att_date= FrmProEntriesController::get_field_value_shortcode(array( 'field_id' => 296, 'entry' => $entries[0]) );
		foreach($entries as $entry){
			//$total += FrmProEntriesController::get_field_value_shortcode(array( 'field_id' => x, 'entry' => $entry ) );
			$att_status=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$att_fieldid , 'entry' => $entry));
			if($att_status=="Present")
				$present_count+=1;
				else{
				$absent_count+=1;


				
		}
		}
		$total_count=$absent_count+$present_count;
		$display_str="<th><strong>Presents:". $present_count. "</th>
		<th><strong>Absents: ". $absent_count."</th>
		<th><strong>Total No. of Students:".$total_count ."</strong></th>";
		$content = str_replace('[sum_302]',$display_str, $content);
	}
	return $content;
}

add_filter('frm_before_display_content', 'nameandclass', 10, 4);
function nameandclass($content, $display, $show, $atts){
	if($display->ID ==1417 ){//Change 1066 to the ID of your View
		$entries = $atts['entry_ids'];
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$display_str ="<th>Name:". $my_entry['x2jzz'] . "<th>";
		$display_str .="<th>Class:". $my_entry['dja2v'] . "<th>";
		
		
		$content = str_replace('[sum_410]',$display_str, $content);
		
	}
	return $content;
	}
	

add_filter('frm_before_display_content', 'dynamic_frm_stats_avg_marks_percentage_sw_classLKGUKGNursery', 10, 4);
function dynamic_frm_stats_avg_marks_percentage_sw_classLKGUKGNursery($content, $display, $show, $atts){
	if($display->ID ==2124 ){//id of class LKGUKGNursery sw marks view
		$entries = $atts['entry_ids'];
		$total = 0;
		
		$per_fieldid=612;
		foreach($entries as $entry){
			
			$total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$display_str ="<th>Name:". $my_entry['dvnxc52'] . "<th>";
		$display_str .="<th>Class:". $my_entry['fm3wn52'] . "<th>";
		$display_str .="<th>Regd Num.:". $my_entry['z34vg52'] . "<th>";
		
		$avg="-";
		if(count($entries)!=0)
		$avg=round($total/count($entries),2);
		$display_str .="<th>No. of Tests:". count($entries) . "</th><th>Avg Percentage:". $avg."</th>";
		
		$content = str_replace('[sum_360]',$display_str, $content);
		
	}
	return $content;
	}
	
add_filter('frm_before_display_content', 'dynamic_frm_stats_avg_marks_percentage_sw_classNursery', 10, 4);
function dynamic_frm_stats_avg_marks_percentage_sw_classNursery($content, $display, $show, $atts){
	if($display->ID ==2839 ){//id of class LKGUKGNursery sw marks view
		$entries = $atts['entry_ids'];
		$total = 0;
		
		$per_fieldid=593;
		foreach($entries as $entry){
			
			$total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$display_str ="<th>Name:". $my_entry['dvnxc5'] . "<th>";
		$display_str .="<th>Class:". $my_entry['fm3wn5'] . "<th>";
		$display_str .="<th>Regd Num.:". $my_entry['z34vg5'] . "<th>";
		
		$avg="-";
		if(count($entries)!=0)
		$avg=round($total/count($entries),2);
		$display_str .="<th>No. of Tests:". count($entries) . "</th><th>Avg Percentage:". $avg."</th>";
		
		$content = str_replace('[sum_360]',$display_str, $content);
		
	}
	return $content;
	}


add_filter('frm_before_display_content', 'dynamic_frm_stats_avg_marks_percentage_sw_class1', 10, 4);
function dynamic_frm_stats_avg_marks_percentage_sw_class1($content, $display, $show, $atts){
	if($display->ID ==2120 ||$display->ID ==2772 ){//id of class1_2 sw marks view
		$entries = $atts['entry_ids'];
		$total = 0;
		
		$per_fieldid=363;
		foreach($entries as $entry){
			
			$total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$display_str ="<th>Name:". $my_entry['dvnxc'] . "<th>";
		$display_str .="<th>Class:". $my_entry['fm3wn'] . "<th>";
		$display_str .="<th>Regd Num.:". $my_entry['z34vg'] . "<th>";
		
		$avg="-";
		if(count($entries)!=0)
		$avg=round($total/count($entries),2);
		$display_str .="<th>No. of Tests:". count($entries) . "</th><th>Avg Percentage:". $avg."</th>";
		
		$content = str_replace('[sum_360]',$display_str, $content);
		
	}
	return $content;
	}
	

		
add_filter('frm_before_display_content', 'dynamic_frm_stats_avg_marks_percentage_sw_class3', 10, 4);
function dynamic_frm_stats_avg_marks_percentage_sw_class3($content, $display, $show, $atts){
	if($display->ID ==2087 || $display->ID ==2789  ){//id of class3 sw marks view
		$entries = $atts['entry_ids'];
		$total = 0;
		
		$per_fieldid=513;
		foreach($entries as $entry){
			
			$total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$display_str ="<th>Name:". $my_entry['dvnxc42'] . "<th>";
		$display_str .="<th>Class:". $my_entry['fm3wn42'] . "<th>";
		$display_str .="<th>Regd Num.:". $my_entry['z34vg42'] . "<th>";
		
		$avg="-";
		if(count($entries)!=0)
		$avg=round($total/count($entries),2);
		$display_str .="<th>No. of Tests:". count($entries) . "</th><th>Avg Percentage:". $avg."</th>";
		
		$content = str_replace('[sum_360]',$display_str, $content);
		
	}
	return $content;
	}	

add_filter('frm_before_display_content', 'dynamic_frm_stats_avg_marks_percentage_sw_class8', 10, 4);
function dynamic_frm_stats_avg_marks_percentage_sw_class8($content, $display, $show, $atts){
	if($display->ID ==2911 ){//id of class3 sw marks view
		$entries = $atts['entry_ids'];
		$total = 0;
		
		$per_fieldid=750;
		foreach($entries as $entry){
			
			$total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$display_str ="<th>Name:". $my_entry['dvnxc44'] . "<th>";
		$display_str .="<th>Class:". $my_entry['fm3wn44'] . "<th>";
		$display_str .="<th>Regd Num.:". $my_entry['z34vg44'] . "<th>";
		
		$avg="-";
		if(count($entries)!=0)
		$avg=round($total/count($entries),2);
		$display_str .="<th>No. of Tests:". count($entries) . "</th><th>Avg Percentage:". $avg."</th>";
		
		$content = str_replace('[sum_360]',$display_str, $content);
		
	}
	return $content;
	}	


add_filter('frm_before_display_content', 'dynamic_frm_stats_tw_class1_2_marks', 10, 4);
function dynamic_frm_stats_tw_class1_2_marks($content, $display, $show, $atts){
	if($display->ID ==2118 || $display->ID ==2756  ){//id of class1 tw marks view
		$entries = $atts['entry_ids'];
		$telugu_total=0;
		$hindi_total=0;
		$english_total = 0;
		$math_total = 0;
		$evs_total = 0;
		$per_total = 0;
		$computer_total = 0;
		$gk_total = 0;
		$drawing_total = 0;
		
		$telugu_fieldid=458;
		$hindi_fieldid=355;
		$english_fieldid=357;
		$math_fieldid=358;
		$evs_fieldid=459;
		$computer_fieldid=475;
		$gk_fieldid=476;
		$drawing_fieldid=478;
		
		$per_fieldid=363;
		
		
		
		foreach($entries as $entry){
			$telugu_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$telugu_fieldid, 'entry' => $entry));
			$hindi_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$hindi_fieldid, 'entry' => $entry));
			$english_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$english_fieldid, 'entry' => $entry));
			$math_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$math_fieldid, 'entry' => $entry));
			$evs_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$evs_fieldid, 'entry' => $entry));
			$computer_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$computer_fieldid, 'entry' => $entry));
			$gk_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$gk_fieldid, 'entry' => $entry));
			$drawing_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$drawing_fieldid, 'entry' => $entry));
			
			$per_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$cnt=count($entries);
		$telugu_avg=$hindi_avg=$english_avg=$math_avg=$evs_avg=$computer_avg=$gk_avg=$drawing_avg=$total_avg=$per_avg="-";
		if($cnt !=0){
		$telugu_avg=round($telugu_total/$cnt,2);
		$hindi_avg=round($hindi_total/$cnt,2);		
		$english_avg=round($english_total/$cnt,2);	
		$math_avg=round($math_total/$cnt,2);
		$evs_avg=round($evs_total/$cnt,2);
		
		$computer_avg=round($computer_total/$cnt,2);
		$gk_avg=round($gk_total/$cnt,2);
		$drawing_avg=round($drawing_total/$cnt,2);
		
		
		
		$per_avg=round($per_total/$cnt,2);
		
		
		$total_avg=round(($telugu_avg+$hindi_avg+$english_avg+$math_avg+$evs_avg)/5,2);
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		
		
		$display_str="";
		$display_str.="<th colspan='2'>Max Marks:". $my_entry['y3dgj']."</th><th colspan='6'>Test Date:".$my_entry['zp5x1']."</th> </tr>";
		$display_str.="<tr><th colspan='2'>Class Average:</th>
		<th>Telugu<br/>:".$telugu_avg."</th>
		<th>Hindi<br/>:".$hindi_avg."</th> 
		<th>English<br/>:".$english_avg."</th>
		<th>Math<br/>:".$math_avg."</th>
		<th>EVS<br/>:".$evs_avg."</th>		
		<th>Total Marks<br/>:".$total_avg."</th>
		<th>Marks%<br/>:".$per_avg."</th>
		<th>CMP<br/>:".$computer_avg."</th>
		<th>GK<br/>:".$gk_avg."</th>
		<th>Drawing<br/>:".$drawing_avg."</th></tr>";
	
		
		
		$content = str_replace('[sum_361]',$display_str, $content);
		
	}
	return $content;
	}	


	
add_filter('frm_before_display_content', 'dynamic_frm_stats_tw_classNLU_marks', 10, 4);
function dynamic_frm_stats_tw_classNLU_marks($content, $display, $show, $atts){
	if($display->ID ==2138 ||$display->ID ==2816 ){//id of class  LKG UKG  tw marks view
		$entries = $atts['entry_ids'];
		$telugu_total=0;
		
		$english_total = 0;
		$math_total = 0;
		$evs_total = 0;
		$per_total = 0;
		$rhymes_total = 0;
		$gk_total = 0;
		$drawing_total = 0;
		
		$telugu_fieldid=604;
		
		$english_fieldid=605;
		$math_fieldid=606;
		$evs_fieldid=607;
		$rhymes_fieldid=608;
		$gk_fieldid=609;
		$drawing_fieldid=610;
		
		$per_fieldid=612;
		
		
		
		foreach($entries as $entry){
			$telugu_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$telugu_fieldid, 'entry' => $entry));
			
			$english_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$english_fieldid, 'entry' => $entry));
			$math_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$math_fieldid, 'entry' => $entry));
			$evs_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$evs_fieldid, 'entry' => $entry));
			$rhymes_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$rhymes_fieldid, 'entry' => $entry));
			//$gk_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$gk_fieldid, 'entry' => $entry));
			$drawing_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$drawing_fieldid, 'entry' => $entry));
			
			$per_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$cnt=count($entries);
		$telugu_avg=$english_avg=$math_avg=$evs_avg=$rhymes_avg=$gk_avg=$drawing_avg=$total_avg=$per_avg="-";
		if($cnt !=0){
		$telugu_avg=round($telugu_total/$cnt,2);
		//$hindi_avg=round($hindi_total/$cnt,2);		
		$english_avg=round($english_total/$cnt,2);	
		$math_avg=round($math_total/$cnt,2);
		$evs_avg=round($evs_total/$cnt,2);
		
		$rhymes_avg=round($rhymes_total/$cnt,2);
		//$gk_avg=round($gk_total/$cnt,2);
		$drawing_avg=round($drawing_total/$cnt,2);
		
		
		
		$per_avg=round($per_total/$cnt,2);
		
		
		$total_avg=round(($telugu_avg+$english_avg+$math_avg+$evs_avg)/4,2);
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		
		
	$display_str="";
		$display_str.="<th colspan='2'>Max Marks:". $my_entry['y3dgj52']."</th><th colspan='6'>Test Date:".$my_entry['zp5x142']."</th> </tr>";
		$display_str.="<tr><th colspan='2'>Class Average:</th>
		<th>Telugu<br/>:".$telugu_avg."</th>
		
		<th>English<br/>:".$english_avg."</th>
		<th>Math<br/>:".$math_avg."</th>
		<th>EVS<br/>:".$evs_avg."</th>		
		<th>Total Marks<br/>:".$total_avg."</th>
		<th>Marks%<br/>:".$per_avg."</th>
		
		<th>Drawing<br/>:".$drawing_avg."</th>
		<th>Rhymes<br/>:".$rhymes_avg."</th>		
		</tr>";
	
		
		
		$content = str_replace('[sum_361]',$display_str, $content);
		
	}
	return $content;
	}	

add_filter('frm_before_display_content', 'dynamic_frm_stats_tw_classNursery_marks', 10, 4);
function dynamic_frm_stats_tw_classNursery_marks($content, $display, $show, $atts){
	if($display->ID ==2825 ||$display->ID ==2822){//id of class  LKG UKG  tw marks view
		$entries = $atts['entry_ids'];
		//$telugu_total=0;
		
		$english_total = 0;
		$math_total = 0;
		$evs_total = 0;
		$per_total = 0;
		$rhymes_total = 0;
		//$gk_total = 0;
		$drawing_total = 0;
		
		
		
		$english_fieldid=587;
		$math_fieldid=588;
		$evs_fieldid=589;
		$rhymes_fieldid=590;
		
		$drawing_fieldid=595;
		
		$per_fieldid=593;
		
		
		
		foreach($entries as $entry){
			//$telugu_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$telugu_fieldid, 'entry' => $entry));
			
			$english_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$english_fieldid, 'entry' => $entry));
			$math_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$math_fieldid, 'entry' => $entry));
			$evs_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$evs_fieldid, 'entry' => $entry));
			$rhymes_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$rhymes_fieldid, 'entry' => $entry));
			//$gk_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$gk_fieldid, 'entry' => $entry));
			$drawing_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$drawing_fieldid, 'entry' => $entry));
			
			$per_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$cnt=count($entries);
		$english_avg=$math_avg=$evs_avg=$rhymes_avg=$gk_avg=$drawing_avg=$total_avg=$per_avg="-";
		if($cnt !=0){
		//$telugu_avg=round($telugu_total/$cnt,2);
		//$hindi_avg=round($hindi_total/$cnt,2);		
		$english_avg=round($english_total/$cnt,2);	
		$math_avg=round($math_total/$cnt,2);
		$evs_avg=round($evs_total/$cnt,2);
		
		$rhymes_avg=round($rhymes_total/$cnt,2);
		//$gk_avg=round($gk_total/$cnt,2);
		$drawing_avg=round($drawing_total/$cnt,2);
		
		
		
		$per_avg=round($per_total/$cnt,2);
		
		
		$total_avg=round(($english_avg+$math_avg+$evs_avg)/3,2);
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		
		
	$display_str="";
		$display_str.="<th colspan='2'>Max Marks:". $my_entry['y3dgj5']."</th><th colspan='5'>Test Date:".$my_entry['zp5x14']."</th> </tr>";
		$display_str.="<tr><th colspan='2'>Class Average:</th>
		
		
		<th>English<br/>:".$english_avg."</th>
		<th>Math<br/>:".$math_avg."</th>
		<th>EVS<br/>:".$evs_avg."</th>		
		<th>Total Marks<br/>:".$total_avg."</th>
		<th>Marks%<br/>:".$per_avg."</th>
		
		<th>Drawing<br/>:".$drawing_avg."</th>
		<th>Rhymes<br/>:".$rhymes_avg."</th>		
		</tr>";
	
		
		
		$content = str_replace('[sum_361]',$display_str, $content);
		
	}
	return $content;
	}	



add_filter('frm_before_display_content', 'dynamic_frm_stats_tw_class3_marks', 10, 4);
function dynamic_frm_stats_tw_class3_marks($content, $display, $show, $atts){
	if($display->ID ==2083 ||$display->ID ==2792  ){//id of class3 tw marks view
		$entries = $atts['entry_ids'];
		$telugu_total=0;
		$hindi_total=0;
		$english_total = 0;
		$math_total = 0;
		$science_total = 0;
		$social_total = 0;
		$per_total = 0;
		$computer_total = 0;
		$gk_total = 0;
		$drawing_total = 0;
		
		$telugu_fieldid=505;
		$hindi_fieldid=506;
		$english_fieldid=507;
		$math_fieldid=508;
		$science_fieldid=509;
		$social_fieldid=517;
		$computer_fieldid=510;
		$gk_fieldid=511;
		$drawing_fieldid=515;
		
		$per_fieldid=513;
		
		
		
		foreach($entries as $entry){
			$telugu_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$telugu_fieldid, 'entry' => $entry));
			$hindi_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$hindi_fieldid, 'entry' => $entry));
			$english_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$english_fieldid, 'entry' => $entry));
			$math_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$math_fieldid, 'entry' => $entry));
			$science_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$science_fieldid, 'entry' => $entry));
			$social_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$social_fieldid, 'entry' => $entry));
			$computer_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$computer_fieldid, 'entry' => $entry));
			$gk_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$gk_fieldid, 'entry' => $entry));
			$drawing_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$drawing_fieldid, 'entry' => $entry));
			
			$per_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$cnt=count($entries);
		$telugu_avg=$hindi_avg=$english_avg=$math_avg=$evs_avg=$social_avg=$computer_avg=$gk_avg=$drawing_avg=$total_avg=$per_avg="-";
		if($cnt !=0){
		$telugu_avg=round($telugu_total/$cnt,2);
		$hindi_avg=round($hindi_total/$cnt,2);		
		$english_avg=round($english_total/$cnt,2);	
		$math_avg=round($math_total/$cnt,2);
		$science_avg=round($science_total/$cnt,2);
		$social_avg=round($social_total/$cnt,2);
		
		$computer_avg=round($computer_total/$cnt,2);
		$gk_avg=round($gk_total/$cnt,2);
		$drawing_avg=round($drawing_total/$cnt,2);
		
		
		
		$per_avg=round($per_total/$cnt,2);
		
		
		$total_avg=round(($telugu_avg+$hindi_avg+$english_avg+$math_avg+$science_avg+$social_avg)/6,2);
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		
		
		$display_str="";
		$display_str.="<th colspan='2'>Max Marks:". $my_entry['y3dgj42']."</th><th colspan='5'>Test Date:".$my_entry['zp5x132']."</th> </tr>";
		$display_str.="<tr><th colspan='2'>Class Average:</th>
		<th>Telugu<br/>:".$telugu_avg."</th>
		<th>Hindi<br/>:".$hindi_avg."</th> 
		<th>English<br/>:".$english_avg."</th>
		<th>Math<br/>:".$math_avg."</th>
		<th>Science<br/>:".$science_avg."</th>
		<th>Social<br/>:".$social_avg."</th>	
		<th>Total<br/>:".$total_avg."</th>
		<th>%<br/>:".$per_avg."</th>
		<th>CMP<br/>:".$computer_avg."</th>
		<th>GK<br/>:".$gk_avg."</th></tr>";
		
		
		$content = str_replace('[sum_361]',$display_str, $content);
		
	}
	return $content;
	}	
	

	
add_filter('frm_before_display_content', 'dynamic_frm_stats_tw_class8_marks', 10, 4);
function dynamic_frm_stats_tw_class8_marks($content, $display, $show, $atts){
	if($display->ID ==2916 ||$display->ID ==2914  ){//id of class8 tw marks view
		$entries = $atts['entry_ids'];
		$telugu_total=0;
		$hindi_total=0;
		$english_total = 0;
		$math_total = 0;
		$science_total = 0;
		$social_total = 0;
		$per_total = 0;
		$computer_total = 0;
		$gk_total = 0;
		//$drawing_total = 0;
		
		$telugu_fieldid=740;
		$hindi_fieldid=741;
		$english_fieldid=742;
		$math_fieldid=743;
		$science_fieldid=744;
		$social_fieldid=745;
		$computer_fieldid=746;
		$gk_fieldid=747;
		//$drawing_fieldid=515;
		
		$per_fieldid=750;
		
		
		
		foreach($entries as $entry){
			$telugu_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$telugu_fieldid, 'entry' => $entry));
			$hindi_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$hindi_fieldid, 'entry' => $entry));
			$english_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$english_fieldid, 'entry' => $entry));
			$math_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$math_fieldid, 'entry' => $entry));
			$science_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$science_fieldid, 'entry' => $entry));
			$social_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$social_fieldid, 'entry' => $entry));
			$computer_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$computer_fieldid, 'entry' => $entry));
			$gk_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$gk_fieldid, 'entry' => $entry));
			//$drawing_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$drawing_fieldid, 'entry' => $entry));
			
			$per_total+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$per_fieldid, 'entry' => $entry));
			
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$cnt=count($entries);
		$telugu_avg=$hindi_avg=$english_avg=$math_avg=$evs_avg=$social_avg=$computer_avg=$gk_avg=$drawing_avg=$total_avg=$per_avg="-";
		if($cnt !=0){
		$telugu_avg=round($telugu_total/$cnt,2);
		$hindi_avg=round($hindi_total/$cnt,2);		
		$english_avg=round($english_total/$cnt,2);	
		$math_avg=round($math_total/$cnt,2);
		$science_avg=round($science_total/$cnt,2);
		$social_avg=round($social_total/$cnt,2);
		
		$computer_avg=round($computer_total/$cnt,2);
		$gk_avg=round($gk_total/$cnt,2);
		//$drawing_avg=round($drawing_total/$cnt,2);
		
		
		
		$per_avg=round($per_total/$cnt,2);
		
		
		$total_avg=round(($telugu_avg+$hindi_avg+$english_avg+$math_avg+$science_avg+$social_avg)/6,2);
		}
		
		global $frm_entry;
		$my_entry = FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		
		
		$display_str="";
		$display_str.="<th colspan='2'>Max Marks:". $my_entry['y3dgj44']."</th><th colspan='5'>Test Date:".$my_entry['zp5x134']."</th> </tr>";
		$display_str.="<tr><th colspan='2'>Class Average:</th>
		<th>Telugu<br/>:".$telugu_avg."</th>
		<th>Hindi<br/>:".$hindi_avg."</th> 
		<th>English<br/>:".$english_avg."</th>
		<th>Math<br/>:".$math_avg."</th>
		<th>Science<br/>:".$science_avg."</th>
		<th>Social<br/>:".$social_avg."</th>	
		<th>Total<br/>:".$total_avg."</th>
		<th>%<br/>:".$per_avg."</th>
		<th>CMP<br/>:".$computer_avg."</th>
		<th>GK<br/>:".$gk_avg."</th></tr>";
		
		
		$content = str_replace('[sum_361]',$display_str, $content);
		
	}
	return $content;
	}	
	

add_filter('frm_before_display_content', 'dynamic_frm_stats_present_absent_cnt2', 10, 4);
function dynamic_frm_stats_present_absent_cnt2($content, $display, $show, $atts){
	if($display->ID == 920|| $display->ID ==2899 ){//Change 1066 to the ID of your View
		$entries = $atts['entry_ids'];
		//$total = 0;
		$present_count=0;
		$absent_count=0;
		$att_fieldid=302;
		foreach($entries as $entry){
			//$total += FrmProEntriesController::get_field_value_shortcode(array( 'field_id' => x, 'entry' => $entry ) );
			$att_status=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>$att_fieldid , 'entry' => $entry));
			if($att_status=="Present")
				$present_count+=1;
				else
				$absent_count+=1;		
				
		}
		$total_count=$absent_count+$present_count;
		if($total_count==0){
		$present_count='-';
		$absent_count='-';
		$per='-';
		}
		else{
		$per=round($present_count*100/$total_count);
		}
		
		$result= FrmProEntriesController::show_entry_shortcode(array('id' => $entries[0], 'plain_text' => 1,'format'=>'array'));
		$stclass=$result['spady'];
		$stname=$result['ok0y1'];
		$father=$result['6k33j'];
		$phone=$result['ewcis'];
		$regdnum=$result['qj7p7'];
		
		
		$display_str="<th>Regd Num:<br/>".$regdnum. "</th>
		<th>Name:<br/> ".$stname."</th>
		<th>Father:<br/>".$father."</th>
		<th>Phone:<br/>".$phone."</th>
		<th>Presents: ".$present_count."</th>
		<th>Absents: ".$absent_count."</th>
		<th>Attn%: ".$per."</th> ";
		
		
		$content = str_replace('[sum_302]',$display_str, $content);
	}
	return $content;
}

	
add_action('frm_date_field_js', 'start_and_end_dates_consol_classwise_att_rptfrm', 10, 2);
function start_and_end_dates_consol_classwise_att_rptfrm($field_id, $options){
	$key_one = 'o1o4l3';// Change pickup to the KEY of the first date field
	$key_two = '7ov57';// Change dropoff to the KEY of the second date field
	$days_between = 0;// Change 0 to the number of days that should be between the start and end dates

	if ( $field_id == 'field_'. $key_one ) {
		echo ',beforeShowDay: function(dateOne){var secondDate=$("#field_' . $key_two . '").datepicker("getDate");if(secondDate==null){return[true];}var modDate=new Date(secondDate);modDate.setDate(modDate.getDate()-' . $days_between . '+1);return [(dateOne < modDate)];}';
	} else if ( $field_id == 'field_' . $key_two ) {
		echo ',beforeShowDay: function(dateTwo){var firstDate=$("#field_' . $key_one . '").datepicker("getDate");if(firstDate==null){return[true];}var modDate=new Date(firstDate);modDate.setDate(modDate.getDate() +  ' . $days_between . '-1);return [(dateTwo > modDate)];}';
	} else {
		echo ',beforeShowDay: null';
	}
}

add_action('frm_date_field_js', 'limit_my_att_from_date_field');
function limit_my_att_from_date_field($field_id){
  if($field_id == 'field_o1o4l3'){ //change FIELDKEY to the key of your date field
    echo ',minDate:-365,maxDate:0';
  }
}

add_action('frm_date_field_js', 'limit_my_att_to_date_field');
function limit_my_att_to_date_field($field_id){
  if($field_id == 'field_7ov57'){ //change FIELDKEY to the key of your date field
    echo ',minDate:-365,maxDate:0';
  }
}

add_shortcode('stconsolattendancemarks','student_con_attendance_marks');
function student_con_attendance_marks($atts,$content=null)
{
	$a=shortcode_atts(array('regdnum'=>'-1','fromdate'=>'-1','todate'=>'-1','stclass'=>'-1','sendsms'=>'No','stname'=>'','phone'=>'','entryid1'=>''),$atts);
	
	$attn_details_formid=33;
$redgno_fieldid=294;
$attstaus_fieldid=302;
$attn_date_fieldid=296;
$present_meta_value="Present";
$absent_meta_value="Absent";
global $wpdb;
$sendsms=$a['sendsms'];
$stname=$a['stname'];
$number=$a['phone'];
$presentquery="";
$absentquery="";


if($a['fromdate']==-1 or $a['todate']==-1 ){
$presentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$present_meta_value ."')  group by m.item_id having count(*)=2";
$absentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$absent_meta_value ."')  group by m.item_id having count(*)=2";		
}
else{
$presentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$present_meta_value ."') or (m.field_id=".$attn_date_fieldid." and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=3";
$absentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$absent_meta_value ."') or (m.field_id=".$attn_date_fieldid." and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=3";		
}
 
$present_count =count( $wpdb->get_col($presentquery));
$absent_count = count($wpdb->get_col($absentquery));
$stclass=$a['stclass'];
$avgmarksqry="";
$totalavg=0;

switch($stclass){
case 1:
case 2:
if($a['fromdate']==-1 or $a['todate']==-1 ){
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 39 )and (m.field_id=350  and  m.meta_value='".$a['regdnum'] ."') ";		
}
else{
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 39 )and (m.field_id=350  and  m.meta_value='".$a['regdnum'] ."')or  (m.field_id=378 and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=2";		
}

$entries =$wpdb->get_col($avgmarksqry);

foreach($entries as $entry){
			$totalavg+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>363 , 'entry' => $entry));
		}
		
 break;
 

 
 case 3:
  case 4:
  case 5:
   case 6:
    case 7:
if($a['fromdate']==-1 or $a['todate']==-1 ){
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 56 )and (m.field_id=500  and  m.meta_value='".$a['regdnum'] ."') ";		
}
else{
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 56 )and (m.field_id=500  and  m.meta_value='".$a['regdnum'] ."')or  (m.field_id=504 and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=2";		
}
$entries =$wpdb->get_col($avgmarksqry);

foreach($entries as $entry){
			$totalavg+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>513 , 'entry' => $entry));
		}
 break;


  case 8:
   case 9:
    case 10:
if($a['fromdate']==-1 or $a['todate']==-1 ){
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 75 )and (m.field_id=735  and  m.meta_value='".$a['regdnum'] ."') ";		
}
else{
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 75 )and (m.field_id=735  and  m.meta_value='".$a['regdnum'] ."')or  (m.field_id=504 and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=2";		
}
$entries =$wpdb->get_col($avgmarksqry);

foreach($entries as $entry){
			$totalavg+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>750 , 'entry' => $entry));
		}
 break;
 

 
 case 'LKG':
 case 'UKG':
 
if($a['fromdate']==-1 or $a['todate']==-1 ){
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 61 )and (m.field_id=599  and  m.meta_value='".$a['regdnum'] ."') ";		
}
else{
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 61 )and (m.field_id=599  and  m.meta_value='".$a['regdnum'] ."')or  (m.field_id=603 and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=2";		
}
$entries =$wpdb->get_col($avgmarksqry);

foreach($entries as $entry){
			$totalavg+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>612 , 'entry' => $entry));
		}
		
 break;
 
 
 case 'Nursery':
 
if($a['fromdate']==-1 or $a['todate']==-1 ){
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 60 )and (m.field_id=580  and  m.meta_value='".$a['regdnum'] ."') ";		
}
else{
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 60 )and (m.field_id=580  and  m.meta_value='".$a['regdnum'] ."')or  (m.field_id=584 and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=2";		
}
$entries =$wpdb->get_col($avgmarksqry);

foreach($entries as $entry){
			$totalavg+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>593 , 'entry' => $entry));
		}
		
 break;
 

 }


$avg_marks="-";
$per='-';
if(count($entries)!=0)
$avg_marks=round($totalavg/count($entries),2);

$output='';
if( $present_count + $absent_count==0){
$output='<td class="centeralign" data-label="Presents:">-</td> <td class="centeralign" data-label="Absents:">-</td class="centeralign" data-label="Percentage:"><td>-</td>';
}
else{
$per=round($present_count*100.0/( $present_count + $absent_count));
$output='<td class="centeralign" data-label="Presents:">'.$present_count.'</td> <td class="centeralign" data-label="Absents:">'. $absent_count .'</td><td class="centeralign" data-label="Attn %:">'.$per.'</td>';
}
if($avg_marks=='-' or !isset($avg_marks))
$output.='<td class="centeralign" data-label="Avg Marks:">-</td>';
else
$output.='<td class="centeralign" data-label="Avg Marks:">'.$avg_marks.'</td>';

 //$st_name= FrmProEntriesController::get_field_value_shortcode(array('field_id' => 263, 'entry' => $a['entryid1']));
 //$number= FrmProEntriesController::get_field_value_shortcode(array('field_id' => 263, 'entry' => $a['entryid1']));

if($sendsms=='Yes'){
$msg="Greetings from LFHS.";
$msg.="Your child:".$stname. "'s attendance percentage  is ".$per.", ";
if( $avg_marks!=0 || $avg_marks !='-')
{
$grade= find_grade($avg_marks);

$msg.="and got ".$avg_marks."percentage of marks with grade ".$grade." ,";
}

if($a['fromdate']!='-1' && $a['todate']!='-1'){
$msg.="during the period ";
$fromdate=date_format($a['fromdate'],"d-m-y");
$todate=date_format($a['todate'],"d-m-y");
$msg.= $fromdate . " to " . $todate;
}
else
$msg.=" till today";

$username="littleflowerhighschool";
$password ="littleflowerhighscho";
//$number="9573811540";
 $sender="LFHSTS";
 $message=$msg;

$url="login.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($number)."&sender=".urlencode($sender)."&message=".urlencode($message)."&type=".urlencode('3'); 
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$curl_scraped_page = curl_exec($ch);

curl_close($ch); 

}
return $output;

}

add_shortcode('stconsolattendancemarks_p','student_con_attendance_marks_p');
function student_con_attendance_marks_p($atts,$content=null)
{
	$a=shortcode_atts(array('regdnum'=>'-1','fromdate'=>'-1','todate'=>'-1','stclass'=>'-1','sendsms'=>'No','stname'=>'','phone'=>'','entryid1'=>''),$atts);
	
	$attn_details_formid=33;
$redgno_fieldid=294;
$attstaus_fieldid=302;
$attn_date_fieldid=296;
$present_meta_value="Present";
$absent_meta_value="Absent";
global $wpdb;
$sendsms=$a['sendsms'];
$stname=$a['stname'];
$number=$a['phone'];
$presentquery="";
$absentquery="";


if($a['fromdate']==-1 or $a['todate']==-1 ){
$presentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$present_meta_value ."')  group by m.item_id having count(*)=2";
$absentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$absent_meta_value ."')  group by m.item_id having count(*)=2";		
}
else{
$presentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$present_meta_value ."') or (m.field_id=".$attn_date_fieldid." and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=3";
$absentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$absent_meta_value ."') or (m.field_id=".$attn_date_fieldid." and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=3";		
}
 $totalavg=0;			
	
$present_count =count( $wpdb->get_col($presentquery));
$absent_count = count($wpdb->get_col($absentquery));
$stclass=$a['stclass'];
$avgmarksqry="";


if($a['fromdate']==-1 or $a['todate']==-1 ){
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 46 )and (m.field_id=408  and  m.meta_value='".$a['regdnum'] ."') ";		
}
else{
$avgmarksqry="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 46 )and (m.field_id=408  and  m.meta_value='".$a['regdnum'] ."')or  (m.field_id=412 and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=2";		
}

$entries =$wpdb->get_col($avgmarksqry);

foreach($entries as $entry){
			$totalavg+=FrmProEntriesController::get_field_value_shortcode(array('field_id' =>413 , 'entry' => $entry));
		}

$avg_marks="-";
$per='-';
if(count($entries)!=0)
$avg_marks=round($totalavg/count($entries),2);

$output='';
if( $present_count + $absent_count==0){
$output='<td class="centeralign">-</td> <td class="centeralign">-</td class="centeralign"><td>-</td>';
}
else{
$per=round($present_count*100.0/( $present_count + $absent_count));
$output='<td class="centeralign">'.$present_count.'</td> <td class="centeralign">'. $absent_count .'</td><td class="centeralign">'.$per.'</td>';
}
if($avg_marks=='-' or !isset($avg_marks))
$output.='<td class="centeralign">-</td>';
else
$output.='<td class="centeralign">'.$avg_marks.'</td>';

 $st_name= FrmProEntriesController::get_field_value_shortcode(array('field_id' => 263, 'entry' => $a['entryid1']));

if($sendsms=='Yes'){
$msg="Greetings from LFHS.";
$msg.="Your child:".$st_name. "'s attendance percentage  is ".$per.", ";
if( $avg_marks!=0 || $avg_marks !='-')
$msg.="and got ".$avg_marks."percentage of marks,";

if($a['fromdate']!='-1' && $a['todate']!='-1'){
$msg.="during the period ";
$fromdate=date_format($a['fromdate'],"d-m-y");
$todate=date_format($a['todate'],"d-m-y");
$msg.= $fromdate . " to " . $todate;
}
else
$msg.=" till today";

$username="littleflowerhighschool";
$password ="littleflowerhighscho";
//$number="9573811540";
 $sender="LFHSTS";
 $message=$msg;

$url="login.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($number)."&sender=".urlencode($sender)."&message=".urlencode($message)."&type=".urlencode('3'); 
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$curl_scraped_page = curl_exec($ch);

curl_close($ch); 

}
return $output;

}

add_shortcode('stconsolattendance','student_con_attendance');
function student_con_attendance($atts,$content=null)
{
	$a=shortcode_atts(array('regdnum'=>'-1','fromdate'=>'-1','todate'=>'-1','stclass'=>'-1','sendsms'=>'No','stname'=>'','phone'=>'','entryid1'=>''),$atts);
	
	$attn_details_formid=33;
$redgno_fieldid=294;
$attstaus_fieldid=302;
$attn_date_fieldid=296;
$present_meta_value="Present";
$absent_meta_value="Absent";
global $wpdb;
$sendsms=$a['sendsms'];
$stname=$a['stname'];
$number=$a['phone'];
$presentquery="";
$absentquery="";


if($a['fromdate']==-1 || $a['todate']==-1 ){
$presentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$present_meta_value ."')  group by m.item_id having count(*)=2";
$absentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$absent_meta_value ."')  group by m.item_id having count(*)=2";		
}
else{
$presentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$present_meta_value ."') or (m.field_id=".$attn_date_fieldid." and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=3";
$absentquery="select m.item_id from {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_items i ON (i.id=m.item_id) where (i.form_id= 33 )and (m.field_id=".$redgno_fieldid ." and  m.meta_value='".$a['regdnum'] ."')or ( m.field_id=".$attstaus_fieldid." and m.meta_value='".$absent_meta_value ."') or (m.field_id=".$attn_date_fieldid." and  m.meta_value>='".$a['fromdate'] ."' and m.meta_value<='".$a['todate'] ."') group by m.item_id having count(*)=3";		
}
 
$present_count =count( $wpdb->get_col($presentquery));
$absent_count = count($wpdb->get_col($absentquery));

$output='';
if( $present_count + $absent_count==0){
$output='<td class="centeralign" data-label="Presents:" >-</td> <td class="centeralign" data-label="Absents:">-</td><td class="centeralign" data-label="Attn.%:">-</td>';
}
else{
$per=round($present_count*100.0/( $present_count + $absent_count));
$output='<td class="centeralign" data-label="Presents:">'.$present_count.'</td> <td class="centeralign" data-label="Absents:">'. $absent_count .'</td><td class="centeralign" data-label="Attn. %:">'.$per.'</td>';
}


 //$st_name= FrmProEntriesController::get_field_value_shortcode(array('field_id' => 263, 'entry' => $a['entryid1']));
 //$number= FrmProEntriesController::get_field_value_shortcode(array('field_id' => 263, 'entry' => $a['entryid1']));

if($sendsms=='Yes'){
$msg="Greetings from LFHS.";
$msg.="Your child:".$stname. "'s attendance percentage  is ".$per.", ";


if($a['fromdate']!='-1' && $a['todate']!='-1'){
$msg.="during the period ";
//$fromdate=date_format($a['fromdate'],"d-m-y");
//$todate=date_format($a['todate'],"d-m-y");
//$msg.= $fromdate . " to " . $todate;
$msg.= $a['fromdate'] . " to " . $a['todate'];
}
else
$msg.=" till today";

$username="littleflowerhighschool";
$password ="littleflowerhighscho";
//$number="9573811540";
 $sender="LFHSTS";
 $message=$msg;

$url="login.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($number)."&sender=".urlencode($sender)."&message=".urlencode($message)."&type=".urlencode('3'); 
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$curl_scraped_page = curl_exec($ch);

curl_close($ch); 

}
return $output;

}

add_shortcode('check_to_display','check_to_display_fun');
function check_to_display_fun($atts,$content=null)
{
	$a=shortcode_atts(array('stclass'=>'-1'),$atts);
	if( $a['stclass'] !=-1 )
	return $content ;
}


add_filter('frm_validate_field_entry', 'redirect_cw_tw_marks_view', 10, 2);
function redirect_cw_tw_marks_view( $errors, $posted_field ) {
  $class_field_id = 379; // change 125 to the id of the first field
  $test_field_id = 380; // change 126 to the id of the second field
  $sendsms_id=795;
   if ( $posted_field->id == $class_field_id ){
   if($_POST['item_meta'][379]==NULL|| $_POST['item_meta'][380]==NULL){
     $class='';$test='';
   $errors['field379']="select both class and test";
  // header("Location: /cw-tw-marks-p");
  return $errors;
   //header("Location: /cw-tw-marks-p/?stclass=".$class."&test=".$test);
 
   }
   else{
   $class= $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][$class_field_id] ) );
   $test= $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 353, 'entry' => $_POST['item_meta'][$test_field_id] ) );
   $sms='';
 $sms=$_POST['item_meta'][795];
 //echo $sms;
 header("Location:/cw-tw-marks-p/?stclass=".$class."&test=".$test."&sendsms=".$sms);
   
 }
 
  }
  return $errors;
}

add_filter('frm_validate_field_entry', 'redirect_cw_tw_marks_view_mobile', 10, 2);
function redirect_cw_tw_marks_view_mobile( $errors, $posted_field ) {
  $class_field_id = 687; // change 125 to the id of the first field
  $test_field_id = 688; // change 126 to the id of the second field
   if ( $posted_field->id == $class_field_id ){
   if($_POST['item_meta'][687]==NULL|| $_POST['item_meta'][688]==NULL){
     $class='';$test='';
   $errors['field379']="select both class and test";
   //header("Location: /cw-tw-marks-p-mobile/?sendsms='here'");
  return $errors;
   //header("Location: /cw-tw-marks-p-mobile/?stclass=".$class."&test=".$test);
 
   }
   else{
   $class= $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][$class_field_id] ) );
   $test= $_POST['item_meta'][ $posted_field->id ] = FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 353, 'entry' => $_POST['item_meta'][$test_field_id] ) );
   $sendsms=$_POST['item_meta'][796];
  header("Location:/cw-tw-marks-p-mobile/?stclass=".$class."&test=".$test."&sendsms=".$sendsms);
 }
 
  }
  return $errors;
}



add_filter('frm_validate_field_entry', 'redirect_studentdetails_view', 10, 2);
function redirect_studentdetails_view( $errors, $posted_field ) {
  $class_field_id = 341; // change 125 to the id of the first field
 // $test_field_id = 380; // change 126 to the id of the second field
  $class=-1;
   if ( $posted_field->id == $class_field_id ){
   $class= FrmProEntriesController::get_field_value_shortcode( array( 'field_id' => 155, 'entry' => $_POST['item_meta'][$class_field_id] ) );
  
   header("Location:/student-details/?stclass1=".$class."&regdnum1=".$_POST['item_meta'][336]."&stname1=".$_POST['item_meta'][337]);
  }
  return $errors;
}


add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',get_stylesheet_directory_uri() . '/style.css',array('parent-style'));
}