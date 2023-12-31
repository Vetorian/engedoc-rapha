<?php
include_once '../../get_dados.php';
include_once '../../conexao.php';
?>
<!DOCTYPE html>
<head>
<link rel="stylesheet" href="../../assets/css/iframe.css">
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<link href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' rel='stylesheet'>
<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="https://code.jquery.com/jquery-1.9.1.js"></script>
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>


<style>
    @import url('https://fonts.cdnfonts.com/css/montserrat');
    body{
        font-family: 'Montserrat', sans-serif;
        background-color: transparent;
    } 
</style>

<?php 
date_default_timezone_set('America/Sao_Paulo');
function diferencaEntreDatasSegundos($dataMenor, $dataMaior){
    $datetime1 = strtotime($dataMaior);
    $datetime2 = strtotime($dataMenor);
    $interval = ($datetime1 - $datetime2);
    return $interval;
}
function diferencaEntreDatasTempo($dataMenor, $dataMaior){
    $datetime1 = new DateTime($dataMenor);
    $datetime2 = new DateTime($dataMaior);
    $interval = $datetime1->diff($datetime2);
    return $interval->format('%a dias %H horas %i minutos ');
}
function segundosToTempo($segundos){
    return sprintf('%02d:%02d:%02d', ($segundos/3600),($segundos/60%60), $segundos%60);
}


$sql = "SELECT * from events where CONCAT(start, ' ', start_time) between NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY) order by CONCAT(start, ' ', start_time) ASC";
$query = mysqli_query($conexao,$sql);
?>
<div class="col-md-2">
    <div class="mt-2">
        <ul class="list list-inline">
<?php

$count = mysqli_num_rows($query);

while($array = mysqli_fetch_array($query, MYSQLI_ASSOC))
    {
        $titulo = $array['title'];
        $inicio = $array['start'];
        $iniciohora = $array['start_time'];
        $inicioConcat = $inicio . ' ' .$iniciohora;
        $dataConcat  = date("Y-m-d H:i:s", strtotime($inicioConcat));
        $formato = $array['formato'];
        $convidados = $array['convidados'];
        $datahoje = date('Y-m-d H:i:s');
        
        if($convidados == null){
            $convidados = 'Sem Convidados!';
        }

        echo '<div class="tooltip-9" title="'.$convidados.'">';
        echo '<li class="d-flex justify-content-between">';
            echo '<div class="d-flex flex-row align-items-center">'; 
            
                echo '<div class="ml-2">';
                    echo '<h6 class="mb-0">'.$titulo.'</h6>';
                    echo '<div class="d-flex flex-row mt-1 text-black-50 date-time">';
                    echo '<div><i class="fa fa-calendar-o"></i><span class="ml-2">' . date('d/m/y', strtotime($inicio)). '</span></div>';
                    echo '<div class="ml-3"><i class="fa fa-clock-o"></i><span class="ml-2"> Faltam: '. diferencaEntreDatasTempo($datahoje, $dataConcat) . '</span></div>';
                echo '</div>';
            echo'</div>';
        echo '</div>';
        echo '<div class="d-flex flex-row align-items-center">';
        echo '</li>';
        echo '</div>';
    }
    echo '</ul>';
    echo '</div>';
    ?>
</body>
<script>

    $(function() {
        $('.tooltip-9').tooltip({
            show: "slideDown", 
            open: function(event, ui) {
                ui.tooltip.hover(
                function () {
                    $(this).fadeTo("slow", 0.5);
                });
            }
        });
        });
    
    function pageScroll(count) {
            count = parseInt(count);
            if(count > 3){
                window.scrollBy(0,1);
                scrolldelay = setTimeout(pageScroll,100, count);
                setInterval('autoRefresh()', (count * 5)*1000);
            }else{
                setInterval('autoRefresh()', 1800000);
            }   
        }
    function autoRefresh() {
        window.location = window.location.href;
    }
    
    var count = "<?=$count?>";
    var count = parseInt(count);
    pageScroll(count);

</script>