// Client-side validation for every form with class "validate"
document.querySelectorAll('form.validate').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    var ok = true;
    form.querySelectorAll('.error').forEach(function (el) { el.textContent = ''; });
    form.querySelectorAll('[data-rule]').forEach(function (input) {
      var v = input.value.trim(), rules = input.dataset.rule.split(' '), msg = '';
      rules.forEach(function (r) {
        if (msg) return;
        if (r === 'required' && !v) msg = 'This field is required.';
        else if (r === 'email' && v && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) msg = 'Enter a valid email address.';
        else if (r.indexOf('min:') === 0 && v.length < +r.slice(4)) msg = 'Must be at least ' + r.slice(4) + ' characters.';
        else if (r === 'phone' && v && !/^\+?[0-9 ]{9,15}$/.test(v)) msg = 'Enter a valid phone number.';
        else if (r === 'number' && v && (isNaN(v) || +v < 0)) msg = 'Enter a positive number.';
        else if (r.indexOf('match:') === 0 && v !== form.querySelector('[name=' + r.slice(6) + ']').value) msg = 'Passwords do not match.';
      });
      if (msg) {
        ok = false;
        var err = input.nextElementSibling;
        if (!err || !err.classList.contains('error')) { err = document.createElement('span'); err.className = 'error'; input.after(err); }
        err.textContent = msg;
      }
    });
    if (!ok) e.preventDefault();
  });
});
// Confirmation prompt before deleting a record
document.querySelectorAll('form.confirm-delete').forEach(function (f) {
  f.addEventListener('submit', function (e) {
    if (!confirm('Are you sure you want to permanently delete this record?')) e.preventDefault();
  });
});
