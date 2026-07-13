import re

with open('resources/views/admin/resep/index.blade.php', 'r') as f:
    content = f.read()

# I will find the @push('script') block and extract the whole JS
script_start = content.find("@push('script')")
if script_start != -1:
    script_part = content[script_start:]
    print("Found script, length:", len(script_part))
    with open('scratch_script.txt', 'w') as out:
        out.write(script_part)
else:
    print("Script not found")

