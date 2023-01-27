# Twitch Soundpad

Simple SFX audio player built with JavaScript and PHP.

# Installation

Just upload it to your PHP webserver (7.4+) and make sure your uploads folder is writable.
It can be easily integrated with OBS as a Browser Dock via `Docks > Custom Browser Docks` menu. It also beeps when someone gets in/out.
If you don't know how to add a Custom Browser Dock, [check out this video](https://youtu.be/ItFeV8TimxE?t=82).

# Parameters

You can easily customize the sound pad appearance and number of buttons. Just pass these parameters via URL:

- `buttons` - Customize the number of buttons (max: 128, default: 48)
- `columns` - Customize the number of columns (max: 6, default: 3)
- `bg_color` - Change the background color to match your OBS UI (default: #2c2e38)
- `text_color` - Change the text color to match your OBS UI (default: #ffffff)
- `button_color` - Change the button background color to match your OBS UI (default: #3c404b)

# Safety

This is a simple tool intended to be used by a single streamer. This tool is not safe for production environments, as it stores data in the server. 