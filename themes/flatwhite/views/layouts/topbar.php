<?php
     try {
          $menu = Yii::app()->controller->mainMenu;
     } catch (CdbException $e) {
          $menu = [];
     }
     if((sizeof($menu) > 2) || (@$menu[1]['label'] != '')){ //if menu items available open bracket
     
?>
<div class="top-bar" onload="getTime()">
	<div id="dl-menu" class="dl-menuwrapper">
		<button class="dl-trigger c-hamburger c-hamburger--htx">
			<span>toggle menu</span>
		</button>
		<ul class="dl-menu">
		     <?php
		          $this->includeFile('menuheader.php', [
		               'menu' => $menu
		          ]);    
                    echo loopMenu($menu);
		          $this->includeFile('menufooter.php', [
		               'menu' => $menu
		          ]);    
               ?>            
		</ul>
	</div><!-- /dl-menuwrapper -->
	<div class="dl-menuleft">
	     <?php
               $this->includeFile('topcontent.php', ['menu'=> $menu]);   
	     ?>
	</div>
	<div class="dl-menuright">
	    <strong id="jam"></strong>
	    <br/>
	    <strong id="tanggal"></strong>
	</div>
	
</div><!-- /top-bar -->
<?php
	} //if menu items available close bracket
?>


<?php
     function getHeaderMenu(){
          
          return "
               <li id='menu-header'>
                    <h3>Test</h3>
               </li>
          
          ";
     }
     function loopMenu($menu){
          $html = '';
          
          foreach($menu as $k => $v){
               if($k > 1){
                    if($v['label'] == '---'){
                         $html .= '<li class="dl-divider">
			                    </li>';
                    } else {
                         $html .= '<li>';
                         if(isset($v['url'])){
                             $url = is_array($v['url']) ? $v['url'][0] : $v['url'];
                            $url = Yii::app()->createUrl($url);
                            $html .= '<a href="' . $url .'">'.$v['label'].'</a>';	
                         } else {
                              $html .= '<a href="#">'.$v['label'].'</a>';	
                         }
                         // vdump($html);
                         // die();
                         if(isset($v['items'])){
                              $html .=  extractChild($v['items']);
                         }
                         $html .= '</li>';
                    }
               }
          }
          // vdump($html);
          
          return $html;
          
          
     }
     
     function extractChild($item){
          $html = '<ul class="dl-submenu">';
          foreach($item as $k => $v){
               if($v['label'] == '---'){
                    $html .= '<li class="dl-divider">
	                         </li>';
               } 
               else {
                    
                    $html .= '<li>';
                    if(isset($v['url'])){
                        if(is_array($v['url'])){
                            $url = $v['url'][0];
                            $params = $v['url'];
                            unset($params[0]);
                            $url = Yii::app()->createUrl($url, $params);
                            $html .= '<a href="' . $url .'">'.$v['label'].'</a>';	    
                        } else {
                            $url = Yii::app()->createUrl($v['url']);
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
          $html .= '</ul>';
          return $html;
     }

?>