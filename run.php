<?php
include('functions.php');
/**
 * Example Bot with Discord-PHP
 *
 * When a User says "ping", the Bot will reply "pong"
 *
 * Getting a User message content requries the Message Content Privileged Intent
 * @link http://dis.gd/mcfaq
 *
 * Run this example bot from main directory using command:
 * php examples/ping.php
 */

$token = ''; // Put your Bot token here from https://discord.com/developers/applications/
$voiceChannel_id = ''; //Right-click any voice channel and copy the ID
$path_to_tts = '/var/www/html/bots/ai/tts/'; //path to folder where generated .mp3-files are saved, must have permissions to delete the .mp3-files

include __DIR__.'/vendor/autoload.php';

// Import classes, install a LSP such as Intelephense to auto complete imports
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Discord\Voice\VoiceClient;

// Create a $discord BOT
$discord = new Discord([
    'token' => $token, // Put your Bot token here from https://discord.com/developers/applications/
    'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT // Required to get message content, enable it on https://discord.com/developers/applications/
]);

// When the Bot is ready
$discord->on("ready", function () use ($discord) {
	unlink_mp3();
    $discord->on('message', function (Message $message, Discord $discord) {
        // If message is from a bot
        if ($message->author->bot) {
            // Do nothing
            return;
        }
		if(strpos($message->content, '.clear') !== false){
				unlink_mp3();
				$message->reply("Files removed.");
		}elseif(strpos($message->content, '.ai') !== false) {
						$q = substr($message->content, strpos($message->content, ' ') + 1);
						global $voiceChannel_id;
						$voiceChannel = $message->guild->channels->get('id', $voiceChannel_id);
						$message->reply("I'm on my way!" .terminator($q));
						$discord->joinVoiceChannel($voiceChannel, false, false)->then(function (VoiceClient $vc) use ($q, $message, $discord, $voiceChannel) {
						global $path_to_tts;
							// terminator($q);
							$q = str_replace(' ', '_', $q);
							$q = str_replace("'", '_', $q);
							$q = str_replace('"', '', $q);
											
							$files = array_diff(scandir($path_to_tts), array('.', '..'));
							$latest_file = end($files);
							$video_file = $path_to_tts.$latest_file;
							echo 'Now playing: ' . $video_file;
							$vc->playFile($video_file)->then(function () use ($vc,$discord,$voiceChannel,$latest_file) {
							// File is done playing
							unlink_mp3();
							$vc->close();
							$discord->leaveVoiceChannel($voiceChannel);

						});
						$vc->on('end', function () use ($vc,$discord,$voiceChannel,$latest_file) {
						// File is done playing
						unlink_mp3();
						$vc->close();
						$discord->leaveVoiceChannel($voiceChannel);
						echo 'Unlinking files' . PHP_EOL;
					});										
				});															
			}	
		}
    );
});

// Start the Bot (must be at the bottom)
$discord->run();