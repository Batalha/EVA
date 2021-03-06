<?php

class block_eva_training_suggestion_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG;

        if (!empty($this->block->config) && is_object($this->block->config)) {
            $data = $this->block->config;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 4;
        }


        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Title
        $mform->addElement('text', 'config_title', get_string('config_title', 'theme_evagu'));
        $mform->setDefault('config_title', 'Sugestão de treinamento');
        $mform->setType('config_title', PARAM_RAW);

        $slidesrange = array(
          1 => '1',
          2 => '2',
          3 => '3',
          4 => '4',
          5 => '5',
          6 => '6',
          7 => '7',
          8 => '8',
          9 => '9',
          10 => '10',
        );

        $slidesmax = 10;
        $mform->addElement('select', 'config_slidesnumber', get_string('config_items', 'theme_evagu'), $slidesrange);
        $mform->setDefault('config_slidesnumber', 4);



        for($i = 1; $i <= $slidesmax; $i++) {
            $mform->addElement('header', 'config_ccn_item' . $i , get_string('config_item', 'theme_evagu') . $i);

            $mform->addElement('text', 'config_title' . $i, get_string('config_title', 'theme_evagu'));
            $mform->setDefault('config_title' .$i , 'Sugestão de treinamento' . $i);
            $mform->setType('config_title' . $i, PARAM_TEXT);

            $mform->addElement('editor', 'config_text'.$i, get_string('config_body', 'theme_evagu'));
            $mform->setType('config_text'.$i, PARAM_RAW);




        }

        include($CFG->dirroot . '/theme/evagu/ccn/block_handler/edit.php');

    }

    function set_data($defaults) {
        if (!empty($this->block->config) && is_object($this->block->config)) {

            for($i = 1; $i <= $this->block->config->slidesnumber; $i++) {
                $field = 'file_slide' . $i;
                $conffield = 'config_file_slide' . $i;
                $draftitemid = file_get_submitted_draft_itemid($conffield);
                file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_eva_training_suggestion', 'slides', $i, array('subdirs'=>false));
                $defaults->$conffield['itemid'] = $draftitemid;
                $this->block->config->$field = $draftitemid;




                // if (!empty($this->block->config) && is_object($this->block->config)) {
                    $text = $this->block->config->faq_html . $i;
                    $conffield = 'config_body' . $i;
                    $draftid_editor = file_get_submitted_draft_itemid($conffield);
                    if (empty($text)) {
                        $currenttext = '';
                    } else {
                        $currenttext = $text;
                    }
                    $defaults->$conffield['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_eva_training_suggestion', 'content', $i, array('subdirs'=>false), $currenttext);
                    $defaults->$conffield['itemid'] = $draftid_editor;
                    $defaults->$conffield['format'] = $this->block->config->format . $i ;
                // } else {
                //     $text = '';
                // }

            }
        }

        parent::set_data($defaults);
    }
}
