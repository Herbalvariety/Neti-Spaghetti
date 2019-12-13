<?php
session_start();
if(isset($_POST['sign_out'])) {
	session_destroy();
	header('Refresh:0');
	exit();
}

$file_active_rules = "active_rules.txt";
$file_tobe_approved = "tobe_approved.txt";

$fh = fopen($file_active_rules, "r");
$file_raw = fread($fh, filesize($file_active_rules));
$active_rules_vals = explode(PHP_EOL, $file_raw);
foreach($active_rules_vals as $entry => $value) {
	$active_rules_vals[$entry] = explode(";", $value);
}
fclose($fh);
array_pop($active_rules_vals);

$fh = fopen($file_tobe_approved, "r");
$file_raw = fread($fh, filesize($file_tobe_approved));
$tobe_approved_vals = explode(PHP_EOL,$file_raw);
foreach($tobe_approved_vals as $entry => $value) {
	$tobe_approved_vals[$entry] = explode(";",$value);
}
//print_r($tobe_approved_vals);
fclose($fh);
array_pop($tobe_approved_vals);
?>

<!DOCTYPE html>
<html>
<head>
     <title>
          Neti Spaghetti
     </title>
     <link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>

     <?php
	echo '<header><h1 id="logo_home"><a href="index.php">Neti Spaghetti</a></h1>';
	if(!isset($_SESSION['loggedIn'])) {
		echo '</header><div id="signin_bg"><form action="index.php" method="post"><h3 align="center" id="signin_txt">Username</h3><input type="text" name="username" id="signin_form" align="middle" /><h3 align="center" id="signin_txt">Password</h3><input type="password" name="password" id="signin_form" align="middle" /><input type="submit" value="OK" id="signin_okbut" /></form>';
	} else {
		echo '<form method="post"><button type="submit" name="sign_out" class="signout_but">Sign Out</header>';
		echo '<h3 id="logged_in_as">Logged in as: ' . $_SESSION['userName'] . '</h3>';
	}
	if(isset($_POST['password'])) {
		if(empty($_POST['username'])) {
			echo '<div id="wrong_password"><p>Please supply all fields!</p></div>';
		} elseif($_POST['password'] === "1234" && $_POST['username'] === "admin") {
			session_start();
			$_SESSION['adminUser'] = TRUE;
			$_SESSION['loggedIn'] = TRUE;
			$_SESSION['userName'] = "admin";
			header('Refresh:0');
		} elseif($_POST['password'] === "4321" && $_POST['username'] === "user") {
			session_start();
			$_SESSION['normalUser'] = TRUE;
			$_SESSION['loggedIn'] = TRUE;
			$_SESSION['userName'] = "user";
			header('Refresh:0');
		} else {
			echo '<div id="wrong_password"><p>Wrong password or username</p></div>';
		}
	}
	if($_SESSION['adminUser'] == TRUE) {
		echo '<div id="content_parent">
			<div id="main_left">
			<h3 align="center">Create a new rule</h3>
			<form method="post">
			<select name="direction">
				<option value="incoming">Incoming</option>
				<option value="outgoing">Outgoing</option>
			</select>
			<select name="protocol">
				<option value="tcp">TCP</option>
				<option value="udp">UDP</option>
				<option value="both">Both</option>
			</select>
			<label for="portnumber"> Portnummer (1-65535):</label>
			<input type="number" id="portnumber" name="portnumber" min="1" max="65535">
			<select name="traffic">
				<option value="Deny">Deny</option>
				<option value="Allow">Allow</option>
				<input type="submit" value="Submit" name="new_rule_submit">
			</select>
			</form><br>
			</div>
			<div id="main_right">
			<h3 align="center">Currently active rules</h3>
			<table align="center" id="tobe_approved_table">
				<tr>
					<th>Direction</th>
					<th>Protocol</th>
					<th>Port Number</th>
					<th>Action</th>
				</tr>';
			foreach($active_rules_vals as $line) {
				echo '<tr>';
				foreach($line as $value) {
					echo '<td>'.$value.'</td>';
				}
				echo '</tr>';
			}
			echo '</table></div>
			<div id="main_bot">
			<h3 align="center">Rules to be approved</h3>
			<table align="center" id="tobe_approved_table">
				<tr>
					<th>Direction</th>
					<th>Protocol</th>
					<th>Port Number</th>
					<th>Action</th>
				</tr>';
		foreach($tobe_approved_vals as $line) {
			echo '<tr>';
			foreach($line as $value) {
				echo '<td>'.$value.'</td>';
			}
			echo '</tr>';
		}
		echo '</table>';

		echo '<form method="post">
			<select name="approve_dropdown">';
		$i = 1;
		foreach($tobe_approved_vals as $value) {
			echo '<option value="'.$i.'">'.$i.'</option>';
			$i++;
		}
		echo '</select><button type="submit" name="approve_ok">Approve</button></div></div>';
		/*echo '<div id="content_parent"><p>User login successful</p></div>';*/
		if(!empty($_POST['approve_dropdown'])) {
			$approve_choice = (int)$_POST['approve_dropdown'];
			array_push($active_rules_vals, $tobe_approved_vals[($approve_choice-1)]);
			array_splice($tobe_approved_vals, $approve_choice, 2);
			$fh = fopen($file_active_rules, "r+");
			foreach($file_active_rules as $line) {
				$tmp_val = implode(";", $line);
				$tmp_val = $tmp_val."\n";
				fwrite($fh, $tmp_val);
			}
			fclose($fh);
			$fh = fopen($file_active_rules, "r+");
			foreach($file_tobe_approved as $line) {
				$tmp_val = implode(";", $line);
				$tmp_val = $tmp_val."\n";
				fwrite($fh, $tmp_val);
			}
			fclose($fh);
			//header('Refresh:0');
		}
	}
	elseif($_SESSION['normalUser'] == TRUE) {
		echo '<div id="content_parent">
			<div id="main_left">
			<h3 align="center">Create a new rule</h3>
			<form method="post">
			<select name="direction">
				<option value="incoming">Incoming</option>
				<option value="outgoing">Outgoing</option>
			</select>
			<select name="protocol">
				<option value="tcp">TCP</option>
				<option value="udp">UDP</option>
				<option value="both">Both</option>
			</select>
			<label for="portnumber"> Portnummer (1-65535):</label>
			<input type="number" id="portnumber" name="portnumber" min="1" max="65535">
			<select name="traffic">
				<option value="Deny">Deny</option>
				<option value="Allow">Allow</option>
				<input type="submit" value="Submit" name="new_rule_submit">
			</select>
			</form><br>
			</div>
			<div id="main_right">
			<h3 align="center">Currently active rules</h3>
			</div>
			</div>';
		/*echo '<div id="content_parent"><p>User login successful</p></div>';*/
	}

	// Create new rule
	if(isset($_POST['new_rule_submit']) && !empty($_POST['portnumber'])) {
		$direction = $_POST['direction'];
		$protocol = $_POST['protocol'];
		$portnumber = $_POST['portnumber'];
		$traffic = $_POST['traffic'];
		$rule_params = $direction . ";" .  $protocol . ";" .  $portnumber . ";" .  $traffic . "\n";
		$fh = fopen($file_tobe_approved, "a+");
		fwrite($fh, $rule_params);
		fclose($fh);
		header('Refresh:0');
	} elseif(isset($_POST['new_rule_submit']) && empty($_POST['portnumber'])) {
		// params supplied are invalid
		echo '<script language="javascript">confirm("Port number is missing!");</script>';
	}
	?>
</body>
</html>
