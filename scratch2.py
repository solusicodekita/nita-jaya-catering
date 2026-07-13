import re

with open('resources/views/admin/resep/index.blade.php', 'r') as f:
    content = f.read()

# Replace button
old_button = r'<button class="btn btn-sm btn-info rounded-circle mb-1 text-white" onclick="manageIngredients\(\'\{\{ \$menu->id \}\}\', \'\{\{ \$menu->name \}\}\'\)" title="Kelola Bahan">\s*<i class="fa-solid fa-list-check"></i>\s*</button>'
new_button = r'<a href="{{ route(\'admin.resep.manageItems.show\', $menu->id) }}" class="btn btn-sm btn-info rounded-circle mb-1 text-white" title="Kelola Bahan">\n                                    <i class="fa-solid fa-list-check"></i>\n                                </a>'
content = re.sub(old_button, new_button, content)

# Remove modals
content = re.sub(r'<!-- Modal Kelola Bahan -->.*?<!-- Modal Import Resep -->', '<!-- Modal Import Resep -->', content, flags=re.DOTALL)

# Remove JS
js_remove_pattern = r'const availableItems = @json\(\$items\);.*?function useRecipe\(id, name\) \{'
content = re.sub(js_remove_pattern, 'function useRecipe(id, name) {', content, flags=re.DOTALL)

# Remove QuickCreate JS
js_quick_remove = r'let targetRowIndex = null;.*?// Fix scroll lock when stacked modal is closed.*?\}\);'
content = re.sub(js_quick_remove, '', content, flags=re.DOTALL)

# Write back
with open('resources/views/admin/resep/index.blade.php', 'w') as f:
    f.write(content)

print("Updated index.blade.php")
