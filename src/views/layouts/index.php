<?php /* @var $content */ ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Meridian stats</title>
    <link href="../../../public/css/app2.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>

<?php if ($_SESSION['user']) : ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-dark mb-3">
        <div class="container-fluid">
            <div class="container nav-items-wrapper">
                <div class="left-side w-50 d-flex justify-content-start">
                    <div class="div brand-logo-wrapper">
                        <img class="brand-logo-img" src="../../../public/images/soccer-ball-stars-light.png" alt="">
                    </div>
                </div>
                <div class="right-side w-50 d-flex justify-content-end">
                    <div class="exit-link-wrapper">
                        <a class="navbar-item exit-link" href="/logout">Вийти</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
<?php endif; ?>

<div id="app" class="main">
    <div class="content-wrapper h-100 container">
        <?= $content ?>
    </div>
</div>

<script src="../../../public/js/app2.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>
</html>