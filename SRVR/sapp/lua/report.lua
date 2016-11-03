-- Report
-- SAPP Compatability: 9.8+
-- Script by: Skylace aka Devieth
-- ffi modual by: 002
-- Discord: https://discord.gg/Mxmuxgm

Main_link = "http://website.com/reporter/discord_report.php?"
Key = "authkey"

timeout_time = 5 -- Timeout after a report before the player can send another report.

timeout = {}

api_version = "1.10.0.0"

ffi = require("ffi")
ffi.cdef [[
    typedef void http_response;
    http_response *http_get(const char *url, bool async);
    void http_destroy_response(http_response *);
    void http_wait_async(const http_response *);
    bool http_response_is_null(const http_response *);
    bool http_response_received(const http_response *);
    const char *http_read_response(const http_response *);
    uint32_t http_response_length(const http_response *);
	]]

http_client = ffi.load("lua_http_client")

function OnScriptLoad()
	server_ip = read_string(0x006260F0) -- -ip argument (or current ip) in shortcut.
	server_port = read_word(0x5A9190)-- -port argument (or current port) in shortcut.
	server_name = get_byte_string(string.gsub(get_var(1, "$svname"), [[]], "")) -- Get the server name and remove spacer.
	register_callback(cb['EVENT_CHAT'], "OnChat")
	timer(1000, "timeout_timer")
end

function OnChat(PlayerIndex, Message)
	local allow, t = true, tokenizestring(string.lower(string.gsub(Message, "\\", "/")))
	if t[1] == "/report" then
		allow = false
		if tonumber(t[2]) then
			command_report(PlayerIndex, t)
		else
			say(PlayerIndex, "Syntax Error: /report [ID] <Message>\nUse /pl to get player ID list.")
		end
	end
	return allow
end

function command_report(PlayerIndex, t)
	if timeout[PlayerIndex] < 1 then -- Can they make a report?
		local SuspectIndex = tonumber(t[2])
		if player_present(SuspectIndex) then -- Is that PlayerIndex currently ocupied?
			if PlayerIndex ~= tonumber(SuspectIndex) then -- Are they trying to report theselves?
				timeout[PlayerIndex] = timeout_time * 60
				local R_Name, R_Hash, R_IP = get_byte_string(getname(PlayerIndex)), get_var(PlayerIndex, "$hash"), get_var(PlayerIndex, "$ip")
				local S_Name, S_Hash, S_IP = get_byte_string(getname(SuspectIndex)), get_var(SuspectIndex, "$hash"), get_var(SuspectIndex, "$ip")
				if t[3] == nil then t[3] = "***No message given.***" end
				local Message = get_byte_string(assemble(t, 2, " "))-- After the second word form the message.
				local report = string.format([[
				%s
				mode=report
				&sv_name=%s
				&sv_ip=%s:%s
				&snitch=%s
				&defendant=%s
				&verify_key=%s
				&snitch_hash=%s
				&snitch_ip=%s
				&defendant_hash=%s
				&defendant_ip=%s
				&snitch_msg=%s]],Main_link ,server_name, server_ip, server_port, R_Name, S_Name, Key, S_Hash, S_IP, R_Hash, R_IP, Message)
				local response = GetPage(report)
				say(PlayerIndex, "Your report has been submited!")
			else
				say(PlayerIndex, "Error: You cannot report yourself.")
			end
		else
			say(PlayerIndex, "Error: Player slot "..t[2].." is currently vacant.")
		end
	else
		local s, m, h = gettimestamp(timeout[PlayerIndex])
		say(PlayerIndex, "Error: Please wait "..m..":"..s.." before reporting again.")
	end
end

function get_byte_string(String)
	local len = string.len(String)
	local bytes = {}
	for i = 1,len do
		local char_byte = string.byte(string.sub(String,i,i))
		if i == len then
			bytes[i] = char_byte
		else
			bytes[i] = char_byte..","
		end
	end
	return table.concat(bytes)
end

function assemble(t, start, spacer)
	local words = {}
	for i = 1,#t do
		if i > start then
			if i == #t then
				words[i-2] = t[i]
			else
				words[i-2] = t[i] .. spacer
			end
		end
	end
	return table.concat(words)
end


function getname(PlayerIndex)
	if player_present(PlayerIndex) then
		local name = get_var(PlayerIndex, "$name")
		if name then
			return name
		end
	end
	return nil
end


function tokenizestring(inputstr, sep)
	if sep == nil then
		sep = "%s"
	end
	local t={} ; i=1
	for str in string.gmatch(inputstr, "([^"..sep.."]+)") do
		t[i] = str
		i = i + 1
	end
	return t
end


function gettimestamp(seconds)
	if seconds < 10 then
		seconds = "0"..math.floor(seconds)
	elseif seconds > 59 then
		minutes = math.floor(seconds / 60)
		seconds = seconds - (minutes * 60)
		if seconds < 10 then seconds = "0"..math.floor(seconds) end
		if minutes < 10 then minutes = "0"..math.floor(minutes) end
		if tonumber(minutes) > 59 then
			hours = math.floor(tonumber(minutes) / 60)
			minutes = tonumber(minutes) - (hours * 60)
			if tonumber(minutes) < 10 then minutes = "0"..math.floor(minutes) end
			if hours < 10 then hours = "0"..math.floor(hours) end
		end
	end
	if hours then else hours = "00" end
	if minutes then else minutes = "00" end
	return hours, minutes, seconds
end

function GetPage(URL)
    local response = http_client.http_get(URL, true)
    local returning = nil
    if http_client.http_response_is_null(response) ~= true then
        local response_text_ptr = http_client.http_read_response(response)
        returning = ffi.string(response_text_ptr)
    end
    http_client.http_destroy_response(response)
    return returning
end

function timeout_timer()
	for i=1,16 do
		if player_present(i) then
			if timeout[i] then
				if timeout[i] >= 1 then
					timeout[i] = timeout[i] - 1
				end
			else
				timeout[i] = 0
			end
		end
	end
	return true
end
