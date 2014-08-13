<style>
    .embed-container { 
        position: relative; 
        padding-bottom: 55.25%; 
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
    }
</style>
<div class="embed-container">
    <iframe src="<?php echo $this->createUrl('error'); ?>" name="framed" frameborder="0"></iframe>
</div>
