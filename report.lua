-- Report
-- SAPP Compatability: 9.8+
-- Script by: Skylace aka Devieth
-- ffi modual by: 002
-- Discord: https://discord.gg/Mxmuxgm

Main_link = "http://website.com/rac/reporter/discord_report.php?"
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
	register_callback(cb['EVENT_CHAT'], "OnChat")
	timer(1000, "timeout_timer")
end

function GetPage(URL)
    local response = http_client.http_get(URL, false)
    local returning = nil
    if http_client.http_response_is_null(response) ~= true then
        local response_text_ptr = http_client.http_read_response(response)
        returning = ffi.string(response_text_ptr)
    end
    http_client.http_destroy_response(response)
    return returning
end

function OnChat(PlayerIndex, Message)
	local allow = true
	local t = tokenizestring(string.lower(Message))
	if t[1] == "!report" or t[1] == "/report" or t[1] == "\\report" then
		allow = false
		if timeout[PlayerIndex] < 1 then
			if tonumber(t[2]) then
				timeout[PlayerIndex] = timeout_time * 60
				local Suspect = tonumber(t[2])
				local sv_ip = tostring(GetPage("https://api.ipify.org/"))..":"..read_word(0x5A9190)
				local sv_name = string.gsub(get_var(1, "$svname"), [[]], "")
				local R_Name, R_Hash, R_IP = get_name_byte(PlayerIndex), get_var(PlayerIndex, "$hash"), get_var(PlayerIndex, "$ip")
				local S_Name, S_Hash, S_IP = get_name_byte(Suspect), get_var(Suspect, "$hash"), get_var(Suspect, "$ip")
				local Complaint = {}
				for i = 0,#t do
					if i > 2 then
						Complaint[i-2] = t[i] .. "%20"
					end
				end
				local Msg = table.concat(Complaint)
				local report = string.format([[
				%s
				name="%s"
				&sv_ip=%s
				&snitch=%s
				&defendant=%s
				&verify_key=%s
				&snitch_hash=%s
				&snitch_ip=%s
				&defendant_hash=%s
				&defendant_ip=%s
				&snitch_msg=%s]],Main_link ,sv_name, sv_ip, R_Name, S_Name, Key, S_Hash, S_IP, R_Hash, R_IP, Msg)
				local data = GetPage(report)
				say(PlayerIndex, "Thank you! Your report submited!")
			else
				say(PlayerIndex, "Error: You must use a playes number to report them.\nUse the command /pl to find their number.")
			end
		else
			local s, m, h = gettimestamp(timeout[PlayerIndex])
			say(PlayerIndex, "Error: Please wait "..m..":"..s.." before reporting again.")
		end
	end
	return allow
end

function get_name_byte(PlayerIndex)
	local name = get_var(PlayerIndex, "$name")
	local len = string.len(name)
	local Name = {}
	for i = 1,len do
		local char_byte = string.byte(string.sub(name,i,i))
		if i == len then
			Name[i] = char_byte
		else
			Name[i] = char_byte..","
		end
	end
	return table.concat(Name)
end

function timeout_timer()
	for i=1,16 do
		if player_present(i) then
			if timeout[i] then
				if timeout[i] > 1 then
					timeout[i] = timeout[i] - 1
				end
			else
				timeout[i] = 0
			end
		end
	end
	return true
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
	return seconds, minutes, hours
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
