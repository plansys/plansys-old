<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminTest
 *
 * @author rizky
 */
class AdminTest extends Form{
    public function getForm() {
        return array (
            'formTitle' => 'Test',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }
    public function getFields() {
        return array (
            '<h2><center>{{ form.formTitle }}</center></h2><hr/>',
            array (
                'label' => 'A',
                'name' => 'a',
                'type' => 'TextField',
            ),
            array (
                'label' => 'B',
                'name' => 'b',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Submit',
                'type' => 'SubmitButton',
            ),
        );
    }
}
