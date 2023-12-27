<?php 


function update($update, $id){
    include_once '../../conexao.php';    
    mysqli_query($conexao, "UPDATE events SET convidados = '$update' where id = $id");
};

function geraLog($user, $evento){
    include_once '../../conexao.php';
    mysqli_query($conexao, 
    "INSERT INTO logs(mensagem, target, usuario)
    values ('Recusou o evento', '$evento',$user)");
}

$id = $_GET['id'];
$nome = $_GET['nome'];

$nome = str_replace('/', '', $nome);

$sql = "SELECT * from events where id = '$id' limit 1";
$query = mysqli_query($conexao, $sql);
$data = mysqli_fetch_assoc($query);

$string = str_replace($nome, '', $data['convidados']);
$string = str_replace(',,', ',', $string);
$evento = $data['title'];

update($string, $id);
geraLog($nome, $evento);
header("Location:127.0.0.1/engedoc_rapha/calendario?recusado=");