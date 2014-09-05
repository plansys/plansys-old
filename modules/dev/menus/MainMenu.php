<?php 

 return array (
    array (
        'label' => 'Builder',
        'items' => array (
            array (
                'label' => 'Form Builder',
                'icon' => 'fa-gavel',
                'url' => '/dev/forms/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Menu Editor',
                'icon' => 'fa-sitemap',
                'url' => '/dev/menus/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Controller Manager',
                'icon' => 'fa-paper-plane',
                'url' => '/dev/controllerGenerator/index',
            ),
            array (
                'label' => 'Model Generator',
                'icon' => 'fa-cube',
                'url' => '/dev/modelGenerator/index',
            ),
        ),
        'state' => 'collapsed',
        'icon' => '',
    ),
    array (
        'label' => 'Users',
        'icon' => '',
        'url' => '#',
        'items' => array (
            array (
                'label' => 'User Management',
                'icon' => 'fa-user',
                'url' => '/dev/users/index',
                'items' => array (
                    array (
                        'label' => 'Reports',
                        'icon' => '',
                        'url' => '#',
                        'items' => array (
                            array (
                                'label' => 'Management Report',
                                'icon' => 'fa-arrow-up',
                                'url' => '/dev/reports/index',
                            ),
                            array (
                                'label' => 'Report Upload',
                                'icon' => 'fa-cloud-upload',
                                'url' => '/dev/reportupload/index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'state' => 'collapsed',
    ),
    array (
        'label' => 'Settings',
        'icon' => '',
        'url' => '/dev/settings/index',
    ),
);