#!/bin/bash

# Main function to generate a table from Markdown files
generate_table() {
  local DIR="$1"
  local OUTPUT_FILE="$2"

  if [ ! -d "$DIR" ]; then
    echo "Directory $DIR does not exist."
    return 1
  fi

  if [ -f "$OUTPUT_FILE" ]; then
    echo "Output file $OUTPUT_FILE already exists. Overwriting."
  fi

  echo "| Index | Title | Category | Subcategory | Date | Description | Path |" > "$OUTPUT_FILE"
  echo "|-------|-------|----------|-------------|------|-------------|------|" >> "$OUTPUT_FILE"

  local index=1

  # Use find to recursively search for .md files
  find "$DIR" -type f -name "*.md" | while read -r file; do
    echo "Processing file: $file"  # Debug statement

    if [ ! -f "$file" ]; then
      echo "Skipping $file as it does not exist."
      continue
    fi

    local title=$(grep -m 1 '^# ' "$file" | sed 's/^# //')
    local category=$(grep -m 1 '^##### ' "$file" | sed 's/^##### //')
    local subCategory=$(awk 'BEGIN {count=0} /^##### / {count++; if (count == 2) {print; exit}}' "$file" | sed 's/^##### //')
    local date=$(awk '/^##### [0-9]{1,2} [A-Za-z]+ [0-9]{4}/ { print; exit }' "$file" | sed 's/^##### //')
    local description=$(awk '/^##### [0-9]{1,2} [A-Za-z]+ [0-9]{4}/ { getline; getline; print; exit }' "$file")

    title=${title:-"N/A"}
    category=${category:-"N/A"}
    subCategory=${subCategory:-"N/A"}
    date=${date:-"N/A"}
    description=${description:-"N/A"}

    # Escape special characters in description to make it compatible with Markdown table
    description=$(echo "$description" | sed 's/|/\\|/g')

    # Escape special characters in file path to make it compatible with Markdown table
    file=$(echo "$file" | sed 's/|/\\|/g')

    echo "| $index | $title | $category | $subCategory | $date | $description | $file |" >> "$OUTPUT_FILE"

    ((index++))
  done
}

# Main function to generate a title from Markdown files
generate_title(){
  local DIR="$1"
  local OUTPUT_FILE="$2"

  if [ ! -d "$DIR" ]; then
    echo "Directory $DIR does not exist."
    return 1
  fi

  if [ -f "$OUTPUT_FILE" ]; then
    echo "Output file $OUTPUT_FILE already exists. Overwriting."
  fi

  echo "| Index | Title |" > "$OUTPUT_FILE"
  echo "|-------|-------|" >> "$OUTPUT_FILE"

  local index=1

  find "$DIR" -type f -name "*.md" | while read -r file; do
    if [ ! -f "$file" ]; then
      echo "Skipping $file as it does not exist."
      continue
    fi

    local title=$(grep -m 1 '^# ' "$file" | sed 's/^# //')

    title=${title:-"N/A"}

    echo "| $index | $title |" >> "$OUTPUT_FILE"
    ((index++))
  done
}

# Main function to subcategory a table from Markdown files
generate_subcategory(){
  local DIR="$1"
  local OUTPUT_FILE="$2"

  if [ ! -d "$DIR" ]; then
    echo "Directory $DIR does not exist."
    return 1
  fi

  if [ -f "$OUTPUT_FILE" ]; then
    echo "Output file $OUTPUT_FILE already exists. Overwriting."
  fi

  echo "| Index | Subcategory |" > "$OUTPUT_FILE"
  echo "|-------|-------|" >> "$OUTPUT_FILE"

  local categories=()

  while read -r file; do
    echo "Reading file: $file"  # Debug statement

    if [ ! -f "$file" ]; then
      echo "Skipping $file as it does not exist."
      continue
    fi

    local subcategory=$(awk '/^##### hyperledger/ { print; exit }' "$file" | sed 's/^##### //')

    subcategory=${subcategory:-"N/A"}

    if [ "$subcategory" == "hyperledger" ]; then
      categories+=("$subcategory")
    fi
  done < <(find "$DIR" -type f -name "*.md")

  # Remove duplicates and sort
  sorted_subcategories=$(printf "%s\n" "${categories[@]}" | sort | uniq)

  local index=1

  # Write sorted and unique categories to the output file
  while read -r subcategory; do
    echo "| $index | $subcategory |" >> "$OUTPUT_FILE"
    ((index++))
  done <<< "$sorted_subcategories"
}

# Call the functions
generate_table "../data/text/" "../data/.meta/tables.md"
generate_title "../data/text/" "../data/.meta/titles.md"
generate_subcategory "../data/text/" "../data/.meta/subcategory.md"
