<?php

require '../../get_dados.php';

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $return = array(
        'nivelSession' => $nivelSession,
        'userSession' => $userSession
    );
    
    echo json_encode($return);
}



if(isset($_GET['id'])){
    if($userSession == $_GET['id']){?>
    initKanban();
    <?php
    }else{ 
    if($nivelSession == 1 && $userSession == $_GET['id']){?>
        initKanban();<?php
    }else if($nivelSession == 1 && $userSession != $_GET['id']){?>
        initKanban();
        alertaAdm();<?php
    }else{?>
        window.location.replace('index?id=<?=$userSession?>');<?php
    }
    }
}else if(isset($_GET['cadastro']) && $_GET['cadastro'] == 'padrao'){?> 
    load_cadastro(); <?php 
}else if(isset($_GET['cadastro']) && $_GET['cadastro'] == 'sequencial'){?>
    load_cadastro_sequencial();<?php
}?>