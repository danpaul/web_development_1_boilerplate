<?php
/** @var \App\ViewModels\ArticlesViewModel|\App\ViewModels\ArticleViewModel $model */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($model->title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2.0.6/css/pico.classless.min.css">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<header>
    <h1><?= htmlspecialchars($model->title) ?></h1>
</header>
