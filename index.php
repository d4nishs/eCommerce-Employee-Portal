<?php

    // eCommerce Employee Portal
    // Written by Danish Siddiqui
    // Last Updated: 11/27/2020
    // index.php


    // Ensure user is connected to https:
    if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' ||
   $_SERVER['HTTPS'] == 1) ||
   isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
   $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
    }


    session_start();

    // If email is not set in session:
    // Lead user to login page:
    if (empty($_SESSION['email'])) {
        echo '
			<html>

			<head>
			  <meta charset="UTF-8">
			  <title>eCommerce Management System</title>
			  <meta name="viewport" content="initial-scale=1.0, width=device-width" />
			  <link rel="stylesheet" href="css/style.css">
				<script src="js/index.js"></script>
			</head>

			<body class ="index">
			  <div class="login_form">

				  <section class="login-wrapper">

				    <div class="logo">
				    </div>

						<form action="processing/logIn.php" method="POST">

					      <input placeholder= "Email" name="email" type="text" autocapitalize="off" autocorrect="off" required />
					      <input placeholder = "Password" name="pass" type="password" required />
					      <button name="submit" type="submit">Sign In</button>
					  	</form>

					 </section>

				 </div>


			</body>

			</html>
			';
    }

    // If user is logged in and email is set.
    // Lead user to the dashboard of the user.
    else {
        $email = $_SESSION['email'];
        header("Location: dash.php", true);
        exit();
    }
