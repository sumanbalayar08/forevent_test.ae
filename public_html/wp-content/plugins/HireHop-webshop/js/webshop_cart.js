function webshop_cart(hh_id)
{
    // Handle button click
    jQuery('#custom-cart-button').on('click', function() {
        // Get data from local session storage
        var cartRowHTML = "",
            itemCount = 0,
            grandTotal = 0,
            price = 0,
            quantity = 0,
            subTotal = 0;
			
			
		var table = jQuery("<table>", {style:"width:100%"}).appendTo('#custom-cart-dialog');
		
		var thead = jQuery("<thead>",{class:"bg fnt-14 bold"}).appendTo(table);
		var tr = jQuery("<tr>").appendTo(thead);
		var td = jQuery("<td>", {html:"#"}).appendTo(tr);
		var td = jQuery("<td>", {html:"Product name", style:"text-align:center"}).appendTo(tr);
		var td = jQuery("<td>", {html:"Rate"}).appendTo(tr);
		var td = jQuery("<td>", {html:"Qunatity"}).appendTo(tr);
		var td = jQuery("<td>", {html:"Action"}).appendTo(tr);

		var tbody = jQuery("<tbody>",{id:"items", class:"fnt-14"}).appendTo(table);

            if (localStorage.getItem('shopping-cart')) {
            
                var shoppingCart = JSON.parse(localStorage.getItem('shopping-cart'));
                itemCount = shoppingCart.length;
				var i =0;
                //Iterate javascript shopping cart array
                jQuery.each(shoppingCart, function(a,b){

					//get the image
					if(b.imageUrl !== '')
						var src = b.imageUrl; // COMPANY ID = 1
					else
						var src = '/images/no-image.png';
					itemCount += parseInt(b.quantity);
					//set cart table
					cartRowHTML += "<tr>" +
						"<td><div style='float:left;'><img src='"+ src+"' style='width: 50px;margin-right: 20px;'></div></td><td><div>"+
						"<span class='productname ws-item-name' >" + b.productName + "</span>" +
						"<input type='text' class='text-truncate pl-2 productName' style='display: none;' name='name_" + i + "' value='" +  b.productName+ "'/>" +
						"<input type='text' class='text-truncate pl-2 price' style='display: none;' name='prc_" + i + "' value='" + b.price + "'/>" +
						"<input type='text' class='text-truncate pl-2 imageId' style='display: none;' name='img_" + i + "' value='" + b.imageId + "'/>" +
						"<input type='text' class='text-truncate pl-2 imageId' style='display: none;' name='imgUrl_" + i + "' value='" + b.imageUrl + "'/>" +
						"<input type='text' class='text-truncate pl-2 productId"+ i +"' style='display: none;' name='prdt_" + i + "' value='" + b.productId + "'/>" +
						"<input type='text' class='text-truncate pl-2 hmsId' style='display: none;' name='hms_" + i + "' value='" + b.hmsId + "'/>" +
						"<input type='text' class='text-truncate pl-2 hmsId' style='display: none;' name='qty" + i + "' value='" + b.quantity + "'/>" +
						"<h6 class='text-truncate pl-2 imageId' style='display: none;'>" + b.imageId + "</h6>" +
						"</div></td>" +
						"<td class='text-right'><span class='badge ws-item-price' >" + b.price + "</span></td>" +
						"<td class='text-right'><span class='badge ws-item-price'>" + b.quantity + "</span></td>" +
						"<td class='align-middle' style='text-align: center !important;'><button class='cancel_btn' onclick='deleteRow(this, " + i + ")'>X</button></td>" +
						"</tr>";
					i++;
				});	
            }

        // Display data in a dialog box
        jQuery(tbody).html(cartRowHTML);
       // var tbl = jQuery("<table>").appendTo("#custom-cart-dialog");

        jQuery('#custom-cart-dialog').dialog({
            modal: true,
            resizable: false,
            width: "525",
            minHeight: "350",
			title:"My Items",
			/*   dialogClass: "cart_dialog",*/
            buttons: [	
                { html: "Checkout",id:"ws_checkout_btn", click: function(){ window.location = "/checkout" } },
                { html: "close", click: function(){ jQuery(this).dialog('close'); jQuery("#custom-cart-dialog").empty(); } },
                { html: "Clear", click: function(){ clear();jQuery(this).dialog('close'); jQuery("#custom-cart-dialog").empty(); } }
            ]
        });
		
		// Check if shopping cart exists in local storage
		if (localStorage.getItem('shopping-cart') !== null) {
			var cartArray = JSON.parse(localStorage.getItem('shopping-cart'));
		}
		else{
			var cartArray = [];
		}
		//check cart is empty then proceed to cart button disable
		if(!Array.isArray(cartArray) || !cartArray.length)
			jQuery("#ws_checkout_btn").prop("disabled",true);
		else
			jQuery("#ws_checkout_btn").prop("disabled",false);
		});
}
function clear()
{
    jQuery('#custom-cart-button').text("cart - 0");   // set count with 0
		localStorage.removeItem("shopping-cart");  // clear localstorage
		localStorage.removeItem("period");  // clear localstorage
}

function Checkout()
{
    var itemCount = 0,
        price = 0,
        quantity = 0,
        i= 1;


    var rm_div = jQuery("<div>", {class:"col-7"}).appendTo("#checkout_container");
    var r_div = jQuery("<div>",{id:"checkout-form"}).appendTo(rm_div);
    var l_div = jQuery("<div>", {class:"col-5"}).appendTo("#checkout_container");
    var dt_div = jQuery("<div>").appendTo(l_div);
    var line = jQuery("<hr>", {class:"mb-1 mt-1"}).appendTo(l_div);

    var table = jQuery("<table>", {style:"width:100%"}).appendTo(dt_div);
    var tr = jQuery("<tr>").appendTo(table);
    var td = jQuery("<td>",{html:"Period", colspan:2, class:"bold fnt-14", style:"background-color:#d9d9d9;"}).appendTo(tr);


    var tr = jQuery("<tr>").appendTo(table);
    var td = jQuery("<td>",{html:"Start date"}).appendTo(tr);
    var td = jQuery("<td>").appendTo(tr);
    var sdt = jQuery("<input>", {type:"date", name:"start_date", style:"width:100%"}).appendTo(td);

    var tr = jQuery("<tr>").appendTo(table);
    var td = jQuery("<td>",{html:"End date"}).appendTo(tr);
    var td = jQuery("<td>").appendTo(tr);
    var sdt = jQuery("<input>", {type:"date", name:"end_date", style:"width:100%"}).appendTo(td);

    
    var table = jQuery("<table>",{class:"table bdr"}).appendTo(l_div);
    var thead = jQuery("<thead>",{class:"bg fnt-14 bold"}).appendTo(table);
    var tr = jQuery("<tr>").appendTo(thead);
    var td = jQuery("<td>", {html:"#"}).appendTo(tr);
    var td = jQuery("<td>", {html:"Product name", style:"text-align:center"}).appendTo(tr);
    var td = jQuery("<td>", {html:"Rate"}).appendTo(tr);
    var td = jQuery("<td>", {html:"Qunatity"}).appendTo(tr);
    var td = jQuery("<td>", {html:"Action"}).appendTo(tr);

    var tbody = jQuery("<tbody>",{id:"items", class:"fnt-14"}).appendTo(table);

    if (localStorage.getItem('shopping-cart')) {
        var shoppingCart = JSON.parse(localStorage.getItem('shopping-cart'));
		console.log(shoppingCart.length);
        itemCount = shoppingCart.length;
		var cartRowHTML = "";
		jQuery.each(shoppingCart, function(a,b){

			//get the image
			if(b.imageUrl !== '')
				var src = b.imageUrl; // COMPANY ID = 1
			else
				var src = '/images/no-image.png';
			itemCount += parseInt(b.quantity);
			//set cart table
			cartRowHTML += "<tr>" +
				"<td><div style='float:left;'><img src='"+ src+"' style='width: 50px;margin-right: 20px;'></div></td><td><div>"+
				"<span class='productname ws-item-name' >" + b.productName + "</span>" +
				"<input type='text' class='text-truncate pl-2 productName' style='display: none;' name='name_" + i + "' value='" +  b.productName+ "'/>" +
				"<input type='text' class='text-truncate pl-2 price' style='display: none;' name='prc_" + i + "' value='" + b.price + "'/>" +
				"<input type='text' class='text-truncate pl-2 imageId' style='display: none;' name='img_" + i + "' value='" + b.imageId + "'/>" +
				"<input type='text' class='text-truncate pl-2 productId"+ i +"' style='display: none;' name='prdt_" + i + "' value='" + b.productId + "'/>" +
				"<input type='text' class='text-truncate pl-2 hmsId' style='display: none;' name='hms_" + i + "' value='" + b.hmsId + "'/>" +
				"<input type='text' class='text-truncate pl-2 hmsId' style='display: none;' name='qty_" + i + "' value='" + b.quantity + "'/>" +
				"<h6 class='text-truncate pl-2 imageId' style='display: none;'>" + b.imageId + "</h6>" +
				"</div></td>" +
				"<td class='text-right'><span class='badge ws-item-price' >" + b.price + "</span></td>" +
				"<td class='text-right'><span class='badge ws-item-price'>" + b.quantity + "</span></td>" +
				"<td class='align-middle' style='text-align: center !important;'><button class='cancel_btn' onclick='deleteRow(this, " + i + ")'>X</button></td>" +
				"</tr>";
			i++;
		});	
		jQuery(tbody).html(cartRowHTML);
    }

    var p = jQuery("<p>").appendTo(r_div);
    var label = jQuery("<label>").text("Full name").appendTo(p);
    var input = jQuery("<input>",{ type:"text", class:"input-text", name:"name"}).appendTo(p);

    var p = jQuery("<p>").appendTo(r_div);
    var label = jQuery("<label>").text("Company name").appendTo(p);
    var input = jQuery("<input>",{ type:"text", class:"input-text", name:"company"}).appendTo(p);

    var p = jQuery("<p>").appendTo(r_div);
    var label = jQuery("<label>").text("Email").appendTo(p);
    var input = jQuery("<input>",{ type:"text", class:"input-text", name:"email"}).appendTo(p);

    var p = jQuery("<p>").appendTo(r_div);
    var label = jQuery("<label>").text("Contact no.").appendTo(p);
    var input = jQuery("<input>",{ type:"text", class:"input-text", name:"mobile"}).appendTo(p);

    var p = jQuery("<p>").appendTo(r_div);
    var label = jQuery("<label>").text("address").appendTo(p);
    var input = jQuery("<input>",{ type:"text", class:"input-text", name:"address"}).appendTo(p);

    var p = jQuery("<p>").appendTo(r_div);
    var label = jQuery("<label>").text("Post code").appendTo(p);
    var input = jQuery("<input>",{ type:"text", class:"input-text", name:"postcode"}).appendTo(p);
    var input = jQuery("<input>",{ type:"text", class:"input-text", name:"cnt", style:"display:none",value: shoppingCart.length}).appendTo(p);




}
function deleteRow(btn, cnt)
{
	var p_id = jQuery(".productId"+cnt).val();
	var shoppingCart = JSON.parse(localStorage.getItem('shopping-cart'));

	shoppingCart = shoppingCart.filter(function( obj ) {
		return obj.productId !== p_id;
	});
	var itemCount = shoppingCart.reduce((n, {quantity}) => n + parseInt(quantity), 0);
	localStorage.setItem('shopping-cart', JSON.stringify(shoppingCart));
	
	jQuery(btn).closest("tr").remove();
}
jQuery(document).ready(function($) {
	Checkout();
});
