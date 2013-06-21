<?php
// $plugins = array();


$plugins[0] = $modx->newObject('modPlugin');
//$plugin->set('name', 'YMapCustomTv');
$plugins[0]->fromArray(
	array(
		//'id' => 0,
		'name' => 'YMapCustomTv',
		//'category' => 0,
		//'description' => '',
		'plugincode' => getSnippetContent($sources['elements'].'plugins/'.PKG_NAME_LOWER.'.plugin.php'),
		//'static' => 1,
		//'static_file' => $sources['elements'].'elements/plugins/'.PKG_NAME_LOWER.'.plugin.php',
	)
);
//$plugins[0]->set('id', 1);
//$plugins[0]->set('name', 'YMapCustomTv');
//$plugins[0]->set('description', '');
//$plugins[0]->set('plugincode', getSnippetContent($sources['elements'].'plugins/'.PKG_NAME_LOWER.'.plugin.php'));

$events = array(); 
$events['OnHandleRequest']= $modx->newObject('modPluginEvent');
$events['OnHandleRequest']->fromArray(array(
    'event' => 'OnTVInputRenderList',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

$plugins[0]->addMany($events, 'PluginEvents');
$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events.');
 

return $plugins;