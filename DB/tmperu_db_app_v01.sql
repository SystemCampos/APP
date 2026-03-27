-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 27, 2026 at 10:42 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tmperu_db_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `activos_depreciacion`
--

CREATE TABLE `activos_depreciacion` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idactivo` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `adquisicion` decimal(18,2) NOT NULL,
  `mejora` decimal(18,2) NOT NULL,
  `retiro` decimal(18,2) NOT NULL,
  `otros` decimal(18,2) NOT NULL,
  `historico` decimal(18,2) NOT NULL,
  `inflacion` decimal(18,2) NOT NULL,
  `ajustado` decimal(18,2) NOT NULL,
  `dep_acumulado` decimal(18,2) NOT NULL,
  `dep_ejercicio` decimal(18,2) NOT NULL,
  `dep_retiros` decimal(18,2) NOT NULL,
  `dep_ajustes` decimal(18,2) NOT NULL,
  `dep_historico` decimal(18,2) NOT NULL,
  `dep_inflacion` decimal(18,2) NOT NULL,
  `dep_acumulada` decimal(18,2) NOT NULL,
  `meses` int(11) NOT NULL,
  `meses_restante` int(11) NOT NULL,
  `periodoant` varchar(10) NOT NULL,
  `obs` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activos_ingresos`
--

CREATE TABLE `activos_ingresos` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `situacion` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `tipobien` varchar(20) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `responsable` int(11) NOT NULL,
  `iddestino` int(11) NOT NULL,
  `pais_origen` varchar(10) NOT NULL,
  `ccostos` int(11) NOT NULL,
  `controlpresupuestal` varchar(50) NOT NULL,
  `tit` varchar(300) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `codigosunat` varchar(50) NOT NULL,
  `codigotipo` int(11) NOT NULL DEFAULT 1,
  `codigo_almacen` varchar(50) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `serie_producto` varchar(100) NOT NULL,
  `placa` varchar(100) NOT NULL,
  `color` varchar(100) NOT NULL,
  `fecha` date NOT NULL,
  `fechauso` date NOT NULL,
  `fechabaja` date NOT NULL,
  `fecha_fabricacion` varchar(10) NOT NULL,
  `idproveedor` int(11) NOT NULL,
  `tipodoc` varchar(3) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `numero` text NOT NULL,
  `guia_remision` varchar(20) NOT NULL,
  `costo` decimal(18,3) NOT NULL,
  `cantidad` decimal(18,3) NOT NULL,
  `moneda` varchar(5) NOT NULL,
  `tcambio` decimal(18,3) NOT NULL,
  `tipoactivo` varchar(30) NOT NULL,
  `nautorizacion` varchar(20) NOT NULL,
  `mdepreciacion` varchar(11) NOT NULL,
  `procesar` int(11) NOT NULL,
  `tdepreciacion` varchar(11) NOT NULL,
  `pordepreciacion` decimal(18,2) NOT NULL,
  `residual` decimal(18,5) NOT NULL,
  `ctaactivo` varchar(10) NOT NULL DEFAULT '33111',
  `ctadepreciacion` varchar(10) NOT NULL DEFAULT '39111',
  `ctagastos` varchar(10) NOT NULL DEFAULT '68111',
  `calcular_depreciacion` varchar(5) NOT NULL DEFAULT 'SI',
  `lasingcontrato` varchar(100) NOT NULL,
  `lasingcuotas` varchar(20) NOT NULL,
  `lasingfechacontrato` date NOT NULL,
  `lasingfechainicio` date NOT NULL,
  `observaciones` text NOT NULL,
  `baja` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activos_variacion`
--

CREATE TABLE `activos_variacion` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idactivo` int(11) NOT NULL,
  `tipovariacion` int(11) NOT NULL,
  `idproveedor` int(11) NOT NULL,
  `tipodoc` varchar(5) NOT NULL,
  `serie` varchar(30) NOT NULL,
  `monto` decimal(18,2) NOT NULL,
  `cantidad` decimal(18,5) NOT NULL,
  `fecha` date NOT NULL,
  `ctacontable` varchar(20) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo`
--

CREATE TABLE `articulo` (
  `txtCOD_ARTICULO` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idcategoria` int(11) NOT NULL,
  `marca` int(11) NOT NULL,
  `linea` int(11) NOT NULL,
  `sublinea` int(11) NOT NULL,
  `subfamilia` int(11) NOT NULL,
  `medida` varchar(5) NOT NULL,
  `sanitario` varchar(200) NOT NULL,
  `principioactivo` varchar(100) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idproveedor` varchar(20) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `codigosunat` varchar(20) NOT NULL,
  `existencia` varchar(5) NOT NULL DEFAULT '02',
  `txtDESCRIPCION_ARTICULO` text NOT NULL,
  `stock` decimal(18,3) NOT NULL,
  `stockmin` decimal(18,3) NOT NULL,
  `stockmax` decimal(18,3) NOT NULL,
  `stock_reposicion` decimal(18,3) NOT NULL DEFAULT 0.000,
  `dias_reposicion` int(11) NOT NULL DEFAULT 0,
  `pide_reposicion` tinyint(1) NOT NULL DEFAULT 0,
  `precio_lista_fob` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_fob` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_cif` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_almacen` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `partida_arancelaria` varchar(50) NOT NULL DEFAULT '',
  `precio` decimal(18,2) NOT NULL,
  `preciooferta` decimal(18,2) NOT NULL,
  `moneda` varchar(10) NOT NULL,
  `precio_porcentaje` decimal(18,2) NOT NULL,
  `precio_compra` decimal(18,2) NOT NULL,
  `mayor` int(11) NOT NULL DEFAULT 3,
  `precio_porcentaje2` decimal(18,2) NOT NULL,
  `precio_mayor2` decimal(18,7) NOT NULL,
  `precio_porcentaje3` decimal(18,2) NOT NULL,
  `precio_mayor3` decimal(18,7) NOT NULL DEFAULT 0.0000000,
  `precio_mayor` decimal(18,2) NOT NULL,
  `exonerado_igv` int(11) NOT NULL,
  `idcatalogo_afectacion` int(10) UNSIGNED DEFAULT NULL,
  `permite_afectacion_manual` tinyint(1) NOT NULL DEFAULT 1,
  `requiere_afectacion_venta` tinyint(1) NOT NULL DEFAULT 1,
  `requiere_afectacion_compra` tinyint(1) NOT NULL DEFAULT 0,
  `metodo_salida_stock` enum('FIFO','LIFO','PROMEDIO','ESPECIFICO') NOT NULL DEFAULT 'FIFO',
  `comision` decimal(18,2) NOT NULL DEFAULT 0.00,
  `comisionm` int(11) NOT NULL DEFAULT 0,
  `comisionmp` decimal(18,2) NOT NULL DEFAULT 0.00,
  `bolsa` int(11) DEFAULT 0,
  `ctacompras` varchar(20) NOT NULL,
  `ctaventas` varchar(20) NOT NULL,
  `canje` varchar(5) NOT NULL DEFAULT 'NO',
  `canjepuntos` int(11) NOT NULL,
  `canjecobro` int(11) NOT NULL,
  `imagen` varchar(50) DEFAULT NULL,
  `estado` int(2) NOT NULL DEFAULT 1,
  `condicion` tinyint(1) NOT NULL DEFAULT 1,
  `maneja_lote` tinyint(1) NOT NULL DEFAULT 0,
  `maneja_serie` tinyint(1) NOT NULL DEFAULT 0,
  `maneja_garantia` tinyint(1) NOT NULL DEFAULT 0,
  `es_servicio` tinyint(1) NOT NULL DEFAULT 0,
  `maneja_stock` tinyint(1) NOT NULL DEFAULT 1,
  `se_compra` tinyint(1) NOT NULL DEFAULT 1,
  `se_vende` tinyint(1) NOT NULL DEFAULT 1,
  `se_almacena` tinyint(1) NOT NULL DEFAULT 1,
  `controla_vencimiento` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_fecha_ingreso` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_fecha_vencimiento` tinyint(1) NOT NULL DEFAULT 0,
  `permite_stock_negativo` tinyint(1) NOT NULL DEFAULT 0,
  `garantia_tipo` enum('NINGUNA','FABRICANTE','COMERCIAL','EXTENDIDA') NOT NULL DEFAULT 'NINGUNA',
  `garantia_meses` smallint(6) NOT NULL DEFAULT 0,
  `resaltado` int(11) NOT NULL,
  `fecha_alta` date DEFAULT NULL,
  `descripcion_adicional` text DEFAULT NULL,
  `modelo` varchar(100) NOT NULL DEFAULT '',
  `procedencia` varchar(100) NOT NULL DEFAULT '',
  `ubicacion_fisica` varchar(100) NOT NULL DEFAULT '',
  `unidad_compra` varchar(10) NOT NULL DEFAULT '',
  `unidad_venta` varchar(10) NOT NULL DEFAULT '',
  `unidad_empaque` varchar(10) NOT NULL DEFAULT '',
  `cantidad_empaque` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `peso` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `unidad_reporte` varchar(10) NOT NULL DEFAULT '',
  `factor_reporte` decimal(18,6) NOT NULL DEFAULT 0.000000
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo2`
--

CREATE TABLE `articulo2` (
  `txtCOD_ARTICULO` int(11) NOT NULL,
  `idcategoria` int(11) NOT NULL,
  `marca` int(11) NOT NULL,
  `medida` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `codigosunat` varchar(100) NOT NULL,
  `txtDESCRIPCION_ARTICULO` varchar(256) NOT NULL,
  `stock` int(11) NOT NULL,
  `precio` decimal(18,2) NOT NULL,
  `mayor` int(11) NOT NULL DEFAULT 200,
  `precio_mayor` decimal(18,2) NOT NULL,
  `exonerado_igv` int(11) NOT NULL,
  `imagen` varchar(50) DEFAULT NULL,
  `bolsa` int(11) NOT NULL,
  `condicion` tinyint(1) NOT NULL DEFAULT 1,
  `resaltado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_auditoria`
--

CREATE TABLE `articulo_auditoria` (
  `idauditoria` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `idcontexto` bigint(20) UNSIGNED DEFAULT NULL,
  `tabla_afectada` varchar(100) NOT NULL,
  `campo` varchar(100) NOT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `accion` enum('INSERT','UPDATE','DELETE','MIGRACION','SINCRONIZACION') NOT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(45) DEFAULT NULL,
  `origen` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_comision`
--

CREATE TABLE `articulo_comision` (
  `idarticulo_comision` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `idarticulo_presentacion` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_comision` enum('MONTO','PORCENTAJE') NOT NULL DEFAULT 'MONTO',
  `valor_comision` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `canal_venta` varchar(50) NOT NULL DEFAULT '',
  `fecha_desde` date DEFAULT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_contabilidad`
--

CREATE TABLE `articulo_contabilidad` (
  `idarticulo_contabilidad` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `cuenta_compra` varchar(20) NOT NULL DEFAULT '',
  `cuenta_venta` varchar(20) NOT NULL DEFAULT '',
  `cuenta_inventario` varchar(20) NOT NULL DEFAULT '',
  `cuenta_costo_venta` varchar(20) NOT NULL DEFAULT '',
  `cuenta_descuento` varchar(20) NOT NULL DEFAULT '',
  `cuenta_merma` varchar(20) NOT NULL DEFAULT '',
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_garantia`
--

CREATE TABLE `articulo_garantia` (
  `idarticulo_garantia` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL DEFAULT 0,
  `idarticulo` int(11) NOT NULL,
  `idarticulo_lote` bigint(20) UNSIGNED DEFAULT NULL,
  `idarticulo_serie` bigint(20) UNSIGNED DEFAULT NULL,
  `idventa` bigint(20) DEFAULT NULL,
  `idventa_detalle` bigint(20) DEFAULT NULL,
  `tipo_garantia` enum('FABRICANTE','COMERCIAL','EXTENDIDA') NOT NULL DEFAULT 'COMERCIAL',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado_garantia` enum('VIGENTE','VENCIDA','ANULADA','ATENDIDA') NOT NULL DEFAULT 'VIGENTE',
  `observacion` varchar(255) DEFAULT NULL,
  `idusuario_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idusuario_modificacion` int(11) DEFAULT NULL,
  `fecha_modificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_garantia_movimiento`
--

CREATE TABLE `articulo_garantia_movimiento` (
  `idarticulo_garantia_movimiento` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL DEFAULT 0,
  `idarticulo_garantia` bigint(20) UNSIGNED NOT NULL,
  `tipo_movimiento` enum('CREACION','AMPLIACION','ANULACION','ATENCION','VENCIMIENTO','REACTIVACION') NOT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `estado_anterior` varchar(30) DEFAULT NULL,
  `estado_nuevo` varchar(30) DEFAULT NULL,
  `origen_tabla` varchar(50) DEFAULT NULL,
  `origen_id` bigint(20) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_imagen`
--

CREATE TABLE `articulo_imagen` (
  `idarticulo_imagen` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `titulo` varchar(300) NOT NULL DEFAULT '',
  `descripcion` text DEFAULT NULL,
  `archivo_imagen` varchar(255) NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `idusuario_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idusuario_modificacion` int(11) DEFAULT NULL,
  `fecha_modificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_images`
--

CREATE TABLE `articulo_images` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `id_serv` varchar(11) NOT NULL,
  `tit` varchar(300) NOT NULL,
  `cont` text NOT NULL,
  `imag` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_lote`
--

CREATE TABLE `articulo_lote` (
  `idarticulo_lote` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `numero_lote` varchar(100) NOT NULL,
  `lote_proveedor` varchar(100) NOT NULL DEFAULT '',
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_fabricacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `stock_actual` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_unitario` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `estado_lote` enum('DISPONIBLE','AGOTADO','RESERVADO','BLOQUEADO','VENCIDO','ANULADO') NOT NULL DEFAULT 'DISPONIBLE',
  `ubicacion` varchar(100) NOT NULL DEFAULT '',
  `origen_tabla` varchar(50) DEFAULT NULL,
  `origen_id` bigint(20) DEFAULT NULL,
  `idusuario_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idusuario_modificacion` int(11) DEFAULT NULL,
  `fecha_modificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_lote_movimiento`
--

CREATE TABLE `articulo_lote_movimiento` (
  `idarticulo_lote_movimiento` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idarticulo_lote` bigint(20) UNSIGNED NOT NULL,
  `tipo_movimiento` enum('INGRESO','SALIDA','AJUSTE','TRANSFERENCIA','RESERVA','LIBERACION','ANULACION') NOT NULL,
  `origen_tabla` varchar(50) DEFAULT NULL,
  `origen_id` bigint(20) DEFAULT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `cantidad` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_unitario` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `stock_anterior` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `stock_nuevo` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `idusuario` int(11) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_perfil_logistico`
--

CREATE TABLE `articulo_perfil_logistico` (
  `idarticulo_perfil_logistico` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `peso_neto` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `peso_bruto` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `volumen` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `alto` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `ancho` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `largo` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `unidad_logistica` varchar(20) NOT NULL DEFAULT '',
  `temperatura_min` decimal(18,2) NOT NULL DEFAULT 0.00,
  `temperatura_max` decimal(18,2) NOT NULL DEFAULT 0.00,
  `material_peligroso` tinyint(1) NOT NULL DEFAULT 0,
  `codigo_onu` varchar(20) NOT NULL DEFAULT '',
  `clase_riesgo` varchar(20) NOT NULL DEFAULT '',
  `vida_util_dias` int(11) NOT NULL DEFAULT 0,
  `requiere_lote_proveedor` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_hoja_seguridad` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_precio_cliente`
--

CREATE TABLE `articulo_precio_cliente` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `precio` decimal(18,5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_presentacion`
--

CREATE TABLE `articulo_presentacion` (
  `idarticulo_presentacion` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL DEFAULT 0,
  `idarticulo` int(11) NOT NULL,
  `idunidad_medida` varchar(10) NOT NULL,
  `nombre_presentacion` varchar(100) NOT NULL DEFAULT '',
  `tipo_presentacion` enum('COMPRA','VENTA','EMPAQUE','REPORTE','OTRA') NOT NULL DEFAULT 'VENTA',
  `equivalencia` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `cantidad_contenida` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `precio_venta` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `precio_compra` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `codigo_barra` varchar(100) NOT NULL DEFAULT '',
  `predeterminado` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 1,
  `idusuario_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idusuario_modificacion` int(11) DEFAULT NULL,
  `fecha_modificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_proveedor`
--

CREATE TABLE `articulo_proveedor` (
  `idarticulo_proveedor` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `idproveedor` int(11) NOT NULL,
  `codigo_proveedor` varchar(100) NOT NULL DEFAULT '',
  `descripcion_proveedor` varchar(255) NOT NULL DEFAULT '',
  `moneda` varchar(10) NOT NULL DEFAULT 'PEN',
  `costo_compra` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_fob` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_cif` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_almacen` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `precio_lista` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `plazo_entrega_dias` int(11) NOT NULL DEFAULT 0,
  `proveedor_principal` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_ultima_compra` date DEFAULT NULL,
  `idusuario_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idusuario_modificacion` int(11) DEFAULT NULL,
  `fecha_modificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_receta`
--

CREATE TABLE `articulo_receta` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `idrelacionado` int(11) NOT NULL,
  `cantidad` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_receta_cabecera`
--

CREATE TABLE `articulo_receta_cabecera` (
  `idarticulo_receta` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `nombre_receta` varchar(100) NOT NULL DEFAULT '',
  `tipo_receta` enum('FORMULA','COMBO','KIT','PRODUCCION') NOT NULL DEFAULT 'FORMULA',
  `predeterminada` tinyint(1) NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_receta_detalle`
--

CREATE TABLE `articulo_receta_detalle` (
  `idarticulo_receta_detalle` bigint(20) UNSIGNED NOT NULL,
  `idarticulo_receta` bigint(20) UNSIGNED NOT NULL,
  `idarticulo_insumo` int(11) NOT NULL,
  `cantidad` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `unidad_medida` varchar(10) NOT NULL DEFAULT '',
  `merma_porcentaje` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `orden` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_serie`
--

CREATE TABLE `articulo_serie` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `iddestino` int(11) NOT NULL,
  `cod_articulo` varchar(100) NOT NULL,
  `idingreso` int(11) NOT NULL,
  `idingresodet` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `serie` varchar(100) NOT NULL,
  `lote` varchar(100) NOT NULL,
  `idproveedor` int(11) NOT NULL,
  `stock` decimal(18,5) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_vto` date NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_serie_movimiento`
--

CREATE TABLE `articulo_serie_movimiento` (
  `idarticulo_serie_movimiento` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idarticulo_serie` bigint(20) UNSIGNED NOT NULL,
  `tipo_movimiento` enum('INGRESO','SALIDA','AJUSTE','TRANSFERENCIA','RESERVA','LIBERACION','ANULACION') NOT NULL,
  `origen_tabla` varchar(50) DEFAULT NULL,
  `origen_id` bigint(20) DEFAULT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `estado_anterior` varchar(30) DEFAULT NULL,
  `estado_nuevo` varchar(30) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_serie_nueva`
--

CREATE TABLE `articulo_serie_nueva` (
  `idarticulo_serie` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `idarticulo_lote` bigint(20) UNSIGNED DEFAULT NULL,
  `numero_serie` varchar(100) NOT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `estado_serie` enum('DISPONIBLE','VENDIDA','RESERVADA','DEVUELTA','BLOQUEADA','ANULADA') NOT NULL DEFAULT 'DISPONIBLE',
  `ubicacion` varchar(100) NOT NULL DEFAULT '',
  `costo_unitario` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `origen_tabla` varchar(50) DEFAULT NULL,
  `origen_id` bigint(20) DEFAULT NULL,
  `idusuario_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idusuario_modificacion` int(11) DEFAULT NULL,
  `fecha_modificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_stock`
--

CREATE TABLE `articulo_stock` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `idarticulo_lote` bigint(20) UNSIGNED DEFAULT NULL,
  `idarticulo_serie` bigint(20) UNSIGNED DEFAULT NULL,
  `idingreso` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `stock` decimal(18,3) NOT NULL,
  `stock_reservado` decimal(18,3) NOT NULL DEFAULT 0.000,
  `stock_disponible` decimal(18,3) NOT NULL DEFAULT 0.000,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `precio_inicial` decimal(18,3) NOT NULL,
  `stock_inicial` decimal(18,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_unidad`
--

CREATE TABLE `articulo_unidad` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `medida` varchar(50) NOT NULL,
  `cti` int(11) NOT NULL DEFAULT 0,
  `ctimayor` int(11) NOT NULL DEFAULT 0,
  `precio` decimal(18,2) NOT NULL DEFAULT 0.00,
  `preciom` decimal(18,2) NOT NULL DEFAULT 0.00,
  `comision` decimal(18,2) NOT NULL DEFAULT 0.00,
  `comisionm` decimal(18,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articulo_web`
--

CREATE TABLE `articulo_web` (
  `idarticulo_web` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idarticulo` int(11) NOT NULL,
  `publicar_web` tinyint(1) NOT NULL DEFAULT 0,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `titulo_web` varchar(255) NOT NULL DEFAULT '',
  `descripcion_web` text DEFAULT NULL,
  `keywords_web` text DEFAULT NULL,
  `mostrar_precio` tinyint(1) NOT NULL DEFAULT 1,
  `mostrar_stock` tinyint(1) NOT NULL DEFAULT 1,
  `destacado_web` tinyint(1) NOT NULL DEFAULT 0,
  `orden_web` int(11) NOT NULL DEFAULT 0,
  `sincronizado_web` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_ultima_sincronizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cajas`
--

CREATE TABLE `cajas` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `saldoi` decimal(18,2) NOT NULL,
  `monto` decimal(18,2) NOT NULL,
  `salidas` decimal(18,2) NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `caja_tipopago`
--

CREATE TABLE `caja_tipopago` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `descripcion` varchar(300) NOT NULL,
  `pagoforma` varchar(10) NOT NULL,
  `pagomedio` varchar(10) NOT NULL,
  `cuentasoles` varchar(50) NOT NULL,
  `cuentadolares` varchar(50) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `tiposunat` varchar(20) NOT NULL,
  `icono` varchar(30) NOT NULL DEFAULT 'fa fa-plus',
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `caja_tipopago_persona`
--

CREATE TABLE `caja_tipopago_persona` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `id_pago` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `cuotas` int(11) NOT NULL,
  `dias` int(11) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `caja_ventapago`
--

CREATE TABLE `caja_ventapago` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL DEFAULT 1,
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `tipopago` varchar(20) NOT NULL DEFAULT 'CONTADO',
  `otrospagos` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `idtipo` int(11) NOT NULL,
  `serie` varchar(20) NOT NULL,
  `moneda` varchar(5) NOT NULL,
  `montosoles` decimal(18,3) NOT NULL,
  `montodolares` decimal(18,3) NOT NULL,
  `tipocambio` decimal(18,3) NOT NULL,
  `operacion` varchar(100) NOT NULL,
  `fecha` datetime NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `fechaoperacion` datetime NOT NULL,
  `comentarios` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cardex`
--

CREATE TABLE `cardex` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `cinicial` decimal(18,2) NOT NULL,
  `pinicial` decimal(18,2) NOT NULL,
  `tinicial` decimal(18,2) NOT NULL,
  `cfinal` decimal(18,2) NOT NULL,
  `pfinal` decimal(18,2) NOT NULL,
  `tfinal` decimal(18,2) NOT NULL,
  `comentario` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cardex_detalle`
--

CREATE TABLE `cardex_detalle` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `beta` int(11) NOT NULL DEFAULT 1,
  `idproducto` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `tipooperacion` int(11) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `iddocumento` int(11) NOT NULL,
  `idoperacion` int(11) NOT NULL,
  `fecha` datetime(6) NOT NULL,
  `tipo` varchar(5) NOT NULL,
  `serie` varchar(6) NOT NULL,
  `numero` varchar(12) NOT NULL,
  `operacion` varchar(5) NOT NULL,
  `cantidad` decimal(18,3) NOT NULL,
  `precio` decimal(18,3) NOT NULL,
  `total` decimal(18,3) NOT NULL,
  `cantidadf` decimal(18,3) NOT NULL,
  `preciof` decimal(10,0) NOT NULL,
  `totalf` decimal(18,3) NOT NULL,
  `extras` varchar(300) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cardex_procesos`
--

CREATE TABLE `cardex_procesos` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `proceso` varchar(20) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` int(11) NOT NULL,
  `cerrado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catalogo_afectaciones_sunat`
--

CREATE TABLE `catalogo_afectaciones_sunat` (
  `idcatalogo_afectacion` int(10) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `version_ubl` varchar(10) NOT NULL DEFAULT '2.1',
  `vigencia_desde` date NOT NULL,
  `vigencia_hasta` date DEFAULT NULL,
  `codigo_afectacion_igv` char(2) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `descripcion_corta` varchar(80) DEFAULT NULL,
  `grupo_afectacion` enum('GRAVADO','EXONERADO','INAFECTO','EXPORTACION','IVAP') NOT NULL,
  `es_onerosa` tinyint(1) NOT NULL DEFAULT 1,
  `es_gratuito` tinyint(1) NOT NULL DEFAULT 0,
  `es_retiro` tinyint(1) NOT NULL DEFAULT 0,
  `es_publicidad` tinyint(1) NOT NULL DEFAULT 0,
  `es_bonificacion` tinyint(1) NOT NULL DEFAULT 0,
  `es_exportacion` tinyint(1) NOT NULL DEFAULT 0,
  `usa_impuesto` tinyint(1) NOT NULL DEFAULT 1,
  `codigo_tributo` char(4) NOT NULL,
  `nombre_tributo` varchar(30) NOT NULL,
  `codigo_tipo_tributo` char(3) NOT NULL,
  `codigo_categoria_tributaria` char(1) NOT NULL,
  `porcentaje_impuesto` decimal(7,4) NOT NULL DEFAULT 0.0000,
  `codigo_tipo_precio` char(2) NOT NULL DEFAULT '01',
  `requiere_valor_referencial` tinyint(1) NOT NULL DEFAULT 0,
  `codigo_total_valor_venta` char(4) DEFAULT NULL,
  `requiere_total_1004` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_leyenda_1002` tinyint(1) NOT NULL DEFAULT 0,
  `codigo_leyenda` char(4) DEFAULT NULL,
  `texto_leyenda` varchar(255) DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `predeterminado` tinyint(1) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `observacion` varchar(255) DEFAULT NULL,
  `usuario_crea` varchar(50) DEFAULT NULL,
  `fecha_crea` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario_modifica` varchar(50) DEFAULT NULL,
  `fecha_modifica` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categoria`
--

CREATE TABLE `categoria` (
  `idcategoria` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idnivel` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(256) DEFAULT NULL,
  `ctaventas` varchar(4) NOT NULL,
  `condicion` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clases`
--

CREATE TABLE `clases` (
  `codigo` varchar(6) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `familia` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cliente_contacto`
--

CREATE TABLE `cliente_contacto` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `nombre` varchar(400) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `sunat` varchar(50) NOT NULL DEFAULT 'sunat',
  `ruc` varchar(12) NOT NULL,
  `razon_social` varchar(300) NOT NULL,
  `nombre_comercial` varchar(300) NOT NULL,
  `direccion` varchar(300) NOT NULL,
  `departamento` varchar(300) NOT NULL,
  `provincia` varchar(300) NOT NULL,
  `distrito` varchar(300) NOT NULL,
  `codpais` varchar(100) NOT NULL,
  `ubigeo` varchar(100) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `correo` varchar(300) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `firma` varchar(200) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `fechaini` date NOT NULL,
  `fechafin` date NOT NULL,
  `directo` varchar(5) NOT NULL DEFAULT 'SI',
  `articulo` varchar(10) NOT NULL DEFAULT 'GENERAL',
  `pie` text NOT NULL,
  `color` varchar(10) NOT NULL DEFAULT '#008080',
  `detraccion` varchar(50) NOT NULL,
  `precio_porcentaje` varchar(10) NOT NULL DEFAULT 'NO',
  `paquetes` varchar(20) NOT NULL,
  `igv` decimal(18,2) NOT NULL,
  `sistem_ruta` varchar(300) NOT NULL,
  `sistem_data` varchar(30) NOT NULL,
  `sistema_user` varchar(30) NOT NULL,
  `sistem_pass` varchar(30) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config_contabilidad`
--

CREATE TABLE `config_contabilidad` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `cps` varchar(50) NOT NULL,
  `cpd` varchar(50) NOT NULL,
  `igv` varchar(50) NOT NULL,
  `bolsa` varchar(50) NOT NULL,
  `venta` varchar(50) NOT NULL,
  `pago` varchar(50) NOT NULL,
  `origencobros` varchar(20) NOT NULL,
  `origenpagos` varchar(10) NOT NULL,
  `ccomprassoles` varchar(10) NOT NULL,
  `ccomprasdolares` varchar(10) NOT NULL,
  `ctadetraccion` varchar(10) NOT NULL DEFAULT '1042',
  `ctadetraccioncompras` varchar(50) NOT NULL DEFAULT '12123',
  `ctaretencion` varchar(10) NOT NULL DEFAULT '40114',
  `percepcion40` varchar(10) NOT NULL DEFAULT '40113',
  `percepcion12` varchar(10) NOT NULL DEFAULT '12411',
  `percepcioncompra40` varchar(12) NOT NULL DEFAULT '40113',
  `ctacuotascompras` varchar(50) NOT NULL DEFAULT '12124',
  `ctacuotasventas` varchar(50) NOT NULL DEFAULT '12125',
  `detraccioncompras` varchar(20) NOT NULL,
  `detraccionventas` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config_paquetes`
--

CREATE TABLE `config_paquetes` (
  `id` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `cont` text NOT NULL,
  `ciclo` text NOT NULL,
  `numeros` int(11) NOT NULL,
  `precio` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config_sistema`
--

CREATE TABLE `config_sistema` (
  `id` int(11) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `uit` decimal(18,2) NOT NULL,
  `uitexonerado` decimal(18,2) NOT NULL,
  `uit1` decimal(18,2) NOT NULL,
  `uit1porciento` decimal(18,2) NOT NULL,
  `uit2` decimal(18,2) NOT NULL,
  `uit2porciento` decimal(18,2) NOT NULL,
  `uit3` decimal(18,2) NOT NULL,
  `uit3porciento` decimal(18,2) NOT NULL,
  `uit4` decimal(18,2) NOT NULL,
  `uit4porciento` decimal(18,2) NOT NULL,
  `uit5` decimal(18,0) NOT NULL,
  `uit5porciento` decimal(18,2) NOT NULL,
  `monto5ta` decimal(18,2) NOT NULL,
  `onp` decimal(18,2) NOT NULL,
  `asignacionfamiliar` decimal(18,2) NOT NULL,
  `segurosalud` decimal(18,2) NOT NULL,
  `basico` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cuentas_bancarias`
--

CREATE TABLE `cuentas_bancarias` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nombre` varchar(400) NOT NULL,
  `banco` varchar(20) NOT NULL,
  `soles` varchar(50) NOT NULL,
  `dolares` varchar(50) NOT NULL,
  `cci_soles` varchar(50) NOT NULL,
  `cci_dolares` varchar(50) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cupones`
--

CREATE TABLE `cupones` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `descuento` decimal(18,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cupones_detalle`
--

CREATE TABLE `cupones_detalle` (
  `id` int(11) NOT NULL,
  `idcupon` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `serie` int(11) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `descuento` decimal(18,2) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departamentos`
--

CREATE TABLE `departamentos` (
  `id` int(11) NOT NULL,
  `nivel` varchar(2) NOT NULL,
  `id_nivel` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `ubigeo` varchar(50) NOT NULL,
  `precio` decimal(8,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detalle_ingreso`
--

CREATE TABLE `detalle_ingreso` (
  `iddetalle_ingreso` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idingreso` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `txtCOD_ARTICULO` varchar(20) NOT NULL,
  `descripcion` text NOT NULL,
  `txtCANTIDAD_ARTICULO` decimal(18,5) NOT NULL,
  `subtotal` decimal(18,5) NOT NULL DEFAULT 0.00000,
  `igv` decimal(18,5) NOT NULL DEFAULT 0.00000,
  `total` decimal(18,5) NOT NULL DEFAULT 0.00000,
  `detalle_gratuita` decimal(18,7) NOT NULL,
  `precio_compra` decimal(18,5) NOT NULL,
  `otrosgastos` decimal(18,7) NOT NULL,
  `precio_venta` decimal(18,3) NOT NULL,
  `precio_promedio` decimal(18,3) NOT NULL DEFAULT 0.000,
  `stock` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tipo` int(11) NOT NULL,
  `fecha_vto` date NOT NULL,
  `fecha` datetime NOT NULL,
  `cuentacompra` varchar(20) NOT NULL,
  `ccostos` varchar(50) NOT NULL,
  `controlpresupuestal` varchar(50) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Triggers `detalle_ingreso`
--
DELIMITER $$
CREATE TRIGGER `tr_updStockIngreso` AFTER INSERT ON `detalle_ingreso` FOR EACH ROW BEGIN
 UPDATE articulo SET stock = stock + NEW.txtCANTIDAD_ARTICULO
 WHERE articulo.txtCOD_ARTICULO = NEW.txtCOD_ARTICULO;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `iddetalle_venta` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `orden_item` int(11) NOT NULL DEFAULT 1,
  `tipoarticulo` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `codigoproducto` varchar(100) NOT NULL,
  `nombreproducto` text NOT NULL,
  `idpresentacion` int(11) NOT NULL,
  `unidadmedida` varchar(50) NOT NULL,
  `idcatalogo_afectacion` int(10) UNSIGNED DEFAULT NULL,
  `txtCANTIDAD_ARTICULO` decimal(18,2) NOT NULL,
  `cantidadp` decimal(18,2) NOT NULL,
  `stock` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tipo` int(11) NOT NULL DEFAULT 0,
  `idlote` int(11) NOT NULL,
  `idproveedor` int(11) NOT NULL,
  `iddestino` int(11) NOT NULL,
  `carga_util` int(11) NOT NULL,
  `cantidad_toneladas` decimal(18,2) NOT NULL,
  `placa` text NOT NULL,
  `moneda` varchar(20) DEFAULT NULL,
  `tipo_cambio` decimal(18,3) DEFAULT NULL,
  `preciocompra` decimal(18,3) NOT NULL DEFAULT 0.000,
  `precio` decimal(18,2) NOT NULL,
  `valor_unitario_bruto` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `base_imponible_bruta` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `igv_bruto` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `total_bruto_linea` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `descuento` decimal(18,2) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL,
  `igv` decimal(18,2) NOT NULL,
  `ICB` decimal(18,2) NOT NULL DEFAULT 0.00,
  `importe` decimal(18,2) NOT NULL,
  `exoneradod` decimal(18,2) NOT NULL,
  `inafectad` decimal(18,2) NOT NULL,
  `gratuitad` decimal(18,2) NOT NULL,
  `detracciond` decimal(18,5) NOT NULL,
  `comisiond` decimal(18,2) NOT NULL DEFAULT 0.00,
  `cod_afectacion_igv` varchar(2) DEFAULT NULL,
  `cod_tributo` varchar(4) DEFAULT NULL,
  `porc_igv` decimal(5,2) DEFAULT NULL,
  `es_gratuito` tinyint(1) NOT NULL DEFAULT 0,
  `es_bonificacion` tinyint(1) NOT NULL DEFAULT 0,
  `es_publicidad` tinyint(1) NOT NULL DEFAULT 0,
  `es_retiro` tinyint(1) NOT NULL DEFAULT 0,
  `afecta_total_1004` tinyint(1) NOT NULL DEFAULT 0,
  `valor_unitario_ref` decimal(18,6) DEFAULT NULL,
  `precio_unitario_ref` decimal(18,6) DEFAULT NULL,
  `base_imponible_ref` decimal(18,6) DEFAULT NULL,
  `igv_ref` decimal(18,6) DEFAULT NULL,
  `valor_total_ref` decimal(18,6) DEFAULT NULL,
  `version_ubl` varchar(10) NOT NULL DEFAULT '2.1',
  `codigo_tipo_precio` char(2) DEFAULT NULL,
  `valor_unitario_xml` decimal(18,6) DEFAULT NULL,
  `precio_unitario_xml` decimal(18,6) DEFAULT NULL,
  `base_imponible_xml` decimal(18,6) DEFAULT NULL,
  `monto_tributo_xml` decimal(18,6) DEFAULT NULL,
  `valor_venta_xml` decimal(18,6) DEFAULT NULL,
  `codigo_leyenda` char(4) DEFAULT NULL,
  `dto_item_monto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `dto_item_tipo` varchar(20) NOT NULL DEFAULT '',
  `dto_item_modo` enum('MONTO','PORCENTAJE') NOT NULL DEFAULT 'MONTO',
  `dto_item_valor` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `dto_item_afecta_base` tinyint(1) NOT NULL DEFAULT 1,
  `dto_item_afecta_igv` tinyint(1) NOT NULL DEFAULT 1,
  `dto_item_monto_base` decimal(18,2) NOT NULL DEFAULT 0.00,
  `dto_item_monto_igv` decimal(18,2) NOT NULL DEFAULT 0.00,
  `dto_global_prorrateado_monto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `dto_global_prorrateado_base` decimal(18,2) NOT NULL DEFAULT 0.00,
  `dto_global_prorrateado_igv` decimal(18,2) NOT NULL DEFAULT 0.00,
  `descuento_total_linea` decimal(18,2) NOT NULL DEFAULT 0.00,
  `descuento_total_base_linea` decimal(18,2) NOT NULL DEFAULT 0.00,
  `descuento_total_igv_linea` decimal(18,2) NOT NULL DEFAULT 0.00,
  `base_imponible_neta` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `igv_neto_linea` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `total_neto_linea` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `anticipo` int(11) NOT NULL,
  `doc_anticipo` varchar(50) NOT NULL,
  `fecha` datetime(2) NOT NULL,
  `idcaja` int(11) NOT NULL,
  `observacion_item` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detalle_venta2`
--

CREATE TABLE `detalle_venta2` (
  `iddetalle_venta` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `codigoproducto` varchar(100) NOT NULL,
  `nombreproducto` text NOT NULL,
  `txtCANTIDAD_ARTICULO` int(11) NOT NULL,
  `precio` decimal(18,2) NOT NULL,
  `descuento` decimal(18,2) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL,
  `igv` decimal(18,2) NOT NULL,
  `ICB` decimal(18,2) NOT NULL,
  `importe` decimal(18,2) NOT NULL,
  `tipo` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detracciones`
--

CREATE TABLE `detracciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `porcentaje` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `existencia`
--

CREATE TABLE `existencia` (
  `id` int(11) NOT NULL,
  `nivel` int(11) NOT NULL DEFAULT 0,
  `cod` varchar(5) NOT NULL,
  `tit` varchar(200) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `familias`
--

CREATE TABLE `familias` (
  `codigo` varchar(4) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `segmento` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gre`
--

CREATE TABLE `gre` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `public` varchar(100) NOT NULL,
  `secret` varchar(100) NOT NULL,
  `usuariosol` varchar(50) NOT NULL,
  `clavesol` varchar(50) NOT NULL,
  `mtcregistro` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guia_chofer`
--

CREATE TABLE `guia_chofer` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nombre` varchar(400) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `doctipo` varchar(9) NOT NULL,
  `docnumero` varchar(10) NOT NULL,
  `lisencia` varchar(15) NOT NULL,
  `certificado` varchar(100) NOT NULL,
  `direccion` varchar(400) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `correo` varchar(300) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guia_detalle`
--

CREATE TABLE `guia_detalle` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `produccion` int(11) NOT NULL DEFAULT 3,
  `idguia` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `codigoproducto` varchar(100) NOT NULL,
  `unidadmedida` varchar(50) NOT NULL,
  `nombreproducto` varchar(1000) NOT NULL,
  `cantidad` decimal(18,5) NOT NULL,
  `precio` decimal(18,5) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL,
  `igv` decimal(18,2) NOT NULL,
  `ICB` decimal(18,2) NOT NULL DEFAULT 0.00,
  `importe` decimal(18,2) NOT NULL,
  `exoneradod` decimal(18,2) NOT NULL,
  `gratuitad` decimal(18,2) NOT NULL,
  `tipo` int(11) NOT NULL,
  `idlote` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guia_guia`
--

CREATE TABLE `guia_guia` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `nivel` int(11) NOT NULL DEFAULT 0,
  `fecharelaciona` date DEFAULT NULL,
  `idusuario` int(11) NOT NULL DEFAULT 0,
  `iddoc_relacionado` int(11) NOT NULL DEFAULT 0,
  `idlocal` int(11) NOT NULL DEFAULT 0,
  `remitenteid` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `tipodoc` varchar(20) NOT NULL DEFAULT '0',
  `serie` varchar(7) NOT NULL DEFAULT '0',
  `numero` varchar(10) NOT NULL DEFAULT '0',
  `motivoid` varchar(10) NOT NULL DEFAULT '0',
  `motivo` varchar(500) NOT NULL DEFAULT '0',
  `fecha` datetime DEFAULT NULL,
  `fecha_transporte` datetime DEFAULT NULL,
  `sucursal` int(11) NOT NULL DEFAULT 0,
  `destino` int(11) NOT NULL DEFAULT 0,
  `tipo_transporteid` varchar(10) NOT NULL DEFAULT '0',
  `tipo_transporte` varchar(500) NOT NULL DEFAULT '0',
  `emptrans_id` int(11) NOT NULL DEFAULT 0,
  `idchofer` int(11) NOT NULL DEFAULT 0,
  `idchofer2` int(11) NOT NULL,
  `idvehiculo` int(11) NOT NULL DEFAULT 0,
  `peso` decimal(18,2) NOT NULL,
  `cajas` varchar(50) NOT NULL DEFAULT '0',
  `ncarga` varchar(100) NOT NULL DEFAULT '-',
  `cvehicular` varchar(20) NOT NULL,
  `placacarreta` varchar(20) NOT NULL,
  `observacion` varchar(300) NOT NULL DEFAULT '-',
  `docadicional` varchar(10) NOT NULL,
  `docadicionalnum` varchar(60) NOT NULL,
  `hash_cpe` varchar(200) NOT NULL DEFAULT '-',
  `hash_cdr` varchar(300) NOT NULL DEFAULT '-',
  `ticket` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `kardex` int(11) NOT NULL,
  `rutaqr` text NOT NULL,
  `transbordo` varchar(5) NOT NULL,
  `vehiculo_m1l` varchar(5) NOT NULL,
  `idventa` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guia_transportista`
--

CREATE TABLE `guia_transportista` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nombre` varchar(400) NOT NULL,
  `ruc` varchar(20) NOT NULL,
  `direccion` varchar(400) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guia_traslado`
--

CREATE TABLE `guia_traslado` (
  `id` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guia_vehiculo`
--

CREATE TABLE `guia_vehiculo` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `placa` varchar(50) NOT NULL,
  `equipo` varchar(20) NOT NULL,
  `anio` varchar(10) NOT NULL,
  `sector` varchar(20) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `tarjetacirculacion` varchar(30) NOT NULL,
  `autorizacionmtc` varchar(30) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `importaciones_detalle`
--

CREATE TABLE `importaciones_detalle` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `iddocumento` int(11) NOT NULL,
  `tipodocumento` varchar(10) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `moneda` varchar(10) NOT NULL,
  `unidadmedida` varchar(20) NOT NULL,
  `precio` decimal(18,7) NOT NULL,
  `cantidad` decimal(18,7) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL,
  `igv` decimal(18,2) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `tipoigv` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingreso`
--

CREATE TABLE `ingreso` (
  `idingreso` int(11) NOT NULL,
  `nivel` int(11) NOT NULL DEFAULT 0,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idproveedor` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `ccostos` int(11) NOT NULL,
  `controlpresupuestal` varchar(50) NOT NULL,
  `tipo_comprobante` varchar(20) NOT NULL,
  `serie_comprobante` varchar(7) DEFAULT NULL,
  `num_comprobante` varchar(12) NOT NULL,
  `tipopago` varchar(30) NOT NULL DEFAULT 'EFECTIVO',
  `mediopago` varchar(5) NOT NULL,
  `moneda` varchar(5) NOT NULL DEFAULT 'PEN',
  `fecha_hora` datetime NOT NULL,
  `fechaven` date NOT NULL,
  `impuesto` decimal(18,2) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `igv` decimal(18,2) NOT NULL DEFAULT 0.00,
  `iscporcentaje` decimal(18,2) NOT NULL,
  `isc` decimal(18,2) NOT NULL,
  `otrosimpuestos` decimal(18,2) NOT NULL,
  `total_compra` decimal(18,2) NOT NULL,
  `exonerado` decimal(18,2) NOT NULL,
  `inafecto` decimal(18,2) NOT NULL,
  `gratuito` decimal(18,2) NOT NULL,
  `detracciones` decimal(18,2) NOT NULL,
  `iddetraccion` int(11) NOT NULL,
  `retenciones` decimal(18,2) NOT NULL,
  `percepcion` decimal(18,2) NOT NULL,
  `otrosgastos` decimal(18,3) NOT NULL,
  `obs` text NOT NULL,
  `idcaja` int(11) NOT NULL,
  `guiaremision` varchar(50) NOT NULL,
  `estadosire` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingreso_aprobado`
--

CREATE TABLE `ingreso_aprobado` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idingreso` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `cargo` varchar(300) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingreso_importacion`
--

CREATE TABLE `ingreso_importacion` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idingreso` int(11) NOT NULL,
  `proveedor` int(11) NOT NULL,
  `tipodocumento` varchar(5) NOT NULL,
  `serie_numero` varchar(30) NOT NULL,
  `fecha` date NOT NULL,
  `motivo` varchar(300) NOT NULL,
  `codcontable` varchar(50) NOT NULL,
  `moneda` varchar(5) NOT NULL,
  `tcambio` decimal(18,3) NOT NULL,
  `subtotal` decimal(18,3) NOT NULL,
  `igv` decimal(18,3) NOT NULL,
  `monto` decimal(18,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingreso_otrosgastos`
--

CREATE TABLE `ingreso_otrosgastos` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `codcontable` varchar(50) NOT NULL,
  `ccostos` int(11) NOT NULL,
  `controlpresupuestal` int(11) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingreso_vale`
--

CREATE TABLE `ingreso_vale` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idingreso` int(11) NOT NULL,
  `serie` varchar(7) DEFAULT NULL,
  `numero` varchar(12) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `id` int(11) NOT NULL,
  `nivel` varchar(50) NOT NULL DEFAULT '',
  `idioma` varchar(5) NOT NULL DEFAULT '',
  `id_idioma` varchar(50) NOT NULL,
  `prioridad` int(11) NOT NULL,
  `tit` varchar(200) NOT NULL DEFAULT '',
  `cont` text NOT NULL,
  `imag` varchar(50) NOT NULL DEFAULT '',
  `tag` varchar(200) NOT NULL DEFAULT '',
  `precio` decimal(8,2) NOT NULL,
  `oferta` decimal(18,2) NOT NULL,
  `link` text NOT NULL,
  `fecha` date NOT NULL DEFAULT '2020-02-01'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paises`
--

CREATE TABLE `paises` (
  `id` int(11) NOT NULL,
  `iso` char(2) DEFAULT NULL,
  `nombre` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `percepcion`
--

CREATE TABLE `percepcion` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `tipodocumento` varchar(10) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `fecha` datetime NOT NULL,
  `regular` varchar(5) NOT NULL,
  `tasa` decimal(18,2) NOT NULL DEFAULT 2.00,
  `percibido` decimal(18,2) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `tipo_pago` varchar(20) NOT NULL DEFAULT 'CREDITO',
  `hash_cpe` varchar(200) NOT NULL,
  `hash_cdr` varchar(300) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `percepcion_det`
--

CREATE TABLE `percepcion_det` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idpercepcion` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `tipodocumento` varchar(20) NOT NULL,
  `serie` varchar(7) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `fecha` date NOT NULL,
  `moneda` varchar(5) NOT NULL,
  `importe` decimal(18,2) NOT NULL,
  `percepcion` decimal(18,2) NOT NULL,
  `neto` decimal(18,2) NOT NULL,
  `porcentaje` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permiso`
--

CREATE TABLE `permiso` (
  `idpermiso` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persona`
--

CREATE TABLE `persona` (
  `idpersona` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `vendedor` int(11) NOT NULL,
  `tipo_persona` varchar(20) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `descuentom` varchar(5) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `tipo_documento` varchar(20) DEFAULT NULL,
  `txtID_CLIENTE` varchar(20) DEFAULT NULL,
  `sector` int(11) NOT NULL,
  `direccion` text DEFAULT NULL,
  `pais` varchar(10) NOT NULL,
  `ciudad` varchar(200) NOT NULL,
  `lat` varchar(100) NOT NULL,
  `lon` varchar(100) NOT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `email` varchar(300) DEFAULT NULL,
  `email2` varchar(300) NOT NULL,
  `pass` varchar(30) NOT NULL,
  `txtRAZON_SOCIAL` varchar(500) DEFAULT NULL,
  `descuento` decimal(18,2) NOT NULL,
  `cci` varchar(300) NOT NULL,
  `puntos` int(11) NOT NULL,
  `creditolimite` decimal(18,2) NOT NULL,
  `credito` decimal(18,2) NOT NULL,
  `obs` text NOT NULL,
  `edad` varchar(2) NOT NULL,
  `venta_pago` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persona2`
--

CREATE TABLE `persona2` (
  `idpersona` int(11) NOT NULL,
  `tipo_persona` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_documento` varchar(20) DEFAULT NULL,
  `txtID_CLIENTE` varchar(20) DEFAULT NULL,
  `direccion` varchar(70) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `txtRAZON_SOCIAL` varchar(50) DEFAULT NULL,
  `descuento` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persona_puntos`
--

CREATE TABLE `persona_puntos` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `proceso` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `puntos` decimal(18,2) NOT NULL,
  `puntostotales` decimal(18,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planillas_ccostos`
--

CREATE TABLE `planillas_ccostos` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idempleado` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `porcentaje` decimal(18,2) NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planillas_configuracion`
--

CREATE TABLE `planillas_configuracion` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `cta_gastos` varchar(50) NOT NULL,
  `cta_recibos` varchar(50) NOT NULL,
  `cta_igv` varchar(20) NOT NULL,
  `cta_pagos` varchar(20) NOT NULL,
  `sol_user` varchar(50) NOT NULL,
  `sol_pass` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planillas_extras`
--

CREATE TABLE `planillas_extras` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idempleado` int(11) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `inicio` datetime NOT NULL,
  `fin` datetime NOT NULL,
  `tipocodigo` varchar(10) NOT NULL,
  `tiponombre` varchar(100) NOT NULL,
  `horas` varchar(5) NOT NULL,
  `minutos` varchar(5) NOT NULL,
  `monto` decimal(18,2) NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planillas_horarios`
--

CREATE TABLE `planillas_horarios` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `hora_inicio` time NOT NULL,
  `refrigerio` time NOT NULL,
  `refrigerio_horas` varchar(10) NOT NULL,
  `salida` time NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planillas_rh`
--

CREATE TABLE `planillas_rh` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idtrabajador` int(11) NOT NULL,
  `periodo` varchar(20) NOT NULL,
  `tiporuc` varchar(5) NOT NULL,
  `ruc` varchar(20) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `tipodoc` varchar(5) NOT NULL,
  `serie` varchar(30) NOT NULL,
  `moneda` varchar(10) NOT NULL,
  `tiporenta` varchar(5) NOT NULL,
  `monto` decimal(18,5) NOT NULL,
  `fechadoc` date NOT NULL,
  `fechapago` date NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planillas_rtps`
--

CREATE TABLE `planillas_rtps` (
  `codigo` varchar(10) NOT NULL,
  `subtablas` varchar(100) NOT NULL,
  `codigortps` varchar(50) NOT NULL,
  `descripcion` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planillas_tablas`
--

CREATE TABLE `planillas_tablas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `tabla` varchar(200) NOT NULL,
  `codregistro` varchar(50) NOT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `coddescripcion` varchar(20) DEFAULT NULL,
  `chefech` varchar(20) DEFAULT NULL,
  `vchbanco` varchar(20) DEFAULT NULL,
  `chcts` varchar(20) DEFAULT NULL,
  `debe` decimal(18,2) DEFAULT NULL,
  `haber` decimal(18,2) DEFAULT NULL,
  `numper` decimal(18,2) DEFAULT NULL,
  `numperprima` decimal(18,2) DEFAULT NULL,
  `chrucempresa` varchar(20) DEFAULT NULL,
  `chcodactividad` varchar(20) DEFAULT NULL,
  `chtipooperacion` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planillas_tareocab`
--

CREATE TABLE `planillas_tareocab` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idtrabajador` int(11) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `extras25` int(11) NOT NULL,
  `extras35` int(11) NOT NULL,
  `extras100` int(11) NOT NULL,
  `domingo_feriado` int(11) NOT NULL,
  `permiso_sindical` int(11) NOT NULL,
  `lic_gosehaber` int(11) NOT NULL,
  `lic_patergosehaber` int(11) NOT NULL,
  `lic_adopgosehaber` decimal(18,2) NOT NULL,
  `dmsub_empleador` decimal(18,2) NOT NULL,
  `dmsubsidio_essalud` decimal(18,2) NOT NULL,
  `dmsubsidio_maternidad` decimal(18,2) NOT NULL,
  `vacaciones` decimal(18,2) NOT NULL,
  `falta_nojustificada` decimal(18,2) NOT NULL,
  `sancion_disciplinaria` decimal(18,2) NOT NULL,
  `permiso_sgh` decimal(18,2) NOT NULL,
  `huelga_legal` decimal(18,2) NOT NULL,
  `huelga_ilegal` decimal(18,2) NOT NULL,
  `total_horaslaborados` int(11) NOT NULL,
  `total_diaslaborados` int(11) NOT NULL,
  `totalmes1` int(11) NOT NULL,
  `totalmes2` int(11) NOT NULL,
  `dias_laborados` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planillas_tareodet`
--

CREATE TABLE `planillas_tareodet` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idempleado` int(11) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `dias_laborados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planilla_empleados`
--

CREATE TABLE `planilla_empleados` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `ccostos` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `nombres` varchar(300) NOT NULL,
  `paterno` varchar(100) NOT NULL,
  `materno` varchar(100) NOT NULL,
  `tipodocumento` varchar(50) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `numero_ruc` varchar(20) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_baja` date NOT NULL,
  `nacimiento` date NOT NULL,
  `direccion` varchar(300) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `nacionalidad` varchar(20) NOT NULL,
  `sueldo` decimal(18,2) NOT NULL,
  `sueldo_banco` varchar(100) NOT NULL,
  `sueldo_moneda` varchar(10) NOT NULL,
  `sueldo_cuenta` text NOT NULL,
  `sueldo_cci` varchar(50) NOT NULL,
  `dias_trabajo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `horas_laborales` decimal(18,2) NOT NULL DEFAULT 8.00,
  `hora_ingreso` varchar(10) NOT NULL,
  `Hora_almuerzo` varchar(10) NOT NULL,
  `hora_talmuerzo` varchar(10) NOT NULL,
  `hora_salida` varchar(10) NOT NULL,
  `tipoestablec` varchar(10) NOT NULL,
  `tipotrabajador` varchar(10) NOT NULL,
  `situacion` varchar(10) NOT NULL,
  `cargo` varchar(50) NOT NULL,
  `pensionregimen` varchar(50) NOT NULL,
  `pensionregimentipo` varchar(30) NOT NULL,
  `aplicaprima` int(11) NOT NULL,
  `pensionfecha` date NOT NULL,
  `cuspp` varchar(50) NOT NULL,
  `estadocivil` varchar(10) NOT NULL,
  `familiarasignacion` varchar(10) NOT NULL,
  `sexo` varchar(10) NOT NULL,
  `tipocontrato` varchar(100) NOT NULL,
  `priodidadremuneracion` varchar(100) NOT NULL,
  `tipopago` text NOT NULL,
  `tmodalidadformativa` varchar(100) NOT NULL,
  `niveleducativo` varchar(50) NOT NULL,
  `ocupacion` varchar(50) NOT NULL,
  `prestacionsalud` varchar(50) NOT NULL,
  `tcomprobante` varchar(50) NOT NULL,
  `tsuspensionlaboral` varchar(50) NOT NULL,
  `motfinperiodo` varchar(50) NOT NULL,
  `trabajadorespecial` varchar(50) NOT NULL,
  `fechavac` date NOT NULL,
  `novac` varchar(50) NOT NULL,
  `essaludvida` varchar(100) NOT NULL,
  `saludvidamonto` decimal(18,2) NOT NULL,
  `discapacidad` varchar(5) NOT NULL,
  `jatipica` varchar(5) NOT NULL,
  `ingresoquinta` varchar(5) NOT NULL,
  `horarionocturno` varchar(5) NOT NULL,
  `sindicalizado` varchar(5) NOT NULL,
  `rta5taexon` varchar(5) NOT NULL,
  `rta5tamonto` decimal(18,2) NOT NULL,
  `afiliadoeps` varchar(5) NOT NULL,
  `jorntrabmax` varchar(5) NOT NULL,
  `sctressalud` varchar(100) NOT NULL,
  `sctrpension` varchar(100) NOT NULL,
  `ctscodbanco` varchar(50) NOT NULL,
  `ctscuenta` varchar(50) NOT NULL,
  `ctsnumerobanco` varchar(50) NOT NULL,
  `ctsmoneda` varchar(50) NOT NULL,
  `ctsdeposito` varchar(50) NOT NULL,
  `ctsliquidado` varchar(50) NOT NULL,
  `polizaseguro` varchar(10) NOT NULL,
  `polizamonto` decimal(18,2) NOT NULL,
  `remype` varchar(10) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planilla_mensual`
--

CREATE TABLE `planilla_mensual` (
  `id` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idempleado` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `cups` varchar(100) NOT NULL,
  `paterno` varchar(100) NOT NULL,
  `materno` varchar(100) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `familiarasignacion` varchar(10) NOT NULL,
  `sueldo` decimal(18,2) NOT NULL,
  `familiarmonto` decimal(18,2) NOT NULL,
  `otrospagos` decimal(18,2) NOT NULL,
  `remuneracion` decimal(18,2) NOT NULL,
  `gratificacion` decimal(18,2) NOT NULL,
  `bonificacion` decimal(18,2) NOT NULL,
  `aportetipo` varchar(50) NOT NULL,
  `aportemonto` decimal(18,2) NOT NULL,
  `afptipo` varchar(50) NOT NULL,
  `afpporcentaje` decimal(18,2) NOT NULL,
  `afpmonto` decimal(18,2) NOT NULL,
  `comisionra` decimal(18,2) NOT NULL,
  `primaseguro` decimal(18,2) NOT NULL,
  `rentan5ta` decimal(18,2) NOT NULL,
  `descuentos` decimal(18,2) NOT NULL,
  `saludpago` varchar(100) NOT NULL,
  `salud` decimal(18,2) NOT NULL,
  `saludvidamonto` decimal(18,2) NOT NULL,
  `cts` decimal(18,2) NOT NULL,
  `polizamonto` decimal(18,2) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `fecha` date NOT NULL,
  `obs` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planilla_pension`
--

CREATE TABLE `planilla_pension` (
  `id` int(11) NOT NULL,
  `periodo` varchar(20) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `fijo` decimal(18,2) NOT NULL,
  `csobre_flujo` decimal(18,2) NOT NULL,
  `mixtasobre_flujo` decimal(18,2) NOT NULL,
  `mixtasobre_saldo` decimal(18,2) NOT NULL,
  `prima` decimal(18,2) NOT NULL,
  `aporte` decimal(18,2) NOT NULL,
  `total` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_cuentas`
--

CREATE TABLE `plan_cuentas` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `cuenta` varchar(20) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `equivalente` varchar(20) NOT NULL,
  `equivalente_nombre` varchar(300) NOT NULL,
  `nivel` varchar(5) NOT NULL,
  `tipo` varchar(5) NOT NULL,
  `analitica` varchar(5) NOT NULL,
  `amarre_debe` varchar(200) NOT NULL,
  `amarre_haber` varchar(200) NOT NULL,
  `moneda` varchar(5) NOT NULL,
  `forbalances` varchar(20) NOT NULL,
  `ccostos` varchar(12) NOT NULL,
  `balcompra` varchar(5) NOT NULL,
  `cuentae` varchar(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `principio_activo`
--

CREATE TABLE `principio_activo` (
  `id` int(11) NOT NULL,
  `codigo` varchar(300) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `cobertura` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resumen`
--

CREATE TABLE `resumen` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `iddocumento` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `serie` varchar(100) NOT NULL,
  `numero` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL,
  `hash` varchar(300) NOT NULL,
  `hash_cdr` varchar(300) NOT NULL,
  `mensaje` varchar(300) NOT NULL,
  `ticket` varchar(200) NOT NULL,
  `fecha_documento` date NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `segmentos`
--

CREATE TABLE `segmentos` (
  `codigo` varchar(2) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

CREATE TABLE `series` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `serie` varchar(100) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `documento` varchar(100) NOT NULL,
  `numeroinicio` varchar(10) NOT NULL,
  `sector` int(11) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `serviciosproductos`
--

CREATE TABLE `serviciosproductos` (
  `codigo` varchar(8) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `clase` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sistema_descargas`
--

CREATE TABLE `sistema_descargas` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `data` varchar(100) NOT NULL,
  `fechaini` date NOT NULL,
  `fechafin` date NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sucursal`
--

CREATE TABLE `sucursal` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idnivel` int(11) NOT NULL,
  `idsunat` varchar(10) NOT NULL DEFAULT '0000',
  `sucursal` varchar(300) NOT NULL,
  `direccion` text NOT NULL,
  `telefono` varchar(200) NOT NULL,
  `ubigeo` varchar(100) NOT NULL,
  `exclusivo` varchar(5) NOT NULL,
  `exonerado` varchar(5) DEFAULT 'NO',
  `observacion` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_base_sistema`
--

CREATE TABLE `tbl_base_sistema` (
  `id` int(11) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `version` varchar(10) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `smtp` varchar(100) NOT NULL,
  `password` varchar(20) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_conciliacion`
--

CREATE TABLE `tbl_conciliacion` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `id_conciliador` int(11) NOT NULL,
  `id_almacenero` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `comentario` varchar(300) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_conciliacion_detalle`
--

CREATE TABLE `tbl_conciliacion_detalle` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL DEFAULT 1,
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idconciliacion` int(11) NOT NULL,
  `fecha` datetime(6) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `cantidad` decimal(18,7) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_mensajes`
--

CREATE TABLE `tbl_mensajes` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `mensaje` varchar(300) NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sire_det`
--

CREATE TABLE `tbl_sire_det` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nivel` int(11) NOT NULL DEFAULT 0,
  `idusuario` int(11) NOT NULL,
  `periodo` varchar(20) NOT NULL,
  `idproveedor` int(11) NOT NULL,
  `tipocomprobanteproveedor` varchar(2) NOT NULL,
  `documentoproveedor` varchar(20) NOT NULL,
  `razonsocialproveedor` varchar(400) NOT NULL,
  `ccostos` int(11) NOT NULL,
  `controlpresupuestal` varchar(50) NOT NULL,
  `tipo_comprobante` varchar(20) NOT NULL,
  `serie` varchar(7) DEFAULT NULL,
  `numero` varchar(12) NOT NULL,
  `tipopago` varchar(30) NOT NULL DEFAULT 'EFECTIVO',
  `mediopago` varchar(5) NOT NULL,
  `moneda` varchar(5) NOT NULL DEFAULT 'PEN',
  `fecha_hora` datetime NOT NULL,
  `fechaven` date NOT NULL,
  `impuesto` decimal(18,2) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `igv` decimal(18,2) NOT NULL DEFAULT 0.00,
  `iscporcentaje` decimal(18,2) NOT NULL,
  `isc` decimal(18,2) NOT NULL,
  `otrosimpuestos` decimal(18,2) NOT NULL,
  `total_compra` decimal(18,2) NOT NULL,
  `exonerado` decimal(18,2) NOT NULL,
  `inafecto` decimal(18,2) NOT NULL,
  `gratuito` decimal(18,2) NOT NULL,
  `detracciones` decimal(18,2) NOT NULL,
  `iddetraccion` int(11) NOT NULL,
  `retenciones` decimal(18,2) NOT NULL,
  `percepcion` decimal(18,2) NOT NULL,
  `otrosgastos` decimal(18,3) NOT NULL,
  `obs` text NOT NULL,
  `idcaja` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_cambio`
--

CREATE TABLE `tipo_cambio` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `tipo` varchar(10) NOT NULL,
  `simbolo` varchar(5) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `cambio` decimal(18,3) NOT NULL,
  `venta` decimal(18,3) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_articulo`
--

CREATE TABLE `tmp_articulo` (
  `id` int(11) NOT NULL,
  `idcontexto` bigint(20) UNSIGNED DEFAULT NULL,
  `idempresa` int(11) NOT NULL,
  `idcategoria` int(11) NOT NULL,
  `marca` int(11) NOT NULL,
  `linea` int(11) NOT NULL,
  `sublinea` int(11) NOT NULL,
  `subfamilia` int(11) NOT NULL,
  `medida` varchar(5) NOT NULL,
  `sanitario` varchar(200) NOT NULL,
  `principioactivo` varchar(100) NOT NULL DEFAULT '',
  `idlocal` int(11) NOT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `token_operacion` char(36) DEFAULT NULL,
  `idproveedor` varchar(20) NOT NULL DEFAULT '',
  `codigo` varchar(50) DEFAULT NULL,
  `codigosunat` varchar(20) NOT NULL,
  `existencia` varchar(5) NOT NULL DEFAULT '02',
  `txtDESCRIPCION_ARTICULO` text NOT NULL,
  `stock` decimal(18,3) NOT NULL DEFAULT 0.000,
  `stockmin` decimal(18,3) NOT NULL,
  `stockmax` decimal(18,3) NOT NULL,
  `stock_reposicion` decimal(18,3) NOT NULL DEFAULT 0.000,
  `dias_reposicion` int(11) NOT NULL DEFAULT 0,
  `pide_reposicion` tinyint(1) NOT NULL DEFAULT 0,
  `precio_lista_fob` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_fob` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_cif` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `costo_almacen` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `partida_arancelaria` varchar(50) NOT NULL DEFAULT '',
  `precio` decimal(18,2) NOT NULL,
  `preciooferta` decimal(18,2) NOT NULL DEFAULT 0.00,
  `moneda` varchar(10) NOT NULL DEFAULT 'PEN',
  `precio_porcentaje` decimal(18,2) NOT NULL DEFAULT 0.00,
  `precio_compra` decimal(18,2) NOT NULL,
  `mayor` int(11) NOT NULL DEFAULT 3,
  `precio_porcentaje2` decimal(18,2) NOT NULL DEFAULT 0.00,
  `precio_mayor2` decimal(18,7) NOT NULL DEFAULT 0.0000000,
  `precio_porcentaje3` decimal(18,2) NOT NULL DEFAULT 0.00,
  `precio_mayor3` decimal(18,7) NOT NULL DEFAULT 0.0000000,
  `precio_mayor` decimal(18,2) NOT NULL,
  `exonerado_igv` int(11) NOT NULL,
  `idcatalogo_afectacion` int(10) UNSIGNED DEFAULT NULL,
  `permite_afectacion_manual` tinyint(1) NOT NULL DEFAULT 1,
  `requiere_afectacion_venta` tinyint(1) NOT NULL DEFAULT 1,
  `requiere_afectacion_compra` tinyint(1) NOT NULL DEFAULT 0,
  `metodo_salida_stock` enum('FIFO','LIFO','PROMEDIO','ESPECIFICO') NOT NULL DEFAULT 'FIFO',
  `comision` decimal(18,2) NOT NULL DEFAULT 0.00,
  `comisionm` int(11) NOT NULL DEFAULT 0,
  `comisionmp` decimal(18,2) NOT NULL DEFAULT 0.00,
  `bolsa` int(11) NOT NULL DEFAULT 0,
  `ctacompras` varchar(20) NOT NULL,
  `ctaventas` varchar(20) NOT NULL,
  `canje` varchar(5) NOT NULL DEFAULT 'NO',
  `canjepuntos` int(11) NOT NULL DEFAULT 0,
  `canjecobro` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(50) DEFAULT NULL,
  `estado` int(2) NOT NULL DEFAULT 1,
  `estado_tmp` enum('BORRADOR','VALIDADO','CONFIRMADO','ANULADO') NOT NULL DEFAULT 'BORRADOR',
  `fecha_creacion_tmp` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion_tmp` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_alta` date DEFAULT NULL,
  `descripcion_adicional` text DEFAULT NULL,
  `modelo` varchar(100) NOT NULL DEFAULT '',
  `procedencia` varchar(100) NOT NULL DEFAULT '',
  `ubicacion_fisica` varchar(100) NOT NULL DEFAULT '',
  `unidad_compra` varchar(10) NOT NULL DEFAULT '',
  `unidad_venta` varchar(10) NOT NULL DEFAULT '',
  `unidad_empaque` varchar(10) NOT NULL DEFAULT '',
  `cantidad_empaque` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `peso` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `unidad_reporte` varchar(10) NOT NULL DEFAULT '',
  `factor_reporte` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `condicion` tinyint(1) NOT NULL DEFAULT 1,
  `maneja_lote` tinyint(1) NOT NULL DEFAULT 0,
  `maneja_serie` tinyint(1) NOT NULL DEFAULT 0,
  `maneja_garantia` tinyint(1) NOT NULL DEFAULT 0,
  `es_servicio` tinyint(1) NOT NULL DEFAULT 0,
  `maneja_stock` tinyint(1) NOT NULL DEFAULT 1,
  `se_compra` tinyint(1) NOT NULL DEFAULT 1,
  `se_vende` tinyint(1) NOT NULL DEFAULT 1,
  `se_almacena` tinyint(1) NOT NULL DEFAULT 1,
  `controla_vencimiento` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_fecha_ingreso` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_fecha_vencimiento` tinyint(1) NOT NULL DEFAULT 0,
  `permite_stock_negativo` tinyint(1) NOT NULL DEFAULT 0,
  `garantia_tipo` enum('NINGUNA','FABRICANTE','COMERCIAL','EXTENDIDA') NOT NULL DEFAULT 'NINGUNA',
  `garantia_meses` smallint(6) NOT NULL DEFAULT 0,
  `resaltado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_articuloimages`
--

CREATE TABLE `tmp_articuloimages` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `id_serv` varchar(11) NOT NULL,
  `tit` varchar(300) NOT NULL,
  `cont` text NOT NULL,
  `imag` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_articulounidad`
--

CREATE TABLE `tmp_articulounidad` (
  `id` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `medida` varchar(50) NOT NULL,
  `cti` int(11) NOT NULL DEFAULT 0,
  `ctimayor` int(11) NOT NULL DEFAULT 0,
  `precio` decimal(18,2) NOT NULL DEFAULT 0.00,
  `preciom` decimal(18,2) NOT NULL DEFAULT 0.00,
  `comision` decimal(18,2) NOT NULL DEFAULT 0.00,
  `comisionm` decimal(18,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_articulo_imagen`
--

CREATE TABLE `tmp_articulo_imagen` (
  `idtmp_imagen` bigint(20) UNSIGNED NOT NULL,
  `idcontexto` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL DEFAULT 0,
  `idusuario` int(11) NOT NULL,
  `idtmp_articulo` int(11) DEFAULT NULL,
  `titulo` varchar(300) NOT NULL DEFAULT '',
  `descripcion` text DEFAULT NULL,
  `archivo_imagen` varchar(255) NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 1,
  `estado_tmp` enum('BORRADOR','VALIDADO','CONFIRMADO','ANULADO') NOT NULL DEFAULT 'BORRADOR',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_articulo_presentacion`
--

CREATE TABLE `tmp_articulo_presentacion` (
  `idtmp_presentacion` bigint(20) UNSIGNED NOT NULL,
  `idcontexto` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL DEFAULT 0,
  `idusuario` int(11) NOT NULL,
  `idtmp_articulo` int(11) DEFAULT NULL,
  `idunidad_medida` varchar(10) NOT NULL,
  `nombre_presentacion` varchar(100) NOT NULL DEFAULT '',
  `tipo_presentacion` enum('COMPRA','VENTA','EMPAQUE','REPORTE','OTRA') NOT NULL DEFAULT 'VENTA',
  `equivalencia` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `cantidad_contenida` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `precio_venta` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `precio_compra` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `comision_monto` decimal(18,6) NOT NULL DEFAULT 0.000000,
  `comision_modo` int(11) NOT NULL DEFAULT 0,
  `predeterminado` tinyint(1) NOT NULL DEFAULT 0,
  `estado_tmp` enum('BORRADOR','VALIDADO','CONFIRMADO','ANULADO') NOT NULL DEFAULT 'BORRADOR',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tmp_contexto_operacion`
--

CREATE TABLE `tmp_contexto_operacion` (
  `idcontexto` bigint(20) UNSIGNED NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL DEFAULT 0,
  `idusuario` int(11) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `submodulo` varchar(100) NOT NULL,
  `tipo_operacion` enum('NUEVO','EDITAR','COPIAR') NOT NULL DEFAULT 'NUEVO',
  `token_operacion` char(36) NOT NULL,
  `tabla_principal` varchar(100) NOT NULL,
  `idregistro_principal` bigint(20) DEFAULT NULL,
  `estado_contexto` enum('ABIERTO','GUARDADO','CANCELADO','EXPIRADO') NOT NULL DEFAULT 'ABIERTO',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `ip_origen` varchar(45) DEFAULT NULL,
  `equipo_origen` varchar(100) DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transporte_cautil`
--

CREATE TABLE `transporte_cautil` (
  `id` int(11) NOT NULL,
  `configuracion` varchar(100) NOT NULL,
  `carga` decimal(18,2) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transporte_ruta`
--

CREATE TABLE `transporte_ruta` (
  `id` int(11) NOT NULL,
  `idruta` int(11) NOT NULL,
  `ruta` varchar(200) NOT NULL,
  `parcial` decimal(18,2) NOT NULL,
  `acumulada` decimal(18,2) NOT NULL,
  `monto` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transporte_rutacab`
--

CREATE TABLE `transporte_rutacab` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id` int(11) NOT NULL,
  `tit` varchar(300) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `cont` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `txtID_CLIENTE` varchar(20) NOT NULL,
  `direccion` varchar(70) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `cargo` varchar(20) DEFAULT NULL,
  `login` varchar(200) NOT NULL,
  `clave` varchar(64) NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `comisions` decimal(18,2) NOT NULL,
  `comisiond` decimal(18,2) NOT NULL,
  `condicion` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuario2`
--

CREATE TABLE `usuario2` (
  `idusuario` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `txtID_CLIENTE` varchar(20) NOT NULL,
  `direccion` varchar(70) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `cargo` varchar(20) DEFAULT NULL,
  `login` varchar(20) NOT NULL,
  `clave` varchar(64) NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `condicion` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuario_permiso`
--

CREATE TABLE `usuario_permiso` (
  `idusuario_permiso` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idpermiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `u_departamentos`
--

CREATE TABLE `u_departamentos` (
  `id` varchar(2) NOT NULL,
  `nombre` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `u_distritos`
--

CREATE TABLE `u_distritos` (
  `id` varchar(6) NOT NULL,
  `id_lista` varchar(4) NOT NULL,
  `nombre` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `u_provincias`
--

CREATE TABLE `u_provincias` (
  `id` varchar(4) NOT NULL,
  `id_lista` varchar(2) NOT NULL,
  `nombre` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta`
--

CREATE TABLE `venta` (
  `idventa` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idcaja` int(11) NOT NULL,
  `pedido` int(11) NOT NULL,
  `sector` int(11) NOT NULL DEFAULT 0,
  `kardex` int(11) NOT NULL,
  `controlpresupuestal` varchar(50) NOT NULL,
  `presupuesto` varchar(50) NOT NULL,
  `referencia` varchar(50) NOT NULL,
  `condiciones` varchar(50) NOT NULL,
  `orden` varchar(50) NOT NULL,
  `exportacion` varchar(5) NOT NULL,
  `txtID_CLIENTE` int(11) NOT NULL,
  `txtID_TIPO_DOCUMENTO` varchar(20) NOT NULL,
  `txtSERIE` varchar(7) NOT NULL,
  `txtNUMERO` varchar(10) NOT NULL,
  `txtFECHA_DOCUMENTO` datetime NOT NULL,
  `fecha_vto` date NOT NULL,
  `txtOBSERVACION` varchar(300) NOT NULL,
  `doc_relaciona` varchar(50) NOT NULL,
  `docmodifica_tipo` varchar(50) NOT NULL,
  `docmodifica` varchar(100) NOT NULL,
  `modifica_motivo` varchar(10) NOT NULL,
  `modifica_motivod` varchar(300) NOT NULL,
  `tipoguia` varchar(10) NOT NULL,
  `guia` varchar(50) NOT NULL,
  `tipoguia2` varchar(10) NOT NULL,
  `guia2` varchar(50) NOT NULL,
  `tipoguia3` varchar(10) NOT NULL,
  `guia3` varchar(50) NOT NULL,
  `tipoguia4` varchar(10) NOT NULL,
  `guia4` varchar(50) NOT NULL,
  `tipoguia5` varchar(10) NOT NULL,
  `guia5` varchar(50) NOT NULL,
  `txtID_MONEDA` varchar(20) NOT NULL,
  `tipocambio` decimal(18,3) NOT NULL,
  `tipo_pago` varchar(100) NOT NULL,
  `medio_pago` varchar(5) NOT NULL,
  `fpago_mpago` int(11) NOT NULL,
  `tarjeta` decimal(18,2) NOT NULL,
  `mach_id` varchar(50) NOT NULL,
  `mach_numero` varchar(50) NOT NULL,
  `mach_monto` decimal(18,5) NOT NULL,
  `mach_fecha` date NOT NULL DEFAULT '2020-01-01',
  `mach_observaciones` varchar(200) NOT NULL,
  `txtSUB_TOTAL` decimal(18,2) NOT NULL,
  `txtIGV` decimal(18,2) NOT NULL,
  `ICB` decimal(18,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(18,2) NOT NULL,
  `txtTOTAL` decimal(18,2) NOT NULL,
  `total_sunat` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_a_pagar` decimal(18,2) NOT NULL DEFAULT 0.00,
  `referencial` decimal(18,2) NOT NULL,
  `gratuita` decimal(18,2) NOT NULL,
  `exonerado` decimal(18,2) NOT NULL,
  `inafecta` decimal(18,2) NOT NULL,
  `percepcion` decimal(18,2) NOT NULL,
  `retencion` decimal(18,2) NOT NULL,
  `iddetraccion` int(11) NOT NULL,
  `detraccion` decimal(18,2) NOT NULL,
  `comision` decimal(18,2) NOT NULL,
  `dto_global_monto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `dto_global_tipo` varchar(20) NOT NULL DEFAULT '',
  `dto_global_modo` enum('MONTO','PORCENTAJE') NOT NULL DEFAULT 'MONTO',
  `dto_global_valor` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `dto_global_afecta_base` tinyint(1) NOT NULL DEFAULT 1,
  `dto_global_afecta_igv` tinyint(1) NOT NULL DEFAULT 1,
  `dto_global_monto_base` decimal(18,2) NOT NULL DEFAULT 0.00,
  `dto_global_monto_igv` decimal(18,2) NOT NULL DEFAULT 0.00,
  `dto_global_aplica_antes_igv` tinyint(1) NOT NULL DEFAULT 1,
  `dto_global_prorrateado` tinyint(1) NOT NULL DEFAULT 0,
  `total_descuentos_item` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_descuentos_global` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_descuentos_prorrateados` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_descuentos_base` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_descuentos_igv` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_descuentos_no_base` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_valor_venta_bruto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_valor_venta_neto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_igv_bruto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_igv_neto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_bruto_operacion` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_neto_operacion` decimal(18,2) NOT NULL DEFAULT 0.00,
  `estado` int(11) NOT NULL,
  `estadopago` int(11) NOT NULL,
  `hash_cpe` varchar(200) NOT NULL,
  `hash_cdr` varchar(300) NOT NULL,
  `mensaje` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta2`
--

CREATE TABLE `venta2` (
  `idventa` int(11) NOT NULL,
  `pedido` int(11) NOT NULL,
  `estadopago` int(11) NOT NULL,
  `anticipo` int(11) NOT NULL,
  `idanticipo` int(11) NOT NULL,
  `txtID_CLIENTE` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `txtID_TIPO_DOCUMENTO` varchar(20) NOT NULL,
  `doc_relaciona` varchar(20) NOT NULL,
  `docmodifica_tipo` varchar(10) NOT NULL,
  `docmodifica` varchar(100) NOT NULL,
  `modifica_motivo` varchar(10) NOT NULL,
  `modifica_motivod` varchar(300) NOT NULL,
  `txtSERIE` varchar(7) NOT NULL,
  `txtNUMERO` varchar(10) NOT NULL,
  `txtFECHA_DOCUMENTO` datetime NOT NULL,
  `fecha_vto` date NOT NULL,
  `txtOBSERVACION` varchar(300) NOT NULL,
  `txtSUB_TOTAL` decimal(18,2) NOT NULL,
  `txtIGV` decimal(18,2) NOT NULL,
  `ICB` decimal(18,2) NOT NULL,
  `descuento` decimal(18,2) NOT NULL,
  `txtTOTAL` decimal(18,2) NOT NULL,
  `tarjeta` decimal(18,2) NOT NULL,
  `ptarjeta` decimal(18,2) NOT NULL,
  `gratuita` decimal(18,2) NOT NULL,
  `exonerado` decimal(18,2) NOT NULL,
  `txtID_MONEDA` varchar(20) NOT NULL,
  `tipo_pago` varchar(100) NOT NULL,
  `orden` varchar(50) NOT NULL,
  `guia` varchar(50) NOT NULL,
  `presupuesto` varchar(50) NOT NULL,
  `referencia` varchar(50) NOT NULL,
  `condiciones` varchar(50) NOT NULL,
  `hash_cpe` varchar(200) NOT NULL,
  `hash_cdr` varchar(300) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta_cobrar`
--

CREATE TABLE `venta_cobrar` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `doc_relaciona` varchar(50) NOT NULL,
  `moneda` varchar(10) NOT NULL,
  `saldoi` decimal(18,2) NOT NULL,
  `saldoact` decimal(18,2) NOT NULL,
  `pago` decimal(18,2) NOT NULL,
  `estado` int(11) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta_descuentos`
--

CREATE TABLE `venta_descuentos` (
  `idventa_descuento` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `iddetalle_venta` int(11) DEFAULT NULL,
  `idventa_descuento_padre` int(11) DEFAULT NULL,
  `alcance` enum('ITEM','GLOBAL','PRORRATEO') NOT NULL,
  `origen_descuento` enum('DIRECTO','PRORRATEO','MANUAL','PROMOCION') NOT NULL DEFAULT 'DIRECTO',
  `tipo_descuento` varchar(20) NOT NULL,
  `modo_descuento` enum('MONTO','PORCENTAJE') NOT NULL DEFAULT 'MONTO',
  `afecta_base_imponible` tinyint(1) NOT NULL DEFAULT 1,
  `afecta_igv` tinyint(1) NOT NULL DEFAULT 1,
  `aplica_antes_igv` tinyint(1) NOT NULL DEFAULT 1,
  `valor_descuento` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `monto_descuento` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_base` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_igv` decimal(18,2) NOT NULL DEFAULT 0.00,
  `monto_total_descuento` decimal(18,2) NOT NULL DEFAULT 0.00,
  `base_antes_descuento` decimal(18,2) NOT NULL DEFAULT 0.00,
  `igv_antes_descuento` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_antes_descuento` decimal(18,2) NOT NULL DEFAULT 0.00,
  `base_despues_descuento` decimal(18,2) NOT NULL DEFAULT 0.00,
  `igv_despues_descuento` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_despues_descuento` decimal(18,2) NOT NULL DEFAULT 0.00,
  `orden_aplicacion` int(11) NOT NULL DEFAULT 1,
  `motivo_descuento` varchar(250) DEFAULT NULL,
  `observacion` varchar(500) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_modifica` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta_opdetalle`
--

CREATE TABLE `venta_opdetalle` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idopedido` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `tipodoc` varchar(30) NOT NULL,
  `seriedoc` varchar(30) NOT NULL,
  `cliente` varchar(400) NOT NULL,
  `docliente` varchar(30) NOT NULL,
  `usuario` varchar(300) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL,
  `igv` decimal(18,2) NOT NULL,
  `importe` decimal(18,2) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta_opdetallet`
--

CREATE TABLE `venta_opdetallet` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idpedido` int(11) NOT NULL,
  `origen` int(11) NOT NULL,
  `tipodoc` varchar(20) NOT NULL,
  `serie` varchar(20) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `codigoproducto` varchar(100) NOT NULL,
  `unidadmedida` varchar(50) NOT NULL,
  `idpresentacion` int(11) NOT NULL,
  `idlote` int(11) NOT NULL,
  `nombreproducto` text NOT NULL,
  `cti` decimal(18,2) NOT NULL,
  `precio` decimal(18,2) NOT NULL,
  `subtotal` decimal(18,2) NOT NULL,
  `igv` decimal(18,2) NOT NULL,
  `importe` decimal(18,2) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta_orden`
--

CREATE TABLE `venta_orden` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `destino` int(11) NOT NULL,
  `area` int(11) NOT NULL,
  `controlpresupuestal` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `tipo` varchar(5) NOT NULL,
  `serie` varchar(50) NOT NULL,
  `numero` varchar(12) NOT NULL,
  `tipodoc` varchar(10) NOT NULL,
  `oservicio` varchar(30) NOT NULL,
  `fecha` datetime NOT NULL,
  `moneda` varchar(10) NOT NULL DEFAULT 'PEN',
  `subtotal` decimal(18,2) NOT NULL,
  `igv` decimal(18,2) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `tipocambio` decimal(18,3) NOT NULL,
  `observaciones` text NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta_periodo`
--

CREATE TABLE `venta_periodo` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `nombreproducto` varchar(300) NOT NULL,
  `moneda` varchar(5) NOT NULL,
  `precio` decimal(18,7) NOT NULL,
  `tipoigv` int(11) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `fecha_pago` date NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta_puntos`
--

CREATE TABLE `venta_puntos` (
  `id` int(11) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `beta` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `idlocal` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `puntos` int(11) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webgal`
--

CREATE TABLE `webgal` (
  `id` int(11) NOT NULL,
  `id_serv` varchar(11) NOT NULL,
  `tit` varchar(300) NOT NULL,
  `cont` text NOT NULL,
  `imag` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activos_depreciacion`
--
ALTER TABLE `activos_depreciacion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activos_ingresos`
--
ALTER TABLE `activos_ingresos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activos_variacion`
--
ALTER TABLE `activos_variacion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `articulo`
--
ALTER TABLE `articulo`
  ADD PRIMARY KEY (`txtCOD_ARTICULO`),
  ADD KEY `idx_articulo_catalogo_afectacion` (`idcatalogo_afectacion`);
ALTER TABLE `articulo` ADD FULLTEXT KEY `Buscar` (`codigo`,`txtDESCRIPCION_ARTICULO`);

--
-- Indexes for table `articulo2`
--
ALTER TABLE `articulo2`
  ADD PRIMARY KEY (`txtCOD_ARTICULO`);

--
-- Indexes for table `articulo_auditoria`
--
ALTER TABLE `articulo_auditoria`
  ADD PRIMARY KEY (`idauditoria`),
  ADD KEY `idx_articulo_auditoria_articulo` (`idempresa`,`idarticulo`,`fecha_hora`);

--
-- Indexes for table `articulo_comision`
--
ALTER TABLE `articulo_comision`
  ADD PRIMARY KEY (`idarticulo_comision`),
  ADD KEY `idx_articulo_comision_articulo` (`idempresa`,`idarticulo`,`activo`);

--
-- Indexes for table `articulo_contabilidad`
--
ALTER TABLE `articulo_contabilidad`
  ADD PRIMARY KEY (`idarticulo_contabilidad`),
  ADD UNIQUE KEY `uk_articulo_contabilidad` (`idempresa`,`idarticulo`);

--
-- Indexes for table `articulo_garantia`
--
ALTER TABLE `articulo_garantia`
  ADD PRIMARY KEY (`idarticulo_garantia`),
  ADD KEY `idx_articulo_garantia_articulo` (`idempresa`,`idarticulo`,`estado_garantia`),
  ADD KEY `idx_articulo_garantia_serie` (`idarticulo_serie`),
  ADD KEY `idx_articulo_garantia_lote` (`idarticulo_lote`);

--
-- Indexes for table `articulo_garantia_movimiento`
--
ALTER TABLE `articulo_garantia_movimiento`
  ADD PRIMARY KEY (`idarticulo_garantia_movimiento`),
  ADD KEY `idx_articulo_garantia_movimiento` (`idarticulo_garantia`,`fecha_movimiento`);

--
-- Indexes for table `articulo_imagen`
--
ALTER TABLE `articulo_imagen`
  ADD PRIMARY KEY (`idarticulo_imagen`),
  ADD KEY `idx_articulo_imagen_articulo` (`idempresa`,`idarticulo`,`activo`);

--
-- Indexes for table `articulo_images`
--
ALTER TABLE `articulo_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `articulo_lote`
--
ALTER TABLE `articulo_lote`
  ADD PRIMARY KEY (`idarticulo_lote`),
  ADD KEY `idx_articulo_lote_articulo` (`idempresa`,`idlocal`,`idarticulo`,`estado_lote`),
  ADD KEY `idx_articulo_lote_numero` (`idempresa`,`numero_lote`);

--
-- Indexes for table `articulo_lote_movimiento`
--
ALTER TABLE `articulo_lote_movimiento`
  ADD PRIMARY KEY (`idarticulo_lote_movimiento`),
  ADD KEY `idx_articulo_lote_movimiento_lote` (`idarticulo_lote`,`fecha_movimiento`);

--
-- Indexes for table `articulo_perfil_logistico`
--
ALTER TABLE `articulo_perfil_logistico`
  ADD PRIMARY KEY (`idarticulo_perfil_logistico`),
  ADD UNIQUE KEY `uk_articulo_perfil_logistico` (`idempresa`,`idarticulo`);

--
-- Indexes for table `articulo_precio_cliente`
--
ALTER TABLE `articulo_precio_cliente`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `articulo_presentacion`
--
ALTER TABLE `articulo_presentacion`
  ADD PRIMARY KEY (`idarticulo_presentacion`),
  ADD KEY `idx_articulo_presentacion_articulo` (`idempresa`,`idarticulo`,`activo`);

--
-- Indexes for table `articulo_proveedor`
--
ALTER TABLE `articulo_proveedor`
  ADD PRIMARY KEY (`idarticulo_proveedor`),
  ADD KEY `idx_articulo_proveedor_articulo` (`idempresa`,`idarticulo`,`activo`),
  ADD KEY `idx_articulo_proveedor_proveedor` (`idempresa`,`idproveedor`);

--
-- Indexes for table `articulo_receta`
--
ALTER TABLE `articulo_receta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `articulo_receta_cabecera`
--
ALTER TABLE `articulo_receta_cabecera`
  ADD PRIMARY KEY (`idarticulo_receta`),
  ADD KEY `idx_articulo_receta_cabecera` (`idempresa`,`idarticulo`,`activo`);

--
-- Indexes for table `articulo_receta_detalle`
--
ALTER TABLE `articulo_receta_detalle`
  ADD PRIMARY KEY (`idarticulo_receta_detalle`),
  ADD KEY `idx_articulo_receta_detalle` (`idarticulo_receta`,`orden`);

--
-- Indexes for table `articulo_serie`
--
ALTER TABLE `articulo_serie`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `articulo_serie_movimiento`
--
ALTER TABLE `articulo_serie_movimiento`
  ADD PRIMARY KEY (`idarticulo_serie_movimiento`),
  ADD KEY `idx_articulo_serie_movimiento_serie` (`idarticulo_serie`,`fecha_movimiento`);

--
-- Indexes for table `articulo_serie_nueva`
--
ALTER TABLE `articulo_serie_nueva`
  ADD PRIMARY KEY (`idarticulo_serie`),
  ADD UNIQUE KEY `uk_articulo_serie_numero` (`idempresa`,`numero_serie`),
  ADD KEY `idx_articulo_serie_articulo` (`idempresa`,`idlocal`,`idarticulo`,`estado_serie`);

--
-- Indexes for table `articulo_stock`
--
ALTER TABLE `articulo_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `articulo_unidad`
--
ALTER TABLE `articulo_unidad`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `articulo_web`
--
ALTER TABLE `articulo_web`
  ADD PRIMARY KEY (`idarticulo_web`),
  ADD UNIQUE KEY `uk_articulo_web` (`idempresa`,`idarticulo`);

--
-- Indexes for table `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `caja_tipopago`
--
ALTER TABLE `caja_tipopago`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `caja_tipopago_persona`
--
ALTER TABLE `caja_tipopago_persona`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `caja_ventapago`
--
ALTER TABLE `caja_ventapago`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cardex`
--
ALTER TABLE `cardex`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cardex_detalle`
--
ALTER TABLE `cardex_detalle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cardex_procesos`
--
ALTER TABLE `cardex_procesos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catalogo_afectaciones_sunat`
--
ALTER TABLE `catalogo_afectaciones_sunat`
  ADD PRIMARY KEY (`idcatalogo_afectacion`),
  ADD UNIQUE KEY `uk_empresa_codigo_version_vigencia` (`idempresa`,`codigo_afectacion_igv`,`version_ubl`,`vigencia_desde`),
  ADD UNIQUE KEY `uk_cat_afec_empresa_ubl_codigo` (`idempresa`,`version_ubl`,`codigo_afectacion_igv`),
  ADD KEY `idx_empresa_estado` (`idempresa`,`estado`),
  ADD KEY `idx_empresa_grupo` (`idempresa`,`grupo_afectacion`),
  ADD KEY `idx_empresa_orden` (`idempresa`,`orden`),
  ADD KEY `idx_empresa_predeterminado` (`idempresa`,`predeterminado`),
  ADD KEY `idx_empresa_codigo_estado` (`idempresa`,`codigo_afectacion_igv`,`estado`),
  ADD KEY `idx_empresa_tributo` (`idempresa`,`codigo_tributo`),
  ADD KEY `idx_vigencia` (`vigencia_desde`,`vigencia_hasta`);

--
-- Indexes for table `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`idcategoria`);

--
-- Indexes for table `cliente_contacto`
--
ALTER TABLE `cliente_contacto`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config_contabilidad`
--
ALTER TABLE `config_contabilidad`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config_paquetes`
--
ALTER TABLE `config_paquetes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config_sistema`
--
ALTER TABLE `config_sistema`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cupones`
--
ALTER TABLE `cupones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cupones_detalle`
--
ALTER TABLE `cupones_detalle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detalle_ingreso`
--
ALTER TABLE `detalle_ingreso`
  ADD PRIMARY KEY (`iddetalle_ingreso`);

--
-- Indexes for table `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`iddetalle_venta`);

--
-- Indexes for table `detalle_venta2`
--
ALTER TABLE `detalle_venta2`
  ADD PRIMARY KEY (`iddetalle_venta`);

--
-- Indexes for table `detracciones`
--
ALTER TABLE `detracciones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `existencia`
--
ALTER TABLE `existencia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gre`
--
ALTER TABLE `gre`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guia_chofer`
--
ALTER TABLE `guia_chofer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guia_detalle`
--
ALTER TABLE `guia_detalle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guia_guia`
--
ALTER TABLE `guia_guia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guia_transportista`
--
ALTER TABLE `guia_transportista`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guia_traslado`
--
ALTER TABLE `guia_traslado`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guia_vehiculo`
--
ALTER TABLE `guia_vehiculo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `importaciones_detalle`
--
ALTER TABLE `importaciones_detalle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingreso`
--
ALTER TABLE `ingreso`
  ADD PRIMARY KEY (`idingreso`);

--
-- Indexes for table `ingreso_aprobado`
--
ALTER TABLE `ingreso_aprobado`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingreso_importacion`
--
ALTER TABLE `ingreso_importacion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingreso_otrosgastos`
--
ALTER TABLE `ingreso_otrosgastos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingreso_vale`
--
ALTER TABLE `ingreso_vale`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `links` ADD FULLTEXT KEY `tit` (`tit`,`cont`);

--
-- Indexes for table `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `percepcion`
--
ALTER TABLE `percepcion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `percepcion_det`
--
ALTER TABLE `percepcion_det`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`idpermiso`);

--
-- Indexes for table `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`idpersona`);

--
-- Indexes for table `persona2`
--
ALTER TABLE `persona2`
  ADD PRIMARY KEY (`idpersona`);

--
-- Indexes for table `persona_puntos`
--
ALTER TABLE `persona_puntos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planillas_ccostos`
--
ALTER TABLE `planillas_ccostos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planillas_configuracion`
--
ALTER TABLE `planillas_configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planillas_extras`
--
ALTER TABLE `planillas_extras`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planillas_horarios`
--
ALTER TABLE `planillas_horarios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planillas_rh`
--
ALTER TABLE `planillas_rh`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planillas_tablas`
--
ALTER TABLE `planillas_tablas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planillas_tareocab`
--
ALTER TABLE `planillas_tareocab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planillas_tareodet`
--
ALTER TABLE `planillas_tareodet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planilla_empleados`
--
ALTER TABLE `planilla_empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planilla_mensual`
--
ALTER TABLE `planilla_mensual`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planilla_pension`
--
ALTER TABLE `planilla_pension`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `principio_activo`
--
ALTER TABLE `principio_activo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resumen`
--
ALTER TABLE `resumen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sistema_descargas`
--
ALTER TABLE `sistema_descargas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_base_sistema`
--
ALTER TABLE `tbl_base_sistema`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_conciliacion`
--
ALTER TABLE `tbl_conciliacion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_conciliacion_detalle`
--
ALTER TABLE `tbl_conciliacion_detalle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_mensajes`
--
ALTER TABLE `tbl_mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_sire_det`
--
ALTER TABLE `tbl_sire_det`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tmp_articulo`
--
ALTER TABLE `tmp_articulo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tmp_articulo_catalogo_afectacion` (`idcatalogo_afectacion`),
  ADD KEY `idx_tmp_articulo_contexto` (`idcontexto`),
  ADD KEY `idx_tmp_articulo_usuario` (`idempresa`,`idusuario`,`idlocal`),
  ADD KEY `idx_tmp_articulo_token` (`token_operacion`);

--
-- Indexes for table `tmp_articuloimages`
--
ALTER TABLE `tmp_articuloimages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tmp_articulounidad`
--
ALTER TABLE `tmp_articulounidad`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tmp_articulo_imagen`
--
ALTER TABLE `tmp_articulo_imagen`
  ADD PRIMARY KEY (`idtmp_imagen`),
  ADD KEY `idx_tmp_imagen_contexto` (`idcontexto`),
  ADD KEY `idx_tmp_imagen_articulo` (`idtmp_articulo`);

--
-- Indexes for table `tmp_articulo_presentacion`
--
ALTER TABLE `tmp_articulo_presentacion`
  ADD PRIMARY KEY (`idtmp_presentacion`),
  ADD KEY `idx_tmp_presentacion_contexto` (`idcontexto`),
  ADD KEY `idx_tmp_presentacion_articulo` (`idtmp_articulo`);

--
-- Indexes for table `tmp_contexto_operacion`
--
ALTER TABLE `tmp_contexto_operacion`
  ADD PRIMARY KEY (`idcontexto`),
  ADD UNIQUE KEY `uk_tmp_contexto_token` (`token_operacion`),
  ADD KEY `idx_tmp_contexto_usuario` (`idempresa`,`idusuario`,`modulo`,`estado_contexto`),
  ADD KEY `idx_tmp_contexto_principal` (`tabla_principal`,`idregistro_principal`);

--
-- Indexes for table `transporte_cautil`
--
ALTER TABLE `transporte_cautil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transporte_ruta`
--
ALTER TABLE `transporte_ruta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transporte_rutacab`
--
ALTER TABLE `transporte_rutacab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`);

--
-- Indexes for table `usuario2`
--
ALTER TABLE `usuario2`
  ADD PRIMARY KEY (`idusuario`),
  ADD UNIQUE KEY `login_UNIQUE` (`login`);

--
-- Indexes for table `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  ADD PRIMARY KEY (`idusuario_permiso`),
  ADD KEY `fk_usuario_permiso_permiso_idx` (`idpermiso`),
  ADD KEY `fk_usuario_permiso_usuario_idx` (`idusuario`);

--
-- Indexes for table `u_departamentos`
--
ALTER TABLE `u_departamentos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `u_distritos`
--
ALTER TABLE `u_distritos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `u_provincias`
--
ALTER TABLE `u_provincias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`idventa`);

--
-- Indexes for table `venta2`
--
ALTER TABLE `venta2`
  ADD PRIMARY KEY (`idventa`);

--
-- Indexes for table `venta_cobrar`
--
ALTER TABLE `venta_cobrar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venta_descuentos`
--
ALTER TABLE `venta_descuentos`
  ADD PRIMARY KEY (`idventa_descuento`),
  ADD KEY `idx_venta` (`idventa`),
  ADD KEY `idx_detalle` (`iddetalle_venta`),
  ADD KEY `idx_empresa_local` (`idempresa`,`idlocal`),
  ADD KEY `idx_padre` (`idventa_descuento_padre`),
  ADD KEY `idx_alcance` (`alcance`),
  ADD KEY `idx_orden_aplicacion` (`orden_aplicacion`);

--
-- Indexes for table `venta_opdetalle`
--
ALTER TABLE `venta_opdetalle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venta_opdetallet`
--
ALTER TABLE `venta_opdetallet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venta_orden`
--
ALTER TABLE `venta_orden`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venta_periodo`
--
ALTER TABLE `venta_periodo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venta_puntos`
--
ALTER TABLE `venta_puntos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webgal`
--
ALTER TABLE `webgal`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activos_depreciacion`
--
ALTER TABLE `activos_depreciacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activos_ingresos`
--
ALTER TABLE `activos_ingresos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activos_variacion`
--
ALTER TABLE `activos_variacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo`
--
ALTER TABLE `articulo`
  MODIFY `txtCOD_ARTICULO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo2`
--
ALTER TABLE `articulo2`
  MODIFY `txtCOD_ARTICULO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_auditoria`
--
ALTER TABLE `articulo_auditoria`
  MODIFY `idauditoria` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_comision`
--
ALTER TABLE `articulo_comision`
  MODIFY `idarticulo_comision` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_contabilidad`
--
ALTER TABLE `articulo_contabilidad`
  MODIFY `idarticulo_contabilidad` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_garantia`
--
ALTER TABLE `articulo_garantia`
  MODIFY `idarticulo_garantia` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_garantia_movimiento`
--
ALTER TABLE `articulo_garantia_movimiento`
  MODIFY `idarticulo_garantia_movimiento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_imagen`
--
ALTER TABLE `articulo_imagen`
  MODIFY `idarticulo_imagen` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_images`
--
ALTER TABLE `articulo_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_lote`
--
ALTER TABLE `articulo_lote`
  MODIFY `idarticulo_lote` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_lote_movimiento`
--
ALTER TABLE `articulo_lote_movimiento`
  MODIFY `idarticulo_lote_movimiento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_perfil_logistico`
--
ALTER TABLE `articulo_perfil_logistico`
  MODIFY `idarticulo_perfil_logistico` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_precio_cliente`
--
ALTER TABLE `articulo_precio_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_presentacion`
--
ALTER TABLE `articulo_presentacion`
  MODIFY `idarticulo_presentacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_proveedor`
--
ALTER TABLE `articulo_proveedor`
  MODIFY `idarticulo_proveedor` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_receta`
--
ALTER TABLE `articulo_receta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_receta_cabecera`
--
ALTER TABLE `articulo_receta_cabecera`
  MODIFY `idarticulo_receta` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_receta_detalle`
--
ALTER TABLE `articulo_receta_detalle`
  MODIFY `idarticulo_receta_detalle` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_serie`
--
ALTER TABLE `articulo_serie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_serie_movimiento`
--
ALTER TABLE `articulo_serie_movimiento`
  MODIFY `idarticulo_serie_movimiento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_serie_nueva`
--
ALTER TABLE `articulo_serie_nueva`
  MODIFY `idarticulo_serie` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_stock`
--
ALTER TABLE `articulo_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_unidad`
--
ALTER TABLE `articulo_unidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articulo_web`
--
ALTER TABLE `articulo_web`
  MODIFY `idarticulo_web` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `caja_tipopago`
--
ALTER TABLE `caja_tipopago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `caja_tipopago_persona`
--
ALTER TABLE `caja_tipopago_persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `caja_ventapago`
--
ALTER TABLE `caja_ventapago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cardex`
--
ALTER TABLE `cardex`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cardex_detalle`
--
ALTER TABLE `cardex_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cardex_procesos`
--
ALTER TABLE `cardex_procesos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `catalogo_afectaciones_sunat`
--
ALTER TABLE `catalogo_afectaciones_sunat`
  MODIFY `idcatalogo_afectacion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categoria`
--
ALTER TABLE `categoria`
  MODIFY `idcategoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cliente_contacto`
--
ALTER TABLE `cliente_contacto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config_contabilidad`
--
ALTER TABLE `config_contabilidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config_paquetes`
--
ALTER TABLE `config_paquetes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config_sistema`
--
ALTER TABLE `config_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cupones`
--
ALTER TABLE `cupones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cupones_detalle`
--
ALTER TABLE `cupones_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detalle_ingreso`
--
ALTER TABLE `detalle_ingreso`
  MODIFY `iddetalle_ingreso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `iddetalle_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detalle_venta2`
--
ALTER TABLE `detalle_venta2`
  MODIFY `iddetalle_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detracciones`
--
ALTER TABLE `detracciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `existencia`
--
ALTER TABLE `existencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gre`
--
ALTER TABLE `gre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guia_chofer`
--
ALTER TABLE `guia_chofer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guia_detalle`
--
ALTER TABLE `guia_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guia_guia`
--
ALTER TABLE `guia_guia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guia_transportista`
--
ALTER TABLE `guia_transportista`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guia_traslado`
--
ALTER TABLE `guia_traslado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guia_vehiculo`
--
ALTER TABLE `guia_vehiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `importaciones_detalle`
--
ALTER TABLE `importaciones_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingreso`
--
ALTER TABLE `ingreso`
  MODIFY `idingreso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingreso_aprobado`
--
ALTER TABLE `ingreso_aprobado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingreso_importacion`
--
ALTER TABLE `ingreso_importacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingreso_otrosgastos`
--
ALTER TABLE `ingreso_otrosgastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingreso_vale`
--
ALTER TABLE `ingreso_vale`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `links`
--
ALTER TABLE `links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paises`
--
ALTER TABLE `paises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `percepcion`
--
ALTER TABLE `percepcion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `percepcion_det`
--
ALTER TABLE `percepcion_det`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permiso`
--
ALTER TABLE `permiso`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persona`
--
ALTER TABLE `persona`
  MODIFY `idpersona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persona2`
--
ALTER TABLE `persona2`
  MODIFY `idpersona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persona_puntos`
--
ALTER TABLE `persona_puntos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planillas_ccostos`
--
ALTER TABLE `planillas_ccostos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planillas_configuracion`
--
ALTER TABLE `planillas_configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planillas_extras`
--
ALTER TABLE `planillas_extras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planillas_horarios`
--
ALTER TABLE `planillas_horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planillas_rh`
--
ALTER TABLE `planillas_rh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planillas_tablas`
--
ALTER TABLE `planillas_tablas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planillas_tareocab`
--
ALTER TABLE `planillas_tareocab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planillas_tareodet`
--
ALTER TABLE `planillas_tareodet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planilla_empleados`
--
ALTER TABLE `planilla_empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planilla_mensual`
--
ALTER TABLE `planilla_mensual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `planilla_pension`
--
ALTER TABLE `planilla_pension`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `principio_activo`
--
ALTER TABLE `principio_activo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resumen`
--
ALTER TABLE `resumen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sistema_descargas`
--
ALTER TABLE `sistema_descargas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_base_sistema`
--
ALTER TABLE `tbl_base_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_conciliacion`
--
ALTER TABLE `tbl_conciliacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_conciliacion_detalle`
--
ALTER TABLE `tbl_conciliacion_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_mensajes`
--
ALTER TABLE `tbl_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sire_det`
--
ALTER TABLE `tbl_sire_det`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tipo_cambio`
--
ALTER TABLE `tipo_cambio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tmp_articulo`
--
ALTER TABLE `tmp_articulo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tmp_articuloimages`
--
ALTER TABLE `tmp_articuloimages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tmp_articulounidad`
--
ALTER TABLE `tmp_articulounidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tmp_articulo_imagen`
--
ALTER TABLE `tmp_articulo_imagen`
  MODIFY `idtmp_imagen` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tmp_articulo_presentacion`
--
ALTER TABLE `tmp_articulo_presentacion`
  MODIFY `idtmp_presentacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tmp_contexto_operacion`
--
ALTER TABLE `tmp_contexto_operacion`
  MODIFY `idcontexto` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transporte_cautil`
--
ALTER TABLE `transporte_cautil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transporte_ruta`
--
ALTER TABLE `transporte_ruta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transporte_rutacab`
--
ALTER TABLE `transporte_rutacab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuario2`
--
ALTER TABLE `usuario2`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  MODIFY `idusuario_permiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta`
--
ALTER TABLE `venta`
  MODIFY `idventa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta2`
--
ALTER TABLE `venta2`
  MODIFY `idventa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta_cobrar`
--
ALTER TABLE `venta_cobrar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta_descuentos`
--
ALTER TABLE `venta_descuentos`
  MODIFY `idventa_descuento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta_opdetalle`
--
ALTER TABLE `venta_opdetalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta_opdetallet`
--
ALTER TABLE `venta_opdetallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta_orden`
--
ALTER TABLE `venta_orden`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta_periodo`
--
ALTER TABLE `venta_periodo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta_puntos`
--
ALTER TABLE `venta_puntos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webgal`
--
ALTER TABLE `webgal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
