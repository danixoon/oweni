<?php

function render_header($title)
{
?>
  <title><?php echo $title ?></title>
  <link rel="stylesheet" href="/public/style.css">
  <link rel="stylesheet" href="/public/page.css">
  <meta charset="UTF-8">
  <meta name=viewport content="width=device-width, initial-scale=1.0 ">
  <meta name="description" content="**">
  <link rel=" shortcut icon" href="EMC.svg" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css?family=Play" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,100italic,300,300italic,400,400italic|Open+Sans:300,regular,italic,700&subset=latin,latin-ext,cyrillic,hebrew"
   rel="stylesheet">
  <script src="/public/main.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" 
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" 
  crossorigin="anonymous"></script>
<?php
}

?>