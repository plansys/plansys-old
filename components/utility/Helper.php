<?php

class Helper {

    public static function isLastString($str, $check) {
        if (strlen($str) > strlen($check)) {
            if (strrpos($str, $check, -strlen($check)) !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function timeToHour($time) {
        return strtotime('1970-01-01 ' . $time . 'GMT') / 3600;
    }

    public static function timeToSec($time) {
        return strtotime('1970-01-01 ' . $time . 'GMT');
    }

    public static function generateMonthInterval($start, $end) {
        $startInt = new DateTime($start);
        $endInt = new DateTime($end);
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($startInt, $interval, $endInt);
        return $period;
    }

    public static function generateDayInterval($start, $end) {
        $startInt = new DateTime($start);
        $endInt = (new DateTime($end))->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($startInt, $interval, $endInt);

        return $period;
    }

    public static function generateExcel($phpExcelObject, $filepath) {
        $objWriter = PHPExcel_IOFactory::createWriter($phpExcelObject, 'Excel5');
        $objWriter->save($filepath);
    }

    public static function exportExcel($data, $file) {

        ## add header
        if (count($data) > 0) {
            array_unshift($data, $data[0]);
            foreach ($data[0] as $k => $i) {
                $data[0][$k] = $k;
            }
        }

        ## generate excel
        Yii::import('ext.phpexcel.XPHPExcel');
        $phpExcelObject = XPHPExcel::createPHPExcel();
        $phpExcelObject->getActiveSheet()->fromArray($data, null, 'A1');
        foreach (range('A', $phpExcelObject->getActiveSheet()->getHighestDataColumn()) as $col) {
            $phpExcelObject->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
        }

        Helper::generateExcel($phpExcelObject, $file);
    }

    public static function getLastModified($class) {
        if (class_exists($class)) {
            $reflector = new ReflectionClass($class);
            $fn = $reflector->getFileName();
            return filemtime($fn);
        } else {
            return 0;
        }
    }

    public static function evaluate($_expression_, $_data_ = array()) {
        if (is_string($_expression_)) {
            extract($_data_);
            return eval('return ' . $_expression_ . ';');
        } else {
            $_data_[] = $this;
            return call_user_func_array($_expression_, $_data_);
        }
    }

    public static function explodeFirst($delimeter, $str) {
        $a = explode($delimeter, $str);
        return array_shift($a);
    }

    public static function explodeLast($delimeter, $str) {
        $a = explode($delimeter, $str);
        return end($a);
    }

    /**
     * Recursive function to get an associative array of class properties by property name => ReflectionProperty() object 
     * including inherited ones from extended classes 
     * @param string $className Class name 
     * @param string $types Any combination of <b>public, private, protected, static</b> 
     * @return array 
     */
    public static function getClassProperties($className, $types = 'public') {
        $ref = new ReflectionClass($className);
        $props = $ref->getProperties();
        $props_arr = [];
        foreach ($props as $prop) {
            $f = $prop->getName();

            if ($prop->isPublic() and ( stripos($types, 'public') === FALSE))
                continue;
            if ($prop->isPrivate() and ( stripos($types, 'private') === FALSE))
                continue;
            if ($prop->isProtected() and ( stripos($types, 'protected') === FALSE))
                continue;
            if ($prop->isStatic() and ( stripos($types, 'static') === FALSE))
                continue;

            $props_arr[$f] = $prop;
        }
        if ($parentClass = $ref->getParentClass()) {
            $parent_props_arr = Helper::getClassProperties($parentClass->getName()); //RECURSION 
            if (count($parent_props_arr) > 0)
                $props_arr = array_merge($parent_props_arr, $props_arr);
        }
        return $props_arr;
    }

    public static function getAlias($object) {
        if (!is_object($object) && is_file($object)) {
            $a1 = Yii::getPathOfAlias('app');
            $a2 = Yii::getPathOfAlias('application');
            if (strpos($object, $a1) === 0) {
                $alias = 'app';
                $filepath = str_replace($a1, '', $object);
            } else if (strpos($object, $a2) === 0) {
                $alias = 'application';
                $filepath = str_replace($a2, '', $object);
            }

            $filepath = trim(str_replace(["/", "\\"], ".", $filepath), ".");
        } else {
            $r = new ReflectionClass($object);
            $f = $r->getFileName();

            $path = str_replace("/", DIRECTORY_SEPARATOR, Yii::getPathOfAlias('app'));
            $filepath = str_replace($path . DIRECTORY_SEPARATOR, '', $f);
            $alias = 'app';
            if (strlen($f) == strlen($filepath)) {
                $path = str_replace("/", DIRECTORY_SEPARATOR, Yii::getPathOfAlias('application'));
                $filepath = str_replace($path . DIRECTORY_SEPARATOR, '', $f);
                $alias = 'application';
            }
        }

        return $alias . '.' . str_replace(DIRECTORY_SEPARATOR, '.', str_replace(".php", "", $filepath));
    }

    public static function arrayValuesRecursive($arr) {
        $arr = array_values($arr);
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                if (array_values($val) === $val) {
                    $arr[$key] = Helper::arrayValuesRecursive($val);
                } else if (count(@$arr[$key]['items']) > 0) {
                    $flatten = array_values($val['items']);
                    $arr[$key]['items'] = Helper::arrayValuesRecursive($flatten);
                }
            }
        }
        return $arr;
    }

// Does not support flag GLOB_BRACE  
    public static function globRecursive($pattern, $flags = 0, $returnCount = false, $count = 0) {
        $files = glob($pattern, $flags);
        if ($returnCount) {
            $count = count($files);
        }
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $recureGlob = Helper::globRecursive($dir . '/' . basename($pattern), $flags, $returnCount);
            if ($returnCount) {
                $files = array_merge($files, $recureGlob['files']);
                $count--;
                $count += $recureGlob['count'];
            } else {
                $files = array_merge($files, $recureGlob);
            }
        }

        if ($returnCount) {
            return ['files' => $files, 'count' => $count];
        } else {
            return $files;
        }
    }

    public static function uniqueArray($array, $key) {
        if (!is_array($array))
            return $array;

        $temp = [];
        return array_filter($array, function ($v) use (&$temp, $key) {
            if (in_array($v[$key], $temp)) {
                return false;
            } else {
                array_push($temp, $v[$key]);
                return true;
            }
        });
    }

    public static function arrayDiffAssocRecursive($array1, $array2) {
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = Helper::arrayDiffAssocRecursive($value, $array2[$key]);
                    if ($new_diff != FALSE) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!isset($array2[$key]) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }
        return !isset($difference) ? 0 : $difference;
    }

    public static function startsWith($haystack, $needle, $case = false) {
        if ($case)
            return strpos($haystack, $needle, 0) === 0;

        return stripos($haystack, $needle, 0) === 0;
    }

    public static function endsWith($haystack, $needle, $case = false) {
        if ($case)
            return strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $haystack) == 0;
        else
            return strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== false;
    }

    public static function is_assoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function toAssoc($arr) {
        $list = [];
        foreach ($arr as $k => $f) {
            if (is_string($f)) {
                $list[$f] = $f;
            }
        }
        return $list;
    }

    public static function execCommandInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B " . $cmd, "r"));
            //exec("start /B ". $cmd);
        } else {
            exec($cmd . " > /dev/null &");
        }
    }

    public static function classAlias($class, $withWebroot = true) {

        $reflector = new ReflectionClass($class);

        $wr = $withWebroot ? "webroot" : '';

        $fn = $reflector->getFileName();
        $webroot = str_replace("/", DIRECTORY_SEPARATOR, Yii::getPathOfAlias('webroot'));
        $alias = str_replace(DIRECTORY_SEPARATOR, ".", str_replace(".php", "", str_replace($webroot, $wr, $fn)));
        return trim($alias, ".");
    }

    public static function expandAttributes($attributes) {

        if (!is_array($attributes))
            return "";

        if (count($attributes) == 0)
            return "";


        return join(' ', array_map(function ($key) use ($attributes) {
                    if (is_bool($attributes[$key])) {
                        return $attributes[$key] ? $key : '';
                    }
                    return $key . '="' . $attributes[$key] . '"';
                }, array_keys($attributes)));
    }

    public static function minifyHtml($text) {

        $re = '%# Collapse whitespace everywhere but in blacklisted elements.
        (?>             # Match all whitespans other than single space.
          [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
        | \s{2,}        # or two or more consecutive-any-whitespace.
        ) # Note: The remaining regex consumes no text at all...
        (?=             # Ensure we are not in a blacklist tag.
          [^<]*+        # Either zero or more non-"<" {normal*}
          (?:           # Begin {(special normal*)*} construct
            <           # or a < starting a non-blacklist tag.
            (?!/?(?:textarea|pre|script)\b)
            [^<]*+      # more non-"<" {normal*}
          )*+           # Finish "unrolling-the-loop"
          (?:           # Begin alternation group.
            <           # Either a blacklist start tag.
            (?>textarea|pre|script)\b
          | \z          # or end of file.
          )             # End alternation group.
        )  # If we made it here, we are not in a blacklist tag.
        %Six';
        $text = preg_replace($re, " ", $text);
        if ($text === null)
            exit("PCRE Error! File too big.\n");
        return $text;
    }

    public static function camelToSnake($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('-', $ret);
    }

    public static function camelToSpacedCamel($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        return implode(' ', $ret);
    }

    public static function camelToUnderscore($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    public static function secondsToTime($s) {
        $h = floor($s / 3600);
        $s -= $h * 3600;
        $m = floor($s / 60);
        $s -= $m * 60;
        return $h . ':' . sprintf('%02d', $m) . ':' . sprintf('%02d', $s);
    }

    public static function iconList() {
        $icon = [
            '' => '- NONE -',
            'fa-adjust' => 'Adjust',
            'fa-adn' => 'Adn',
            'fa-align-center' => 'Align center',
            'fa-align-justify' => 'Align justify',
            'fa-align-left' => 'Align left',
            'fa-align-right' => 'Align right',
            'fa-ambulance' => 'Ambulance',
            'fa-anchor' => 'Anchor',
            'fa-android' => 'Android',
            'fa-angle-double-down' => 'Angle double down',
            'fa-angle-double-left' => 'Angle double left',
            'fa-angle-double-right' => 'Angle double right',
            'fa-angle-double-up' => 'Angle double up',
            'fa-angle-down' => 'Angle down',
            'fa-angle-left' => 'Angle left',
            'fa-angle-right' => 'Angle right',
            'fa-angle-up' => 'Angle up',
            'fa-apple' => 'Apple',
            'fa-archive' => 'Archive',
            'fa-arrow-circle-down' => 'Arrow circle down',
            'fa-arrow-circle-left' => 'Arrow circle left',
            'fa-arrow-circle-o-down' => 'Arrow circle o down',
            'fa-arrow-circle-o-left' => 'Arrow circle o left',
            'fa-arrow-circle-o-right' => 'Arrow circle o right',
            'fa-arrow-circle-o-up' => 'Arrow circle o up',
            'fa-arrow-circle-right' => 'Arrow circle right',
            'fa-arrow-circle-up' => 'Arrow circle up',
            'fa-arrow-down' => 'Arrow down',
            'fa-arrow-left' => 'Arrow left',
            'fa-arrow-right' => 'Arrow right',
            'fa-arrow-up' => 'Arrow up',
            'fa-arrows' => 'Arrows',
            'fa-arrows-alt' => 'Arrows alt',
            'fa-arrows-h' => 'Arrows h',
            'fa-arrows-v' => 'Arrows v',
            'fa-asterisk' => 'Asterisk',
            'fa-backward' => 'Backward',
            'fa-ban' => 'Ban',
            'fa-bar-chart-o' => 'Bar chart o',
            'fa-barcode' => 'Barcode',
            'fa-bars' => 'Bars',
            'fa-beer' => 'Beer',
            'fa-behance' => 'Behance',
            'fa-behance-square' => 'Behance square',
            'fa-bell' => 'Bell',
            'fa-bell-o' => 'Bell o',
            'fa-bitbucket' => 'Bitbucket',
            'fa-bitbucket-square' => 'Bitbucket square',
            'fa-bold' => 'Bold',
            'fa-bolt' => 'Bolt',
            'fa-bomb' => 'Bomb',
            'fa-book' => 'Book',
            'fa-bookmark' => 'Bookmark',
            'fa-bookmark-o' => 'Bookmark o',
            'fa-briefcase' => 'Briefcase',
            'fa-btc' => 'Btc',
            'fa-bug' => 'Bug',
            'fa-building' => 'Building',
            'fa-building-o' => 'Building o',
            'fa-bullhorn' => 'Bullhorn',
            'fa-bullseye' => 'Bullseye',
            'fa-calendar' => 'Calendar',
            'fa-calendar-o' => 'Calendar o',
            'fa-camera' => 'Camera',
            'fa-camera-retro' => 'Camera retro',
            'fa-car' => 'Car',
            'fa-caret-down' => 'Caret down',
            'fa-caret-left' => 'Caret left',
            'fa-caret-right' => 'Caret right',
            'fa-caret-square-o-down' => 'Caret square o down',
            'fa-caret-square-o-left' => 'Caret square o left',
            'fa-caret-square-o-right' => 'Caret square o right',
            'fa-caret-square-o-up' => 'Caret square o up',
            'fa-caret-up' => 'Caret up',
            'fa-certificate' => 'Certificate',
            'fa-chain-broken' => 'Chain broken',
            'fa-check' => 'Check',
            'fa-check-circle' => 'Check circle',
            'fa-check-circle-o' => 'Check circle o',
            'fa-check-square' => 'Check square',
            'fa-check-square-o' => 'Check square o',
            'fa-chevron-circle-down' => 'Chevron circle down',
            'fa-chevron-circle-left' => 'Chevron circle left',
            'fa-chevron-circle-right' => 'Chevron circle right',
            'fa-chevron-circle-up' => 'Chevron circle up',
            'fa-chevron-down' => 'Chevron down',
            'fa-chevron-left' => 'Chevron left',
            'fa-chevron-right' => 'Chevron right',
            'fa-chevron-up' => 'Chevron up',
            'fa-child' => 'Child',
            'fa-circle' => 'Circle',
            'fa-circle-o' => 'Circle o',
            'fa-circle-o-notch' => 'Circle o notch',
            'fa-circle-thin' => 'Circle thin',
            'fa-clipboard' => 'Clipboard',
            'fa-clock-o' => 'Clock o',
            'fa-cloud' => 'Cloud',
            'fa-cloud-download' => 'Cloud download',
            'fa-cloud-upload' => 'Cloud upload',
            'fa-code' => 'Code',
            'fa-code-fork' => 'Code fork',
            'fa-codepen' => 'Codepen',
            'fa-coffee' => 'Coffee',
            'fa-cog' => 'Cog',
            'fa-cogs' => 'Cogs',
            'fa-columns' => 'Columns',
            'fa-comment' => 'Comment',
            'fa-comment-o' => 'Comment o',
            'fa-comments' => 'Comments',
            'fa-comments-o' => 'Comments o',
            'fa-compass' => 'Compass',
            'fa-compress' => 'Compress',
            'fa-credit-card' => 'Credit card',
            'fa-crop' => 'Crop',
            'fa-crosshairs' => 'Crosshairs',
            'fa-css3' => 'Css3',
            'fa-cube' => 'Cube',
            'fa-cubes' => 'Cubes',
            'fa-cutlery' => 'Cutlery',
            'fa-database' => 'Database',
            'fa-delicious' => 'Delicious',
            'fa-desktop' => 'Desktop',
            'fa-deviantart' => 'Deviantart',
            'fa-digg' => 'Digg',
            'fa-dot-circle-o' => 'Dot circle o',
            'fa-download' => 'Download',
            'fa-dribbble' => 'Dribbble',
            'fa-dropbox' => 'Dropbox',
            'fa-drupal' => 'Drupal',
            'fa-eject' => 'Eject',
            'fa-ellipsis-h' => 'Ellipsis h',
            'fa-ellipsis-v' => 'Ellipsis v',
            'fa-empire' => 'Empire',
            'fa-envelope' => 'Envelope',
            'fa-envelope-o' => 'Envelope o',
            'fa-envelope-square' => 'Envelope square',
            'fa-eraser' => 'Eraser',
            'fa-eur' => 'Eur',
            'fa-exchange' => 'Exchange',
            'fa-exclamation' => 'Exclamation',
            'fa-exclamation-circle' => 'Exclamation circle',
            'fa-exclamation-triangle' => 'Exclamation triangle',
            'fa-expand' => 'Expand',
            'fa-external-link' => 'External link',
            'fa-external-link-square' => 'External link square',
            'fa-eye' => 'Eye',
            'fa-eye-slash' => 'Eye slash',
            'fa-facebook' => 'Facebook',
            'fa-facebook-square' => 'Facebook square',
            'fa-fast-backward' => 'Fast backward',
            'fa-fast-forward' => 'Fast forward',
            'fa-fax' => 'Fax',
            'fa-female' => 'Female',
            'fa-fighter-jet' => 'Fighter jet',
            'fa-file' => 'File',
            'fa-file-archive-o' => 'File archive o',
            'fa-file-audio-o' => 'File audio o',
            'fa-file-code-o' => 'File code o',
            'fa-file-excel-o' => 'File excel o',
            'fa-file-image-o' => 'File image o',
            'fa-file-o' => 'File o',
            'fa-file-pdf-o' => 'File pdf o',
            'fa-file-powerpoint-o' => 'File powerpoint o',
            'fa-file-text' => 'File text',
            'fa-file-text-o' => 'File text o',
            'fa-file-video-o' => 'File video o',
            'fa-file-word-o' => 'File word o',
            'fa-files-o' => 'Files o',
            'fa-film' => 'Film',
            'fa-filter' => 'Filter',
            'fa-fire' => 'Fire',
            'fa-fire-extinguisher' => 'Fire extinguisher',
            'fa-flag' => 'Flag',
            'fa-flag-checkered' => 'Flag checkered',
            'fa-flag-o' => 'Flag o',
            'fa-flask' => 'Flask',
            'fa-flickr' => 'Flickr',
            'fa-floppy-o' => 'Floppy o',
            'fa-folder' => 'Folder',
            'fa-folder-o' => 'Folder o',
            'fa-folder-open' => 'Folder open',
            'fa-folder-open-o' => 'Folder open o',
            'fa-font' => 'Font',
            'fa-forward' => 'Forward',
            'fa-foursquare' => 'Foursquare',
            'fa-frown-o' => 'Frown o',
            'fa-gamepad' => 'Gamepad',
            'fa-gavel' => 'Gavel',
            'fa-gbp' => 'Gbp',
            'fa-gift' => 'Gift',
            'fa-git' => 'Git',
            'fa-git-square' => 'Git square',
            'fa-github' => 'Github',
            'fa-github-alt' => 'Github alt',
            'fa-github-square' => 'Github square',
            'fa-gittip' => 'Gittip',
            'fa-glass' => 'Glass',
            'fa-globe' => 'Globe',
            'fa-google' => 'Google',
            'fa-google-plus' => 'Google plus',
            'fa-google-plus-square' => 'Google plus square',
            'fa-graduation-cap' => 'Graduation cap',
            'fa-h-square' => 'H square',
            'fa-hacker-news' => 'Hacker news',
            'fa-hand-o-down' => 'Hand o down',
            'fa-hand-o-left' => 'Hand o left',
            'fa-hand-o-right' => 'Hand o right',
            'fa-hand-o-up' => 'Hand o up',
            'fa-hdd-o' => 'Hdd o',
            'fa-header' => 'Header',
            'fa-headphones' => 'Headphones',
            'fa-heart' => 'Heart',
            'fa-heart-o' => 'Heart o',
            'fa-history' => 'History',
            'fa-home' => 'Home',
            'fa-hospital-o' => 'Hospital o',
            'fa-html5' => 'Html5',
            'fa-inbox' => 'Inbox',
            'fa-indent' => 'Indent',
            'fa-info' => 'Info',
            'fa-info-circle' => 'Info circle',
            'fa-inr' => 'Inr',
            'fa-instagram' => 'Instagram',
            'fa-italic' => 'Italic',
            'fa-joomla' => 'Joomla',
            'fa-jpy' => 'Jpy',
            'fa-jsfiddle' => 'Jsfiddle',
            'fa-key' => 'Key',
            'fa-keyboard-o' => 'Keyboard o',
            'fa-krw' => 'Krw',
            'fa-language' => 'Language',
            'fa-laptop' => 'Laptop',
            'fa-leaf' => 'Leaf',
            'fa-lemon-o' => 'Lemon o',
            'fa-level-down' => 'Level down',
            'fa-level-up' => 'Level up',
            'fa-life-ring' => 'Life ring',
            'fa-lightbulb-o' => 'Lightbulb o',
            'fa-link' => 'Link',
            'fa-linkedin' => 'Linkedin',
            'fa-linkedin-square' => 'Linkedin square',
            'fa-linux' => 'Linux',
            'fa-list' => 'List',
            'fa-list-alt' => 'List alt',
            'fa-list-ol' => 'List ol',
            'fa-list-ul' => 'List ul',
            'fa-location-arrow' => 'Location arrow',
            'fa-lock' => 'Lock',
            'fa-long-arrow-down' => 'Long arrow down',
            'fa-long-arrow-left' => 'Long arrow left',
            'fa-long-arrow-right' => 'Long arrow right',
            'fa-long-arrow-up' => 'Long arrow up',
            'fa-magic' => 'Magic',
            'fa-magnet' => 'Magnet',
            'fa-male' => 'Male',
            'fa-map-marker' => 'Map marker',
            'fa-maxcdn' => 'Maxcdn',
            'fa-medkit' => 'Medkit',
            'fa-meh-o' => 'Meh o',
            'fa-microphone' => 'Microphone',
            'fa-microphone-slash' => 'Microphone slash',
            'fa-minus' => 'Minus',
            'fa-minus-circle' => 'Minus circle',
            'fa-minus-square' => 'Minus square',
            'fa-minus-square-o' => 'Minus square o',
            'fa-mobile' => 'Mobile',
            'fa-money' => 'Money',
            'fa-moon-o' => 'Moon o',
            'fa-music' => 'Music',
            'fa-openid' => 'Openid',
            'fa-outdent' => 'Outdent',
            'fa-pagelines' => 'Pagelines',
            'fa-paper-plane' => 'Paper plane',
            'fa-paper-plane-o' => 'Paper plane o',
            'fa-paperclip' => 'Paperclip',
            'fa-paragraph' => 'Paragraph',
            'fa-pause' => 'Pause',
            'fa-paw' => 'Paw',
            'fa-pencil' => 'Pencil',
            'fa-pencil-square' => 'Pencil square',
            'fa-pencil-square-o' => 'Pencil square o',
            'fa-phone' => 'Phone',
            'fa-phone-square' => 'Phone square',
            'fa-picture-o' => 'Picture o',
            'fa-pied-piper' => 'Pied piper',
            'fa-pied-piper-alt' => 'Pied piper alt',
            'fa-pinterest' => 'Pinterest',
            'fa-pinterest-square' => 'Pinterest square',
            'fa-plane' => 'Plane',
            'fa-play' => 'Play',
            'fa-play-circle' => 'Play circle',
            'fa-play-circle-o' => 'Play circle o',
            'fa-plus' => 'Plus',
            'fa-plus-circle' => 'Plus circle',
            'fa-plus-square' => 'Plus square',
            'fa-plus-square-o' => 'Plus square o',
            'fa-power-off' => 'Power off',
            'fa-print' => 'Print',
            'fa-puzzle-piece' => 'Puzzle piece',
            'fa-qq' => 'Qq',
            'fa-qrcode' => 'Qrcode',
            'fa-question' => 'Question',
            'fa-question-circle' => 'Question circle',
            'fa-quote-left' => 'Quote left',
            'fa-quote-right' => 'Quote right',
            'fa-random' => 'Random',
            'fa-rebel' => 'Rebel',
            'fa-recycle' => 'Recycle',
            'fa-reddit' => 'Reddit',
            'fa-reddit-square' => 'Reddit square',
            'fa-refresh' => 'Refresh',
            'fa-renren' => 'Renren',
            'fa-repeat' => 'Repeat',
            'fa-reply' => 'Reply',
            'fa-reply-all' => 'Reply all',
            'fa-retweet' => 'Retweet',
            'fa-road' => 'Road',
            'fa-rocket' => 'Rocket',
            'fa-rss' => 'Rss',
            'fa-rss-square' => 'Rss square',
            'fa-rub' => 'Rub',
            'fa-scissors' => 'Scissors',
            'fa-search' => 'Search',
            'fa-search-minus' => 'Search minus',
            'fa-search-plus' => 'Search plus',
            'fa-share' => 'Share',
            'fa-share-alt' => 'Share alt',
            'fa-share-alt-square' => 'Share alt square',
            'fa-share-square' => 'Share square',
            'fa-share-square-o' => 'Share square o',
            'fa-shield' => 'Shield',
            'fa-shopping-cart' => 'Shopping cart',
            'fa-sign-in' => 'Sign in',
            'fa-sign-out' => 'Sign out',
            'fa-signal' => 'Signal',
            'fa-sitemap' => 'Sitemap',
            'fa-skype' => 'Skype',
            'fa-slack' => 'Slack',
            'fa-sliders' => 'Sliders',
            'fa-smile-o' => 'Smile o',
            'fa-sort' => 'Sort',
            'fa-sort-alpha-asc' => 'Sort alpha asc',
            'fa-sort-alpha-desc' => 'Sort alpha desc',
            'fa-sort-amount-asc' => 'Sort amount asc',
            'fa-sort-amount-desc' => 'Sort amount desc',
            'fa-sort-asc' => 'Sort asc',
            'fa-sort-desc' => 'Sort desc',
            'fa-sort-numeric-asc' => 'Sort numeric asc',
            'fa-sort-numeric-desc' => 'Sort numeric desc',
            'fa-soundcloud' => 'Soundcloud',
            'fa-space-shuttle' => 'Space shuttle',
            'fa-spinner' => 'Spinner',
            'fa-spoon' => 'Spoon',
            'fa-spotify' => 'Spotify',
            'fa-square' => 'Square',
            'fa-square-o' => 'Square o',
            'fa-stack-exchange' => 'Stack exchange',
            'fa-stack-overflow' => 'Stack overflow',
            'fa-star' => 'Star',
            'fa-star-half' => 'Star half',
            'fa-star-half-o' => 'Star half o',
            'fa-star-o' => 'Star o',
            'fa-steam' => 'Steam',
            'fa-steam-square' => 'Steam square',
            'fa-step-backward' => 'Step backward',
            'fa-step-forward' => 'Step forward',
            'fa-stethoscope' => 'Stethoscope',
            'fa-stop' => 'Stop',
            'fa-strikethrough' => 'Strikethrough',
            'fa-stumbleupon' => 'Stumbleupon',
            'fa-stumbleupon-circle' => 'Stumbleupon circle',
            'fa-subscript' => 'Subscript',
            'fa-suitcase' => 'Suitcase',
            'fa-sun-o' => 'Sun o',
            'fa-superscript' => 'Superscript',
            'fa-table' => 'Table',
            'fa-tablet' => 'Tablet',
            'fa-tachometer' => 'Tachometer',
            'fa-tag' => 'Tag',
            'fa-tags' => 'Tags',
            'fa-tasks' => 'Tasks',
            'fa-taxi' => 'Taxi',
            'fa-tencent-weibo' => 'Tencent weibo',
            'fa-terminal' => 'Terminal',
            'fa-text-height' => 'Text height',
            'fa-text-width' => 'Text width',
            'fa-th' => 'Th',
            'fa-th-large' => 'Th large',
            'fa-th-list' => 'Th list',
            'fa-thumb-tack' => 'Thumb tack',
            'fa-thumbs-down' => 'Thumbs down',
            'fa-thumbs-o-down' => 'Thumbs o down',
            'fa-thumbs-o-up' => 'Thumbs o up',
            'fa-thumbs-up' => 'Thumbs up',
            'fa-ticket' => 'Ticket',
            'fa-times' => 'Times',
            'fa-times-circle' => 'Times circle',
            'fa-times-circle-o' => 'Times circle o',
            'fa-tint' => 'Tint',
            'fa-trash-o' => 'Trash o',
            'fa-tree' => 'Tree',
            'fa-trello' => 'Trello',
            'fa-trophy' => 'Trophy',
            'fa-truck' => 'Truck',
            'fa-try' => 'Try',
            'fa-tumblr' => 'Tumblr',
            'fa-tumblr-square' => 'Tumblr square',
            'fa-twitter' => 'Twitter',
            'fa-twitter-square' => 'Twitter square',
            'fa-umbrella' => 'Umbrella',
            'fa-underline' => 'Underline',
            'fa-undo' => 'Undo',
            'fa-university' => 'University',
            'fa-unlock' => 'Unlock',
            'fa-unlock-alt' => 'Unlock alt',
            'fa-upload' => 'Upload',
            'fa-usd' => 'Usd',
            'fa-user' => 'User',
            'fa-user-md' => 'User md',
            'fa-users' => 'Users',
            'fa-video-camera' => 'Video camera',
            'fa-vimeo-square' => 'Vimeo square',
            'fa-vine' => 'Vine',
            'fa-vk' => 'Vk',
            'fa-volume-down' => 'Volume down',
            'fa-volume-off' => 'Volume off',
            'fa-volume-up' => 'Volume up',
            'fa-weibo' => 'Weibo',
            'fa-weixin' => 'Weixin',
            'fa-wheelchair' => 'Wheelchair',
            'fa-windows' => 'Windows',
            'fa-wordpress' => 'Wordpress',
            'fa-wrench' => 'Wrench',
            'fa-xing' => 'Xing',
            'fa-xing-square' => 'Xing square',
            'fa-yahoo' => 'Yahoo',
            'fa-youtube' => 'Youtube',
            'fa-youtube-play' => 'Youtube play',
            'fa-youtube-square' => 'Youtube square',
        ];

        foreach ($icon as $k => $v) {
            $icon[$k] = '<i class="fa ' . $k . '"></i> ' . $v;
        }
        return $icon;
    }

    public static function returnAlias($items, $type, $code = null) {
        if (isset($code))
            return isset($items[$type][$code]) ? $items[$type][$code] : false;
        else
            return isset($items[$type]) ? $items[$type] : false;
    }

    /**
     * Build a URL.
     *
     * The parts of the second URL will be merged into the first according to
     * the flags argument.
     *
     * @param mixed $url     (part(s) of) an URL in form of a string or
     *                       associative array like parse_url() returns
     * @param mixed $parts   same as the first argument
     * @param int   $flags   a bitmask of binary or'ed HTTP_URL constants;
     *                       HTTP_URL_REPLACE is the default
     * @param array $new_url if set, it will be filled with the parts of the
     *                       composed url like parse_url() would return
     * @return string
     */
    public static function buildUrl($url, $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = array()) {
        is_array($url) || $url = parse_url($url);
        is_array($parts) || $parts = parse_url($parts);

        isset($url['query']) && is_string($url['query']) || $url['query'] = null;
        isset($parts['query']) && is_string($parts['query']) || $parts['query'] = null;

        $keys = array('user', 'pass', 'port', 'path', 'query', 'fragment');

        // HTTP_URL_STRIP_ALL and HTTP_URL_STRIP_AUTH cover several other flags.
        if ($flags & HTTP_URL_STRIP_ALL) {
            $flags |= HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS | HTTP_URL_STRIP_PORT | HTTP_URL_STRIP_PATH | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT;
        } elseif ($flags & HTTP_URL_STRIP_AUTH) {
            $flags |= HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS;
        }

        // Schema and host are alwasy replaced
        foreach (array('scheme', 'host') as $part) {
            if (isset($parts[$part])) {
                $url[$part] = $parts[$part];
            }
        }

        if ($flags & HTTP_URL_REPLACE) {
            foreach ($keys as $key) {
                if (isset($parts[$key])) {
                    $url[$key] = $parts[$key];
                }
            }
        } else {
            if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH)) {
                if (isset($url['path']) && substr($parts['path'], 0, 1) !== '/') {
                    $url['path'] = rtrim(
                                    str_replace(basename($url['path']), '', $url['path']), '/'
                            ) . '/' . ltrim($parts['path'], '/');
                } else {
                    $url['path'] = $parts['path'];
                }
            }

            if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY)) {
                if (isset($url['query'])) {
                    parse_str($url['query'], $url_query);
                    parse_str($parts['query'], $parts_query);

                    $url['query'] = http_build_query(
                            array_replace_recursive(
                                    $url_query, $parts_query
                            )
                    );
                } else {
                    $url['query'] = $parts['query'];
                }
            }
        }

        if (isset($url['path']) && substr($url['path'], 0, 1) !== '/') {
            $url['path'] = '/' . $url['path'];
        }

        foreach ($keys as $key) {
            $strip = 'HTTP_URL_STRIP_' . strtoupper($key);
            if ($flags & constant($strip)) {
                unset($url[$key]);
            }
        }

        $parsed_string = '';

        if (isset($url['scheme'])) {
            $parsed_string .= $url['scheme'] . '://';
        }

        if (isset($url['user'])) {
            $parsed_string .= $url['user'];

            if (isset($url['pass'])) {
                $parsed_string .= ':' . $url['pass'];
            }

            $parsed_string .= '@';
        }

        if (isset($url['host'])) {
            $parsed_string .= $url['host'];
        }

        if (isset($url['port'])) {
            $parsed_string .= ':' . $url['port'];
        }

        if (!empty($url['path'])) {
            $parsed_string .= $url['path'];
        } else {
            $parsed_string .= '/';
        }

        if (isset($url['query'])) {
            $parsed_string .= '?' . $url['query'];
        }

        if (isset($url['fragment'])) {
            $parsed_string .= '#' . $url['fragment'];
        }

        $new_url = $url;

        return $parsed_string;
    }

}

############################ ANOTHER PHP FUNCTION #############################

if (!defined('HTTP_URL_REPLACE')) {
    define('HTTP_URL_REPLACE', 1);
}
if (!defined('HTTP_URL_JOIN_PATH')) {
    define('HTTP_URL_JOIN_PATH', 2);
}
if (!defined('HTTP_URL_JOIN_QUERY')) {
    define('HTTP_URL_JOIN_QUERY', 4);
}
if (!defined('HTTP_URL_STRIP_USER')) {
    define('HTTP_URL_STRIP_USER', 8);
}
if (!defined('HTTP_URL_STRIP_PASS')) {
    define('HTTP_URL_STRIP_PASS', 16);
}
if (!defined('HTTP_URL_STRIP_AUTH')) {
    define('HTTP_URL_STRIP_AUTH', 32);
}
if (!defined('HTTP_URL_STRIP_PORT')) {
    define('HTTP_URL_STRIP_PORT', 64);
}
if (!defined('HTTP_URL_STRIP_PATH')) {
    define('HTTP_URL_STRIP_PATH', 128);
}
if (!defined('HTTP_URL_STRIP_QUERY')) {
    define('HTTP_URL_STRIP_QUERY', 256);
}
if (!defined('HTTP_URL_STRIP_FRAGMENT')) {
    define('HTTP_URL_STRIP_FRAGMENT', 512);
}
if (!defined('HTTP_URL_STRIP_ALL')) {
    define('HTTP_URL_STRIP_ALL', 1024);
}
