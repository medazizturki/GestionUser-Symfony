<?php

      if(isset($_POST['submit']))
{
   $secret = "6LfHIN8kAAAAAJzgMdPBgtEM99kOMxi_PLAGkIGK";
   $response = $_POST['g-recaptcha-response'];
   $remoteip = $_SERVER['REMOTE_ADDR'];  
   $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip";
   $data = file_get_contents($url);
   $row = json_decode($data, true);



   if($row['success'] == "true"){

    echo "<script> alert(' woow you are not a robet ');</script>";
   }else {
    echo "<script> alert('Ooops you are a robet !!!!');</script>";
   }

}


?>


