
function manageProductQuantity() 
{
    var plusButtons = document.getElementsByClassName("plus");
    var minusButtons = document.getElementsByClassName("minus");
    var products = document.getElementsByClassName("product");

    function incrementQuantity(button) {
        var input = button.previousElementSibling;
        var maxValue = parseInt(input.getAttribute("max")) || Infinity;
        if (parseInt(input.value) < maxValue) {
            input.value++;
            updateQuantity();
        }
    }

    function decrementQuantity(button) {
        var input = button.nextElementSibling;
        var minValue = parseInt(input.getAttribute("min")) || 0;
        if (parseInt(input.value) > minValue) {
            input.value--;
            updateQuantity();
        }
    }

    function updateQuantity() {
        if (products) {
            Array.from(products).forEach(product => {
                // Update quantity logic here
            });
        }
    }

    if (plusButtons && plusButtons.length > 0) {
        Array.from(plusButtons).forEach(button => {
            button.addEventListener("click", event => {
                if(event.target.classList.contains("plus")) {
                    incrementQuantity(event.target);
                } else {
                    incrementQuantity(event.target.parentElement);
                }
            });
        });
    }

    if (minusButtons && minusButtons.length > 0) {
        Array.from(minusButtons).forEach(button => {
            button.addEventListener("click", event => {
                if(event.target.classList.contains("minus")) {
                    decrementQuantity(event.target);
                } else {
                    decrementQuantity(event.target.parentElement);
                }
            });
        });
    }
}

manageProductQuantity();