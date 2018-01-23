$(function(){
	$("#datepicker").datepicker();
	$("#datepicker-due").datepicker();
});
function bdsohel(value){
	var val = $(value).val().trim();
	alert(val);
}


$("#price").blur(function(){
    var pr = $(this).val().trim();
    var price = function(pr) {
      return /^(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(pr);
    }
    if (price(pr)==0) {
    	alert('price undefined');
    }

});



function invoice_submit(){
	var price = $("#price").val().trim();
	var qt = $("#qty").val().trim();
	var dis = $("#discount").val().trim();

	if (price=='') {var price=0;}
	if (qt=='') {var qt=0;}
	if (dis=='') {var dis=0;}

	var qty = parseInt(qt);
	var discount = parseInt(dis);
	var subtotal = (price*qty);
	var discountTk = (subtotal*discount/100);
	var total = (subtotal-discountTk);
	$("#subtotal").val(total);	
}
function new_line(){
	var new_line = 
	'<tr>'+
		'<td><input type="text" size="20" value="32" /></td>'+
		'<td><input type="text" size="10" /></td>'+
		'<td><input type="text" size="15" /></td>'+
		'<td><input type="text" size="15" /></td>'+
		'<td><input type="text" size="15" value="sohel" /></td>'
	+'</tr>';



	$("#trMore").append(new_line);
}

