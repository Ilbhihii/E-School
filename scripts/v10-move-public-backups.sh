#!/usr/bin/env bash
set -euo pipefail

mkdir -p storage/feature-backups
shopt -s nullglob

found=0
for source_dir in public/storage_backup_*; do
    found=1
    base_name="$(basename "$source_dir")"
    destination="storage/feature-backups/${base_name}_$(date +%Y%m%d_%H%M%S)"
    echo "Moving ${source_dir} -> ${destination}"
    mv "$source_dir" "$destination"
done

if [ "$found" -eq 0 ]; then
    echo "No public/storage_backup_* directory found."
fi
