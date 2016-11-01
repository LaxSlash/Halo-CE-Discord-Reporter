# Halo-CE-Discord-Reporter
An utility for users to report users in SAPP-enabled Halo CE gameservers via a webhook.

## Web Installation
### Pre-requisites

* PHP >= 5.3.3
* A Discord Server Webhook
* A webserver that supports PHP cURL

### Steps
1. Upload the files in the /WEB directory to your website where you want the server to go.
2. Configuration
..* Rename config_example.php to config.pho
..* Create your server secret/key. You will need this for your SAPP Lua scipt configuration.
..* Save the file
..* Go to http://www.yoursitehere.com/discord_report.php?name="Test Server"&sv_ip=123.123.123.123:1234&snitch="New001"&defendant="New002"&verify_key=thisIsSomeKey&snitch_hash=lettershere&snitch_ip=123.123.123.123&snitch_msg="Some message goes here."&defendant_hash=someMoreLettersGoHere&defendant_ip=123.123.123.123
..* A webhook message should have been posted. If it was, your website is correctly setup. At this point, you can turn on the extra security settings if youwish to.

### Security
Make sure that you do not give your verification key out to anyone. If you give this out, whoever has it and the URL will be able to spam your Discord channel.