<body>
    
</body>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="ui/css/bootstrap.min.css">
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
                            <p>Nazwa: <?= ($item['item_name']) ?></p>
                            <p>Wartosc: <?= ($item['value']) ?></p>                
                            <?php if ($item['item_description']): ?>
                                <p>Opis: <?= ($item['item_description']) ?></p>                
                            <?php endif; ?>
                        </div>
                    </div>
        
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        // var data=JSON.parse('<?= ($this->raw($data)) ?>');
        var data=document.getElementById('foo').dataset.json;

        console.log(data)
    </script>
</body>
</html>