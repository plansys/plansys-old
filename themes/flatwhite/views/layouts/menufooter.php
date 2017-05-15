<li class="dl-divider"></li>
<?php 
    
    function loopMenuFooter($menu){
          $html = '';
          foreach($menu as $k => $v){
                if($v['label'] == '---'){
                     $html .= '<li class="dl-divider">
		                    </li>';
                } else {
                     $html .= '<li>';
                     if(isset($v['url'])){
                        
                        if(is_array($v['url'])){
                            $url = $v['url'][0];
                            $params = $v['url'];
                            unset($params[0]);
                            $url = Yii::app()->createUrl($url, $params);
                            $html .= '<a href="' . $url .'">'.$v['label'].'</a>';	    
                        } else {
                            $url = Yii::app()->createUrl($url);
                            $html .= '<a href="' . $url .'">'.$v['label'].'</a>';	    
                        }
                        
                     } else {
                          $html .= '<a href="#">'.$v['label'].'</a>';	
                     }
                     if(isset($v['items'])){
                          $html .=  extractChild($v['items']);
                     }
                     $html .= '</li>';
                }
          }
          return $html;
     }
     
     echo loopMenuFooter($menu[1]['items']);
?>


