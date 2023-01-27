# Twitch Soundpad

Simple soundpad tool for Twitch made with PHP

# Installation

Just upload it to your PHP webserver (7.4+) and make sure your uploads folder is writable.

# Parameters

You can easily customize the sound pad appearance and number of buttons. Just pass these parameters via URL:

- `total_buttons` - Customize the number of buttons (max: 128, default: 48)
- `total_columns` - Customize the number of columns (max: 6, default: 3)
- `bg_color` - Change the background color to match your OBS UI (default: #2c2e38)
- `text_color` - Change the text color to match your OBS UI (default: #ffffff)
- `button_color` - Change the button background color to match your OBS UI (default: #3c404b)

# Safety

This is a simple tool intended to be used by a single streamer. This tool is not safe for production environments, as it stores data in the server. 