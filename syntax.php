<?php
if(!defined('DOKU_INC')) die();

class syntax_plugin_sqcard extends DokuWiki_Syntax_Plugin {

    public function getType(){ return 'formatting'; }
    public function getSort(){ return 158; }
    public function connectTo($mode) { $this->Lexer->addEntryPattern('<sqcard .*?>',$mode,'plugin_sqcard'); }
    public function postConnect() { $this->Lexer->addExitPattern('</sqcard>','plugin_sqcard'); }

    public function handle($match, $state, $pos, Doku_Handler $handler){
        switch ($state) {
            case DOKU_LEXER_ENTER :
            case DOKU_LEXER_UNMATCHED :
                return array($state, $match);
            case DOKU_LEXER_EXIT :
                return array($state, '');
        }
        return array();
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode != 'xhtml')
            return false;
        
        list($state,$match) = $data;
        switch ($state) {
            case DOKU_LEXER_ENTER :
                {
                    $id = substr($match,8,-1);
                    $file = wikiFN($id);
                    if(file_exists($file)) {
                        $fp = fopen($file, "r");
                        $fr = fread($fp, 4096*3);
                        fclose($fp);
                        preg_match('/<sq (.*)>(.*)<\/sq>/',$fr,$match);
                        $renderer->doc .= '<a href="'.wl($id).'" style="border:0;outline:none;">';
                        $renderer->doc .= '<div class=sqcard_box>';
                        $renderer->doc .= '<div class=sqcard_img_box><img class="sqcard_img" src="'.ml($match[1],'w=512,h=512').'" title="'.p_get_first_heading($id).'"><div class=sqcard_img_hover></div></div>';
                        $renderer->doc .= '<div class=sqcard_text>'.p_get_first_heading($id).'</div>';
                        $renderer->doc .= '</div>';
                        $renderer->doc .= '</a>';

                    }

                }
                break;
            case DOKU_LEXER_UNMATCHED :
                break;
            case DOKU_LEXER_EXIT :
                break;
        }
        return true;
    }
}

