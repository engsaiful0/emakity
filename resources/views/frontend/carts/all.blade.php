<div class="table-content table-responsive cart-table-content m-t-30">
    <table>
        <thead class="gray-bg" >
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Until Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr>
                <td class="product-thumbnail">
                    <a href="#">
                        {!! getMediaurl_frontend($product['product'], 'web_image', 'product__img img-fluid',asset('frontend/assets/img/product/size-small/product-home-1-img-2.jpg'),140,120) !!}
                    </a>
                </td>
                <td class="product-name"><a href="#">{{$product['product']['name']}}</a></td>
                <td class="product-price-cart"><span class="amount">{{getPriceValue($product['product'],'discount_price') * $product['quantity']}}</span></td>
                <td class="product-quantities">
                    <div class="quantity d-inline-block">
                        <input type="number" min="1" step="1" value="{{$product['quantity']}}" data-id='{{$product['id']}}'>
                    </div>
                </td>
                <td class="product-subtotal">{{getPriceValue($product['product'],'discount_price') * $product['quantity']}}</td>
                <td class="product-remove">
                    <a href="#"><i class="fa fa-pencil-alt"></i></a>
                    <a href="#" onclick="delete_cart({{$product['id']}})"><i class="fa fa-times"></i></a>
                </td>
            </tr>    
            @endforeach
            
        </tbody>
    </table>
</div>