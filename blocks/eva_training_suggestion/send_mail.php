<?php
require_once('../../config.php');
global $CFG, $DB, $USER;

require_once($CFG->libdir . '/moodlelib.php');

$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] : 0;
$pageid = isset($_REQUEST['pageid']) ? $_REQUEST['pageid'] : 0;
$ts = isset($_REQUEST['ts']) ? $_REQUEST['ts'] : 0;
$eva_slc_1 = isset($_REQUEST['eva_slc_1']) ? $_REQUEST['eva_slc_1'] : 0;
$eva_slc_2 = isset($_REQUEST['eva_slc_2']) ? $_REQUEST['eva_slc_2'] : 0;
$eva_txtarea_1 = isset($_REQUEST['eva_txtarea_1']) ? $_REQUEST['eva_txtarea_1'] : "";
$eva_txtarea_2 = isset($_REQUEST['eva_txtarea_2']) ? $_REQUEST['eva_txtarea_2'] : "";
$slc_priority_area_legal = isset($_REQUEST['slc_priority_area_legal']) ? $_REQUEST['slc_priority_area_legal'] : "";
$slc_technical_legal = isset($_REQUEST['slc_technical_legal']) ? $_REQUEST['slc_technical_legal'] : "";
$slc_modality = isset($_REQUEST['slc_modality']) ? $_REQUEST['slc_modality'] : "";
$eva_input_1 = isset($_REQUEST['eva_input_1']) ? $_REQUEST['eva_input_1'] : "";
$eva_input_2 = isset($_REQUEST['eva_input_2']) ? $_REQUEST['eva_input_2'] : "";
$eva_input_3 = isset($_REQUEST['eva_input_3']) ? $_REQUEST['eva_input_3'] : "";
$eva_input_4 = isset($_REQUEST['eva_input_4']) ? $_REQUEST['eva_input_4'] : "";
$eva_input_5 = isset($_REQUEST['eva_input_5']) ? $_REQUEST['eva_input_5'] : "";

date_default_timezone_set("America/Sao_Paulo");
$date = date("d/m/Y h:i:sa");
// Multiple recipients
$to = 'eva@agu.gov.br'; // note the comma

// Subject
$subject = '[eva] Sugestão de capacitação';

// Message
$message = '
<html>
<head>
<title>Sugestão de capacitação</title>
</head>
<body style="margin: 2em;">
<div class="row" style="width: 100%;">
    <div style="text-align: center;">
        <h1>Nova solicitação de necessidades de capacitação</h1>
    </div>
</div>
<div class="row" style="width: 100%;">
    <span><b>Data e hora:</b>&nbsp;'.$date.'</span>
</div>
<div class="row" style="width: 100%;">
    <span><b>Solicitante:</b>&nbsp;'.$USER->firstname.' '.$USER->lastname.'</span>
</div>
<div class="row" style="width: 100%;">
    <span><b>Órgão:</b>&nbsp;'.(($USER->institution == "") ? "-" : $USER->institution).'</span>
</div>
<div class="row" style="width: 100%;">
    <span><b>Link da solicitação:</b>&nbsp;<a href="'.$CFG->wwwroot.'/mod/page/view.php?id='.$pageid.'&id_ts='.$ts.'">'.$CFG->wwwroot.'/mod/page/view.php?id='.$pageid.'&id_ts='.$ts.'</a></span>
</div>
<br>
<div class="row" style="width: 100%;background: gray;">
    <h3>Dados da capacitação</h3>
</div>
';

$sql = "select no_organ from {eva_superior_organ} where id = " . $eva_slc_1;
$rs = $DB->get_record_sql($sql);
$message .= '
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Orgão de direção superior:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$rs->no_organ.'</div>
</div><br><br>';

$eixo_juridico = "";
$x = 0;
foreach($_REQUEST['slc_priority_area_legal'] as $row){
    if($x < 1){
        if($row == "0"){
            $eixo_juridico = "Não aplicável";
        }else if($row == "1"){
            $eixo_juridico = "Combate à corrupção e recuperação de ativos";
        }else if($row == "2"){
            $eixo_juridico = "Judicialização da saúde pública";
        }else if($row == "3"){
            $eixo_juridico = "Mecanismos de resolução de controvérsias e disputas em organizações internacionais";
        }
    }else{
        if($row == "0"){
            $eixo_juridico .= ", Não aplicável";
        }else if($row == "1"){
            $eixo_juridico .= ", Combate à corrupção e recuperação de ativos";
        }else if($row == "2"){
            $eixo_juridico .= ", Judicialização da saúde pública";
        }else if($row == "3"){
            $eixo_juridico .= ", Mecanismos de resolução de controvérsias e disputas em organizações internacionais";
        }
    }
    $x++;
}
$message .= '
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Vinculação com área prioritária para treinamento da AGU - eixo jurídico:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$eixo_juridico.'</div>
</div><br><br><br>';

$tecnico_juridico = "";
$x = 0;
foreach($_REQUEST['slc_technical_legal'] as $row){
    if($x < 1){
        if($row == "0"){
            $tecnico_juridico = "Não aplicável";
        }else if($row == "38"){
            $tecnico_juridico = "Gestão de competências";
        }else if($row == "39"){
            $tecnico_juridico = "Educação corporativa";
        }else if($row == "40"){
            $tecnico_juridico = "Designer industrial";
        }else if($row == "41"){
            $tecnico_juridico = "Produção e edição de vídeo";
        }
    }else{
        if($row == "0"){
            $tecnico_juridico .= ", Não aplicável";
        }else if($row == "38"){
            $tecnico_juridico .= ", Gestão de competências";
        }else if($row == "39"){
            $tecnico_juridico .= ", Educação corporativa";
        }else if($row == "40"){
            $tecnico_juridico .= ", Designer industrial";
        }else if($row == "41"){
            $tecnico_juridico .= ", Produção e edição de vídeo";
        }
    }
    $x++;
}
$message .= '
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Vinculação com área prioritária para formação da AGU - eixo de gestão técnico-jurídico:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$tecnico_juridico.'</div>
</div><br><br><br>';

$modalidade = "";
$x = 0;
foreach($_REQUEST['slc_modality'] as $row){
    if($x < 1){
        if($row == "1"){
            $modalidade = "Presencial";
        }else if($row == "2"){
            $modalidade = "Transmissão ao vivo interna (teams)";
        }else if($row == "3"){
            $modalidade = "Sala de estudo virtual (moodle)";
        }else if($row == "4"){
            $modalidade = "Ciclo permanente de ações de treinamento a distância";
        }
    }else{
        if($row == "1"){
            $modalidade .= ", Presencial";
        }else if($row == "2"){
            $modalidade .= ", Transmissão ao vivo interna (teams)";
        }else if($row == "3"){
            $modalidade .= ", Sala de estudo virtual (moodle)";
        }else if($row == "4"){
            $modalidade .= ", Ciclo permanente de ações de treinamento a distância";
        }
    }
    $x++;
}
$message .= '
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Modalidade:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$modalidade.'</div>
</div><br><br>';

$message .= '
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Tema do treino:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$eva_txtarea_1.'</div>
</div><br><br>
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>O desenvolvimento precisa ser atendido com o treinamento solicitado:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$eva_txtarea_2.'</div>
</div><br><br>
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Público alvo:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$eva_input_1.'</div>
</div><br><br>
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Número de participantes:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$eva_input_2.'</div>
</div><br><br>
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Tranvesalidade:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.(($eva_slc_2 == "1") ? "Apenas para este órgão / unidade" : "Atende a todos").'</div>
</div><br><br>
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Carga de trabalho:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$eva_input_3.'</div>
</div><br><br>
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Instituição/instrutor:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$eva_input_4.'</div>
</div><br><br>
<div class="row" style="width: 100%">
    <div style="width: 30%;float: left;"><b>Valor estimado:</b></div>
    <div style="width: 70%;float: right;">&nbsp;'.$eva_input_5.'</div>
</div><br><br>
';
$message .= '
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=iso-8859-1';

// Additional headers
$headers[] = 'To: eva@agu.gov.br';
$headers[] = 'From: '.$USER->email;

//$m = mail($to, $subject, $message, implode("\r\n", $headers));
$from = $USER->email;

//mail($to, $subject, $message, implode("\r\n", $headers));

require_once($CFG->libdir . '/phpmailer/moodle_phpmailer.php');
$mail = new moodle_phpmailer();

$mail->isMail();
$mail->Subject = $subject;
$mail->isHTML(true);
$mail->Body = $message;
$mail->From = $USER->email;
$mail->FromName = $USER->firstname . ' ' . $USER->lastname;
$mail->addAddress($to);
if ($bccself) {
    $mail->addBCC($from->email);
}
if ($attachment) {
    $mail->addAttachment($attachment);
}

if (!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    exit;
}