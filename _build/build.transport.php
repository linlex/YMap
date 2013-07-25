<?php
$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;

//makes sure our script doesnt timeout
set_time_limit(0); 

define('PKG_NAME','YMap');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION','1.0.1');
define('PKG_RELEASE','rc1');


//set path for installer
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root'          => $root,
    'build'         => $root.'_build/',
    'data'          => $root.'_build/data/',
    'resolvers'     => $root.'_build/resolvers/',
    'source_core'   => $root.'core/components/'.PKG_NAME_LOWER,
    'lexicon'       => $root.'core/components/'.PKG_NAME_LOWER.'/lexicon/',
    'docs'          => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
    'elements'      => $root.'core/components/'.PKG_NAME_LOWER.'/elements/',
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
);


require_once $sources['build'] . '/includes/functions.php';


//require_once $sources['build'].'build.config.php';
define('MODX_CORE_PATH', '../../core/');
define('MODX_CONFIG_KEY', 'config');
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
//$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->setLogTarget('HTML');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/'.PKG_NAME_LOWER.'/');
// $modx->getService('lexicon','modLexicon');
// $modx->lexicon->load(PKG_NAME_LOWER.':properties');


//-----------------------------------------------------------------------------
//add namespace
// $namespace = $modx->newObject('modNamespace');
// $namespace->set('name', NAMESPACE_NAME);
// $namespace->set('path', "{core_path}components/".PKG_NAME_LOWER."/");
// $namespace->set('assets_path', "{assets_path}components/".PKG_NAME_LOWER."/");
// $vehicle = $builder->createVehicle($namespace, array(
//     xPDOTransport::UNIQUE_KEY    => 'name',
//     xPDOTransport::PRESERVE_KEYS => true,
//     xPDOTransport::UPDATE_OBJECT => true,
// ));
// $builder->putVehicle($vehicle);
// $modx->log(modX::LOG_LEVEL_INFO, "Packaged in ".NAMESPACE_NAME." namespace.");


//-----------------------------------------------------------------------------
// create category
$category = $modx->newObject('modCategory');
//$category->set('id', 1);
$category->set('category', PKG_NAME);


//-----------------------------------------------------------------------------
//add plugins
$plugins = include $sources['data'].'transport.plugins.php';
if (!is_array($plugins)) {
    $modx->log(modX::LOG_LEVEL_FATAL, 'Adding plugins failed.');
} else {
    $category->addMany($plugins);
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in '.count($plugins).' plugins.');
}


//-----------------------------------------------------------------------------
// create category vehicle
$attr = array(
    xPDOTransport::UNIQUE_KEY                => 'category',
    xPDOTransport::PRESERVE_KEYS             => false,
    xPDOTransport::UPDATE_OBJECT             => true,
    xPDOTransport::RELATED_OBJECTS           => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        // 'Snippets' => array(
        //     xPDOTransport::PRESERVE_KEYS => false,
        //     xPDOTransport::UPDATE_OBJECT => true,
        //     xPDOTransport::UNIQUE_KEY    => 'name',
        // ),
        // 'Chunks' => array(
        //     xPDOTransport::PRESERVE_KEYS => false,
        //     xPDOTransport::UPDATE_OBJECT => true,
        //     xPDOTransport::UNIQUE_KEY    => 'name',
        // ),
        'Plugins' => array(
            xPDOTransport::PRESERVE_KEYS             => false,
            xPDOTransport::UPDATE_OBJECT             => true,
            xPDOTransport::UNIQUE_KEY                => 'name',
            xPDOTransport::RELATED_OBJECTS           => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                'PluginEvents' => array(
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => false,
                    xPDOTransport::UNIQUE_KEY    => array('pluginid','event'),
                ),
            ),
        ),
    )
);
$vehicle = $builder->createVehicle($category, $attr);


//-----------------------------------------------------------------------------
// Add core source
$vehicle->resolve(
    'file', 
    array(
        'source' => $sources['source_core'],
        'target' => "return MODX_CORE_PATH.'components/';",
    )
);
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in CorePath');


//-----------------------------------------------------------------------------
// Add assets source
// $vehicle->resolve('file',array(
//     'source' => $sources['source_assets'],
//     'target' => "return MODX_ASSETS_PATH . 'components/';",
// ));
// $modx->log(modX::LOG_LEVEL_INFO,'Packaged in AssetsPath');


//-----------------------------------------------------------------------------
$builder->putVehicle($vehicle);
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in resolvers.');


//-----------------------------------------------------------------------------
//now pack in the license file, readme and setup options
$builder->setPackageAttributes(
    array(
        'license'   => file_get_contents($sources['docs'].'license.txt'),
        'readme'    => file_get_contents($sources['docs'].'readme.txt'),
        'changelog' => file_get_contents($sources['docs'].'changelog.txt'),
    )
);


//-----------------------------------------------------------------------------
//zip up package
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip...');
$builder->pack();

$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf('%2.4f s', $totalTime);
$modx->log(modX::LOG_LEVEL_INFO, "\nPackage Built.\nExecution time: {$totalTime}\n");

exit();