#!/bin/bash

# Enable debugging
set -x

# Define the watermark text
WATERMARK="KnowledgeCompass"

# Full paths to the directory to watch for new images and where to save watermarked images
WATCH_DIR="/Users/badrulamin/Library/CloudStorage/Dropbox/Workspace/Domain/www/Personal/Web_Project/KnowledgeCompass/data/diagram/blockhain/hyperledger"
OUTPUT_DIR="/Users/badrulamin/Library/CloudStorage/Dropbox/Workspace/Domain/www/Personal/Web_Project/KnowledgeCompass/public/media/blockchain/hyperledger"

# Create the output directory if it doesn't exist
mkdir -p "$OUTPUT_DIR"

# Loop through all the JPEG and PNG images in the WATCH_DIR
for img in "$WATCH_DIR"/*.jpg "$WATCH_DIR"/*.png; do
  # Check if the file exists
  if [ -f "$img" ]; then
    # Apply the watermark and save the new image in the OUTPUT_DIR
    convert "$img" -font "Ubuntu" -weight 2000 -pointsize 25 -fill white -stroke black -strokewidth 1 -gravity southeast -annotate +21+21 "$WATERMARK" -annotate +19+19 "$WATERMARK" -annotate +20+20 "$WATERMARK" "$OUTPUT_DIR/$(basename "$img")"
    echo "Added watermark to $(basename "$img")"
  fi
done

echo "Watermarking completed."

# Disable debugging
set +x
