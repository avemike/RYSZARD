<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
    <h1>serwery</h1>
    <?php foreach (($result?:[]) as $server): ?>
        <form action="logintoserver" method="POST">
            <?php if ($server['char_id']): ?>
                
                    <span class="servers green">SERWER <?= ($server['server_id']) ?> - <?= ($server['nickname']) ?> <?= ($server['level']) ?>lv</span>
                    <input type="hidden" name="serverno" value="<?= ($server['server_id']) ?>">
                
                <?php else: ?>
                    <span class="servers error">SERWER <?= ($server['server_id']) ?> - <?= ($server['nickname']) ?> 0lv</span>
                    <input type="hidden" name="serverno" value="<?= ($server['server_id']) ?>">
                
            <?php endif; ?>
        </form>
    <?php endforeach; ?>
    <form action="logout" method="POST">
        <button type="submit">logout</button>
    </form>
    <script>
        const buttons = document.querySelectorAll(".servers")
        for (const button of buttons) {
            button.addEventListener('click', function(event) {
                button.parentNode.submit();
            })
        }
    </script>
</body>
</html>