# Halo-CE-Discord-Reporter
A utility for users to report users in SAPP-enabled Halo CE gameservers via a webhook.

## Web Installation

### Pre-requisites
* PHP >= 5.3.3
* A Discord Server Webhook in the desired channel where your reports should go
* A webserver that supports PHP cURL

### Steps
1. Upload the files in the /WEB directory to your website where you want the reporter utility to go.
2. Configuration
  * Rename config_example.php to config.pho
  * Create your server secret/key. You will need this for your SAPP Lua scipt configuration.
  * Save the file

### Security
Make sure that you do not give your verification key out to anyone. If you give this out, whoever has it and the URL will be able to spam your Discord channel.

## Lua Script Installation

### Pre-requisites
* SAPP Lua API must be version 1.10.0.0
* SAPP Version: 9.8+
* SAPP HTTP Client created by 002 (Obtainable either in the dependencies folder, or at http://opencarnage.net/index.php?/topic/5998-sapp-http-client/)
* Other dependencies in the "SRVR/dependencies" folder (Place these into your dedicated server .exe folder)

### Configuration
1. In the LUA Script, set the Main_link to be the www location where you uploaded your reporter utility.
2. Also in the lua script, set the Key to be the secret key you had set in your web configuration file.
3. timeout_time can be set in minutes. Setting this value to 0 is not reccommended at all, as rapid fire reports can get your webhook and server IP Address blacklisted by Discord.

### Usage
To send a report, simply run the command "/pl" to get the target player's number, and then run "/report # [reason]" to submit the report.

# Support and Suggestions:
For support and suggestions for the script, please submit an issue on the Git Repo (https://github.com/LaxSlash/Halo-CE-Discord-Reporter).

# Credits:
PHP Script: «DG»MyHogs (LaxSlash)

Lua Script: =DG=Devieth (Skylace)

# License:
The web script and the Lua script are released under the GPL-3.0 license. All dependencies are released under their respective licensing terms and conditions,
and are the property of their respective authors and developers.