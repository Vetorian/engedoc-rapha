<?php 
require '../../vendor/autoload.php';
require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';

date_default_timezone_set('America/Sao_Paulo');
use PHPMailer\PHPMailer\PHPMailer;

function saudacao(){
    $hr = date(" H ");
    if($hr >= 12 && $hr <18) {
    $resp = "Boa tarde!";}
    else if ($hr >= 0 && $hr <12 ){
    $resp = "Bom dia!";}
    else {
    $resp = "Boa noite!";}
    return $resp;
}


$conexao = mysqli_connect('localhost', 'raphael', 'v3t0r14n!', 'calendario');

$nome = $_POST['nome'];
$email = $_POST['email'];
$dados = $_POST['titulo'];
$formato = $_POST['formato'];

$sql = "SELECT * from events where title = '$dados' order by id desc limit 1";
$query = mysqli_query($conexao, $sql);

if($query){
    $array = mysqli_fetch_assoc($query);
    $id = $array['id'];
    $title = $array['title'];
    $start = $array['start'];
    $start_time = $array['start_time'];
    $end = $array['end'];
    $end_time = $array['end_time'];
    $convidados = $array['convidados'];
    $link = $array['link'];
}else{
    exit;
}

$ical_content = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//rapha-ssector7//
METHOD:REQUEST
X-WR-TIMEZONE:UTC
BEGIN:VEVENT
UID:rapha-ssector7
DTSTAMP:'. date('Ymd'). 'T' . date('His') . 'Z
ATTENDEE;RSVP=TRUE;CN=Vetorian;X-NUM-GUESTS=0:MAILTO:'.$email.'
DESCRIPTION:Location Name: Vetorian\nWebsite: vetorian.com
\nInstructor Name: Vetorian \nInstructor Email: vetorian@vetorian.com\nInstructor Bio: Vetorian\nVetorian
DTSTART:'.date('Ymd', strtotime($start)).'T'.date('His', strtotime($start_time. '+ 3 hours' )).'Z
DTEND:'.date('Ymd', strtotime($end)).'T'.date('His', strtotime($end_time. '+ 3 hours' )).'Z
LOCATION: '. $formato .'
ORGANIZER;CN=Vetorian ;SENT-BY=”MAILTO:vetorian@vetorian.com”
SEQUENCE:1504692112
STATUS:CONFIRMED
SUMMARY:'.$title.' 
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR';




$mail = new PHPMailer();

$mail->CharSet = "UTF-8";
$mail->SMTPDebug = 0;                                 // Enable verbose debug output
$mail->Debugoutput = 'html';
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host     = 'smtp.vetorian.com';                          // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'vetorian@vetorian.com';                         // SMTP username
$mail->Password = 'V3t0r14n!';                           // SMTP password
$mail->SMTPSecure = 'auto';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;
$mail->SMTPOptions = array(
'ssl' => array(
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true
)
);

$mail->setFrom('vetorian@vetorian.com');
// $mail->addReplyTo('vetorian@vetorian.com');
$mail->addAddress($email);
// $mail->addCC('william.oliveira@vetorian.com', 'Cópia');
// $mail->addCC('eduardo.marubayashi@vetorian.com', 'Cópia');
// $mail->addBCC('email@email.com.br', 'Cópia Oculta')
$mail->setLanguage('pt_br', '/optional/path/to/language/directory/');
$mail->isHTML(true);
$mail->ContentType = 'text/calendar'; 

$mail->Subject = 'Reunião marcada: '.$title.'';

$conexao = mysqli_connect('engedoc.com.br', 'engedoc', '3Ng3d0c!', 'engeline_erp');

$sql = "SELECT html from email_template where tipo = 5";
$query = mysqli_query($conexao, $sql);

$array = mysqli_fetch_assoc($query);
$body = $array['html'];
if($link != null){
    $link = "Link para a reunião: $link"; // caso exista link para reuniao
}
$arr = array("%nome%" => $nome,
            "%nomedoarquivo%" => '',
            "%data%" => "Ocorrido em: ".date("d/m/Y H:i:s",strtotime("now")),
            "%frase%" => "Este é um e-mail automático gerado pelo calendário do Engedoc",
            "%disciplina%" => '',
            "%titulo1%" =>  $title,
            "%titulo2%" =>  "Convidados: $convidados",
            "%descricao%" => 'E-mail destinado ao aviso de compromisso que você foi convidado para ás ' . date('d/m/Y', strtotime($start)) . " " .
            date('H:m', strtotime($start_time)) . " até: " . date('d/m/Y', strtotime($end)) . " ". date('H:m', strtotime($end_time))  . " formato: $formato <br> $link",
            "%linksistema%" => 'https://engedoc.com.br/calendario/updatedb.php?id='.$id.'&nome='.$nome.'',
            "%linkarquivo%" => "",
            "%saudacao%" => saudacao(),
            "%footer%" => "© 2023 - EngeDOC - Software de gestão de documentos desenvolvido por Engeline Engenharia"
            );


$mail->Body = strtr($body,$arr);
             
// $mail->AddEmbeddedImage('img/Logo-Vetorian-Horizontal-Color.png', 'logo_png');
$mail->AltBody = $ical_content;
$mail->Ical = $ical_content;
$mail->addStringAttachment($ical_content, 'ical.ics', 'base64', 'application/ics; name="ical.ics"'); //This seems to be important for Gmail

// $mail->AltBody = 'Para visualizar essa mensagem acesse http://site.com.br/mail';
// $mail->addAttachment('img/Logo-Vetorian-Horizontal-Color.png');


if(!$mail->send()) {
    echo 'Não foi possível enviar a mensagem.<br>';
    echo 'Erro: ' . $mail->ErrorInfo;
} else {
    echo 'Mensagem enviada.';
}

?>