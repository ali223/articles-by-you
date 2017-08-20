
<div class="content">
	<div class="panel">    
	    <h3 class="headinglogout"><?= isset($logoutMessage) ? $logoutMessage : null; ?></h3>

		<?php

		if(!empty($errorMessages)) {

		    echo '<ul class="errorslist">';

		    foreach($errorMessages as $singleError) {

		     echo "<li>$singleError</li>";        

		    }

		   echo '</ul>';

		}

		?>

		<form name="loginform" method="post" action="/login">
			<table class="logintable" border="1">
				<tr>
					<td>User Name</td>
					<td><input type="text" name="txtusername" value="" /></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" name="txtuserpassword" /></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="Sign in" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>