<?php
// Helper para manejar mensajes flash y sesiones de usuario

function flash($nombre = '', $mensaje = '', $clase = 'alert alert-success'){
    if(!empty($nombre)){
        if(!empty($mensaje) && empty($_SESSION[$nombre])){
            if(!empty($_SESSION[$nombre])){ unset($_SESSION[$nombre]); }
            if(!empty($_SESSION[$nombre. '_class'])){ unset($_SESSION[$nombre. '_class']); }
            $_SESSION[$nombre] = $mensaje;
            $_SESSION[$nombre. '_class'] = $clase;
        } elseif(empty($mensaje) && !empty($_SESSION[$nombre])){
            $clase = !empty($_SESSION[$nombre. '_class']) ? $_SESSION[$nombre. '_class'] : '';
            echo '<div class="'.$clase.'" id="msg-flash">'.$_SESSION[$nombre].'</div>';
            unset($_SESSION[$nombre]);
            unset($_SESSION[$nombre. '_class']);
        }
    }
}

function isLoggedIn(){
    if(isset($_SESSION['id_usuario'])){
        return true;
    } else {
        return false;
    }
}
// Este archivo NO LLEVA la etiqueta de cierre de PHP