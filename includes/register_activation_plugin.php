<?php
function wp_register_activation_check_wa(){
    $success = false;
    $wpurl = get_bloginfo('wpurl');
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.jogjaitclinic.com/v1/?verify=" . $wpurl . "&type=Whatsapp",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'User-Agent: Mozilla/5.0 (iPad; CPU OS 8_1_1 like Mac OS X; en-US) AppleWebKit/533.44.7 (KHTML, like Gecko) Version/3.0.5 Mobile/8B113 Safari/6533.44.7'
        ),
    ));
    $response = curl_exec($curl);
    $res = json_decode($response);
    curl_close($curl);
    if ($res->response_code === 200){
        $success = true;
    }

    if($res->response_code===500){
        dberrormessage($res->status);
    }
    return $success;
}
function wp_register_activation_register_wa(){

	$page = '
		<div class="wrap"> <h3>WhatsApp Rotator &gt; <a href="">Setup</a></h3> <h3>WhatsApp Rotator Setup</h3> <span>Silahkan isi data dibawah ini untuk menggunakan WhatsApp Rotator</span> <form  method="post"> <table style="margin-top: 20px" border="0"> <tbody> <tr> <td style="padding-right: 40px">Name :</td><td><input placeholder="Budi Santoso" name="fullname" required="" type="text"/></td></tr><tr> <td>Email :</td><td><input placeholder="budi@me.com" name="email" required="" type="email"/></td></tr><tr> <td>Whatsapp :</td><td><input name="phonenumber" id="phonenumber" required="" pattern="^\s*(?:\+?(\d{1,100}))?[-. (]*(\d{3})[-. )]*(\d{3})[-.]*(\d{4})(?: *x(\d+))?\s*$" placeholder="081111111" type="tel"/> </td></tr><tr> <td colspan="2" style="text-align:right"> <input name="submit" id="submit" type="submit" style="margin-top: 20px"/></td></tr></tbody> </table> </form></div>
			';


    if (isset($_POST['submit'])){
        $newuser = array();
        $wpurl = get_bloginfo('wpurl');
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $phonenumber = $_POST['phonenumber'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.jogjaitclinic.com/v1/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'hostname' => $wpurl,
                'fullname' => $fullname,
                'email' => $email,
                'phonenumber' => $phonenumber,
                'rtype' => "Whatsapp"
            ) ,
        ));

        $response = curl_exec($curl);
        $resjson = json_decode($response);

        if ($resjson->response_code === 200){
        	add_action('admin_menu', 'check_registration_status');
        	$url = admin_url('admin.php?page=contacts');
        	echo("<script>location.href = '".$url."'</script>");
        }
        else{
        	dberrormessage($resjson->status);
        	echo $page;
        }
    }else{
    	echo $page;
    }
   
}

function dberrormessage_wa($messge = NULL){
        echo '<div style="text-align:center;width:400px"><p style="background:red;color:white;padding:4px;border-radius:4px"> Tidak dapat terhubung ke Database Server. (' . $messge . ')</p></div>';
}

?>
