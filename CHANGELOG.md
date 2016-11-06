# CHANGELOG

## Version 1.0.0 BETA 3
  - **[ENHANCEMENT]** Change display of webhook in Discord (PHP)
  - **[CHANGE]** Minor lingual changes (PHP)
  - **[FIX]** Discord syntax should now at least be partially escaped in Strings (PHP/Lua)
  - **[FIX]** Fixed a bug where players that quit then returned could no longer submit any reports until SAPP was reloaded (Lua)

## Version 1.0.0 BETA 2
  - **[NEW]** Added coloring for the webhook embeds based off of the server IP:Port and mode. A default is also settable. (PHP)
  - **[NEW]** Added a notify mode for things like bans, aimbot scores, kicks, etc. (PHP/Lua)
  - **[ENHANCEMENT]** Clean up code, add into function files. (PHP)
  - **[ENHANCEMENT]** Use a $data array for all of the variables. (PHP)
  - **[ENHANCEMENT]** Webhook URLs are now handled based on IP and Mode. A default is also settable (PHP)
  - **[ENHANCEMENT]** The Report URL is also now required by the PHP Script. (PHP)
  - **[ENHANCEMENT]** The name vairable should now be sent as sv_name from the Lua script into the URL. (PHP/Lua)
  - **[CHANGE]** The config file is now in the /includes directory. (PHP)
  - **[FIX]** Send and parse the report message and server name as a string of bytes to avoid a server crash when a report message contains special characters. (PHP/Lua)

## Version 1.0.0 BETA 1
  - Initial Release