<?php
$from = "lp@opora-rg.ru";
$to = "lp@opora-rg.ru";
$subject = "Заявка";
$message = "Имя - ".(isset($_POST['name'])?$_POST['name']."\n":"не введено\n"); //Текст письма
$message .= "Телефон - ".(isset($_POST['phone'])?$_POST['phone']."\n":"не введено\n"); //Текст письма
$message .= "Эл. почта - ".(isset($_POST['email'])?$_POST['email']."\n":"не введено\n"); //Текст письма
$message .= "Текст заказа - ".(isset($_POST['ordertext'])?$_POST['ordertext']."\n":"не введено\n"); //Текст письма

function sendMailAttachment($mailTo, $From, $subject_text, $message){

    $to = $mailTo;

    $EOL = "\r\n"; // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём
    $boundary     = "--".md5(uniqid(time()));  // любая строка, которой не будет ниже в потоке данных.

    $subject= '=?utf-8?B?' . base64_encode($subject_text) . '?=';

    $headers    = "MIME-Version: 1.0;$EOL";
    $headers   .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";
    $headers   .= "From: $From\nReply-To: $From\n";

    $multipart  = "--$boundary$EOL";
    $multipart .= "Content-Type: text/html; charset=utf-8$EOL";
    $multipart .= "Content-Transfer-Encoding: base64$EOL";
    $multipart .= $EOL; // раздел между заголовками и телом html-части
    $multipart .= chunk_split(base64_encode($message));
    if(!empty($_FILES["order"]['name'][0])) {
        foreach($_FILES["order"]["name"] as $key => $value){
            $filename = $_FILES["order"]["tmp_name"][$key];
            $file = fopen($filename, "rb");
            $data = fread($file,  filesize( $filename ) );
            fclose($file);
            $NameFile = $_FILES["order"]["name"][$key]; // в этой переменной надо сформировать имя файла (без всякого пути);
            $File = $data;
            $multipart .=  "$EOL--$boundary$EOL";
            $multipart .= "Content-Type: application/octet-stream; name=\"$NameFile\"$EOL";
            $multipart .= "Content-Transfer-Encoding: base64$EOL";
            $multipart .= "Content-Disposition: attachment; filename=\"$NameFile\"$EOL";
            $multipart .= $EOL; // раздел между заголовками и телом прикрепленного файла
            $multipart .= chunk_split(base64_encode($File));
        }
    }


    $multipart .= "$EOL--$boundary--$EOL";
    mail($to, $subject, $multipart, $headers);

}
sendMailAttachment($to,$from,$subject,$message);
header('Location: /?thanks=t');