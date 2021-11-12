
<ul class="product__review">
    @for ($i=0; $i<$rate; $i++)
        <li class="product__review--fill"><i class="icon-star"></i></li>
    @endfor
    @for ($i=0; $i<5-$rate; $i++)
        <li class="product__review--blank"><i class="icon-star"></i></li>    
    @endfor    
</ul>