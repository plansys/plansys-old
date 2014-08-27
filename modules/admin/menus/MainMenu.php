<?php 

 return array (
    array (
        'label' => 'Builder',
        'items' => array (
            array (
                'label' => 'Form Builder',
                'icon' => 'fa-gavel',
                'url' => '/admin/forms/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Menu Editor',
                'icon' => 'fa-sitemap',
                'url' => '/admin/menus/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Controller Manager',
                'icon' => 'fa-paper-plane',
                'url' => '/admin/controllerGenerator/index',
            ),
            array (
                'label' => 'Model Generator',
                'icon' => 'fa-cube',
                'url' => '/admin/modelGenerator/index',
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
                'url' => '/admin/users/index',
            ),
        ),
        'state' => 'collapsed',
    ),
    array (
        'label' => 'Reports',
        'icon' => '',
        'url' => '#',
        'items' => array (
            array (
                'label' => 'Management Report',
                'icon' => 'fa-arrow-up',
                'url' => '/admin/reports/index',
            ),
            array (
                'label' => 'Report Upload',
                'icon' => 'fa-cloud-upload',
                'url' => '/admin/reportupload/index',
            ),
        ),
    ),
    array (
        'label' => 'Settings',
        'icon' => '',
        'url' => '/admin/settings/index',
    ),
);