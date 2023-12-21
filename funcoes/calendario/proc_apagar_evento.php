<?php

include_once '../../get_dados.php';

include_once '../../conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$titulo = $_GET['titulo'];

$sql = "SELECT usuario_id from events where id = $id limit 1";
$query = mysqli_query($conexao, $sql);
$data = mysqli_fetch_assoc($query);    
// echo $data['usuario_id'];
if($userSession === $data['usuario_id']){

    $sql = "DELETE FROM events where id = $id";
    $query = mysqli_query($conexao, $sql);

    if($query){

        $sqllog = "INSERT INTO logs(mensagem, usuario, target) values ('Remoção de evento', '$userSession', '$titulo')";
        $query = mysqli_query($conexao, $sqllog);

        $_SESSION['msg'] = '<div class="alert alert-success" role="alert">O evento foi apagado com sucesso!</div>';
        header("Location: ../../calendario");
    }
}else{
    $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: Não foi você que cadastrou o evento!</div>';
    header("Location: ../../calendario");
}

?>