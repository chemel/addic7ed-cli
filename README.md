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
console get -l English

# Erase existing subtitle (Default: skip)
console get -e true
console get --erase=true

# Show help
addic7ed-cli get --help

```
