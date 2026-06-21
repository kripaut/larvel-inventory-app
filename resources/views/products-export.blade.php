<table>
  <tr>
    <th>Name</th>
    <th>Company</th>
    <th>Category</th>
    <th>Subcategory</th>
    <th>Stock</th>
    <th>Buy Price</th>
    <th>Sell Price</th>
    <th>GST %</th>
    <th>Created At</th>
  </tr>
  @foreach ($products as $product)
    <tr>
      <td>{{ $product->name }}</td>
      <td>{{ $product->company->name ?? '' }}</td>
      <td>{{ $product->category->name ?? '' }}</td>
      <td>{{ $product->subcategory->name ?? '' }}</td>
      <td>{{ $product->stock }}</td>
      <td>{{ number_format((float) $product->buy_price, 2, '.', '') }}</td>
      <td>{{ number_format((float) $product->sell_price, 2, '.', '') }}</td>
      <td>{{ number_format((float) $product->gst, 2, '.', '') }}</td>
      <td>{{ $product->created_at?->format('Y-m-d') }}</td>
    </tr>
  @endforeach
</table>
