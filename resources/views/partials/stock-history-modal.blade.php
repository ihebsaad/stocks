<!-- Stock History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Historique de stocks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="historyTable">
                    <thead>
                        <tr>
                            <th>Emplacement</th>
                            <th>Entrée</th>
                            <th>Sortie</th>
                            <th>Prix d'achat</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filled dynamically by loadStockHistory function -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div class="modal fade stockModal" id="addstockModal" tabindex="-1" role="dialog" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockModalLabel">Ajouter Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="stockForm">
                <div class="modal-body">
                    <input type="hidden" name="product_id">
                    <input type="hidden" name="action">
                    <div class="form-group">
                        <label for="location">Emplacement</label>
                        <select name="location" class="form-control" required>
                            <option value="depot">Dépot</option>
                            <option value="magasin">Magasin</option>
                            <option value="exposition">Exposition</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantité</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="purchase_price">Prix d'achat</label>
                        <input type="number" name="purchase_price" class="form-control" placeholder="Prix d'achat" value="" step="0.01" >
                    </div>
                    <div class="form-group">
                        <label for="entry_date"> Date d'entrée</label>
                        <input type="date" name="entry_date" class="entry-date form-control" value="{{date('d/m/Y')}}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Remove Stock Modal -->
<div class="modal fade stockModal" id="removestockModal" tabindex="-1" role="dialog" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockModalLabel">Retirer Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="stockForm2">
                <div class="modal-body">
                    <input type="hidden" name="product_id">
                    <input type="hidden" name="action">
                    <div class="form-group">
                        <label for="location">Emplacement</label>
                        <select name="location" class="form-control" required>
                            <option value="depot">Dépot</option>
                            <option value="magasin">Magasin</option>
                            <option value="exposition">Exposition</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantité</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="purchase_price">Prix d'achat</label>
                        <input type="number" name="purchase_price" class="form-control" placeholder="Prix d'achat" value="" step="0.01" >
                    </div>
                    <div class="form-group">
                        <label for="entry_date"> Date de sortie</label>
                        <input type="date" name="exit_date" class="entry-date form-control" value="{{date('d/m/Y')}}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>