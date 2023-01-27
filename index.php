<?php

# Environment
# ----------------------------------------------------------------------------------------------------

error_reporting(0);

# Definitions
# ----------------------------------------------------------------------------------------------------

const allowed_audio_types = 
[
	"audio/mpeg",
	"audio/mp3",
	"audio/x-mp3",
	"audio/ogg",
	"audio/wav",
	"audio/x-wav",
];

const max_upload_fize = 52428800;

# Parameters
# ----------------------------------------------------------------------------------------------------

$total_buttons = (!empty($_GET['buttons']) and $_GET['buttons'] <= 129)
	? substr(intval($_GET['buttons']), 0, 3)
	: 48;

$total_columns = (!empty($_GET['columns']) and $_GET['columns'] <= 6)
	? substr(intval($_GET['columns']), 0, 1)
	: 2;

$bg_color = (!empty($_GET['bg_color'])) 
	? substr($_GET['bg_color'], 0, 6) 
	: "2c2e38";

$text_color = (!empty($_GET['text_color'])) 
	? substr($_GET['text_color'], 0, 6) 
	: "ffffff";

$button_color = (!empty($_GET['button_color'])) 
	? substr($_GET['button_color'], 0, 6) 
	: "3c404b";

# Functions
# ----------------------------------------------------------------------------------------------------

function alert ($message = "", $redirect = "")
{

	$redirect = (!empty($redirect))
		? "window.location.href = '{$redirect}';"
		: "history.go(-1);";

	$message = (!empty($message))
		? "alert(\"{$message}\");"
		: "";

	die('<script>' . $message . ' ' . $redirect . '</script>');

}

# POST actions
# ----------------------------------------------------------------------------------------------------

if (!empty($_POST)):

	$action = (!empty($_POST['action']))
		? $_POST['action']
		: "";

	switch ($action):

		# --------------------------------------------------------------------------------------------

		default:

			alert("Invalid action");

		break;

		# --------------------------------------------------------------------------------------------

		case 'remove':

			if (!is_dir("./uploads"))
				alert("Error: Folder '/uploads' not found");

			if (empty(trim($_POST['num'])))
				alert("Error: Button number not provided");

			$num = intval(trim($_POST['num']));
			$file = "./uploads/{$num}.json";

			if (!is_file($file))
				alert("Error: Audio file not found");

			$file_info = json_decode(file_get_contents("./uploads/{$num}.json"));

			unlink("./uploads/{$file_info -> file}");
			unlink($file);

			alert(null, "index.php");

		break;

		# --------------------------------------------------------------------------------------------

		case 'upload':

			if (!is_dir("./uploads"))
				alert("Error: Folder '/uploads' not found");

			if (!is_writable("./uploads"))
				alert("Error: Folder '/uploads' cannot be written!");

			if (empty(trim($_POST['num'])))
				alert("Error: Button number not provided");

			if (empty(trim($_POST['name'])))
				alert("Error: Name not provided");

			$num = intval(trim($_POST['num']));
			$name = preg_replace("/[^A-Za-z0-9 ]/", "", trim($_POST['name']));
			$file = $_FILES['file'];

			if (empty($file['name']))
				alert("Error: File name not found");

			if (empty($file['tmp_name']))
				alert("Error: File not found or invalid");

			if (!empty($file['error']))
				alert("Error: Looks like your server did not accept this file. Check PHP upload limit settings.");

			if (empty($file['size']))
				alert("Error: File seems empty or there's a server error.");

			if (!in_array($file['type'], allowed_audio_types))
				alert("Error: Invalid file type. Allowed files are: " . implode(", ", allowed_audio_types));

			if ($file['size'] > max_upload_fize)
				alert("Error: File exceeds limit of " . max_upload_fize . " bytes");

			$data = file_get_contents($file['tmp_name']);
			$extension = @array_pop(explode(".", $file['name']));

			if (empty($extension))
				alert("Error: File extension not found.");

			if (empty($data))
				alert("Error: File is empty or there's a server error.");

			file_put_contents("./uploads/{$num}.{$extension}", $data)
				or alert("Error: Failure attempting to store file in server. Is the folder writable?");

			file_put_contents("./uploads/{$num}.json", json_encode
			([
				"num" => $num,
				"name" => $name,
				"file" => "{$num}.{$extension}",
			])) or alert("Error: Failure attempting to store file info in server. Is the folder writable?");

			alert(null, "index.php");

		break;

	endswitch;

	exit;

endif;

# ----------------------------------------------------------------------------------------------------


?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Twitch Soundpad</title>
		<style>

			body
			{
				margin: 0;
				padding: 0;
				background-color: #<?=$bg_color?>;
				color: #<?=$text_color?>;
				font-family: Arial;
				overflow-x: hidden;
				-webkit-user-select: none; 
				-ms-user-select: none; 
				user-select: none;
				padding: 0;
			}

			.player
			{
				display: table;
				background-color: rgba(255,255,255,0.1);
				width: 100%;
				margin-bottom: 1px;
				transition: all 0.2s;
			}

				.player.active
				{
					background-color: rgba(255,255,255,0.2);
				}

				.player > .left,
				.player > .right
				{
					display: table-cell;
					padding: 10px;
				}

				.player > .left
				{
					width: 60%;
				}

					.player > .left > label
					{
						font-size: 10px;
					}

					.player > .left #time
					{
						width: 100%;
					}

				.player > .right
				{
					width: 40%;
				}

					.player > .right > label
					{
						font-size: 10px;
					}

					.player > .right #volume
					{
						width: 100%;
					}

			.buttons
			{
				display: inline-block;
				font-size: 0; 
				line-height: 0;
				width: 100%;
			}

				.buttons > .button-holder
				{
					display: inline-block;
					font-size: 0; 
					line-height: 0;
					width: <?=round((100 / $total_columns), 2)?>%;
					padding: 1px;
					box-sizing: border-box;  
				}

					.buttons > .button-holder > button
					{
						width: 100%;
						outline: none;
						padding: 12px 14px;
						font-family: Arial;
						font-size: 12px;
						line-height: 14px;
						background-color: #<?=$button_color?>;
						color: white;
						border: none;
						margin: 0;
						white-space: nowrap;
						overflow: hidden;
						text-align: center;
						vertical-align: middle;
					}

						.buttons > .button-holder > button.active
						{
							background-color: #6441a5;
						}

						.buttons > .button-holder > button:active
						{
							background-color: #6441a5;
						}

		</style>
	</head>
	<body>

		<div class="player" id="player_header">
			<div class="left">
				<audio src="" id="player" style="display: none"></audio>
				<label>
					Time<br>
					<input type="range" id="time" value="0" step="1" oninput="player_set_position()">
				</label>
			</div>
			<div class="right">
				<label>
					Volume
					<input type="range" id="volume" value="1" min="0" max="1" step="0.01" oninput="player_set_volume(this)">
				</label>
			</div>
		</div>

		<div class="buttons">
			<?php for ($n = 1; $n <= $total_buttons; $n++): ?>
				<?php $file_info = (is_file("uploads/{$n}.json")) ? json_decode(file_get_contents("uploads/{$n}.json")) : null ?>
				<div class="button-holder">
					<?php if (!empty($file_info -> name)): ?>
						<button class="play" onclick="file_play(<?=$n?>, this)" ondblclick="file_remove(<?=$n?>)" title="<?=$file_info -> name?>">
							&#x25B6; <?=$file_info -> name?>
						</button>
					<?php else: ?>
						<button class="play" onclick="file_select(<?=$n?>)">
							<span style="opacity: 0.2">&mdash;</span>
						</button>
					<?php endif ?>
				</div>
			<?php endfor ?>
		</div>

		<form action="index.php" method="post" name="upload_form" id="upload_form" enctype="multipart/form-data" style="display: none">
			<input type="file" accept="<?=implode(",", allowed_audio_types)?>" name="file" id="upload_file" value="" onchange="file_send()">
			<input type="hidden" name="name" id="upload_name" value="">
			<input type="hidden" name="num" id="upload_num" value="">
			<input type="hidden" name="action" value="upload">
		</form>

		<form action="index.php" method="post" name="remove_form" id="remove_form" style="display: none">
			<input type="hidden" name="num" id="remove_num" value="">
			<input type="hidden" name="action" value="remove">
		</form>

	</body>
	<script>

		const el_buttons 		= document.querySelectorAll('.play');
		const el_player 		= document.getElementById("player");
		const el_time 			= document.getElementById("time");
		const el_volume 		= document.getElementById("volume");
		const el_upload_form 	= document.getElementById('upload_form');
		const el_upload_file 	= document.getElementById('upload_file');
		const el_upload_num 	= document.getElementById('upload_num');
		const el_upload_name 	= document.getElementById('upload_name');
		const el_remove_form 	= document.getElementById('remove_form');
		const el_remove_num 	= document.getElementById('remove_num');
		const el_player_header 	= document.getElementById('player_header');

		var is_playing = false;

		/* Triggers file selector */

		const file_select = function (num)
		{
			el_upload_form.querySelector('input[name="file"]').click();
			el_upload_num.value = num;
		};

		/* Upload audio file */

		const file_send = function ()
		{

			var num = el_upload_num.value;
			var file = el_upload_file.files.item(0).name;
			var name = prompt("Qual será o nome do arquivo?", file);

			name = name.trim();

			if (!name || !name.length)
			{
				alert("You need to type a name!");
				el_upload_file.value = "";
				return false;
			}

			name = name.substr(0, 24);

			el_upload_name.value = name;
			el_upload_form.submit();

		};

		/* Play audio */

		const file_play = function (num, el_button)
		{

			buttons_inactive();

			if (is_playing)
			{
				is_playing = false;
				el_player.src = '';
				el_player_header.classList.remove('active');
			}
			else
			{
				is_playing = true;
				el_player.src = `./uploads/${num}.mp3`;
				el_player.play();
				el_button.classList.add('active');
				el_player_header.classList.add('active');
			}

		};

		/* Delete audio on double click */

		const file_remove = function (num)
		{

			if (!confirm("Are you sure you want to remove this audio?"))
			{
				return false;
			}

			el_remove_num.value = num;
			el_remove_form.submit();

		};

		/* Mark all buttons as inactive */

		const buttons_inactive = function ()
		{
			for (const button of el_buttons)
			{
				button.classList.remove('active');
			}
		};

		/* Set current audio position */

		const player_set_position = function ()
		{
			el_player.currentTime = el_time.value;
		};

		/* Set volume */

		const player_set_volume = function (el)
		{
			el_player.volume = el.value;
		};

		/* Event: Makes all buttons inactive when player stops */

		el_player.onended = function ()
		{
			is_playing = false;
			el_player_header.classList.remove('active');
			buttons_inactive();
		};

		/* Event: Updates progress range on play */

		el_player.ontimeupdate = function() 
		{

			var time_current = (el_player.currentTime) 
				? Math.round(el_player.currentTime)
				: 0;

			var time_duration = (el_player.duration) 
				? Math.round(el_player.duration)
				: 0;

			el_time.max = time_duration;
			el_time.value = time_current;

		};
		
	</script>
</html>