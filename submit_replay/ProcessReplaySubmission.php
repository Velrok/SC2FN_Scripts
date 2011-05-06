<?php 

require_once("setup.php");



$error_occured = false;

/*
lets make sure files are uploaded corretly
*/
$replay01 = $files['replay01'];
if ($replay01->has_error()){
    addMessage("Replay 01 konnte nicht verarbeitete werden: ".$replay01->get_error());
    $error_occured = true;
}

$replay02 = $files['replay02'];
if ($replay02->has_error()){
    addMessage("Replay 02 konnte nicht verarbeitete werden: ".$replay02->get_error());
    $error_occured = true;
}


if(!$error_occured){

    /*
    lets get the player names
    */
    $playerA = $post->playerA;
    $playerB = $post->playerB;
    
    
    if($post->Game1Winner == 'Spieler A')
        $winnerGame01 = $playerA;
    else
        $winnerGame01 = $playerB;
        
    if($post->Game2Winner == 'Spieler A')
        $winnerGame02 = $playerA;
    else
        $winnerGame02 = $playerB;



    /*
    Sending the mail
    */
    $mail = new Zend_Mail();
    $mail->setBodyText("Neues Ergebnis: $playerA gegen $playerB\n
    Spiel01 ging an {$winnerGame01}\n
    Spiel02 ging an {$winnerGame02}\n
    \n
    $message_postfix\n\n");
    $mail->setFrom($from);
    $mail->addTo($recipient);
    $mail->setSubject("$subject_prefix$playerA vs $playerB");
    
    
    // attache replay 01
    $at = $mail->createAttachment( $replay01->get_data() );
    $at->type        = 'application/octet-stream';
    $at->disposition = Zend_Mime::DISPOSITION_INLINE;
    $at->encoding    = Zend_Mime::ENCODING_BASE64;
    $at->filename    = "{$replay_prefix}{$playerA}_vs_{$playerB}_Game01.SC2Replay";
    
    // attache replay 02
    $at = $mail->createAttachment( $replay02->get_data() );
    $at->type        = 'application/octet-stream';
    $at->disposition = Zend_Mime::DISPOSITION_INLINE;
    $at->encoding    = Zend_Mime::ENCODING_BASE64;
    $at->filename    = "{$replay_prefix}{$playerA}_vs_{$playerB}_Game02.SC2Replay";
    
    
    $mail->send($transport);
}


// redirect
if ($error_occured) {
    header('Location:./fail.phtml');  
} else {
    header('Location:./success.phtml');
}