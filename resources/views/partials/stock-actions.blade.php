<!-- Add Stock Button -->
<button class="btn btn-success btn-sm" onclick="showAddStockModal('{{ $product->id }}')" >Ajouter Stock</button>

<!-- Remove Stock Button -->
<button class="btn btn-danger btn-sm" onclick="showRemoveStockModal('{{ $product->id }}')" >Retirer Stock</button>

<!-- View History Button -->
<button class="btn btn-info btn-sm" onclick="loadStockHistory('{{ $product->id }}')">Voir Historique</button>
