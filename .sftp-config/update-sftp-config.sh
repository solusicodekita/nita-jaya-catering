#!/bin/bash

# Get current branch
BRANCH=$(git branch --show-current)

# Path to SFTP config files
CONFIG_DIR=".sftp-config"
TARGET_FILE=".vscode/sftp.json"

# Ensure .vscode directory exists
mkdir -p .vscode

if [ "$BRANCH" = "main" ]; then
    # Copy main configuration
    cp "$CONFIG_DIR/sftp.main.json" "$TARGET_FILE"
    echo "Updated SFTP config for main branch"
elif [ "$BRANCH" = "dev" ]; then
    # Copy dev configuration
    cp "$CONFIG_DIR/sftp.dev.json" "$TARGET_FILE"
    echo "Updated SFTP config for dev branch"
else
    echo "Unknown branch: $BRANCH"
    echo "No SFTP config updated"
fi
