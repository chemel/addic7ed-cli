# addic7ed-cli

Download subtitles from www.addic7ed.com

## Install

```bash
composer global require alc/addic7ed-cli

# Make sure you have export PATH in your ~/bashrc
export PATH=~/.config/composer/vendor/bin:$PATH
```

## Usage

```bash
# Find subtitles for all videos in the current directory
addic7ed-cli get
# Filter by extension
addic7ed-cli get '*.mkv'
# Select single file
addic7ed-cli get Video.S01E01.mkv
```

### Options

```bash
# Non interactive mode (Download the best subtitle without asking)
addic7ed-cli get -n

# Erase existing subtitle (Default: skip)
addic7ed-cli get -e
addic7ed-cli get --erase

# Language filter (Default: French)
addic7ed-cli get -l english
# Show subtitles for all languages
addic7ed-cli get -l all

# Use proxy (Default: none)
addic7ed-cli get --proxy=socks5://localhost:9050

# Show help
addic7ed-cli get --help
```
