<a href="{{ route('recipes.show', $recipe->id) }}" class="btn btn-info btn-sm">
    <i class="fas fa-eye"></i>
</a>
<a href="{{ route('recipes.edit', $recipe->id) }}" class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i>
</a>
<button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $recipe->id }}')">
    <i class="fas fa-trash"></i>
</button>
<form id="delete-form-{{ $recipe->id }}" 
      action="{{ route('recipes.destroy', $recipe->id) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
