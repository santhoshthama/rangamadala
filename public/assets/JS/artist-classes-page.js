function initArtistClassPayments() {
  const enrollForms = document.querySelectorAll('.class-enroll-payment-form');
  if (!enrollForms.length) {
    return;
  }

  enrollForms.forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();

      if (typeof payhere === 'undefined') {
        alert('PayHere is not available right now. Please refresh and try again.');
        return;
      }

      const submitBtn = form.querySelector('button[type="submit"]');
      const classIdInput = form.querySelector('input[name="class_id"]');
      if (!classIdInput || !classIdInput.value) {
        alert('Invalid class selected.');
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
      }

      fetch(form.getAttribute('action'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'class_id=' + encodeURIComponent(classIdInput.value)
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (!data.success) {
            alert(data.error || 'Unable to initialize class payment.');
            if (submitBtn) {
              submitBtn.disabled = false;
            }
            return;
          }

          const payment = {
            sandbox: !!data.sandbox,
            merchant_id: data.merchant_id,
            return_url: data.return_url,
            cancel_url: data.cancel_url,
            notify_url: data.notify_url,
            order_id: data.order_id,
            items: data.title || 'Drama Class',
            amount: data.amount,
            currency: 'LKR',
            hash: data.hash,
            first_name: 'Artist',
            last_name: 'User',
            email: 'artist@example.com',
            phone: '0770000000',
            address: 'Sri Lanka',
            city: 'Colombo',
            country: 'Sri Lanka'
          };

          payhere.onCompleted = function () {
            window.location.href = data.return_url;
          };

          payhere.onDismissed = function () {
            if (submitBtn) {
              submitBtn.disabled = false;
            }
          };

          payhere.onError = function (error) {
            alert('Payment error: ' + error);
            if (submitBtn) {
              submitBtn.disabled = false;
            }
          };

          payhere.startPayment(payment);
        })
        .catch(function () {
          alert('Payment initialization failed. Please try again.');
          if (submitBtn) {
            submitBtn.disabled = false;
          }
        });
    });
  });
}

function openClassesTab(evt, tabId) {
  evt.preventDefault();

  const tabContents = document.getElementsByClassName('tab-content');
  for (let i = 0; i < tabContents.length; i++) {
    tabContents[i].classList.remove('active');
  }

  const tabButtons = document.getElementsByClassName('nav-tab-btn');
  for (let i = 0; i < tabButtons.length; i++) {
    tabButtons[i].classList.remove('active');
  }

  document.getElementById(tabId).classList.add('active');
  evt.currentTarget.classList.add('active');
}

window.openClassesTab = openClassesTab;

window.addEventListener('DOMContentLoaded', function () {
  const userMenu = document.getElementById('userMenu');
  const userMenuTrigger = document.getElementById('user-menu-trigger');

  if (userMenu && userMenuTrigger) {
    userMenuTrigger.addEventListener('click', function (e) {
      e.stopPropagation();
      userMenu.classList.toggle('active');
    });

    document.addEventListener('click', function (e) {
      if (!userMenu.contains(e.target)) {
        userMenu.classList.remove('active');
      }
    });
  }

  initArtistClassPayments();
});
