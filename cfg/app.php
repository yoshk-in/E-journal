<?php

namespace cfg\app;

use App\domain\AbstractProduct;
use App\repository\DBLayer;
use App\domain\procedures\{CasualProcedure, CompositeProcedure};
use App\domain\productManager\ProductClassManager;

$abstractProductClass = ProductClassManager::ABSTRACT_PRODUCT_CLASS;
$abstractProductNamespace = ProductClassManager::ABSTRACT_PRODUCT_NAMESPACE;
$productProcedurePath = ProductClassManager::ABSTRACT_PRODUCT_DIR;
$productGenerateFolder = ProductClassManager::PRODUCT_GEN_FOLDER;
$generatingProductPath = ProductClassManager::PRODUCT_GEN_DIR;
$generatingProductClassNamePattern = ProductClassManager::PRODUCT_NAME_GEN_PATTERN;
$generatingProductNamespace = ProductClassManager::PRODUCT_GEN_NAMESPACE;
$generatingProductClassPattern = ProductClassManager::PRODUCT_GEN_PATTERN;
$eventSystemTrait = ProductClassManager::EVENT_SYS_TRAIT;

return [
    'app.generating_product_pattern' => $generatingProductClassPattern,
    'app.product_namespace' => 'App\\domain\\',
    'app.product_path' => $productProcedurePath,
    'app.event_sys_observable_trait' => $eventSystemTrait,
    'app.domain_path' => [$productProcedurePath],
    'app.product_gen_folder' => $productGenerateFolder,
    'app.product_gen_dir' => $generatingProductPath,
    'app.product_class' => $abstractProductClass,
    'app.product_gen_pattern' => $generatingProductClassNamePattern,
    'app.dev_mode' => true,
    'app.dbLayer' => DBLayer::class,
    'app.procedure_map' => require_once 'cfg/procedure_map.php',
    'app.product_map' => require_once 'cfg/product_map.php',
];