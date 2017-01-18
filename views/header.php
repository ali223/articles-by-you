<?php

spl_autoload_register();

?>
<!DOCTYPE html>
<html lang="en">

<head>
<!--<base href=" http://localhost/cleansite/ "/> -->
<title><?= isset($pageTitle) ? $pageTitle : "Welcome to Articles By U -- Share your thoughts with the world"?></title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">


<!--<link href="https://fonts.googleapis.com/css?family=Josefin+Sans" rel="stylesheet">-->

<link rel="stylesheet" type="text/css" href="styles.css?<?php echo date('l jS \of F Y h:i:s A');?>" >


<script language="Javascript">
    function toggleMenu(){
        //alert("hello");
    var x = document.getElementById("mynavbar");
    if (x.className === "mynav") {        
        x.className += " responsive";
        //alert(x.className);
    } else {
        x.className = "mynav";
    }
    }
</script>
    
</head>

<body>

<div class="container">
<div class="topnav">

<nav>

<ul class="mynav" id="mynavbar">
<li><a href="index.php?controller=posts&action=index">Home</a></li>

<li><a href="index.php?controller=users&action=login">Sign in </a></li>

<li><a href="index.php?controller=users&action=userregistrationform">Register</a></li>

<li><a href="index.php?controller=posts&action=search">Search</a></li>

<li><a href="index.php?controller=pages&action=contact">Contact me</a></li>

<li class="icon">
    <a href="javascript:void(0);" onclick="toggleMenu()">&#9776;</a>
  </li>
</ul>

</nav>
</div>

<div class="main">


 <img class="logo" src="images/logo.png"  />
<!-- <p><em>Warning</em>: I am coding this website to learn PHP. It is not a professional website. Please do not enter your personal information
on this website. </p>
-->
 <!-- <p class="slogan"> Share your thoughts with the world.</p>-->

<!-- <img class="bgimage" src="images/open-book.jpeg" />-->

 </div>



