# DiscordPHP GPT-3 with Voicerss
A DiscordPHP bot with GPT-3 AI and Voicerss implementation

By using https://api.openai.com/v1/engines/text-davinci-003/completions and voicerss-text-to-speech, we get a funny take on AI, having the AI talk back to you with the generated text.

# Test it live
https://discord.gg/M8ufRPa8q6
(due to limited hosting capabilities, bot might be offline)

# Installation
*  Clone repo to wherever or download directly
*  Edit run.php and functions.php, add your tokens
*  Install FFMPEG locally
*  Invite your discordbot your server
*  Have atleast php8.0 installed


# Usage
* Start: sudo php run.php
* Type into your discord-channel: .ai what does color red taste like?
* Bot will now start AI generating an answer for your question, then use Voicerss to generate text-to-speech based on AI's answer
* Bot will now join selected voice channel, start talking then leave the voice channel
* Bot will remove the generated .mp3-file from the system

# Discord
* Crille#6623


# Credits
* DiscordPHP
* OpenAI GPT-3
* Voicerss
