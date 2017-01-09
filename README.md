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
addic7ed-cli get
addic7ed-cli get '*.mkv'
addic7ed-cli get Video.S01E01.mkv

# Non interactive mode (Download the best subtitle without asking)
addic7ed-cli get -n

# Erase existing subtitle (Default: skip)
addic7ed-cli get -e
addic7ed-cli get --erase

# Change language (Default: French)
addic7ed-cli get -l english
# Show subtitles for all languages
addic7ed-cli get -l all

# Use proxy (Default: none)
addic7ed-cli get --proxy=socks5://localhost:9050

# Show help
addic7ed-cli get --help
```
