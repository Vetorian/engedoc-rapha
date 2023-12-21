<?php 
$conexao = mysqli_connect('localhost', 'raphael', 'v3t0r14n!', 'calendario');

function update($update, $id){
    mysqli_query(mysqli_connect('localhost', 'raphael', 'v3t0r14n!', 'calendario'), "UPDATE events SET convidados = '$update' where id = $id");
};

function geraLog($user, $evento){
    mysqli_query(mysqli_connect('localhost', 'raphael', 'v3t0r14n!', 'calendario'), 
    "INSERT INTO logs(log, event, user)
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
header("Location:https://engedoc.com.br/calendario/index?recusado=");