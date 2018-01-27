var li="https://thirdhandbd/invoice-generator/";

$(function(){
	$("#datepicker").datepicker({dateFormat: 'yy-mm-dd'});
	$("#datepicker-due").datepicker({dateFormat: 'yy-mm-dd'});
}); //DATEPICKER


//THEN CODIGNITER
function new_line(value){
	invoice_id = $(value).val().trim();

	$.ajax({
      type: 'POST',
      url:li+'home/add_newline/',
      data:{invoice_id:invoice_id},
      dataType:'json',
      success: function(data){

      	for(i=0; i<data.length;i++){
      		var id = data[i].id;
      		var item_name = data[i].item_name;
      		var qty = data[i].qty;
      		var price = data[i].price;
      		var discount = data[i].discount;
      		var subtotal = data[i].subtotal;      		

      		var new_line = 
			'<tr id="item'+id+'">'+
				'<td><input type="text" size="25" id="item_name'+id+'" data-id="'+id+'" placeholder="Item name" value="'+item_name+'" /></td>'+
				'<td><input type="text" size="10" id="qty'+id+'" data-id="'+id+'" onblur="qty(this)" value="'+qty+'" /></td>'+
				'<td><input type="text" size="10" id="price'+id+'" data-id="'+id+'"onblur="price(this)" value="'+price+'" /></td>'+
				'<td><input type="text" size="10" id="discount'+id+'" data-id="'+id+'"onblur="discount(this)" value="'+discount+'" /></td>'+
				'<td><input type="text" size="10" id="subtotal'+id+'" data-id="'+id+'" value="'+subtotal+'" /></td>'+
				'<td class="for-delete-td">'+
                    '<button class="btn btn-sm btn-default" value="'+id+'" onclick="return delete_items(this);" type="button">x</button>'+
                '</td>'+
			+'</tr>';

			$("#trMore").append(new_line);
      	}

      },
      error: function(){
        alert('Error add newline!');
      }
    });
}

function qty(value){
	var id = $(value).attr("data-id"); //row id
	var qty = $(value).val().trim(); //self value

	var price = $("#price"+id).val().trim();
	var discount = $("#discount"+id).val().trim();
	var item_name = $("#item_name"+id).val().trim();

	var subtotal = (price*qty);
	var discountTk = (subtotal*discount/100);
	var total = (subtotal-discountTk);

	if (total != '' || total != 0) {
		$.ajax({
	      type: 'POST',
	      url:li+'home/add_data_items/',
	      data:{id:id,qty:qty,price:price,discount:discount,total:total,item_name:item_name},
	      dataType:'json',
	      success: function(data){
	      	if (data.return==1) {
	      		$("#subtotal"+id).val(total); //row subtotal

	      		$("#total").val(data.total); //all total
	      	}
	      },
	      error: function(){
	        alert('Error add items!');
	      }
	    });
	}
	
}

function price(value){
	var id = $(value).attr("data-id"); //row id
	var price = $(value).val().trim(); //self value

	var qty = $("#qty"+id).val().trim();
	var discount = $("#discount"+id).val().trim();	
	var item_name = $("#item_name"+id).val().trim();

	var subtotal = (price*qty);
	var discountTk = (subtotal*discount/100);
	var total = (subtotal-discountTk);

	if (total != '' || total != 0) {
		$.ajax({
	      type: 'POST',
	      url:li+'home/add_data_items/',
	      data:{id:id,qty:qty,price:price,discount:discount,total:total,item_name:item_name},
	      dataType:'json',
	      success: function(data){
	      	if (data.return==1) {
	      		$("#subtotal"+id).val(total); //row subtotal

	      		$("#total").val(data.total); //all total
	      	}
	      },
	      error: function(){
	        alert('Error add items!');
	      }
	    });
	}

	
}
function discount(value){
	var id = $(value).attr("data-id"); //row id
	var discount = $(value).val().trim(); //self value

	var qty = $("#qty"+id).val().trim();
	var price = $("#price"+id).val().trim();
	var item_name = $("#item_name"+id).val().trim();


	var subtotal = (price*qty);
	var discountTk = (subtotal*discount/100);
	var total = (subtotal-discountTk);

	if (total != '' || total != 0) {
		$.ajax({
	      type: 'POST',
	      url:li+'home/add_data_items/',
	      data:{id:id,qty:qty,price:price,discount:discount,total:total,item_name:item_name},
	      dataType:'json',
	      success: function(data){
	      	if (data.return==1) {
	      		$("#subtotal"+id).val(total); //row subtotal

	      		$("#total").val(data.total); //all total
	      	}
	      },
	      error: function(){
	        alert('Error add items!');
	      }
	    });
	}
}

/*
$("#price").blur(function(){
    var pr = $(this).val().trim();
    var price = function(pr) {
      return /^(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(pr);
    }
    if (price(pr)==0) {
    	alert('price undefined');
    }

});
*/

function added_tax(value){
	var tax = $(value).val().trim(); //self value
	var subtotal = $("#total").val().trim();
	var total = parseInt(subtotal);

	var taxTk = (subtotal*tax/100);

	var total_kwr = taxTk+total;

	var data = parseFloat(Math.round(total_kwr * 100) / 100).toFixed(2)

	$("#total-krw").val(data);
	
}
function showPreview(objFileInput) {
    if (objFileInput.files[0]) {
        var fileReader = new FileReader();
        fileReader.onload = function (e) {
            $("#targetLayer").html('<img src="'+e.target.result+'" width="225px" height="225px" class="upload-preview" />');
      $("#targetLayer").css('opacity','0.9');
      $(".icon-choose-image").css('opacity','0.5');
        }
    fileReader.readAsDataURL(objFileInput.files[0]);
    }else{
    	alert('sohel');
    }
} //click to uploaded image on browser



function delete_items(value){
	var id = $(value).val().trim();
	$.ajax({
      type: 'POST',
      url:li+'home/delete_items/',
      data:{id:id},
      dataType:'json',
      success: function(data){
      	if (data.return==1) {
      		$("#item"+id).fadeOut(); //all total
      		$("#total").val(data.total); //all total
      	}
      },
      error: function(){
        alert('Error add items!');
      }
    });
}