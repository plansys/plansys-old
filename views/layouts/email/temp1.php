<html>
    <head>
        <title> Template Title </title>
    </head>
    <body>
        <div style='width:70%;height:80%;border:1px solid #bbb;display:block;margin:auto'>
            <div style='width;100%;padding:2% 2%;border-bottom:1px solid #bbb'>
                <?=EmailBuilder::text[0]?>
            </div>
            <div style='width:100%;height:70%;padding:2%;display:block'>
                <img src='<?=EmailBuilder::img[0]?>'>
                <img src='<?=EmailBuilder::img[0]?>' width='50'>
            </div>
            <div style='width;100%;padding:2% 2%;border-top:1px solid #bbb'>
                <?=EmailBuilder::text[2]?>
            </div>
        </div>
    </body>
</html>
		