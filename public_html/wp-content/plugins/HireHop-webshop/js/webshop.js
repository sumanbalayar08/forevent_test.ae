/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


	/* call the functin for product add to cart
	 * @name addToCart()
	 * @param {string} element
	 * @returns {undefined}
	 */
	function addToCart(element) 
	{
		var productParent = jQuery(element).closest('div.col-3'); // find the main div
		var price = jQuery(productParent).find('.price').text(); // price
		var productName = jQuery(productParent).find('.productname').text(); // get the product Name
		var productId = jQuery(productParent).find('.productId').text();  // get the product id
		var prdtId = parseInt(productId);
		var hmsId = jQuery(productParent).find('.hmsId').text();  // get the product id
		var imageId = jQuery(productParent).find('.imageId').text();  // get the image id
		var imageUrl = jQuery(productParent).find('.imageUrl').text();  // get the image url
		var quantity = typeof(jQuery(productParent).find('.itm_qty').val()) === "undefined" ? 1 : jQuery(productParent).find('.itm_qty').val(); // get the qty
		var qty = parseInt(quantity);
		//create the cartItem object with key and value 
		var cartItem = {
			productId:productId,
			hmsId:jQuery.trim(hmsId),
			productName: productName,
			imageId:imageId,
			imageUrl:imageUrl,
			price: price,
			quantity: quantity
		};

		var cartArray = new Array();   // create new array

		// Check if shopping cart exists in local storage
		if (localStorage.getItem('shopping-cart')) {
			try {
				// Try to parse the JSON from local storage
				cartArray = JSON.parse(localStorage.getItem('shopping-cart'));
			} catch (error) {
				// Handle the error appropriately (e.g., set cartArray to an empty array)
				cartArray = [];
			}
		}

		var index = cartArray.findIndex(obj => obj.productId == prdtId);
		if (index !== -1) 
		{
			// Update the quantity as an integer without parsing
			cartArray[index].quantity = (parseInt(cartArray[index].quantity) || 0) + qty;
		} 
		else 
		{
			cartArray.push(cartItem); // push the json items into array
		}

		var cartJSON = JSON.stringify(cartArray);  // cart array convert into json 
		localStorage.setItem('shopping-cart', cartJSON); // store json into local storage
		var shoppingCart = JSON.parse(localStorage.getItem('shopping-cart'));
		var	itemCount = shoppingCart.length;
			jQuery('#custom-cart-button').text("Cart - "+itemCount);   // get the count total item added to the cart
			
	};
	function change_category(cat_id, type)
	{
		// If search form ok, do the search
		 window.location.href = "?cat="+cat_id+"&type="+type;
		
	};
	//find the category list for toggle
	var toggler = document.getElementsByClassName("caret");
	var i;
	// loop with list 
	for (i = 0; i < toggler.length; i++) {
	  toggler[i].addEventListener("click", function() {
		this.parentElement.querySelector(".nested").classList.toggle("active");
		this.classList.toggle("caret-down");
	  });
	}
jQuery(document).ready(function () 
{ 
	var shoppingCart = JSON.parse(localStorage.getItem('shopping-cart'));
	var itemCount = shoppingCart === null ? 0 : shoppingCart.length ;
	jQuery("#custom-cart-button").text("Cart - "+itemCount);

	});