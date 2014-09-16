<style>
    .embed-container { 
        position: relative; 
        padding-bottom: 60.25%; 
        height: 0; 
        overflow: hidden; 
        -webkit-overflow-scrolling:touch;
        max-width: 100%; 
        height: auto; 
    } 
    .embed-container iframe, .embed-container object, .embed-container embed { 
        position: absolute;
        width: 100%; 
        height: 100%;
        overflow:hidden;
        margin:-4px -5px 0px -5px;
    }
</style>
<div class="embed-container">
    <iframe src="<?php echo $server;?>" name="iframe" id="jasper" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>