<?php
// --- Archivo de Entrada Principal de la Aplicación ---

// Cargamos el archivo de arranque (bootstrap) que prepara todo el sistema.
require_once '../app/bootstrap.php';

// Creamos una instancia de la clase Core para que maneje la URL e inicie la aplicación.
$iniciar = new Core();
