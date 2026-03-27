<?php
// ======================================================
// app/config/global.php
// - Soporta variables de entorno (recomendado) y mantiene
//   compatibilidad con constantes por defecto.
// ======================================================

function env_or_default($key, $default){
  $v = getenv($key);
  return ($v !== false && $v !== null && $v !== "") ? $v : $default;
}

// DB (usar variables de entorno en producción)
// Ejemplos:
//   DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD, DB_ENCODE
define("DB_HOST",     env_or_default("DB_HOST",     "localhost"));
define("DB_NAME",     env_or_default("DB_NAME",     "tmperu_db_app"));
define("DB_USERNAME", env_or_default("DB_USERNAME", "tmperu_user_app"));
define("DB_PASSWORD", env_or_default("DB_PASSWORD", "TMPERU_hPrLNYWANM4Nbwfk"));
define("DB_ENCODE",   env_or_default("DB_ENCODE",   "utf8"));

// Rutas (también admiten env)
define("RUTA",     env_or_default("APP_RUTA",     "https://app.tmperu.online"));
define("RUTASUNAT",env_or_default("APP_RUTASUNAT","https://app.tmperu.online"));

?>