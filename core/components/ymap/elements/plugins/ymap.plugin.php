<?php
$corePath = $modx->getOption('core_path', null, MODX_CORE_PATH).'components/ymap/';
switch ($modx->event->name) {
    case 'OnTVInputRenderList':
        $modx->event->output($corePath.'elements/tv/input/');
        break;
    // case 'OnTVOutputRenderList':
    //     $modx->event->output($corePath.'elements/tv/output/');
    //     break;
    // case 'OnTVInputPropertiesList':
    //     $modx->event->output($corePath.'elements/tv/inputoptions/');
    //     break;
    // case 'OnTVOutputRenderPropertiesList':
    //     $modx->event->output($corePath.'elements/tv/properties/');
    //     break;
    // case 'OnManagerPageBeforeRender':
    //     break;
}