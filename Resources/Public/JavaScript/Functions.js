// check if all Input fields are filled

function valid() {
  var input0 = document.querySelector('input[name="username"]');
  if (!input0.checkValidity()) {
    console.log('invalid');
  } else {
    console.log('valid');
  }
  document.getElementById('msform').submit();
  var input1 = document.querySelector('input[name="email"]');
  input1.checkValidity();
  var input2 = document.querySelector('input[name="pass"]');
  input2.checkValidity();
  var input3 = document.querySelector('input[name="cpass"]');
  input3.checkValidity();

  if (!input0.validity.valid) {
    console.log('Input is not valid');
  }
  return false;
}

// Fehlermeldung ausgeben

function errorMessage() {
  console.log('oh no');
}
