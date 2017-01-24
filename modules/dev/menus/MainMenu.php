<?php 

## AUTOGENERATED OPTIONS - DO NOT EDIT
$options = array (
    'mode' => 'normal',
    'layout' => array (
        'size' => '200',
        'sizetype' => 'px',
        'type' => 'menu',
        'name' => 'col1',
        'file' => 'application.modules.dev.menus.MainMenu',
        'title' => '',
        'icon' => '',
        'inlineJS' => '',
    ),
);
## END OF AUTOGENERATED OPTIONS

return array (
    array (
        'label' => 'Builder',
        'items' => array (
            array (
                'label' => 'Form Builder',
                'icon' => 'fa-file',
                'url' => '/dev/forms/index',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/forms/index',
                'state' => 'collapsed',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'MenuTree Editor',
                'icon' => 'fa-sitemap',
                'url' => '/dev/genMenu/index',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/genMenu/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Module Builder',
                'icon' => 'fa-empire',
                'url' => '/dev/genModule/index',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/genModule/index',
            ),
            array (
                'label' => 'Model Builder',
                'icon' => 'fa-cube',
                'url' => '/dev/genModel/index',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/genModel/index',
            ),
            array (
                'label' => 'Controller Builder',
                'icon' => 'fa-slack',
                'url' => '/dev/genCtrl/index',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/genCtrl/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Email Builder',
                'icon' => 'fa-envelope',
                'url' => '/dev/genEmail/index',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/genEmail/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Service Manager',
                'icon' => 'fa-asterisk',
                'url' => '/dev/service/index',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/service/index',
            ),
        ),
        'state' => 'collapsed',
        'icon' => 'fa-gavel',
    ),
    array (
        'label' => 'Users',
        'icon' => 'fa-user',
        'url' => '',
        'items' => array (
            array (
                'label' => 'User List',
                'icon' => 'fa-user',
                'url' => '/dev/user/index',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/user/index',
            ),
            array (
                'label' => 'Role Manager',
                'icon' => 'fa-graduation-cap',
                'url' => '/dev/user/roles',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/user/roles',
            ),
        ),
        'state' => 'collapsed',
        'formattedUrl' => '/eoffice/test/index.php',
    ),
    array (
        'label' => 'Settings',
        'icon' => 'fa-sliders',
        'url' => '/dev/setting',
        'formattedUrl' => '/eoffice/test/index.php?r=dev/setting',
        'items' => array (
            array (
                'label' => 'Application Setting',
                'icon' => 'fa-home',
                'url' => '/dev/setting/app',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/setting/app',
                'state' => 'collapsed',
            ),
            array (
                'label' => 'Theme Setting',
                'icon' => 'fa-picture-o',
                'url' => '/dev/setting/theme',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/setting/theme',
                'state' => 'collapsed',
            ),
            array (
                'label' => 'Database Setting',
                'icon' => 'fa-database',
                'url' => '/dev/setting/database',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/setting/database',
            ),
            array (
                'label' => 'Email Setting',
                'icon' => 'fa-envelope',
                'url' => '/dev/setting/email',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/setting/email',
                'state' => 'collapsed',
            ),
            array (
                'label' => 'LDAP Setting',
                'icon' => 'fa-users',
                'url' => '/dev/setting/ldap',
                'formattedUrl' => '/eoffice/test/index.php?r=dev/setting/ldap',
            ),
        ),
        'state' => 'collapsed',
    ),
    array (
        'label' => 'Repository',
        'icon' => 'fa-folder-open',
        'url' => '/repo/index',
        'formattedUrl' => '/eoffice/test/index.php?r=repo/index',
    ),
    array (
        'label' => 'Help',
        'icon' => 'fa-question-circle',
        'url' => '#',
        'items' => array (
            array (
                'label' => 'Selamat Datang di Plansys',
                'icon' => 'fa-flag-checkered',
                'url' => '/help/welcome',
                'formattedUrl' => '/eoffice/test/index.php?r=help/welcome',
            ),
            array (
                'label' => 'Tutorial Plansys',
                'icon' => 'fa-file-text-o',
                'url' => '/help/tutorial/bab1',
                'formattedUrl' => '/eoffice/test/index.php?r=help/tutorial/bab1',
            ),
        ),
        'state' => 'collapsed',
    ),
);