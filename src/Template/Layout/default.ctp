<?php
$cakeDescription = 'Library';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
<!-- Add Bootstrap CSS -->



<!-- new links -->






    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['bootstrap.min.css']) ?>
    <?= $this->Html->script(['bootstrap.min.js']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<nav class="navbar navbar-light bg-light">
  <a class="navbar-brand" href="#">
    <img src="img/book.jpg" width="40" height="40" alt="">
    Library Books
  
  </a>
</nav>
<script>
    var csrfToken = '<?= $this->request->getAttribute('csrfToken') ?>';
</script>

    <?= $this->Flash->render() ?>
    <div class="container clearfix">
        <?= $this->fetch('content') ?>
    </div>
    <footer style="margin-bottom:100%">
        <!-- <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2)">
            © 2020 Copyright:
            <a class="text-white" href="https://books.com/">Libraryzbooks.com</a>
        </div> -->
    </footer>

    <?= $this->Flash->render() ?>
</body>
</html>
