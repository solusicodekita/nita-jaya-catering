<tr>
    <td>{{ $no }}</td>
    <td>
        <select class="form-control ubahSelect" name="item[{{ $no }}][item_id]" id="item_id" onchange="getHargaSatuan(this)">
            <option value="">-- Pilih Item --</option>
            @foreach ($item as $row)
                <option value="{{ $row->id }}">{{ $row->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select class="form-control warehouse_id" name="item[{{ $no }}][warehouse_id]"></select>
    </td>
    <td>
        <input type="text" class="form-control harga_satuan" name="item[{{ $no }}][harga_satuan]" id="harga_satuan" readonly value="0">
    </td>
    <td>
        <input type="text" class="form-control desimal quantity" name="item[{{ $no }}][quantity]" id="quantity" onblur="totalHargaItem(this)"
            placeholder="Ketikkan Jumlah Satuan" autocomplete="off">
    </td>
    <td>
        <input type="text" name="item[{{ $no }}][total_harga_item]" id="total_harga_item" value="0" class="form-control total_harga_item" value="0" readonly>
    </td>
    <td>
        <input type="text" class="form-control" name="item[{{ $no }}][description]" id="description"
            placeholder="Ketikkan Keterangan" autocomplete="off">
    </td>
    <td>
        <button class="btn btn-danger btn-sm" onclick="deleteItem(this)"><i
                class="fas fa-trash"></i></button>
    </td>
</tr>