<body>
    
</body>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="ui/css/bootstrap.min.css">
    <link rel="stylesheet" href="ui/css/itemShop.css">

    <title>Document</title>
    <style>
        .error{ 
            color: red;
        }
        .green{
            color: green;
        }
    </style>
</head>
<body>
    <div id="foo" data-json="<?= ($items_to_buy) ?>" style="display: none;"></div>
    <?php echo $this->render('./upperPanel.html',NULL,get_defined_vars(),0); ?>
    <div class="container">
        <div class="row">
            <?php echo $this->render('./leftMenu.html',NULL,get_defined_vars(),0); ?>
            <div class="col-md-9">
                <?php foreach (($items_to_buy?:[]) as $item): ?>
                    <div class="col-md-4">
                        <div class="item">
                            <img src="ui/images/items/<?= ($item['item_icon']) ?>.png" alt="">
                            <div class="item-info">
                                <p>Nazwa: <?= ($item['item_name']) ?></p>
                                <p>Wartosc: <?= ($item['value']) ?></p>                
                                <?php if ($item['item_description']): ?>
                                    <p>Opis: <?= ($item['item_description']) ?></p>                
                                <?php endif; ?>
                                <p>Siła: <?= ($item['strength']) ?></p>            
                                <p>Życie: <?= ($item['hp']) ?></p>                
                                <p>Zręczność: <?= ($item['dex']) ?></p>                
                                <p>Intelekt: <?= ($item['intelligence']) ?></p>                
                                <p>Każdy atrybut: <?= ($item['every_attrib']) ?></p>                

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        // mouse over img of item makes item-info visible
        // const allItems = document.querySelectorAll('.item');
        // [...allItems].map( item => {
        //     const itemInfo = item.querySelector('.item-info'); 
            
        //     item.addEventListener('mouseover', e => {
        //         itemInfo.style.left = e.clientX;
        //         itemInfo.style.top = e.clientY;
 
        //         itemInfo.classList.add('item-info--active');
        //     })
        //     item.addEventListener('mouseleave', e => {
        //         itemInfo.classList.remove('item-info--active');
        //     })
        // })
        // document.querySelectorAll('.item').addEventListener('mouseover', (e) => { 
    </script>
</body>
</html>