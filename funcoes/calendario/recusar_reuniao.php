<?php 

function update($update, $id){
    require '../../conexao.php';
    $sql = "UPDATE events SET convidados = '$update' where id = $id";
    mysqli_query($conexao, $sql);
};

function geraLog($user, $evento){
    require '../../conexao.php';
    $sql = "select * from usuario where nome = '$user'";
    $query = mysqli_query($conexao, $sql);
    $array = mysqli_fetch_array($query);
    $user_id = $array['id'];
    $sql = "INSERT INTO logs(mensagem, target, usuario) values ('Recusou o evento', '$evento', '$user_id')";
    mysqli_query($conexao, $sql);
}

require '../../conexao.php';

$id = $_GET['id'];
$nome = $_GET['nome'];

$nome = str_replace('/', '', $nome);
echo $nome . PHP_EOL;
$sql = "SELECT * from events where id = '$id' limit 1";
$query = mysqli_query($conexao, $sql);
$data = mysqli_fetch_assoc($query);


$string = str_replace($nome, '', $data['convidados']);
$string = str_replace(',,', ',', $string);
$evento = $data['title'];

update($string, $id);
geraLog($nome, $evento);
header("Location: http://127.0.0.1/engedoc_rapha/calendario?recusado=");