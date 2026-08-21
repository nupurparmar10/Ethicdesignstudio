<script>
//Only character Function
function onlyCharacters(event) 
{
	let key = event.key;
	if (/[0-9]/.test(key)) {
		event.preventDefault(); 
		return false;
	}
	return true; 
}

//Allow alpha Numeric
function allowAlphaNumeric(event) 
{
    let key = event.key;
    if (!/^[a-zA-Z0-9]$/.test(key)) {
        event.preventDefault(); 
        return false;
    }
    return true; 
}


//Mobile No

function allowOnly10Numeric(input) 
{
    input.value = input.value.replace(/[^0-9]/g, '').slice(0, 10);

    const errorElement = document.getElementById('mobileError');

    // Check for exactly 10 characters
    if (input.value.length === 10) {
        errorElement.textContent = ''; // No error
    } else {
        errorElement.textContent = 'Mobile number must be exactly 10 digits.';
    }
}
//Aadhar No. Function
function allowOnly12Numeric(input) 
{
    input.value = input.value.replace(/[^0-9]/g, '').slice(0, 12);

    const errorElement = document.getElementById('aadharError');

    // Check for exactly 12 characters
    if (input.value.length === 12) {
        errorElement.textContent = ''; // No error
    } else {
        errorElement.textContent = 'AAdhar number must be exactly 12 digits.';
    }
}

function allowOnly15AlphaNumeric(input) 
{
    input.value = input.value.replace(/[^A-Za-z0-9]/g, '').slice(0, 15);

    const errorElement = document.getElementById('gstError');

    // Check for exactly 15 characters
    if (input.value.length === 15) {
        errorElement.textContent = ''; // No error
    } else {
        errorElement.textContent = 'GST number must be exactly 15 characters.';
    }
}
</script>