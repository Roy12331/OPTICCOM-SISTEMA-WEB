<?php
class Usuarios extends Controlador {
    private $usuarioModelo;
    private $clienteModelo;

    public function __construct(){
        if (!isLoggedIn()) {
            flash('mensaje_error', 'Debe iniciar sesión para acceder.', 'alert alert-warning');
            header('location: ' . RUTA_URL . '/usuarios/login');
            exit();
        }
        $this->usuarioModelo = $this->modelo('Usuario');
        $this->clienteModelo = $this->modelo('Cliente');
    }

    public function index(){
        $usuarios_admin = $this->usuarioModelo->obtenerUsuarios() ?? [];
        $clientes = $this->clienteModelo->obtenerClientes() ?? [];
        $datos = [
            'titulo' => 'Gestión de Accesos y Usuarios',
            'usuarios_admin' => $usuarios_admin,
            'clientes' => $clientes
        ];
        $this->vista('usuarios/inicio', $datos);
    }

    public function agregar(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $datos = [
                'titulo' => 'Agregar Usuario Administrativo',
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'rol' => trim($_POST['rol']),
                'password' => trim($_POST['password']),
                'confirmar_password' => trim($_POST['confirmar_password']), // Nombre correcto del campo
                'nombre_error' => '', 'email_error' => '', 'rol_error' => '', 'password_error' => ''
            ];

            if (empty($datos['nombre'])) { $datos['nombre_error'] = 'Nombre obligatorio.'; }
            if (empty($datos['email'])) { $datos['email_error'] = 'Email obligatorio.'; }
            elseif ($this->usuarioModelo->obtenerUsuarioPorEmail($datos['email'])) { // Función que sí existe
                $datos['email_error'] = 'Email ya registrado.';
            }
            if (empty($datos['rol'])) { $datos['rol_error'] = 'Rol obligatorio.'; }
            if (empty($datos['password'])) { $datos['password_error'] = 'Contraseña obligatoria.'; }
            elseif (strlen($datos['password']) < 6) { $datos['password_error'] = 'Mínimo 6 caracteres.'; }
            if ($datos['password'] != $datos['confirmar_password']) { $datos['password_error'] = 'Contraseñas no coinciden.'; }

            if (empty($datos['nombre_error']) && empty($datos['email_error']) && empty($datos['rol_error']) && empty($datos['password_error'])) {
                $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
                if ($this->usuarioModelo->agregarUsuario($datos)) {
                    flash('usuario_mensaje', 'Usuario administrativo agregado.');
                    header('location: ' . RUTA_URL . '/usuarios');
                    exit();
                } else { /* error al guardar */ }
            } else {
                $this->vista('usuarios/agregar', $datos);
            }
        } else {
            $datos = [
                'titulo' => 'Agregar Usuario Administrativo',
                'nombre' => '', 'email' => '', 'rol' => '', 'password' => '', 'confirmar_password' => '',
                'nombre_error' => '', 'email_error' => '', 'rol_error' => '', 'password_error' => ''
            ];
            $this->vista('usuarios/agregar', $datos);
        }
    }

    // --- ¡FUNCIÓN 'editar' CORREGIDA! ---
    public function editar($id_usuario){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $datos = [
                'id_usuario' => $id_usuario,
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'rol' => trim($_POST['rol']),
                'password' => trim($_POST['password']), // Contraseña nueva (opcional)
                'confirmar_password' => trim($_POST['confirmar_password']),
                'nombre_error' => '', 'email_error' => '', 'rol_error' => '', 'password_error' => '',
                'titulo' => 'Editar Usuario Administrativo'
            ];

            // Validar datos
            if (empty($datos['nombre'])) { $datos['nombre_error'] = 'Nombre obligatorio.'; }
            if (empty($datos['email'])) { $datos['email_error'] = 'Email obligatorio.'; }
            if (empty($datos['rol'])) { $datos['rol_error'] = 'Rol obligatorio.'; }

            // Validar contraseña SOLO SI se escribió una nueva
            if (!empty($datos['password'])) {
                if (strlen($datos['password']) < 6) {
                    $datos['password_error'] = 'Mínimo 6 caracteres.';
                }
                if ($datos['password'] != $datos['confirmar_password']) {
                    $datos['password_error'] = 'Las contraseñas no coinciden.';
                }
            } else {
                // Si la contraseña está vacía, NO la actualizamos y borramos errores
                $datos['password_error'] = '';
            }

            if (empty($datos['nombre_error']) && empty($datos['email_error']) && empty($datos['rol_error']) && empty($datos['password_error'])) {
                // Llama al modelo (que ya sabe manejar la contraseña vacía)
                if ($this->usuarioModelo->actualizarUsuario($datos)) {
                    flash('usuario_mensaje', 'Usuario administrativo actualizado.');
                    header('location: ' . RUTA_URL . '/usuarios'); // ¡Error de sintaxis corregido!
                    exit();
                } else {
                    flash('mensaje_error', 'No se pudo actualizar el usuario.', 'alert alert-danger');
                    $this->vista('usuarios/editar', $datos);
                }
            } else {
                // Si hay errores de validación, vuelve a mostrar el form con los errores
                $this->vista('usuarios/editar', $datos);
            }

        } else {
            // Método GET (Cargar datos del usuario para mostrar en el formulario)
            $usuario = $this->usuarioModelo->obtenerUsuarioPorId($id_usuario);
            if (!$usuario) {
                header('location: ' . RUTA_URL . '/usuarios');
                exit();
            }
            $datos = [
                'titulo' => 'Editar Usuario Administrativo',
                'id_usuario' => $id_usuario,
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'rol' => $usuario->rol, // <-- Pasa el ROL actual a la vista
                'password' => '', // Dejar vacío por seguridad
                'confirmar_password' => '',
                'nombre_error' => '', 'email_error' => '', 'rol_error' => '', 'password_error' => ''
            ];
            $this->vista('usuarios/editar', $datos);
        }
    }

    // --- ¡FUNCIÓN 'borrar' CORREGIDA! ---
    public function borrar($id_usuario){
        // Solo por POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Evitar que un usuario se borre a sí mismo
            if ($id_usuario == $_SESSION['id_usuario']) {
                flash('mensaje_error', 'No puedes borrar tu propio usuario.', 'alert alert-danger');
                header('location: ' . RUTA_URL . '/usuarios');
                exit();
            }
            
            if ($this->usuarioModelo->borrarUsuario($id_usuario)) {
                flash('usuario_mensaje', 'Usuario administrativo borrado.');
            } else {
                flash('mensaje_error', 'No se pudo borrar el usuario.', 'alert alert-danger');
            }
            header('location: ' . RUTA_URL . '/usuarios');
            exit();
        } else {
            // Si no es POST, redirige
            header('location: ' . RUTA_URL . '/usuarios');
            exit();
        }
    }

    // ... (login, logout - SIN CAMBIOS) ...
    public function login(){ /* ... (código existente) ... */ }
    public function logout(){ /* ... (código existente) ... */ }

    // ... (crearAccesoCliente - SIN CAMBIOS) ...
    public function crearAccesoCliente($id_cliente){ /* ... (código existente) ... */ }

} // Fin Controlador Usuarios