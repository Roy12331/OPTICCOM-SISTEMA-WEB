<?php
// Iniciar la sesión para toda la aplicación. Es lo PRIMERO que debe ocurrir.
session_start();

// Establecer la Zona Horaria por defecto para toda la aplicación PHP (Hora de Perú)
date_default_timezone_set('America/Lima');

// --- Archivo de Arranque de la Aplicación (Bootstrap) ---

// 1. Cargar helpers.
require_once 'helpers/session_helper.php';

// 2. Definir constantes.
// APP_ROOT: Ruta física a la carpeta 'app'
define('APP_ROOT', dirname(dirname(__FILE__))); // Correcto: __FILE__ apunta a bootstrap.php, dirname() sube un nivel, dirname() sube otro nivel a la raíz del proyecto.
// RUTA_URL: URL base del sitio web (Verifica http/https)
define('RUTA_URL', 'https://opticcomperu.com');

// 3. Cargar el núcleo del sistema, incluyendo la CONEXIÓN en la ruta correcta.
require_once '../config/Conexion.php'; // Correcto: Sube un nivel desde 'app' a la raíz, luego entra a 'config'
require_once 'librerias/Core.php';
require_once 'controladores/Controlador.php';

// 4. Cargar todos los modelos manualmente (Método a prueba de fallos).
require_once 'modelos/Usuario.php';
require_once 'modelos/Cliente.php'; // Carga el modelo Cliente actualizado
require_once 'modelos/Plan.php';
require_once 'modelos/Solicitud.php';
require_once 'modelos/Cotizacion.php';

// Este archivo NO LLEVA la etiqueta de cierre de PHP