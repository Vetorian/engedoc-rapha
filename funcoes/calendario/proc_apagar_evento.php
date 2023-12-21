<?php

include_once 'validacao.php';

include_once 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$titulo = $_GET['titulo'];

$sql = "SELECT usuario_id from events where id = $id limit 1";
$query = mysqli_query($conexao, $sql);
$data = mysqli_fetch_assoc($query);    

if($usuario === $data['usuario_id']){

    $sql = "DELETE FROM events where id = $id";
    $query = mysqli_query($conexao, $sql);

    if($query){

        $sqllog = "INSERT INTO logs(log, event, user) values ('Remoção de evento', '$titulo', $usuario)";
        $query = mysqli_query($conexao, $sqllog);

        $_SESSION['msg'] = '<div class="alert alert-success" role="alert">O evento foi apagado com sucesso!</div>';
        header("Location: ../index");
    }
}else{
    $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: Não foi você que cadastrou o evento!</div>';
    header("Location: ../index");
}

?>