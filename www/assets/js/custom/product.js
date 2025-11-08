

$(".opencartmodel").on("click", function () {
    let product = $(this).data('productid')
    if (product) {
        axios.post(BASE_URL + 'backend/product/info', {
            id: product,
        })
            .then(function (response) {
                if(response.status===200){
                   var ProductDetails = response.data.ProductDetails;
                   console.log(ProductDetails)
                   var ProductOffers = response.data.ProductOffers;
                   if(ProductDetails){
                       let StockStatus = 'Not Avabile';
                       $('#ProductName').html(ProductDetails.name);
                       $('#ProductType').html(ProductDetails.category_name);
                       $('#ManufacturerName').html(ProductDetails.manufacturer)
                       if(parseInt(ProductDetails.stock)>1){
                                StockStatus = 'In Stock';
                       }
                       $('#StockStatus').html(StockStatus)
                       $('#ProductImage').attr('src',BASE_URL+'data/product/'+ProductDetails.image)
                       $('#ProductMRP').html('Rs. '+ProductDetails.mrp)
                       $('#OpenCartModal').modal('show')
                   }
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }


});
$(document).ready(function() {
    $('.minus').click(function () {
        var $input = $(this).parent().find('input');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });
    $('.plus').click(function () {
        var $input = $(this).parent().find('input');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
    });
});