<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Guestbook</h1>

    <?php
        if(!empty($posts)):
            foreach ($posts as $post): ?>
    <div>
        <h2><?php echo htmlspecialchars($post['name']); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($post['message'])); ?></p>
        <small>Posted at: <?php echo htmlspecialchars($post['posted_at']); ?></small>
    </div>
    <?php
            endforeach;
        endif;    
    ?>
</body>

</html>