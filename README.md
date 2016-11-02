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