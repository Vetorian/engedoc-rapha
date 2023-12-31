<?php 
require '../../vendor/autoload.php';
require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';

date_default_timezone_set('America/Sao_Paulo');
use PHPMailer\PHPMailer\PHPMailer;

if(getenv('REQUEST_METHOD') == 'POST') {
    
    require '../../conexao.php';
    
    $client_data = file_get_contents("php://input");
    file_put_contents('email_instalacao.txt', $client_data);
    $json = json_decode($client_data);
     
    $produto = $json->produto;
    $data = $json->data;

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

    foreach($json->user as $userid){
        $sql = "SELECT * from usuario where id = $userid";
        $query = mysqli_query($conexao, $sql);
        $result = mysqli_fetch_assoc($query);
        $cc_email = $result['email'];
        $nome = $result['nome'];
        $sobrenome = $result['sobrenome'];
        $mail->addCC($cc_email, "$nome $sobrenome");
   

        $mail->setFrom('vetorian@vetorian.com');
        $mail->addReplyTo('vetorian@vetorian.com');
        $mail->addAddress($cc_email);
        $mail->setLanguage('pt_br', '/optional/path/to/language/directory/');
        $mail->isHTML(true);
        $mail->Subject = 'Instalação Vetorian';

        $sql = "SELECT html from email_template where tipo = 'INSTALACAO_MARCADA'";
        $query = mysqli_query($conexao, $sql);

        $array = mysqli_fetch_assoc($query);
        $body = $array['html'];
        
        $arrayHtml = array(
            "%content%" => 'Data da instalação: ' . date('d/m/Y', strtotime($data)),
            "%content2%" => "Rastreador para ser configurado: <strong>$produto</strong>",
        );
        
        $mail->Body = strtr($body,$arrayHtml);
    }

    if(!$mail->send()) {
        echo 'Não foi possível enviar a mensagem.<br>';
        echo 'Erro: ' . $mail->ErrorInfo;
    } else {
        echo 'Mensagem enviada.';
    }

}
?>