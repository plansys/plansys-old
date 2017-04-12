
<?php
     try {
          $menu = Yii::app()->controller->mainMenu;
     } catch (CdbException $e) {
          $menu = [];
     }
     
     if(sizeof($menu) > 2){ //if menu items available open
     
?>
<div class="top-bar">
	<div id="dl-menu" class="dl-menuwrapper">
		<button class="dl-trigger c-hamburger c-hamburger--htx">
			<span>toggle menu</span>
		</button>
		<ul class="dl-menu">
		     <?php
		          if(file_exists(Setting::getRootPath() . "/app/theme/flatwhite/menuheader.php")){
		               include(Setting::getRootPath() . "/app/theme/flatwhite/menuheader.php");
		          } else {
		               include('menuheader.php');     
		          }
                    echo loopMenu($menu);
                    if(file_exists(Setting::getRootPath() . "/app/theme/flatwhite/menufooter.php")){
		               include(Setting::getRootPath() . "/app/theme/flatwhite/menufooter.php");
		          } else {
		               include('menufooter.php');     
		          }
                    
               ?>            
		</ul>
	</div><!-- /dl-menuwrapper -->
	<?php
     	} //if menu items available closed
	?>
</div><!-- /top-bar -->


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
                              if(is_array($v['url'])){
                              	$html .= '<a href="' . Yii::app()->getBaseUrl() . '/index.php?r=' . $v['url'][0].'">'.$v['label'].'</a>';	
                              } else {
                              	$html .= '<a href="' . Yii::app()->getBaseUrl() . '/index.php?r=' . $v['url'].'">'.$v['label'].'</a>';
                              }     
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
                              $html .= '<a href="' . Yii::app()->getBaseUrl() . '/index.php?r=' .  $v['url'][0].'">'.$v['label'].'</a>';          
                         } else {
                              
                              $html .= '<a href="'.Yii::app()->getBaseUrl() . '/index.php?r=' . $v['url'].'">'.$v['label'].'</a>';          
                              
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