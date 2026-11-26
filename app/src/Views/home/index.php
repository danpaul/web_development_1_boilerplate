<?php
use Contentful\RichText\Renderer;

/** @var \App\ViewModels\HomeViewModel $vm */

/*
    * Accessing Contentful fields
    * ---------------------------
    * $vm->content is a Contentful Entry object (or null if not found).
    * Read a field with $vm->content->get('fieldId').
    * Empty fields return null, so check before using them.
    *
    * Simple text field "name" (returns a string):
    *
    *     echo htmlspecialchars($vm->content->get('name') ?? '');
    *
    * Image field "photo" (returns a Contentful\Delivery\Resource\Asset):
    * The URL starts with "//images.ctfassets.net/...", so add "https:" in front.
    * Echo these (with htmlspecialchars) into the img tag's src and alt attributes.
    *
    *     $photo = $vm->content->get('photo');
    *     if ($photo !== null) {
    *         $src = 'https:' . $photo->getFile()->getUrl();
    *         $alt = $photo->getTitle() ?? '';
    *     }
    *
    * Rich text field "bio" (returns a Contentful\RichText\Node\Document):
    * The Renderer turns it into HTML and escapes the text itself,
    * so do NOT wrap it in htmlspecialchars or the tags will show as text.
    *
    *     $bio = $vm->content->get('bio');
    *     $renderer = new Renderer();
    *     if ($bio !== null) {
    *         echo $renderer->render($bio);
    *     }
    *
    * See the body of this file for these examples in use.
    */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KAIRO // Indie Electro Pop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&family=Syne:wght@700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>

<body class="home">

    <main class="container">
        <?php if ($vm->content === null) { ?>
        <p>Content not found.</p>
        <?php } else { ?>
        <?php
        $name = $vm->content->get('name');
        $photo = $vm->content->get('photo');
        $bio = $vm->content->get('bio');
        $renderer = new Renderer();
        ?>

        <!-- Featured Image (Hero) -->
        <?php if ($photo !== null) { ?>
        <div class="hero-wrapper">
            <img class="hero-image" src="<?php echo htmlspecialchars('https:' . $photo->getFile()->getUrl()); ?>"
                alt="<?php echo htmlspecialchars($photo->getTitle() ?? ''); ?>" />
        </div>
        <?php } ?>

        <!-- Name & Bio -->
        <section class="content">
            <h1 class="artist-name"><?php echo htmlspecialchars($name ?? ''); ?></h1>
            <?php if ($bio !== null) { ?>
            <div class="artist-bio">
                <?php echo $renderer->render($bio); ?>
            </div>
            <?php } ?>
        </section>
        <?php } ?>
    </main>

</body>

</html>