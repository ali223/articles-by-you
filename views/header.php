<!DOCTYPE html>
<html lang="en">

  <head>
  <base href="/"/>
  <title><?= isset($pageTitle) ? $pageTitle : "Welcome to Articles By U -- Share your thoughts with the world"?></title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="styles.css?<?php echo date('l jS \of F Y h:i:s A');?>" >


    <script>
        function toggleMenu(){
          var x = document.getElementById("mynavbar");
          if (x.className === "mynav") {        
              x.className += " responsive";
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
          <li><a href="/index">Home</a></li>
          <li><a href="/login">Sign in </a></li>
          <li><a href="/registrationform">Register</a></li>
          <li><a href="/search">Search</a></li>
          <li><a href="/contact">Contact me</a></li>
          <li class="icon">
              <a href="javascript:void(0);" onclick="toggleMenu()">&#9776;</a>
            </li>
        </ul>
        </nav>
    </div>

    <div class="main">
     <img class="logo" src="images/logo.png"  />
     </div>



