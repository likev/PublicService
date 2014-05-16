<?php

function putFtp( $local_file , $remote_file)
{

	
	$ftp_server = '172.18.152.41';
	$ftp_user_name = 'pro-city';
	$ftp_user_pass = 'countybezz';
	$ftp_path = '/';

	// set up basic connection
	if(!($conn_id = ftp_connect($ftp_server, 21, 10)) ) return false;
	
	if(!ftp_login($conn_id, $ftp_user_name, $ftp_user_pass) ) return false;
	
	ftp_chdir($conn_id, $ftp_path);

	// upload a file
	$result = ftp_put($conn_id, $remote_file, $local_file, FTP_BINARY);

	// close the connection
	ftp_close($conn_id);
	
	return $result;

}
	
if (isset($_FILES['myFile'])) {
    // 测试:
    $result = putFtp($_FILES['myFile']['tmp_name'], $_POST['newname']);
	
	if($result) echo 'success';
	else echo 'fail';
    
	exit;
	
}
	
echo 'fail';//should never occur!
	
?>
