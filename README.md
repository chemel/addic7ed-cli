# addic7ed-cli
Download subtitles from www.addic7ed.com

## Install

```bash

composer global require alc/addic7ed-cli

# Make sure you have export PATH in your ~/bashrc
export PATH=~/.config/composer/vendor/bin:$PATH

```

## Usage

```bash

addic7ed-cli get
addic7ed-cli get '*.mkv'
addic7ed-cli get Video.S01E01.mkv

# Change language (Default: French)
addic7ed-cli get -l english
# Show subtitles for all languages
addic7ed-cli get -l all

# Erase existing subtitle (Default: skip)
addic7ed-cli get -e
addic7ed-cli get --erase

# Use proxy (Default: none)
addic7ed-cli get --proxy=socks5://localhost:9050

# Non interactive mode (Download the last subtitle in the list)
addic7ed-cli get -n

# Show help
addic7ed-cli get --help

```
