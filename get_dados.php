<?php
session_start();
include_once 'conexao.php';

if ($_SESSION["usuario"] == "" || $_SESSION["usuario"] == null) {
    header("Location: http://192.168.0.122/engedoc_rapha/login.php");
}
$userSession = $_SESSION["usuario"];
$sql = "SELECT * FROM usuario WHERE id = $userSession  AND status = 1";
$retorno = mysqli_query($conexao, $sql);
$array = mysqli_fetch_array($retorno);
$senhaSession = $array['password'];
$emailSession = $array['email'];
$loginSession = $array['login'];
$nivelSession = $array['nivel'];
$nomeSession = $array['nome'];
$sobrenomeSession = $array['sobrenome'];
$fotoSession = $array['img'];

?>