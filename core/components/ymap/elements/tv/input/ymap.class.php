<?php
class YMapInputRender extends modTemplateVarInputRender {
    public function getTemplate() {
        return $this->modx->getOption('core_path').'components/ymap/elements/tv/input/tpl/ymap.tpl';
    }
    public function process($value, array $params = array()) {
        //$modx->lexicon->load('tv_widget');
    }
}
return 'YMapInputRender';